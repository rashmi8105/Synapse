<?php

namespace Synapse\CoreBundle\Logger;

/**
* Monolog processor for removing sensitive data from logs
*/
class OTRProcessor
{
    private $searchStrings = [
        '/(?<=password=)\w*(?=\b)/i',
        '/(?<="password":")\w*(?=")/i'
    ];

    public function __invoke(array $record)
    {

        array_walk_recursive($record, [$this, 'removeSensitiveData']);

        return $record;

    }

    private function removeSensitiveData(&$item, $key)
    {
        if (is_string($item)) {
            foreach ($this->searchStrings as $searchString) {
                $item = preg_replace($searchString, '***REDACTED***', $item);
            }
        }
    }

}