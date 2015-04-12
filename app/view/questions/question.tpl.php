<?php if (is_array($questions)) : ?>

	<?php foreach ($questions as $question) : ?>
		<?php $question = $question->getProperties(); ?>
		
		<?php 
			//Provide some user feedback to the reputation buttons
			$reputationStatus = $this->reputation->repGiven($question['id'])['type']; 
			$repDisabled = null; $repPositive = null; $repNegative = null;
			switch($reputationStatus){
				case 'disabled': 	$repDisabled = 'disabled'; 								break;
				case '+': 			$repPositive = 'style="color:#3c763d !important;"'; 	break;
				case '-': 			$repNegative = 'style="color:#a94442 !important;"'; 	break;
			}
		?>
		
		<div class="wrap-question-single">
			<div class="summary" >
				<h3><?=$question['title']?></h3>
				<div class="votes">
					<a class="<?=$repDisabled?>" href="<?=$this->url->create('reputation/rep/'.$this->reputation->buildRepString('+',$question['member_id'],$question['id']))?>" title="This question shows research effort; it is useful and clear"><i <?=$repPositive?> class="glyphicon glyphicon-triangle-top"></i></a>
					<div><?=$this->reputation->getRep($question['id'])?></div>
					<a class="<?=$repDisabled?>" href="<?=$this->url->create('reputation/rep/'.$this->reputation->buildRepString('-',$question['member_id'],$question['id']))?>" title="This question does not show any research effort; it is unclear or not useful"><i <?=$repNegative?> class="glyphicon glyphicon-triangle-bottom"></i></a>
				</div>
				<div class="wrap-container">
					<div><?=$this->textFilter->doFilter($question['content'], 'shortcode, markdown')?></div>
					<div class="tags left">
						<?php $tags = explode(',', $question['tags']); ?>
						<?php foreach ($tags as $tag) : ?>
							<a href="<?=$this->url->create('question/tagged/'.$tag)?>"><span class="label label-info"><?=$tag?></span></a>
						<?php endforeach; ?>
					</div>
					<div class="started right">asked <?= time_elapsed_string('@'.$question['timestamp']);?> ago <a href="<?=$this->url->create('users/id/'.$question['member_id'])?>"><?=$question['member_name']?></a></div>
				</div>
			</div>
		</div>
		<div class="comment-area">
			<?php foreach ($comments as $comment) : ?>
				<?php $comment = $comment->getProperties(); ?>
					<?php if(is_null($comment['answer_id']) || $comment['answer_id'] == 0): ?>
						<div class="comment"><?=$this->textFilter->doFilter($comment['content'], 'shortcode, markdown')?> –  <a href="<?=$this->url->create('users/id/'.$question['member_id'])?>"><?=$comment['member_name']?></a> <span class="started"><?= time_elapsed_string('@'.$comment['timestamp']);?> ago</span></div>
					<?php endif; ?>
				<?php endforeach; ?>
			<div class="add-comment">add a comment</div>
			
			<div class="comment-container">
				<?=
					$this->dispatcher->forward([
						'controller' => 'comment',
						'action'     => 'create',
						'params'	 => ['qId' => $question['id']],
					]);
				?>
			</div>
			
		</div>
	<?php endforeach; ?>
<?php else : ?>
	<?=$questions;?>
<?php endif; ?>