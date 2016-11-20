<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Service
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

namespace Tollwerk\Admin\Infrastructure\Service;

/**
 * PHP FPM Service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class PhpFpmService extends AbstractShellService
{
    /**
     * PHP version
     *
     * @var string
     */
    protected $php;

    /**
     * Constructor
     *
     * @param array $config Service configuration
     * @param string $php PHP version
     * @throws \RuntimeException If the PHP version is invalid
     */
    public function __construct(array $config, $php)
    {
        parent::__construct($config);

        // If the PHP version is invalid
        if (($php !== null) && (!preg_match("%^[5789]\.\d$%", $php) || (floatval($php) < 5.6))) {
            throw new \RuntimeException(sprintf('Invalid PHP version "%s"', $php), 1475937755);
        }

        $this->php = $php;
        $this->config['service'] = empty($this->config['service'])
            ? null : sprintf($this->config['service'], $this->php);
    }

    /**
     * Restart the service
     *
     * @return boolean Success
     */
    public function restart()
    {
        if ($this->config['service'] !== null) {
            self::run($this->serviceCommand((array)$this->config['restart']));
        }
        return true;
    }

    /**
     * Reload the service
     *
     * @return boolean Success
     */
    public function reload()
    {
        if ($this->config['service'] !== null) {
            self::run($this->serviceCommand((array)$this->config['reload']));
        }
        return true;
    }
}
