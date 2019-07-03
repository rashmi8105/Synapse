<?php
namespace Synapse\CoreBundle\Util;

class CommandUtils
{

    const GENERATE_STUDENT_REPORT_COMMAND_KEY = "gen-stdnt-reprt-cmd";
    const EMAIL_STUDENT_REPORT_COMMAND_KEY = "email-stdnt-reprt-cmd";


    /**
     * Using the passed in $commandKey, looks in the /var/run/lock directory to see if another instances of this command is
     * already running on this server.  If there is, this function returns true.  If another instance is not running
     * then it creates a lock file in /var/run/lock (to let others know the current instance is running) and then returns
     * false.
     *
     * @param $commandKey
     * @return bool
     */
    public static function commandAlreadyRunning($commandKey){

        $commandRunning = false;

        $fd = fopen("/var/run/lock/".$commandKey.".pid","c+");
        $pid = fgets($fd);

        if (isset($pid) and $pid > 0){
            if (posix_getpgid($pid) !== false){
                $commandRunning = true;
            }
        }

        if (!$commandRunning){
            ftruncate($fd,0);
            rewind($fd);
            fputs($fd,getmypid());
        }

        fclose($fd);

        return $commandRunning;
    }

    /**
     * Using the passed in $commandKey, removes any pid file that matches the $commandKey and is in the /var/run/lock directory
     *
     * @param $commandKey
     */
    public static function commandFinishedRunning($commandKey){

        unlink("/var/run/lock/".$commandKey.".pid");

    }

}

