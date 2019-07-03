#!/bin/bash
cd "$(dirname "$0")"
colrGn="\033[0;32m"
colrNo="\033[0m"
colrFl="\033[37;41m"
colrFlOff="\033[39;49m"

dataFile="the test suite's sql file"


if [[ -z $1 || ! -z $3 ]]; then

  echo
  echo -e $colrGn"This script requires either 1 or 2 arguments."$colrNo
  echo

elif [[ ! -z $2 ]]; then

  if [[ $2 == auth ]]; then
    dataFile="auth-test-single-file.sql"
    dataFilePath="tests/_data/auth-test-single-file.sql"
  elif [[ $2 == risk ]]; then
    dataFile="RiskTestData.sql"
    dataFilePath="tests/_data/RiskTestData.sql"
  elif [[ $2 == oldrisk ]]; then
      dataFile="OldRiskTestData.sql"
      dataFilePath="tests/_data/OldRiskTestData.sql"
  else
    echo
    echo "If multiple arguments are used, the second argument must be either auth, risk, or oldrisk."
    echo "Use --help for details."
    echo
    exit
  fi

  if [[ $1 == --reload ]]; then

    echo
    echo "Dropping and Creating the synapse database..."
    mysql -u root -psynapse -e "drop database synapse"
    mysql -u root -psynapse -e "create database synapse"

    echo "Loading database schema and data from $dataFilePath..."
    mysql -u root -psynapse synapse < $dataFilePath
    echo "Done!"
    echo
    exit

  elif [[ $1 == --dumpcomp ]]; then
  
    echo -n "Dumping and Prepping dumped database files for comparison..."
    mysqldump -uroot -psynapse --routines --events synapse --single-transaction -f > ./comp_tmp.sql
    sed -e "s/),(/),\n(/g" ./comp_tmp.sql | sed -e "s/VALUES (/VALUES\n(/g" > ./comp_localhost_db.sql
    sed -e "s/),(/),\n(/g" $dataFilePath | sed -e "s/VALUES (/VALUES\n(/g" > ./comp_tmp.sql
    mv ./comp_tmp.sql $dataFilePath
    echo " Done."
    echo
    echo "1) Open a terminal window on your local machine."
    echo "2) cd to <YourRepositoryDirectory>/synapse-backend"
    echo "3) Run (assuming you have diffmerge installed):"
    echo
    echo "diffmerge $dataFilePath comp_localhost_db.sql"
    echo
    exit

  elif [[ $1 == --move ]]; then

    echo
    echo "Moving database dump file to $dataFile (assumes --dumpcomp has already been run)..."
    mv ./comp_localhost_db.sql $dataFilePath
    echo "Done!"
    echo
    exit

  fi

else  # if user supplies one command-line option

  if [[ $1 == --gotnewcode ]]; then

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
    echo
    exit

  elif [[ $1 == --migrate ]]; then

    echo 
    echo -e $colrGn"REMINDER: "$colrNo"Have you already run the --reload command so you have a clean database?"
    echo "Running Doctrine Migration scripts against localhost database for your current synapse-backend branch..."
    echo
    app/console doctrine:migration:migrate
    echo
    echo "Done!"
    exit

  elif [[ $1 == --reload || $1 == --dumpcomp || $1 == --move ]]; then
      echo
      echo -e $colrGn"This option requires a second argument."$colrNo
      echo
  fi
fi

# The following is displayed for --help or any unrecognized command.

echo
echo "./managedata.sh [option] [option for test suite]"
echo
echo "Options: (You may only use ONE option at a time)"
echo
echo "    --help       Display this help information."
echo "    --helplong   Display this help information plus additional examples and details."
echo "    --reload     Reload the localhost database with the contents found in $dataFile."
echo "    --dumpcomp   Dump the contents of the 'synapse' database on localhost, make the dumped" 
echo "                 file and $dataFile ready for comparison and display the compare command."
echo "    --move       After you confirm via diffmerge that the data is correct, moves the file"
echo "                 dumped during --dumpcomp to $dataFile."
echo "    --gotnewcode Used to reset your development/test environment after you download" 
echo "                 new code from git."
echo "    --migrate    Runs the Doctrine Migration command for your current branch against the" 
echo "                 localhost DB. MAKE SURE you have just run --reload before doing this."
echo "                 (There are rare cases when you might not want to run --reload so we" 
echo "                 don't automatically do it for you.)"
echo
echo "Options for test suite:    auth    risk    oldrisk"
echo "  One of these options must be selected to use the options --reload, --dumpcomp, or --move."
echo "  They cannot be used with the options --gotnewcode or --migrate."
echo
if [[ $1 == --helplong ]]; then
  echo "Adding Test Data:"
  echo "    When adding data for tests to your local database, one would usually use the following sequence:"
  echo 
  echo "    1) ./managedata.sh --reload"
  echo "    2) Add your database changes via your local UI."
  echo "    3) ./managedata.sh --dumpcomp"
  echo "    4) Follow the directions displayed when --dumpcomp runs to see a visual comparison"
  echo "       between the data you added and how the database looked prior to your changes."
  echo "    5) If the changes are not what you expect, go back to step 1. Warning: Running step 1"
  echo "       will wipe out any data changes you have made since your last --move."
  echo "    6) If you are good with your changes..."
  echo "    7) ./managedata.sh --move"
  echo "    8) The --move will move your version of the database into your local copy of $dataFile."
  echo "    9) You can now run your test with no fear of losing your new data changes."
  echo 
  echo "    Repeat the above steps until all your data and tests are as you want them."
  echo "    Then (after doing a pull) commit and push your tests and also $dataFile."
  echo
  echo "Migration:"
  echo "    ./managedata.sh --migrate"
  echo "        The command is only used by an individual who is merging another branch into the"
  echo "        'auth' branch. After pulling down and merging the other branch into the auth branch,"
  echo "        run the --reload option. Then run this command. Do the --dumpcomp command to see" 
  echo "        what changed. If it looks good, --move. Then run some tests to make sure it looks" 
  echo "        good. Finally, commit and push $dataFile."
  echo
fi


