<?php

/**
 * tollwerk-admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2018 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Tollwerk\Admin\Domain\Vhost;

use Tollwerk\Admin\Domain\Domain\DomainInterface;

/**
 * Virtual host interface
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Domain
 */
interface VhostInterface
{
    /**
     * Virtual host constructor
     *
     * @param DomainInterface $primaryDomain Primary domain
     * @param string $docroot Document root
     * @param string $type Virtual host type
     * @internal param int $port Port
     */
    public function __construct(DomainInterface $primaryDomain, $docroot, $type);

    /**
     * Return whether the account is active
     *
     * @return boolean Active
     */
    public function isActive();

    /**
     * Set whether the account is active
     *
     * @param boolean $active Active
     * @return VhostInterface Self reference
     */
    public function setActive($active);

    /**
     * Return the primary domain
     *
     * @return DomainInterface Primary domain
     */
    public function getPrimaryDomain();

    /**
     * Return the document root
     *
     * @return string Document root
     */
    public function getDocroot();

    /**
     * Return the virtual host type
     *
     * @return string Virtual host type
     */
    public function getType();

    /**
     * Return a ports list
     *
     * @param int|null $protocol Optional Protocol
     * @return array|null Ports list
     * @throws \RuntimeException If the requested protocol is unsupported
     */
    public function getPorts($protocol = null);

    /**
     * Return the secondary domains
     *
     * @param bool $excludeWildcards Exclude wildcard domains
     * @return DomainInterface[] Secondary domains
     */
    public function getSecondaryDomains($excludeWildcards = false);

    /**
     * Set the secondary domains
     *
     * @param DomainInterface[] $secondaryDomains
     * @return Vhost Self reference
     * @throws \RuntimeException If the domain is invalid
     */
    public function setSecondaryDomains(array $secondaryDomains);

    /**
     * Add a secondary domain
     *
     * @param DomainInterface $secondaryDomain Secondary domain
     * @return Vhost Self reference
     */
    public function addSecondaryDomain(DomainInterface $secondaryDomain);

    /**
     * Remove a secondary domain
     *
     * @param DomainInterface $secondaryDomain Secondary domain
     * @return Vhost Self reference
     */
    public function removeSecondaryDomain(DomainInterface $secondaryDomain);

    /**
     * Return the active PHP version
     *
     * @return null|string Active PHP version
     */
    public function getPhp();

    /**
     * Set the active PHP version
     *
     * @param null|string $php Active PHP version
     * @return Vhost Self reference
     */
    public function setPhp($php);

    /**
     * Enable a port
     *
     * @param int $protocol Protocol
     * @param int $port Optional: Port (defaults to protocol specific port)
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     * @throws \RuntimeException If the port is invalid
     */
    public function enablePort($protocol, $port = null);

    /**
     * Disable a port
     *
     * @param int $port Port
     * @return Vhost Self reference
     * @throws \RuntimeException If the port is invalid
     */
    public function disablePort($port);

    /**
     * Disable a supported protocol
     *
     * @param int $protocol Protocol
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     */
    public function disableProtocol($protocol);

    /**
     * Return the redirect URL
     *
     * @return null|string Redirect URL
     */
    public function getRedirectUrl();

    /**
     * Set the redirect URL
     *
     * @param null|string $redirectUrl Redirect URL
     * @return Vhost Self reference
     * @throws \RuntimeException If the redirect URL is invalid
     */
    public function setRedirectUrl($redirectUrl);

    /**
     * Return the redirect HTTP status code
     *
     * @return int Redirect HTTP status code
     */
    public function getRedirectStatus();

    /**
     * Set the redirect HTTP status code
     *
     * @param int $redirectStatus Redirect HTTP status code
     * @return Vhost Self reference
     */
    public function setRedirectStatus($redirectStatus);
}
