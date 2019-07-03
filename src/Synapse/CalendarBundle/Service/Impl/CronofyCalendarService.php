<?php
namespace Synapse\CalendarBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CalendarBundle\Exception\CronofyException;
use Synapse\CalendarBundle\Service\Impl\CronofyPagedResultIterator;

/**
 * @DI\Service("cronofy_calendar_service")
 */
class CronofyCalendarService
{
    const USER_AGENT = 'Cronofy PHP 0.7';
    const API_ROOT_URL = 'https://api.cronofy.com';
    const SERVICE_KEY = 'cronofy_calendar_service';
    const API_VERSION = 'v1';
    public $clientId;
    public $clientSecret;
    public $accessToken;
    public $refreshToken;

    /**
     * Initialize cronofy object variables
     *
     * @param string|boolean $clientId
     * @param string|boolean $clientSecret
     * @param string|boolean $accessToken
     * @param string|boolean $refreshToken
     * @throws CronofyException
     */
    public function enableAuthentication($clientId = false, $clientSecret = false, $accessToken = false, $refreshToken = false)
    {
        if (!function_exists('curl_init')) {
            throw new CronofyException("missing cURL extension", 1);
        }
        if (!empty($clientId)) {
            $this->clientId = $clientId;
        }
        if (!empty($clientSecret)) {
            $this->clientSecret = $clientSecret;
        }
        if (!empty($accessToken)) {
            $this->accessToken = $accessToken;
        }
        if (!empty($refreshToken)) {
            $this->refreshToken = $refreshToken;
        }
    }

    /**
     * Initiate cronofy CURL request for GET API
     *
     * @param string $path
     * @param array $params
     * @return string
     * @throws CronofyException
     */
    private function getHttpRequest($path, array $params = array())
    {
        $url = $this->getApiUrl($path);
        $url .= $this->getUrlParams($params);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new CronofyException('invalid URL', null, null, $url);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthHeaders());
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);
        $result = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new CronofyException(curl_error($curl), 2, null, $url);
        }
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $this->getHandleResponse($result, $status_code, $url);
    }

    /**
     * Initiate cronofy CURL request for POST API
     *
     * @param string $path
     * @param array $params
     * @return string
     * @throws CronofyException
     */
    private function postHttpRequest($path, array $params = array())
    {
        $url = $this->getApiUrl($path);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new CronofyException('invalid URL', null, null, $url);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthHeaders(true));
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $result = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new CronofyException(curl_error($curl), 3, null, $url);
        }
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $this->getHandleResponse($result, $status_code, $url);
    }

    /**
     * Initiate cronofy CURL request for DELETE API
     *
     * @param string $path
     * @param array $params
     * @return string
     * @throws CronofyException
     */
    private function deleteHttpRequest($path, array $params = array())
    {
        $url = $this->getApiUrl($path);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new CronofyException('invalid URL', null, null, $url);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthHeaders(true));
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        $result = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new CronofyException(curl_error($curl), 4, null, $url);
        }
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $this->getHandleResponse($result, $status_code, $url);
    }

    /**
     * Function to get the authorized URL
     *
     * @param array $params An array of additional parameters
     * redirect_uri : String The HTTP or HTTPS URI you wish the user's authorization request decision to be redirected to. REQUIRED
     * scope : An array of scopes to be granted by the access token. Possible scopes detailed in the Cronofy API documentation. REQUIRED
     * state : String A value that will be returned to you unaltered along with the user's authorization request decision. OPTIONAL
     * avoid_linking : Boolean when true means we will avoid linking calendar accounts together under one set of credentials. OPTIONAL
     * @return string $url : The URL to authorize your access to the Cronofy API
     */
    public function getAuthorizationURL($params)
    {
        $scopeList = join(" ", $params['scope']);
        $url = "https://app.cronofy.com/oauth/authorize?response_type=code&client_id=" . $this->clientId . "&redirect_uri=" . urlencode($params['redirect_uri']) . "&scope=" . $scopeList;
        if (!empty($params['state'])) {
            $url .= "&state=" . $params['state'];
        }
        if (!empty($params['avoid_linking'])) {
            $url .= "&avoid_linking=" . $params['avoid_linking'];
        }
        return $url;
    }

    /**
     * The URL to authorize your enterprise connect access to the Cronofy API
     *
     * @param array $params
     * redirect_uri : String. The HTTP or HTTPS URI you wish the user's authorization request decision to be redirected to. REQUIRED
     * scope : Array. An array of scopes to be granted by the access token. Possible scopes detailed in the Cronofy API documentation. REQUIRED
     * delegated_scope : Array. An array of scopes to be granted that will be allowed to be granted to the account's users. REQUIRED
     * state : String. A value that will be returned to you unaltered along with the user's authorization request decision. OPTIONAL
     * @return string
     */
    public function getEnterpriseConnectAuthorizationUrl($params)
    {
        $scopeList = rawurlencode(join(" ", $params['scope']));
        $delegated_scope_list = rawurlencode(join(" ", $params['delegated_scope']));
        $url = "https://app.cronofy.com/enterprise_connect/oauth/authorize?response_type=code&client_id=" . $this->clientId . "&redirect_uri=" . urlencode($params['redirect_uri']) . "&scope=" . $scopeList . "&delegated_scope=" . $delegated_scope_list;
        if (!empty($params['state'])) {
            $url .= "&state=" . rawurlencode($params['state']);
        }
        return $url;
    }

    /**
     * Request Cronofy tokens, true if successful, error string if not
     *
     * @param array $params
     * redirect_uri : String The HTTP or HTTPS URI you wish the user's authorization request decision to be redirected to. REQUIRED
     * code: The short-lived, single-use code issued to you when the user authorized your access to their account as part of an Authorization  REQUIRED
     * @return boolean
     */
    public function requestToken($params)
    {
        $postFields = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $params['code'],
            'redirect_uri' => $params['redirect_uri']
        );
        $tokens = $this->postHttpRequest("/oauth/token", $postFields);
        if (!empty($tokens["access_token"])) {
            return $tokens;
        } else {
            return $tokens["error"];
        }
    }

    /**
     * Function to set the refresh token, true if successful, error string if not
     *
     * @return boolean
     */
    public function refreshToken()
    {
        $postFields = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken
        );
        $tokens = $this->postHttpRequest("/oauth/token", $postFields);
        if (!empty($tokens["access_token"])) {
            return $tokens;
        } else {
            return $tokens["error"];
        }
    }

    /**
     * Function to revoke authorization, true if successful, error string if not
     *
     * @param string $token - Either the refresh_token or access_token for the authorization you wish to revoke. REQUIRED
     * @return string
     */
    public function revokeAuthorization($token)
    {
        $postFields = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'token' => $token
        );
        return $this->postHttpRequest("/oauth/token/revoke", $postFields);
    }

    /**
     * info for the user logged in.
     *
     * @return string
     */
    public function getAccount()
    {
        return $this->getHttpRequest("/" . self::API_VERSION . "/account");
    }

    /**
     * list of all the authenticated user's calendar profiles
     *
     * @return string
     */
    public function getProfiles()
    {
        return $this->getHttpRequest("/" . self::API_VERSION . "/profiles");
    }

    /**
     * list of calendars
     *
     * @return string
     */
    public function listCalendars()
    {
        return $this->getHttpRequest("/" . self::API_VERSION . "/calendars");
    }

    /**
     * Read events
     *
     * @param array $params
     * Date from : The minimum date from which to return events. Defaults to 16 days in the past. OPTIONAL
     * Date to : The date to return events up until. Defaults to 201 days in the future. OPTIONAL
     * String tzid : A string representing a known time zone identifier from the IANA Time Zone Database. REQUIRED
     * Boolean include_deleted : Indicates whether to include or exclude events that have been deleted. Defaults to excluding deleted events. OPTIONAL
     * Boolean include_moved: Indicates whether events that have ever existed within the given window should be included or excluded from the results. Defaults to only include events currently within the search window. OPTIONAL
     * Time last_modified : The Time that events must be modified on or after in order to be returned. Defaults to including all events regardless of when they were last modified. OPTIONAL
     * Boolean include_managed : Indicates whether events that you are managing for the account should be included or excluded from the results. Defaults to include only non-managed events. OPTIONAL
     * Boolean only_managed : Indicates whether only events that you are managing for the account should be included in the results. OPTIONAL
     * Array calendar_ids : Restricts the returned events to those within the set of specified calendar_ids. Defaults to returning events from all of a user's calendars. OPTIONAL
     * Boolean localized_times : Indicates whether the events should have their start and end times returned with any available localization information. Defaults to returning start and end times as simple Time values. OPTIONAL
     *
     * @return CronofyPagedResultIterator
     */
    public function readEvents($params)
    {
        $url = $this->getApiUrl("/" . self::API_VERSION . "/events");
        return new CronofyPagedResultIterator($this, "events", $this->getAuthHeaders(), $url, $this->getUrlParams($params));
    }

    /**
     * Free busy events from External calendar
     *
     * @param array $params
     * Date from : The minimum date from which to return free-busy information. Defaults to 16 days in the past. OPTIONAL
     * Date to : The date to return free-busy information up until. Defaults to 201 days in the future. OPTIONAL
     * String tzid : A string representing a known time zone identifier from the IANA Time Zone Database. REQUIRED
     * Boolean include_managed : Indicates whether events that you are managing for the account should be included or excluded from the results. Defaults to include only non-managed events. OPTIONAL
     * Array calendar_ids : Restricts the returned free-busy information to those within the set of specified calendar_ids. Defaults to returning free-busy information from all of a user's calendars. OPTIONAL
     * Boolean localized_times : Indicates whether the free-busy information should have their start and end times returned with any available localization information. Defaults to returning start and end times as simple Time values. OPTIONAL
     *
     * @return CronofyPagedResultIterator
     */
    public function getFreeBusyEvents($params)
    {
        $url = $this->getApiUrl("/" . self::API_VERSION . "/free_busy");
        return new CronofyPagedResultIterator($this, "free_busy", $this->getAuthHeaders(), $url, $this->getUrlParams($params));
    }

    /**
     * Insert update event
     * @param array $params
     * calendar_id : The calendar_id of the calendar you wish the event to be added to. REQUIRED
     * String event_id : The String that uniquely identifies the event. REQUIRED
     * String summary : The String to use as the summary, sometimes referred to as the name, of the event. REQUIRED
     * String description : The String to use as the description, sometimes referred to as the notes, of the event. REQUIRED
     * String tzid : A String representing a known time zone identifier from the IANA Time Zone Database. OPTIONAL
     * Time start: The start time can be provided as a simple Time string or an object with two attributes, time and tzid. REQUIRED
     * Time end: The end time can be provided as a simple Time string or an object with two attributes, time and tzid. REQUIRED
     * String location.description : The String describing the event's location. OPTIONAL
     * @return string
     */
    public function upsertEvent($params)
    {
        $postFields = array(
            'event_id' => $params['event_id'],
            'summary' => $params['summary'],
            'description' => $params['description'],
            'start' => $params['start'],
            'end' => $params['end']
        );
        if (!empty($params['tzid'])) {
            $postFields['tzid'] = $params['tzid'];
        }
        if (!empty($params['location']['description'])) {
            $postFields['location']['description'] = $params['location']['description'];
        }
        if(!empty($params['reminders'])) {
            $postFields['reminders'] = $params['reminders'];
        }
        if(!empty($params['transparency'])) {
            $postFields['transparency'] = $params['transparency'];
        }
        return $this->postHttpRequest("/" . self::API_VERSION . "/calendars/" . $params['calendar_id'] . "/events", $postFields);
    }

    /**
     * Delete an event
     *
     * @param array $params
     * calendar_id : The calendar_id of the calendar you wish the event to be removed from. REQUIRED
     * String event_id : The String that uniquely identifies the event. REQUIRED
     * @return string
     */
    public function deleteEvent($params)
    {
        $postFields = array('event_id' => $params['event_id']);
        return $this->deleteHttpRequest("/" . self::API_VERSION . "/calendars/" . $params['calendar_id'] . "/events", $postFields);
    }

    /**
     * Function to create channel
     *
     * @param array $params
     * String callback_url : The URL that is notified whenever a change is made. REQUIRED
     * @return string
     */
    public function createChannel($params)
    {
        $postFields = array('callback_url' => $params['callback_url']);
        return $this->postHttpRequest("/" . self::API_VERSION . "/channels", $postFields);
    }

    /**
     * Array of channels
     *
     * @return string
     */
    public function listChannels()
    {
        return $this->getHttpRequest("/" . self::API_VERSION . "/channels");
    }

    /**
     * Delete channels
     *
     * @param array $params
     * channel_id : The ID of the channel to be closed. REQUIRED
     * @return string
     */
    public function closeChannel($params)
    {
        return $this->deleteHttpRequest("/" . self::API_VERSION . "/channels/" . $params['channel_id']);
    }

    /**
     * Delete external event
     *
     * @param array $params
     * calendar_id : The calendar_id of the calendar you wish the event to be removed from. REQUIRED
     * String event_uid : The String that uniquely identifies the event. REQUIRED
     * @return string
     */
    public function deleteExternalEvent($params)
    {
        $postFields = array('event_uid' => $params['event_uid']);
        return $this->deleteHttpRequest("/" . self::API_VERSION . "/calendars/" . $params['calendar_id'] . "/events", $postFields);
    }

    /**
     * Function to authorize with service account
     *
     * @param array $params
     * email : The email of the user to be authorized. REQUIRED
     * scope : The scopes to authorize for the user. REQUIRED
     * callback_url : The URL to return to after authorization. REQUIRED
     * @return string
     */
    public function authorizeWithServiceAccount($params)
    {
        if (isset($params["scope"]) && gettype($params["scope"]) == "array") {
            $params["scope"] = join(" ", $params["scope"]);
        }
        return $this->postHttpRequest("/" . self::API_VERSION . "/service_account_authorizations", $params);
    }

    /**
     * @param array $params
     * permissions : The permissions to elevate to. Should be in an array of `array($calendar_id, $permission_level)`. REQUIRED
     * redirect_uri : The application's redirect URI. REQUIRED
     * @return string
     */
    public function elevatedPermissions($params)
    {
        return $this->postHttpRequest("/" . self::API_VERSION . "/permissions", $params);
    }

    /**
     * Create a calendar
     *
     * @param array $params
     * profile_id : The ID for the profile on which to create the calendar. REQUIRED
     * name : The name for the created calendar. REQUIRED
     * @return string
     */
    public function createCalendar($params)
    {
        return $this->postHttpRequest("/" . self::API_VERSION . "/calendars", $params);
    }

    /**
     * @param array $params
     * calendar_id : The ID of the calendar holding the event. REQUIRED
     * event_uid : The UID of the event to chang ethe participation status of. REQUIRED
     * status : The new participation status for the event. Accepted values are: accepted, tentative, declined. REQUIRED
     * @return string
     */
    public function changeParticipationStatus($params)
    {
        $postFields = array(
            "status" => $params["status"]
        );
        return $this->postHttpRequest("/" . self::API_VERSION . "/calendars/" . $params["calendar_id"] . "/events/" . $params["event_uid"] . "/participation_status", $postFields);
    }

    /**
     * Get root URL
     *
     * @param string $path
     * @return string
     */
    private function getApiUrl($path)
    {
        return self::API_ROOT_URL . $path;
    }

    /**
     * Get URL parameters
     *
     * @param array $params
     * @return string
     */
    private function getUrlParams($params)
    {
        if (count($params) == 0) {
            return "";
        }
        $stringParams = array();
        foreach ($params as $key => $val) {
            if (gettype($val) == "array") {
                for ($i = 0; $i < count($val); $i++) {
                    array_push($stringParams, $key . "[]=" . urlencode($val[$i]));
                }
            } else {
                array_push($stringParams, $key . "=" . urlencode($val));
            }
        }
        return "?" . join("&", $stringParams);
    }

    /**
     * Set authentication headers
     *
     * @param bool $withContentHeaders
     * @return array
     */
    private function getAuthHeaders($withContentHeaders = false)
    {
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $this->accessToken;
        $headers[] = 'Host: api.cronofy.com';
        if ($withContentHeaders) {
            $headers[] = 'Content-Type: application/json; charset=utf-8';
        }
        return $headers;
    }

    /**
     * @param $response
     * @return string
     */
    private function getParsedResponse($response)
    {
        $jsonDecoded = json_decode($response, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return $response;
        }
        return $jsonDecoded;
    }

    /**
     * Handle CURL response
     *
     * @param array $result
     * @param string $statusCode
     * @param string|null $url
     * @return string
     * @throws CronofyException
     */
    public function getHandleResponse($result, $statusCode, $url = null)
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            return $this->getParsedResponse($result);
        }
        throw new CronofyException($this->httpCodes[$statusCode], $statusCode, $this->getParsedResponse($result), $url);
    }

    /**
     * Delete all events from the calendar.
     *
     * @param array $postFields
     * @return string
     */
    public function deleteAllEvents($postFields)
    {
        return $this->deleteHttpRequest("/" . self::API_VERSION . "/events", $postFields);
    }

    private $httpCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    /**
     * Insert update events in batch
     *
     * @param array $postFields
     * @return mixed
     */
    public function upsertBatchEvent($postFields)
    {
        return $this->postBatchHttpRequest("/" . self::API_VERSION . "/batch", $postFields);
    }

    /**
     * Initiate cronofy CURL request for POST API
     *
     * @param string $path
     * @param array $params
     * @return mixed
     * @throws CronofyException
     */
    private function postBatchHttpRequest($path, array $params = array())
    {
        $url = $this->getApiUrl($path);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new CronofyException('invalid URL', null, null, $url);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getAuthHeaders(true));
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $result = curl_exec($curl);
        if (curl_errno($curl) > 0) {
            throw new CronofyException(curl_error($curl), 3, null, $url);
        }
        curl_close($curl);
        return $result;
    }
}