<?php $this->beginContent('//layouts/main'); ?>
	<div id="homeheader">
		<div class="slider-wrapper theme-default">
			<div id="slider" class="nivoSlider">
				<?php if (isset(Yii::app()->controller->imagesForIndexPageSlider) && $imgCount = (count(Yii::app()->controller->imagesForIndexPageSlider))):?>
					<?php foreach(Yii::app()->controller->imagesForIndexPageSlider as $img):?>
						<?php if (isset($img['url']) && $img['url']) :?>
							<a href="<?php echo $img['url'];?>" target="_blank">
						<?php endif;?>

						<?php if (isset($img['title']) && $img['title']):?>
							<img src="<?php echo $img['src'];?>" alt="" width="<?php echo $img['width'];?>" height="<?php echo $img['height']+30;?>" title="<?php echo CHtml::encode($img['title']);?>" />
						<?php else:?>
							<img src="<?php echo $img['src'];?>" alt="" width="<?php echo $img['width'];?>" height="<?php echo $img['height']+30;?>" />
						<?php endif;?>

						<?php if (isset($img['url']) && $img['url']) :?>
							</a>
						<?php endif;?>
					<?php endforeach;?>
				<?php endif;?>
			</div>
        </div>

		<?php
			Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/js/slider/themes/default/default.css');
			Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl.'/js/slider/nivo-slider.css');

			Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/slider/jquery.nivo.slider.js', CClientScript::POS_END);
			Yii::app()->clientScript->registerScript('slider', '
				$("#slider").nivoSlider({effect: "random", randomStart: true});
			', CClientScript::POS_READY);
		?>
		<div id="homeintro">
            <?php Yii::app()->controller->renderPartial('//site/index-search'); ?>
		</div>
	</div>

	<?php
		if(issetModule('advertising')) {
			$this->renderPartial('//modules/advertising/views/advert-top', array());
		}
	?>
	<!-- FSAN Banner -->
	<div id="fsan-banner">
		<script src="http://vashdomkrym.alldeluxe.ru/get_banner?tip=3&color=007bc2" type="text/javascript"></script>
	</div>
	<!-- FSAN Banner end -->
	<div class="main-content">
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
<?php $this->endContent(); ?>
