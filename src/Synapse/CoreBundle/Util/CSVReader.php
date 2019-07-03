<?php
namespace Synapse\CoreBundle\Util;

use Iterator;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

/**
 * CSV reader utility class
 *
 * Simple wrapper for Box/Spout that simplifies memory effecient CSV handling throughout Synapse.
 *
 * Usage:
 * $file = new Synapse\CoreBundle\Util\CSVReader('testset.csv');
 *
 * foreach($file as $idx => $row) {
 *     print_r($row);
 * }
 */
class CSVReader implements Iterator
{
    private $tempFileName;
    private $columns;
    private $hasHeader;
    private $enableCaseConversion;
    private $reader;
    private $rows;

    public function __construct($filePath, $hasHeader = true, $enableCaseConversion = true)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found");
        }

        // Spout and Gaufrette don't play well together. We need a temporary local file.
        $this->tempFileName = uniqid("/tmp/");
        file_put_contents($this->tempFileName, file_get_contents($filePath));

        $this->reader = ReaderFactory::create(Type::CSV);
        $this->reader->open($this->tempFileName);
        $this->rows = $this->reader->getSheetIterator()->current()->getRowIterator();

        $this->hasHeader = $hasHeader;
        $this->enableCaseConversion = $enableCaseConversion;

        if ($this->hasHeader) {
            $this->rows->rewind();
            $this->columns = $this->rows->current();

            if ($enableCaseConversion) {
                $this->columns = array_map('strtolower', $this->columns);
            }

            $this->rows->next();
        }
    }

    public function __destruct()
    {
        $this->reader->close();
        // clean up temp file
        unlink($this->tempFileName);
    }

    /**
     * Returns the current row (according to the iterator pointer) of a CSV file where every entry has been trimmed and UTF-8 converted
     *
     * @return array
     */
    public function current()
    {
        if ($this->hasHeader && $this->key() === 1) {
            $this->next();
        }

        $rows = $this->rows->current();

        if ($this->hasHeader) {
            $rows = array_combine($this->columns, $rows);
        }

        // Excel only saves CSV's as ISO-8859-1. We need to fix that.
        $rows = $this->utf8Convert($rows);

        foreach ($rows as $idx => $data) {
            $rows[$idx] = trim($data);
        }

        return $rows;
    }

    public function rewind()
    {
        $this->rows->rewind();
    }

    public function next()
    {
        $this->rows->next();
    }

    public function key()
    {
        return $this->rows->key();
    }

    public function valid()
    {
        return $this->rows->valid();
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getRowCount()
    {
        $rowsTotal = 0;
        $firstRow = true;
        $originalRow = $this->key();
        $this->rewind();

        foreach ($this->rows as $row) {
            if ($firstRow && $this->hasHeader) {
                $firstRow = false;
                continue;
            }
            $rowsTotal++;
        }

        $this->rewind();

        while ($this->key() < $originalRow) {
            $this->next();
        }

        return $rowsTotal;
    }

    /**
     * Converts all non UTF-8 strings in an array to UTF-8
     *
     * @param  array $array - data to convert
     * @return array - converted data
     */
    private function utf8Convert($array)
    {
        array_walk_recursive($array, function (&$item) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

}
