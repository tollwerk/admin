<?php

/**
 * admin
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  ${NAMESPACE}
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

require_once __DIR__.DIRECTORY_SEPARATOR.'bootstrap.php';

use Tollwerk\Admin\Infrastructure\App;
use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\ConsoleEvents;
use Tollwerk\Admin\Infrastructure\Commands\Account\CreateAccountCommand;
use Tollwerk\Admin\Infrastructure\Commands\Account\DeleteAccountCommand;
use Tollwerk\Admin\Infrastructure\Commands\Account\EnableAccountCommand;
use Tollwerk\Admin\Infrastructure\Commands\Account\DisableAccountCommand;
use Tollwerk\Admin\Infrastructure\Commands\Account\RenameAccountCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\CreateDomainCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\DeleteDomainCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\EnableDomainCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\DisableDomainCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\EnableDomainWildcardCommand;
use Tollwerk\Admin\Infrastructure\Commands\Domain\DisableDomainWildcardCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\CreateVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\DeleteVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\EnableVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\DisableVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\RedirectVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\PhpVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\PortAddVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\PortRemoveVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\DomainAddVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\DomainRemoveVhostCommand;
use Tollwerk\Admin\Infrastructure\Commands\Vhost\CertifyVhostCommand;

$dispatcher = new EventDispatcher();
$dispatcher->addListener(ConsoleEvents::TERMINATE, function (/*ConsoleTerminateEvent $event*/) {
    App::getServiceService()->runSchedule();
});

$application = new Application();
$application->setCatchExceptions(true);
$application->setDispatcher($dispatcher);

// Account commands
$application->add(new CreateAccountCommand());
$application->add(new DeleteAccountCommand());
$application->add(new EnableAccountCommand());
$application->add(new DisableAccountCommand());
$application->add(new RenameAccountCommand());

// Domain commands
$application->add(new CreateDomainCommand());
$application->add(new DeleteDomainCommand());
$application->add(new EnableDomainCommand());
$application->add(new DisableDomainCommand());
$application->add(new EnableDomainWildcardCommand());
$application->add(new DisableDomainWildcardCommand());
$application->add(new DisableDomainWildcardCommand());

// Virtual host commands
$application->add(new CreateVhostCommand());
$application->add(new DeleteVhostCommand());
$application->add(new EnableVhostCommand());
$application->add(new DisableVhostCommand());
$application->add(new RedirectVhostCommand());
$application->add(new PhpVhostCommand());
$application->add(new PortAddVhostCommand());
$application->add(new PortRemoveVhostCommand());
$application->add(new DomainAddVhostCommand());
$application->add(new DomainRemoveVhostCommand());
$application->add(new CertifyVhostCommand());

$application->run();
