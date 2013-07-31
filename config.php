<?php

// configurations - requires Slim $app object

$app->config('templates.path', 'templates');
$app->config('debug', 'true');

require_once 'vendor/slim/extras/Slim/Extras/Views/Twig.php';

\Slim\Extras\Views\Twig::$twigExtensions = array(
    'Twig_Extensions_Slim',
);

$app->view(new \Slim\Extras\Views\Twig());

// extensions to Twig Templating system

// this adds Slims's urlFor function to Twig templating
$env = $app->view()->getEnvironment();

$urlFor = new Twig_SimpleFunction('urlFor', function ($url, $data=null) use($app) {
    if(is_array($data)){
        return $app->urlFor($url,$data);
    } else {
        return $app->urlFor($url);
    }
});

$env->addFunction($urlFor);

ORM::configure('mysql:host=localhost;dbname=feeds');
ORM::configure('username', 'feeds');
ORM::configure('password', 'feeds');