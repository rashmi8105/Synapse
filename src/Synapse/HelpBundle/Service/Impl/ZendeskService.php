<?php
namespace Synapse\HelpBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Zendesk\API\Client as ZendeskAPI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\HelpBundle\Service\HelpdeskServiceInterface;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\HelpBundle\EntityDto\TicketCategoryDto;
use Synapse\HelpBundle\EntityDto\TicketDto;
use Synapse\HelpBundle\EntityDto\TicketRequesterDto;
use Synapse\HelpBundle\Util\Constants\HelpConstants;
use \Firebase\JWT\JWT;
use Synapse\RestBundle\Entity\Response;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * Synapse Zendesk service
 * @DI\Service("zendesk_service")
 */
class ZendeskService extends AbstractService implements HelpdeskServiceInterface
{

    const SERVICE_KEY = 'zendesk_service';

    private $ebiConfigService;

    private $context;

    private $zendeskClient;

    /**
     *
     * @var container
     */
    private $container;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service"),
     *            "context" = @DI\Inject("security.context"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    function __construct($repositoryResolver, $logger, $ebiConfigService, $context, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        $this->ebiConfigService = $ebiConfigService;

        $this->context = $context;

        $this->zendeskClient = new ZendeskAPI($this->ebiConfigService->get('Zendesk_Subdomain'), $this->ebiConfigService->get('Zendesk_User'));

        $this->zendeskClient->setAuth('token', $this->ebiConfigService->get('Zendesk_Token'));

        $this->container = $container;
    }

    public function getCategories()
    {
        $this->logger->info(" Get Categories ");
        try{
            $ticketField = $this->zendeskClient->ticketFields()->find([
                'id' => $this->ebiConfigService->get('Zendesk_Category_Field_Id')
                ]);
            $ticketFieldOptions = $ticketField->ticket_field->custom_field_options;

            $categories = [];

            foreach ($ticketFieldOptions as $option) {
                $categories[] = [
                'name' => $option->name,
                'value' => $option->value
                ];
            }

            return $categories;
        }catch (\Exception $e) {
            $this->logger->debug(HelpConstants::ZEN_DESK_CAT_ERROR_CODE. " : ".$e->getCode()." - ".$e->getMessage());
            throw new ValidationException([
                HelpConstants::ZEN_DESK_CAT_ERROR
            ], HelpConstants::ZEN_DESK_CAT_ERROR, HelpConstants::ZEN_DESK_CAT_ERROR_CODE);
        }

    }

    public function createTicket(TicketDto $ticketDTO)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($ticketDTO);
        $this->logger->debug(" Creating Ticket  -  " . $logContent );
        try {
            $resp = $this->zendeskClient->tickets()->create([
                'subject' => $ticketDTO->getSubject(),
                'comment' => [
                    'body' => $ticketDTO->getDescription(),
                    'uploads' => [$ticketDTO->getAttachment()]
                ],
                'custom_fields' => [
                    [
                        'id' => $this->ebiConfigService->get('Zendesk_Category_Field_Id'),
                        'value' => $ticketDTO->getCategory()
                    ]
                ],
                'requester' => $this->createRequester()
            ]);
            if (! $resp->ticket->id) {
                throw new ValidationException([
                    HelpConstants::ZEN_DESK_ERROR
                ], HelpConstants::ZEN_DESK_ERROR, HelpConstants::ZEN_DESK_ERROR_CODE);
            } else {
                $ticketDTO->setId($resp->ticket->id);
                return $ticketDTO;
            }
        } catch (\Exception $e) {
           $this->logger->debug(HelpConstants::ZEN_DESK_CAT_ERROR_CODE. " : ".$e->getCode()." - ".$e->getMessage());
            throw new ValidationException([
                HelpConstants::ZEN_DESK_CAT_ERROR
            ], HelpConstants::ZEN_DESK_CAT_ERROR, HelpConstants::ZEN_DESK_CAT_ERROR_CODE);
        }
    }

   private function createRequester()
    {
        $user = $this->context->getToken()->getUser();
        $requester = [
            'name' => $user->getFirstname() . ' ' . $user->getLastname(),
            'email' => $user->getUsername(),
            'external_id' => $user->getId()
        // 'organization_id' => $this->getOrganizationId(
        // $user->getOrganization()->getId()
        // ),
        // 'user_fields' => [
        // 'user_school' => $user->getSchool()
        // ]
        ];

        return $requester;
    }

    public function createAttachment($file)
    {
        $extension = $file->guessExtension();
        if (! $extension) {
            $extension = 'bin';
        }

        $dir = '/tmp';
        $filename = rand(1, 99999) . '.' . $extension;
        $file->move($dir, $filename);
        $data = [
            'data' => [
                'fileId' => $this->attachFile($dir . '/' . $filename)
            ]
        ];
        echo json_encode($data);
    }

    /**
     * Returns the SSO URL.
     *
     * @param string $returnUrl
     * @param int $organizationId
     * @return string
     */
    public function getSsoLoginUrl($returnUrl, $organizationId = null) {
        $systemUrl = $this->ebiConfigService->getSystemUrl($organizationId);

        return $systemUrl . '#/zendesk/sso?return_to=' . $returnUrl;
    }

    public function getSsoTokenUrl($returnUrl) {
        $user = $this->context->getToken()->getUser();
        $zendeskSubdomain = $this->ebiConfigService->get('Zendesk_Subdomain');
        $zendeskSecret = $this->ebiConfigService->get('Zendesk_Secret');
        $now = time();

        $token = [
          "jti"   => md5($now . rand()),
          "iat"   => $now,
          "name"  => $user->getFirstname() . ' ' . $user->getLastname(),
          "email" => $user->getUsername()
        ];

        $payload = JWT::encode($token, $zendeskSecret);

        return 'https://' . $zendeskSubdomain
            . '.zendesk.com/access/jwt?jwt=' . $payload . '&return_to=' . $returnUrl;
    }

    private function attachFile($file)
    {
        try {
            $path_array = explode('/', $file);
            $file_index = count($path_array) - 1;
            $name = $path_array[$file_index];

            $attachment = $this->zendeskClient->attachments()->upload(array(
                'file' => $file, 'name' => $name
            ));

            unlink($file);
            if (! $attachment->upload->token) {
                throw new ValidationException([
                    HelpConstants::ZEN_DESK_FILE_NOT_UPLOADED
                ], HelpConstants::ZEN_DESK_FILE_NOT_UPLOADED, HelpConstants::ZEN_DESK_FILE_NOT_UPLOADED);
            } else {
                return $attachment->upload->token;
            }
        } catch (\Exception $e) {
            throw new ValidationException([
                HelpConstants::ZEN_DESK_LIC_ERROR . $e->getMessage()
            ], HelpConstants::ZEN_DESK_LIC_ERROR, HelpConstants::ZEN_DESK_LIC_ERROR_CODE);
        }
    }

    private function getOrganizationId($organizationId)
    {
        $organizations = $this->zendeskClient->organizations()->search([
            'external_id' => $organizationId
        ]);

        return $organizations->organizations[0]->id;
    }
    /**
     * To get zend desk sub domain
     */
    public function getSubDomain()
    {
        $subdomain = $this->ebiConfigService->get('Zendesk_Subdomain');
        return ['subdomain'=>$subdomain];
    }
}