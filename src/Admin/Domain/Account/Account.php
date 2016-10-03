<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Domain\Account
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

namespace Tollwerk\Admin\Domain\Account;

use Tollwerk\Admin\Domain\Vhost\VhostInterface;

/**
 * Account
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Domain
 */
class Account implements AccountInterface
{
    /**
     * Account name
     *
     * @var string
     */
    protected $name;
    /**
     * Virtual hosts
     *
     * @var VhostInterface[]
     */
    protected $vhosts = [];

    /**
     * Account constructor
     *
     * @param string $name Account name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
     * Return the virtual hosts
     *
     * @return VhostInterface[] Virtual hosts
     */
    public function getVhosts()
    {
        return array_values($this->vhosts);
    }

    /**
     * Set the virtual hosts
     *
     * @param VhostInterface[] $vhosts Virtual hosts
     * @return Account Self reference
     */
    public function setVhosts(array $vhosts)
    {
        // Run through all virtual hosts
        foreach ($vhosts as $vhost) {
            if (!($vhost instanceof VhostInterface)) {
                throw new \RuntimeException('Invalid virtual host', 1475488477);
            }

            $this->vhosts[strval($vhost->getPrimaryDomain())] = $vhost;
        }
        return $this;
    }

    /**
     * Add a virtual host
     *
     * @param VhostInterface $vhost Virtual host
     * @return Account Self reference
     */
    public function addVirtualHost(VhostInterface $vhost)
    {
        $this->vhosts[strval($vhost->getPrimaryDomain())] = $vhost;
        return $this;
    }

    /**
     * Remove a virtual host
     *
     * @param VhostInterface $vhost Virtual host
     * @return Account Self reference
     */
    public function removeVirtualHost(VhostInterface $vhost)
    {
        unset($this->vhosts[strval($vhost->getPrimaryDomain())]);
        return $this;
    }
}
