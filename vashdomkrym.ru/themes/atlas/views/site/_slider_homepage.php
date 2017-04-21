<?php
	Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jssor-slider/jssor.slider.mini.js', CClientScript::POS_END);
	Yii::app()->clientScript->registerScript('jssor-slider', '
		var jssor_1_options = {
		  $AutoPlay: true,
		  $SlideDuration: 1000,
		  $Idle: 2000,
		  $SlideEasing: $Jease$.$OutQuint,
		  $ArrowNavigatorOptions: {
			$Class: $JssorArrowNavigator$
		  },
		  $BulletNavigatorOptions: {
			$Class: $JssorBulletNavigator$
		  }
		};

		var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

		/* responsive code begin */
		/* you can remove responsive code if you dont want the slider scales while window resizing */
		function ScaleSlider() {
			var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
			if (refSize) {
				refSize = Math.min(refSize, 1920);
				jssor_1_slider.$ScaleWidth(refSize);
			}
			else {
				window.setTimeout(ScaleSlider, 30);
			}
		}
		ScaleSlider();
		$(window).bind("load", ScaleSlider);
		$(window).bind("resize", ScaleSlider);
		$(window).bind("orientationchange", ScaleSlider);
		/* responsive code end */
	', CClientScript::POS_READY);
?>

<?php
$width = ($useFullWidthSlider) ? '1300px' : '663px';
$height = ($useFullWidthSlider) ? '410px' : '380px';
$sliderClass = ($useFullWidthSlider) ? 'slider-with-search-form' : 'slider-without-search-form';
?>

<div class="<?php echo $sliderClass;?>">			
	<div id="jssor_1" style="position: relative; margin: 0px auto; top: 0px; left: 0px; width: <?php echo $width;?>; height: <?php echo $height;?>; overflow: hidden; visibility: hidden;">
		<!--<div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
			<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
			<div style="position:absolute;display:block;background:url('<?php //echo Yii::app()->getBaseUrl(true);?>/images/ajax-loader-bg-3278B4.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
		</div>-->

		<div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: <?php echo $width;?>; height: <?php echo $height;?>; overflow: hidden;">
			<?php foreach($images as $img):?>
				<div data-p="225.00" style="display: none;">
					<?php if (isset($img['url']) && $img['url']) :?>
						<a href="<?php echo $img['url'];?>" target="_blank">
					<?php endif;?>

					<img data-u="image" src="<?php echo $img['src'];?>" alt="" />

					<?php if (isset($img['title']) && $img['title']):?>
						<div class="jssor-slide-text-title">
							<?php echo CHtml::encode($img['title']);?>
						</div>
					<?php endif;?>

					<?php if (isset($img['url']) && $img['url']) :?>
						</a>
					<?php endif;?>
				</div>
			<?php endforeach;?>
		</div>

		<span data-u="arrowleft" class="jssora22l" style="top:0px;left:12px;width:40px;height:58px;" data-autocenter="2"></span>
		<span data-u="arrowright" class="jssora22r" style="top:0px;right:12px;width:40px;height:58px;" data-autocenter="2"></span>
	</div>
	
	<?php if ($useFullWidthSlider):?>
		<div class="index-page-search-form">
			<?php Yii::app()->controller->renderPartial('//site/inner-search', array('showHideFilter' => false)); ?>
		</div>
	<?php endif;?>
</div>