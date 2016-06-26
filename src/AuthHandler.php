<?php
namespace ChadLinden\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AuthHandler
 * @package ChadLinden\Api
 */
class AuthHandler
{
    /**
     *
     */
    const IDENTITY = 'CMLAPI';

    /**
     * @var array
     */
    public $exceptedRoutes = [
        'login'
    ];

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     * @throws \Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $params = $request->getServerParams();

        $token = $this->extractToken( $params );

        // Throw forbidden if no token
        // and this isn't an open route
        if( ! $token && ! $this->exceptedRoutesCheck( $params ) ){
            throw new \Exception('forbidden');
        }

        // Insert token in the request
        // for other classes reference
        $request = $request->withAttribute(static::IDENTITY, $token);

        return $next($request, $response);
    }

    /**
     * @param $params
     * @return bool
     */
    private function extractToken($params)
    {
        if( ! empty($params['HTTP_TOKEN']) )
        {
            return $params['HTTP_TOKEN'];
        }
        return false;
    }

    /**
     * Routes which are excepted
     * from token authentication
     * @param $params
     * @return bool
     */
    private function exceptedRoutesCheck($params)
    {
        foreach( $this->exceptedRoutes as $safeRoute )
        {
            if( strpos($params['REQUEST_URI'], $safeRoute) !== false )
            {
                return true;
            }
        }
        return false;
    }

}