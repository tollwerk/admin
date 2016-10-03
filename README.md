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