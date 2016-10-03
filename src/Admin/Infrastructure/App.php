<?php

/**
 * Toggl Dashboard
 *
 * @category    Apparat
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Ports
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Tollwerk\Admin\Infrastructure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Yaml\Yaml;
use Tollwerk\Admin\Application\Service\AccountService;
use Tollwerk\Admin\Infrastructure\Strategy\DoctrineStorageAdapterStrategy;

/**
 * App
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Ports
 */
class App
{
    /**
     * Configuration
     *
     * @var array
     */
    protected static $config;
    /**
     * Root directory
     *
     * @var string
     */
    protected static $rootDirectory;
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    protected static $entityManager;
    /**
     * Developer mode
     *
     * @var boolean
     */
    protected static $devMode;
    /**
     * Account service
     *
     * @var AccountService
     */
    protected static $accountService = null;
    /**
     * App domain
     *
     * @var string
     */
    const DOMAIN = 'admin';

    /**
     * Bootstrap
     *
     * @see https://github.com/toggl/toggl_api_docs/blob/master/reports.md
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/configuration.html
     *
     * @param bool $devMode Developer mode
     */
    public static function bootstrap($devMode = false)
    {
        self::$devMode = !!$devMode;
        self::$rootDirectory = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR;

        // Initialize the configuration
        $config = file_get_contents(self::$rootDirectory.'config'.DIRECTORY_SEPARATOR.'config.yml');
        self::$config = Yaml::parse($config);

        // Initialize doctrine
        self::initializeDoctrine();
    }

    /**
     * Initialize Doctrine
     */
    protected static function initializeDoctrine()
    {
        // If the Doctrine parameters don't exist
        if (empty(self::$config['doctrine'])
            || !is_array(self::$config['doctrine'])
            || empty(self::$config['doctrine']['dbparams'])
        ) {
            throw new \InvalidArgumentException('Invalid Doctrine database parameters', 1466175889);
        }

        $modelPaths = [
            self::$rootDirectory.'src'.DIRECTORY_SEPARATOR.'Admin'
            .DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Model'
        ];
        $dbParams = self::$config['doctrine']['dbparams'];
        $config = Setup::createAnnotationMetadataConfiguration($modelPaths, self::$devMode);

        self::$entityManager = EntityManager::create($dbParams, $config);

        // Set the locale
        $locale = self::getConfig('general.locale');
        putenv('LC_ALL='.$locale);
        setlocale(LC_ALL, $locale);

        \bindtextdomain(self::DOMAIN, self::$rootDirectory.'src'.DIRECTORY_SEPARATOR.'Admin'
            .DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Lang');
        \bind_textdomain_codeset(self::DOMAIN, 'UTF-8');
        \textdomain(self::DOMAIN);
    }

    /**
     * Return the entity manager
     *
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        return self::$entityManager;
    }

    /**
     * Get a configuration value
     *
     * @param null $key Optional: config value key
     * @return mixed Configuration value(s)
     */
    public static function getConfig($key = null)
    {
        if ($key === null) {
            return self::$config;
        }
        $keyParts = explode('.', $key);
        $config =& self::$config;
        foreach ($keyParts as $keyPart) {
            if (!array_key_exists($keyPart, $config)) {
                throw new \InvalidArgumentException(sprintf('Invalid config key "%s"', $key), 1466179561);
            }
            $config =& $config[$keyPart];
        }
        return $config;
    }

    /**
     * Return the contents of a particular template
     *
     * @param string $template Template name
     * @return string template contents
     */
    public static function getTemplate($template)
    {
        $templateFile = self::$rootDirectory.'src'.DIRECTORY_SEPARATOR.'Admin'
            .DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.$template;
        if (!file_exists($templateFile)) {
            throw new \RuntimeException(sprintf('Unknown template "%s"', $template), 1475503926);
        }
        return file_get_contents($templateFile);
    }

    /**
     * Return the account service
     *
     * @return AccountService Account service
     */
    public static function getAccountService() {
        if (self::$accountService === null) {
            $storageAdapter = new DoctrineStorageAdapterStrategy();
            self::$accountService = new AccountService($storageAdapter);
        }

        return self::$accountService;
    }
}
