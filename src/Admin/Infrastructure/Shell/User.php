<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Infrastructure\Shell
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

namespace Tollwerk\Admin\Infrastructure\Shell;

use mikehaertl\shellcommand\Command;
use Tollwerk\Admin\Ports\App;

/**
 * User related commands
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class User
{
    /**
     * Create a new system user
     *
     * @param string $user User name
     */
    public static function create($user)
    {
        $user = self::validateUser($user);
        $command = new Command();
        $command->setCommand('useradd');
        $command->addArg('--gid', App::getConfig('shell.group'));
//        $command->addArg('--create-home', App::getConfig('shell.group'));
        $command->addArg('--shell', '/bin/false');
        $command->addArg('--base-dir', App::getConfig('shell.base'));
        $command->addArg('--home-dir', $user);
        $command->addArg($user);

        echo $command->getCommand();
    }

    /**
     * Rename a system user
     *
     * @param string $olduser Old user name
     * @param string $newuser New user name
     */
    public static function rename($olduser, $newuser)
    {

    }

    /**
     * Delete a system user
     *
     * @param string $user User name
     */
    public static function delete($user)
    {

    }

    /**
     * Add a system user to a user group
     *
     * @param string $user User name
     * @param string $group Group name
     */
    public static function addGroup($user, $group)
    {

    }

    /**
     * Delete a system user from a user group
     *
     * @param string $user User name
     * @param string $group Group name
     */
    public static function deleteGroup($user, $group)
    {

    }


    protected static function run(Command $command) {
        return true;
    }

    /**
     * Validate a user name
     *
     * @param string $user User name
     * @return string Validated user
     * @throws \RuntimeException If the user name is invalid
     */
    protected static function validateUser($user) {
        $user = trim($user);

        // If the user name is invalid
        if (!strlen($user) || !preg_match('%^[a-z]+$%', $user)) {
            throw  new \RuntimeException(sprintf('Invalid user name "%s"', $user), 1475514940);
        }
        return $user;
    }
}
