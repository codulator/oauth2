<?php
/**
 * OAuth 2.0 Access token storage interface
 *
 * @package     league/oauth2-server
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace League\OAuth2\Server\Storage;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AbstractTokenEntity;
use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;

/**
 * Access token interface
 */
interface AccessTokenInterface
{
    /**
     * Get an instance of Entity\AccessTokenEntity
     * @param  string                                         $token The access token
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function get($token);

    /**
     * Get the scopes for an access token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token
     * @return array                                            Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AbstractTokenEntity $token);

    /**
     * Creates a new access token
     * @param  string                                   $token      The access token
     * @param  integer                                  $expireTime The expire time expressed as a unix timestamp
     * @param  string|integer                           $sessionId  The session ID
     * @return \League\OAuth2\Server\Entity\AccessToken
     */
    public function create($token, $expireTime, $sessionId);

    /**
     * Associate a scope with an acess token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token
     * @param  \League\OAuth2\Server\Entity\ScopeEntity         $scope The scope
     * @return void
     */
    public function associateScope(AbstractTokenEntity $token, ScopeEntity $scope);

    /**
     * Delete an access token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token to delete
     * @return void
     */
    public function delete(AbstractTokenEntity $token);
}
