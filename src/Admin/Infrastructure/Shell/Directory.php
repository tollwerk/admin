<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
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

/**
 * Directory related commands
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class Directory extends AbstractCommand
{
    /**
     * Recursively create a directory
     *
     * @param string $path Directory path
     * @param string $user User name
     * @param string $group Group name
     * @param int $mode File mode
     * @return string Output
     */
    public static function create($path, $user, $group, $mode = 0775)
    {
        $command = Binary::sudo('mkdir', $user, $group);
        $command->addArg('--parents');
        $command->addArg('--mode', decoct($mode));
        $command->addArg($path);
        self::run($command);
        return true;
    }

    /**
     * Recursively delete a directory
     *
     * @param string $path Directory path
     * @param string $user User name
     * @param string $group Group name
     * @param bool $recursive Rekursiv
     * @return string Output
     */
    public static function delete($path, $user, $group, $recursive = false)
    {
        if ($recursive) {
            $command = Binary::sudo('rm', $user, $group);
            $command->addArg('--force');
            $command->addArg('--recursive');
        } else {
            $command = Binary::sudo('rmdir', $user, $group);
        }

        $command->addArg($path);
        self::run($command);
        return true;
    }
}
