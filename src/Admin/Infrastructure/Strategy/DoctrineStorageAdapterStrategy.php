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
use Tollwerk\Admin\Domain\Account\AccountInterface;
use Tollwerk\Admin\Domain\Domain\DomainInterface;
use Tollwerk\Admin\Domain\Factory\DomainFactory;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Model\Account as DoctrineAccount;
use Tollwerk\Admin\Infrastructure\Model\Domain as DoctrineDomain;
use Tollwerk\Admin\Infrastructure\Model\Vhost as DoctrineVhost;

/**
 * DoctrineAccountFactoryStrategy
 *
 * @package Tollwerk\Admin
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
     * Domain repository
     *
     * @var EntityRepository
     */
    protected $domainRepository;
    /**
     * Virtual host repository
     *
     * @var EntityRepository
     */
    protected $vhostRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->entityManager = App::getEntityManager();
        $this->accountRepository =
            $this->entityManager->getRepository(\Tollwerk\Admin\Infrastructure\Model\Account::class);
        $this->domainRepository =
            $this->entityManager->getRepository(\Tollwerk\Admin\Infrastructure\Model\Domain::class);
        $this->vhostRepository =
            $this->entityManager->getRepository(\Tollwerk\Admin\Infrastructure\Model\Vhost::class);
    }

    /**
     * Create an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
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
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
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
     * @return AccountInterface Account
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
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Disable an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
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
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Delete an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
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
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
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
     * @return AccountInterface Account
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
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Load and return the renamed account
        return $this->loadFromDoctrineAccount($doctrineAccount);
    }

    /**
     * Instantiate an account
     *
     * @param string $name Account name
     * @return AccountInterface Account
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
     * @return AccountInterface Domain account
     */
    protected function loadFromDoctrineAccount(DoctrineAccount $doctrineAccount)
    {
        $account = new Account($doctrineAccount->getName());
        $account->setActive($doctrineAccount->isActive());

        // Run through all virtual hosts of this account
        /** @var DoctrineVhost $doctrineVhost */
        foreach ($doctrineAccount->getVhosts() as $doctrineVhost) {
            $doctrinePrimaryDomain = $doctrineVhost->getPrimarydomain();
            if ($doctrinePrimaryDomain instanceof DoctrineDomain) {
                $account->addVirtualHost($this->loadFromDoctrineVhost($doctrineVhost));
            } else {
                trigger_error('Skipping virtual host due to missing primary domain', E_USER_NOTICE);
            }
        }

        return $account;
    }

    /**
     * Load a domain (optionally: unassigned)
     *
     * @param string $name Domain name
     * @param AccountInterface $account Optional: Account the domain must belong to
     * @param string $vhostDocroot Optional: Document root of the virtual host the domain must be assigned to (otherwise: unassigned)
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain is unknown
     * @throws \RuntimeException If the domain belongs to another account
     * @throws \RuntimeException If the domain is assigned to a virtual host but should be unassigned
     * @throws \RuntimeException If the domain is assigned to a different virtual host
     */
    public function loadDomain($name, AccountInterface $account = null, $vhostDocroot = null)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475915909);
        }

        // If only an assigned or unassigned account domain should be returned
        if ($account instanceof Account) {
            // If the domain belongs to another account
            if ($doctrineDomain->getAccount()->getName() != $account->getName()) {
                throw new \RuntimeException(
                    sprintf(
                        'Domain "%s" belongs to another account ("%s")',
                        $name,
                        $doctrineDomain->getAccount()->getName()
                    ),
                    1475917184
                );
            }

            // Determine the virtual host the domain is assigned to
            $doctrineVhost = $doctrineDomain->getVhost();

            // If the domain is already assigned to another virtual host
            if ($doctrineVhost instanceof DoctrineVhost) {
                // If only an uassigned domain should be returned
                if ($vhostDocroot === null) {
                    throw new \RuntimeException(
                        sprintf('Domain "%s" is already assigned to a virtual host', $name),
                        1475917298
                    );
                }

                // If the document route doesn't match the requested one
                if ($doctrineVhost->getDocroot() !== $vhostDocroot) {
                    throw new \RuntimeException(
                        sprintf('Domain "%s" is assigned to another virtual host', $name),
                        1475942759
                    );
                }
            }
        }

        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Create a domain
     *
     * @param string $name Domain name
     * @param Account $account Account the domain belongs to
     * @return DomainInterface Domain
     * @throws \RuntimeException If the account is unknown
     */
    public function createDomain($name, AccountInterface $account)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $name), 1475495500);
        }

        // Create the new domain
        $doctrineDomain = new DoctrineDomain();
        $doctrineDomain->setAccount($doctrineAccount);
        $doctrineDomain->setActive(false);
        $doctrineDomain->setName($name);
        $doctrineDomain->setWildcard(false);
        $doctrineDomain->setPrimarydomain(false);

        // Persist the new domain
        try {
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Delete a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be deleted
     */
    public function deleteDomain($name)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475921539);
        }

        // If the domain is assigned to a virtual host
        if ($doctrineDomain->getVhost() instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(
                sprintf('Domain "%s" is assigned to a virtual host, please release the assignment first', $name),
                1475921740
            );
        }

        // Remove and persist the domain
        try {
            $this->entityManager->remove($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain stub
        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Enable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be enabled
     */
    public function enableDomain($name)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475921539);
        }

        // Enable and persist the account
        try {
            $doctrineDomain->setActive(true);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Disable a domain
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be disabled
     */
    public function disableDomain($name)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475921539);
        }

        // Enable and persist the account
        try {
            $doctrineDomain->setActive(false);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Enable a domain wildcard
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be enabled
     */
    public function enableDomainWildcard($name)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475921539);
        }

        // Enable and persist the account
        try {
            $doctrineDomain->setWildcard(true);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Disable a domain wildcard
     *
     * @param string $name Domain name
     * @return DomainInterface Domain
     * @throws \RuntimeException If the domain cannot be disabled
     */
    public function disableDomainWildcard($name)
    {
        $doctrineDomain = $this->domainRepository->findOneBy(['name' => $name]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', $name), 1475921539);
        }

        // Enable and persist the account
        try {
            $doctrineDomain->setWildcard(false);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        return $this->loadFromDoctrineDomain($doctrineDomain);
    }

    /**
     * Create domain domain from doctrine domain
     *
     * @param DoctrineDomain $doctrineDomain Doctrine domain
     * @return DomainInterface Domain domain
     */
    protected function loadFromDoctrineDomain(DoctrineDomain $doctrineDomain)
    {
        return DomainFactory::parseString($doctrineDomain->getName());
    }

    /**
     * Create a virtual host
     *
     * @param AccountInterface $account Account name
     * @param DomainInterface $domain Domain
     * @param string $docroot Document root
     * @param string $type Virtual host Type
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function createVhost(AccountInterface $account, DomainInterface $domain, $docroot, $type)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineDomain = $this->domainRepository->findOneBy(['name' => strval($domain)]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', strval($domain)), 1475921539);
        }

        // Create the new domain
        $doctrineVhost = new DoctrineVhost();
        $doctrineVhost->setAccount($doctrineAccount);
        $doctrineVhost->setActive(false);
        $doctrineVhost->setType($type);
        $doctrineVhost->setDocroot($docroot);

        // Persist the new virtual host
        try {
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $doctrineVhost = $this->vhostRepository->findOneBy(['docroot' => $docroot]);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Set the primary domain
        $doctrineDomain->setVhost($doctrineVhost);
        $doctrineDomain->setPrimarydomain(true);

        // Persist the domain
        try {
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Delete a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function deleteVhost(AccountInterface $account, $docroot)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Remove and persist the virtual host
        try {
            $this->entityManager->remove($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Enable a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function enableVhost(AccountInterface $account, $docroot)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Update and persist the virtual host
        try {
            $doctrineVhost->setActive(true);
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Disable a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function disableVhost(AccountInterface $account, $docroot)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Update and persist the virtual host
        try {
            $doctrineVhost->setActive(false);
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Redirect a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @param string|null $url Redirect URL
     * @param int $status Redirect HTTP status
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function redirectVhost(AccountInterface $account, $docroot, $url, $status)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Update and persist the virtual host
        try {
            $doctrineVhost->setRedirecturl($url);
            $doctrineVhost->setRedirectstatus($status);
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Configure the PHP version of a virtual host
     *
     * @param AccountInterface $account Account name
     * @param string $docroot Document root
     * @param string|null $php PHP version
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function phpVhost(AccountInterface $account, $docroot, $php)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Update and persist the virtual host
        try {
            $doctrineVhost->setPhp($php);
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Configure a protocol based port for a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param int $protocol Protocol
     * @param int $port Port
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     */
    public function portVhost(AccountInterface $account, $docroot, $protocol, $port)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        // Update and persist the virtual host
        try {
            $doctrineVhost->{'set'.ucfirst(Vhost::$supportedProtocols[$protocol]).'port'}($port);
            $this->entityManager->persist($doctrineVhost);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Add a secondary domain to a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param DomainInterface $domain Domain
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function addVhostDomain(AccountInterface $account, $docroot, DomainInterface $domain)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        $doctrineDomain = $this->domainRepository->findOneBy(['name' => strval($domain)]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', strval($domain)), 1475921539);
        }

        // If the domain is already assigned to a virtual host
        $doctrineDomainVhost = $doctrineDomain->getVhost();
        if (($doctrineDomainVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost)
            && ($doctrineDomainVhost->getId() != $doctrineVhost->getId())
        ) {
            throw new \RuntimeException(
                sprintf('Domain "%s" is already assigned to a virtual host', strval($domain)),
                1475917298
            );
        }

        // Update and persist the virtual host
        try {
            $doctrineDomain->setVhost($doctrineVhost);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Remove a secondary domain from a virtual host
     *
     * @param string $account Account name
     * @param string $docroot Document root
     * @param DomainInterface $domain Domain
     * @return VhostInterface Virtual host
     * @throws \RuntimeException If the account is unknown
     * @throws \RuntimeException If the virtual host is unknown
     * @throws \RuntimeException If the domain is unknown
     */
    public function removeVhostDomain(AccountInterface $account, $docroot, DomainInterface $domain)
    {
        $doctrineAccount = $this->accountRepository->findOneBy(['name' => $account->getName()]);

        // If the account is unknown
        if (!$doctrineAccount instanceof \Tollwerk\Admin\Infrastructure\Model\Account) {
            throw new \RuntimeException(sprintf('Unknown account "%s"', $account->getName()), 1475495500);
        }

        $doctrineVhost = $this->vhostRepository->findOneBy(['account' => $doctrineAccount, 'docroot' => $docroot]);

        // If the virtual host is unknown
        if (!$doctrineVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost) {
            throw new \RuntimeException(sprintf('Unknown virtual host "%s"', $docroot), 1475933194);
        }

        $doctrineDomain = $this->domainRepository->findOneBy(['name' => strval($domain)]);

        // If the domain is unknown
        if (!$doctrineDomain instanceof \Tollwerk\Admin\Infrastructure\Model\Domain) {
            throw new \RuntimeException(sprintf('Unknown domain "%s"', strval($domain)), 1475921539);
        }

        // If the domain is not assigned to the current virtual host
        $doctrineDomainVhost = $doctrineDomain->getVhost();
        if (!($doctrineDomainVhost instanceof \Tollwerk\Admin\Infrastructure\Model\Vhost)
            || ($doctrineDomainVhost->getId() != $doctrineVhost->getId())
        ) {
            throw new \RuntimeException(
                sprintf('Domain "%s" is not assigned to this virtual host', strval($domain)),
                1475942759
            );
        }

        // Update and persist the virtual host
        try {
            $doctrineDomain->setVhost(null);
            $this->entityManager->persist($doctrineDomain);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode() || 1475925451);
        }

        // Create and return a domain domain
        return $this->loadFromDoctrineVhost($doctrineVhost);
    }

    /**
     * Create virtual host from a doctrine virtual host
     *
     * @param DoctrineVhost $doctrineVhost Doctrine virtual host
     * @return VhostInterface Virtual host
     */
    protected function loadFromDoctrineVhost(DoctrineVhost $doctrineVhost)
    {
        $doctrinePrimaryDomain = $doctrineVhost->getPrimarydomain();
        $primaryDomain = $this->loadFromDoctrineDomain($doctrinePrimaryDomain);

        $vhost = new Vhost($primaryDomain, $doctrineVhost->getDocroot(), $doctrineVhost->getType());
        $vhost->setActive($doctrineVhost->isActive());
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

        return $vhost;
    }
}
