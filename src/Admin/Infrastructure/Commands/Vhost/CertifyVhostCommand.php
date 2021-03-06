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
 * vhost:certify command
 *
 * @package Tollwerk\Admin
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class CertifyVhostCommand extends AbstractCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('vhost:certify')
            // the short description shown while running "php bin/console list"
            ->setDescription('Certify a virtual host')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to issue an SSL certificate for a virtual host")
            // configure the virtual host account name
            ->addArgument(
                'account',
                InputArgument::REQUIRED,
                'The name of the account the virtual host belongs to'
            )
            // configure the virtual host document root
            ->addArgument('docroot', InputArgument::OPTIONAL, 'The virtual hosts\'s document root', '');
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
        try {
            Vhost::certify($account, $docroot);
            $output->writeln(sprintf('<info>Virtual host "%s" certified successfully</info>', $docroot ?: '/'));
            $this->printMessages($output);
            return 0;
        } catch (\Exception $e) {
            $output->writeln(
                sprintf(
                    '<error>Error certifying virtual host "%s": %s (%s)</error>',
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
