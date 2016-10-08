<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Service
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

namespace Tollwerk\Admin\Infrastructure\Service;

use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Infrastructure\App;

/**
 * Account directory service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class DirectoryService
{
    /**
     * Account
     *
     * @var AccountInterface
     */
    protected $account;
    /**
     * Account base directory
     *
     * @var string
     */
    protected $basedir;

    /**
     * Constructor
     *
     * @param AccountInterface $account Account
     */
    public function __construct(AccountInterface $account)
    {
        $this->account = $account;
        $this->basedir = rtrim(App::getConfig('general.basedir'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.
            $this->account->getName().DIRECTORY_SEPARATOR;
    }

    /**
     * Return the data directory (or a sub directory)
     *
     * @param string|null $path Optional: Subdirectory
     * @param boolean $absolute Return the absolute path
     * @return string Data directory
     * @throws \RuntimeException If the directory doesn't exist or is invalid
     */
    public function getDataDirectory($path = null, $absolute = true)
    {
        $dataDirectory = $dataBaseDirectory = $this->basedir.'data';
        if (strlen($path)) {
            $dataDirectory .= DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR);
        }

        // Resolve all symbolic links etc.
        $absDataDirectory = realpath($dataDirectory);

        // If the directory doesn't exist or is otherwise invalid
        if (!$absDataDirectory
            || !is_dir($absDataDirectory)
            || strncmp($dataBaseDirectory, $absDataDirectory, strlen($dataBaseDirectory))
        ) {
            throw new \RuntimeException(sprintf('Path "%s" doesn\'t exist or is not a valid data directory',
                $dataDirectory), 1475929558);
        }

        return $absolute ?
            $absDataDirectory :
            trim(substr($absDataDirectory, strlen($dataBaseDirectory)), DIRECTORY_SEPARATOR);
    }
}
