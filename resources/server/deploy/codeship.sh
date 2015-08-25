#!/bin/bash
#
# File
# ---------------------
# This script should be called by CI, not manually
#
# CodeShip.com > Project settings > Deployment > Add new "Custom script":
#
#   bash ~/clone/scripts/Deploy.sh
# 
# Authenticate
# ---------------------
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

# Fill missing variables with default values
# ---------------------
if [ -z "$CLONE" ];       then CLONE=$HOME/clone; fi
if [ -z "$TARGET_PORT" ]; then TARGET_PORT=22; fi
if [ -z "$SSH" ];         then SSH=$(which ssh)" $TARGET_USER@$TARGET_HOST -p $TARGET_PORT"; fi

# Check variables
# ---------------------
DEPLOY_ERROR=0
if [ -z "$TARGET_USER" ];     then DEPLOY_ERROR=1; echo "Missing variable TARGET_USER";     fi
if [ -z "$TARGET_HOST" ];     then DEPLOY_ERROR=1; echo "Missing variable TARGET_HOST";     fi
if [ -z "$TARGET_DIR" ];      then DEPLOY_ERROR=1; echo "Missing variable TARGET_DIR";      fi
if [ -z "$TARGET_COMPOSER" ]; then DEPLOY_ERROR=1; echo "Missing variable TARGET_COMPOSER"; fi
if [ $DEPLOY_ERROR -ge 1 ];   then exit 1; fi

# Sync built source code to target
# ---------------------
# Sync source code
$SSH "mkdir -p $TARGET_DIR"
rsync -a --delete --exclude=files/ --exclude=vendor/ --exclude=.git/ \
      ${CLONE}/ \
      $TARGET_USER@$TARGET_HOST:$TARGET_DIR/;

# Run update commands on remote server
$SSH "cd $TARGET_DIR; \
      $TARGET_COMPOSER install --optimize-autoloader --no-interaction;"
