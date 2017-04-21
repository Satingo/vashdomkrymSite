<?php $this->beginContent('//layouts/main'); ?>
		<?php if($this->showSearchForm):?>
			<?php Yii::app()->controller->renderPartial('//site/inner-search'); ?>
		<?php endif; ?>

		<?php if(issetModule('advertising')) :?>
			<?php $this->renderPartial('//modules/advertising/views/advert-top', array());?>
		<?php endif; ?>

		<div class="clear"></div>
	</div> <!-- /bg ( from layouts/main.php ) -->

	<div class="content">
		<?php if($this->showSearchForm):?>
			<a href="javascript: void(0);" id="inner_open_button" <?php if(!param("useCompactInnerSearchForm", false)) echo 'style="display:none;"' ?>>
				<div class="hide_filtr collapsed">
					<div class="filtr-label"><?php echo tc('Search form');?></div>
				</div>
			</a>
		<?php endif; ?>

		<div class="<?php echo ($this->htmlPageId == 'viewlisting') ? 'item' : 'content_entries';?>">
			<?php if(isset($this->breadcrumbs) && $this->breadcrumbs):?>
				<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'homeLink' => CHtml::link(Yii::t('zii','Home'),Yii::app()->homeUrl, array('class' => 'path')),
					'links'=>$this->breadcrumbs,
					'separator' => ' / ',
					'activeLinkTemplate' => '<a class="path" href="{url}">{label}</a>',
					'inactiveLinkTemplate' => '<a href="javascript: void(0);">{label}</a>',
				));
				?>
			<?php endif; ?>

			<div class="main-content-wrapper">
				<?php
					foreach(Yii::app()->user->getFlashes() as $key => $message) {
						if ($key=='error' || $key == 'success' || $key == 'notice'){
							echo "<div class='flash-{$key}'>{$message}</div>";
						}
					}
				?>
				<?php echo $content; ?>
			</div>
		</div>
	</div>
<?php $this->endContent(); ?>