# tollwerk / admin

Purpose of this module:

* ???

## Documentation

Please find the [project documentation](doc/index.md) in the `doc` directory. I recommend [reading it](http://tollwerk-admin.readthedocs.io/) via *Read the Docs*.

## Installation

This library requires PHP 5.6 or later. We recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

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



# Konzept

## Konfiguration

```
CONFIG_DIR = /www/accounts
WEBROOT_DIR = /www/vhtdocs
LOG_DIR = /var/log/apache2
CERTBOT_EMAIL = info@tollwerk.de
APACHE_GROUP = apache
FPM_SOCKETS = /var/run/php-fpm
PHPMYADMIN_VHOST = /etc/apache2/vhosts.d/default_pma_vhost.include
```

## Anlegen eines Accounts

1. **Abfragen** eines Account-Namens `$ACCOUNT`
2. Erzeugen eines Datenverzeichnisses `$WEBROOT_DIR/$ACCOUNT`
3. Erzeugen eines Konfigurationsverzeichnisses `$CONFIG_DIR/$ACCOUNT`
4. Erzeugen eines Logverzeichnisses `$LOG_DIR/$ACCOUNT`
5. Anlegen eines Systembenutzers `$ACCOUNT`
6. **Abfragen** der Hauptdomain `$PRIMARY_DOMAIN`
7. Anlegen einer Certbot-Konfiguration unter `$CONFIG_DIR/$ACCOUNT/certbot.ini` anhand des Templates `certbot.ini`
8. **Abfragen** der gewünschten PHP-Version `$PHP_VERSION`
9. Anlegen einer PHP-Konfiguration unter `$CONFIG_DIR/$ACCOUNT/fpm-$PHP_VERSION.conf` anhand des Templates `fpm.conf`
10. Anlegen eines Vhost-FPM-Konfiguration unter `$CONFIG_DIR/$ACCOUNT/vhost_fpm.include` anhand des Templates `vhost_fpm.include`
11. Anlegen einer Vhost-Konfiguration unter `$CONFIG_DIR/$ACCOUNT/vhost.include` anhand des Templates `vhost.include`
12. Anlegen eines Vhosts (ohne SSL) unter `$CONFIG_DIR/$ACCOUNT/vhost.conf` anhand des Templates `vhost.conf`
13. Neustart von PHP-FPM
14. Neustart von Apache
15. Erzeugen eines Zertifikats per Certbot (TODO)
16. Neuschreiben des Vhosts mit SSL (TODO)
17. Neustart von Apache

## Account-Operationen

### Account anlegen

#### Parameter

* Account-Name

#### Operationen

* Systembenutzer erzeugen
* Datenbankeintrag erzeugen

### Account umbenennen

#### Parameter

* Alter Account-Name
* Neuer Account-Name

#### Operationen

* Alten Systembenutzer löschen
* Neuen Systembenutzer anlegen
* Alten Datenbankeintrag anpassen

### Account löschen

#### Parameter

* Account-Name

#### Operationen

* Systembenutzer löschen
* Datenbankeintrag und abhängige Datensätze löschen
