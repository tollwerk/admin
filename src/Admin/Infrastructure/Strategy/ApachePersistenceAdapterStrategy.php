<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
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

use Tollwerk\Admin\Application\Contract\VhostPersistenceAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Persistence\Apache;

/**
 * Apache persistence adapter strategy
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class ApachePersistenceAdapterStrategy implements VhostPersistenceAdapterStrategyInterface
{
    /**
     * Configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * Constructor
     *
     * @throws \RuntimeException If the adapter configuration is invalid
     */
    public function __construct()
    {
        $apacheConfig = App::getConfig('apache');

        // If the adapter configuration is invalid
        if (!is_array($apacheConfig)) {
            throw new \RuntimeException('Invalid apache adapter strategy configuration', 1475500531);
        }

        $this->config = array_merge((array)App::getConfig('general'), $apacheConfig);
    }

    /**
     * Persist a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return bool Success
     * @throws \RuntimeException If a config file couldn't be written
     */
    public function persist(AccountInterface $account, VhostInterface $vhost)
    {
        $persister = new Apache($account, $this->config);
        $entries = $persister($vhost);

        // Persist the file entries
        foreach ($entries as $filename => $filecontent) {
            if (!file_put_contents($filename, $filecontent)) {
                throw new \RuntimeException(sprintf('Couldn\'t write config file "%s"', $filename, 1475507805));
            }
        }

        return true;
    }
}
