<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Tollwerk\Admin\Tests
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

namespace Tollwerk\Admin\Tests;

use Tollwerk\Admin\Domain\Account\Account;
use Tollwerk\Admin\Domain\Factory\DomainFactory;
use Tollwerk\Admin\Domain\Vhost\Vhost;

/**
 * Account tests
 *
 * @package Apparat\Server
 * @subpackage Tollwerk\Admin\Tests
 */
class AccountTests extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the instatiation of an account
     */
    public function testAccount()
    {
        $account = new Account('test');
        $this->assertInstanceOf(Account::class, $account);
        $this->assertEquals('test', $account->getName());
        $this->assertFalse($account->isActive());
        $this->assertEquals([], $account->getVhosts());
    }

    /**
     * Test the registration of virtual hosts
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1475488477
     */
    public function testAccountVirtualHosts()
    {
        $account = new Account('test');
        $this->assertInstanceOf(Account::class, $account);

        $domain = DomainFactory::parseString('example.com');
        $vhost = new Vhost($domain, __DIR__);
        $this->assertInstanceOf(Vhost::class, $vhost, Vhost::PORT_HTTP_DEFAULT);

        $account->setActive(true);
        $this->assertTrue($account->isActive());

        $account->setVhosts([$vhost]);
        $this->assertEquals([$vhost], $account->getVhosts());
        $account->removeVirtualHost($vhost);
        $this->assertEquals([], $account->getVhosts());
        $account->addVirtualHost($vhost);
        $this->assertEquals([$vhost], $account->getVhosts());
        $account->setVhosts(['test']);
    }
}
