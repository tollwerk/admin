<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
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

/**
 * Virtual host model
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 * @Entity
 * @Table(name="vhost")
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
     * Virtual host name
     *
     * @var string
     * @Column(length=64)
     */
    protected $name;
    /**
     * Account this domain is belonging to
     *
     * @var Account
     * @ManyToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Account", inversedBy="vhosts")
     */
    protected $account;
    /**
     * List of all associated domains
     *
     * @var Domain[]
     * @OneToMany(targetEntity="Tollwerk\Admin\Infrastructure\Model\Domain", mappedBy="account")
     */
    protected $domains;
    /**
     * Primary domain of this virtual host
     *
     * @var Domain
     * @OneToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Domain")
     */
    protected $primaryDomain;
    /**
     * Document root directory
     *
     * @var string
     * @Column(length=128)
     */
    protected $docroot;
    /**
     * Port
     *
     * @var int
     * @Column(type="integer", nullable=false, options={"unsigned":true, "default":1})
     */
    protected $port;
    /**
     * Supported PHP version
     *
     * @var float|null
     * @Column(type="decimal",precision=2,scale=1,nullable=true)
     */
    protected $php;
    /**
     * Supported protocols
     *
     * @var int
     * @Column(type="integer", nullable=false, options={"unsigned":true, "default":1})
     */
    protected $protocols;
    /**
     * Redirect URL
     *
     * @var string
     * @Column(length=128)
     */
    protected $redirectUrl;
    /**
     * Redirect status
     *
     * @var int
     * @Column(type="integer", nullable=false, options={"unsigned":true, "default":301})
     */
    protected $redirectStatus;

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
     * Return the vhost name
     *
     * @return string Vhost name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the vhost name
     *
     * @param string $name Vhost name
     * @return Vhost
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setDomains($domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * Return the primary domain of this virtual host
     *
     * @return Domain Primary domain
     */
    public function getPrimaryDomain()
    {
        return $this->primaryDomain;
    }

    /**
     * Set the primary domain of this virtual host
     *
     * @param Domain $primaryDomain Primary domain
     * @return Vhost Self reference
     */
    public function setPrimaryDomain($primaryDomain)
    {
        $this->primaryDomain = $primaryDomain;
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
     * Return the port
     *
     * @return int Port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the port
     *
     * @param int $port Port
     * @return Vhost Self reference
     */
    public function setPort($port)
    {
        $this->port = $port;
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
     */
    public function setProtocols($protocols)
    {
        $this->protocols = $protocols;
        return $this;
    }

    /**
     * Return the redirect URL
     *
     * @return string Redirect URL
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set the redirect URL
     *
     * @param string $redirectUrl Redirect URL
     * @return Vhost Self reference
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * Return the redirect status
     *
     * @return int Redirect status
     */
    public function getRedirectStatus()
    {
        return $this->redirectStatus;
    }

    /**
     * Set the redirect status
     *
     * @param int $redirectStatus Redirect status
     * @return Vhost Self reference
     */
    public function setRedirectStatus($redirectStatus)
    {
        $this->redirectStatus = $redirectStatus;
        return $this;
    }
}
