<?php

/**
 * tollwerk-admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Tests
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

namespace Tollwerk\Admin\Tests;

use PHPUnit\Framework\TestCase;
use Tollwerk\Admin\Domain\Domain\Domain;
use Tollwerk\Admin\Domain\Domain\TopLevelDomain;
use Tollwerk\Admin\Domain\Factory\DomainFactory;

/**
 * Domain tests
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Tests
 */
class DomainTests extends TestCase
{
    /**
     * Test a top-level-domain
     */
    public function testTopLevelDomain()
    {
        $name = 'com';
        $tld = DomainFactory::parseString($name);
        $this->assertInstanceOf(TopLevelDomain::class, $tld);
        $this->assertEquals($name, strval($tld));
    }

    /**
     * Test an empty top-level-domain
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475351791
     */
    public function testEmptyTopLevelDomain()
    {
        DomainFactory::parseString('');
    }

    /**
     * Test an invalid top-level-domain
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475352515
     */
    public function testInvalidTopLevelDomain()
    {
        DomainFactory::parseString('123');
    }

    /**
     * Test a domain
     */
    public function testDomain()
    {
        $name = 'example.com';
        $domain = DomainFactory::parseString($name);
        $this->assertInstanceOf(Domain::class, $domain);
        $this->assertEquals($name, strval($domain));
    }

    /**
     * Test an invalid top-level-domain
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475352664
     */
    public function testInvalidDomain()
    {
        DomainFactory::parseString('-.com');
    }

    /**
     * Test a subdomain
     */
    public function testSubdomain()
    {
        $name = 'www.example.com';
        $subdomain = DomainFactory::parseString($name);
        $this->assertInstanceOf(Domain::class, $subdomain);
        $this->assertEquals($name, strval($subdomain));
    }

    /**
     * Test an invalid subdomain
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475352664
     */
    public function testInvalidSubdomain()
    {
        DomainFactory::parseString('-.example.com');
    }

    /**
     * Test a subdomain
     */
    public function testSubsubdomain()
    {
        $name = 'sub.www.example.com';
        $subsubdomain = DomainFactory::parseString($name);
        $this->assertInstanceOf(Domain::class, $subsubdomain);
        $this->assertEquals($name, strval($subsubdomain));
    }
}
