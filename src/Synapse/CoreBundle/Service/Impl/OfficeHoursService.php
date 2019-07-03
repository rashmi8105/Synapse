<?php
namespace Synapse\CoreBundle\Service\Impl;

use BCC\ResqueBundle\Resque;
use DateTime;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\CalendarBundle\Service\Impl\CalendarFactoryService;
use Synapse\CalendarBundle\Service\Impl\CalendarIntegrationService;
use Synapse\CalendarBundle\Service\Impl\CalendarWrapperService;
use Synapse\CoreBundle\CoreBundleConstant;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Entity\OfficeHoursSeries;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\job\BulkOfficeHourSeriesJob;
use Synapse\CoreBundle\Repository\AppointmentsRepository;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OfficeHoursRepository;
use Synapse\CoreBundle\Repository\OfficeHoursSeriesRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Service\Impl\JobService;
use Synapse\PersonBundle\Repository\ContactInfoRepository;
use Synapse\RestBundle\Entity\OfficeHoursDto;
use Synapse\RestBundle\Entity\OfficeHoursSeriesDto;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * @DI\Service("officehours_service")
 */
class OfficeHoursService extends OfficeHoursHelperService
{
    const SERVICE_KEY = 'officehours_service';

    const ERROR_DATE_GREATER = "Slot start date cannot be greater than Slot end date";

    const ERROR_OFFICEHOURS_NOT_FOUND_ERROR_KEY = "Officehours_not_found";

    const ERROR_OFFICEHOURS_NOT_FOUND = "Office hours Not Found.";

    const FIELD_OFFICEHOURS_SERIES = "officeHoursSeries";

    const FIELD_DATE_FORMAT = "DT0H0M0S";

    /**
     * @var integer
     */
    private $countOfOfficeHoursInSeries;

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Resque
     */
    private $resque;

    /**
     * @var LegacyValidator
     */
    private $validator;

    /**
     * @var Manager
     */
    private $rbacManager;

    // Services

    /**
     * @var CalendarFactoryService
     */
    private $calendarFactoryService;

    /**
     * @var CalendarIntegrationService
     */
    private $calendarIntegrationService;

    /**
     * @var CalendarWrapperService
     */
    private $calendarWrapperService;

    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var JobService
     */
    private $jobService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var PersonService
     */
    private $personService;

    // Repositories

    /**
     * @var AppointmentsRepository
     */
    private $appointmentsRepository;

    /**
     * @var ContactInfoRepository
     */
    private $contactInfoRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListRepository;

    /**
     * @var OfficeHoursRepository
     */
    private $officeHoursRepository;

    /**
     * @var OfficeHoursSeriesRepository
     */
    private $officeHoursSeriesRepository;

    /**
     * @var OrgAcademicYearRepository
     */
    private $orgAcademicYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;



    /**
     * OfficeHoursService constructor.
     *
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        // Scaffolding
        $this->container = $container;
        $this->resque = $this->container->get(SynapseConstant::RESQUE_CLASS_KEY);
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);

        // Services
        $this->calendarFactoryService = $this->container->get(CalendarFactoryService::SERVICE_KEY);
        $this->calendarIntegrationService = $this->container->get(CalendarIntegrationService::SERVICE_KEY);
        $this->calendarWrapperService = $this->container->get(CalendarWrapperService::SERVICE_KEY);
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->jobService = $this->container->get(JobService::SERVICE_KEY);
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->personService = $this->container->get(PersonService::SERVICE_KEY);

        // Repositories
        $this->appointmentsRepository = $this->repositoryResolver->getRepository(AppointmentsRepository::REPOSITORY_KEY);
        $this->contactInfoRepository = $this->repositoryResolver->getRepository(ContactInfoRepository::REPOSITORY_KEY);
        $this->metadataListRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->officeHoursRepository = $this->repositoryResolver->getRepository(OfficeHoursRepository::REPOSITORY_KEY);
        $this->officeHoursSeriesRepository = $this->repositoryResolver->getRepository(OfficeHoursSeriesRepository::REPOSITORY_KEY);
        $this->orgAcademicYearRepository = $this->repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

    }

    /**
     * Create an office hour.
     *
     * @param OfficeHoursDto $officeHoursDTO
     * @return OfficeHoursDto
     * @throws SynapseValidationException
     */
    public function createOfficeHour(OfficeHoursDto $officeHoursDTO)
    {
        //Create office hours entity
        $officeHoursEntity = new OfficeHours();

        // Get person and organization objects and ids of the person who is creating the office hour.
        $person = $this->personRepository->find($officeHoursDTO->getPersonId());
        if (!$person) {
            throw new SynapseValidationException('The person does not exist.');
        }

        $organization = $person->getOrganization();
        $personId = $person->getId();
        $organizationId = $organization->getId();

        //Set slot start and end dates to UTC, format them in YYYY-MM-DD hh:mm:ss
        $overallOfficeHourStartTime = $officeHoursDTO->getSlotStart();
        $overallOfficeHourEndTime = $officeHoursDTO->getSlotEnd();

        //Get the offset from UTC of the start date and the direction of said offset.
        $offsetFromUTC = $overallOfficeHourStartTime->getOffset();
        $offsetString = (string)$offsetFromUTC;
        $offsetDirection = $offsetString[0];
        $offsetAbsoluteValue = abs($offsetFromUTC);

        //If the offset direction is negative "-", we want to adjust the passed in datetime FORWARD in time to UTC. Otherwise, we want to adjust the datetime BACKWARDS.
        //The start and end dates' offsets will only be different when the time between the two crosses over Daylight Savings Time.
        if ($offsetDirection == "-") {
            $overallOfficeHourStartTime = $overallOfficeHourStartTime->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $overallOfficeHourEndTime = $overallOfficeHourEndTime->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        } else {
            $overallOfficeHourStartTime = $overallOfficeHourStartTime->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $overallOfficeHourEndTime = $overallOfficeHourEndTime->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        }

        $overallOfficeHourStartTimeFormatted = $overallOfficeHourStartTime->format('Y-m-d H:i:s');
        $overallOfficeHourEndTimeFormatted = $overallOfficeHourEndTime->format('Y-m-d H:i:s');

        //Check to see if the slot overlaps an existing office hour. If it does, fail the entire office hour.
        $isOverLapping = $this->officeHoursRepository->isOverlappingOfficeHours($personId, $organizationId, $overallOfficeHourStartTimeFormatted, $overallOfficeHourEndTimeFormatted);
        if ($isOverLapping) {
            throw new SynapseValidationException('Office hours cannot overlap');
        }

        //Set variables for other repeatedly used information
        $proxyPersonId = $officeHoursDTO->getPersonIdProxy();
        $meetingLength = $officeHoursDTO->getMeetingLength();
        $slotType = $officeHoursDTO->getSlotType();
        $location = $officeHoursDTO->getLocation();

        //Fail the office hour creation if the start date is greater than the end date.
        if ($overallOfficeHourEndTime < $overallOfficeHourStartTime) {
            throw new SynapseValidationException('The slot start date cannot be greater than the slot end date. ');
        }

        //Check to see if the office hour is being created via proxy.
        $personProxy = ($proxyPersonId == 0) ? NULL : $proxyPersonId;
        if ($personProxy) {
            $personProxy = $this->personService->findPerson($personProxy);
        }

        //Set entity attributes
        $officeHoursEntity->setPerson($person);
        $officeHoursEntity->setPersonProxyCreated($personProxy);
        $officeHoursEntity->setOrganization($organization);
        $officeHoursEntity->setSlotType($slotType);
        $officeHoursEntity->setSlotStart($overallOfficeHourStartTime);
        $officeHoursEntity->setSlotEnd($overallOfficeHourEndTime);
        $officeHoursEntity->setIsCancelled(false);
        $officeHoursEntity->setLocation($location);
        $officeHoursEntity->setSource('S');

        //Set the OfficeHoursDTO's slot start and slot end to the UTC times set earlier
        $officeHoursDTO->setSlotStart($overallOfficeHourStartTime);
        $officeHoursDTO->setSlotEnd($overallOfficeHourEndTime);

        $calendarStartSlot = $officeHoursDTO->getSlotStart();
        $newOfficeHours = [];

        $startTimeOfOfficeHourInterval = clone $overallOfficeHourStartTime;

        //While the appointment still has slots to be created, create those slots.
        while ($startTimeOfOfficeHourInterval < $overallOfficeHourEndTime) {
            //Set the end time, and increment it by the meeting length.
            $endTimeOfOfficeHourInterval = clone $startTimeOfOfficeHourInterval;
            $endTimeOfOfficeHourInterval->add(new \DateInterval('PT' . $meetingLength . 'M'));

            //If the current slot's end date is greater than the overall office hour end date, don't create the slot.
            if ($endTimeOfOfficeHourInterval > $overallOfficeHourEndTime) {
                break;
            }
            $calenderEndSlot = clone $calendarStartSlot;
            $calenderEndSlot->add(new \DateInterval('P0DT0H' . $meetingLength . 'M0S'));

            //Set entity attributes for office hour slot creation
            $officeHoursEntity = new OfficeHours();
            $officeHoursEntity->setPerson($person);
            $officeHoursEntity->setPersonProxyCreated($personProxy);
            $officeHoursEntity->setOrganization($organization);
            $officeHoursEntity->setSlotType($slotType);
            $officeHoursEntity->setMeetingLength($meetingLength);
            $officeHoursEntity->setSlotStart($startTimeOfOfficeHourInterval);
            $officeHoursEntity->setSlotEnd($endTimeOfOfficeHourInterval);
            $officeHoursEntity->setIsCancelled(false);
            $officeHoursEntity->setLocation($location);
            $officeHoursEntity->setSource('S');

            $errors = $this->validator->validate($officeHoursEntity);
            $this->validateErrors($errors);
            $newOfficeHours[] = $officeHoursEntity;
            $this->officeHoursRepository->createOfficeHours($officeHoursEntity);
            //Set the start time of the next slot to the end time of the current slot.
            $startTimeOfOfficeHourInterval = clone $endTimeOfOfficeHourInterval;
            $calendarStartSlot = clone $calenderEndSlot;

        }
        $this->officeHoursRepository->flush();

        //Get the calendar integration settings for the organization and person
        $calendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $personId);

        // Create the office hour slot.
        if ($calendarSettings['facultyMAFTOPCS'] == "y") {
            foreach ($newOfficeHours as $officeHourEntity) {
                $this->calendarIntegrationService->syncOneOffEvent($organizationId, $personId, $officeHourEntity->getId(), 'office_hour', 'create');
            }
        }

        $officeHoursDTO->setOfficeHoursId($officeHoursEntity->getId());
        return $officeHoursDTO;
    }

    /**
     * Creates an office hours series
     *
     * @param OfficeHoursDto $officeHoursDTO     
     * @param int|null $officeHoursId
     * @throws SynapseValidationException
     * @return OfficeHoursDto
     */
    public function createOfficeHourSeries(OfficeHoursDto $officeHoursDTO, $officeHoursId = null)
    {
        $personId = $officeHoursDTO->getPersonId();
        $person = $this->personRepository->find($personId);
        if (!$person) {
            throw new SynapseValidationException('This person does not exist');
        }

        $organization = $person->getOrganization();
        $organizationId = $organization->getId();
        $proxyPersonId = $officeHoursDTO->getPersonIdProxy();

        //Get slot start and end dates
        $slotStart = $officeHoursDTO->getSlotStart();
        $slotEnd = $officeHoursDTO->getSlotEnd();

        //Get the offset from UTC of the start date and the direction of said offset.
        $offsetFromUTC = $slotStart->getOffset();
        $offsetString = (string)$offsetFromUTC;
        $offsetDirection = $offsetString[0];
        $offsetAbsoluteValue = abs($offsetFromUTC);

        //If the offset direction is negative "-", we want to adjust the passed in datetime FORWARD in time to UTC. Otherwise, we want to adjust the datetime BACKWARDS.
        //The start and end dates' offsets will only be different when the time between the two crosses over Daylight Savings Time.
        if ($offsetDirection == "-") {
            $slotStart = $slotStart->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        } else {
            $slotStart = $slotStart->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        }

        //Re-create the datetime objects. Down the function chain, timezone comparisons require that these be UTC.
        //The above IF/ELSE set them to a UTC time but cannot update the timezone.
        $slotStart = new DateTime($slotStart->format('Y-m-d H:i:s'));
        $slotEnd = new DateTime($slotEnd->format('Y-m-d H:i:s'));


        //Validate start date. Start date should always be greater than the current time
        $this->isValidEndDate($officeHoursDTO);
        //TODO:: This should be passed in so the unit test doesn't rely on the current date of the server
        $currentDateTime = new \DateTime('now');
        $this->isValidStartDate($currentDateTime, $slotStart);

        //Get necessary information from the OfficeHoursDTO

        //Repeat occurrence: How many times you want to repeat the appointment instead of setting an end date. Matches up with repeat range
        $repeatOccurrence = $officeHoursDTO->getSeriesInfo()->getRepeatOccurence();
        //Repeat every: Repeat the meeting every X days/weeks/months. Matches up with Repeat pattern.
        $repeatEvery = $officeHoursDTO->getSeriesInfo()->getRepeatEvery();
        // Location: location of the office hour
        $location = $officeHoursDTO->getLocation();
        // Repeat Range: 'N' - No end date, 'D' - End by this date, 'E' - end after X occurrences.
        $repeatRange = $officeHoursDTO->getSeriesInfo()->getRepeatRange();
        // Repeat Pattern: 'MWF'- Monday / Wednesday / Friday, 'TT' - Tuesday / Thursday, 'M' - Monthly, 'W' - Weekly, 'D'- Daily, 'N'- No repeat pattern
        $repeatPattern = $officeHoursDTO->getSeriesInfo()->getRepeatPattern();
        // Meeting length: 15 / 30 / 45 / 60 minutes
        $meetingLength = $officeHoursDTO->getSeriesInfo()->getMeetingLength();
        // Repeat days: String of 0s or 1s indicating whether or not meetings occur on that day. Ordered Sunday to Saturday.
        $repeatDays = $officeHoursDTO->getSeriesInfo()->getRepeatDays();
        // Repeat monthly on: 1/2/3/4 Week of the month to repeat the meeting
        $weekToRepeatMonthly = $officeHoursDTO->getSeriesInfo()->getRepeatMonthlyOn();
        //Include Saturday and Sunday: 1 - include, 0 - exclude
        $includeSaturdayAndSunday = $officeHoursDTO->getSeriesInfo()->getIncludeSatSun();

        //Check to see if the person creating the office hour is a proxy user.
        $personProxy = ($proxyPersonId == 0) ? NULL : $proxyPersonId;
        if ($personProxy) {
            $personProxy = $this->personService->findPerson($personProxy);
        }

        //Create office hours series entity, set necessary attributes.
        $officeHourSeriesEntity = new OfficeHoursSeries();
        $officeHourSeriesEntity->setLocation($location);
        $officeHourSeriesEntity->setMeetingLength($meetingLength);
        $officeHourSeriesEntity->setOrganization($organization);
        $officeHourSeriesEntity->setPerson($person);
        $officeHourSeriesEntity->setPersonProxy($personProxy);
        $officeHourSeriesEntity->setRepeatPattern($repeatPattern);
        $officeHourSeriesEntity->setSlotStart($slotStart);
        $officeHourSeriesEntity->setSlotEnd($slotEnd);
        $officeHourSeriesEntity->setRepetitionRange($repeatRange);
        $officeHourSeriesEntity->setIncludeSatSun($includeSaturdayAndSunday);

        //If the office hour series has a repeat pattern, set the values for how many times the series repeats and on which days it repeats.
        if ($repeatPattern != 'N') {
            $officeHourSeriesEntity->setRepeatEvery($repeatEvery);

            if ($repeatPattern == 'MWF') {
                $officeHourSeriesEntity->setDays('0101010');
            } elseif ($repeatPattern == 'TT') {
                $officeHourSeriesEntity->setDays('0010100');
            } else {
                $officeHourSeriesEntity->setDays($repeatDays);
            }

            if ($repeatPattern == "M") {
                $officeHourSeriesEntity->setRepeatMonthlyOn($weekToRepeatMonthly);
            }
        }
        //If the office hour series has no specified end date, get the last day of the academic year and use that as the end date.
        $academicYearEndDate = $this->getCurrentAcademicYearEndDate($organization);

        //If the office hour series has a set number of occurrences, set that number of occurrences.
        if ($repeatRange == 'E') {
            $officeHourSeriesEntity->setRepetitionOccurrence($repeatOccurrence);
        }

        $timezone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
        $organizationDateTimeZoneObject = new \DateTimeZone($timezone);

        //Create the office hours series
        $this->officeHoursSeriesRepository->persist($officeHourSeriesEntity, false);

        // To get all future slot dates for loggedInUser within start date to academic year end date for checking overlap
        $currentAcademicYearEndDate = $academicYearEndDate->setTime($slotEnd->format(CoreBundleConstant::DATE_FORMAT_HOURS), $slotEnd->format(CoreBundleConstant::DATE_FORMAT_MINUTES));
        $allFutureSlotStartAndEndDates = $this->officeHoursRepository->getAllOfficeHourSlots($personId, $organizationId, $slotStart, $currentAcademicYearEndDate, null, $officeHoursId);

        // To create all slots for office hours series
        $this->determineOfficeHoursSeriesByRepeatPattern($officeHourSeriesEntity, $organizationDateTimeZoneObject, $allFutureSlotStartAndEndDates, $currentAcademicYearEndDate, true);
        $this->officeHoursSeriesRepository->flush();

        //Sync calendar integration with the new office hours series.
        $this->calendarIntegrationService->syncOfficeHourSeries($officeHourSeriesEntity->getId(), $organizationId, $personId, 'create');

        $officeHoursDTO->setOfficeHoursId($officeHourSeriesEntity->getId());
        return $officeHoursDTO;
    }

    /**
     * Edit an existing office hour
     *
     * @param OfficeHoursDto $officeHoursDTO
     * @throws SynapseValidationException
     * @return OfficeHoursDto
     */
    public function editOfficeHour(OfficeHoursDto $officeHoursDTO)
    {
        //Get the existing office hour.
        $existingOfficeHour = $this->officeHoursRepository->find($officeHoursDTO->getOfficeHoursId());
        $this->isOfficeHourFound($existingOfficeHour);
        $this->isDateGreater($officeHoursDTO);

        //Get the organization and person ids/objects of the person editing the office hour.
        $person = $this->personService->findPerson($officeHoursDTO->getPersonId());
        $personId = $person->getId();
        $organization = $person->getOrganization();
        $organizationId = $organization->getId();
        $proxyPersonId = $officeHoursDTO->getPersonIdProxy();

        //If the person editing the office hour is proxying, get that person object.
        $personProxy = ($proxyPersonId == 0 ? NULL : $proxyPersonId);
        if ($personProxy) {
            $personProxy = $this->personService->findPerson($personProxy);
        }

        //Set slot start and end dates to UTC, format them in YYYY-MM-DD hh:mm:ss
        $slotStart = $officeHoursDTO->getSlotStart();
        $slotEnd = $officeHoursDTO->getSlotEnd();

        //Get the offset from UTC of the start date and the direction of said offset.
        $offsetFromUTC = $slotStart->getOffset();
        $offsetString = (string)$offsetFromUTC;
        $offsetDirection = $offsetString[0];
        $offsetAbsoluteValue = abs($offsetFromUTC);

        //If the offset direction is negative "-", we want to adjust the passed in datetime FORWARD in time to UTC. Otherwise, we want to adjust the datetime BACKWARDS.
        //The start and end dates' offsets will only be different when the time between the two crosses over Daylight Savings Time.
        if ($offsetDirection == "-") {
            $slotStart = $slotStart->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        } else {
            $slotStart = $slotStart->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        }


        $startDate = $slotStart->format('Y-m-d H:i:s');
        $endDate = $slotEnd->format('Y-m-d H:i:s');

        //Check if existing office hour has appointments tied to it.
        $appointments = $officeHoursDTO->getAppointmentId();
        $appointmentsTiedToExistingOfficeHour = $appointments == 0 ? NULL : $appointments;

        $appointmentObject = null;
        if ($appointmentsTiedToExistingOfficeHour) {
            $appointmentObject = $this->appointmentsRepository->find($appointmentsTiedToExistingOfficeHour);
        }

        //Get necessary information from the OfficeHoursDTO
        $location = $officeHoursDTO->getLocation();
        $slotType = $officeHoursDTO->getSlotType();
        $isCancelled = $officeHoursDTO->getIsCancelled();
        $officeHourId = [$officeHoursDTO->getOfficeHoursId()];

        //Set necessary attributes
        $existingOfficeHour->setPerson($person);
        $existingOfficeHour->setPersonProxyCreated($personProxy);
        $existingOfficeHour->setOrganization($organization);
        $existingOfficeHour->setSlotType($slotType);
        $existingOfficeHour->setSlotStart($slotStart);
        $existingOfficeHour->setSlotEnd($slotEnd);
        $existingOfficeHour->setLocation($location);
        $existingOfficeHour->setIsCancelled($isCancelled);

        //Set any appointments that exist with the existing office hour.
        if ($appointmentObject) {
            $existingOfficeHour->setAppointments($appointmentObject);
        }

        //Check if there are existing office hours in the time where the current office hour is trying to be moved to. If there are, fail the edit.
        $isOverLapping = $this->officeHoursRepository->isOverlappingOfficeHours($personId, $organizationId, $startDate, $endDate, $officeHourId);
        if ($isOverLapping) {
            throw new SynapseValidationException('Office Hours cannot overlap');
        }

        $errors = $this->validator->validate($existingOfficeHour);
        $this->validateErrors($errors);

        $this->officeHoursRepository->flush();
        $officeHoursDTO->setOfficeHoursId($existingOfficeHour->getId());

        // Sync changes to external calendar.
        $this->calendarIntegrationService->syncOneOffEvent($organizationId, $personId, $officeHoursDTO->getOfficeHoursId(), 'office_hour', 'update');

        return $officeHoursDTO;
    }

    /**
     * Edit an existing office hour into an office hour series, or an existing office hour series.
     *
     * @param OfficeHoursDto $officeHoursDTO
     * @param int|null $loggedUserId
     * @return OfficeHoursDto
     * @throws SynapseValidationException
     */
    public function editOfficeHourSeries(OfficeHoursDto $officeHoursDTO, $loggedUserId = null)
    {
        $person = $this->personRepository->find($officeHoursDTO->getPersonId());
        if (!$person) {
            throw new SynapseValidationException('This person does not exist');
        }
        $currentDate = new \DateTime('now');
        $currentDate = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);

        $organization = $person->getOrganization();
        $personId = $person->getId();
        $organizationId = $organization->getId();

        //Set slot start and end dates to UTC, format them in YYYY-MM-DD hh:mm:ss
        $slotStart = $officeHoursDTO->getSlotStart();
        $slotEnd = $officeHoursDTO->getSlotEnd();

        //If the call is not a job, adjust the datetimes accordingly to account for Daylight Savings Time.
        // If the call is a job, this will be accounted for before job creation
        // (This function as a whole is called both to create the job and when the job runs.)

        //Get the offset from UTC of the start date and the direction of said offset.
        $offsetFromUTC = $slotStart->getOffset();
        $offsetString = (string)$offsetFromUTC;
        $offsetDirection = $offsetString[0];
        $offsetAbsoluteValue = abs($offsetFromUTC);

        //If the offset direction is negative "-", we want to adjust the passed in datetime FORWARD in time to UTC. Otherwise, we want to adjust the datetime BACKWARDS.
        //The start and end dates' offsets will only be different when the time between the two crosses over Daylight Savings Time.
        if ($offsetDirection == "-") {
            $slotStart = $slotStart->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->add(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        } else {
            $slotStart = $slotStart->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
            $slotEnd = $slotEnd->sub(new \DateInterval('PT' . (string)$offsetAbsoluteValue . 'S'));
        }

        //Re-create the datetime objects. Down the function chain, timezone comparisons require that these be UTC.
        //The above IF/ELSE set them to a UTC time but cannot update the timezone.
        $slotStart = new DateTime($slotStart->format('Y-m-d H:i:s'));
        $slotEnd = new DateTime($slotEnd->format('Y-m-d H:i:s'));

        $officeHoursDTO->setSlotStart($slotStart);
        $officeHoursDTO->setSlotEnd($slotEnd);

        //Get necessary information from the OfficeHoursDTO

        //Repeat occurrence: How many times you want to repeat the appointment instead of setting an end date. Matches up with repeat range
        $repeatOccurrence = $officeHoursDTO->getSeriesInfo()->getRepeatOccurence();
        //Repeat every: Repeat the meeting every X days, 1/2/3/4 weeks, X months. Matches up with repeat pattern
        $repeatEvery = $officeHoursDTO->getSeriesInfo()->getRepeatEvery();
        // Location: location of the office hour
        $location = $officeHoursDTO->getLocation();
        // Repeat Range: 'N' - No end date, 'D' - End by this date, 'E' - end after X occurrences.
        $repeatRange = $officeHoursDTO->getSeriesInfo()->getRepeatRange();
        // Repeat Pattern: 'MWF'- Monday / Wednesday / Friday, 'TT' - Tuesday / Thursday, 'M' - Monthly, 'W' - Weekly, 'D'- Daily, 'N'- No repeat pattern
        $repeatPattern = $officeHoursDTO->getSeriesInfo()->getRepeatPattern();
        // Meeting length: 15 / 30 / 45 / 60 minutes
        $meetingLength = $officeHoursDTO->getSeriesInfo()->getMeetingLength();
        // Repeat days: String of 0s or 1s indicating whether or not meetings occur on that day. Ordered Sunday to Saturday.
        $repeatDays = $officeHoursDTO->getSeriesInfo()->getRepeatDays();
        // Repeat monthly on: 1/2/3/4 Week of the month to repeat the meeting
        $weekToRepeatMonthly = $officeHoursDTO->getSeriesInfo()->getRepeatMonthlyOn();
        //Include Saturday and Sunday: 1 - include, 0 - exclude
        $includeSaturdayAndSunday = $officeHoursDTO->getSeriesInfo()->getIncludeSatSun();

        $officeHoursId = $officeHoursDTO->getOfficeHoursId();
        $convertOfficeHourToSeries = $officeHoursDTO->getOneToSeries();

        //Get the calendar integration settings for the organization and person
        $calendarSettings = $this->calendarIntegrationService->facultyCalendarSettings($organizationId, $personId);

        //If the existing office hour is not a series, remove it, set any appointments that aren't deleted along with it to free standing, and create an office hours series in its place
        if ($convertOfficeHourToSeries) {
            $existingOfficeHours = $this->officeHoursRepository->find($officeHoursId);
            $appointmentsTiedToExistingOfficeHours = $existingOfficeHours->getAppointments();

            if (!isset($existingOfficeHours)) {
                $this->logger->error("Office Hours Service  - editOfficeHourSeries - Office hours not found" . 'Office hours Not Found.');
                throw new SynapseValidationException('Office hours Not Found');
            }

            if ($appointmentsTiedToExistingOfficeHours) {
                $appointments = $this->appointmentsRepository->find($appointmentsTiedToExistingOfficeHours);
                if (isset($appointments)) {
                    $appointments->setIsFreeStanding(true);
                }
            }

            // Create all office hours slot
            $officeHoursDTO = $this->createOfficeHourSeries($officeHoursDTO, $officeHoursId);

            // Remove existing office hour
            $this->officeHoursRepository->remove($existingOfficeHours);
            $this->officeHoursRepository->flush();

            // Remove event from external calendar.
            $googleAppointmentId = $existingOfficeHours->getGoogleAppointmentId();
            $this->calendarIntegrationService->syncOneOffEvent($organizationId, $personId, $existingOfficeHours->getId(), 'office_hour', 'delete', $googleAppointmentId);

        } else {
            // The existing office hour is a series. Verify that the series exists.
            $officeHourSeries = $this->officeHoursSeriesRepository->find($officeHoursId);
            $this->isOfficeHourExists($officeHourSeries);

            //Set necessary office hours series entity attributes.
            $officeHourSeries->setLocation($location);
            $officeHourSeries->setMeetingLength($meetingLength);
            $officeHourSeries->setSlotStart($slotStart);
            $officeHourSeries->setSlotEnd($slotEnd);
            $officeHourSeries->setRepeatPattern($repeatPattern);
            $officeHourSeries->setIncludeSatSun($includeSaturdayAndSunday);
            $officeHourSeries->setRepetitionRange($repeatRange);

            //if the office hours series is set to not have an end date, get the org's current academic year end date and use that.
            $academicYearEndDate = $this->getCurrentAcademicYearEndDate($organization);

            //If the office hours series repeats monthly, set the week it will repeat on each month.
            if ($repeatPattern == "M") {
                $officeHourSeries->setRepeatMonthlyOn($weekToRepeatMonthly);
            } else {
                $officeHourSeries->setRepeatMonthlyOn(NULL);
            }

            //If the office hours series has a repeating pattern, set the number of days it repeats and which days it repeats.
            if ($repeatPattern != 'N') {

                $officeHourSeries->setRepeatEvery($repeatEvery);

                if ($repeatPattern == 'MWF') {
                    $officeHourSeries->setDays('0101010');
                } elseif ($repeatPattern == 'TT') {
                    $officeHourSeries->setDays('0010100');
                } else {
                    $officeHourSeries->setDays($repeatDays);
                }
            } else {
                $officeHourSeries->setRepeatEvery(NULL);
                $officeHourSeries->setDays(NULL);
            }

            //If the repeat range ends after X occurrences, set that value.
            if ($repeatRange == 'E') {
                $officeHourSeries->setRepetitionOccurrence($repeatOccurrence);
            } else {
                $officeHourSeries->setRepetitionOccurrence(NULL);
            }

            $timezone = $this->dateUtilityService->getOrganizationISOTimeZone($organizationId);
            $organizationDateTimeZoneObject = new \DateTimeZone($timezone);

            // To get all future slot dates for loggedInUser within start date to academic year end date for checking overlap
            $currentAcademicYearEndDate = $academicYearEndDate->setTime($slotEnd->format(CoreBundleConstant::DATE_FORMAT_HOURS), $slotEnd->format(CoreBundleConstant::DATE_FORMAT_MINUTES));
            $allFutureSlotStartAndEndDates = $this->officeHoursRepository->getAllOfficeHourSlots($personId, $organizationId, $slotStart, $currentAcademicYearEndDate, $officeHoursId);
            $this->determineOfficeHoursSeriesByRepeatPattern($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotStartAndEndDates, $currentAcademicYearEndDate, false);
            
            $this->officeHoursRepository->removeExistingSlots($officeHoursId, $currentDate, $loggedUserId);
            $this->officeHoursSeriesRepository->flush();

            // Update previous slot appointments with recently created slots
            $this->officeHoursRepository->updateAppointmentForEditedSlots($officeHoursId, $organizationId, $currentDate);

            $freeStandingAppointments = $this->officeHoursRepository->getFreeStandingAppointments($officeHoursId, $currentDate);
            if (!empty($freeStandingAppointments)) {
                $this->officeHoursRepository->updateAppointmentAsFreeStanding($freeStandingAppointments, $currentDate, $loggedUserId);
            }

            // Sync external calendar.
            $this->calendarIntegrationService->syncOfficeHourSeries($officeHoursId, $organizationId, $personId, 'update');
            $officeHoursDTO->setOfficeHoursId($officeHourSeries->getId());
        }
        return $officeHoursDTO;
    }

    /**
     * Will change the existing office hour series, on the interval in which it exists, to the desired new value.
     * Eg. If I am migrating an existing daily repeating office hour series from 2016-08-01 22:00:00 to 2016-08-10 23:00:00 to the same date range, but 17:00:00 to 18:00:00,
     * this function will be called for each day's office hours, to migrate each interval within that day from 22:00:00 - 23:00:00 to 17:00:00 - 18:00:00.
     *
     * @param DateTime $officeHoursSeriesIntervalStartDate
     * @param DateTime $officeHoursSeriesIntervalEndDate
     * @param int $slotLength - 15/30/45/60 minutes.
     * @param OfficeHoursSeries $officeHourSeries
     * @param array $allFutureSlotDates - all future slots of start_slot and end_slot
     * @return array
     * @throws SynapseValidationException
     */
    private function editOfficeHourInterval($officeHoursSeriesIntervalStartDate, $officeHoursSeriesIntervalEndDate, $slotLength, $officeHourSeries, $allFutureSlotDates)
    {
        // To Check office hour overlap with interval start date and end date
        $isOverlapping = $this->isOfficeHoursSeriesOverlap($officeHoursSeriesIntervalStartDate, $officeHoursSeriesIntervalEndDate, $allFutureSlotDates);
        if ($isOverlapping) {
            throw new SynapseValidationException('Office hours cannot overlap');
        }

        $officeHourResponse = [];
        $subIntervalStartDate = $officeHoursSeriesIntervalStartDate;

        while ($subIntervalStartDate < $officeHoursSeriesIntervalEndDate) {

            if ($officeHourSeries->getRepetitionRange() == 'E' && $this->countOfOfficeHoursInSeries == $officeHourSeries->getRepetitionOccurrence()) {
                break;
            }

            $officeHour = clone $this->getOfficeHoursWithDefault($officeHourSeries);
            $subIntervalEndDate = clone $subIntervalStartDate;
            $officeHour->setSlotStart(clone $subIntervalStartDate);
            $subIntervalEndDate->add(new \DateInterval('PT' . $slotLength . 'M'));
            $officeHour->setSlotEnd(clone $subIntervalEndDate);

            if ($officeHoursSeriesIntervalEndDate < $subIntervalEndDate) {
                break;
            }

            $this->officeHoursRepository->createOfficeHours($officeHour);
            $officeHourResponse[] = $officeHour;
            $subIntervalStartDate = clone $subIntervalEndDate;
            unset($subIntervalEndDate);
            $this->countOfOfficeHoursInSeries++;
        }
        return $officeHourResponse;
    }

    /**
     * Check office hour start_slot and end_slot overlap with all future slot dates
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $allFutureSlotDates
     * @return bool
     */
    public function isOfficeHoursSeriesOverlap($startDate, $endDate, $allFutureSlotDates)
    {
        $isOverlapping = false;
        foreach ($allFutureSlotDates as $slotDate) {
            $slotStartDate = new DateTime($slotDate['slot_start']);
            $slotEndDate = new DateTime($slotDate['slot_end']);

            /**
             * Cases for the condition:
             * Lets say we have below existing office hour, $slotStartDate = '2017-09-24 21:30:00' and $slotEndDate = '2017-09-24 21:45:00'
             * Overlapping scenario should be:
             * $startDate = '2017-09-24 21:15:00' and $endDate = '2017-09-24 21:45:00'
             * $startDate = '2017-09-24 21:35:00' and $endDate = '2017-09-24 21:50:00'
             * $startDate = '2017-09-24 21:30:00' and $endDate = '2017-09-24 21:45:00'
             * $startDate = '2017-09-24 21:00:00' and $endDate = '2017-09-24 22:00:00'
             *
             */
            if (($startDate > $slotStartDate && $startDate < $slotEndDate) || ($endDate > $slotStartDate && $endDate < $slotEndDate) ||
                ($slotStartDate >= $startDate && $slotEndDate <= $endDate)) {
                $isOverlapping = true;
                break;
            }
        }

        return $isOverlapping;
    }

    /**
     * Create individual office hour instance / office hour series instance.
     *
     * @param DateTime $officeHoursSeriesIntervalStartDate
     * @param DateTime $officeHoursSeriesIntervalEndDate
     * @param int $slotLength
     * @param OfficeHoursSeries $officeHourSeries
     * @param array $allFutureSlotDates - all future slots of start_slot and end_slot
     * @return array|OfficeHours
     * @throws SynapseValidationException
     */
    private function createOfficeHourInterval($officeHoursSeriesIntervalStartDate, $officeHoursSeriesIntervalEndDate, $slotLength, $officeHourSeries, $allFutureSlotDates)
    {
        // To Check office hour overlap with interval start date and end date
        $isOverlapping = $this->isOfficeHoursSeriesOverlap($officeHoursSeriesIntervalStartDate, $officeHoursSeriesIntervalEndDate, $allFutureSlotDates);
        if ($isOverlapping) {
            throw new SynapseValidationException('Office hours cannot overlap');
        }

        $officeHourResponse = [];
        $subIntervalStartDate = $officeHoursSeriesIntervalStartDate;
        while ($subIntervalStartDate < $officeHoursSeriesIntervalEndDate) {
            if ($officeHourSeries->getRepetitionRange() == 'E' && $this->countOfOfficeHoursInSeries == $officeHourSeries->getRepetitionOccurrence()) {
                break;
            }

            $officeHour = clone $this->getOfficeHoursWithDefault($officeHourSeries);
            $subIntervalEndDate = clone $subIntervalStartDate;
            $officeHour->setSlotStart(clone $subIntervalStartDate);
            $subIntervalEndDate->add(new \DateInterval('PT' . $slotLength . 'M'));
            $officeHour->setSlotEnd($subIntervalEndDate);

            if ($officeHoursSeriesIntervalEndDate < $subIntervalEndDate) {
                break;
            }

            $this->officeHoursRepository->createOfficeHours($officeHour);
            $officeHourResponse[] = $officeHour;
            $subIntervalStartDate = clone $subIntervalEndDate;

            unset($subIntervalEndDate);
            $this->countOfOfficeHoursInSeries++;
        }
        return $officeHourResponse;
    }

    /**
     * Based on the repeat pattern, call the appropriate function to
     * create/edit the office hours series.
     *
     * @param OfficeHoursSeries $officeHourSeries
     * @param \DateTimeZone $organizationDateTimeZoneObject
     * @param array $allFutureSlotDates - array of all slot start_date and end_date
     * @param DateTime $academicYearEndDate
     * @param boolean $createFlag - will be true if creating an office hour series
     * @throws SynapseValidationException
     */
    private function determineOfficeHoursSeriesByRepeatPattern($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag = false)
    {
        $repeatPattern = $officeHourSeries->getRepeatPattern();

        switch ($repeatPattern) {
            case 'W':
            case 'MWF':
            case 'TT':
                $this->splitOfficeHoursSeriesIntoWeeklyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag);
                break;
            case 'D':
                $this->splitOfficeHoursSeriesIntoDailyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag);
                break;
            case 'M':
                $this->splitOfficeHoursSeriesIntoMonthlyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag);
                break;
            default:
                throw new SynapseValidationException('Invalid office hours series repeat pattern.');
                break;
        }
    }

    /**
     * This method will divide the series down into daily segments,
     * and pass those segments to createOfficeHourInterval() or editOfficeHourInterval() to create/edit the individual office hours
     * within that daily segment.
     *
     * Eg. Office hours series goes from 2017-01-20 17:00:00 UTC to 2017-05-20 19:00:00 UTC.
     * this function will split that range down into individual days (2017-01-20 17:00:00 UTC - 2017-01-20 19:00:00 UTC)
     * then pass that range down to createOfficeHourInterval() to create the individual meeting length office hours on that
     * daily time range.
     *
     * @param OfficeHoursSeries $officeHourSeries
     * @param \DateTimeZone $organizationDateTimeZoneObject
     * @param array $allFutureSlotDates
     * @param DateTime $academicYearEndDate
     * @param boolean $createFlag
     */
    private function splitOfficeHoursSeriesIntoDailyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag = false)
    {
        //Set the count of office hours in the series to 0
        $this->countOfOfficeHoursInSeries = 0;

        //Get the start date time of the series, and the organization of the creator of the series.
        $officeHoursSeriesStartDateTime = clone $officeHourSeries->getSlotStart();

        //Get the series metadata. Definitions of meaning of these variables can be found in createOfficeHoursSeries()
        $meetingLength = $officeHourSeries->getMeetingLength();
        $repetitionOccurrence = $officeHourSeries->getRepetitionOccurrence();
        $repetitionRange = $officeHourSeries->getRepetitionRange();
        $includeWeekends = $officeHourSeries->getIncludeSatSun();
        $repeatEvery = $officeHourSeries->getRepeatEvery();

        //If the office hours series is set to end after a certain number of repetitions, set the end date time.
        // Otherwise, just use the passed in date time for the end date.
        if ($repetitionRange == 'E') {
            $officeHourSeriesEndDateTime = clone $officeHoursSeriesStartDateTime;
            $officeHourSeriesEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . ($repeatEvery * $repetitionOccurrence) . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));
            $officeHourSeriesEndDateTime->setTime($officeHourSeries->getSlotEnd()->format(CoreBundleConstant::DATE_FORMAT_HOURS), $officeHourSeries->getSlotEnd()->format(CoreBundleConstant::DATE_FORMAT_MINUTES));

            //If the office hours series ends after a certain number of repetitions but does not include weekends, adjust the end date accordingly.
            if (!$includeWeekends) {
                $saturdaysAndSundaysInTimeframe = $this->getNumberOfSatSun($officeHoursSeriesStartDateTime, $officeHourSeriesEndDateTime);
                $officeHourSeriesEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . $repeatEvery * $saturdaysAndSundaysInTimeframe . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));
            }
        } else {
            $officeHourSeriesEndDateTime = clone $officeHourSeries->getSlotEnd();
        }

        // To check office hours series end date is greater than current academic year date
        if ($officeHourSeriesEndDateTime > $academicYearEndDate) {
            $officeHourSeriesEndDateTime->setDate($academicYearEndDate->format('Y'), $academicYearEndDate->format('m'), $academicYearEndDate->format('d'));
        }

        //Get the start date time of the first interval within the series, as well as the hour and minute values of the end time.
        $officeHoursSeriesIntervalStartDateTime = clone $officeHoursSeriesStartDateTime;
        $officeHoursSeriesIntervalEndDateTime = null;
        $previousIntervalEndDateTime = null;
        $officeHoursSeriesEndHours = (int)$officeHourSeriesEndDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS);
        $officeHoursSeriesEndMinutes = (int)$officeHourSeriesEndDateTime->format(CoreBundleConstant::DATE_FORMAT_MINUTES);

        //Until the start date time of the interval is greater than the end date time of the overall series, continue to create office hours series intervals.
        while ($officeHoursSeriesIntervalStartDateTime < $officeHourSeriesEndDateTime) {

            //Clone the datetime object, and set the time for the end of the interval.
            $officeHoursSeriesIntervalEndDateTime = clone $officeHoursSeriesIntervalStartDateTime;
            $officeHoursSeriesIntervalEndDateTime->setTime($officeHoursSeriesEndHours, $officeHoursSeriesEndMinutes);

            //If the hour of the start datetime is greater than the hour of the end datetime, then the interval crosses UTC midnight.
            // Adjust the end date time forward 1 day to account for the midnight crossover.
            if ((int)$officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS) > (int)$officeHoursSeriesIntervalEndDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS)) {
                $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_DAY));
            }

            //If there is an end date time, check for the needed Daylight Savings Time adjustment.
            if ($previousIntervalEndDateTime) {
                $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $previousIntervalEndDateTime, $officeHoursSeriesIntervalEndDateTime);
            } else {
                $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime);
            }

            //If the daylight savings time adjustment is forward X hours (greater than 0), then we want to adjust the current datetimes forward.
            // Setting the time on the overall interval end date ($officeHourSeriesEndDateTime) will ensure that the while loop completes properly.
            // Adjust the hours variable, since that's what dictates the end time later on.
            if ($daylightSavingsTimeAdjustment > 0) {
                $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval($datetimeAdjustment));
                $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval($datetimeAdjustment));
                $officeHourSeriesEndDateTime->add(new \DateInterval($datetimeAdjustment));
                $officeHoursSeriesEndHours += $daylightSavingsTimeAdjustment;
            } else if ($daylightSavingsTimeAdjustment < 0) {
                $daylightSavingsTimeAdjustment = abs($daylightSavingsTimeAdjustment);
                $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                $officeHoursSeriesIntervalStartDateTime->sub(new \DateInterval($datetimeAdjustment));
                $officeHoursSeriesIntervalEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                $officeHourSeriesEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                $officeHoursSeriesEndHours = $officeHoursSeriesEndHours - $daylightSavingsTimeAdjustment;
            }

            //Get the day of the week from the start datetime object.
            $dayOfTheWeek = $officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_DAY_OF_WEEK);

            //If weekends are to be included, create the office hour.
            // Otherwise, if the day is not Saturday or Sunday, create the office hour.
            if ($includeWeekends || (!$includeWeekends && ($dayOfTheWeek != CoreBundleConstant::SATURDAY && $dayOfTheWeek != CoreBundleConstant::SUNDAY))) {
                if ($createFlag) {
                    $this->createOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                } else {
                    $this->editOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                }

            }

            //If the series is a end after X occurrences series & has had X occurrences created,
            // Set the end date time to the start date time to kill the loop.
            if ($repetitionRange == 'E' && $this->countOfOfficeHoursInSeries == $repetitionOccurrence) {
                $officeHourSeriesEndDateTime = $officeHoursSeriesStartDateTime;
            }

            //Increase the start date time to the start date time of the next day.
            $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . $repeatEvery . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));
            $previousIntervalEndDateTime = clone $officeHoursSeriesIntervalEndDateTime;
        }
    }

    /**
     * This method will divide the series down into weekly segments,
     * and pass those segments to createOfficeHourInterval() or editOfficeHourInterval() to create/edit the individual office hours
     * within that weekly segment.
     *
     * @param OfficeHoursSeries $officeHourSeries
     * @param \DateTimeZone $organizationDateTimeZoneObject
     * @param array $allFutureSlotDates
     * @param DateTime $academicYearEndDate
     * @param boolean $createFlag
     */
    private function splitOfficeHoursSeriesIntoWeeklyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag = false)
    {
        //Get office hours series metadata
        $repetitionOccurrence = $officeHourSeries->getRepetitionOccurrence();
        $repetitionRange = $officeHourSeries->getRepetitionRange();
        $repeatEvery = $officeHourSeries->getRepeatEvery();
        $meetingLength = $officeHourSeries->getMeetingLength();
        $officeHoursSeriesEndHours = (int)$officeHourSeries->getSlotEnd()->format('H');
        $officeHoursSeriesEndMinutes = (int)$officeHourSeries->getSlotEnd()->format('i');

        //Get the start date time of the series as two variables, and the first day of the week for that start date.
        //The adjusted start date time will be adjusted based on the availability of office hour slots in the current week.
        //The original start date is used as a comparison later on when checking for a DST overlap.
        $adjustedOfficeHoursSeriesStartDateTime = clone $officeHourSeries->getSlotStart();
        $originalOfficeHoursSeriesStartDateTime = clone $adjustedOfficeHoursSeriesStartDateTime;
        $firstDayInStartDateWeek = clone $adjustedOfficeHoursSeriesStartDateTime;

        //Get the selected days of the week for the office hours series.
        $daysSelected = $officeHourSeries->getDays();
        $daysSelected = str_split($daysSelected);
        $daysSelected = array_keys(array_diff($daysSelected, [0]));

        //Get the first day in the week of the passed in start date.
        $firstDayInStartDateWeek = $this->dateUtilityService->getFirstDayOfWeekForDatetime($firstDayInStartDateWeek);
        $createOfficeHoursInCurrentWeekFlag = false;
        $indexOfFirstDayInWeek = 0;

        //For the selected days, check in the current week if there's office hours that need to be created. Set the flag indicating so.
        for ($index = 0; $index < count($daysSelected); $index++) {
            $datetimeInWeekOfStartDate = clone $firstDayInStartDateWeek;
            $datetimeInWeekOfStartDate->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . $daysSelected[$index] . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));

            //If the start date is less than the desired date for the office hour, then there needs to be office hours created in the current week.
            if ($adjustedOfficeHoursSeriesStartDateTime <= $datetimeInWeekOfStartDate) {
                $indexOfFirstDayInWeek = $index;
                $createOfficeHoursInCurrentWeekFlag = true;
                break;
            }
        }

        //If there's no office hours to create in the current week, set the start date time ahead by one week.
        if (!$createOfficeHoursInCurrentWeekFlag) {
            $adjustedOfficeHoursSeriesStartDateTime = $firstDayInStartDateWeek;
            $adjustedOfficeHoursSeriesStartDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_WEEK));
        }

        //If the office hours series is meant to terminate after X occurrences, get the Xth occurrence and set the end date.
        // Otherwise, use the passed in end date.
        if ($repetitionRange == 'E') {
            $officeHourSeriesEndDateTime = clone $adjustedOfficeHoursSeriesStartDateTime;
            $officeHourSeriesEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . ($repeatEvery * 7 * $repetitionOccurrence) . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));
            $officeHourSeriesEndDateTime->setTime($officeHoursSeriesEndHours, $officeHoursSeriesEndMinutes);

        } else {
            $officeHourSeriesEndDateTime = clone $officeHourSeries->getSlotEnd();
        }

        // To check office hours series end date is greater than current academic year date
        if ($officeHourSeriesEndDateTime > $academicYearEndDate) {
            $officeHourSeriesEndDateTime->setDate($academicYearEndDate->format('Y'), $academicYearEndDate->format('m'), $academicYearEndDate->format('d'));
        }

        //Set the count of individual office hours within the series to 0. Clone the start date time, and create the intervals on which the
        // weekly office hours are to be created.
        $this->countOfOfficeHoursInSeries = 0;
        $firstDayInWeek = clone $adjustedOfficeHoursSeriesStartDateTime;
        $officeHoursSeriesIntervalStartDateTime = clone $adjustedOfficeHoursSeriesStartDateTime;
        $officeHoursSeriesIntervalEndDateTime = null;
        $previousIntervalEndDateTime = null;

        // While the interval start date is less than the end date of the overall series, create new office hours.
        while ($officeHoursSeriesIntervalStartDateTime < $officeHourSeriesEndDateTime) {

            //Get the first day of the week for the week of the office hours start date.
            $firstDayInWeek = $this->dateUtilityService->getFirstDayOfWeekForDatetime($firstDayInWeek);
            for ($index = $indexOfFirstDayInWeek; $index < count($daysSelected); $index++) {

                //Get the first day of the week, then adjust it forward to match the desired dates of the office hours series.
                $officeHoursSeriesIntervalStartDateTime = $this->dateUtilityService->getFirstDayOfWeekForDatetime($firstDayInWeek);
                $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . $daysSelected[$index] . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));

                //Set the interval end date time.
                $officeHoursSeriesIntervalEndDateTime = clone $officeHoursSeriesIntervalStartDateTime;
                $officeHoursSeriesIntervalEndDateTime->setTime($officeHoursSeriesEndHours, $officeHoursSeriesEndMinutes);

                //If the hour of the start datetime is greater than the hour of the end datetime, then the interval crosses UTC midnight.
                // Adjust the end date time forward 1 day to account for the midnight crossover.
                if ((int)$officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS) > (int)$officeHoursSeriesIntervalEndDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS)) {
                    $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_DAY));
                }

                //If there is an end date time, check for the needed Daylight Savings Time adjustment.
                // Otherwise, check for the daylight savings time interval between the original start date time of the series and the end of the first interval. 
                if ($previousIntervalEndDateTime) {
                    $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $previousIntervalEndDateTime, $officeHoursSeriesIntervalEndDateTime);
                } else {
                    $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $originalOfficeHoursSeriesStartDateTime, $officeHoursSeriesIntervalEndDateTime);
                }

                //If the daylight savings time adjustment is forward/back X hours (greater than/less than 0), then we want to adjust the current datetimes forward/backwards.
                // Setting the time on the overall interval end date ($officeHourSeriesEndDateTime) will ensure that the while loop completes properly.
                // Adjust the hours variable, since that's what dictates the end time later on.
                // The first day in the week must also be adjusted, since that's what the interval start date references when it's recreated on the next week.
                if ($daylightSavingsTimeAdjustment > 0) {
                    $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                    $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval($datetimeAdjustment));
                    $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval($datetimeAdjustment));
                    $officeHourSeriesEndDateTime->add(new \DateInterval($datetimeAdjustment));
                    $firstDayInWeek->add(new \DateInterval($datetimeAdjustment));
                    $officeHoursSeriesEndHours += $daylightSavingsTimeAdjustment;
                } else if ($daylightSavingsTimeAdjustment < 0) {
                    $daylightSavingsTimeAdjustment = abs($daylightSavingsTimeAdjustment);
                    $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                    $officeHoursSeriesIntervalStartDateTime->sub(new \DateInterval($datetimeAdjustment));
                    $officeHoursSeriesIntervalEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                    $officeHourSeriesEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                    $firstDayInWeek->sub(new \DateInterval($datetimeAdjustment));
                    $officeHoursSeriesEndHours = $officeHoursSeriesEndHours - $daylightSavingsTimeAdjustment;
                }

                //If the interval start date is greater than the end date, don't create the next interval.
                if (($officeHoursSeriesIntervalStartDateTime > $officeHourSeriesEndDateTime) || ($repetitionRange == 'E' && $this->countOfOfficeHoursInSeries == $repetitionOccurrence)) {
                    $officeHourSeriesEndDateTime = $adjustedOfficeHoursSeriesStartDateTime;
                    break;
                }

                //If the operation is a create ($createFlag = true), create the new interval. Otherwise, edit the interval.
                if ($createFlag) {
                    $this->createOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                } else {
                    $this->editOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                }

                $previousIntervalEndDateTime = clone $officeHoursSeriesIntervalEndDateTime;
            }

            //Reset the day of the week counter for the next week.
            $indexOfFirstDayInWeek = 0;
            $firstDayInWeek->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_SET_DAYS_HEADER . $repeatEvery * 7 . CoreBundleConstant::DATE_INTERVAL_SET_DAYS));
            $officeHoursSeriesIntervalStartDateTime = clone $firstDayInWeek;
            $previousIntervalEndDateTime = clone $officeHoursSeriesIntervalEndDateTime;
        }
    }

    /**
     * This method will divide the series down into monthly segments,
     * and pass those segments to createOfficeHourInterval() or editOfficeHourInterval() to create/edit the individual office hours
     * within that monthly segment.
     *
     * @param OfficeHoursSeries $officeHourSeries
     * @param \DateTimeZone $organizationDateTimeZoneObject
     * @param array $allFutureSlotDates
     * @param DateTime $academicYearEndDate
     * @param bool $createFlag
     */
    private function splitOfficeHoursSeriesIntoMonthlyIntervals($officeHourSeries, $organizationDateTimeZoneObject, $allFutureSlotDates, $academicYearEndDate, $createFlag = false)
    {
        $repetitionOccurrence = $officeHourSeries->getRepetitionOccurrence();
        $meetingLength = $officeHourSeries->getMeetingLength();
        $adjustedOfficeHoursStartDateTime = clone $officeHourSeries->getSlotStart();
        $originalOfficeHoursSeriesStartDateTime = clone $adjustedOfficeHoursStartDateTime;
        $daysSelected = $this->getDaysSelected($officeHourSeries);

        $this->countOfOfficeHoursInSeries = 0;
        $adjustedOfficeHoursStartDateTime = $this->getSeriesSlotStartDate($adjustedOfficeHoursStartDateTime, $officeHourSeries);
        $officeHourSeriesEndDateTime = $this->getOfficeHoursEndDate($adjustedOfficeHoursStartDateTime, $officeHourSeries, $repetitionOccurrence);

        // To check office hours series end date is greater than current academic year date
        if ($officeHourSeriesEndDateTime > $academicYearEndDate) {
            $officeHourSeriesEndDateTime->setDate($academicYearEndDate->format('Y'), $academicYearEndDate->format('m'), $academicYearEndDate->format('d'));
        }

        $currentDate = new \DateTime('now');
        $this->getStartSlot($adjustedOfficeHoursStartDateTime, $currentDate);
        $monthNumber = (int)$adjustedOfficeHoursStartDateTime->format(CoreBundleConstant::DATE_FORMAT_MONTH);

        $officeHoursSeriesIntervalStartDateTime = clone $adjustedOfficeHoursStartDateTime;
        $officeHoursSeriesIntervalEndDateTime = null;
        $previousIntervalEndDateTime = null;
        $officeHoursSeriesEndHours = $officeHourSeriesEndDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS);
        $officeHoursSeriesEndMinutes = $officeHourSeriesEndDateTime->format(CoreBundleConstant::DATE_FORMAT_MINUTES);

        while ($officeHoursSeriesIntervalStartDateTime < $officeHourSeriesEndDateTime) {
            for ($dayOfTheWeek = 0; $dayOfTheWeek < 7; $dayOfTheWeek++) {
                if ($officeHoursSeriesIntervalStartDateTime < $officeHourSeries->getSlotStart()) {
                    $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_DAY));
                    continue;
                }

                $dayNumber = $officeHoursSeriesIntervalStartDateTime->format('w');
                if (in_array($dayNumber, $daysSelected) && $monthNumber == (int)$officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_MONTH)) {

                    $officeHoursSeriesIntervalEndDateTime = clone $officeHoursSeriesIntervalStartDateTime;
                    $officeHoursSeriesIntervalEndDateTime->setTime($officeHoursSeriesEndHours, $officeHoursSeriesEndMinutes);

                    //If the hour of the start datetime is greater than the hour of the end datetime, then the interval crosses UTC midnight.
                    // Adjust the end date time forward 1 day to account for the midnight crossover.
                    if ((int)$officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS) > (int)$officeHoursSeriesIntervalEndDateTime->format(CoreBundleConstant::DATE_FORMAT_HOURS)) {
                        $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_DAY));
                    }

                    if ($previousIntervalEndDateTime) {
                        $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $previousIntervalEndDateTime, $officeHoursSeriesIntervalEndDateTime);
                    } else {
                        $daylightSavingsTimeAdjustment = $this->dateUtilityService->getDaylightSavingsTimeOffsetAdjustment($organizationDateTimeZoneObject, $originalOfficeHoursSeriesStartDateTime, $officeHoursSeriesIntervalEndDateTime);
                    }

                    //If the daylight savings time adjustment is forward/back X hours (greater than/less than 0), then we want to adjust the current datetimes forward/backwards.
                    // Setting the time on the overall interval end date ($officeHourSeriesEndDateTime) will ensure that the while loop completes properly.
                    // Adjust the hours variable, since that's what dictates the end time later on.
                    // The first day in the week must also be adjusted, since that's what the interval start date references when it's recreated on the next week.
                    if ($daylightSavingsTimeAdjustment > 0) {
                        $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                        $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval($datetimeAdjustment));
                        $officeHoursSeriesIntervalEndDateTime->add(new \DateInterval($datetimeAdjustment));
                        $officeHourSeriesEndDateTime->add(new \DateInterval($datetimeAdjustment));
                        $officeHoursSeriesEndHours += $daylightSavingsTimeAdjustment;
                    } else if ($daylightSavingsTimeAdjustment < 0) {
                        $daylightSavingsTimeAdjustment = abs($daylightSavingsTimeAdjustment);
                        $datetimeAdjustment = CoreBundleConstant::DATE_INTERVAL_SET_HOURS_HEADER . $daylightSavingsTimeAdjustment . CoreBundleConstant::DATE_INTERVAL_SET_HOURS;

                        $officeHoursSeriesIntervalStartDateTime->sub(new \DateInterval($datetimeAdjustment));
                        $officeHoursSeriesIntervalEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                        $officeHourSeriesEndDateTime->sub(new \DateInterval($datetimeAdjustment));
                        $officeHoursSeriesEndHours = $officeHoursSeriesEndHours - $daylightSavingsTimeAdjustment;
                    }

                    if ($createFlag) {
                        $this->createOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                    } else {
                        $this->editOfficeHourInterval($officeHoursSeriesIntervalStartDateTime, $officeHoursSeriesIntervalEndDateTime, $meetingLength, $officeHourSeries, $allFutureSlotDates);
                    }

                    if ($officeHourSeries->getRepetitionRange() == 'E' && $this->countOfOfficeHoursInSeries == $repetitionOccurrence) {
                        $officeHourSeriesEndDateTime = $officeHourSeries->getSlotStart();
                    }
                }
                $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval(CoreBundleConstant::DATE_INTERVAL_ONE_DAY));
                $previousIntervalEndDateTime = clone $officeHoursSeriesIntervalEndDateTime;
            }

            $officeHoursSeriesIntervalStartDateTime->add(new \DateInterval('P' . $officeHourSeries->getRepeatEvery() . 'M'));
            $monthNumber = (int)$officeHoursSeriesIntervalStartDateTime->format(CoreBundleConstant::DATE_FORMAT_MONTH);
            $officeHoursSeriesIntervalStartDateTime = $this->getSeriesSlotStartDate($officeHoursSeriesIntervalStartDateTime, $officeHourSeries);
            $previousIntervalEndDateTime = clone $officeHoursSeriesIntervalEndDateTime;

        }
    }

    /**
     * Cancel office hours
     *
     * @param int $person
     * @param bool $isProxy
     * @param int $id
     */
    public function cancel($person, $isProxy, $id)
    {
        $this->logger->debug(">>>> Cancel Office hours IS PROXY" . $isProxy . "Id" . $id);

        // Cancel Office Hours
        $officeHours = $this->officeHoursRepository->find($id);
        if (! isset($officeHours)) {
            throw new ValidationException([
                self::ERROR_OFFICEHOURS_NOT_FOUND
            ], self::ERROR_OFFICEHOURS_NOT_FOUND, self::ERROR_OFFICEHOURS_NOT_FOUND_ERROR_KEY);
        }
        $person = $this->personService->findPerson($person);
        if (! isset($person)) {
            throw new ValidationException([
                'Person Not Found.'
            ], 'Person Not Found.', 'Person_not_found');
        }
        if ($isProxy) {
            $officeHours->setPersonProxyCancelled($person);
            $person = $officeHours->getPerson();
        }
        if ($officeHours) {
            $officeHours->setIsCancelled(true);
        }
        // Update Appointment table for isFreeStanding
        if ($officeHours->getAppointments()) {
            $appointment = $this->appointmentsRepository->find($officeHours->getAppointments());
            if (isset($appointment)) {
                $appointment->setIsFreeStanding(true);
            }
        }
        $this->officeHoursRepository->remove($officeHours);
        $this->officeHoursRepository->flush();

        // Remove from external calendar.
        $externalCalendarEventId = $officeHours->getGoogleAppointmentId();
        if (!empty($externalCalendarEventId)) {
            $organizationId = $person->getOrganization()->getId();
            $facultyId = $person->getId();
            $officeHourId = $officeHours->getId();
            $this->calendarIntegrationService->syncOneOffEvent($organizationId, $facultyId, $officeHourId, 'office_hour', 'delete', $externalCalendarEventId);
        }
    }

    /**
     * Get Office Hours Series
     *
     * @param int $id
     * @return OfficeHoursDto
     */
    public function getOfficeHourSeries($id)
    {
        $this->logger->debug(">>>> Getting Office Hour Series " . $id);

        $officeHour = $this->officeHoursRepository->find($id);
        if (! $officeHour) {
            throw new ValidationException([
                self::ERROR_OFFICEHOURS_NOT_FOUND
            ], self::ERROR_OFFICEHOURS_NOT_FOUND, self::ERROR_OFFICEHOURS_NOT_FOUND_ERROR_KEY);
        }
        $officeHourSeries = $officeHour->getOfficeHoursSeries();
        if (! $officeHourSeries) {
            throw new ValidationException([
                'Office hours Series Not Found.'
            ], 'Office hours  Series Not Found.', self::ERROR_OFFICEHOURS_NOT_FOUND_ERROR_KEY);
        }

        $officeHourDto = new OfficeHoursDto();
        $officeHourDto->setOfficeHoursId($officeHourSeries->getId());
        $officeHourDto->setLocation($officeHourSeries->getLocation());
        $officeHourDto->setMeetingLength($officeHourSeries->getMeetingLength());
        $officeHourDto->setOrganizationId($officeHourSeries->getOrganization()
            ->getId());
        $officeHourDto->setPersonId($officeHourSeries->getPerson()
            ->getId());
        $officeHourDto->setPersonIdProxy(($officeHourSeries->getPersonProxy()) ? $officeHourSeries->getPersonProxy()
            ->getId() : 0);
        $startDateTime = $officeHourSeries->getSlotStart();
        $endDateTime = $officeHourSeries->getSlotEnd();

        $officeHourDto->setSlotStart($startDateTime);
        $officeHourDto->setSlotEnd($endDateTime);
        $officeHourDto->setSlotType('S');
        $offSeries = new OfficeHoursSeriesDto();
        $offSeries->setRepeatDays($officeHourSeries->getDays());
        $offSeries->setRepeatEvery($officeHourSeries->getRepeatEvery());
        $offSeries->setRepeatMonthlyOn($officeHourSeries->getRepeatMonthlyOn());
        $offSeries->setRepeatOccurence($officeHourSeries->getRepetitionOccurrence());
        $offSeries->setRepeatPattern($officeHourSeries->getRepeatPattern());
        $offSeries->setRepeatRange($officeHourSeries->getRepetitionRange());
        $offSeries->setMeetingLength($officeHourSeries->getMeetingLength());
        $offSeries->setIncludeSatSun($officeHourSeries->getIncludeSatSun());
        $officeHourDto->setSeriesInfo($offSeries);        
        $this->logger->info(">>>> Getting Office Hour Series " );
        return $officeHourDto;
    }

    /**
     * Get Office Hour
     *
     * @param int $id
     * @return OfficeHoursDto
     */
    public function getOfficehour($id)
    {
        $this->logger->debug(">>>> Getting Individual Office hour " . $id);

        $officeHour = $this->officeHoursRepository->find($id);
        if (! $officeHour) {
            throw new ValidationException([
                self::ERROR_OFFICEHOURS_NOT_FOUND
            ], self::ERROR_OFFICEHOURS_NOT_FOUND, self::ERROR_OFFICEHOURS_NOT_FOUND_ERROR_KEY);
        }

        $officeHourDto = new OfficeHoursDto();
        $officeHourDto->setOfficeHoursId($officeHour->getId());
        $officeHourDto->setLocation($officeHour->getLocation());
        $officeHourDto->setOrganizationId($officeHour->getOrganization()
            ->getId());
        $officeHourDto->setPersonId($officeHour->getPerson()
            ->getId());
        $officeHourDto->setPersonIdProxy(($officeHour->getPersonProxyCreated()) ? $officeHour->getPersonProxyCreated()
            ->getId() : 0);
        $startDateTime = $officeHour->getSlotStart();
        $endDateTime = $officeHour->getSlotEnd();

        $officeHourDto->setSlotStart($startDateTime);
        $officeHourDto->setSlotEnd($endDateTime);
        $officeHourDto->setSlotType('I');
        $officeHourDto->setMeetingLength($officeHour->getMeetingLength());        
        $officeHourDto->setPcsCalendarId($officeHour->getGoogleAppointmentId());
        $this->logger->info(">>>> Getting Individual Office hour " );
        return $officeHourDto;
    }

    /**
     * Delete office hour series
     *
     * @param int $officeHoursSeriesId
     * @param int $organizationId
     * @param int $loggedInUserId
     * @throws ValidationException
     */
    public function deleteOfficeHourSeries($officeHoursSeriesId, $organizationId, $loggedInUserId)
    {
        $officeHourSeries = $this->officeHoursSeriesRepository->findOneBy(['id' => $officeHoursSeriesId, 'organization' => $organizationId]);
        $this->isOfficeHourExists($officeHourSeries);
        $currentDate = new \DateTime('now');
        $currentDate = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        $this->officeHoursRepository->removeExistingSlots($officeHoursSeriesId, $currentDate, $loggedInUserId);

        // Get free standing appointments
        $freeStandingAppointments = $this->officeHoursRepository->getFreeStandingAppointments($officeHoursSeriesId, $currentDate);
        if (!empty($freeStandingAppointments)) {
            $this->officeHoursRepository->updateAppointmentAsFreeStanding($freeStandingAppointments, $currentDate, $loggedInUserId);
        }

        $personId = $officeHourSeries->getPerson()->getId();

        // sync to external calendar.
        $this->calendarIntegrationService->syncOfficeHourSeries($officeHoursSeriesId, $organizationId, $personId, 'delete');
    }

    private function getNumberOfSatSun($start,$end)
    {
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end);
        $no = 0;
        foreach ($period as $dt) {
            if ($dt->format('N') == 7 || $dt->format('N') == 6) {
                $no++;
            }
        }
        $this->logger->info("************************* Number Sat SUN  : ".$no);
        return $no;
        
    }
    
    private function getCurrentAcademicYearEndDate($organizationId)
    {
        $currentDate = new \DateTime('now');
        $yearDetails = $this->orgAcademicYearRepository->getCurrentAcademicDetails($currentDate->setTime(0, 0, 0), $organizationId);
        if($yearDetails) {
            $this->logger->info("************************* Acdemic Year End Date : ".$yearDetails[0]['endDate']);
            return  new \DateTime($yearDetails[0]['endDate']);
        }else{
           return  new \DateTime($currentDate->format('Y')."-12-31");
        }
    }

    /**
     * Function to validate office hour DTO
     *
     * @param object $officeHoursDto
     * @throws ValidationException
     * @return mixed
     */
    public function validateData($officeHoursDto)
    {
        $person = $this->personService->findPerson($officeHoursDto->getPersonId());
        $organization = $person->getOrganization();

        // UTC timezone is assigned to $timezone.
        $timezone = $person->getOrganization()->getTimeZone();
        $timezone = $this->metadataListRepository->findByListName($timezone);
        
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        $this->isValidEndDate($officeHoursDto);
        $start = $officeHoursDto->getSlotStart();
        $end = $officeHoursDto->getSlotEnd();
        
        // start Date must greater than now
        $currentDateTime = new \DateTime('now',new \DateTimeZone($timezone));

        $this->isValidStartDate($currentDateTime, $start);
        
        if ($officeHoursDto->getSeriesInfo()->getRepeatRange() == 'N') {
            $academicYearEndDate = $this->getCurrentAcademicYearEndDate($organization);
           
            $end->setDate($academicYearEndDate->format('Y'), $academicYearEndDate->format('m'), $academicYearEndDate->format('d'));
         
            if ($end < $start) {
                throw new ValidationException([
                    'Academic Year end date less than start date.'
                    ], 'Invalid End Slot Date', 'invalid_end_date');
            }
        
        }
        return $officeHoursDto;
    }
    
    
    protected function validateErrors($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage();
            }
    
            throw new ValidationException([
                $errorsString
                ], $errorsString, 'duplicate_error');
        }
    }

    /**
     * delete office hour and set appointment as free standing
     *
     * @param int $id
     * @param int $organizationId
     * @return void
     */
    public function deleteOfficeHour($id, $organizationId)
    {
        $existingOfficeHour = $this->officeHoursRepository->findOneBy([
            'id' => $id,
            'organization' => $organizationId
        ]);

        $this->isOfficeHourFound($existingOfficeHour);
        $personId = $existingOfficeHour->getPerson()->getId();

        // Verifying passed personId through API to loggedInPersonId
        $this->rbacManager->validateUserAsAuthorizedAppointmentUser($personId);
        $appointment = $existingOfficeHour->getAppointments();
        if ($appointment) {
            $appointment->setIsFreeStanding(true);
        }
        $googleAppointmentId = $existingOfficeHour->getGoogleAppointmentId();
        $this->officeHoursRepository->remove($existingOfficeHour);
        $this->officeHoursRepository->flush();

        if ($googleAppointmentId) {
            $this->calendarIntegrationService->syncOneOffEvent($organizationId, $personId, $existingOfficeHour->getId(), 'office_hour', 'delete', $googleAppointmentId);
        }
    }

    /**
     * Return slot start date for monthly and weekly series.
     *
     * @param DateTime $startSlot
     * @param OfficeHoursSeries $officeHourSeries
     * @return DateTime
     */
    public function getSeriesSlotStartDate($startSlot, OfficeHoursSeries $officeHourSeries)
    {
        //$repeatMonthOn -> week of the month on which the series occurs
        $repeatMonthOn = $officeHourSeries->getRepeatMonthlyOn();

        //$daysSelected -> array of keys that had a value of 1, indicating on which days of the week the series occurred.
        $daysSelected = $this->getDaysSelected($officeHourSeries);
        $startDateOriginal = clone $officeHourSeries->getSlotStart();

        $weekNoArray = array("1" => "First", "2" => "Second", "3" => "Third", "4" => "Fourth");
        $weekDaysArray = array("0" => "Sunday", "1" => "Monday", "2" => "Tuesday", "3" => "Wednesday", "4" => "Thursday", "5" => "Friday", "6" => "Saturday");
        if ($repeatMonthOn == 0 && $officeHourSeries->getRepeatPattern() == 'M') {
            $repeatMonthOn = 1;
        }

        //$start -> first day of the series in the first week. 0-6 Sunday-Saturday
        $start = 0;
        $startDateYear = $startSlot->format(SynapseConstant::YEAR_DATETIME_FORMAT);
        $startDateMonth = $startSlot->format(SynapseConstant::MONTH_DATETIME_FORMAT);
        $firstStartMonthDate = new \DateTime("First day of " . $startDateYear . "-" . $startDateMonth . "-" . SynapseConstant::FIRST_DATE_IN_MONTH);
        $dayNo = (int)$firstStartMonthDate->format(SynapseConstant::DAY_OF_WEEK_DATETIME_FORMAT);

        for ($i = $dayNo; $i < SynapseConstant::DAYS_IN_WEEK; $i++) {
            if (in_array($i, $daysSelected)) {
                $start = array_search($i, $daysSelected);
                break;
            }
        }

        //$thisMonthFlag -> is the first day of the series in the current month
        $thisMonthFlag = false;
        $weekStartSlotDate = array();

        for ($i = $start; $i < count($daysSelected); $i++) {
            //Example creation string -> "First Sunday of 2017-12-01"
            $startSlotWeekDate = new \DateTime($weekNoArray[$repeatMonthOn] . " " . $weekDaysArray[$daysSelected[$i]] . " of " . $startDateYear . "-" . $startDateMonth . "-" . SynapseConstant::FIRST_DATE_IN_MONTH);
            //Sets the time of the first slot to the time of the start slot
            $tempStart = new \DateTime($startSlotWeekDate->format(SynapseConstant::DEFAULT_DATE_FORMAT) . ' ' . $startSlot->format(SynapseConstant::DEFAULT_TIME_FORMAT));
            if ($tempStart >= $startDateOriginal) {
                $thisMonthFlag = true;
                $weekStartSlotDate[] = $tempStart;
                break;
            }
        }

        // If the series is not starting in this month, get the first day from the next month.
        if (!$thisMonthFlag && empty($weekStartSlotDate)) {
            $seriesStartDate = clone $startSlot;
            $seriesStartDate->add(new \DateInterval(SynapseConstant::ONE_MONTH_DURATION));
            $firstDayOfNextMonth = $seriesStartDate->format(SynapseConstant::YEAR_DATETIME_FORMAT) . "-" . $seriesStartDate->format(SynapseConstant::MONTH_DATETIME_FORMAT) . "-" . SynapseConstant::FIRST_DATE_IN_MONTH;
            $firstMonthDate = new \DateTime("First day of " . $firstDayOfNextMonth);
            $firstDayNo = (int)$firstMonthDate->format(SynapseConstant::DAY_OF_WEEK_DATETIME_FORMAT);

            $start = 0;
            for ($i = $firstDayNo; $i < SynapseConstant::DAYS_IN_WEEK; $i++) {
                if (in_array($i, $daysSelected)) {
                    $start = array_search($i, $daysSelected);
                    break;
                }
            }
            //Example creation string -> "First Sunday of 2017-12-01"
            $startSlotWeekDate = new \DateTime($weekNoArray[$repeatMonthOn] . " " . $weekDaysArray[$daysSelected[$start]] . " of " . $firstDayOfNextMonth);
            $weekStartSlotDate[] = new \DateTime($startSlotWeekDate->format(SynapseConstant::DEFAULT_DATE_FORMAT) . ' ' . $startSlot->format(SynapseConstant::DEFAULT_TIME_FORMAT));
        }
        //Returns the first slot start date of the series.
        return $weekStartSlotDate[0];
    }
}