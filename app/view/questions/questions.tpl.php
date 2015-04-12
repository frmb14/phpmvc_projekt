<?php if (is_array($questions)) : ?>

	<?php foreach ($questions as $question) : ?>
		<?php $question = $question->getProperties(); ?>
		
		<?php 
			if (strlen($question['title'])>90)
				$question['title'] = substr($question['title'], 0, 87) . "...";
			
			$question['content'] = $this->textFilter->doFilter($question['content'], 'shortcode, markdown');
			
			if (strlen($question['content'])>200)
				$question['content'] = substr($question['content'], 0, 197) . "...";
			else if(strpos($question['content'],'</p>') !== false)
				$question['content'] = explode('</p>', $question['content'])[0] . "</br>";
		?>
		
		<div class="wrap-question">
			<div class="votes">
				<?=$this->reputation->getRep($question['id'])?><br/>
				votes
			</div>
			<div class="answers">
				<?=
					$this->dispatcher->forward([
						'controller' => 'question',
						'action'     => 'countAnswer',
						'params'	 => ['id' => $question['id']],
					]);
				?><br/>
				answers
			</div>
			<div class="summary">
				<div><a href="<?=$this->url->create('question/id/'.$question['url'])?>"><?=$question['title']?></a></div>
				<div><?=$question['content']?></div>
				<div class="tags left">
					<?php $tags = explode(',', $question['tags']); ?>
					<?php foreach ($tags as $tag) : ?>
						<a href="<?=$this->url->create('question/tagged/'.$tag)?>"><span class="label label-info"><?=$tag?></span></a>
					<?php endforeach; ?>
				</div>
				<div class="started right">asked <?= time_elapsed_string('@'.$question['timestamp']);?> ago <a href="<?=$this->url->create('users/id/'.$question['member_id'])?>"><?=$question['member_name']?></a></div>
			</div>
		</div>
		
	<?php endforeach; ?>
<?php else : ?>
	<?=$questions;?>
<?php endif; ?>