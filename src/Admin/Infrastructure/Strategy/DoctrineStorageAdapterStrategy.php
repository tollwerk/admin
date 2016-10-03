<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Tollwerk\Admin\Application\Contract\StorageAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Domain\Factory\DomainFactory;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Infrastructure\Model\Vhost as DoctrineVhost;
use Tollwerk\Admin\Infrastructure\Model\Domain as DoctrineDomain;
use Tollwerk\Admin\Ports\App;

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

        $account = new Account($name);

        // Run through all virtual hosts of this account
        /** @var DoctrineVhost $doctrineVhost */
        foreach ($doctrineAccount->getVhosts() as $doctrineVhost) {
            $doctrinePrimaryDomain = $doctrineVhost->getPrimarydomain();
            if ($doctrinePrimaryDomain instanceof DoctrineDomain) {
                $primaryDomain = DomainFactory::parseString($doctrinePrimaryDomain->getName());

                $vhost = new Vhost($primaryDomain, $doctrineVhost->getDocroot());
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
