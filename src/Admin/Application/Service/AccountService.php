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

use Tollwerk\Admin\Domain\Account\AccountInterface;

/**
 * Account service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
class AccountService extends AbstractService
{
    /**
     * Load and return an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function load($name)
    {
        return $this->storageAdapterStrategy->loadAccount($name);
    }

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function create($name)
    {
        $account = $this->storageAdapterStrategy->createAccount($name);
        $this->persistenceService->createAccount($account);
        return $account;
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function delete($name)
    {
        $account = $this->storageAdapterStrategy->deleteAccount($name);
        $this->persistenceService->deleteAccount($account);
        return $account;
    }

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function enable($name)
    {
        $account = $this->storageAdapterStrategy->enableAccount($name);
        $this->persistenceService->enableAccount($account);
        return $account;
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
     */
    public function disable($name)
    {
        $account = $this->storageAdapterStrategy->disableAccount($name);
        $this->persistenceService->disableAccount($account);
        return $account;
    }

    /**
     * Rename an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @return AccountInterface Account
     */
    public function rename($oldname, $newname)
    {
        $account = $this->storageAdapterStrategy->renameAccount($oldname, $newname);
        $this->persistenceService->renameAccount($account);
        return $account;
    }
}
