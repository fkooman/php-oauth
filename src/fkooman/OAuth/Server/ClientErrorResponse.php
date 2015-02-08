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

namespace fkooman\OAuth\Server;

use fkooman\Http\Request;
use fkooman\Http\RedirectResponse;

class ClientErrorResponse extends RedirectResponse
{
    public function __construct(ClientData $clientData, Request $request, $redirectUri, $error, $description)
    {
        $clientType = $clientData->getType();
        $state = $request->getQueryParameter('state');

        if ('token' === $clientType) {
            $separator = "#";
        } else {
            $separator = (false === strpos($redirectUri, "?")) ? "?" : "&";
        }

        $parameters = array(
            "error" => $error,
            "error_description" => $description
        );
        if (null !== $state) {
            $parameters['state'] = $state;
        }

        parent::__construct(
            sprintf(
                '%s%s%s',
                $redirectUri,
                $separator,
                http_build_query($parameters)
            ),
            302
        );
    }
}
