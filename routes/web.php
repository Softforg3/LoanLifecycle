<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('/example', ['uses' => '\App\Http\Controllers\Controller@exampleFunction']);

// Debug endpoint - lista wszystkich routes
$router->get('/routes', function () use ($router) {
    $routes = [];
    foreach ($router->getRoutes() as $route) {
        $routes[] = [
            'method' => $route['method'],
            'uri' => $route['uri'],
            'action' => $route['action']['uses'] ?? 'Closure',
        ];
    }
    return response()->json($routes, 200, [], JSON_PRETTY_PRINT);
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/loans', ['uses' => 'LoanController@create']);
    $router->get('/loans/{id}', ['uses' => 'LoanController@show']);
    $router->post('/loans/{id}/approve', ['uses' => 'LoanController@approve']);
    $router->post('/loans/{id}/addPayment', ['uses' => 'LoanController@addPayment']);
});
