<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Strategy
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

namespace Tollwerk\Admin\Infrastructure\Strategy;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Domain\Factory\DomainFactory;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Model\Account as DoctrineAccount;
use Tollwerk\Admin\Infrastructure\Model\Domain as DoctrineDomain;
use Tollwerk\Admin\Infrastructure\Model\Vhost as DoctrineVhost;

/**
 * DoctrineAccountFactoryStrategy
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Infrastructure\Strategy
 */
class DoctrineStorageAdapterStrategy implements StorageAdapterStrategyInterface
{
    /**
     * Doctrine entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * Account repository
     *
     * @var EntityRepository
     */
    protected $accountRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entityManager = App::getEntityManager();
        $this->accountRepository =
            $this->entityManager->getRepository(\Tollwerk\Admin\Infrastructure\Model\Account::class);
    }

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return Account Account
     * @throws \RuntimeException If the account cannot be created
     */
    public function createAccount($name)
    {
        // Create the new account
        $doctrineAccount = new DoctrineAccount();
        $doctrineAccount->setName($name);
        $doctrineAccount->setActive(false);

        // Persist the new account
        try {
            $this->entityManager->persist($doctrineAccount);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $doctrineAccount = $this->accountRepository->findOneBy(['name' => $name]);

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        // Create and return a domain account
        $account = new Account($doctrineAccount->getName());
        $account->setActive($doctrineAccount->getActive());
        return $account;
    }

    /**
     * Enable an account
     *
     * @param string $name Account name
     * @return Account Account
     * @throws \RuntimeException If the account cannot be enabled
     */
    public function enableAccount($name)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $name]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $name), 1475495500);
        }

        // Enable and persist the account
        try {
            $doctrineAccount->setActive(true);
            $this->entityManager->persist($doctrineAccount);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return Account Account
     * @throws \RuntimeException If the account cannot be disabled
     */
    public function disableAccount($name)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $name]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $name), 1475495500);
        }

        // Enable and persist the account
        try {
            $doctrineAccount->setActive(false);
            $this->entityManager->persist($doctrineAccount);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return Account Account
     * @throws \RuntimeException If the account cannot be deleted
     */
    public function deleteAccount($name)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $name]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $name), 1475495500);
        }

        // Enable and persist the account
        try {
            $this->entityManager->remove($doctrineAccount);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        // Create and return a domain account stub
        $account = new Account($doctrineAccount->getName());
        $account->setActive($doctrineAccount->getActive());
        return $account;
    }

    /**
     * Rename and return an account
     *
     * @param string $oldname Old account name
     * @param string $newname New account name
     * @return Account Account
     * @throws \RuntimeException If the account is unknown
     */
    public function renameAccount($oldname, $newname)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $oldname]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $oldname), 1475495500);
        }

        // Rename and persist the account
        try {
            $doctrineAccount->setName($newname);
            $this->entityManager->persist($doctrineAccount);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode());
        }

        // Load and return the renamed account
        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Instantiate an account
     *
     * @param string $name Account name
     * @return Account Account
     * @throws \RuntimeException If the account is unknown
     */
    public function loadAccount($name)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $name]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $name), 1475495500);
        }

        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Create domain account from doctrine account
     *
     * @param DoctrineAccount $doctrineAccount Doctrine account
     * @return Account Domain account
     */
    protected function loadFromDoctrineAccount(DoctrineAccount $doctrineAccount)
    {
        $account = new Account($doctrineAccount->getName());

        // Run through all virtual hosts of this account
        /** @var DoctrineVhost $doctrineVhost */
        foreach ($doctrineAccount->getVhosts() as $doctrineVhost) {
            $doctrinePrimaryDomain = $doctrineVhost->getPrimarydomain();
            if ($doctrinePrimaryDomain instanceof DoctrineDomain) {
                $primaryDomain = DomainFactory::parseString($doctrinePrimaryDomain->getName());

                $vhost = new Vhost($primaryDomain, $doctrineVhost->getDocroot(), $doctrineVhost->getType());
                $vhost->setPhp($doctrineVhost->getPhp());
                $vhost->setRedirectUrl($doctrineVhost->getRedirecturl());
                $vhost->setRedirectStatus($doctrineVhost->getRedirectstatus());
                if ($doctrineVhost->getHttpport() !== null) {
                    $vhost->enableProtocol(Vhost::PROTOCOL_HTTP, $doctrineVhost->getHttpport());
                }
                if ($doctrineVhost->getHttpsport() !== null) {
                    $vhost->enableProtocol(Vhost::PROTOCOL_HTTPS, $doctrineVhost->getHttpsport());
                }

                // Add the wilcard version of the primary domain
                if ($doctrinePrimaryDomain->isWildcard()) {
                    $vhost->addSecondaryDomain(DomainFactory::parseString('*.'.$primaryDomain));
                }

                // Add all secondary domains
                /** @var DoctrineDomain $doctrineDomain */
                foreach ($doctrineVhost->getDomains() as $doctrineDomain) {
                    if ($doctrineDomain->getName() != $doctrinePrimaryDomain->getName()) {
                        $vhost->addSecondaryDomain(DomainFactory::parseString($doctrineDomain->getName()));

                        if ($doctrineDomain->isWildcard()) {
                            $vhost->addSecondaryDomain(DomainFactory::parseString('*.'.$doctrineDomain->getName()));
                        }
                    }
                }

                $account->addVirtualHost($vhost);
            } else {
                trigger_error('Skipping virtual host due to missing primary domain', E_USER_NOTICE);
            }
        }

        return $account;
    }
}
