#!/usr/bin/env bash

# Deployment check for local env override file, then replace tokens in parameters.
if [ -f ~/.synapse.local.env.sh ]
then
  . ~/.synapse.local.env.sh
  PARAMETERS_YML=app/config/parameters_$SYMFONY_ENV.yml
  echo >> $PARAMETERS_YML # ruby parsing requires newline at eof
  /opt/chef/embedded/bin/ruby -ple '{
      "database_host"     => ENV["SYNAPSE_DATABASE_HOST"],
      "database_port"     => ENV["SYNAPSE_DATABASE_PORT"],
      "database_name"     => ENV["SYNAPSE_DATABASE_NAME"],
      "database_user"     => ENV["SYNAPSE_DATABASE_USER"],
      "database_password" => ENV["SYNAPSE_DATABASE_PASSWORD"],
      "ses_host"          => ENV["SYNAPSE_SES_HOST"],
      "ses_access_key"    => ENV["SYNAPSE_SES_ACCESS_KEY"],
      "ses_secret_key"    => ENV["SYNAPSE_SES_SECRET_KEY"],
      "cache.host"        => ENV["SYNAPSE_CACHE_HOST"],
      "queue.host"        => ENV["SYNAPSE_QUEUE_HOST"],
    }.each { |key,val| $_ = $1+val if  $_ =~ /^(\s*#{key}:\s*)/ }
  ' -i $PARAMETERS_YML
fi

./fixcache.sh
sudo -u www-data SYMFONY_ENV=$SYMFONY_ENV php app/console doctrine:migrations:migrate -n
sudo -u www-data SYMFONY_ENV=$SYMFONY_ENV php app/console cache:clear --no-debug -n
sudo -u www-data SYMFONY_ENV=$SYMFONY_ENV php app/console cache:warmup -n

