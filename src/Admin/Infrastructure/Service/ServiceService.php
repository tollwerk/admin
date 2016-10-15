<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Service
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

namespace Tollwerk\Admin\Infrastructure\Service;

use Tollwerk\Admin\Application\Contract\ServiceServiceInterface;
use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\Factory\PhpFpmServiceFactory;
use Tollwerk\Admin\Infrastructure\Factory\WebserverServiceFactory;

/**
 * Service service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class ServiceService implements ServiceServiceInterface
{
    /**
     * Webservers to reload
     *
     * @var array
     */
    protected $webserver = [];
    /**
     * PHP FPM pools to reload
     *
     * @var array
     */
    protected $php = [];

    /**
     * Run all scheduled operations
     */
    public function runSchedule()
    {
        // Run through all PHP versions and reload the according FPM pool
        foreach (array_keys($this->php) as $php) {
            PhpFpmServiceFactory::create($php)->reload();
        }

        // Run through all webservers and reload them
        foreach (array_keys($this->webserver) as $webserver) {
            WebserverServiceFactory::create($webserver)->reload();
        }
    }

    /**
     * Reload a webserver
     *
     * @param string $type Webserver type
     */
    public function reloadWebserver($type)
    {
        $this->webserver[$type] = true;
    }

    /**
     * Reload a PHP FPM pool
     *
     * @param string|null $version PHP version
     */
    public function reloadPhp($version)
    {
        $this->php[$version] = true;
    }

    /**
     * Renew a SSL certificate
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     */
    public function renewCertificate(AccountInterface $account, VhostInterface $vhost) {

    }
}
