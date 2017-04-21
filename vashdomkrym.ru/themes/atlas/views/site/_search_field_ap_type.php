<div class="<?php echo $divClass; ?>">
    <?php if($this->searchShowLabel){ ?>
	<div class="<?php echo $textClass; ?>"><?php echo tt('Search in section', 'common'); ?>:</div>
    <?php } ?>
	<div class="<?php echo $controlClass; ?>">
		<?php
		$data = SearchForm::apTypes();

		echo CHtml::dropDownList(
			'apType',
			isset($this->apType) ? CHtml::encode($this->apType) : '',
			$data['propertyType'],
			array('class' => $fieldClass)
		);

		Yii::app()->clientScript->registerScript('ap-type-init', '
				focusSubmit($("select#apType"));
			', CClientScript::POS_READY);
		?>
	</div>
</div>
