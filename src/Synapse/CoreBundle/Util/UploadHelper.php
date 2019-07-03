<?php

namespace Synapse\CoreBundle\Util;

use SplFileObject;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

/**
* Provides helper methods for upload functionalities
*/
class UploadHelper
{
    const UPLOAD_ERROR = 'Upload Errors';

    /**
     * Generate Errors CSV file
     * @param array $errors
     * @param CSVReader $fileReader
     * @param string $url
     * @return $errorFilename
     */
    public function generateErrorCSV($errors, $fileReader, $url)
    {
        $list = [];

        foreach ($fileReader as $idx => $row) {
            if (isset($errors[$idx])) {
                $rowErrors = '';
                $rowErrors = $this->createRow($idx,$errors, $rowErrors);
                $row[self::UPLOAD_ERROR] = $rowErrors;
                $list[] = $row;
            }
        }
        
        $errorCSVFile = new SplFileObject($url, 'w');

        $csvHeaders = $fileReader->getColumns();
        $csvHeaders[self::UPLOAD_ERROR] = self::UPLOAD_ERROR;
        $errorCSVFile->fputcsv($csvHeaders);

        foreach ($list as $fields) {
            $errorCSVFile->fputcsv($fields);
        }
        $errorFilename = strstr($url,'errors');
        return $errorFilename;
    }

    /**
     * Function to generate error rows
     * @param int $idx
     * @param array $errors
     * @param string $rowErrors
     * @return string
     */
    public function createRow($idx,$errors, $rowErrors)
    {
        foreach ($errors[$idx] as $id => $column) {
            if ($id) {
                $rowErrors .= "\r";
            }
            if (count($column['errors']) > 0) {
                $rowErrors .= "{$column['name']} - ";
                $rowErrors .= implode("{$column['name']} - ", $column['errors'])."\n";
            } else {
                $rowErrors .= "{$column['name']} - {$column['errors'][0]}"."\n";
            }
        }
        return $rowErrors;
    }


    /*
     * @codeCoverageIgnore
     */
    /**
     * Returns encoded string.
     *
     * @param string $key
     * @param string $data
     * @return mixed
     */
    public function hmacsha1($key, $data)
    {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
        
        return bin2hex($hmac);
    }

    /**
     * Encodes string to base64 encoding.
     *
     * @param $str
     * @return mixed
     */
    public function hex2b64($str)
    {
        $raw = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $raw .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($raw);
    }

    /**
     * Converts an XLS(X) file to a CSV file
     *
     * @param string $infile - input file path
     * @param string $outfile - output file path
     * @throws \PHPExcel_Reader_Exception
     */
    public function convertXLStoCSV($infile, $outfile)
    {
        $fileType = \PHPExcel_IOFactory::identify($infile);
        $objReader = \PHPExcel_IOFactory::createReader($fileType);
        
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($infile);
        
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }
       
}