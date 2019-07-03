<?php

namespace Synapse\UploadBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\CoreBundle\Util\CSVFile;
use Resque;
use Resque_Job_Status;
use PHPExcel_IOFactory;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\CSVReader;


class FTPWatchCommand extends ContainerAwareCommand
{

    private $output;


    protected function configure()
    {
        $this
            ->setName('ftp:watch')
            ->setDescription('Check for new FTP uploads')
            ->addArgument('directory', InputArgument::REQUIRED, 'Root directory to watch?')
            ->addArgument('waitTimeout', InputArgument::REQUIRED, 'How long to wait for types?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;
        $this->uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->root = $input->getArgument('directory');
        $this->waitTimeout = $input->getArgument('waitTimeout');

        $directory = new \RecursiveDirectoryIterator($this->root);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^' . preg_quote($this->root, '/') . '\/\d+\/.+\/incoming\/.+\.(csv|xls|xlsx)$/i', \RecursiveRegexIterator::GET_MATCH);
        $jobs = [];
        $files = [];

        $typeOrder = [
            'staff',
            'student',
            'group',
            'group-staff',
            'group-student',
            'course',
            'course-faculty',
            'course-student',
            'academic-updates',
            'static-list',
            'multi-group-student'
        ];

        foreach($regex as $name) {
            $name = $name[0];

            preg_match('/(\d+)\/(.+)\/incoming\/(.+\.(csv|xlsx|xls))$/', $name, $matches);

            $orgId = $matches[1];
            $type = $matches[2];
            $filename = $matches[3];

            $pathParts = pathinfo($name);

            $files[$type][] = [
                'name' => $name,
                'orgId' => $orgId,
                'filename' => $filename,
                'pathParts' => $pathParts,
                'job' => false
            ];
        }

        foreach ($typeOrder as $type) {
            if (isset($files[$type])) {
                $jobs = [];

                foreach ($files[$type] as $file) {
                    $job = $this->createJob($type, $file, $pathParts);
                    
                    if ($job) {
                        $jobs[] = $job;
                    } else {
                        continue;
                    }

                    $this->output->writeln("<info>" . date('Y-m-d H:i:s') . " - Org: $orgId - File: {$file['name']} - Successfully queued</info>");
                }

                $timeout = time() + ($this->waitTimeout * 60);

                while (count($jobs) > 0) {
                    sleep(15);

                    foreach ($jobs as $key => $job) {
                        $status = $job['resqueJob']->get();

                        if ($status === Resque_Job_Status::STATUS_COMPLETE) {
                            rename($job['filePath'], $this->root . '/' . $job['orgId'] . '/' . $job['type'] . '/completed/' . date('Y-m-d') . '-' . $job['filename']);
                            $this->output->writeln("<info>" . date('Y-m-d H:i:s') . " - Org: {$job['orgId']} - File: {$job['filename']} - Completed</info>");
                            unset($jobs[$key]);
                        } elseif ($status === Resque_Job_Status::STATUS_FAILED) {
                            rename($job['filePath'], $this->root . '/' . $job['orgId'] . '/' . $job['type'] . '/failed/' . date('Y-m-d') . '-' . $job['filename']);
                            $this->output->writeln("<error>" . date('Y-m-d H:i:s') . " - Org: {$job['orgId']} - File: {$job['filename']} - Failed</error>");
                            unset($jobs[$key]);
                        }
                    }

                    if (time() > $timeout) {
                        $this->output->writeln("<error>" . date('Y-m-d H:i:s') . " - Timed out waiting for $type jobs</error>");
                        break;
                    }
                }
            }
        }
    }

    private function createJob($type, $file, $pathParts)
    {
        extract($file);

        $commandClass = false;

        switch ($type) {
            case 'student':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessStudentUpload';
                $typeCode = 'S';
                $uploadFolder = 'student_uploads';
                break;
            case 'staff':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessFacultyUpload';
                $typeCode = 'F';
                $uploadFolder = 'faculty_uploads';
                break;
            case 'group-student':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessGroupStudentBulkUpload';
                $typeCode = 'GS';
                $uploadFolder = 'group_uploads';
                break;
            case 'group-staff':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessGroupFacultyBulkUpload';
                $typeCode = 'GF';
                $uploadFolder = 'group_uploads';
                break;
            case 'group':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessSubGroupUpload';
                $typeCode = 'G';
                $uploadFolder = 'group_uploads';
                break;
            case 'course':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessCourseUpload';
                $typeCode = 'C';
                $uploadFolder = 'course_uploads';
                break;
            case 'course-student':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessCourseStudentUpload';
                $typeCode = 'T';
                $uploadFolder = 'course_student_uploads';
                break;
            case 'course-faculty':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessCourseFacultyUpload';
                $typeCode = 'P';
                $uploadFolder = 'course_faculty_uploads';
                break;
            case 'academic-updates':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessAcademicUpdateUpload';
                $typeCode = 'A';
                $uploadFolder = 'academic_update_uploads';
                break;
            case 'static-list':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessStaticListUpload';
                $typeCode = 'SL';
                $uploadFolder = 'staticlist_uploads';
                break;
            case 'multi-group-student':
                $commandClass = 'Synapse\UploadBundle\Job\ProcessManageGroupStudentBulkUpload';
                $typeCode = 'GS';
                $uploadFolder = 'group_uploads';
                break;
            default:
                $this->output->writeln("<error>" . date('Y-m-d H:i:s') . " - Org: $orgId - File: $name - Uknown upload type</error>");
                continue;
                break;
        }

        if ($commandClass) {
            if ($pathParts['extension'] == 'xls' || $pathParts['extension'] == 'xlsx') {
                $this->convertXLStoCSV($name, "{$pathParts['dirname']}/{$pathParts['filename']}.csv");
                unlink($name);
                $filename = "{$pathParts['filename']}.csv";
                $name = "{$pathParts['dirname']}/{$pathParts['filename']}.csv";
            }

            $fileHash = md5_file($name);

            $filename = 'ftp-'. date('Y-m-d') . '-' . $orgId . '-' . $filename;

            file_put_contents("data://$uploadFolder/$filename", file_get_contents($name));

            $reader = new CSVReader($name);
            $rowsTotal = $reader->getRowCount();
            $columns = $reader->getColumns();

            // checks if the new group student csv format is in the old folder;
            // the new version should not have the column header 'groupid'
            $lowercaseColumns = array_map('strtolower', $columns);

            if ($type == 'group-student') {
                if (!(in_array('groupid', $lowercaseColumns))) {
                    $commandClass = 'Synapse\UploadBundle\Job\ProcessManageGroupStudentBulkUpload';
                }
            }

            unset($reader);

            $resque = $this->getContainer()->get('bcc_resque.resque');

            $jobNumber = uniqid();

            $this->em->getConnection()->refresh();

            $uploadFile = $this->uploadFileLogService->createUploadService($orgId, $filename, $columns, $rowsTotal, $jobNumber, null, $typeCode);

            $this->uploadFileLogService->updateFileHash($uploadFile, $fileHash);

            if ($rowsTotal == 0) {
                $this->uploadFileLogService->updateJobStatus($uploadFile->getJobNumber(), 'F');
                $this->output->writeln("<error>" . date('Y-m-d H:i:s') . " - Org: $orgId - File: $name - Missing required data</error>");
                return false;
            }

            $this->uploadFileLogService->updateJobErrorPath($uploadFile);

            $job = new $commandClass();

            $job->args = array(
                'organization' => $orgId,
                'orgId' => $orgId,
                'userId' => 1,
                'key' => $filename,
                'jobNumber' => $jobNumber,
                'uploadId' => $uploadFile->getId()
            );

            return [
                'resqueJob' => $resque->enqueue($job, true),
                'filePath' => $name,
                'orgId' => $orgId,
                'type' => $type,
                'filename' => $filename
            ];

        }

    }

    private function convertXLStoCSV($infile, $outfile)
    {
        $fileType = PHPExcel_IOFactory::identify($infile);
        $objReader = PHPExcel_IOFactory::createReader($fileType);

        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($infile);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->save($outfile);
    }

}