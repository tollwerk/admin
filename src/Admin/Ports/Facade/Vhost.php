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
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Facade\AbstractFacade;

/**
 * Virtual host facade
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Ports
 */
class Vhost extends AbstractFacade
{
    /**
     * Create and add a virtual host to an account
     *
     * @param string $account Account name
     * @param string $domain Primary domain name
     * @param string $docroot Document root
     * @param string $type Virtual host type
     * @return boolean Success
     */
    public static function create($account, $domain, $docroot = '', $type = DomainVhost::TYPE_APACHE)
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        // Load the primary domain
        $domain = App::getDomainService()->loadUnassigned($domain, $account);

        return App::getVirtualHostService()->create($account, $domain, $docroot, $type) instanceof VhostInterface;
    }

    /**
     * Delete and remove a virtual host from an account
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @return boolean Success
     */
    public static function delete($account, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->delete($account, $docroot) instanceof VhostInterface;
    }

    /**
     * Enable a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @return bool Success
     */
    public static function enable($account, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->enable($account, $docroot) instanceof VhostInterface;
    }

    /**
     * Disable a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @return bool Success
     */
    public static function disable($account, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->disable($account, $docroot) instanceof VhostInterface;
    }

    /**
     * Redirect a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param string $url Redirect URL
     * @param int $status Redirect HTTP status
     * @return bool Success
     */
    public static function redirect($account, $docroot = '', $url = '', $status = DomainVhost::REDIRECT_DEFAULT_STATUS)
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()
            ->redirect($account, $docroot, $url, intval($status)) instanceof VhostInterface;
    }

    /**
     * Configure the PHP version of a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param string|null $php PHP version
     * @return bool Success
     */
    public static function php($account, $docroot = '', $php = null)
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->php($account, $docroot, $php) instanceof VhostInterface;
    }

    /**
     * Configure a protocol based port for a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param int $protocol Protocol
     * @param int|null $port Port
     * @return bool Success
     */
    public static function port(
        $account,
        $docroot = '',
        $protocol = \Tollwerk\Admin\Domain\Vhost\Vhost::PROTOCOL_HTTP,
        $port = null
    ) {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->port($account, $docroot, $protocol, $port) instanceof VhostInterface;
    }

    /**
     * Add a secondary domain to a virtual host
     *
     * @param string $account Account name
     * @param string $domain Domain
     * @param string $docroot Document root
     * @return bool Success
     */
    public static function addDomain($account, $domain, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        // Load the primary domain
        $domain = App::getDomainService()->loadUnassigned($domain, $account);

        return App::getVirtualHostService()->addDomain($account, $domain, $docroot) instanceof VhostInterface;
    }

    /**
     * Remove a secondary domain from a virtual host
     *
     * @param string $account Account name
     * @param string $domain Domain
     * @param string $docroot Document root
     * @return bool Success
     */
    public static function removeDomain($account, $domain, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        // Load the primary domain
        $domain = App::getDomainService()->loadAssigned($domain, $account, $docroot);

        return App::getVirtualHostService()->removeDomain($account, $domain, $docroot) instanceof VhostInterface;
    }

    /**
     * Certify a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @return boolean Success
     */
    public static function certify($account, $docroot = '')
    {
        // Get the account to operate on
        $account = self::loadAccount($account);

        return App::getVirtualHostService()->certify($account, $docroot) instanceof VhostInterface;
    }
}
