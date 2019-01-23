<?php

namespace App\Http\Controllers;

use Exception;
use DateInterval;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Response;
use App\Classes\ScopeRepository;
use App\Classes\ClientRepository;
use League\OAuth2\Server\CryptKey;
#use App\Classes\AuthCodeRepository;
use App\Classes\AccessTokenRepository;
#use App\Classes\RefreshTokenRepository;
#use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;
#use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\AuthorizationServer;
#use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;

class ExampleController extends Controller
{
    const PRIVATE_KEY = 'private.key';
    const PUBLIC_KEY = 'public.key';
    const ENCRYPTION_KEY = 'yyyy';

    protected $request;
    protected $response;

    protected $clientRepository;
    protected $accessTokenRepository;
    protected $scopeRepository;
#   protected $authCodeRepository;
#   protected $refreshTokenRepository;

    protected $bearerTokenValidator;

    protected $authServer;
    
    protected $scopes = ['kick', 'punch', 'headbutt'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $this->response = new Response;

        $this->clientRepository = new ClientRepository;
        $this->accessTokenRepository = new AccessTokenRepository;
        $this->scopeRepository = new ScopeRepository($this->scopes);
#       $this->authCodeRepository = new AuthCodeRepository;
#       $this->refreshTokenRepository = new RefreshTokenRepository;

        $this->bearerTokenValidator = new BearerTokenValidator($this->accessTokenRepository);
        $this->bearerTokenValidator->setPublicKey(new CryptKey(base_path(static::PUBLIC_KEY)));

        $this->authServer = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            base_path(static::PRIVATE_KEY),
            static::ENCRYPTION_KEY
        );

#       $grant = new AuthCodeGrant($this->authCodeRepository, $this->refreshTokenRepository, new DateInterval('PT10M'));
#       $this->authServer->enableGrantType($grant, new DateInterval('PT1H'));

        $grant = new ClientCredentialsGrant;
        $this->authServer->enableGrantType($grant, new DateInterval('PT1H'));
    }

    protected function check()
    {
        $this->request = $this->bearerTokenValidator->validateAuthorization($this->request);
    }

    public function _handle($method)
    {
        $method = strtolower($this->request->getMethod()) . studly_case($method);

        if (method_exists($this, $method)) {
            (new SapiEmitter)->emit(app()->call([$this, $method]));
            exit;
        }
        
        abort(404);
    }

#    public function getAuthorize(ServerRequestInterface $request, ResponseInterface $response)
/*
    public function postAuthorize()
    {
        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            // The auth request object can be serialized into a user's session
            $authRequest = $this->authServer->validateAuthorizationRequest($request);

            // Once the user has logged in set the user on the AuthorizationRequest
            #$authRequest->setUser(new UserEntity());

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);
            
            // Return the HTTP redirect response
            return $this->authServer->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Exception $e) {
            $body = new Stream('php://memory', 'r+');
            $body->write($e->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }
*/

#    public function postAccessToken(ServerRequestInterface $request, ResponseInterface $response)
    public function postAccessToken()
    {
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = new Response;

        try {
            return $this->authServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        } catch (Exception $e) {
            $body = new Stream('php://memory', 'r+');
            $body->write($e->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }

    public function getHello()
    {
        $this->check();

        $body = new Stream('php://memory', 'r+');
        $body->write('Hello, World!');

        return $this->response->withBody($body);
    }
}
