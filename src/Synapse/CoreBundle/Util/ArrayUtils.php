<?php
namespace Synapse\CoreBundle\Util;

class ArrayUtils
{

    public static function isAttributeNumeric ($object, $attr) {
        
        if ( !isset( $object[$attr]) ) {
            
            return false;
        }
        
        return ( ($object[$attr] == 0) || ($object[$attr] > 0) || ($object[$attr] < 0) );
    }
    
    public static function isAttributeBoolean ($object, $attr) {
        
        if ( !isset( $object[$attr]) ) {
            
            return false;
        }
        
        return ( ($object[$attr] === true) || ($object[$attr] === false) );
    }
    
    public static function isAttributeEmpty ($object, $attr) {
        
        return !isset( $object[$attr]) || empty( $object[$attr]);
    }
    
    public static function appendArray (&$arr, $new) {
    
    	foreach( $new as $item) {
    
    		$arr[] = $item;
    	}
    
    	return $arr;
    }
    
    /**
     * Universal method to sort multi dimentional array
     * @param multidimentional array $data
     * @param array key to be sort $sort_key
     * @param sorting type asc/desc $sortBy
     * @return sorted array
     */
    public static function sortData($dataArray, $sort_key, $sortBy) {
    	if(strtolower($sortBy) == 'asc'){
    		$sortBy = SORT_ASC;
    	}
    	else {
    		$sortBy = SORT_DESC;
    	}
    	$sKey = explode(',', $sort_key);    	    	
    	if(!empty($dataArray)){
    		foreach ($dataArray as $key => $row) {
    			$sortDataBy[$key] = $row[$sKey[0]];
    			if(isset($sKey[1])){
    			     $sortDataBy[$key] = $row[$sKey[1]];
    			}    			
    		}
    		// Add $dataArray as the last parameter, to sort by the common key
    		array_multisort($sortDataBy, $sortBy, $dataArray);
    	}
    	return $dataArray;
    }
    
    
}