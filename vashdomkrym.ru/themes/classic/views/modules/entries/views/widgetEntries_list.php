<?php
foreach ($entries as $item) : ?>
    <div class="entries-items">
        <p>
            <span class="date"><?php echo $item->dateCreated; ?></span>
        </p>

		<?php if($item->image):?>
			<?php $src = $item->image->getSmallThumbLink(); ?>
			<?php if($src) : ?>
				<div class="entries-image-list">
					<?php echo CHtml::link(CHtml::image($src, $item->getStrByLang('title')), $item->image->fullHref(), array('class' => 'fancy')); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<p>
			<span class="title"><?php echo CHtml::link(CHtml::encode($item->getStrByLang('title')), $item->getUrl()); ?></span>
		</p>
		<?php
			echo $item->getAnnounce();
		?>
        <p>
            <?php echo CHtml::link(tt('Read more &raquo;', 'entries'), $item->getUrl()); ?>
        </p>
        <div class="clear"></div>
    </div>
<?php endforeach; ?>

<?php

if(!$entries){
	echo tt('Entries list is empty.', 'entries');
}

if($pages){
	$this->widget('itemPaginator',array('pages' => $pages, 'header' => ''));
}
?>
