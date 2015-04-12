<div class="popular-tags well"> 	
	<h2>Popular tags</h2>
	
	<?php if (is_array($tags)) : ?>

		<?php foreach ($tags as $tag) : ?>
		<?php $tag = $tag->getProperties(); ?>
		<a href="<?=$this->url->create('question/tagged/'.$tag['tag'])?>"><span title="<?=$tag['Used']?>" class="label label-info"><?=$tag['tag']?></span></a>
		<?php endforeach; ?>
	<?php else : ?>
		<?=$tags;?>
	<?php endif; ?>
</div>