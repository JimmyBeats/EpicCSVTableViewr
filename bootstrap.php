<?php
/**
 * Bootstrap file which registers the autoloader, loads in the configuration file and instantiates the Repository
 * in a pretend service container. This gets required at the top of any entry point to the application
 */
require __DIR__ . '/vendor/autoload.php';

// Using PHP Dot Env to load in environment variables from the .env file in the root
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Initial a global array variable for use as a very basic service container
// I would normally use something like Pimple to do this properly (http://pimple.sensiolabs.org/)
$app = [];

// Instantiate the Repository
$app['repository'] = new Jimmy\EpicCSVTableViewr\Entity\Repository(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_NAME')
);
