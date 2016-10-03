<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Domain\Vhost
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

namespace Tollwerk\Admin\Domain\Vhost;

use Tollwerk\Admin\Domain\Domain\DomainInterface;

/**
 * Virtual host
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Domain
 */
class Vhost implements VhostInterface
{
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
     * Port
     *
     * @var int
     */
    protected $port;
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
    protected $redirectStatus = 301;
    /**
     * Supported protocols
     *
     * @var int
     */
    protected $protocols = self::PROTOCOL_HTTP;
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
     * Supported protocols
     *
     * @var array
     */
    protected static $supportedProtocols = [
        self::PROTOCOL_HTTP => 'http',
        self::PROTOCOL_HTTPS => 'https',
    ];

    /**
     * Virtual host constructor
     *
     * @param DomainInterface $primaryDomain Primary domain
     * @param string $docroot Document root
     * @param int $port Port
     */
    public function __construct(DomainInterface $primaryDomain, $docroot, $port = self::PORT_HTTP_DEFAULT)
    {
        $this->primaryDomain = $primaryDomain;
        $this->docroot = $docroot;
        $this->port = $port;
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
     * Return the port
     *
     * @return int Port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Return the secondary domains
     *
     * @return DomainInterface[]
     */
    public function getSecondaryDomains()
    {
        return array_values($this->secondaryDomains);
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
     * Return the supported protocols
     *
     * @return int Supported protocols
     */
    public function getProtocols()
    {
        return $this->protocols;
    }

    /**
     * Set the supported protocols
     *
     * @param int $protocols Supported protocols
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     */
    public function setProtocols($protocols)
    {
        $protocols = $supportedProtocols = intval($protocols);
        foreach (self::$supportedProtocols as $supportedProtocol => $supportedProtocolString) {
            $supportedProtocols &= ~$supportedProtocol;
        }
        // If the protocol is unsupported
        if (intval($supportedProtocols)) {
            throw new \RuntimeException(sprintf('Invalid protocols "%s"', $protocols), 1475484081);
        }
        $this->protocols = $protocols;
        return $this;
    }

    /**
     * Enable a supported protocol
     *
     * @param int $protocol Protocol
     * @return Vhost Self reference
     * @throws \RuntimeException If the protocol is unsupported
     */
    public function enableProtocol($protocol)
    {
        $protocol = intval($protocol);

        // If the protocol is unsupported
        if (empty(self::$supportedProtocols[$protocol])) {
            throw new \RuntimeException(sprintf('Invalid protocol "%s"', $protocol), 1475484081);
        }

        $this->protocols |= $protocol;
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

        $this->protocols &= ~$protocol;
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
     */
    public function setRedirectStatus($redirectStatus)
    {
        if (!is_int($redirectStatus) || (($redirectStatus < 300) || ($redirectStatus > 308))) {
            throw new \RuntimeException(sprintf('Invalid redirect HTTP status code "%s"', $redirectStatus), 1475486679);
        }
        $this->redirectStatus = $redirectStatus;
        return $this;
    }
}