<?php
namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVWriter;


/**
 * @DI\Service("csv_utility_service")
 */
class CSVUtilityService
{

    const SERVICE_KEY = 'csv_utility_service';

    /**
     * Uses CSVWriter to generate a CSV with the given name containing the given records and places it at the given S3 path.
     * If $csvHeaders is passed in, only those columns are included.
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $records
     * @param array|null $csvHeaders -- associative array with keys matching the ones in $records and values being the expected column headers in the CSV.
     * @param array|null $preliminaryRows -- rows to put into the CSV above the tabular data (e.g., a title or search attributes)
     * @return string
     */
    public function generateCSV($filePath, $fileName, $records, $csvHeaders = null, $preliminaryRows = null)
    {
        $tempFileName = uniqid($fileName);
        $csvWriter = $this->createCSVFileInTempFolder($tempFileName);
        if (!empty($preliminaryRows)) {
            $this->writeToFile($csvWriter, $preliminaryRows);
        }
        if (empty($csvHeaders)) {
            $this->writeToFile($csvWriter, $records);
        } else {
            $this->writeToFile($csvWriter, $csvHeaders, true);
            $rowsToWrite = $this->getRowsToWrite($records, $csvHeaders);
            $this->writeToFile($csvWriter, $rowsToWrite);
        }

        $this->copyFileToDirectory($tempFileName, $filePath, $fileName);
        return $fileName;
    }


    /**
     * Create Temporary file
     *
     * @param string $tempFileName
     * @return CSVWriter
     */
    public function createCSVFileInTempFolder($tempFileName)
    {

        $tempFileName = "/tmp/$tempFileName";
        $CSVWriter = new CSVWriter($tempFileName);
        return $CSVWriter;
    }

    /**
     * Write to file
     *
     * @param CSVWriter $CSVWriter
     * @param bool $isHeader
     * @param array $rowsToWrite
     */
    public function writeToFile($CSVWriter, $rowsToWrite, $isHeader = false)
    {
        if ($isHeader) {
            $CSVWriter->addRow($rowsToWrite);
        } else {
            $CSVWriter->addRows($rowsToWrite);
        }
    }

    /**
     * Create the rows to write based on the csv headers
     *
     * @param array $records
     * @param array $csvHeaders
     * @return array
     */
    public function getRowsToWrite($records, $csvHeaders)
    {

        $rowsToWrite = [];
        foreach ($records as $record) {
            $rowToWrite = [];
            foreach ($csvHeaders as $key => $header) {
                if (array_key_exists($key, $record)) {
                    $rowToWrite[] = $record[$key];
                } else {
                    $rowToWrite[] = '';
                }
            }
            $rowsToWrite[] = $rowToWrite;
        }
        return $rowsToWrite;
    }

    /**
     * After the temporary file is done, move file to the downloadable directory
     *
     * @param string $tempFileName
     * @param string $filePath
     * @param string $fileName
     */
    public function copyFileToDirectory($tempFileName, $filePath, $fileName)
    {
        $tempFileName = "/tmp/$tempFileName";
        copy($tempFileName, $filePath . $fileName);
        unlink($tempFileName);
    }

}