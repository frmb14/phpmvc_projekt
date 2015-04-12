<?php

require __DIR__ . '/config_with_app.php';

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

$app->theme->configure(ANAX_APP_PATH . 'config/theme_grid.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');
$app->theme->addStylesheet('css/bootstrap.min.css');


$app->router->add('', function() use ($app){
	$app->theme->setTitle("Home");
	
	$app->views->add('default/page', ['title' => 'Bear Teh Papur', 'content' => 'Guardian Druid Overflow'], 'jumbotron');
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'view',
	]);
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'popularTags',
	]);
	
	$app->dispatcher->forward([
		'controller' => 'users',
		'action'     => 'highestReputation',
	]);
});

$app->router->add('questions', function() use ($app){
	$app->theme->setTitle("Questions");
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'view',
		'params'	 => ['view' => 'questions'],
	]);
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'popularTags',
	]);
	
});

$app->router->add('users', function() use ($app){
	$app->theme->setTitle("Users");
	
	
	$app->dispatcher->forward([
		'controller' => 'users',
		'action'     => 'list',
	]);
	
	$app->dispatcher->forward([
		'controller' => 'users',
		'action'     => 'highestReputation',
	]);
	
});

$app->router->add('questions/tags', function() use ($app){
	$app->theme->setTitle("Tags");
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'tags',
	]);
	
});

$app->router->add('about', function() use ($app){
	$app->theme->setTitle("About");
	
	$content = $app->fileContent->get('about.md');
	$content = $app->textFilter->doFilter($content, 'shortcode, markdown');
	
 
    $app->views->add('default/page', [
		'title'	  => null,
        'content' => $content,
    ]);
	
});

$app->router->add('setup', function() use ($app){
	
	$app->theme->setTitle("Setting up the website");
	
	$app->dispatcher->forward([
		'controller' => 'question',
		'action'     => 'setup',
	]);
	$app->dispatcher->forward([
		'controller' => 'answer',
		'action'     => 'setup',
	]);
	$app->dispatcher->forward([
		'controller' => 'comment',
		'action'     => 'setup',
	]);
	$app->dispatcher->forward([
		'controller' => 'users',
		'action'     => 'setup',
	]);
	$app->dispatcher->forward([
		'controller' => 'reputation',
		'action'     => 'setup',
	]);
	
	
	$app->views->add('default/page', [
		'title' => "Setup database",
		'content' => "<h3>Your database have now been created!</h3> <p>An administrator account have been created for you with the username <code>admin</code> and the password <code>admin</code>.</p>",
		'links' => [
			[
				'href' => $app->url->create('users/id/1'),
				'text' => "Take me to my account",
			],
			[
				'href' => $app->url->create(''),
				'text' => "Home",
			],
		],
	]);
	
});



$app->router->handle();
$app->theme->render();