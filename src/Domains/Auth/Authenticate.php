<?php

namespace ChadLinden\Api\Domains\Auth;

use Equip\Payload;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use ChadLinden\Api\Authenticator;
use ChadLinden\Api\Domains\Domain;

class Authenticate extends Domain implements DomainInterface
{
    protected $auth;
    protected $payload;

    public function __construct(Authenticator $auth, Payload $payload)
    {
        $this->auth = $auth;
        $this->payload = $payload;
    }

    /**
     * @param array $input
     * @return PayloadInterface
     */
    public function __invoke(array $input)
    {
        if( empty($input['email']) || empty($input['password'])){
            return $this->respondNotAuthorized();
        }

        // Check login credentials
        $login = $this->auth->login( $input['email'], $input['password']);

        if ( ! $login){
            return $this->respondNotAccepted('Invalid username or password.');
        }

        // Login okay, time for API to play
        $this->payload = $this->payload->withStatus(Payload::STATUS_ACCEPTED);

        // Get a new token for user
        $token = $this->auth->authenticate();

        // Return token to user
        return $this->payload->withOutput([
            'token' => $token->getAttributes()
        ]);
    }

    /**
     * @return Authenticator
     */
    public function getAuth()
    {
        return $this->auth;
    }

}