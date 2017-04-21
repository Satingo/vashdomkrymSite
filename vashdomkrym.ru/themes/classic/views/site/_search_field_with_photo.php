<div class="<?php echo $divClass; ?>">
	<span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Only with photo'); ?>:</div> </span>
	<?php
		echo CHtml::checkBox('wp', (isset($this->wp) && $this->wp) ? CHtml::encode($this->wp) : '', array(
			'class' => 'search-input-new searchField',
			'id' => 'search_with_photo'
		));
	?>
</div>
