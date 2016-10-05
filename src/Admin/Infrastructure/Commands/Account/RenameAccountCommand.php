<?php

/**
 * admin
 *
 * @category    Tollwerk
 * @package     Tollwerk\Admin
 * @subpackage  Tollwerk\Admin\Infrastructure
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

namespace Tollwerk\Admin\Infrastructure\Commands\Account;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tollwerk\Admin\Ports\Facade\Account;

/**
 * account:rename command
 *
 * @package Tollwerk\Server
 * @subpackage Tollwerk\Admin\Infrastructure
 */
class RenameAccountCommand extends Command
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('account:rename')
            // the short description shown while running "php bin/console list"
            ->setDescription('Rename an existing account')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command allows you to rename an existing account")
            // configure the account oldname argument
            ->addArgument('oldname', InputArgument::REQUIRED, 'The old name of the account')
            // configure the account newname argument
            ->addArgument('newname', InputArgument::REQUIRED, 'The new name of the account');
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldaccount = $input->getArgument('oldname');
        $newaccount = $input->getArgument('newname');
        try {
            Account::rename($oldaccount, $newaccount);
            $output->writeln(
                sprintf(
                    '<info>Account "%s" successfully renamed to "%s"</info>',
                    $oldaccount,
                    $newaccount
                )
            );
        } catch (\Exception $e) {
            $output->writeln(
                sprintf(
                    '<error>Error renaming account "%s" to "%s": %s (%s)</error>',
                    $oldaccount,
                    $newaccount,
                    $e->getMessage(),
                    $e->getCode()
                )
            );
        }
    }
}
