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
        $command = new Command();
        $command->setCommand(Binary::get('groupadd'));
        $command->addArg('-f');
        $command->addArg(App::getConfig('shell.group'));
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
            $this->deleteTree($tmpDirectory);
        }
    }

    /**
     * Test creating an account
     */
    public function testAccount()
    {
        $command = $this->getAdminCmd()->addArg('account:create')->addArg('test');
        $success = $command->execute();
        if (!$success) {
            echo $command->getOutput().' ('.$command->getExitCode().')';
        }
        $this->assertTrue($success);

        $this->assertEquals(1, $this->getConnection()->getRowCount('account'));
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->createFlatXMLDataSet($this->getFixture('account_create.xml'))->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertDirectoryExists(self::$homebase.'test');
        $this->assertUserExists('test');

        // Enable the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:enable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->createFlatXMLDataSet($this->getFixture('account_enable.xml'))->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Disable the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:disable')->addArg('test')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->createFlatXMLDataSet($this->getFixture('account_create.xml'))->getTable('account');
        $this->assertTablesEqual($expectedTable, $queryTable);

        // Rename the account
        $this->assertTrue($this->getAdminCmd()->addArg('account:rename')->addArg('test')->addArg('renamed')->execute());
        $queryTable = $this->getConnection()->createQueryTable('account', 'SELECT * FROM account');
        $expectedTable = $this->createFlatXMLDataSet($this->getFixture('account_rename.xml'))->getTable('account');
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
        $command = new Command();
        $command->setCommand(Binary::get('php'));
        $command->addArg(self::$admincli);
        return $command;
    }

    /**
     * Return the absolute path to a fixture file
     *
     * @param string $fixture Fixture file name
     * @return string Absolute fixture file
     */
    protected function getFixture($fixture)
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.$fixture;
    }

    /**
     * Recursively delete a directory
     *
     * @param string $dir Directory
     * @return bool Success
     */
    protected function deleteTree($dir)
    {
        foreach (array_diff(scandir($dir), array('.', '..')) as $file) {
            if (is_dir("$dir/$file")) {
                $this->deleteTree("$dir/$file");
                continue;
            }
            unlink("$dir/$file");
        }
        return rmdir($dir);
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
