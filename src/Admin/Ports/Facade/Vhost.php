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

use Tollwerk\Admin\Domain\Vhost\Vhost as DomainVhost;
use Tollwerk\Admin\Domain\Account\Account as DomainAccount;
use Tollwerk\Admin\Infrastructure\App;

/**
 * Virtual host facade
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Ports
 */
class Vhost
{
    /**
     * Create and add a virtual host to an account
     *
     * @param string $account Account name
     * @param string $domain Primary domain name
     * @param string $docroot Document root
     * @param string $type Virtual host type
     * @return boolean Success
     * @throws \Exception If the account couldn't be created
     */
    public static function create($account, $domain, $docroot, $type = DomainVhost::TYPE_APACHE)
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        // Check if the domain already exists and is unassigned
        try {
            $domain = App::getDomainService()->loadAvailable($domain, $account);
        } catch (\RuntimeException $e) {
            // If the domain already exists but is assigned or belongs to another account: Error
            if ($e->getCode() != 1475915909) {
                throw $e;
            }

            // Create the domain
            App::getDomainService()->create($domain, $account);
        }

        return true;
    }

    /**
     * Get the account to operate on
     *
     * @param string $name Account name
     * @return DomainAccount Account instance
     * @throws \Exception If the account doesn't exist
     */
    protected static function loadAccount($name) {
        $account = App::getAccountService()->load($name);
        if (!$account instanceof DomainAccount) {
            throw new \Exception(sprintf('Account "%s" doesn\'t exist', $name), 1475915142);
        }
        return $account;
    }
}
