<?php

/***
 *  Call this file from seeds/ to seed db
 */
require_once __DIR__.'/../../../public/index.php';
require_once __DIR__ . '/SeedFactory.php';

/* Bootstrapping */
$injector = new Auryn\Injector();
$boot = new ChadLinden\Api\Bootstrap( $injector );


$records = 100;
//SeedFactory::create()->seed($records);
//SeedFactory::create()->makeEmployee(100);
SeedFactory::create()->oneTime();
