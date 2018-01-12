<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Domain\Vhost
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

namespace Tollwerk\Admin\Domain\Vhost;

use Tollwerk\Admin\Domain\Domain\DomainInterface;

/**
 * Virtual host
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Domain
 */
class Vhost implements VhostInterface
{
    /**
     * Active account
     *
     * @var boolean
     */
    protected $active = false;
    /**
     * Primary domain
     *
     * @var DomainInterface
     */
    protected $primaryDomain;
    /**
     * Secondary domains
     *
     * @var DomainInterface[]
     */
    protected $secondaryDomains = [];
    /**
     * Document root
     *
     * @var string
     */
    protected $docroot;
    /**
     * Virtual host type
     *
     * @var string
     */
    protected $type = self::TYPE_APACHE;
    /**
     * Ports
     *
     * @var array
     */
    protected $ports = [];
    /**
     * Active PHP version
     *
     * @var null|string
     */
    protected $php = null;
    /**
     * Absolute URL to redirect to
     *
     * @var null|string
     */
    protected $redirectUrl = null;
    /**
     * Redirect status code
     *
     * @var int
     */
    protected $redirectStatus = self::REDIRECT_DEFAULT_STATUS;
    /**
     * Default port for HTTP virtual hosts
     *
     * @var int
     */
    const PORT_HTTP_DEFAULT = 80;
    /**
     * Default port for HTTPS virtual hosts
     *
     * @var int
     */
    const PORT_HTTPS_DEFAULT = 443;
    /**
     * Default redirect status
     *
     * @var int
     */
    const REDIRECT_DEFAULT_STATUS = 301;
    /**
     * HTTP protocol
     *
     * @var int
     */
    const PROTOCOL_HTTP = 1;
    /**
     * HTTPS protocol
     *
     * @var int
     */
    const PROTOCOL_HTTPS = 2;
    /**
     * Apache virtual host
     *
     * @var string
     */
    const TYPE_APACHE = 'apache';
    /**
     * Supported protocols
     *
     * @var array
     */
    public static $supportedProtocols = [
        self::PROTOCOL_HTTP => 'http',
        self::PROTOCOL_HTTPS => 'https',
    ];
    /**
     * Default protocol ports
     *
     * @var array
     */
    public static $defaultProtocolPorts = [
        self::PROTOCOL_HTTP => self::PORT_HTTP_DEFAULT,
        self::PROTOCOL_HTTPS => self::PORT_HTTPS_DEFAULT,
    ];

    /**
     * Virtual host constructor
     *
     * @param DomainInterface $primaryDomain Primary domain
     * @param string $docroot Document root
     * @param string $type Virtual host type
     * @internal param int $port Port
     */
    public function __construct(DomainInterface $primaryDomain, $docroot, $type = self::TYPE_APACHE)
    {
        $this->primaryDomain = $primaryDomain;
        $this->docroot = $docroot;
        $this->type = $type;
    }

    /**
     * Return whether the account is active
     *
     * @return boolean Active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set whether the account is active
     *
     * @param boolean $active Active
     * @return VhostInterface Self reference
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Return the primary domain
     *
     * @return DomainInterface Primary domain
     */
    public function getPrimaryDomain()
    {
        return $this->primaryDomain;
    }

    /**
     * Return the document root
     *
     * @return string Document root
     */
    public function getDocroot()
    {
        return $this->docroot;
    }

    /**
     * Return the virtual host type
     *
     * @return string Virtual host type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return a ports list
     *
     * @param int|null $protocol Optional Protocol
     * @return array|null Ports list
     * @throws \RuntimeException If the requested protocol is unsupported
     */
    public function getPorts($protocol = null)
    {
        if ($protocol !== null) {
            $protocol = intval($protocol);

            // If the protocol is unsupported
            if (empty(self::$supportedProtocols[$protocol])) {
                throw new \RuntimeException(sprintf('Invalid protocol "%s"', $protocol), 1475484081);
            }

            return empty($this->ports[$protocol]) ? null : $this->ports[$protocol];
        }

        return $this->ports;
    }

    /**
     * Return the secondary domains
     *
     * @param bool $excludeWildcards Exclude wildcard domains
     * @return DomainInterface[] Secondary domains
     */
    public function getSecondaryDomains($excludeWildcards = false)
    {
        $secondaryDomains = array_values($this->secondaryDomains);
        return $excludeWildcards ?
            array_filter($secondaryDomains, function (DomainInterface $domain) {
                return !$domain->isWildcard();
            }) :
            $secondaryDomains;
    }

    /**
     * Set the secondary domains
     *
     * @param DomainInterface[] $secondaryDomains
     * @return Vhost Self reference
     * @throws \RuntimeException If the domain is invalid
     */
    public function setSecondaryDomains(array $secondaryDomains)
    {
        $this->secondaryDomains = [];
        /** @var DomainInterface $secondaryDomain */
        foreach ($secondaryDomains as $secondaryDomain) {
            // If the domain is invalid
            if (!is_object($secondaryDomain)
                || !(new \ReflectionClass($secondaryDomain))->implementsInterface(DomainInterface::class)
            ) {
                throw new \RuntimeException(sprintf('Invalid secondary domain "%s"', $secondaryDomain), 1475484852);
            }
            $this->secondaryDomains[strval($secondaryDomain)] = $secondaryDomain;
        }

        return $this;
    }

    /**
     * Add a secondary domain
     *
     * @param DomainInterface $secondaryDomain Secondary domain
     * @return Vhost Self reference
     */
    public function addSecondaryDomain(DomainInterface $secondaryDomain)
    {
        if (!array_key_exists(strval($secondaryDomain), $this->secondaryDomains)) {
            $this->secondaryDomains[strval($secondaryDomain)] = $secondaryDomain;
        }
        return $this;
    }

    /**
     * Remove a secondary domain
     *
     * @param DomainInterface $secondaryDomain Secondary domain
     * @return Vhost Self reference
     */
    public function removeSecondaryDomain(DomainInterface $secondaryDomain)
    {
        unset($this->secondaryDomains[strval($secondaryDomain)]);
        return $this;
    }

    /**
     * Return the active PHP version
     *
     * @return null|string Active PHP version
     */
    public function getPhp()
    {
        return $this->php;
    }

    /**
     * Set the active PHP version
     *
     * @param null|string $php Active PHP version
     * @return Vhost Self reference
     * @throws \RuntimeException If the PHP version is invalid
     */
    public function setPhp($php)
    {
        // If the PHP version is invalid
        if (($php !== null) && !preg_match('%^\d\.\d$%', $php)) {
            throw new \RuntimeException(sprintf('Invalid PHP version "%s"', $php), 1475485163);
        }

        $this->php = $php;
        return $this;
    }

    /**
     * Enable a port
     *
     * @param int $protocol Protocol
     * @param int $port Optional: Port (defaults to protocol specific port)
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     * @throws \RuntimeException If the port is invalid
     */
    public function enablePort($protocol, $port = null)
    {
        $protocol = intval($protocol);

        // If the protocol is unsupported
        if (empty(self::$supportedProtocols[$protocol])) {
            throw new \RuntimeException(sprintf('Invalid protocol "%s"', $protocol), 1475484081);
        }

        // If the port is invalid
        $port = ($port === null) ? self::$defaultProtocolPorts[$protocol] : intval($port);
        if ($port <= 0) {
            throw new \RuntimeException(sprintf('Invalid port "%s"', $port), 1475502412);
        }

        // Create the protocol specific port list if necessary
        if (empty($this->ports[$protocol])) {
            $this->ports[$protocol] = [];
        }

        // Register the port
        $this->ports[$protocol] = array_unique(array_merge($this->ports[$protocol], [$port]));
        return $this;
    }

    /**
     * Disable a port
     *
     * @param int $port Port
     * @return Vhost Self reference
     * @throws \RuntimeException If the port is invalid
     */
    public function disablePort($port)
    {
        $port = intval($port);

        // If the port is invalid
        if ($port <= 0) {
            throw new \RuntimeException(sprintf('Invalid port "%s"', $port), 1475502412);
        }

        // Run through all protocols and delete the port
        foreach ($this->ports as $protocol => $ports) {
            $this->ports[$protocol] = array_values(array_diff($ports, [$port]));
        }
        return $this;
    }

    /**
     * Disable a supported protocol
     *
     * @param int $protocol Protocol
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     */
    public function disableProtocol($protocol)
    {
        $protocol = intval($protocol);

        // If the protocol is unsupported
        if (empty(self::$supportedProtocols[$protocol])) {
            throw new \RuntimeException(sprintf('Invalid protocol "%s"', $protocol), 1475484081);
        }

        unset($this->ports[$protocol]);
        return $this;
    }

    /**
     * Return the redirect URL
     *
     * @return null|string Redirect URL
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set the redirect URL
     *
     * @param null|string $redirectUrl Redirect URL
     * @return Vhost Self reference
     * @throws \RuntimeException If the redirect URL is invalid
     */
    public function setRedirectUrl($redirectUrl)
    {
        $redirectUrl = trim($redirectUrl) ?: null;

        // If the redirect URL is invalid
        if (($redirectUrl !== null) &&
            (!filter_var($redirectUrl, FILTER_VALIDATE_URL)
                || !in_array(strtolower(parse_url($redirectUrl, PHP_URL_SCHEME)), self::$supportedProtocols))
        ) {
            throw new \RuntimeException(sprintf('Invalid redirect URL "%s"', $redirectUrl), 1475486589);
        }

        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Return the redirect HTTP status code
     *
     * @return int Redirect HTTP status code
     */
    public function getRedirectStatus()
    {
        return $this->redirectStatus;
    }

    /**
     * Set the redirect HTTP status code
     *
     * @param int $redirectStatus Redirect HTTP status code
     * @return Vhost Self reference
     * @throw \RuntimeException If the redirect HTTP status code is invalid
     */
    public function setRedirectStatus($redirectStatus)
    {
        // If the redirect HTTP status code is invalid
        if (!is_int($redirectStatus) || (($redirectStatus < 300) || ($redirectStatus > 308))) {
            throw new \RuntimeException(sprintf('Invalid redirect HTTP status code "%s"', $redirectStatus), 1475486679);
        }
        $this->redirectStatus = $redirectStatus;
        return $this;
    }
}
