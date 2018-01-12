<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Persistence
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

namespace Tollwerk\Admin\Infrastructure\Persistence;

use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\App;

/**
 * Account persistence helper
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class AccountHelper
{
    /**
     * Account
     *
     * @var AccountInterface
     */
    protected $account;

    /**
     * Constructor
     *
     * @param AccountInterface $account
     */
    public function __construct(AccountInterface $account)
    {
        $this->account = $account;
    }

    /**
     * Return an account specific directory
     *
     * @param string|null $path Optional: sub directory
     * @return string Directory path
     */
    public function directory($path = null)
    {
        $directory = rtrim(App::getConfig('general.basedir'), DIRECTORY_SEPARATOR).
            DIRECTORY_SEPARATOR.$this->account->getName();
        $path = trim($path, DIRECTORY_SEPARATOR);
        return $directory.(strlen($path) ? DIRECTORY_SEPARATOR.$path : '');
    }

    /**
     * Return a virtual host directory
     *
     * @param VhostInterface $vhost Virtual host
     * @param bool $enabled Enabled virtual host
     * @return string Virtual host directory
     */
    public function vhostDirectory(VhostInterface $vhost, $enabled = false)
    {
        return $this->directory(
            'config'.DIRECTORY_SEPARATOR.'vhosts-'.($enabled ? 'enabled' : 'available').DIRECTORY_SEPARATOR.
            strval($vhost->getPrimaryDomain())
        );
    }
}
