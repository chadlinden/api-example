<?php
namespace ChadLinden\Api;


use Carbon\Carbon;
use ChadLinden\Api\Models\User;
use ChadLinden\Api\Models\Token;

/**
 * Class Authenticator
 * @package ChadLinden\Api
 */
class Authenticator
{
    /**
     * @var
     */
    protected $password;
    /**
     * @var
     */
    protected $email;
    /**
     * @var
     */
    protected $user;

    /**
     * The world's simplest,
     * and worst, RABC system
     * @var array
     */
    protected $permissions = [
        'manager' => ['post', 'get'],
        'employee' => [ 'get' ]
    ];

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function login($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
        return $this->checkLogin($email, $password);
    }


    /**
     * @param $email
     * @param $password
     * @return bool
     */
    private function checkLogin($email, $password)
    {
        $this->user = User::where('email', $email)->first();
        return ( $this->user && $this->checkPassword($password) );
    }

    /**
     * @return static
     */
    public function authenticate()
    {
        return Token::create([
            'user_id' => $this->user->id,
            'expires' => Carbon::now()->addHour()->toDateTimeString(),
            'token' => password_hash( $this->user->password.$_ENV['app_key'], 1)
        ]);
    }

    /**
     * @param $password
     * @return bool
     */
    private function checkPassword($password )
    {
        return password_verify( $password , $this->user->password );
    }

    /**
     * A bare-minimum auth check
     * @param $input
     * @return bool
     */
    public function check( $input, $method )
    {
        // Validate token
        $token = Token::getInputToken( $input );

        if( ! $token->expired() )
        {
            // Assign user for reference
            $this->user = $token->user();

            // Add time to token expiration
            $token->refresh();

            // Verify user permissions
            return $this->can( $method );
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $method
     * @return bool
     */
    private function can($method)
    {
        return in_array(
            $method,
            $this->permissions[$this->user->role]
        );
    }
}