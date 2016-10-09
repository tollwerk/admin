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

use Tollwerk\Admin\Application\Contract\PersistenceAdapterFactoryInterface;
use Tollwerk\Admin\Application\Contract\PersistenceServiceInterface;
use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\Persistence\AccountHelper;

/**
 * Persistence service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class PersistenceService implements PersistenceServiceInterface
{
    /**
     * Persistence adapter factory
     *
     * @var PersistenceAdapterFactoryInterface
     */
    protected $persistenceAdapterFactory;
    /**
     * Webservers scheduled for reload
     *
     * @var array
     */
    protected $reloadWebserver = [];
    /**
     * PHP-Restart flag
     *
     * @var bool
     */
    protected $reloadPhp = false;

    /**
     * Constructor
     *
     * @param PersistenceAdapterFactoryInterface $persistenceAdapterFactory Persistence adapter factory
     */
    public function __construct(PersistenceAdapterFactoryInterface $persistenceAdapterFactory)
    {
        $this->persistenceAdapterFactory = $persistenceAdapterFactory;
    }

    /**
     * Create an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function createAccount(AccountInterface $account)
    {
    }

    /**
     * Delete an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function deleteAccount(AccountInterface $account)
    {
        // Delete all virtual hosts
        /** @var VhostInterface $vhost */
        foreach ($account->getVhosts() as $vhost) {
            $this->deleteVhost($account, $vhost);
        }
    }

    /**
     * Enable an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function enableAccount(AccountInterface $account)
    {
        // Enable all virtual hosts
        /** @var VhostInterface $vhost */
        foreach ($account->getVhosts() as $vhost) {
            $this->enableVhost($account, $vhost);
        }
    }

    /**
     * Disable an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function disableAccount(AccountInterface $account)
    {
        // Disable all virtual hosts
        /** @var VhostInterface $vhost */
        foreach ($account->getVhosts() as $vhost) {
            $this->disableVhost($account, $vhost);
        }
    }

    /**
     * Rename an account
     *
     * @param AccountInterface $account Account
     * @return void
     */
    public function renameAccount(AccountInterface $account)
    {
        // Rewrite all virtual hosts
        // Reload Apache
    }

    /**
     * Create a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function createVhost(AccountInterface $account, VhostInterface $vhost)
    {
        $accountHelper = new AccountHelper($account);
        $availableVhost = $accountHelper->vhostDirectory($vhost, false);
        if (is_dir($availableVhost)) {
            throw new \RuntimeException(
                sprintf('Virtual host "%s" already exists', strval($vhost->getPrimaryDomain())),
                1476018313
            );
        }

        // Prepare the virtual host directory
        $this->prepareDirectory($availableVhost);

        // Persist the virtual host
        $this->persistVhost($account, $vhost);
    }

    /**
     * Delete a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function deleteVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // Disable the virtual host first
        $this->disableVhost($account, $vhost);

        $accountHelper = new AccountHelper($account);
        $availableVhost = $accountHelper->vhostDirectory($vhost, false);
        if (is_dir($availableVhost)) {
            $this->deleteDirectory($availableVhost);
        }
    }

    /**
     * Enable a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     * @throws \RuntimeException If the virtual host directory doesn't exist yet
     * @throws \RuntimeException If the virtual host is already enabled but cannot be renewed
     */
    public function enableVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // If both the account and the virtual host are active: Enable the virtual host
        if ($account->isActive() && $vhost->isActive()) {
            $accountHelper = new AccountHelper($account);
            $availableVhost = $accountHelper->vhostDirectory($vhost, false);

            // If the virtual host directory doesn't exist yet: Error
            if (!is_dir($availableVhost)) {
                throw new \RuntimeException(
                    sprintf('Virtual host "%s" isn\'t persisted yet', strval($vhost->getPrimaryDomain())),
                    1476015113
                );
            }

            $enabledVhost = $accountHelper->vhostDirectory($vhost, true);
            $this->prepareDirectory(dirname($enabledVhost));

            // If the virtual host is already enabled but cannot be renewed
            if (file_exists($enabledVhost) && !unlink($enabledVhost)) {
                throw new \RuntimeException(
                    sprintf(
                        'Virtual host "%s" is already enabled but cannot be renewed',
                        strval($vhost->getPrimaryDomain())
                    ),
                    1476016371
                );
            }

            $relativeEnabledVhost = '..'.substr($availableVhost, strlen($accountHelper->directory('config')));
            symlink($relativeEnabledVhost, $enabledVhost);

            // Reload apache
            $this->registerWebserverReload($vhost->getType());
        }
    }

    /**
     * Disable a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function disableVhost(AccountInterface $account, VhostInterface $vhost)
    {
        $accountHelper = new AccountHelper($account);
        $enabledVhost = $accountHelper->vhostDirectory($vhost, true);

        // If the virtual host cannot be disabled
        if (file_exists($enabledVhost) && !unlink($enabledVhost)) {
            throw new \RuntimeException(
                sprintf('Virtual host "%s" cannot be disabled', strval($vhost->getPrimaryDomain())),
                1476016872
            );
        }

        // Reload apache
        $this->registerWebserverReload($vhost->getType());
    }

    /**
     * Redirect a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function redirectVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // Re-persist the virtual host
        $this->persistVhost($account, $vhost);

        // Reload apache
        $this->registerWebserverReload($vhost->getType());
    }

    /**
     * Configure the PHP version of a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     * @throws \RuntimeException If the previous PHP configuration cannot be removed
     */
    public function phpVhost(AccountInterface $account, VhostInterface $vhost)
    {
        $accountHelper = new AccountHelper($account);
        $availableVhost = $accountHelper->vhostDirectory($vhost, false);

        // Find all existing PHP configurations
        foreach (glob($availableVhost.DIRECTORY_SEPARATOR.'{fpm-*.conf,*_fpm.include}', GLOB_BRACE) as $fpmConfig) {
            // If the previous PHP configuration cannot be removed
            if (!unlink($fpmConfig)) {
                throw new \RuntimeException(
                    sprintf('Cannot remove PHP configuration "%s"', basename($fpmConfig)),
                    1476019274
                );
            }
        }

        // Re-persist the virtual host
        $this->persistVhost($account, $vhost);

        // Reload apache
        $this->registerWebserverReload($vhost->getType());

        // Reload PHP
        $this->registerPhpReload();
    }

    /**
     * Configure a protocol based port for a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function portVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // Re-persist the virtual host
        $this->persistVhost($account, $vhost);

        // TODO: Issue SSL certificate if needed

        // Reload apache
        $this->registerWebserverReload($vhost->getType());
    }

    /**
     * Add a secondary domain to a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     * @return void
     */
    public function domainVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // Re-persist the virtual host
        $this->persistVhost($account, $vhost);

        // TODO: Re-issue SSL certificate in case of domain changes

        // Reload apache
        $this->registerWebserverReload($vhost->getType());
    }

    /**
     * Register a webserver reload
     *
     * @param string $type Webserver type
     */
    protected function registerWebserverReload($type)
    {
        $this->reloadWebserver[$type] = true;
    }

    /**
     * Flag PHP for reloading
     */
    protected function registerPhpReload()
    {
        $this->reloadPhp = true;
    }

    /**
     * Prepare a directory
     *
     * @param string $directory Directory
     * @return string Prepared directory
     */
    protected function prepareDirectory($directory)
    {
        if (!is_dir($directory) && !mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not prepare directory "%s"', $directory), 1476014854);
        }
        return $directory;
    }

    /**
     * Recursively delete a directory
     *
     * @param string $directory Directory
     * @throws \RuntimeException If a file or directory cannot be deleted
     */
    protected function deleteDirectory($directory)
    {
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach ($files as $file) {
            // Recursive call if it's a directory
            if (is_dir($directory.DIRECTORY_SEPARATOR.$file)) {
                $this->deleteDirectory($directory.DIRECTORY_SEPARATOR.$file);
                continue;
            }

            // Delete the file
            if (!unlink($directory.DIRECTORY_SEPARATOR.$file)) {
                throw new \RuntimeException(
                    sprintf('Could not delete file "%s"', $directory.DIRECTORY_SEPARATOR.$file),
                    1476017676
                );
            }
        }

        // Delete the directory
        if (!rmdir($directory)) {
            throw new \RuntimeException(sprintf('Could not delete directory "%s"', $directory), 1476017747);
        }
    }

    /**
     * Persist a virtual host
     *
     * @param AccountInterface $account Account
     * @param VhostInterface $vhost Virtual host
     */
    protected function persistVhost(AccountInterface $account, VhostInterface $vhost)
    {
        // Persist all virtual host files
        $this->persistenceAdapterFactory
            ->makeVhostPersistenceAdapterStrategy($vhost->getType())
            ->persist($account, $vhost);
    }
}