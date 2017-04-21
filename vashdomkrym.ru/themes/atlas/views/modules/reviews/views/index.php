<?php
$this->pageTitle .= ' - '.ReviewsModule::t('Reviews');
$this->breadcrumbs=array(
	ReviewsModule::t('Reviews'),
);
?>
<div class="title highlight-left-right">
	<span><h1><?php echo ReviewsModule::t('Reviews'); ?></h1></span>
</div>
<div class="clear"></div><br />

<?php if(count($reviews) > 3){ ?>
<div class="add-review-link">
	<?php echo CHtml::link(tt('Add_feedback', 'reviews'), Yii::app()->createUrl('/reviews/main/add'), array('class' => 'apt_btn fancy mgp-open-ajax link_blue'));?>
</div>
<?php } ?>

<?php
if ($reviews) : ?>
	<div id="reviews-wrap">
		<ol class="reviewslist">
			<?php foreach ($reviews as $review) :?>
				<li class="review thread-even depth-1" id="li-review-<?php echo $review->id;?>">
					<div id="review-<?php echo $review->id;?>" class="review-body clearfix">
						<span class="icon-avatar-review"></span>
						<div class="review-author vcard"><?php echo CHtml::encode($review->name); ?></div>
						<div class="review-meta reviewmetadata">
							<span class="review-date"><?php echo $review->dateCreatedFormat; ?></span>
						</div>
						<div class="review-inner">
							<p><?php echo CHtml::encode($review->body); ?></p>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>

<?php endif; ?>

<?php if(!$reviews) : ?>
	<div><?php echo tt('Review list is empty');?></div>
<?php endif; ?>

	<div class="add-review-link">
		<?php echo CHtml::link(tt('Add_feedback', 'reviews'), Yii::app()->createUrl('/reviews/main/add'), array('class' => 'apt_btn fancy mgp-open-ajax link_blue'));?>
	</div>

<?php if($pages && $pages->pageCount > 1):?>
	<div class="pagination">
		<?php
		$this->widget('itemPaginatorAtlas',
			array(
				'pages' => $pages,
				'header' => '',
				'selectedPageCssClass' => 'current',
				'htmlOptions' => array(
					'class' => ''
				)
			)
		);
		?>
	</div>
	<div class="clear"></div>
<?php endif; ?>