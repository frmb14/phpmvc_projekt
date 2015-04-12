<h1><?=$title?></h1>
 
<?php if (!empty($users)) : ?>
<div class='comments'>
<?php foreach ($users as $user) : ?>
<?php $user = $user->getProperties(); ?>

<div class="wrap-users">
	<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user['email'])))?>?s=45" />
	<a href="<?=$this->url->create('users/id/'.$user['id'])?>"><span><b><?=$user['acronym']?> </b></span></a> 
	&bull;
	<span><?=$user['active'] &&  !$user['deleted'] ? 'Active' : ($user['deleted'] ? 'In Trashbin' : 'Inactive')?></span>
	
</div>
<div class="clearfix"></div>
<?php endforeach; ?>
</div>
<?php else: ?>
<p>There's no members registered!</p>
<?php endif; ?>

<div class="clearfix"></div>
 