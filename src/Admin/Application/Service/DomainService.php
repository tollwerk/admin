<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Application\Service
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

namespace Tollwerk\Admin\Application\Service;

use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Domain\DomainInterface;

/**
 * Domain service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
class DomainService extends AbstractService
{
    /**
     * Load and return a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function load($name)
    {
        return $this->storageAdapterStrategy->loadDomain($name);
    }

    /**
     * Load and return an unassigned domain belonging to a particular account
     *
     * @param string $name Domain name
     * @param AccountInterface $account Account name
     * @return DomainInterface Domain
     */
    public function loadUnassigned($name, AccountInterface $account)
    {
        return $this->storageAdapterStrategy->loadDomain($name, $account);
    }

    /**
     * Load and return a domain assigned a particular virtual host
     *
     * @param string $name Domain name
     * @param AccountInterface $account Account name
     * @param string $docroot Virtual host document root
     * @return DomainInterface Domain
     */
    public function loadAssigned($name, AccountInterface $account, $docroot)
    {
        return $this->storageAdapterStrategy->loadDomain($name, $account, $docroot);
    }

    /**
     * Create a domain
     *
     * @param string $name Domain name
     * @param AccountInterface $account Account name
     * @return DomainInterface Domain
     */
    public function create($name, AccountInterface $account)
    {
        return $this->storageAdapterStrategy->createDomain($name, $account);
    }

    /**
     * Delete a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function delete($name)
    {
        return $this->storageAdapterStrategy->deleteDomain($name);
    }

    /**
     * Enable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function enable($name)
    {
        return $this->storageAdapterStrategy->enableDomain($name);
    }

    /**
     * Disable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function disable($name)
    {
        return $this->storageAdapterStrategy->disableDomain($name);
    }

    /**
     * Enable a domain wildcard
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function enableWildcard($name)
    {
        return $this->storageAdapterStrategy->enableDomainWildcard($name);
    }

    /**
     * Disable a domain wildcard
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     */
    public function disableWildcard($name)
    {
        return $this->storageAdapterStrategy->disableDomainWildcard($name);
    }
}
