# tollwerk / admin

Purpose of this module:

* ???

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. I recommend [reading it](http://tollwerk-admin.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP 5.6 or later. We recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

Please install the module by cloning the Github repository and running composer:

```bash
git clone https://github.com/tollwerk/admin.git
cd admin
composer install
```

### System setup

Create a user group `account` that all account users will belong to:

```bash
groupadd account
```

Configure Apache to run under the newly created `account` group by setting the `Group` directive in `/etc/apache2/httpd.conf` accordingly:

```
# ...
Group account
# ...
```

Create a directory used for the Certbot challenges:

```bash
mkdir /www/htdocs/letsencrypt
```

### Database installation

Run `mysql` with appropriate privileges and run the following commands (replace `system` with your database name / user and `***` with your actual database password):

```
CREATE USER 'system'@'localhost' IDENTIFIED BY '***';
GRANT USAGE ON *.* TO 'system'@'localhost' IDENTIFIED BY '***' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
CREATE DATABASE IF NOT EXISTS `system` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL PRIVILEGES ON `system`.* TO 'system'@'localhost';
FLUSH PRIVILEGES;
```

### Admin configuration

Create the admin configuration by copying the sample file:

```bash
cp config/config.example.yml config/config.yml
```

Edit the configuration file by adding your database credentials and adapting the binary commands accordingly to your server setup.

### Doctrine initialization

In order to initialize the database, please run the following commands (from the installation directory):

```bash
# To create the database
./vendor/bin/doctrine orm:schema-tool:create

# To update the database and preview the SQL queries first
./vendor/bin/doctrine orm:schema-tool:update --dump-sql

# To update the database
./vendor/bin/doctrine orm:schema-tool:update --force

# To create the proxie classes
./vendor/bin/doctrine orm:generate:proxies
```

## Quality

[![Build Status](https://secure.travis-ci.org/tollwerk/admin.svg)](https://travis-ci.org/tollwerk/admin)
[![Coverage Status](https://coveralls.io/repos/tollwerk/admin/badge.svg?branch=master&service=github)](https://coveralls.io/github/tollwerk/admin?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tollwerk/admin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tollwerk/admin/?branch=master)
[![Code Climate](https://codeclimate.com/github/tollwerk/admin/badges/gpa.svg)](https://codeclimate.com/github/tollwerk/admin)
[![Documentation Status](https://readthedocs.org/projects/apparat-resource/badge/?version=latest)](http://tollwerk-admin.readthedocs.io/en/latest/?badge=latest)

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
