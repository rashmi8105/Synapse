<?php

namespace tests\unit\Synapse\CoreBundle\Service\Util;

use Codeception\TestCase\Test;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\Util\CSVReader;


class CSVUtilityServiceTest extends Test
{
    use \Codeception\Specify;

    public function testGenerateCSV()
    {

        $this->specify("Test Create academic Update report csv", function ($filePath, $fileName, $records, $csvHeaders, $errorMessage = null) {

            try {
                $CSVUtilityService = new CSVUtilityService();
                $result = $CSVUtilityService->generateCSV($filePath, $fileName, $records, $csvHeaders);
                $this->assertEquals($result, $fileName);
                $fileExists = file_exists("/tmp/$fileName");
                $this->assertTrue($fileExists);
                unlink("/tmp/$fileName");
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $this->assertEquals($message, $errorMessage);
            }

        }, [
            'examples' => [
                [
                    "/tmp/",
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "col_one" => "test",
                            "col_two" => "test1"
                        ]
                    ],
                    // headers of the file
                    [
                        'col_one' => 'col1',
                        'col_two' => 'col2'
                    ]
                ],
                // with invalid file path, IN any case this will  not be shown up to the users, any exception will be stored in the failure_description field in the org_person_job_status
                [
                    "/tmp1/",
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "col_one" => "test",
                            "col_two" => "test1"
                        ]
                    ],
                    // headers of the file
                    [
                        'col_one' => 'col1',
                        'col_two' => 'col2'
                    ],
                    $errorMessage = "copy(/tmp1/orig.csv): failed to open stream: No such file or directory"
                ]
            ]

        ]);
    }

    // This methods tests createCSVFileInTempFolder,writeToFile,getRowsToWrite and copyFileToDirectory method
    public function testCsvFileOperation()
    {

        $this->specify("Test all Csv file operation", function ($tempFileName, $fileName, $rowsToWrite, $headers, $expectedResult) {

            $tempFilePath = "/tmp/$tempFileName";
            $CSVUtilityService = new CSVUtilityService();
            $csvWriter = $CSVUtilityService->createCSVFileInTempFolder($tempFileName);

            $this->assertInstanceOf("Synapse\CoreBundle\Util\CSVWriter", $csvWriter); // asserting that instance of Csv writer

            $isFileCreated = file_exists($tempFilePath);
            $this->assertTrue($isFileCreated); //  checking id the temporary file is created or not.

            if ($headers) {
                $hasHeaders = true;
            } else {
                $hasHeaders = false;
            }

            //Writing to the file
            if ($headers) {
                $CSVUtilityService->writeToFile($csvWriter, $headers, true);
                $rowsToWrite = $CSVUtilityService->getRowsToWrite($rowsToWrite, $headers);
            }

            $CSVUtilityService->writeToFile($csvWriter, $rowsToWrite);

            // Reading the file
            $fileReader = new CSVReader($tempFilePath, $hasHeaders, true);
            foreach ($fileReader as $idx => $row) {
                $this->assertEquals($row, $expectedResult); // Ensuring thar the data written to the file is good.
            }

            $CSVUtilityService->copyFileToDirectory($tempFileName, "/tmp/", $fileName); // moving the file from one location to another

            $isFileCreated = file_exists("/tmp/$fileName"); // checking id the file is copied or not
            $this->assertTrue($isFileCreated);

            $isTempFilePresent = file_exists($tempFilePath);
            $this->assertFalse($isTempFilePresent); // checking id the old file exists or not
            unlink("/tmp/$fileName"); // finally after all test are done, removing the test file.

        }, [
            'examples' => [
                // added tests  with headers
                [
                    "temp.csv", // temporary file name, the file that is created in the tmp folder
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "col_one" => "test",
                            "col_two" => "test1"
                        ]
                    ],
                    // headers of the file
                    [
                        'col_one' => 'col1',
                        'col_two' => 'col2'
                    ],
                    // This would be the expected data when we read from the file
                    [
                        'col1' => 'test',
                        'col2' => 'test1'
                    ]
                ],
                // added tests  without headers
                [
                    "temp.csv", // temporary file name, the file that is created in the tmp folder
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "test",
                            "test1"
                        ]
                    ],
                    // headers of the file
                    null,
                    // This would be the expected data when we read from the file
                    [
                        "test",
                        "test1"
                    ]
                ],

                //Invalid rows, if the rows do not match the headers, we will not be writing that tote csv
                [
                    "temp.csv", // temporary file name, the file that is created in the tmp folder
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "col_one" => "test",
                            "col_twooo" => "test1"
                        ]
                    ],
                    // headers of the file
                    [
                        'col_one' => 'col1',
                        'col_two' => 'col2'
                    ],
                    // This would be the expected data when we read from the file
                    [
                        'col1' => 'test',
                        'col2' => ''
                    ]
                ],

                //Invalid rows, all rows are invalid
                [
                    "temp.csv", // temporary file name, the file that is created in the tmp folder
                    "orig.csv", // this would be the original filename  once the temporary file is created ,it is moved to the download directory with this name

                    //data to be written to the file, in the format we get from db
                    [
                        [
                            "test" => "test",
                            "rest" => "test1"
                        ]
                    ],
                    // headers of the file
                    [
                        'col_one' => 'col1',
                        'col_two' => 'col2'
                    ],
                    // This would be the expected data when we read from the file
                    [
                        'col1' => '',
                        'col2' => ''
                    ]
                ]

            ]

        ]);

    }
}
