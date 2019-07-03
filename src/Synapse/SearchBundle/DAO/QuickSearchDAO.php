<?php
namespace Synapse\SearchBundle\DAO;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\FeatureMasterLangRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("quick_search_dao")
 */
class QuickSearchDAO
{

    const DAO_KEY = 'quick_search_dao';

    // Scaffolding
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    // Repositories
    /**
     * @var FeatureMasterLangRepository
     */
    private $featureMasterLangRepository;

    /**
     * QuickSearchDAO constructor.
     *
     * @param $connection
     * @param $repositoryResolver
     * @DI\InjectParams({
     *          "connection" = @DI\Inject("database_connection"),
     *          "repositoryResolver" = @DI\Inject("repository_resolver")
     *      })
     */
    public function __construct($connection, $repositoryResolver)
    {
        $this->connection = $connection;
        $this->repositoryResolver = $repositoryResolver;

        // Repositories
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:FeatureMasterLang');
    }

    /**
     * Gets the first 100 students at the given organization that match a search string - no permissions applied.
     * Each token in the search string (separated by spaces and/or commas) is matched against the user's first name, last name, username, and external_id.
     *
     * @param int $organizationId
     * @param string $searchString
     * @param int $orgAcademicYearId
     * @return array
     */
    public function searchFor100StudentsAsCoordinator($organizationId, $searchString, $orgAcademicYearId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $searchString = str_replace(',', ' ', $searchString);
        $searchStringTokenized = explode(' ', $searchString);
        $searchStringTokenized = array_filter($searchStringTokenized);

        $searchStringClause = '';

        $i = 0;
        foreach ($searchStringTokenized as $searchToken) {
            $searchToken = "%$searchToken%";

            $searchTokenParameterString = "searchToken$i";
            $parameters[$searchTokenParameterString] = $searchToken;

            $searchStringClause .= " AND (p.lastname LIKE :$searchTokenParameterString
                                          OR p.firstname LIKE :$searchTokenParameterString
                                          OR p.username LIKE :$searchTokenParameterString
                                          OR p.external_id like :$searchTokenParameterString) ";
            $i++;
        }

        $query = "SELECT p.id AS person_id,
                        p.firstname,
                        p.lastname,
                        p.external_id,
                        p.username AS primary_email,
                        opsy.is_active as status
                    FROM
                        person p
                            INNER JOIN
                        org_person_student_year opsy
                                ON opsy.person_id = p.id
                                AND opsy.organization_id = p.organization_id
                                AND opsy.org_academic_year_id = :orgAcademicYearId
                    WHERE p.organization_id = :organizationId
                        $searchStringClause
                        AND opsy.deleted_at IS NULL
                        AND p.deleted_at IS NULL
                    ORDER BY p.lastname, p.firstname, p.username, p.id
                    LIMIT 100;";
        try {
            $stmt = $this->connection->executeQuery($query, $parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Gets the first 100 students for which the given faculty has individual access and which match a search string.
     * Each token in the search string (separated by spaces and/or commas) is matched against the user's first name, last name, username, and external_id.
     * If $purpose is "appointment", only includes students for whom the faculty member has permission to create an appointment.
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $searchString
     * @param int $orgAcademicYearId
     * @param string|null $purpose
     * @return array
     */
    public function searchFor100StudentsAsFaculty($organizationId, $facultyId, $searchString, $orgAcademicYearId, $purpose = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $facultyId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $searchString = str_replace(',', ' ', $searchString);
        $searchStringTokenized = explode(' ', $searchString);
        $searchStringTokenized = array_filter($searchStringTokenized);

        $searchStringClause = '';

        $i = 0;
        foreach ($searchStringTokenized as $searchToken) {
            $searchToken = "%$searchToken%";

            $searchTokenParameterString = "searchToken$i";
            $parameters[$searchTokenParameterString] = $searchToken;

            $searchStringClause .= " AND (p.lastname LIKE :$searchTokenParameterString 
                                          OR p.firstname LIKE :$searchTokenParameterString 
                                          OR p.username LIKE :$searchTokenParameterString 
                                          OR p.external_id like :$searchTokenParameterString) ";
            $i++;
        }

        if ($purpose === "appointment") {
            // Add a join to the features table to allow checking that this faculty has appropriate appointment permissions
            $appointmentJoinClause = "INNER JOIN
                        org_permissionset_features opf
                                ON opf.org_permissionset_id = ofspm.permissionset_id
                                AND opf.organization_id = p.organization_id";

            // Get the feature ID for appointments
            $featureForAppointments = $this->featureMasterLangRepository->findOneBy(['featureName' => SynapseConstant::APPOINTMENT_FEATURE_NAME_IN_FEATURE_MASTER_LANG]);
            $featureIdForAppointments = $featureForAppointments->getFeatureMaster()->getId();

            // Add a verification that the given faculty member can modify the appointment using the feature ID above
            $appointmentWhereClause = "AND opf.feature_id = $featureIdForAppointments
                        AND (opf.public_create = 1
                              OR opf.private_create = 1
                              OR opf.team_create = 1)
                        AND opf.deleted_at IS NULL";
        } else {
            $appointmentJoinClause = "";
            $appointmentWhereClause = "";
        }

        $query = "SELECT DISTINCT
                        p.id AS person_id,
                        p.firstname,
                        p.lastname,
                        p.external_id,
                        p.username AS primary_email,
                        opsy.is_active as status
                    FROM
                        person p
                            INNER JOIN
                        org_faculty_student_permission_map ofspm
                                ON ofspm.student_id = p.id
                                AND ofspm.org_id = p.organization_id
                            INNER JOIN
                        org_permissionset op
                                ON op.id = ofspm.permissionset_id
                                AND op.organization_id = ofspm.org_id
                            INNER JOIN
                        org_person_student_year opsy
                                ON opsy.person_id = p.id
                                AND opsy.org_academic_year_id = :orgAcademicYearId
                        $appointmentJoinClause
                    WHERE p.organization_id = :organizationId
                        AND ofspm.faculty_id = :facultyId
                        AND op.accesslevel_ind_agg = 1
                        $appointmentWhereClause
                        $searchStringClause
                        AND p.deleted_at IS NULL
                        AND op.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                    ORDER BY p.lastname, p.firstname, p.username, p.id
                    LIMIT 100;";
        try {
            $stmt = $this->connection->executeQuery($query, $parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;

    }


}