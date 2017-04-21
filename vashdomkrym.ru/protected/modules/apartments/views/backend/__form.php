<?php $this->renderPartial('//../modules/apartments/views/backend/__form_general', array('model' => $model, 'form' => $form, 'seasonalPricesModel' => $seasonalPricesModel));?>

<div class="tab-pane" id="tab-extended">
	<?php
	if ($model->is_free_to == '0000-00-00') {
		$model->is_free_to = '';
	}

    if (Yii::app()->user->checkAccess('backend_access')) { ?>
	<div class="rowold">
		<?php echo $form->checkboxRow($model, 'is_special_offer'); ?>
	</div>
	<?php
    }

    if (Yii::app()->user->checkAccess('backend_access')) { ?>
	<div class="special-calendar">
		<?php echo $form->labelEx($model, 'is_free_to', array('class' => 'noblock')); ?><br/>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model' => $model,
			'attribute' => 'is_free_to',
			'language' => Yii::app()->controller->datePickerLang,

			'options' => array(
				'showAnim' => 'fold',
				'dateFormat' => 'yy-mm-dd',
				'minDate' => 'new Date()',
			),
			'htmlOptions' => array(
				'class' => 'width100 eval_period'
			),
		));
		?>
		<?php echo $form->error($model, 'is_free_to'); ?>
	</div>

	<?php
    }

	if (!isset($element)) {
		$element = 0;
	}

	if (issetModule('bookingcalendar') && $model->active != Apartment::STATUS_DRAFT) {
		$this->renderPartial('//modules/bookingcalendar/views/_form', array('apartment' => $model, 'element' => $element));
	}

    $rows = HFormEditor::getExtendedFields();
    HFormEditor::renderFormRows($rows, $model, $form);

    ?>

</div>

	<?php

	/*if ($model->isNewRecord) {
		echo '<p>' . tt('After pressing the button "Create", you will be able to load photos for the listing and to mark the property on the map.', 'apartments') . '</p>';
	}*/

	if (Yii::app()->user->checkAccess('backend_access')) {
		$this->widget('bootstrap.widgets.TbButton',
			array('buttonType' => 'submit',
				'type' => 'primary',
				'icon' => 'ok white',
				'label' => $model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'),
				'htmlOptions' => array(
					'onclick' => "$('#Apartment-form').submit(); return false;",
				)
			));
	} else {
		echo '<div class="row buttons save">';
		echo CHtml::button($model->isNewRecord ? Yii::t('common', 'Create') : Yii::t('common', 'Save'), array(
			'onclick' => "$('#Apartment-form').submit(); return false;", 'class' => 'big_button button-blue',
		));
		echo '</div>';
	}
?>


