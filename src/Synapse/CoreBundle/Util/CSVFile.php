<?php
namespace Synapse\CoreBundle\Util;

use SPLFileObject;

/**
 * CSV file utility class
 *
 * Extends SPLFileObject to allow iterating over a CSV file, returning each row
 * as a named array based on the column titles of the header row (optional,
 * enabled by default, otherwise just returns each row as an indexed array).
 *
 * Usage:
 * $file = new Synapse\CoreBundle\Util\CSVFile('testset.csv');
 *
 * foreach($file as $idx => $row) {
 *     print_r($row);
 * }
 */
class CSVFile extends SPLFileObject
{
    protected $first_row = true;
    protected $columns;
    protected $has_header = true;
    protected $enableCaseConversion = false;

    public function __construct($filename, $has_header = true, $delimiter = ',', $enclosure = '"', $escape = '\\', $enableCaseConversion = false)
    {
        parent::__construct($filename);
        $this->setFlags(SPLFileObject::READ_CSV);
        $this->setCsvControl($delimiter, $enclosure, $escape);

        $this->has_header = $has_header;
        $this->enableCaseConversion = $enableCaseConversion;
    }

    public function current()
    {
        // Set the column names if first row
        if ($this->first_row && $this->has_header) {
            $this->first_row = false;
            $this->columns = parent::current();

            if( $this->enableCaseConversion ) {

                $cols = [];
                foreach($this->columns as $col) {
                    $cols[] = strtolower( $col );
                }

                $this->columns = $cols;
            }

            $this->next();
        }

        $row_data = parent::current();

        // Stop at end of file
        // if (!$this->valid()) {
        //     return;
        // }

        //var_dump($this->columns);
        //var_dump($row_data);
        return $this->has_header ? array_combine($this->columns, $row_data) : $row_data;
    }

}
