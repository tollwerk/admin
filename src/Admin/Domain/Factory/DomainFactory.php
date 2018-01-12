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

namespace Tollwerk\Admin\Domain\Factory;

use Tollwerk\Admin\Domain\Domain\Domain;
use Tollwerk\Admin\Domain\Domain\DomainInterface;
use Tollwerk\Admin\Domain\Domain\TopLevelDomain;

/**
 * Domain factory
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Domain
 */
class DomainFactory
{
    /**
     * Parse a domain string
     *
     * @param string $domainStr Domain strinng
     * @return DomainInterface Domain
     */
    public static function parseString($domainStr)
    {
        $domains = array_filter(array_map('trim', explode('.', $domainStr)));

        // If the domain string is not valid
        if (!count($domains)) {
            throw new \RuntimeException(sprintf('Invalid domain string "%s"', $domainStr), 1475351791);
        }

        $domain = new TopLevelDomain(array_pop($domains));
        while (count($domains)) {
            $domain = new Domain(array_pop($domains), $domain);
        }

        return $domain;
    }
}
