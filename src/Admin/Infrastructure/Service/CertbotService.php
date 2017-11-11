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

use Tollwerk\Admin\Domain\Vhost\VhostInterface;
use Tollwerk\Admin\Infrastructure\Persistence\AccountHelper;

/**
 * Certbot service
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure\Service
 */
class CertbotService extends AbstractShellService
{
    /**
     * Restart the service
     *
     * @return boolean Success
     */
    public function restart()
    {
        return $this->reload();
    }

    /**
     * Reload the service
     *
     * @return boolean Success
     * @todo Implement
     */
    public function reload()
    {
        $command = $this->serviceCommand((array)$this->config['reload']);
        $command->addArg('-c', $this->config['certconfig']);
        echo $command->getExecCommand().PHP_EOL;
//        $output = self::run($command);
//        echo trim($output).PHP_EOL;
        return false;
    }

    /**
     * Return whether a particular domain is generally certified
     *
     * @param string $domain Domain name
     * @return bool Is certified
     */
    public function isCertified($domain)
    {
        return is_dir(rtrim($this->config['certdir'], DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$domain);
    }

    /**
     * Prepare a virtual host
     *
     * @param VhostInterface $vhost Virtual host
     * @param AccountHelper $accountHelper Account helper
     * @throws \RuntimeException If the Certbot challenge directory is invalid
     * @throws \RuntimeException If the well-known symlink cannot be created
     * @throws \RuntimeException If the well-known link exists but is invalid
     */
    public function prepare(VhostInterface $vhost, AccountHelper $accountHelper)
    {
        // If the Certbot challenge directory is invalid
        $challengeDir = rtrim($this->config['challenge'], DIRECTORY_SEPARATOR);
        if (!is_dir($challengeDir)) {
            throw new \RuntimeException(
                sprintf('Certbot challenge directory "%s" is invalid', $challengeDir, 1510398571)
            );
        }

        // Create the well-known symlink
        $wellKnownLink = $accountHelper->directory('data'.rtrim(DIRECTORY_SEPARATOR.$vhost->getDocroot(),
                DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'.well-known');
        if (file_exists($wellKnownLink)) {
            // If the well-known link exists but is invalid
            if (!is_link($wellKnownLink) || (readlink($wellKnownLink) != $challengeDir)) {
                throw new \RuntimeException(
                    sprintf('Invalid certbot well-known link "%s" exists', $wellKnownLink, 1510399415)
                );
            }
            return;
        }

        // If the well-known symlink cannot be created
        if (!symlink($challengeDir, $wellKnownLink)) {
            throw new \RuntimeException(
                sprintf('Certbot well-known link "%s" cannot be created', $wellKnownLink, 1510399241)
            );
        }
    }
}
