<?php

/**
 * Toggl Dashboard
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Ports
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2018 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2018 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Log\LogLevel;
use Symfony\Component\Yaml\Yaml;
use Tollwerk\Admin\Application\Contract\PersistenceAdapterFactoryInterface;
use Tollwerk\Admin\Application\Contract\PersistenceServiceInterface;
use Tollwerk\Admin\Application\Contract\ServiceServiceInterface;
use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Application\Service\AccountService;
use Tollwerk\Admin\Application\Service\DomainService;
use Tollwerk\Admin\Application\Service\VirtualHostService;
use Tollwerk\Admin\Infrastructure\Doctrine\EnumVhosttypeType;
use Tollwerk\Admin\Infrastructure\Factory\PersistenceAdapterFactory;
use Tollwerk\Admin\Infrastructure\Service\PersistenceService;
use Tollwerk\Admin\Infrastructure\Service\ServiceService;
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
     * Virtual host service
     *
     * @var VirtualHostService
     */
    protected static $vhostService = null;
    /**
     * Domain service
     *
     * @var DomainService
     */
    protected static $domainService = null;
    /**
     * Active Storage adapter
     *
     * @var StorageAdapterStrategyInterface
     */
    protected static $storageAdapter;
    /**
     * Persistence adapter factory
     *
     * @var PersistenceAdapterFactoryInterface
     */
    protected static $persistenceAdapterFactory;
    /**
     * Persistence service
     *
     * @var PersistenceServiceInterface
     */
    protected static $persistenceService;
    /**
     * Service service
     *
     * @var ServiceServiceInterface
     */
    protected static $serviceService;
    /**
     * Application level messages
     *
     * @var array
     */
    protected static $messages = [];
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

        // Register the Doctrine storage adapter and persistence adapter factory
        self::$storageAdapter = new DoctrineStorageAdapterStrategy();
        self::$persistenceAdapterFactory = new PersistenceAdapterFactory();
        self::$serviceService = new ServiceService();
        self::$persistenceService = new PersistenceService(self::$persistenceAdapterFactory, self::$serviceService);
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
        $platform = self::$entityManager->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        // Register the virtual host type declaration
        Type::addType(EnumVhosttypeType::ENUM_TYPE, EnumVhosttypeType::class);

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
     * @param bool $useDefault Use a default template
     * @return string template contents
     */
    public static function getTemplate($template, $useDefault = false)
    {
        $templateFile = self::$rootDirectory;
        $templateFile .= $useDefault ? 'config' :
            'src'.DIRECTORY_SEPARATOR.'Admin'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Templates';
        $templateFile .= DIRECTORY_SEPARATOR.$template;
        if (!file_exists($templateFile)) {
            throw new \RuntimeException(
                sprintf('Unknown '.($useDefault ? 'default ' : '').'template "%s"', $template),
                1475503926
            );
        }
        return file_get_contents($templateFile);
    }

    /**
     * Return the account service
     *
     * @return AccountService Account service
     */
    public static function getAccountService()
    {
        if (self::$accountService === null) {
            self::$accountService = new AccountService(self::$storageAdapter, self::$persistenceService);
        }

        return self::$accountService;
    }

    /**
     * Return the virtual host service
     *
     * @return VirtualHostService Virtual host service
     */
    public static function getVirtualHostService()
    {
        if (self::$vhostService === null) {
            self::$vhostService = new VirtualHostService(self::$storageAdapter, self::$persistenceService);
        }

        return self::$vhostService;
    }

    /**
     * Return the domain service
     *
     * @return DomainService Domain service
     */
    public static function getDomainService()
    {
        if (self::$domainService === null) {
            self::$domainService = new DomainService(self::$storageAdapter, self::$persistenceService);
        }

        return self::$domainService;
    }

    /**
     * Return the shell service service
     *
     * @return ServiceServiceInterface Service service
     */
    public static function getServiceService()
    {
        return self::$serviceService;
    }

    /**
     * Add an application level message
     *
     * @param string $message Message
     * @param string $level Log level
     */
    public static function addMessage($message, $level = LogLevel::INFO)
    {
        $message = trim($message);
        if (strlen($message)) {
            self::$messages[] = [$message, $level];
        }
    }

    /**
     * Return all application level messages
     *
     * @return array Messages
     */
    public static function getMessages()
    {
        return self::$messages;
    }
}
