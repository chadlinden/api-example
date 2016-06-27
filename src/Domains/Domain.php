<?php

namespace ChadLinden\Api\Domains;

use Equip\Payload;
use ChadLinden\Api\Authenticator;
/**
 * Class Domain
 * @package ChadLinden\Api\Domains
 */
class Domain
{
    /**
     * @var
     */
    protected $auth;
    /**
     * @var
     */
    protected $payload;
    /**
     * @var
     */
    protected $mutator;

    /**
     * Check user's access token
     * @param array $input
     * @param string $message
     * @return mixed
     */
    protected function authorize(array $input, $message = 'unauthorized method')
    {
        if( ! $this->auth->check( $input, 'post' ) ){
            return false;
        }

        return true;
    }

    protected function respondNotAccepted( $message = 'credentials not accepted')
    {
        $this->payload = $this->payload->withStatus(Payload::STATUS_NOT_ACCEPTABLE);
        return $this->payload->withOutput(['error' => $message]);
    }
    /**
     * Returns a not authorized status 
     * with optional message / default 
     * @param string $message
     * @return mixed
     */
    protected function respondNotAuthorized( $message = 'unauthorized')
    {
        $this->payload = $this->payload->withStatus(Payload::STATUS_UNAUTHORIZED);
        return $this->payload->withOutput([ "error" => $message ]);
    }

    /**
     * Return not found status with
     * optional message / default
     * @param string $message
     * @return mixed
     */
    protected function respondNotFound($message = 'not found')
    {
        $this->payload = $this->payload->withStatus( Payload::STATUS_NOT_FOUND);
        return $this->payload->withOutput([ 'error' => $message ]);
    }

    /**
     * Return missing field with
     * optional message / default
     * @param string $message
     * @return mixed
     */
    protected function respondMissingField($message = 'missing requirement')
    {
        $this->payload = $this->payload->withStatus( Payload::STATUS_FAILED_DEPENDENCY);
        return $this->payload->withOutput([ 'error' => $message ]);
    }

}