<?php
namespace Synapse\AuthenticationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgSamlConfig
 *
 * @ORM\Table(name="org_saml_config", indexes={@ORM\Index(name="fk_org_saml_config_organization1_idx", columns={"org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\AuthenticationBundle\Repository\OrgSamlConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgSamlConfig extends BaseEntity
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Synapse\CoreBundle\Entity\Organization
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="org_id", nullable=false, referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var string
     * @ORM\Column(name="entity_id", type="string", nullable=false)
     */
    private $entityId;

    /**
     * @var string
     * @ORM\Column(name="federation_metadata", type="string", nullable=true)
     */
    private $federationMetadata;

    /**
     * @var string
     * @ORM\Column(name="sso_url", type="string", nullable=false)
     */
    private $ssoUrl;

    /**
     * @var string
     * @ORM\Column(name="logout_url", type="string", nullable=true)
     */
    private $logoutUrl;

    /**
     * @var string
     * @ORM\Column(name="public_key_file", type="string", nullable=false)
     */
    private $publicKeyFile;

    /**
     * @var string
     * @ORM\Column(name="settings_override", type="string", nullable=true)
     */
    private $settingsOverride;


    /**
     * Sets the value of id.
     *
     * @param integer $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the }).
     *
     * @param Synapse\CoreBundle\Entity\Organization $organization the organization
     *
     * @return self
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Gets the }).
     *
     * @return Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Sets the value of entityId.
     *
     * @param string $entityId the entity id
     *
     * @return self
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Gets the value of entityId.
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Sets the value of federationMetadata.
     *
     * @param string $federationMetadata the federation metadata
     *
     * @return self
     */
    public function setFederationMetadata($federationMetadata)
    {
        $this->federationMetadata = $federationMetadata;

        return $this;
    }

    /**
     * Gets the value of federationMetadata.
     *
     * @return string
     */
    public function getFederationMetadata()
    {
        return $this->federationMetadata;
    }

    /**
     * Sets the value of ssoUrl.
     *
     * @param string $ssoUrl the sso url
     *
     * @return self
     */
    public function setSsoUrl($ssoUrl)
    {
        $this->ssoUrl = $ssoUrl;

        return $this;
    }

    /**
     * Gets the value of ssoUrl.
     *
     * @return string
     */
    public function getSsoUrl()
    {
        return $this->ssoUrl;
    }

    /**
     * Sets the value of logoutUrl.
     *
     * @param string $logoutUrl the logout url
     *
     * @return self
     */
    public function setLogoutUrl($logoutUrl)
    {
        $this->logoutUrl = $logoutUrl;

        return $this;
    }

    /**
     * Gets the value of logoutUrl.
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->logoutUrl;
    }

    /**
     * Sets the value of publicKeyFile.
     *
     * @param string $publicKeyFile the public key file
     *
     * @return self
     */
    public function setPublicKeyFile($publicKeyFile)
    {
        $this->publicKeyFile = $publicKeyFile;

        return $this;
    }

    /**
     * Gets the value of publicKeyFile.
     *
     * @return string
     */
    public function getPublicKeyFile()
    {
        return $this->publicKeyFile;
    }

    /**
    * Sets the value of settingsOverride.
    *
    * @param string $settingsOverride setting json array
    *
    * @return self
    */
    public function setSettingsOverride($settingsOverride)
    {
        $this->settingsOverride = $settingsOverride;

        return $this;
    }
 
    /**
    * Gets the value of settingsOverride.
    *
    * @return string
    */
    public function getSettingsOverride()
    {
        return $this->settingsOverride;
    }
}
