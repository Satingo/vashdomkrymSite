<?php
	$this->pageTitle .= ' - '.$categoryTitle.' - '.CHtml::encode($model->getStrByLang('title'));
	$this->breadcrumbs=array(
		$categoryTitle => array('/'.$linkToCategory),
		truncateText(CHtml::encode($model->getStrByLang('title')), 10),
	);
	
	$src = '';
?>

<div>
	<div class="title highlight-left-right">
		<span><h1><?php echo CHtml::encode($model->getStrByLang('title'));?></h1></span>
	</div>
<div class="clear"></div><br />

<div class="block_new">
	<?php if($model->image) : ?>
		<?php $src = $model->image->getFullThumbLink(); ?>
		<?php if($src) : ?>
				<div class="entries-image">
					<?php
					$htmlOptions = array();
					$htmlOptions['class'] = 'fancy';					
					echo CHtml::link(CHtml::image($src, $model->getStrByLang('title')), $model->image->fullHref(), $htmlOptions);
					?>
				</div>
				<div class="clear"></div><br /><br />
		<?php endif; ?>
	<?php endif; ?>

		<div class="entry-page-body">
	<?php echo $model->body;?>
</div>
	</div><div class="clear"></div>

<?php if ($model->tags):?>
<div class="block_new entries-tags">
	<strong><?php echo tt('Tags', 'entries');?>:</strong>&nbsp;<?php echo implode(', ', $model->tagLinks); ?>
		</div><div class="clear"></div>
	<?php endif;?>
</div>

<?php if(param('enableCommentsForEntries', 1)): ?>
		<?php
		$this->widget('application.modules.comments.components.commentListWidget', array(
			'model' => $model,
			'url' => $model->getUrl(),
			'showRating' => false,
		));
		?>
<?php endif; ?>
<?php if (param('useSchemaOrgMarkup')) {
	$dateCreated = new DateTime($model->date_created);
	$dateUpdated = new DateTime($model->date_updated);
	$hostname = IdnaConvert::checkDecode(Yii::app()->getRequest()->getHostInfo());
	
	$jsonLD = array();
	$jsonLD['@context'] = 'http://schema.org';
	$jsonLD['@type'] = 'NewsArticle';
	$jsonLD['mainEntityOfPage'] = array(
		'@type' => 'WebPage',
		'@id' => $model->getUrl(),
	);
	$jsonLD['headline'] = CHtml::encode($model->getStrByLang('title'));
	
	if ($src) {
		$jsonLD['image'] = array(
			'@type' => 'ImageObject',
			'url' => $src,
			'height' => EntriesImage::FULL_THUMB_HEIGHT,
			'width' => EntriesImage::FULL_THUMB_WIDTH
		);
	}
	
	$jsonLD['datePublished'] = $dateCreated->format('c');
	$jsonLD['dateModified'] = $dateUpdated->format('c');
	$jsonLD['author'] = array(
		'@type' => 'Person',
		'name' => CHtml::encode(User::getAdminName())
	);
	$jsonLD['publisher'] = array(
		'@type' => 'Organization',
		'name' => CHtml::encode(Yii::app()->name),
		'logo' => array(
			'@type' => 'ImageObject',
			'url' => $hostname.Yii::app()->theme->baseUrl.'/images/pages/logo-open-ore.png',
			'width' => 276,
			'height' => 60
		)
	);
	$jsonLD['description'] = strip_tags($model->body);
	echo '<script type="application/ld+json">'.  CJavaScript::jsonEncode($jsonLD).'</script>';
}