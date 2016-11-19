<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Application\Factory
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

namespace Tollwerk\Admin\Application\Contract;

use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Domain\DomainInterface;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;

/**
 * Storage adapter strategy interface
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
interface StorageAdapterStrategyInterface
{
    /**
     * Load an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function loadAccount($name);

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     * @throws \RuntimeException If the account cannot be created
     */
    public function createAccount($name);

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     * @throws \RuntimeException If the account cannot be enabled
     */
    public function enableAccount($name);

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     * @throws \RuntimeException If the account cannot be disabled
     */
    public function disableAccount($name);

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     * @throws \RuntimeException If the account cannot be deleted
     */
    public function deleteAccount($name);

    /**
     * Rename and return an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @return AccountInterface Account
     * @throws \RuntimeException If the account is unknown
     */
    public function renameAccount($oldname, $newname);

    /**
     * Load a domain (optionally: unassigned)
     *
     * @param string $name Domain name
     * @param AccountInterface $account Optional: Account the domain must belong to
     * @param string $vhostDocroot Optional: Document root of the virtual host the domain must be assigned to (otherwise: unassigned)
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain is unknown
     * @throws \RuntimeException If the domain belongs to another account
     * @throws \RuntimeException If the domain is assigned to a virtual host but should be unassigned
     * @throws \RuntimeException If the domain is assigned to a different virtual host
     */
    public function loadDomain($name, AccountInterface $account = null, $vhostDocroot = null);

    /**
     * Create a domain
     *
     * @param string $name Domain name
     * @param AccountInterface $account Account the domain belongs to
     * @return DomainInterface Domain
     * @throws \RuntimeException If the account is unknown
     */
    public function createDomain($name, AccountInterface $account);

    /**
     * Delete a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be deleted
     */
    public function deleteDomain($name);

    /**
     * Enable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be enabled
     */
    public function enableDomain($name);

    /**
     * Disable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be disabled
     */
    public function disableDomain($name);

    /**
     * Enable a domain wildcard
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be enabled
     */
    public function enableDomainWildcard($name);

    /**
     * Disable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be disabled
     */
    public function disableDomainWildcard($name);

    /**
     * Load a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function loadVhost(AccountInterface $account, $docroot = '');

    /**
     * Create a virtual host
     *
     * @param AccountInterface $account Account name
     * @param DomainInterface $domain Domain
     * @param string $docroot Document root
     * @param string $type Virtual host Type
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function createVhost(AccountInterface $account, DomainInterface $domain, $docroot, $type);

    /**
     * Delete a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function deleteVhost(AccountInterface $account, $docroot);

    /**
     * Enable a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function enableVhost(AccountInterface $account, $docroot);

    /**
     * Disable a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function disableVhost(AccountInterface $account, $docroot);

    /**
     * Redirect a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @param string|null $url Redirect URL
     * @param int $status Redirect HTTP status
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function redirectVhost(AccountInterface $account, $docroot, $url, $status);

    /**
     * Configure the PHP version of a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @param string|null $php PHP version
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function phpVhost(AccountInterface $account, $docroot, $php);

    /**
     * Add a protocol port to a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param int $protocol Protocol
     * @param int $port Port
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function addVhostPort(AccountInterface $account, $docroot, $protocol, $port);

    /**
     * Remove a protocol port from a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param int $protocol Protocol
     * @param int $port Port
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function removeVhostPort(AccountInterface $account, $docroot, $protocol, $port);

    /**
     * Add a secondary domain to a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param DomainInterface $domain Domain
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function addVhostDomain(AccountInterface $account, $docroot, DomainInterface $domain);

    /**
     * Remove a secondary domain from a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param DomainInterface $domain Domain
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function removeVhostDomain(AccountInterface $account, $docroot, DomainInterface $domain);
}
