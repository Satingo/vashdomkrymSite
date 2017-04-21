<?php
	//			if(Yii::app()->user->isGuest && param('useUserRegistration')){
	//				echo Yii::t('module_booking', 'Already have site account? Please <a title="Login" href="{n}">login</a>',
	//					Yii::app()->controller->createUrl('/site/login')).'<br /><br />';
	//			}
	//			else{
	if(!Yii::app()->user->isGuest){
		if(!$model->username)
			$model->username = $user->username;
		if(!$model->phone)
			$model->phone = $user->phone;
		if(!$model->useremail)
			$model->useremail = $user->email;
	}
	//      	}
?>

<div class="row">
	<div class="full-multicolumn-first">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>
	<div class="full-multicolumn-second">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone'); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>
</div>
<div class="row">
	<div class="full-multicolumn-first">
		<?php echo $form->labelEx($model,'useremail'); ?>
		<?php echo $form->textField($model,'useremail'); ?>
		<?php //echo $form->error($model,'useremail'); ?>
	</div>
	<div class="full-multicolumn-second">
		<?php if (!$isSimpleForm):?>
			<?php echo $form->labelEx($model,'num_guest'); ?>
			<?php echo $form->dropDownList($model,'num_guest',Booking::getNumGuestList()); ?>
			<?php //echo $form->error($model,'num_guest'); ?>
		<?php endif;?>
	</div>
</div>

<?php if ($isSimpleForm) { echo '<div id="rent_form">'; } ?>
<?php
	$useBookingCalendar = false;
	if(issetModule('bookingcalendar')) {
		$useBookingCalendar = true;
	}

	if($useBookingCalendar && isset($apartment) && $apartment) {
		$reservedDays = Bookingcalendar::getReservedDays($apartment->id);

		Yii::app()->clientScript->registerScript('reservedDays', '
			var reservedDays = '.$reservedDays.';
		', CClientScript::POS_END);
	}
?>
<div class="row">
	<div class="full-multicolumn-first">
		<?php echo $form->labelEx($model,'date_start'); ?>

		<?php
		if(!$model->date_start){
            if(issetModule('bookingcalendar') && isset($apartment) && $apartment){
                $time = Bookingcalendar::getFirstFreeDay($apartment->id);
            } else {
                $time = time();
            }

			if(Yii::app()->language != 'ru'){
				$model->date_start = date('m/d/Y', $time);
			} else {
				$model->date_start = date('d.m.Y', $time);
                //$model->date_start = Yii::app()->dateFormatter->formatDateTime($time, 'medium', null);
            }
		}
		if (!$isSimpleForm && $useBookingCalendar) {
			$this->widget('application.modules.bookingcalendar.extensions.FFJuiDatePicker', array(
				'model'=>$model,
				'attribute'=>'date_start',
				'range' => 'eval_period',
				'language' => Yii::app()->controller->datePickerLang,

				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>Booking::getJsDateFormat(),
					'minDate'=>'new Date()',
				),
				'htmlOptions' => array(
					'readonly' => 'true',
				),
			));
		}
		else {
			$this->widget('application.extensions.FJuiDatePicker', array(
				'model'=>$model,
				'attribute'=>'date_start',
				'range' => 'eval_period',
				'language' => Yii::app()->controller->datePickerLang,

				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>Booking::getJsDateFormat(),
					'minDate'=>'new Date()',
				),
				'htmlOptions' => array(
					'readonly' => 'true',
				),
			));
		}
		?>
		<?php echo $form->error($model,'date_start'); ?>
	</div>
	<div class="full-multicolumn-second">
		<?php echo $form->labelEx($model,'time_in'); ?>
		<?php echo $form->dropDownList($model,'time_in', $this->getTimesIn(), array('class' => 'width150')); ?>
		<?php echo $form->error($model,'time_in'); ?>
	</div>
</div>
<div class="row">
	<div class="full-multicolumn-first">
		<?php echo $form->labelEx($model,'date_end'); ?>
		<?php
		/*if(!$model->date_end){
			$model->date_end = Yii::app()->dateFormatter->formatDateTime(time()+60*60*24, 'medium', null);
		}*/
		if (!$isSimpleForm && $useBookingCalendar) {
			$this->widget('application.modules.bookingcalendar.extensions.FFJuiDatePicker', array(
				'model'=>$model,
				'attribute'=>'date_end',
				'range' => 'eval_period',
				'language' => Yii::app()->controller->datePickerLang,

				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>Booking::getJsDateFormat(),
					'minDate'=>'new Date()',
				),
				'htmlOptions' => array(
					'readonly' => 'true',
				),
			));
		}
		else {
			$this->widget('application.extensions.FJuiDatePicker', array(
				'model'=>$model,
				'attribute'=>'date_end',
				'range' => 'eval_period',
				'language' => Yii::app()->controller->datePickerLang,

				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>Booking::getJsDateFormat(),
					'minDate'=>'new Date()',
				),
				'htmlOptions' => array(
					'readonly' => 'true',
				),
			));
		}
		?>
		<?php echo $form->error($model,'date_end'); ?>
	</div>
	<div class="full-multicolumn-second">
		<?php echo $form->labelEx($model,'time_out'); ?>
		<?php echo $form->dropDownList($model,'time_out', $this->getTimesOut(), array('class' => 'width150')); ?>
		<?php echo $form->error($model,'time_out'); ?>
	</div>
</div>

<?php if ($isSimpleForm) { echo '</div>'; } ?>

<div class="clear"></div>
<div class="row">
	<?php echo $form->labelEx($model,'comment'); ?>
	<?php echo $form->textArea($model,'comment',array('class'=>'width500', 'rows' => '3')); ?>
	<?php echo $form->error($model,'comment'); ?>
</div>


<?php if (Yii::app()->user->isGuest) : ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
		<?php
		$cAction = '/booking/main/captcha';
		$this->widget('CustomCCaptcha',
			array(
				'captchaAction' => $cAction,
				'buttonOptions' => array('class' => 'get-new-ver-code'),
				'imageOptions'=>array('id'=>'booking_captcha'),
				'clickableImage' => true,
			)
		);?>
		<br/>
	</div>
<?php endif; ?>