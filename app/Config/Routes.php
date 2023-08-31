<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/music', 'Music::index');
$routes->post('/music/upload', 'Music::upload');
