<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Infrastructure\Persistence
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

namespace Tollwerk\Admin\Infrastructure\Persistence;

use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Infrastructure\Service\TemplateService;
use Tollwerk\Admin\Ports\App;

/**
 * Apache persister
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class Apache
{
    /**
     * Account
     *
     * @var Account
     */
    protected $account;
    /**
     * Webroot directory
     *
     * @var string
     */
    protected $webroot;
    /**
     * Configuration root directory
     *
     * @var
     */
    protected $configroot;
    /**
     * Logging root directory
     *
     * @var
     */
    protected $logroot;

    /**
     * Constructor
     *
     * @param Account $account Account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Create the persistence files
     *
     * @return array
     * @param string $webroot Webroot directory
     * @param string $configroot Configuration directory
     * @param string $logroot Logging root directory
     */
    public function persist($webroot, $configroot, $logroot) {
        // Prepare account directories
        $directories = $this->prepareDirectories($this->account, $webroot, $configroot, $logroot);

        // Prepare account templating variables
        $variables = array_merge(
            (array)App::getConfig('variables'),
            (array)App::getConfig('apache'),
            $directories,
            ['account' => $this->account->getName()]
        );

        // Prepare a list of files and virtual hosts to create / update
        $files = [];

        // Run through all account virtual hosts
        /** @var Vhost $vhost */
        foreach ($this->account->getVhosts() as $vhost) {
            $this->createVirtualHost($vhost, $files, $variables);
        }

        return $files;
    }

    /**
     * Create a virtual host
     *
     * @param Vhost $vhost Virtual host
     * @param array $files Files to create
     * @param array $variables Templating variables
     */
    protected function createVirtualHost(Vhost $vhost, array &$files, array $variables)
    {
        $variables['primary_domain'] = strval($vhost->getPrimaryDomain());
        $variables['secondary_domains'] = implode(', ', array_map('strval', $vhost->getSecondaryDomains()));
        $variables['php_version'] = $vhost->getPhp();

        // If the virtual host should redirect
        if ($vhost->getRedirectUrl() !== null) {
            // TODO: Redirect

            return;
        }

        // If the virtual host should support PHP
        if ($vhost->getPhp() !== null) {
            $this->createPhpConfig($vhost, $files, $variables);
        }

        // Add the virtual host include
        $this->addEntry($files, 'vhost_'.$vhost->getPrimaryDomain().'.include', TemplateService::render('vhost.include', $variables));

        // If the HTTPS protocol is supported
        if ($httpsport = $vhost->getPort(Vhost::PROTOCOL_HTTPS)) {

            // Add the SSL configuration include
            $this->addEntry($files, 'ssl_'.$vhost->getPrimaryDomain().'.include', TemplateService::render('ssl.include', $variables));

            // Add the HTTPS vhost declaration
            $variables['port'] = $httpsport;
            $variables['ssl'] = true;
            $this->addEntry($files, 'vhost_'.$this->account->getName().'.conf', TemplateService::render('vhost.conf', $variables));
        }

        // If the HTTP protocol is supported
        if ($httpport = $vhost->getPort(Vhost::PROTOCOL_HTTP)) {

            // Add the HTTP vhost declaration
            $variables['port'] = $httpport;
            $variables['ssl'] = false;
            $this->addEntry($files, 'vhost_'.$this->account->getName().'.conf', TemplateService::render('vhost.conf', $variables));
        }
    }

    /**
     * Create a PHP configuration include
     *
     * @param Vhost $vhost Virtual host
     * @param array $files Configuration files
     * @param array $variables Templating variables
     */
    protected function createPhpConfig(Vhost $vhost, array &$files, array $variables)
    {
        // Add the FPM configuration
        $this->addEntry($files, 'fpm-'.$vhost->getPhp().'.conf', TemplateService::render('fpm.conf', $variables));

        // Add the FPM include
        $this->addEntry($files, 'fmp_'.$vhost->getPrimaryDomain().'.include', TemplateService::render('fpm.include', $variables));
    }

    /**
     * Prepare account directories
     *
     * @param Account $account
     * @return array Account directories
     * @param string $webroot Webroot directory
     * @param string $configroot Configuration directory
     * @param string $logroot Logging root directory
     * @throws \RuntimeException If the account webroot cannot be created
     */
    protected function prepareDirectories(Account $account, $webroot, $configroot, $logroot)
    {
        // If the account webroot cannot be created
        $this->webroot = $webroot.$account->getName();
        if (!is_dir($this->webroot) && !mkdir($this->webroot, 0777)) {
            throw new \RuntimeException(sprintf('Couldn\'t create account webroot "%s"', $this->webroot, 1475501235));
        }

        // If the account webroot cannot be created
        $this->configroot = $configroot.$account->getName();
        if (!is_dir($this->configroot) && !mkdir($this->configroot, 0777)) {
            throw new \RuntimeException(sprintf('Couldn\'t create account config root "%s"', $this->configroot,
                1475501282));
        }

        // If the account logroot cannot be created
        $this->logroot = $logroot.$account->getName();
        if (!is_dir($this->logroot) && !mkdir($this->logroot, 0777)) {
            throw new \RuntimeException(sprintf('Couldn\'t create account logroot "%s"', $this->logroot, 1475501235));
        }

        return ['webroot' => $this->webroot, 'configroot' => $this->configroot, 'logroot' => $this->logroot];
    }

    /**
     * Add a file entry
     *
     * @param array $files Files
     * @param string $name File name
     * @param string $entry entry
     */
    protected function addEntry(&$files, $name, $entry) {
        if (empty($files[$name])) {
            $files[$name] = [];
        }
        $files[$name][] = $entry;
    }
}
