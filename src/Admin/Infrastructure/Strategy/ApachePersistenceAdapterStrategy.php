<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Infrastructure\Strategy
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

namespace Tollwerk\Admin\Infrastructure\Strategy;

use Tollwerk\Admin\Application\Contract\PersistenceAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Infrastructure\Persistence\Apache;
use Tollwerk\Admin\Ports\App;

/**
 * Apache persistence adapter strategy
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class ApachePersistenceAdapterStrategy implements PersistenceAdapterStrategyInterface
{
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
     * @throws \RuntimeException If the adapter configuration is invalid
     * @throws \RuntimeException If the Apache webroot configuration is invalid
     * @throws \RuntimeException If the Apache config root configuration is invalid
     */
    public function __construct()
    {
        $apacheConfig = App::getConfig('apache');

        // If the adapter configuration is invalid
        if (!is_array($apacheConfig)) {
            throw new \RuntimeException('Invalid apache adapter strategy configuration', 1475500531);
        }

        // If the Apache webroot configuration is invalid
        if (empty($apacheConfig['webroot']) || !is_dir($apacheConfig['webroot'])) {
            throw new \RuntimeException(sprintf('Invalid apache webroot configuration', 1475500605));
        }
        $this->webroot = rtrim($apacheConfig['webroot'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        // If the Apache config root configuration is invalid
        if (empty($apacheConfig['config']) || !is_dir($apacheConfig['config'])) {
            throw new \RuntimeException(sprintf('Invalid apache config root configuration', 1475500666));
        }
        $this->configroot = rtrim($apacheConfig['config'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        // If the Apache log root configuration is invalid
        if (empty($apacheConfig['logroot']) || !is_dir($apacheConfig['logroot'])) {
            throw new \RuntimeException(sprintf('Invalid apache logging root configuration', 1475506107));
        }
        $this->logroot = rtrim($apacheConfig['logroot'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    /**
     * Persist an account
     *
     * @param Account $account Account
     * @return boolean Success
     * @throws \RuntimeException If a config file couldn't be written
     */
    public function persistAccount(Account $account)
    {
        $persister = new Apache($account);
        $entries = $persister->persist($this->webroot, $this->configroot, $this->logroot);

        // Persist the file entries
        foreach ($entries as $filename => $configParts) {
            $configFile = $this->configroot.$account->getName().DIRECTORY_SEPARATOR.$filename;
            if (!file_put_contents($configFile, implode(PHP_EOL.PHP_EOL, $configParts))) {
                throw new \RuntimeException(sprintf('Couldn\'t write config file "%s"', $filename, 1475507805));
            }
        }

        return true;
    }
}
