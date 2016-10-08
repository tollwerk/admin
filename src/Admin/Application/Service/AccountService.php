<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Application\Service
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

namespace Tollwerk\Admin\Application\Service;

use Tollwerk\Admin\Application\Contract\PersistenceAdapterFactoryInterface;
use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Domain\Vhost\Vhost;

/**
 * Account service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
class AccountService
{
    /**
     * Storage adapter strategy
     *
     * @var StorageAdapterStrategyInterface
     */
    protected $storageAdapterStrategy;
    /**
     * Persistence adapter strategy factory
     *
     * @var PersistenceAdapterFactoryInterface
     */
    protected $persistenceAdapterFactory;

    /**
     * Constructor
     *
     * @param StorageAdapterStrategyInterface $storageAdapterStrategy Storage adapter strategy
     * @param PersistenceAdapterFactoryInterface $persistenceAdapterFactory
     */
    public function __construct(
        StorageAdapterStrategyInterface $storageAdapterStrategy,
        PersistenceAdapterFactoryInterface $persistenceAdapterFactory
    ) {
        $this->storageAdapterStrategy = $storageAdapterStrategy;
        $this->persistenceAdapterFactory = $persistenceAdapterFactory;
    }

    /**
     * Load and return an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function load($name)
    {
        return $this->storageAdapterStrategy->loadAccount($name);
    }

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function create($name)
    {
        return $this->storageAdapterStrategy->createAccount($name);
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function delete($name)
    {
        return $this->storageAdapterStrategy->deleteAccount($name);
    }

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function enable($name)
    {
        return $this->storageAdapterStrategy->enableAccount($name);
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function disable($name)
    {
        return $this->storageAdapterStrategy->disableAccount($name);
    }

    /**
     * Rename an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @return Account Account
     */
    public function rename($oldname, $newname)
    {
        return $this->storageAdapterStrategy->renameAccount($oldname, $newname);
    }

    /**
     * Persist an account
     *
     * @param Account $account Account
     */
    public function persist(Account $account)
    {
        // Persist the account's virtual hosts
        /** @var Vhost $vhost */
        foreach ($account->getVhosts() as $vhost) {
            $this->persistenceAdapterFactory
                ->makeVhostPersistenceAdapterStrategy($vhost->getType())
                ->persist($account, $vhost);
        }

        // TODO: Persist the account's mailboxes
        // TODO: Persist the account's FTP accesses
    }
}
