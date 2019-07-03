TMP_CACHE=/var/cache/synapse-backend
TMP_LOGS=/var/log/synapse-backend
APP=./app
APP_CACHE="$APP/cache"
APP_LOGS="$APP/logs"

if [ ! -d $TMP_CACHE ]
then
        sudo mkdir $TMP_CACHE;
        sudo mkdir $TMP_LOGS;
fi
sudo chmod -R 0777 $TMP_CACHE;
sudo chmod -R 0777 $TMP_LOGS;

#Delete the app cache folder if it exists and is not a symlink
if [[ -d $APP_CACHE && ! -L $APP_CACHE ]]
then
        rm -rf $APP_CACHE
fi

#Delete the app logs folder if it exists and is not a symlink
# if [[ -d $APP_LOGS && ! -L $APP_LOGS ]]
# then
#         rm -rf $APP_LOGS
# fi

#Create symlinks to the tmp cache folder
if [ ! -L $APP_CACHE ]
then
        ln -s $TMP_CACHE $APP_CACHE
fi

#Create symlinks to the tmp log folder
# if [ ! -L $APP_LOGS ]
# then
#         ln -s $TMP_LOGS $APP_LOGS
# fi

#Create log folder
if [ ! -d $APP_LOGS ]
then
        mkdir $APP_LOGS
        chmod 777 $APP_LOGS
fi
