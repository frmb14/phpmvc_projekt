<?php
/**
 * Config file for pagecontrollers, creating an instance of $app.
 *
 */

// Get environment & autoloader.
require __DIR__.'/config.php'; 

// Create services and inject into the app. 
$di  = new \Anax\DI\CDIFactoryDefault();
$app = new \Anax\Kernel\CAnax($di);

$di->set('QuestionController', function() use ($di) {
    $controller = new Phpmvc\Question\QuestionController();
    $controller->setDI($di);
    return $controller;
});

$di->set('AnswerController', function() use ($di) {
    $controller = new Phpmvc\Answer\AnswerController();
    $controller->setDI($di);
    return $controller;
});

$di->set('CommentController', function() use ($di) {
    $controller = new Phpmvc\Comment\CommentController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('ReputationController', function() use ($di) {
    $controller = new \Anax\Reputation\ReputationController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('users', function() use ($di) {
    $users = new \Anax\Users\User();
    $users->setDI($di);
    return $users;
});

$di->setShared('reputation', function() use ($di) {
    $reputation = new \Anax\Reputation\Reputation();
    $reputation->setDI($di);
    return $reputation;
});

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/config_mysql.php');
    $db->connect();
    return $db;
});