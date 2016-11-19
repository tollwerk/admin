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
