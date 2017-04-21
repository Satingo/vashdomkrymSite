<?php $this->beginContent('//layouts/main'); ?>
	<?php $useAdditionalView = Yii::app()->controller->useAdditionalView; ?>

	<?php if (!$useAdditionalView):?>
		<?php Yii::app()->controller->renderPartial('//site/_slider_homepage', array('images' => Yii::app()->controller->imagesForIndexPageSlider, 'useFullWidthSlider' => false)); ?>
		<?php Yii::app()->controller->renderPartial('//site/index-search'); ?>

		<div class="clear"></div>
		</div> <!-- /bg ( from layouts/main.php ) -->
	<?php else:?>
		<div class="clear"></div>
		</div> <!-- /bg ( from layouts/main.php ) -->
		
		<?php if ($useAdditionalView == Themes::ADDITIONAL_VIEW_FULL_WIDTH_SLIDER) :?>
			<?php Yii::app()->controller->renderPartial('//site/_slider_homepage', array('images' => Yii::app()->controller->imagesForIndexPageSlider, 'useFullWidthSlider' => true)); ?>
		<?php else:?>
			<?php Yii::app()->controller->renderPartial('//site/_map_with_search_form_homepage', array('selectedIds' => Yii::app()->controller->adsForIndexMap)); ?>
		<?php endif;?>
		<div class="clear"></div>
	<?php endif; ?>

	<?php if(issetModule('advertising')) :?>
		<?php $this->renderPartial('//modules/advertising/views/advert-top', array());?>
	<?php endif; ?>

	<!-- content -->
	<div class="content">
		<div class="main-content-wrapper">
			<?php
			foreach(Yii::app()->user->getFlashes() as $key => $message) {
				if ($key=='error' || $key == 'success' || $key == 'notice'){
					echo "<div class='flash-{$key}'>{$message}</div>";
				}
			}
			?>

			<?php echo $content; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
<?php $this->endContent(); ?>