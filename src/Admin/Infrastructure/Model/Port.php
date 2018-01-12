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
 * Port model
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 * @Entity
 * @Table(name="port",uniqueConstraints={@UniqueConstraint(name="primary_idx", columns={"vhost_id", "port"})})
 */
class Port
{
    /**
     * Port ID
     *
     * @var integer
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * Virtual host this port is belonging to
     *
     * @var Vhost|null
     * @ManyToOne(targetEntity="Tollwerk\Admin\Infrastructure\Model\Vhost", inversedBy="ports")
     * @JoinColumn(name="vhost_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $vhost;
    /**
     * HTTP port
     *
     * @var int
     * @Column(type="integer", options={"unsigned":true})
     */
    protected $port;
    /**
     * Protocol
     *
     * @var int
     * @Column(type="integer", options={"unsigned":true})
     */
    protected $protocol;

    /**
     * Return the port ID
     *
     * @return int Port ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the port ID
     *
     * @param int $id Port ID
     * @return Port
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return the virtual host this port is belonging to
     *
     * @return Vhost Virtual host this port is belonging to
     */
    public function getVhost()
    {
        return $this->vhost;
    }

    /**
     * Set the virtual host this port is belonging to
     *
     * @param Vhost $vhost Virtual host this port is belonging to
     * @return Port Self reference
     */
    public function setVhost($vhost = null)
    {
        $this->vhost = $vhost;
        return $this;
    }

    /**
     * Return the HTTP port
     *
     * @return int HTTP port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the HTTP port
     *
     * @param int $port HTTP port
     * @return Port Self reference
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Return the protocol
     *
     * @return int Protocol
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set the protocol
     *
     * @param int $protocol Protocol
     * @return Port Self reference
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }
}
