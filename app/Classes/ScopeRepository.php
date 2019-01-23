<?php

namespace App\Classes;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    protected $scopes;

    public function __construct(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * Return information about a scope.
     *
     * @param string $identifier The scope identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        if (in_array($identifier, $this->scopes)) {
            return new ScopeEntity($identifier);
        }
    }

    /**
     * Given a client, grant type and optional user identifier validate the set of scopes requested are valid and optionally
     * append additional scopes or remove requested scopes.
     *
     * @param ScopeEntityInterface[] $scopes
     * @param string                 $grantType
     * @param ClientEntityInterface  $clientEntity
     * @param null|string            $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        foreach ($scopes as $key => $scope) {
            if (!in_array($scope->getIdentifier(), $this->scopes)) {
                unset($scopes[$key]);
            }
        }

        if ($grantType == 'password' && $grantType == 'personal_access') {
            foreach ($scopes as $key => $scope) {
                if (trim($scope->getIdentifier()) == '*') {
                    unset($scopes[$key]);
                }
            }
        }

        return array_values($scopes);
    }
}
