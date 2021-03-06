<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure
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

namespace Tollwerk\Admin\Infrastructure\Commands\Vhost;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tollwerk\Admin\Infrastructure\Commands\AbstractCommand;
use Tollwerk\Admin\Ports\Facade\Vhost;

/**
 * vhost:domain:remove command
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class DomainRemoveVhostCommand extends AbstractCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('vhost:domain:remove')
            // the short description shown while running "php bin/console list"
            ->setDescription('Remove a secondary domain')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to remove a secondary domain from a virtual host")
            // configure the virtual host account name
            ->addArgument(
                'account',
                InputArgument::REQUIRED,
                'The name of the account the virtual host belongs to'
            )
            // configure the virtual host document root
            ->addArgument('docroot', InputArgument::REQUIRED, 'The virtual hosts\'s document root')
            // configure the domain to remove from the virtual host
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain to remove from the virtual host');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Status code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $input->getArgument('account');
        $docroot = $input->getArgument('docroot');
        $domain = $input->getArgument('domain');
        try {
            Vhost::removeDomain($account, $domain, $docroot);
            $output->writeln(
                sprintf('<info>Domain "%s" successfully removed from virtual host "%s"</info>', $domain, $docroot ?: '/')
            );
            $this->printMessages($output);
            return 0;
        } catch (\Exception $e) {
            $output->writeln(
                sprintf(
                    '<error>Error removing domain "%s" from virtual host "%s": %s (%s)</error>',
                    $domain,
                    $docroot ?: '/',
                    $e->getMessage(),
                    $e->getCode()
                )
            );
            $this->printMessages($output);
            return $e->getCode();
        }
    }
}
