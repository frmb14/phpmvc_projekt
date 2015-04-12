<h1><?=$user['acronym']?></h1>
 
<img class="left" src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user['email'])))?>?s=100" />
<div class="left" style="margin-left: 15px;">
	<p>Registered: <?=$user['created']?></p>
	<p>Status: <?=$user['active'] &&  !$user['deleted'] ? 'Activated' : ($user['deleted'] ? 'In Trashbin' : 'Inactive')?></p>
	<p>Reputation points: <span class="badge"><?=$user['rep']?></span></p>
</div>

<?php if($this->users->isAuthenticated() && $this->session->get('member_id') == $user['id']): ?>
	<form method=post class="right">
		<input type=hidden name="redirect" value="<?=$_SERVER['REQUEST_URI']?>">
		<input class="fa-input fa-icon-only" type="submit" onclick="this.form.action = '<?=$this->url->create('users/update/'.$user['id'])?>'" value="&#xf044; Update Account" title="Update this account"><br/>
		<?php if (!$user['deleted']) : ?>
		<input class="fa-input fa-icon-only" type="submit" onclick="this.form.action = '<?=$this->url->create('users/soft-delete/'.$user['id'])?>'" value="&#xf1f8; Trash Account" title="Put this user in the trashbin"><br/>
		<?php endif; ?>
		<input class="fa-input fa-icon-only" type="submit" onclick="this.form.action = '<?=$this->url->create('users/delete/'.$user['id'])?>'" value="&#xf00d; Delete Account" title="Ta bort"><br/>
		<input class="fa-input fa-icon-only" type="submit" onclick="this.form.action = '<?=$this->url->create('users/toggle-active/'.$user['id'])?>'" value="<?=$user['active'] &&  !$user['deleted'] ? '&#xf235; Inactivate Account' : '&#xf234; Activate Account'?>" title="Activate / Inactivate"><br/>
	</form>
<?php endif; ?>
<div class="clearfix"></div>

<br/><br/>
<h2>Last questions</h2>
<div class="well wrap-user-questions">

<?php foreach ($questions as $question) : ?>
<?php $question = $question->getProperties(); ?>
	
	<?php 
		if (strlen($question['title'])>70)
				$question['title'] = substr($question['title'], 0, 67) . "...";
		$question['content'] = $this->textFilter->doFilter($question['content'], 'shortcode, markdown');
		
		if (strlen($question['content'])>155)
			$question['content'] = substr($question['content'], 0, 152) . "...";
	?>
	
	<h4><a href="<?=$this->url->create('question/id/'.$question['id'])?>"><?= $question['title'] ?></a></h4>
	<?=$question['content']?>
	<hr>
<?php endforeach; ?>
</div>

<h2 style="margin-top: -43px; margin-left: 374px;">Last answers</h2>
<div class="well wrap-user-answers">

<?php $title = "no-title"; ?>

<?php foreach ($answers as $answer) : ?>
	<?php $answer = $answer->getProperties(); ?>

	<?php 
		if (strlen($answer['title'])>70)
				$answer['title'] = substr($answer['title'], 0, 67) . "...";
		$answer['content'] = $this->textFilter->doFilter($answer['content'], 'shortcode, markdown');
		
		if (strlen($answer['content'])>155){
			$answer['content'] = substr($answer['content'], 0, 152) . "...";
		}
		
	if($answer['title'] != $title) : ?>
		<h4><a href="<?=$this->url->create('question/id/'.$answer['question_id'])?>"><?= $title = $answer['title'] ?></a></h4>
	<?php endif;?>
	
	<?=$answer['content']?>
<?php endforeach; ?>
</div>