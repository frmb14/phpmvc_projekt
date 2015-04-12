	
<h2>Used tags</h2>
<p>These tags are used on Bear Teh Papur - Guardian Druid Overflow</p>
<?php if (is_array($tags)) : ?>

	<?php foreach ($tags as $tag) : ?>
	<?php $tag = $tag->getProperties(); ?>
	<a href="<?=$this->url->create('question/tagged/'.$tag['tag'])?>"><span title="<?=$tag['Used']?>" class="label label-info"><?=$tag['tag']?></span></a>
	<?php endforeach; ?>
<?php else : ?>
	<?=$tags;?>
<?php endif; ?>
