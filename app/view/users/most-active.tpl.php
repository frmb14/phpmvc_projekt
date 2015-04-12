<div class="popular-tags well"> 	
	<h2>Most Reputation <i class="fa fa-star-o"></i></h2>
	
	<?php if (is_array($users)) : ?>

		<?php foreach ($users as $user) : ?>
		<?php $user = $user->getProperties(); ?>
		
			<img class="left" src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user['email'])))?>?s=50" />
			<div class="left" style="margin-left: 15px;">
				<span><b><a href="<?=$this->url->create('users/id/'.$user['id'])?>"><?=$user['acronym']?></a></b></span>
				<div> <span class="badge"><?=$user['rep']?></span> reputation</div>
			</div>
			<div class="clearfix" style="padding-bottom: 5px;"></div>

		<?php endforeach; ?>
	<?php else : ?>
		<?=$users;?>
	<?php endif; ?>
</div>
