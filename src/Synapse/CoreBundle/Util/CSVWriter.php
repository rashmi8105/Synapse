<?php
namespace Synapse\CoreBundle\Util;

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

/**
 * CSV writer utility class
 *
 * Simple wrapper for Box/Spout that simplifies memory effecient CSV handling throughout Synapse.
 *
 * Usage:
 * $file = new Synapse\CoreBundle\Util\CSVWriter('testset.csv');
 *
 * $file->addRow($data);
 *
 * $file->close();
 */
class CSVWriter
{
    private $writer;

    public function __construct($filePath)
    {
        $this->writer = WriterFactory::create(Type::CSV);
        $this->writer->setShouldAddBOM(false);
        $this->writer->openToFile($filePath);
    }

    public function __destruct()
    {
        $this->writer->close();
    }

    public function addRow($row)
    {
        // Excel can only properly read CSV files in ISO-8859-1
        $row = $this->iso8859Convert($row);
        $this->writer->addRow($row);
    }

    public function addRows($rows)
    {
        // Excel can only properly read CSV files in ISO-8859-1
        $rows = $this->iso8859Convert($rows);
        $this->writer->addRows($rows);
    }

    public function close()
    {
        $this->writer->close();
    }

    /**
     * Converts all UTF-8 strings in an array to ISO-8859-1
     *
     * @param  array $array - data to convert
     * @return array - converted data
     */
    private function iso8859Convert(array $array)
    {
        array_walk_recursive($array, function (&$item) {
            if (mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_decode($item);
            }
        });

        return $array;
    }

}
