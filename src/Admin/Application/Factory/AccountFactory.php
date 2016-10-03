<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
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

namespace Tollwerk\Admin\Application\Factory;

use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\Account;

/**
 * Account factory
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Application
 */
class AccountFactory
{
    /**
     * Instantiate and return an account
     *
     * @param string $name Account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function load($name, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->loadAccount($name);
    }

    /**
     * Create and return an account
     *
     * @param string $name Account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function create($name, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->createAccount($name);
    }

    /**
     * Delete and return an account
     *
     * @param string $name Account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function delete($name, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->deleteAccount($name);
    }

    /**
     * Enable and return an account
     *
     * @param string $name Account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function enable($name, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->enableAccount($name);
    }

    /**
     * Disable and return an account
     *
     * @param string $name Account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function disable($name, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->disableAccount($name);
    }

    /**
     * Rename and return an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @param StorageAdapterStrategyInterface $strategy Storage adapter strategy
     * @return Account Account
     */
    public static function rename($oldname, $newname, StorageAdapterStrategyInterface $strategy)
    {
        return $strategy->renameAccount($oldname, $newname);
    }
}
