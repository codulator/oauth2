<?php
/**
 * OAuth 2.0 Auth code entity
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
class AuthCodeEntity extends AbstractTokenEntity
{
    /**
     * Redirect URI
     * @var string
     */
    protected $redirectUri = '';

    /**
     * Set the redirect URI for the authorization request
     * @param  string $redirectUri
     * @return self
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * Get the redirect URI
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * Generate a redirect URI
     * @param  string $state          The state parameter if set by the client
     * @param  string $queryDelimeter The query delimiter ('?' for auth code grant, '#' for implicit grant)
     * @return string
     */
    public function generateRedirectUri($state = null, $queryDelimeter = '?')
    {
        $uri = $this->getRedirectUri();
        $uri .= (strstr($this->getRedirectUri(), $queryDelimeter) === false) ? $queryDelimeter : '&';

        return $uri.http_build_query([
            'code'  =>  $this->getId(),
            'state' =>  $state
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSession()
    {
        if ($this->session instanceof SessionEntity) {
            return $this->session;
        }

        $this->session = $this->server->getStorage('session')->getByAuthCode($this);

        return $this->session;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        if ($this->scopes === null) {
            $this->scopes = $this->formatScopes(
                $this->server->getStorage('auth_code')->getScopes($this)
            );
        }

        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->server->getStorage('auth_code')->create(
            $this->getId(),
            $this->getExpireTime(),
            $this->getSession()->getId(),
            $this->getRedirectUri()
        );

        // Associate the scope with the token
        foreach ($this->getScopes() as $scope) {
            $this->server->getStorage('auth_code')->associateScope($this, $scope);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expire()
    {
        $this->server->getStorage('auth_code')->delete($this);
    }
}
