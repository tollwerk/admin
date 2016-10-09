<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Application\Contract
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
use Tollwerk\Admin\Domain\Vhost\VhostInterface;

/**
 * Persistence service interface
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
interface PersistenceServiceInterface
{
    /**
     * Constructor
     *
     * @param PersistenceAdapterFactoryInterface $persistenceAdapterFactory Persistence adapter factory
     */
    public function __construct(PersistenceAdapterFactoryInterface $persistenceAdapterFactory);

    /**
     * Create an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function createAccount(AccountInterface $account);

    /**
     * Delete an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function deleteAccount(AccountInterface $account);

    /**
     * Enable an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function enableAccount(AccountInterface $account);

    /**
     * Disable an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function disableAccount(AccountInterface $account);

    /**
     * Rename an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function renameAccount(AccountInterface $account);

    /**
     * Create a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function createVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Delete a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function deleteVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Enable a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function enableVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Disable a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function disableVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Redirect a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function redirectVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Configure the PHP version of a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function phpVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Configure a protocol based port for a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function portVhost(AccountInterface $account, VhostInterface $vhost);

    /**
     * Add a secondary domain to a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function domainVhost(AccountInterface $account, VhostInterface $vhost);
}
