#!/bin/bash
cd "$(dirname "$0")"
colrGn="\033[0;32m"
colrNo="\033[0m"
colrFl="\033[37;41m"
colrFlOff="\033[39;49m"

if [[ ! -z $3 || $2 =~ ^-.* ]]; then

	echo
	echo "You can only use one options at a time.  Try --help for details."
	echo

elif [ "$1" == "--reload" ]; then

	echo
	echo "Dropping and Creating the synapse database..."
	mysql -u root -psynapse -e "drop database synapse"
	mysql -u root -psynapse -e "create database synapse"

	echo "Loading database schema and data from tests/_data/auth-test-single-file.sql..."
	mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql
	echo "NOT runing test... Done!"
	echo

elif [ "$1" == "--gotnewcode" ]; then

	echo 
	echo "Running: php composer.phar install..."
	php composer.phar install
	echo
	echo "Running: redis-cli flushall..."
	redis-cli flushall
	echo
	echo "Running: rm -r app/cache/test/"
	rm -r app/cache/test/
	echo
	echo "Done!"

elif [ "$1" == "--migrate" ]; then

	echo 
	echo -e $colrGn"REMINDER: "$colrNo"Have you already run the --reload command so you have a clean database?"
	echo "Runing Doctrine Migration scripts against localhost database for your current synapse-backend branch..."
	echo
	app/console doctrine:migration:migrate
	echo
	echo "Done!"

elif [ "$1" == "--dumpcomp" ]; then
	
	echo -n "Dumping and Prepping dumped database files for comparison..."
	mysqldump -uroot -psynapse --routines --events --single-transaction synapse > ./comp_tmp.sql
	sed -e "s/),(/),\n(/g" ./comp_tmp.sql | sed -e "s/VALUES (/VALUES\n(/g" > ./comp_localhost_db.sql
	sed -e "s/),(/),\n(/g" tests/_data/auth-test-single-file.sql | sed -e "s/VALUES (/VALUES\n(/g" > ./comp_tmp.sql
	mv ./comp_tmp.sql tests/_data/auth-test-single-file.sql
	echo " Done."
	echo
	echo "1) Open a terminal window on your local machine."
	echo "2) cd to <YourRepositoryDirectory>/synapse-backend"
	echo "3) Run (assuming you have diffmerge installed):"
	echo
	echo "diffmerge tests/_data/auth-test-single-file.sql comp_localhost_db.sql"
	echo

elif [ "$1" == "--move" ]; then

	echo
	echo "Moving database dump file to auth-test-single-file.sql (assumes --dumpcomp has already been run)..."
	mv ./comp_localhost_db.sql tests/_data/auth-test-single-file.sql
	echo "Done!"
	echo

elif [[ ! -z $1 && ! $1 =~ --x.* ]]; then

	echo
	echo "./runauthtests.sh [option] [tests/apiauth/Authorization/<TestFileName>|all]"
	echo
	echo "If one of the --x options is specified, runs Codeception against the test file specified by <TestFileName>.  If"
	echo -e "the word "$colrGn"all"$colrNo" is provided instead of a specific test file name, then runs all of the test in the"
	echo "tests/apiauth/Authorization directory (reloading the localhost database with data between each file)."
	echo
	echo "Options: (You may only use ONE option at a time)"
	echo
	echo "    --help       Display this help information"
	echo "    --helplong   Display this help information plus additional examples and details"
	echo "    --gotnewcode Used to reset your development/test environment after you download new code from git"
	echo "    --reload     Reload the localhost database with the contents found in tests/_data/auth-test-single-file.sql"
	echo "    --migrate    Runs the Doctrine Migration command for your current branch against the locahost DB."
	echo "                 MAKE SURE you have just run --reload before doing this. (There are rare cases when you might not"
	echo "                 want to run --reload so we don't automatically do it for you)"
	echo "    --dumpcomp   Dump the contents of the 'synapse' database on localhost, make the dumped file and" 
	echo "                 tests/_data/auth-test-single-file.sql ready for comparison and display the compare command"
	echo "    --move       After you confirm via diffmerge that the data is correct, moves the file dumped during --compare"
	echo "                 to test/_data/auth-test-single-file.sql"
	echo "    --xsd        Execute the specified test file with --steps and -d options"
	echo "    --xs         Execute the specified test file with --steps option"
	echo "    --x          Execute the specified test file with no Codeception options"
	echo "    --xnightly   Execute the specified test file with the nightly test Codeception options of:"
	echo "                 --coverage --coverage-xml --coverage-html --xml"
	echo "    [nothing]    If nothing is specified on the command line, runs all of the test found in the"
	echo "                 tests/apiauth/Authorization directory with the Codeception options --steps and -d.  Reloads the"
	echo "                 localhost database between each test file with the contents found in"
	echo "                 tests/_data/auth-test-single-file.sql"
	echo
	if [[ $1 = --helplong ]]; then
		echo "Building Tests and Data:"
		echo "    When adding data for tests to your local database, one would usually use the following sequence:"
		echo 
		echo "    1) ./runauthtests.sh --reload"
		echo "    2) Add your database changes via your local UI"
		echo "    3) ./runauthtests.sh --dumpcomp"
		echo "    4) Follow the directions displayed when -dumpcomp runs to see a visual comparison between the"
		echo "       data you added and how the database looked prior to your changes"
		echo "    5) If the changes are not what you expect, go back to step 1. Warning: Running step 1 will wipe out any"
		echo "       data changes you have made since your last --move."
		echo "    6) If you are good with your changes..."
		echo "    7) ./runauthtests.sh --move"
		echo "    8) The --move will move your version of the database into your local copy of auth-test-single-file.sql."
		echo "    9) You can now run you test using one of the --x options with no fear of losing your new data changes."
		echo 
		echo "    Repeat the above steps until all your data and tests are as you want them.  Then (after doing a pull)"
		echo "    commit and push your tests and also the auth-test-single-file.sql"
		echo
		echo "Other Examples:"
		echo "    ./runauthtests.sh --xsd all"
		echo "        Runs all of the tests in the Authorization directory with the --steps -d options."
		echo "    ./runauthtests.sh --xs tests/apiauth/Authorization/CoordinatorFeaturesCest.php"
		echo "        Runs the CoordinatorFeaturesCest.php test with the --steps option."
		echo "    ./runauthtests.sh --x all"
		echo "        Runs all of the tests in the Authorization directory with no options."
		echo "    ./runauthtests.sh"
		echo "        Runs all of the tests in the Authorization directory with the --steps -d option."
		echo "        (Same as ./runauthtests.sh --xsd all)"
		echo "    ./runauthtests.sh -migrate"
		echo "        The command is only used by an individual who is merging another branch into the 'auth' branch."
		echo "        After pulling down and merging the other branch into the auth branch, run the --reload option. Then"
		echo "        run this command.  Do the --dumpcomp command to see what changed.  If it looks good, --move. Then run some"
		echo "        tests to make sure it looks good. Finally, commit and push auth-test-single-file.sql"
		echo
	fi

else

	codceptParams=""
	if [[ -z $1 || $1 == --xsd ]]; then
		codceptParams="--steps -d"
	elif [ "$1" == "--xs" ]; then
		codceptParams="--steps"
	elif [ "$1" == "--xnightly" ]; then
		codceptParams="--coverage --coverage-xml --coverage-html --xml"
	fi

	tstList=$2;
	if [[ -z $tstList || $tstList == all ]]; then
		tstList=`ls tests/apiauth/Authorization/*Cest.php`
	fi

	echo
	let "fileCnt = 0"
	let "failedFileCnt = 0"
	failedFiles="";
	echo "Run Authorization Tests..."
	for tstFile in $tstList
	do
		echo
		echo -e $colrGn">>>>>>>>>>>>>>>=====================================<<<<<<<<<<<<<<<<"$colrNo
		echo "Loading database schema and data from tests/_data/auth-teset-single-file.sql... "
		mysql -u root -psynapse -e "drop database synapse"
		mysql -u root -psynapse -e "create database synapse"
		mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql
		echo "Running: bin/codecept run $tstFile $codceptParams"
		echo -e $colrGn">>>>>>>>>>>>>>>=====================================<<<<<<<<<<<<<<<<"$colrNo
		echo
		let "fileCnt++"
		bin/codecept run $tstFile $codceptParams
		if [[ $? > 0 ]]; then
			let "failedFileCnt++"
			failedFiles=$failedFiles" "$tstFile
		fi
		echo
		echo
	done

	echo
	echo -e $colrGn">>>>>>>>>>>>>>>=====================================<<<<<<<<<<<<<<<<"$colrNo
	echo "Loading database schema and data one last time so it is clean... "
	mysql -u root -psynapse -e "drop database synapse"
	mysql -u root -psynapse -e "create database synapse"
	mysql -u root -psynapse synapse < tests/_data/auth-test-single-file.sql
	echo -e $colrGn">>>>>>>>>>>>>>>=====================================<<<<<<<<<<<<<<<<"$colrNo
	echo

	if [[ $fileCnt > 1 ]]; then
		echo
		echo "Number of test Files run: "$fileCnt
		if [[ $failedFileCnt > 0 ]]; then
			echo -e $colrFl"    FAIL: There were one or more tests in "$failedFileCnt" test file(s) that failed!"$colrFlOff
			echo "    Files with failed tests:"
			for tstFile in $failedFiles
			do
				echo "        "$tstFile
			done
		else
			echo -e $colrGn"    ALL tests in ALL files passed successfully!"$colrNo
		fi
		echo
	fi
fi

