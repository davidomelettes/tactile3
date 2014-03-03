Omelettes/Skeleton Application
=======================

Introduction
------------
This application skeleton is cool; and by cool, I mean totally sweet.


Environment Configuration
-------------------------

In order to install and run this application, you will need PHP >=5.3.3, as specified
in composer.json. You will also need the following PHP modules:

JSON (php5-json)
An appropriate PDO database driver (e.g. php5-pgsql)


Installation using Composer
---------------------------
This application uses Composer for dependency management. Project dependencies are
specified in composer.json, and installed by executing the Composer binary:

    php composer.phar self-update
    php composer.phar install

If Composer gives you shit about missing a missing json_decode() function, you may
need to install the json module for PHP as specified in the Environment Configuration!

The vendor directory should now contain the project dependencies.


Database Setup
--------------

To create a database for the application, use the database initialisation script:

    cd scripts/
    ./db-create.sh dbname


Console Usage
-------------




Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the
internal PHP cli-server in the root directory:

    php -S localhost:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName omelettes-app.localhost
        DocumentRoot /path/to/omelettes-app/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/omelettes-app/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>

