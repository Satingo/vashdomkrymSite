<?php
$this->pageTitle .= ' - '.tt("FAQ");
$this->breadcrumbs=array(
	tt("FAQ"),
);
?>

<div class="title highlight-left-right">
	<span><h1><?php echo tt("FAQ"); ?></h1></span>
</div>
<div class="clear"></div><br />

<div class="block_entries">
	<?php if ($articles):?>
		<div class="b_entries">
			<?php foreach ($articles as $article) : ?>
				<div class="b_entries__item b_entries__item_no_src">
					<div class="b_entries__item_post b_entries__item_post_no_src">
						<div class="title">
							<?php echo CHtml::link($article['page_title'], $article->getUrl(), array('class'=>'title')); ?>
						</div><br />
						<div class="new_desc">
							<?php echo truncateText($article['page_body'], 50); ?>
						</div>
						<?php echo CHtml::link(Yii::t('module_articles', 'Read more &raquo;'), $article->getUrl(), array('class' => 'read_more'))?>
					</div>
				</div>
				<div class="clear"></div>
			<?php endforeach; ?>
		</div>
	<?php endif;?>
</div>
<div class="clear"></div>

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