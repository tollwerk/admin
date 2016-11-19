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
use Tollwerk\Admin\Domain\Factory\DomainFactory;
use Tollwerk\Admin\Domain\Vhost\Vhost;

/**
 * Virtual host tests
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Tests
 */
class VhostTests extends TestCase
{
    /**
     * Test the instantiation of a virtual host
     */
    public function testVirtualHost()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);
        $this->assertEquals($domain, $vhost->getPrimaryDomain());
        $this->assertEquals(__DIR__, $vhost->getDocroot());
        $this->assertNull($vhost->getPorts(Vhost::PROTOCOL_HTTP));
    }

    /**
     * Test setting / adding / removing secondary domains
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475484852
     */
    public function testVirtualHostSecondaryDomains()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $secondaryDomainA = DomainFactory::parseString('a.example.com');
        $secondaryDomainB = DomainFactory::parseString('b.example.com');
        $secondaryDomainC = DomainFactory::parseString('c.example.com');
        $vhost->setSecondaryDomains([$secondaryDomainA, $secondaryDomainB]);
        $vhost->addSecondaryDomain($secondaryDomainC);
        $vhost->removeSecondaryDomain($secondaryDomainA);
        $this->assertEquals($vhost->getSecondaryDomains(), [$secondaryDomainB, $secondaryDomainC]);

        $vhost->setSecondaryDomains(['one', 'two']);
    }

    /**
     * Test the enabling / disabling the supported PHP version
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475485163
     */
    public function testVirtualHostPhp()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $this->assertNull($vhost->getPhp());
        $vhost->setPhp('5.0');
        $this->assertEquals('5.0', $vhost->getPhp());
        $vhost->setPhp(null);
        $this->assertNull($vhost->getPhp());
        $vhost->setPhp('five');
    }

    /**
     * Test the retrieval of the port for an invalid protocol
     */
    public function testVirtualHostPorts()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $vhost->enablePort(Vhost::PROTOCOL_HTTP);
        $this->assertEquals([Vhost::PROTOCOL_HTTP => [Vhost::PORT_HTTP_DEFAULT]], $vhost->getPorts());
        $this->assertEquals([Vhost::PORT_HTTP_DEFAULT], $vhost->getPorts(Vhost::PROTOCOL_HTTP));
        $this->assertNull($vhost->getPorts(Vhost::PROTOCOL_HTTPS));
        $vhost->enablePort(Vhost::PROTOCOL_HTTP, 81);
        $vhost->disablePort(80);
        $this->assertEquals([81], $vhost->getPorts(Vhost::PROTOCOL_HTTP));
        $vhost->enablePort(Vhost::PROTOCOL_HTTPS);
        $vhost->disableProtocol(Vhost::PROTOCOL_HTTP);
        $this->assertEquals([Vhost::PROTOCOL_HTTPS => [Vhost::PORT_HTTPS_DEFAULT]], $vhost->getPorts());
    }

    /**
     * Test the retrieval of the port for an invalid protocol
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475484081
     */
    public function testVirtualHostInvalidProtocol()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $vhost->getPorts(4);
    }

    /**
     * Test the enabling of an invalid port
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475502412
     */
    public function testVirtualHostEnableInvalidPort()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $vhost->enablePort(Vhost::PROTOCOL_HTTP, -1);
    }

    /**
     * Test the disabling of an invalid protocol
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475484081
     */
    public function testVirtualHostDisableInvalidProtocol()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $vhost->disableProtocol(4);
    }

    /**
     * Test setting a redirect URL
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475486589
     */
    public function testVirtualHostRedirectUrl()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $vhost->setRedirectUrl('http://test.com/abc');
        $this->assertEquals('http://test.com/abc', $vhost->getRedirectUrl());
        $vhost->setRedirectUrl(null);
        $this->assertNull($vhost->getRedirectUrl());
        $vhost->setRedirectUrl('invalid');
    }

    /**
     * Test setting a redirect status
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475486679
     */
    public function testVirtualHostRedirectStatus()
    {
        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost);

        $this->assertEquals(301, $vhost->getRedirectStatus());
        $vhost->setRedirectStatus(302);
        $this->assertEquals(302, $vhost->getRedirectStatus());
        $vhost->setRedirectStatus(100);
    }
}
