<?php namespace ChadLinden\Api;

use Auryn\Injector;
use ChadLinden\Api\Database\PDOConfiguration;

class Bootstrap
{
    protected $connection;
    protected $injector;
    protected $booted = false;
    protected $env;
    
    public function __construct( Injector $injector )
    {
        $this->injector = $injector;
        if( ! $this->booted )
        {
            // Put the PDO manager on the
            // injector's shared array
            $this->injector->share(PDOConfiguration::connect());
            $this->booted = ! $this->booted;
        }
        return $this;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }
}


