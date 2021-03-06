<?php

/**
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\Ini\IniReader;
use fkooman\OAuth\Server\ApprovalsService;
use fkooman\OAuth\Server\Authenticator;
use fkooman\Rest\PluginRegistry;
use fkooman\OAuth\Server\PdoStorage;
use fkooman\Rest\Plugin\ReferrerCheck\ReferrerCheckPlugin;
use fkooman\Rest\ExceptionHandler;

ExceptionHandler::register();

$iniReader = IniReader::fromFile(
    dirname(__DIR__).'/config/oauth.ini'
);

$db = new PDO(
    $iniReader->v('PdoStorage', 'dsn'),
    $iniReader->v('PdoStorage', 'username', false),
    $iniReader->v('PdoStorage', 'password', false)
);

$auth = new Authenticator($iniReader);
$authenticationPlugin = $auth->getAuthenticationPlugin();

$service = new ApprovalsService(
    new PdoStorage($db)
);

$pluginRegistry = new PluginRegistry();
$pluginRegistry->registerDefaultPlugin(new ReferrerCheckPlugin());
$pluginRegistry->registerDefaultPlugin($authenticationPlugin);
$service->setPluginRegistry($pluginRegistry);
$service->run()->send();
