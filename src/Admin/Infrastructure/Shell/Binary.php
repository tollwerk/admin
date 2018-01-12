<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Shell
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

namespace Tollwerk\Admin\Infrastructure\Shell;

use mikehaertl\shellcommand\Command;

/**
 * Helper class to identify system binaries
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure\Shell
 */
class Binary
{
    /**
     * Registered binaries
     *
     * @var array
     */
    protected static $binaries = [];

    /**
     * Identify and return a system binary
     *
     * @param string $binary Binary name
     * @return string Absolute binary path
     */
    public static function get($binary)
    {
        // One time binary registration
        if (empty(self::$binaries[$binary])) {
            $command = new Command();
            $command->setCommand('which');
            $command->addArg($binary);

            if (!$command->execute()) {
                throw new \RuntimeException($command->getError(), $command->getExitCode());
            }
            self::$binaries[$binary] = $command->getOutput();
        }

        return self::$binaries[$binary];
    }

    /**
     * Return a sudoed binary command
     *
     * @param string $binary Binary
     * @return Command Sudoed command
     */
    public static function sudo($binary, $user = null, $group = null)
    {
        $command = new Command();
        $command->setCommand(self::get('sudo'));

        // Add a user
        if (strlen(trim($user))) {
            $command->addArg('-u', trim($user));
        }

        // Add a group
        if (strlen(trim($group))) {
            $command->addArg('-g', trim($group));
        }

        $command->addArg(self::get($binary));
        return $command;
    }
}
