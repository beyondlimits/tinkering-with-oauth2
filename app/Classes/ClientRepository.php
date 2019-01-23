<?php

namespace App\Classes;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Get a client.
     *
     * @param string      $clientIdentifier   The client's identifier
     * @param string      $grantType          The grant type used
     * @param null|string $clientSecret       The client's secret (if sent)
     * @param bool        $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        // find active client
        $record = app('db')->table('clients')->where(['id' => $clientIdentifier, 'revoked' => false])->first();

        if (!$record) {
            return;
        }


        if ($mustValidateSecret && !hash_equals($record->secret, (string) $clientSecret)) {
            return;
        }

        return new ClientEntity($clientIdentifier, $record->name, $record->redirect);
    }
}
