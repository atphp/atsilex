#!/bin/bash

# File
# ---------------------
# This script should be called by CI, not manually
#
# CodeShip.com > Project settings > Deployment > Add new "Custom script":
#
#   bash ~/clone/Deploy.sh
#

# Target requirements
# ---------------------
# 
# Softwares
# --------
# ssh
# rsync
# php5-cli (5.5+)
# php5-sqlite or php5-mysql or php5-pgsql
# php5-intl
# 
# Authenticate
# -------
# CodeShip.com > Project settings > General -> SSH public key
#   Copy -> Add to target ~/.ssh/authorized_keys
# 

# Variables
# ---------------------
# $CLONE           - Example: $HOME/clone
# $TARGET_USER     - Example: deployer
# $TARGET_HOST     - Example: example.com
# $TARGET_PORT     - Example: 22
# $TARGET_DIR      - Example: /var/www
# $TARGET_COMPOSER - Example: /usr/local/bin/composer

# Fill default values
# ---------------------
if [ -z "$CLONE" ];       then CLONE=$HOME/clone; fi
if [ -z "$TARGET_PORT" ]; then TARGET_PORT=22; fi
if [ -z "$SSH" ];         then SSH=$(which ssh)" $TARGET_USER@$TARGET_HOST -p $TARGET_PORT"; fi

# Check variables
# ---------------------
DEPLOY_ERROR=0
if [ -z "$TARGET_ALIAS" ];    then DEPLOY_ERROR=1; echo "Missing variable $TARGET_ALIAS";   fi
if [ -z "$TARGET_USER" ];     then DEPLOY_ERROR=1; echo "Missing variable TARGET_USER";     fi
if [ -z "$TARGET_HOST" ];     then DEPLOY_ERROR=1; echo "Missing variable TARGET_HOST";     fi
if [ -z "$TARGET_DIR" ];      then DEPLOY_ERROR=1; echo "Missing variable TARGET_DIR";      fi
if [ -z "$TARGET_COMPOSER" ]; then DEPLOY_ERROR=1; echo "Missing variable TARGET_COMPOSER"; fi
if [ $DEPLOY_ERROR -ge 1 ];   then exit 1; fi

# Set php version through phpenv. 5.3, 5.4 and 5.5 available
phpenv local 5.5

# Install requirements
composer install --prefer-source --no-interaction || {
	echo "Composer install failed.";
  exit 1;
}

# Sync built source code to target
# ---------------------
# Sync source code
rsync -Pav --delete ${CLONE}/ $TARGET_USER@$TARGET_HOST:$TARGET_DIR/ --exclude=files/ --exclude=.git/ || {
	echo "rsync failed!";
  exit 1;
}

# Run orm update command on remote server
$SSH "cd $TARGET_DIR; php cli.php orm:schema-tool:update --force"
