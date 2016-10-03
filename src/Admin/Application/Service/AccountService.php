<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
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

use Tollwerk\Admin\Application\Contract\PersistenceAdapterStrategyInterface;
use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Application\Factory\AccountFactory;
use Tollwerk\Admin\Domain\Account\Account;

/**
 * Account service
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Application\Service
 */
class AccountService
{
    /**
     * @var StorageAdapterStrategyInterface
     */
    protected $storageAdapterStrategy;

    /**
     * Constructor
     *
     * @param StorageAdapterStrategyInterface $storageAdapterStrategy Storage adapter strategy
     */
    public function __construct(StorageAdapterStrategyInterface $storageAdapterStrategy)
    {
        $this->storageAdapterStrategy = $storageAdapterStrategy;
    }

    /**
     * Load an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function load($name)
    {
        return AccountFactory::load($name, $this->storageAdapterStrategy);
    }

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function create($name)
    {
        return AccountFactory::create($name, $this->storageAdapterStrategy);
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function delete($name)
    {
        return AccountFactory::delete($name, $this->storageAdapterStrategy);
    }

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function enable($name)
    {
        return AccountFactory::enable($name, $this->storageAdapterStrategy);
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return Account Account
     */
    public function disable($name)
    {
        return AccountFactory::disable($name, $this->storageAdapterStrategy);
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
        return AccountFactory::rename($oldname, $newname, $this->storageAdapterStrategy);
    }

    /**
     * Persist an account
     *
     * @param Account $account Account
     * @param PersistenceAdapterStrategyInterface $persistenceAdapterStrategy Persistence adapter strategy
     */
    public function persist(Account $account, PersistenceAdapterStrategyInterface $persistenceAdapterStrategy)
    {
        $persistenceAdapterStrategy->persistAccount($account);
    }
}
