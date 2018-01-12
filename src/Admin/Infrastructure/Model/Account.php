<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Model
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

namespace Tollwerk\Admin\Infrastructure\Model;

/**
 * Account model
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 * @Entity
 * @Table(name="account")
 */
class Account
{
    /**
     * Account ID
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
     * Account name
     *
     * @var string
     * @Column(length=64,unique=true)
     */
    protected $name;
    /**
     * List of all associated domains
     *
     * @var Domain[]
     * @OneToMany(targetEntity="Tollwerk\Admin\Infrastructure\Model\Domain", mappedBy="account")
     */
    protected $domains;
    /**
     * List of all associated virtual hosts
     *
     * @var Vhost[]
     * @OneToMany(targetEntity="Tollwerk\Admin\Infrastructure\Model\Vhost", mappedBy="account")
     */
    protected $vhosts;

    /**
     * Return the account ID
     *
     * @return int Account ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the account ID
     *
     * @param int $id Account ID
     * @return Account
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return the account name
     *
     * @return string Account name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the account name
     *
     * @param string $name Account name
     * @return Account
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return whether this account is active
     *
     * @return bool Active account
     */
    public function getActive() {
       return $this->active;
    }

    /**
     * Set whether this is an active account
     *
     * @param boolean $active Active account
     * @return Account
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Return whether the account is ative
     *
     * @return boolean Active account
     */
    public function isActive()
    {
        return $this->active;
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
     * @return Account Self reference
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * Return the associated virtual hosts
     *
     * @return Vhost[] Associated virtual hosts
     */
    public function getVhosts()
    {
        return $this->vhosts;
    }

    /**
     * Set the associated virtual hosts
     *
     * @param Vhost[] $vhosts Associated virtual hosts
     * @return Account Self reference
     */
    public function setVhosts($vhosts)
    {
        $this->domains = $vhosts;
        return $this;
    }
}
