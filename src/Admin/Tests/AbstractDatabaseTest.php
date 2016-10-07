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

use Doctrine\DBAL\DriverManager;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

/**
 * Abstract database test case
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Tests
 */
abstract class AbstractDatabaseTest extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * PDO instance
     *
     * @var null
     */
    private static $pdo = null;
    /**
     * DBAL instance
     *
     * @var null
     */
    private static $dbal = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    /**
     * Connection
     *
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $conn = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection Database connection
     */
    final protected function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    /**
     * Return the PDO instance
     *
     * @return \PDO PDO instance
     */
    protected function getPdo()
    {
        return $this->getConnection()->getConnection();
    }

    /**
     * Return the DBAL connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getDbal()
    {
        if (self::$dbal === null) {
            self::$dbal = DriverManager::getConnection(array('pdo' => $this->getPdo()));
        }

        return self::$dbal;
    }
}
