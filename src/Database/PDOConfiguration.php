<?php
namespace ChadLinden\Api\Database;

use josegonzalez\Dotenv\Loader;
use Illuminate\Database\Capsule\Manager as Capsule;
use josegonzalez\Dotenv\Filter\LowercaseKeyFilter;
class PDOConfiguration
{
    protected $config;

    public static function connect()
    {
        // Load .env file for database config
        $config = (new static)->getConfig();

        // Pass configuration to
        // Illuminate's capsule
        $capsule = new Capsule;
        $capsule->addConnection( $config );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    }

    /**
     * @return array|null
     */
    protected function getConfig()
    {
        return (new Loader(BASE_PATH.'.env'))
            ->setFilters([LowercaseKeyFilter::class])
            ->parse()   // Get values from file
            ->filter()  // apply lowercasing
            ->toEnv()   // save to $_ENV
            ->toArray();// return array
    }
}