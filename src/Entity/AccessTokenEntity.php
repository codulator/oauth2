<?php
/**
 * OAuth 2.0 Access token entity
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Entity;

/**
 * Access token entity class
 */
class AccessTokenEntity extends AbstractTokenEntity
{
    /**
     * Get session
     * @return \League\OAuth2\Server\SessionEntity
     */
    public function getSession()
    {
        if ($this->session instanceof SessionEntity) {
            return $this->session;
        }

        $this->session = $this->server->getStorage('session')->getByAccessToken($this);

        return $this->session;
    }

    /**
     * Check if access token has an associated scope
     * @param  string $scope Scope to check
     * @return bool
     */
    public function hasScope($scope)
    {
        if ($this->scopes === null) {
            $this->getScopes();
        }

        return isset($this->scopes[$scope]);
    }

    /**
     * Return all scopes associated with the session
     * @return array Array of \League\OAuth2\Server\Entity\Scope
     */
    public function getScopes()
    {
        if ($this->scopes === null) {
            $this->scopes = $this->formatScopes(
                $this->server->getStorage('access_token')->getScopes($this)
            );
        }

        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->server->getStorage('access_token')->create(
            $this->getId(),
            $this->getExpireTime(),
            $this->getSession()->getId()
        );

        // Associate the scope with the token
        foreach ($this->getScopes() as $scope) {
            $this->server->getStorage('access_token')->associateScope($this, $scope);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expire()
    {
        $this->server->getStorage('access_token')->delete($this);
    }
}
