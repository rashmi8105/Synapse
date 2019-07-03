<?php
namespace Synapse\CoreBundle\Util;

class SqlUtils
{
    private static $simplyReturn;
    private static $quoteValue;
    
    static function init () {
        
        SqlUtils::$simplyReturn = function ($value) {
        
        	return $value;
        };
        
        SqlUtils::$quoteValue = function ($value, $quoteWith='"') {
    
    	   return $quoteWith . $value . $quoteWith;
        };
    }
    
    public static function toDateValue ($value) {
        
        return ' STR_TO_DATE("' . $value . '", "%Y-%m-%d") ';
    }
    
    public static function makeFilterCondition ($values, $quote=false, $notCondition = '') {
        
        $quoteFn = SqlUtils::$simplyReturn;
        
        $separator = ', ';

        if ( $quote ) {
            
            $quoteFn = SqlUtils::$quoteValue;
            $separator = '", "';
        }
        
        if ( empty( $values) ) {
            
            return '';
        }
        
        if ( count( $values) > 1 ) {
            
            $condition = ($notCondition == 'not') ? ' not in ( ' . $quoteFn( implode( $separator, $values)) . ') ' : ' in ( ' . $quoteFn( implode( $separator, $values)) . ') ';
            return $condition;
            
        } else {
            
            $condition = ($notCondition == 'not') ? ' != ' . $quoteFn( $values[0]) : ' = ' . $quoteFn( $values[0]);
            return $condition;
        }
    }
}

SqlUtils::init();
