<?php

/**
 * tollwerk-admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2018 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Tollwerk\Admin\Domain\Domain;

/**
 * Domain
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Domain
 */
class Domain extends AbstractDomain
{
    /**
     * Root domain
     *
     * @var DomainInterface
     */
    protected $rootDomain;

    /**
     * Constructor
     *
     * @param string $name Domain name
     * @param DomainInterface $rootDomain Root domain
     * @throws \RuntimeException If the domain name is invalid
     */
    public function __construct($name, DomainInterface $rootDomain)
    {
        // If the domain name is invalid
        if (!strlen($name) || !preg_match('%^\*|(?:[a-z\d][a-z\d\d]*)$%', $name)) {
            throw new \RuntimeException(sprintf('Invalid domain name "%s"', $name), 1475352664);
        }

        $this->name = $name;
        $this->rootDomain = $rootDomain;
    }

    /**
     * Serialize as string
     *
     * @return string Domain
     */
    public function __toString()
    {
        return $this->name.'.'.$this->rootDomain;
    }
}
