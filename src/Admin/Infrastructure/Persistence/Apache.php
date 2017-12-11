<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
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

use Psr\Log\LogLevel;
use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Factory\CertbotServiceFactory;
use Tollwerk\Admin\Infrastructure\Service\TemplateService;

/**
 * Apache persister
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class Apache
{
    /**
     * Account
     *
     * @var AccountInterface
     */
    protected $account;
    /**
     * Configuration
     *
     * @var array
     */
    protected $config;
    /**
     * Account helper
     *
     * @var AccountHelper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param AccountInterface $account Account
     * @param array $config Configuration
     */
    public function __construct(AccountInterface $account, array $config)
    {
        $this->account = $account;
        $this->config = $config;
        $this->helper = new AccountHelper($this->account);

        // Add some directory names
        $this->config['dataroot'] = $this->helper->directory('data');
        $this->config['vhostroot'] = $this->helper->directory('config'.DIRECTORY_SEPARATOR.'vhosts-available');
        $this->config['logdir'] = $this->helper->directory('log');
        $this->config['account'] = $this->account->getName();
    }

    /**
     * Persist a virtual host
     *
     * @param VhostInterface $vhost Virtual host
     * @return array Files
     */
    public function __invoke(VhostInterface $vhost)
    {
        $files = [];

        $variables = $this->config;
        $variables['primary_domain'] = strval($vhost->getPrimaryDomain());
        $variables['secondary_domains'] = implode(' ', array_map('strval', $vhost->getSecondaryDomains()));
        $variables['secondary_domains_without_wildcards'] =
            implode(', ', array_map('strval', $vhost->getSecondaryDomains(true)));
        $variables['docroot'] =
            rtrim($this->config['dataroot'].DIRECTORY_SEPARATOR.$vhost->getDocroot(), DIRECTORY_SEPARATOR);
        $variables['configroot'] = $this->config['vhostroot'].DIRECTORY_SEPARATOR.$variables['primary_domain'];
        $variables['php_version'] = $vhost->getPhp();

        // If the virtual host should redirect
        if ($vhost->getRedirectUrl() !== null) {
            // TODO: Redirect

            return $files;
        }

        // If the virtual host should support PHP
        if ($vhost->getPhp() !== null) {
            $this->createPhpConfig($vhost, $files, $variables);
        }

        // Add the virtual host include
        $this->addEntry($files, 'apache_vhost.include', TemplateService::render('apache_vhost.include', $variables));
        $this->addEntry($files, 'apache_vhost_custom_directory.include?', TemplateService::render('apache_vhost_custom_directory.include', $variables));
        $this->addEntry($files, 'apache_vhost_custom_all.include?', '');

        // If the HTTPS protocol is supported
        $httpsPorts = $vhost->getPorts(Vhost::PROTOCOL_HTTPS);
        if (!empty($httpsPorts)) {
            $certbotConfig = $this->helper->vhostDirectory($vhost).DIRECTORY_SEPARATOR.'certbot.ini';
            $certbotService = CertbotServiceFactory::create($certbotConfig);
            $primaryDomainIsCertified = $certbotService->isCertified($variables['primary_domain']);

            // Add the SSL configuration include
            $this->addEntry(
                $files,
                'apache_ssl.include',
                TemplateService::render('apache_ssl.include', $variables, !$primaryDomainIsCertified)
            );

            // Run through all SSL ports
            foreach ($httpsPorts as $httpsPort) {

                // Add the HTTPS vhost declaration
                $variables['port'] = $httpsPort;
                $variables['ssl'] = true;
                $this->addEntry($files, 'apache_vhost.conf',
                    TemplateService::render('apache_vhost.conf', $variables));
                $this->addEntry($files, 'apache_vhost_custom_'.$httpsPort.'.include?', '');
            }

            // Add the Certbot configuration
            $this->addEntry($files, 'certbot.ini',
                TemplateService::render('certbot.ini', $variables));

            // Create the well-known symlink
             $certbotService->prepare($vhost, $this->helper);

            // Output a hint if the primary domain isn't certified
            if (!$primaryDomainIsCertified) {
                App::addMessage(
                    sprintf(
                        'The virtual host primary domain "%s" should be properly certified!',
                        $variables['primary_domain']
                    ),
                    LogLevel::NOTICE
                );
            }
        }

        // Run through all non-SSL ports
        foreach ((array)$vhost->getPorts(Vhost::PROTOCOL_HTTP) as $httpPort) {

            // Add the HTTP vhost declaration
            $variables['port'] = $httpPort;
            $variables['ssl'] = false;
            $this->addEntry($files, 'apache_vhost.conf',
                TemplateService::render('apache_vhost.conf', $variables));
            $this->addEntry($files, 'apache_vhost_custom_'.$httpPort.'.include?', '');
        }

        $absoluteFiles = [];
        foreach ($files as $relativeFile => $fileContent) {
            $absoluteFiles[$this->helper->vhostDirectory($vhost).DIRECTORY_SEPARATOR.$relativeFile] =
                implode(PHP_EOL, (array)$fileContent);
        }
        return $absoluteFiles;
    }

    /**
     * Create a PHP configuration include
     *
     * @param VhostInterface $vhost Virtual host
     * @param array $files Configuration files
     * @param array $variables Templating variables
     */
    protected function createPhpConfig(VhostInterface $vhost, array &$files, array $variables)
    {
        // Add the FPM configuration
        $this->addEntry($files, 'fpm-'.$vhost->getPhp().'.conf', TemplateService::render('fpm.conf', $variables));

        // Add the FPM custom configuration
        $this->addEntry($files, 'fpm.include?', TemplateService::render('fpm.include', $variables));

        // Add the FPM include
        $this->addEntry($files, 'apache_fmp.include',
            TemplateService::render('apache_fpm.include', $variables));
    }

    /**
     * Add a file entry
     *
     * @param array $files Files
     * @param string $name File name
     * @param string $entry entry
     */
    protected function addEntry(&$files, $name, $entry)
    {
        if (empty($files[$name])) {
            $files[$name] = [];
        }
        $files[$name][] = $entry;
    }
}
