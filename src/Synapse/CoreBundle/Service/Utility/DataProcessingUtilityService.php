<?php
namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("data_processing_utility_service")
 *
 * This class is intended for utility functions which process the data in the parameters without retrieving data from the database.
 * They are not specific to our application, and are the type of functions that could be in a generic PHP library
 * (but aren't in existing libraries, as far as we could find).
 */
class DataProcessingUtilityService
{

    const SERVICE_KEY = 'data_processing_utility_service';

    /**
     * Given an array of strings, combines them into a grammatically correct string connected with "and" or "or"
     * (for example, "a or b" or "a, b, and c").
     *
     * @param array $listAsArray
     * @param string $conjunction - "and" or "or"
     * @return string
     */
    public function formatListWithConjunction($listAsArray, $conjunction)
    {
        if (count($listAsArray) <= 2) {
            $formattedList = implode(" $conjunction ", $listAsArray);
        } else {
            $listAsArray[count($listAsArray) - 1] = "$conjunction " . $listAsArray[count($listAsArray) - 1];
            $formattedList = implode(', ', $listAsArray);
        }

        return $formattedList;
    }

    /**
     * Given an array, remove all entries with the given arrayKey
     *
     * @param mixed $array
     * @param mixed $arrayKey
     * @return array
     */
    public function recursiveRemovalByArrayKey($array, $arrayKey)
    {
        $arrayWithRemovedKey = [];
        if(is_array($array)) {
            foreach($array as $key=>$arrayElement) {
                if($key !== $arrayKey && is_array($arrayElement)){
                    $arrayWithRemovedKey[$key] = $this->recursiveRemovalByArrayKey($arrayElement, $arrayKey);
                }
                elseif($key !== $arrayKey) {
                    $arrayWithRemovedKey[$key] = $arrayElement;
                }
            }
            return $arrayWithRemovedKey;
        }else{
            return $array;
        }

    }


    /*
     * Returns Filtered Array (Used for Associative Table-like Multi-dimensional arrays)
     *
     * @param array $source (Table Style multidimensional Array)
     * @param array $filter (one dimensional array)
     * @param string $columnName
     * @return array (Table Style multidimensional Array)
     */
    public function filterMultiColumnSourceBySingleColumnFilter($source, $filter, $columnName){
        $filteredSource = array();

        foreach($source as $sourceRow){
            foreach($filter as $filterRow) {
                $filterParameter = $sourceRow[$columnName];
                if ($filterParameter == $filterRow) {
                    array_push($filteredSource, $sourceRow);
                }
            }

        }

        return $filteredSource;

    }


    /**
     * Given an array of records, returns a similar array, but with records removed which have one of the $valuesToRemove as the value associated with $key.
     * The numeric indexes of the records will not be the same in the array returned, but relative order will be preserved.
     *
     * @param array $records -- an array of associative arrays, typically coming from a database query
     * @param string $key -- typically a database column whose values we want to filter on
     * @param array $valuesToRemove -- values in that column for which we want to remove the associated record
     * @return array
     */
    public function removeRecords($records, $key, $valuesToRemove)
    {
        // If the original array is empty, or if the records don't contain the key we're trying to filter on, return the original array untouched.
        if (empty($records) || !in_array($key, array_keys($records[0]))) {
            return $records;
        }

        $recordsToReturn = [];

        foreach ($records as $record) {
            if (!in_array($record[$key], $valuesToRemove)) {
                $recordsToReturn[] = $record;
            }
        }

        return $recordsToReturn;
    }

    /**
     * Universal method to sort multi-dimensional array
     *
     * @param array $dataArray
     * @param string $sortKey
     * @param string $sortBy
     * @return Array
     */
    public function sortMultiDimensionalArray($dataArray, $sortKey, $sortBy) {
        if(strtolower($sortBy) == 'asc'){
            $sortBy = SORT_ASC;
        }
        else {
            $sortBy = SORT_DESC;
        }
        $sortKeyArray = explode(',', $sortKey);
        if(!empty($dataArray)){
            foreach ($dataArray as $key => $row) {
                $sortDataBy[$key] = $row[$sortKeyArray[0]];
                if(isset($sortKeyArray[1])){
                    $sortDataBy[$key] = $row[$sortKeyArray[1]];
                }
            }
            // Add $dataArray as the last parameter, to sort by the common key
            array_multisort($sortDataBy, $sortBy, $dataArray);
        }
        return $dataArray;
    }

    /** Returns encrypted string.
     *
     * @param string $textToEncrypt
     * @param string $encryptionMethod
     * @param string $secretHash
     * @return mixed
     */
    public function encrypt($textToEncrypt, $encryptionMethod, $secretHash)
    {
        $encryptedMessage = @openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
        return $encryptedMessage;
    }

    /**
     * Serializes the object to associative array
     *
     * @param object $object
     * @return mixed
     */
    public function serializeObjectToArray($object)
    {
        $encoders = array(
            new JsonEncoder()
        );
        $normalizers = array(
            new GetSetMethodNormalizer()
        );

        // Serialize associative array of object to array
        $serializer = new Serializer($normalizers, $encoders);
        $responseArray = $serializer->serialize($object, 'json');
        $responseArray = json_decode($responseArray, true);

        return $responseArray;
    }

    /**
     * This method validates the password strength
     *
     * @param string $validateString
     * @return bool
     */
    public function validatePasswordStrength($validateString)
    {
        if (preg_match('/^(?=.*?[0-9])(?=.*?[#_?!@$%^&*-]).{6,20}$/', $validateString)) {
            return true;
        }
        return false;
    }

    /**
     * Sets error message or value in an array
     *
     * @param array $records
     * @param array $errorArray
     * @param array $entityFieldMap - this is to map th entity fields with the json attributes
     * @return array
     */
    public function setErrorMessageOrValueInArray($records, $errorArray, $entityFieldMap = [])
    {
        foreach ($errorArray as $errorKey => $errorValue) {
            if (array_key_exists($errorKey, $entityFieldMap)) {
                $errorArray[$entityFieldMap[$errorKey]] = $errorValue;
            }
        }

        $responseArray = [];
        foreach ($records as $key => $value) {
            if (array_key_exists($key, $errorArray)) {
                $responseArray[$key]['value'] = $value;
                $responseArray[$key]['message'] = $errorArray[$key];
            } else {
                $responseArray[$key] = $value;
            }
        }
        return $responseArray;
    }

    /**
     * Get all attributes for doctrine entity
     *
     * @param BaseEntity $doctrineEntity
     * @return array
     */
    public function getAllAttributesOfDoctrineEntity($doctrineEntity)
    {
        $entityName = get_class($doctrineEntity);
        $entity = (array)$doctrineEntity;
        $attributeArray = array_keys($entity);
        $entityAttributesArray = [];
        foreach($attributeArray as $attribute) {
            $entityAttributesArray[] = trim(str_replace($entityName, '', $attribute));
        }
        return $entityAttributesArray;
    }

    /**
     * Change camelCase string to underscore string (eg. collegeCode to college_code)
     *
     * @param string|null $camelCaseString
     * @return string
     */
    public function convertCamelCasedStringToUnderscoredString($camelCaseString = null)
    {
        $underscoreString = strtolower(preg_replace('/([A-Z])/', '_$1', lcfirst($camelCaseString)));
        return $underscoreString;
    }

    /**
     * Removes duplicate arrays based off of a key
     *
     * @param array $elements => [
     *                              0 => ['key' => 'value', 'key1' => 'value1'],
     *                              1 => ['key' => 'value1', 'key1' => 'value2'],
     *                              2 => ['key' => 'value', 'key1' => 'value1']
     *                          ]
     * @param String $key
     * @return array => [
     *                      0 => ['key' => 'value', 'key1' => 'value1'],
     *                      1 => ['key' => 'value1', 'key1' => 'value2']
     *                  ]
     */
    public function removeDuplicateElements($elements, $key)
    {
        $uniqueElements = array();
        foreach ($elements as $element) {
            $foundElement = false;
            foreach ($uniqueElements as $earlierElement) {
                if (isset($element[$key]) && isset($earlierElement[$key])) {
                    if ($element[$key] == $earlierElement[$key]) {
                        $foundElement = true;
                        break;
                    }
                }
            }
            if (!$foundElement) {
                $uniqueElements[] = $element;
            }
        }
        return $uniqueElements;
    }

    /**
     *
     * simple sort that will sort an array based off of another array.
     * puts all unsorted values as the appears at the end of the array
     *

     * example
     * $pile
     * [                                   *
     *   0 => [                            * $output
     *          column_name => name1,      * [
     *          extra => extra 1           *   0 => [
     *        ],                           *          column_name => name1,
     *   1 => [                            *          extra => extra 1
     *          column_name => leftover,   *        ],
     *          extra => extra 0           *   1 => [
     *        ],                           *          column_name => name2,
     *   2 => [                            *          extra => extra 2
     *          column_name => name2,      *        ],
     *          extra => extra 2           *   2 => [
     *        ]                            *          column_name => leftover,
     * ]                                   *          extra => extra 0
     *                                     *        ]
     * $sortKey                            * ]
     * [                                   *
     *   0 => name1,
     *   1 => name2
     * ]

     * @param Array $pile
     * @param Array $sortKey
     * @param String $columnName
     *
     * @return array
     */
    public function sortBasedOnSortKey($pile, $sortKey, $columnName)
    {
        $returnValue = [];
        $leftovers = [];
        foreach ($pile as $key => $value) {
            if (array_key_exists($columnName, $value)) {
                $compareValue = $value[$columnName];
                $location = array_search($compareValue, $sortKey);
                if ($location || $location === 0) {
                    $returnValue[$location] = $value;
                } else{
                    $leftovers[] = $value;
                }
            } else {
                $leftovers[] = $value;
            }
        }
        ksort($returnValue);
        return array_merge($returnValue, $leftovers);

    }

    /**
     * Will make change all strings in the array to lowercase (keys and values)
     * Works with arrays multiple layers deep
     *
     * @param $array
     * @return array
     */
    public function arrayStringToLowerNullSafeAll($array)
    {
        $returnArray = [];
        foreach ($array as $arrayKey => $arrayValue) {
            $newArrayKey = $arrayKey;
            if (is_string($arrayKey)) {
                $newArrayKey = strtolower($arrayKey);
            }

            if (is_array($arrayValue)) {
                $returnArray[$newArrayKey] = $this->arrayStringToLowerNullSafeAll($arrayValue);
            } else if (is_string($arrayValue)) {
                $returnArray[$newArrayKey] = strtolower($arrayValue);
            } else {
                $returnArray[$newArrayKey] = $arrayValue;
            }

        }
        return $returnArray;
    }


    /**
     * This will return everything in array1 that is NOT in array2,
     * null safe but case sensitive
     *
     * ** Will only work on arrays that are 1 value deep **
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function nullSafeEqualsArrayDiff($array1, $array2)
    {
        $returnArray = [];
        foreach ($array1 as $arrayKey => $arrayValue)
        {
            if (!in_array($arrayValue, $array2, true))   {
                $returnArray[] = $arrayValue;
            }
        }
        return $returnArray;
    }

}