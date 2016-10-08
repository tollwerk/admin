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

/**
 * Domain model
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 * @Entity
 * @Table(name="domain",uniqueConstraints={@UniqueConstraint(name="primary_idx", columns={"vhost_id", "primarydomain"})})
 */
class Domain
{
    /**
     * Domain ID
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
     * Domain name
     *
     * @var string
     * @Column(length=128,unique=true)
     */
    protected $name;
    /**
     * Account this domain is belonging to
     *
     * @var Account
     * @ManyToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Account", inversedBy="domains")
     * @JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $account;
    /**
     * Virtual host this domain is belonging to
     *
     * @var Vhost|null
     * @ManyToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Vhost", inversedBy="domains")
     * @JoinColumn(name="vhost_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $vhost;
    /**
     * Primary domain
     *
     * @var boolean
     * @Column(type="boolean", nullable=true)
     */
    protected $primarydomain;
    /**
     * Add the wildcard subdomain
     *
     * @var boolean
     * @Column(type="boolean")
     */
    protected $wildcard;

    /**
     * Return the domain ID
     *
     * @return int Domain ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the domain ID
     *
     * @param int $id Domain ID
     * @return Domain
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return the domain name
     *
     * @return string Domain name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the domain name
     *
     * @param string $name Domain name
     * @return Domain
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set whether this is an active domain
     *
     * @param boolean $active Active domain
     * @return Domain
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Return whether the domain is ative
     *
     * @return boolean Active domain
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Return the account this domain is belonging to
     *
     * @return Account Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the account this domain is belonging tp
     *
     * @param Account $account Account
     * @return Domain Self reference
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Return the virtual host this domain is belonging to
     *
     * @return Vhost Virtual host this domain is belonging to
     */
    public function getVhost()
    {
        return $this->vhost;
    }

    /**
     * Set the virtual host this domain is belonging to
     *
     * @param Vhost $vhost Virtual host this domain is belonging to
     * @return Domain Self reference
     */
    public function setVhost($vhost = null)
    {
        $this->vhost = $vhost;
        return $this;
    }

    /**
     * Return whether the wildcard subdomain option is enabled
     *
     * @return boolean Wildcard subdomain enabled
     */
    public function isWildcard()
    {
        return $this->wildcard;
    }

    /**
     * Set whether the wildcard subdomain option is enabled
     *
     * @param boolean $wildcard Enable the wildcard subdomain
     * @return Domain Self reference
     */
    public function setWildcard($wildcard)
    {
        $this->wildcard = $wildcard;
        return $this;
    }

    /**
     * Return whether this is the primary domain of the associated virtual host
     *
     * @return boolean Primary domain
     */
    public function isPrimarydomain()
    {
        return $this->primarydomain;
    }

    /**
     * Set whether this is the primary domain of the associated virtual host
     *
     * @param boolean $primarydomain Primary domain
     * @return Domain Self reference
     */
    public function setPrimarydomain($primarydomain)
    {
        $this->primarydomain = $primarydomain ?: null;
        return $this;
    }
}
