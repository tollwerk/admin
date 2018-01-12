<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure\Factory
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2018 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Tollwerk\Admin\Infrastructure\Factory;

use Tollwerk\Admin\Application\Contract\PersistenceAdapterFactoryInterface;
use Tollwerk\Admin\Application\Contract\VhostPersistenceAdapterStrategyInterface;
use Tollwerk\Admin\Domain\Vhost\Vhost;
use Tollwerk\Admin\Infrastructure\Strategy\ApachePersistenceAdapterStrategy;

/**
 * Persistence adapter factory
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class PersistenceAdapterFactory implements PersistenceAdapterFactoryInterface
{
    /**
     * Virtual host persistence adapter strategies
     *
     * @var array
     */
    protected static $vhostPersistenceAdapterStrategies = [
        Vhost::TYPE_APACHE => ApachePersistenceAdapterStrategy::class
    ];

    /**
     * Create a virtual host persistence adapter
     *
     * @param string $type Adapter type
     * @return VhostPersistenceAdapterStrategyInterface Persistence adapter strategy
     */
    public function makeVhostPersistenceAdapterStrategy($type)
    {
        $type = trim($type);

        // If an invalid virtual host persistence adapter strategy is requested
        if (!strlen($type) || empty(self::$vhostPersistenceAdapterStrategies[$type])) {
            throw new \RuntimeException(
                sprintf('Invalid virtual host persistence adapter strategy "%s"', $type),
                1475695171
            );
        }

        return new self::$vhostPersistenceAdapterStrategies[$type]();
    }
}
