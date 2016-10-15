<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Model
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

namespace Tollwerk\Admin\Infrastructure\Model;

use Tollwerk\Admin\Infrastructure\App;

/**
 * Virtual host model
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="vhost",uniqueConstraints={@UniqueConstraint(name="docroot_idx", columns={"account_id", "docroot"})})
 */
class Vhost
{
    /**
     * Virtual host ID
     *
     * @var integer
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * Active
     *
     * @var boolean
     * @Column(type="boolean")
     */
    protected $active;
    /**
     * Account this domain is belonging to
     *
     * @var Account
     * @ManyToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Account", inversedBy="vhosts")
     * @JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $account;
    /**
     * Virtual host type
     *
     * @var string
     * @Column(type="enumvhosttype")
     */
    protected $type;
    /**
     * Primary domain of this virtual host
     *
     * @var Domain
     */
    protected $primarydomain;
    /**
     * List of all associated domains
     *
     * @var Domain[]
     * @OneToMany(targetEntity="Tollwerk\Admin\Infrastructure\Model\Domain", mappedBy="vhost")
     */
    protected $domains = [];
    /**
     * Document root directory
     *
     * @var string
     * @Column(length=128,unique=true)
     */
    protected $docroot;
    /**
     * HTTP Port
     *
     * @var int|null
     * @Column(type="integer", nullable=true, options={"unsigned":true, "default":80})
     */
    protected $httpport = \Tollwerk\Admin\Domain\Vhost\Vhost::PORT_HTTP_DEFAULT;
    /**
     * HTTPS Port
     *
     * @var int|null
     * @Column(type="integer", nullable=true, options={"unsigned":true})
     */
    protected $httpsport;
    /**
     * Supported PHP version
     *
     * @var float|null
     * @Column(type="decimal",precision=2,scale=1,nullable=true)
     */
    protected $php = null;
    /**
     * Redirect URL
     *
     * @var string
     * @Column(length=128, nullable=true)
     */
    protected $redirecturl = null;
    /**
     * Redirect status
     *
     * @var int
     * @Column(type="integer", nullable=false, options={"unsigned":true, "default":301})
     */
    protected $redirectstatus = \Tollwerk\Admin\Domain\Vhost\Vhost::REDIRECT_DEFAULT_STATUS;

    /**
     * Return the vhost ID
     *
     * @return int Vhost ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the vhost ID
     *
     * @param int $id Vhost ID
     * @return Vhost
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set whether this is an active vhost
     *
     * @param boolean $active Active vhost
     * @return Vhost
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Return whether the vhost is ative
     *
     * @return boolean Active vhost
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Return the account this virtual host is belonging to
     *
     * @return Account Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the account this virtual host is belonging tp
     *
     * @param Account $account Account
     * @return Vhost Self reference
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
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
     * Set the virtual host type
     *
     * @param string $type Virtual host type
     * @return Vhost Self reference
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return the associated domains
     *
     * @return Domain[] Associated domains
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Set the associated domains
     *
     * @param Domain[] $domains Associated domains
     * @return Vhost Self reference
     */
    public function setDomains(array $domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * Return the primary domain of this virtual host
     *
     * @return Domain Primary domain
     */
    public function getPrimarydomain()
    {
        if ($this->primarydomain === null) {
            $entityManager = App::getEntityManager();
            $domainRepository = $entityManager->getRepository(Domain::class);
            $this->primarydomain = $domainRepository->findOneBy(['vhost' => $this->getId(), 'primarydomain' => 1]);
        }

        return $this->primarydomain;
    }

    /**
     * Set the primary domain of this virtual host
     *
     * @param Domain $primarydomain Primary domain
     * @return Vhost Self reference
     */
    public function setPrimarydomain($primarydomain)
    {
        $this->primarydomain = $primarydomain;
        return $this;
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
     * Set the document root
     *
     * @param string $docroot Document root
     * @return Vhost Self reference
     */
    public function setDocroot($docroot)
    {
        $this->docroot = $docroot;
        return $this;
    }

    /**
     * Return the HTTP port
     *
     * @return int|null HTTP port
     */
    public function getHttpport()
    {
        return $this->httpport;
    }

    /**
     * Set the HTTP port
     *
     * @param int|null $httpport HTTP port
     * @return Vhost Self reference
     */
    public function setHttpport($httpport)
    {
        $this->httpport = $httpport;
        return $this;
    }

    /**
     * Return the HTTPS port
     *
     * @return int|null HTTPS port
     */
    public function getHttpsport()
    {
        return $this->httpsport;
    }

    /**
     * Set the HTTPS port
     *
     * @param int|null $httpsport HTTPS port
     * @return Vhost
     */
    public function setHttpsport($httpsport)
    {
        $this->httpsport = $httpsport;
        return $this;
    }

    /**
     * Return the supported PHP version
     *
     * @return float|null Supported PHP version
     */
    public function getPhp()
    {
        return $this->php;
    }

    /**
     * Set the supported PHP version
     *
     * @param float|null $php Supported PHP version
     * @return Vhost Self reference
     */
    public function setPhp($php)
    {
        $this->php = $php;
        return $this;
    }

    /**
     * Return the redirect URL
     *
     * @return string Redirect URL
     */
    public function getRedirecturl()
    {
        return $this->redirecturl;
    }

    /**
     * Set the redirect URL
     *
     * @param string $redirecturl Redirect URL
     * @return Vhost Self reference
     */
    public function setRedirecturl($redirecturl)
    {
        $this->redirecturl = $redirecturl;
        return $this;
    }

    /**
     * Return the redirect status
     *
     * @return int Redirect status
     */
    public function getRedirectstatus()
    {
        return $this->redirectstatus;
    }

    /**
     * Set the redirect status
     *
     * @param int $redirectstatus Redirect status
     * @return Vhost Self reference
     */
    public function setRedirectstatus($redirectstatus)
    {
        $this->redirectstatus = $redirectstatus;
        return $this;
    }

    /**
     * Release the primary domain on deletion of this virtual host
     *
     * @PreRemove
     */
    public function releasePrimaryDomain() {
        $this->getPrimarydomain()->setPrimarydomain(false);
        App::getEntityManager()->persist($this->getPrimarydomain());
        App::getEntityManager()->flush();
    }
}
