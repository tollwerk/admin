<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Application\Service
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

namespace Tollwerk\Admin\Application\Service;

use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Domain\DomainInterface;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\Service\DirectoryService;

/**
 * Virtual host service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Application
 */
class VirtualHostService extends AbstractService
{
    /**
     * Create a virtual host
     *
     * @param AccountInterface $account Account
     * @param DomainInterface $domain Domain
     * @param string $docroot Document root
     * @param string $type Virtual host Type
     * @return VhostInterface Virtual host
     */
    public function create(
        AccountInterface $account,
        DomainInterface $domain,
        $docroot = '',
        $type = Vhost::TYPE_APACHE
    ) {
        $vhost = $this->storageAdapterStrategy->createVhost(
            $account,
            $domain,
            $this->validateDocroot($account, $docroot),
            $type
        );
        $this->persistenceService->createVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Load a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function load(AccountInterface $account, $docroot = '')
    {
        return $this->storageAdapterStrategy->loadVhost($account, $this->validateDocroot($account, $docroot));
    }

    /**
     * Delete a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function delete(AccountInterface $account, $docroot = '')
    {
        $vhost = $this->storageAdapterStrategy->deleteVhost($account, $this->validateDocroot($account, $docroot));
        $this->persistenceService->deleteVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Enable a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function enable(AccountInterface $account, $docroot = '')
    {
        $vhost = $this->storageAdapterStrategy->enableVhost($account, $this->validateDocroot($account, $docroot));
        $this->persistenceService->enableVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Disable a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function disable(AccountInterface $account, $docroot = '')
    {
        $vhost = $this->storageAdapterStrategy->disableVhost($account, $this->validateDocroot($account, $docroot));
        $this->persistenceService->disableVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Redirect a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param string $url Redirect URL
     * @param int $status Redirect HTTP status
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the redirect URL is invalid
     * @throws \RuntimeException If the redirect HTTP status code is invalid
     */
    public function redirect(
        AccountInterface $account,
        $docroot = '',
        $url = '',
        $status = Vhost::REDIRECT_DEFAULT_STATUS
    ) {
        $url = trim($url) ?: null;

        // If the redirect URL is invalid
        if (($url !== null) &&
            (!filter_var($url, FILTER_VALIDATE_URL)
                || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME)), Vhost::$supportedProtocols))
        ) {
            throw new \RuntimeException(sprintf('Invalid redirect URL "%s"', $url), 1475486589);
        }

        // If the redirect HTTP status code is invalid
        if (!is_int($status) || (($status < 300) || ($status > 308))) {
            throw new \RuntimeException(sprintf('Invalid redirect HTTP status code "%s"', $status), 1475486679);
        }

        $vhost = $this->storageAdapterStrategy->redirectVhost(
            $account,
            $this->validateDocroot($account, $docroot),
            $url,
            $status
        );
        $this->persistenceService->redirectVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Configure the PHP version of a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param string|null $php PHP version
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the redirect URL is invalid
     * @throws \RuntimeException If the redirect HTTP status code is invalid
     */
    public function php(AccountInterface $account, $docroot = '', $php = null)
    {
        $php = trim($php) ?: null;

        // If the PHP version is invalid
        if (($php !== null) && (!preg_match("%^[5789]\.\d$%", $php) || (floatval($php) < 5.6))) {
            throw new \RuntimeException(sprintf('Invalid PHP version "%s"', $php), 1475937755);
        }

        // Validate the docroot
        $docroot = $this->validateDocroot($account, $docroot);
        $oldVhost = $this->load($account, $docroot);

        $vhost = $this->storageAdapterStrategy->phpVhost($account, $docroot, $php);
        $this->persistenceService->phpVhost($account, $vhost, $oldVhost->getPhp());
        return $vhost;
    }

    /**
     * Configure a protocol based port for a virtual host
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @param int $protocol Protocol
     * @param int|null $port Port
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the protocol is invalid
     * @throws \RuntimeException If the protocol port is invalid
     */
    public function port(
        AccountInterface $account,
        $docroot = '',
        $protocol = \Tollwerk\Admin\Domain\Vhost\Vhost::PROTOCOL_HTTP,
        $port = null
    ) {
        // If the protocol is unsupported
        if (empty(Vhost::$supportedProtocols[$protocol])) {
            throw new \RuntimeException(sprintf('Invalid protocol "%s"', $protocol), 1475484081);
        }

        // If the protocol port is invalid
        $port = ($port === null) ? Vhost::$defaultProtocolPorts[$protocol] : (intval($port) ?: null);
        if (($port !== null) && ($port <= 0)) {
            throw new \RuntimeException(sprintf('Invalid protocol port "%s"', $port), 1475502412);
        }

        $vhost = $this->storageAdapterStrategy->portVhost(
            $account,
            $this->validateDocroot($account, $docroot),
            $protocol,
            $port
        );
        $this->persistenceService->portVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Add a secondary domain to a virtual host
     *
     * @param string $account Account name
     * @param DomainInterface $domain Domain
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function addDomain(AccountInterface $account, DomainInterface $domain, $docroot = '')
    {
        $vhost = $this->storageAdapterStrategy->addVhostDomain(
            $account,
            $this->validateDocroot($account, $docroot),
            $domain
        );
        $this->persistenceService->domainVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Remove a secondary domain from a virtual host
     *
     * @param string $account Account name
     * @param DomainInterface $domain Domain
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     */
    public function removeDomain(AccountInterface $account, DomainInterface $domain, $docroot = '')
    {
        $vhost = $this->storageAdapterStrategy->removeVhostDomain(
            $account,
            $this->validateDocroot($account, $docroot),
            $domain
        );
        $this->persistenceService->domainVhost($account, $vhost);
        return $vhost;
    }

    /**
     * Validate a given document root path
     *
     * @param AccountInterface $account Account
     * @param string $docroot Document root
     * @return string Validated document root
     */
    protected function validateDocroot(AccountInterface $account, $docroot)
    {
        $accountDirectorySvc = new DirectoryService($account);
        return $accountDirectorySvc->getDataDirectory($docroot, false);
    }
}
