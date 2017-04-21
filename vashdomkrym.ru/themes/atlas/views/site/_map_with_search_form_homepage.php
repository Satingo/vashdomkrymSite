<div class="index-page-search-form-with-map">
	<div id="index-page-map">
		<?php Yii::app()->controller->renderPartial('//site/_map_homepage', array('selectedIds' => $selectedIds)); ?>
	</div>
	
	<div class="index-page-search-form">
		<?php Yii::app()->controller->renderPartial('//site/inner-search', array('showHideFilter' => false)); ?>
	</div>
</div>