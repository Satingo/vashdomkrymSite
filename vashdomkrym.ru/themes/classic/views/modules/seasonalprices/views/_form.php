<?php $showPricesTable = (isset($showPricesTable)) ? $showPricesTable : true;?>
<?php $showAddButton = (isset($showAddButton)) ? $showAddButton : true;?>
<?php $showHelp = (isset($showHelp)) ? $showHelp : true;?>
<?php $setDatepickerDate = (isset($setDatepickerDate)) ? $setDatepickerDate : false;?>
<?php $datepickerDateStart = (isset($datepickerDateStart)) ? $datepickerDateStart : '';?>
<?php $datepickerDateEnd = (isset($datepickerDateEnd)) ? $datepickerDateEnd : '';?>

<div class="rowold padding-bottom10 seasonal-prices-add-block">
	<?php if ($showHelp):?>
		<div class="alert in alert-block fade alert-info">
			<?php echo tt('seasonalprices_help_full', 'seasonalprices');?>
		</div>
	<?php endif;?>

	<div class="rowold">
		<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $seasonalPricesModel,
			'field' => 'name',
			'type' => 'string'
		));
		?>
	</div>

	<div class="rowold">
		<?php echo $form->hiddenField($seasonalPricesModel, 'date_start_formatting');?>
		<?php echo $form->hiddenField($seasonalPricesModel, 'date_end_formatting');?>

		<?php echo CHtml::activeLabel($seasonalPricesModel, 'dateStart', array('class' => 'noblock')); ?> /
		<?php echo CHtml::activeLabel($seasonalPricesModel, 'dateEnd', array('class' => 'noblock')); ?>

		<br />

		<?php
		$this->widget('application.modules.seasonalprices.extensions.FFJuiDatePicker', array(
			'model'=>$seasonalPricesModel,
			'attribute' => 'dateStart',
			'range' => 'eval_period_elem_add',
			'language' => Yii::app()->controller->datePickerLang,

			'options'=>array(
				'showAnim' => 'fold',
				'dateFormat' => "dd, MM",
				//'minDate'=>'new Date()',
				'changeYear' => false
			),
			'htmlOptions'=>array(
				'class' => 'width100',
				'readonly' => 'true',
				'setDatepickerDate' => $setDatepickerDate,
				'datepickerDateStart' => $datepickerDateStart,
				'datepickerDateEnd' => $datepickerDateEnd,
			),
		));
		?>
		/
		<?php
		$this->widget('application.modules.seasonalprices.extensions.FFJuiDatePicker', array(
			'model'=>$seasonalPricesModel,
			'attribute'=>'dateEnd',
			'range' => 'eval_period_elem_add',
			'language' => Yii::app()->controller->datePickerLang,

			'options'=>array(
				'showAnim' => 'fold',
				'dateFormat' => "dd, MM",
				//'minDate' => 'new Date()',
				'changeYear' => false
			),
			'htmlOptions'=>array(
				'class' => 'width100',
				'readonly' => 'true',
				'setDatepickerDate' => $setDatepickerDate,
				'datepickerDateStart' => $datepickerDateStart,
				'datepickerDateEnd' => $datepickerDateEnd,
			),
		));
		?>
	</div>

	<div class="rowold">
		<?php
		echo $form->labelEx($seasonalPricesModel,'price');
		echo $form->textField($seasonalPricesModel, 'price', array('class' => 'width100'));

		if(issetModule('currency')){
			echo '&nbsp;' . Currency::getDefaultCurrencyName();
			$seasonalPricesModel->in_currency = Currency::getDefaultCurrencyModel()->char_code;
			echo $form->hiddenField($seasonalPricesModel, 'in_currency');
		} else {
			echo '&nbsp;'.param('siteCurrency', '$');
		}

		$priceArray = HApartment::getPriceArray($apartment->type);
		if(!in_array($apartment->price_type, array_keys($priceArray))){
			$apartment->price_type = Apartment::PRICE_PER_HOUR;
		}
		$seasonalPricesModel->price_type = $apartment->price_type;
		echo '&nbsp;'.$form->dropDownList($seasonalPricesModel, 'price_type', HApartment::getPriceArray($apartment->type), array('class' => 'width150', 'onchange' => 'changeRentalPeriodTitle(this.value);'));

		echo $form->error($seasonalPricesModel,'price'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($seasonalPricesModel,'min_rental_period'); ?>
		<?php echo $form->textField($seasonalPricesModel, 'min_rental_period', array('class' => 'width50'));?>&nbsp;<span id="rental_period_title"><?php echo tt('month', 'seasonalprices');?></span>
		<?php echo $form->error($seasonalPricesModel, 'min_rental_period'); ?>
	</div>

	<?php if ($showAddButton):?>
		<div class="rowold">
			<?php echo CHtml::button(tc('Add'), array('id' => 'seasonalprices-save', 'class' => 'seasonalprices-save btn btn-primary'))?>
		</div>
	<?php endif;?>

	<br/>

	<div id="status-save"></div>
	<div id="date_error" class="errorMessage" style="display: none;"><?php echo tt('Fill fields', 'seasonalprices'); ?></div>

	<?php if ($showPricesTable):?>
		<br />

		<div class="rowold padding-bottom10">
			<?php $this->renderPartial('//modules/seasonalprices/views/_table', array('apartment' => $apartment, 'showDeleteButton' => true));?>
		</div>
	<?php endif;?>
</div>

<script>
	function changeRentalPeriodTitle(priceTypeVal) {
		var rentalPricesArr = $.parseJSON('<?php echo CJSON::encode(Seasonalprices::rentalPeriodNames());?>');
		$("#rental_period_title").html(rentalPricesArr[priceTypeVal]);
	}

	function hideMessageDb(elem) {
		elem.removeClass("status-save-success").removeClass("status-save-error").html("");
	}

	var postData = {};

	$(document).ready(function() {
		changeRentalPeriodTitle($("#Seasonalprices_price_type").val());

		$("#seasonalprices-save").click(function(){
			$(".seasonal-prices-add-block").find('input[type=text]').serializeArray().map(function(x){ postData[x.name] = x.value;});
			$(".seasonal-prices-add-block").find('input[type=hidden]').serializeArray().map(function(x){ postData[x.name] = x.value;});
			$(".seasonal-prices-add-block").find('select').serializeArray().map(function(x){ postData[x.name] = x.value;});
			postData['<?php echo Yii::app()->request->csrfTokenName;?>'] = '<?php echo Yii::app()->request->csrfToken;?>';

			$.ajax({
				success: function(dataResult){
					if (dataResult.msg == "ok") {
						$('#date_error').hide();
						$("#status-save").addClass('status-save-success').html('<?php echo tt("Success save", "seasonalprices") ?>');
						$.fn.yiiGridView.update('apartment-seasonal-prices-grid');
					}
					else if (dataResult.msg == "access_error") {
						$("#status-save").addClass('status-save-error').html('<?php echo tt("Access denied", "seasonalprices") ?>');
					}
					else if (dataResult.msg == "error_filling") {
						$('#date_error').show();
						$('#date_error').html(dataResult.msg_full);
					}
					else if (dataResult.msg == "error_save"){
						$("#status-save").addClass('status-save-error').html('<?php echo tt("Save error", "seasonalprices") ?>');
					}
					else {
						document.location.href = dataResult.msg;
					}
					setTimeout('hideMessageDb($("#status-save"))', 3000);
				},
				type: 'post',
				url: '<?php echo Yii::app()->controller->createUrl("/seasonalprices/main/saveprice", array('apId' => $apartment->id)) ;?>',
				data: postData,
				cache: false,
				dataType: 'json'
			});
		});
	});
</script>