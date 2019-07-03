<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class ApiauthHelper extends \Codeception\Module
{
    private $output;

    public function _beforeSuite()
    {
        //Save Database
        if(getenv('environment') == 'test') {
            $this->output = shell_exec('mysqldump -uroot -psynapse --routines synapse > tests/_data/tempDBHolder.sql');
            if($this->output == '') {
                codecept_debug('Saving test data for non-Authorization Tests');
            }
            else{
                codecept_debug('Saving Failed: '. $this->output);
                //Throw Error?
            }

        }

        // Set up before test suite
        $this->output = shell_exec('./runauthtests.sh --reload');
        codecept_debug($this->output);

        $this->output = shell_exec('redis-cli flushall');
        codecept_debug('redis-flush: ' . $this->output);

    }

    public function _afterSuite()
    {


        if(getenv('environment') == 'test') {
            $this->output = shell_exec('mysql -uroot -psynapse -e "DROP DATABASE synapse;"');
            if ($this->output == '') {
                codecept_debug('Database Dropped');
            } else {
                codecept_debug('Database Drop Failed: ' . $this->output);
                //Throw Error?
            }

            $this->output = shell_exec('mysql -uroot -psynapse -e "CREATE DATABASE synapse;"');
            if ($this->output == '') {
                codecept_debug('Database ReCreated');
            } else {
                codecept_debug('Database Create Failed: ' . $this->output);
                //Throw Error?
            }

            $this->output = shell_exec('mysql -uroot -psynapse -f -D synapse < tests/_data/tempDBHolder.sql > /dev/null 2>&1');

            if ($this->output == '') {
                codecept_debug('Reloaded test data for non-Authorization Tests');
            } else {
                codecept_debug('Reloading Failed: ' . $this->output);
                //Throw Error?
            }

            $this->output = shell_exec('redis-cli flushall');
            codecept_debug('redis-flush: ' . $this->output);

            $this->output = shell_exec('rm tests/_data/tempDBHolder.sql');
            codecept_debug($this->output);

        }
    }
}
