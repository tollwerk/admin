<?php

/**
 * tollwerk-admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Server\<Layer>
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Tollwerk\Admin\Domain\Vhost;

/**
 * Virtual host interface
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Domain
 */
interface VhostInterface
{
    /**
     * Virtual host constructor
     *
     * @param int $port Port
     * @param VhostCoreInterface $core Virtual host core
     */
    public function __construct($port, VhostCoreInterface $core);

    /**
     * Return the virtual host core
     *
     * @return VhostCoreInterface Virtual host core
     */
    public function getCore();

    /**
     * Return the port
     *
     * @return int Port
     */
    public function getPort();

    /**
     * Return the active SSL account
     *
     * @return null|string SSL account
     */
    public function getSsl();

    /**
     * Set the active SSL account
     *
     * @param null|string $ssl SSL account
     * @return Vhost Self reference
     */
    public function setSsl($ssl);

    /**
     * Get the active PHP version
     *
     * @return null|string Active PHP version
     */
    public function getPhp();

    /**
     * Set the active PHP version
     *
     * @param null|string $php Active PHP version
     * @return Vhost Self reference
     */
    public function setPhp($php);
}
