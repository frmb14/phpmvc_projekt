<div class="wrap-answer">

	<?php if (is_array($answers)) : ?>
		<h4><?=count($answers)?> Answer</h4>
		<?php foreach ($answers as $answer) : ?>
			<?php //$answer = $answer->getProperties(); ?>
			
			<?php
				//Provide some user feedback to the reputation buttons
				$reputationStatus = $this->reputation->repGiven($answer['question_id'], $answer['id'])['type']; 
				$repDisabled = null; $repPositive = null; $repNegative = null;
				switch($reputationStatus){
					case 'disabled': 	$repDisabled = 'disabled'; 								break;
					case '+': 			$repPositive = 'style="color:#3c763d !important;"'; 	break;
					case '-': 			$repNegative = 'style="color:#a94442 !important;"'; 	break;
				}
			?>
			
			<div class="summary" >
				<div class="votes">
					<a class="<?=$repDisabled?>" href="<?=$this->url->create('reputation/rep/'.$this->reputation->buildRepString('+',$answer['member_id'],$answer['question_id'], $answer['id']))?>" title="This answer shows research effort; it is useful and clear"><i <?=$repPositive?> class="glyphicon glyphicon-triangle-top"></i></a>
					<div><?=$answer['reputation']?></div>
					<a class="<?=$repDisabled?>" href="<?=$this->url->create('reputation/rep/'.$this->reputation->buildRepString('-',$answer['member_id'],$answer['question_id'], $answer['id']))?>" title="This answer does not show any research effort; it is unclear or not useful"><i <?=$repNegative?> class="glyphicon glyphicon-triangle-bottom"></i></a>
				</div>
				<div class="wrap-container">
					<div><?=$this->textFilter->doFilter($answer['content'], 'shortcode, markdown')?></div>
					<div class="started right">answered <?= time_elapsed_string('@'.$answer['timestamp']);?> ago <a href="<?=$this->url->create('users/id/'.$answer['member_id'])?>"><?=$answer['member_name']?></a></div>
				</div>
			</div>
			<div class="comment-area">
				<?php foreach ($comments as $comment) : ?>
					<?php $comment = $comment->getProperties(); ?>
					<?php if($comment['answer_id'] == $answer['id']): ?>
						<div class="comment"><?=$this->textFilter->doFilter($comment['content'], 'shortcode, markdown')?> –  <a href="<?=$this->url->create('users/id/'.$comment['member_id'])?>"><?=$comment['member_name']?></a> <span class="started"><?= time_elapsed_string('@'.$comment['timestamp']);?> ago</span></div>
					<?php endif; ?>
				<?php endforeach; ?>
				<div class="add-comment">add a comment</div>
				<div class="comment-container">
					<?=
						$this->dispatcher->forward([
							'controller' => 'comment',
							'action'     => 'create',
							'params'	 => ['qId' => $answer['question_id'], 'aId' => $answer['id']],
						]);
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<?php else : ?>
			<?=$answers;?>
	<?php endif; ?>
</div>

