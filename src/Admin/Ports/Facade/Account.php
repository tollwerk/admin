<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Ports\Facade
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

namespace Tollwerk\Admin\Ports\Facade;

use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Facade\AbstractFacade;
use Tollwerk\Admin\Infrastructure\Shell\Exception as ShellException;
use Tollwerk\Admin\Infrastructure\Shell\User;

/**
 * Account facade
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Ports
 */
class Account extends AbstractFacade
{
    /**
     * Create an account
     *
     * @param string $name Account name
     * @return boolean Success
     * @throws \Exception If the account couldn't be created
     */
    public static function create($name)
    {
        User::create($name);

        try {
            $account = App::getAccountService()->create($name);
            if (!$account instanceof \Tollwerk\Admin\Domain\Account\Account) {
                throw new \Exception(sprintf('Couldn\'t create account "%s"', $name), 1475528906);
            }

        } catch (\Exception $e) {
            User::delete($name);
            throw $e;
        }

        return true;
    }

    /**
     * Rename an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @return boolean Success
     */
    public static function rename($oldname, $newname)
    {
        User::rename($oldname, $newname);

        try {
            $account = App::getAccountService()->rename($oldname, $newname);
            if (!$account instanceof \Tollwerk\Admin\Domain\Account\Account) {
                throw new \Exception(sprintf('Couldn\'t rename account "%s" to "%s"', $oldname, $newname), 1475531002);
            }

        } catch (\Exception $e) {
            User::rename($newname, $oldname);
            throw $e;
        }

        return true;
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     */
    public static function delete($name)
    {
        // If the account cannot be deleted
        $deletedAccount = App::getAccountService()->delete($name);
        if (!($deletedAccount instanceof \Tollwerk\Admin\Domain\Account\Account)) {
            throw new \RuntimeException(sprintf('Couldn\'t delete account "%s"', $name), 1475532983);
        }

        try {
            User::delete($name);
        } catch (ShellException $e) {
            $account = App::getAccountService()->create($name);
            if (($account instanceof \Tollwerk\Admin\Domain\Account\Account) && $deletedAccount->isActive()) {
                App::getAccountService()->enable($name);
            }
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        return true;
    }

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return bool Success
     * @throws \RuntimeException If the account cannot be ensabled
     */
    public static function enable($name)
    {
        // If the account cannot be enabled
        if (!(App::getAccountService()->enable($name) instanceof \Tollwerk\Admin\Domain\Account\Account)) {
            throw new \RuntimeException(sprintf('Couldn\'t enable account "%s"', $name), 1475532231);
        }
        return true;
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return bool Success
     * @throws \RuntimeException If the account cannot be disabled
     */
    public static function disable($name)
    {
        // If the account cannot be disabled
        if (!(App::getAccountService()->disable($name) instanceof \Tollwerk\Admin\Domain\Account\Account)) {
            throw new \RuntimeException(sprintf('Couldn\'t disable account "%s"', $name), 1475532231);
        }
        return true;
    }
}
