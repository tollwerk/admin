<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
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

use mikehaertl\shellcommand\Command;
use Tollwerk\Admin\Infrastructure\App;
use Tollwerk\Admin\Infrastructure\Shell\Binary;

/**
 * Command line tool tests
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Tests
 */
class CliTest extends AbstractDatabaseTest
{
    /**
     * Admin CLI interface
     *
     * @var string
     */
    protected static $admincli;
    /**
     * Home directory base
     *
     * @var string
     */
    protected static $homebase;
    /**
     * Temporary directories
     *
     * @var array
     */
    protected static $tmpDirectories = [];

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self::$admincli =
            dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'tollwerk-admin.php';

        // TODO: Make environment dependent
        self::$homebase = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;

        // Ensure the account system group exists
        App::bootstrap();
        $command = Binary::sudo('groupadd');
        $command->addArg('-f');
        $command->addArg(App::getConfig('general.group'));
        if (!$command->execute()) {
            echo $command->getOutput().' ('.$command->getExitCode().')'.PHP_EOL;
            throw new \RuntimeException($command->getOutput(), $command->getExitCode());
        }
    }

    /**
     * Performs operation returned by getTearDownOperation().
     */
    public function tearDown()
    {
        parent::tearDown();

        // Remove all temporary directories
        foreach (self::$tmpDirectories as $tmpDirectory) {
            Binary::sudo('rm')->addArg('-Rf')->addArg($tmpDirectory)->execute();
        }
    }

    /**
     * Test creating, modifying and deleting an account
     */
    public function testAccount()
    {
        // Create an account
        $this->assertTrue($this->getAdminCmd()->addArg('account:create')->addArg('test')->execute());
        $this->assertEquals(1, $this->getConnection()->getRowCount('account'));
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->getFixtureDataSet('account_create.xml')->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertDirectoryExists(self::$homebase.'test');
        $this->assertUserExists('test');

        // Enable the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:enable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->getFixtureDataSet('account_enable.xml')->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Disable the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:disable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->getFixtureDataSet('account_create.xml')->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Rename the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:rename')->addArg('test')->addArg('renamed')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->getFixtureDataSet('account_rename.xml')->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertDirectoryExists(self::$homebase.'renamed');
        $this->assertDirectoryNotExists(self::$homebase.'test');
        $this->assertUserExists('renamed');
        $this->assertUserNotExists('tests');

        // Delete the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:delete')->addArg('renamed')->execute());
        $this->assertEquals(0, $this->getConnection()->getRowCount('account'));
        $this->assertDirectoryExists(self::$homebase.'renamed');
        $this->assertUserNotExists('renamed');

        // Register home directory for removal
        self::$tmpDirectories[] = self::$homebase.'renamed';
    }

    /**
     * Test creating, modifying and deleting a domain
     */
    public function testDomain()
    {
        // Create an account
        $this->assertTrue($this->getAdminCmd()->addArg('account:create')->addArg('test')->execute());

        // Create a domain
        $this->assertTrue($this->getAdminCmd()->addArg('domain:create')->addArg('test')->addArg('example.com')
            ->execute());
        $this->assertEquals(1, $this->getConnection()->getRowCount('domain'));
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('domain_create.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Enable the domain
        $this->assertTrue($this->getAdminCmd()->addArg('domain:enable')->addArg('example.com')->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('domain_enable.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Enable the domain wildcard
        $this->assertTrue($this->getAdminCmd()->addArg('domain:enable:wildcard')->addArg('example.com')->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('domain_enable_wildcard.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Disable the domain
        $this->assertTrue($this->getAdminCmd()->addArg('domain:disable')->addArg('example.com')->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('domain_disable.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Disable the domain wildcard
        $this->assertTrue($this->getAdminCmd()->addArg('domain:disable:wildcard')->addArg('example.com')->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('domain_create.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Delete the domain
        $this->assertTrue($this->getAdminCmd()->addArg('domain:delete')->addArg('example.com')->execute());
        $this->assertEquals(0, $this->getConnection()->getRowCount('domain'));

        // Delete the account and the home directory
        $this->assertTrue($this->getAdminCmd()->addArg('account:delete')->addArg('test')->execute());
        $this->assertEquals(0, $this->getConnection()->getRowCount('account'));
        $this->assertUserNotExists('test');
        self::$tmpDirectories[] = self::$homebase.'test';
    }

    /**
     * Test creating, modifying and deleting a virtual host
     */
    public function testVhost()
    {
        // Create an account
        $this->assertTrue($this->getAdminCmd()->addArg('account:create')->addArg('test')->execute());

        // Create two domains
        $this->assertTrue($this->getAdminCmd()->addArg('domain:create')->addArg('test')->addArg('example.com')
            ->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('domain:create')->addArg('test')->addArg('test.com')->execute());
        $this->assertEquals(2, $this->getConnection()->getRowCount('domain'));
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('vhost_create_domain.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Create a virtual host
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:create')->addArg('test')->addArg('example.com')
            ->execute());
        $this->assertEquals(1, $this->getConnection()->getRowCount('vhost'));
        $queryTable = $this->getConnection()->createQueryTable('vhost', 'SELECT * FROM vhost');
        $expectedTable = $this->getFixtureDataSet('vhost_create.xml')->getTable('vhost');
        $this->assertTablesEqual($expectedTable, $queryTable);
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('vhost_create.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Enable the vhost
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:enable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('vhost', 'SELECT * FROM vhost');
        $expectedTable = $this->getFixtureDataSet('vhost_enable.xml')->getTable('vhost');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Disable the vhost
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:disable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('vhost', 'SELECT * FROM vhost');
        $expectedTable = $this->getFixtureDataSet('vhost_create.xml')->getTable('vhost');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Configure PHP, the HTTP and the HTTPS ports, the redirect URL and status
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:php')->addArg('test')->addArg('/')->addArg('7.0')
            ->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:port:http')->addArg('test')->addArg('/')->addArg('81')
            ->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:port:https')->addArg('test')->addArg('/')->addArg('444')
            ->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:redirect')->addArg('test')->addArg('/')
            ->addArg('http://test.com')->addArg('302')->execute());
        $queryTable = $this->getConnection()->createQueryTable('vhost', 'SELECT * FROM vhost');
        $expectedTable = $this->getFixtureDataSet('vhost_php_ports_redirect.xml')->getTable('vhost');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Reset PHP, the HTTP and the HTTPS ports, the redirect URL and status
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:php')->addArg('test')->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:port:http')->addArg('test')->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:port:https')->addArg('test')->execute());
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:redirect')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('vhost', 'SELECT * FROM vhost');
        $expectedTable = $this->getFixtureDataSet('vhost_create.xml')->getTable('vhost');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Add a secondary domain
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:domain:add')->addArg('test')->addArg('test.com')
            ->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('vhost_domain_add.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Remove a secondary domain
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:domain:remove')->addArg('test')->addArg('test.com')
            ->execute());
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('vhost_create.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Delete the virtual host
        $this->assertTrue($this->getAdminCmd()->addArg('vhost:delete')->addArg('test')->execute());
        $this->assertEquals(0, $this->getConnection()->getRowCount('vhost'));
        $queryTable = $this->getConnection()->createQueryTable('domain', 'SELECT * FROM domain');
        $expectedTable = $this->getFixtureDataSet('vhost_create_domain.xml')->getTable('domain');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Delete the account and the home directory
        $this->assertTrue($this->getAdminCmd()->addArg('account:delete')->addArg('test')->execute());
        $this->assertEquals(0, $this->getConnection()->getRowCount('account'));
        $this->assertUserNotExists('test');
//        self::$tmpDirectories[] = self::$homebase.'test';
    }

    /**
     * Returns the test dataset
     *
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet(
            __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'admin_test_cli.xml'
        );
    }

    /**
     * Create and return an admin base command
     *
     * @return Command Admin base command
     */
    protected function getAdminCmd()
    {
        $command = new TestCommand();
        $command->setCommand(Binary::get('php'));
        $command->addArg(self::$admincli);
        return $command;
    }

    /**
     * Assert that a system user exists
     *
     * @param string $user User name
     */
    protected function assertUserExists($user)
    {
        $command = new Command();
        $command->setCommand(Binary::get('id'));
        $command->addArg($user);
        return $command->execute();
    }

    /**
     * Assert that a system user doesn't exist
     *
     * @param string $user User name
     */
    protected function assertUserNotExists($user)
    {
        $command = new Command();
        $command->setCommand(Binary::get('id'));
        $command->addArg($user);
        return !$command->execute();
    }
}
