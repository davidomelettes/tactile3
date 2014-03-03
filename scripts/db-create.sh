#!/bin/bash

if [ -z "$1" ]; then
  echo "No argument supplied; expected database name"
  exit 1
fi

echo -e "This script will drop, recreate, and then initialise the application database - \e[1m\e[31mUSE WITH CAUTION!\e[0m"
echo -n "Are you sure you wish to continue? [y/N]: "
read confirm
if [ "$confirm" != 'y' ]; then
  echo "Stopped"
  exit 0
fi

# Test whether database already exists
if psql -lqt | cut -d \| -f 1 | grep -wq "$1"; then
  echo -e "\e[1m\e[31mWARNING!\e[0m A database with the name $1 already exists. Continuing will drop the existing database!"
  echo -n "Are you SURE you wish to drop the existing $1 database? [y/N]: "
  read confirm;
  if [ "$confirm" == "y" ]; then
    echo "--DROPPING DATABASE..."
    dropdb "$1"
    if [ "$?" -ne "0" ]; then
      echo "ERR: Failed to drop database"
      exit 1
    fi
    echo "--DATABASE DROPPED"
  else
    echo "Stopped"
    exit 0
  fi
fi

# Create the new database
echo "--CREATING DATABASE..."
createdb "$1"
if [ "$?" -ne "0" ]; then
  echo "ERR: Failed to create database"
  exit 1
fi
echo "--DATABASE CREATED"

# Initialsise the newly created database
echo "--INITIALSING DATABASE..."
PGOPTIONS='--client-min-messages=warning' psql -v ON_ERROR_STOP=1 -q -d $1 -f db_init.sql
if [ "$?" -ne "0" ]; then
  echo "ERR: Failed to initialise database"
  exit 1
fi
echo "--DATABASE INITIALISED"

echo "Application is now ready to accept console commands"
echo "Execute public/index.php for console command help"
