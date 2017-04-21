<h2><?php echo tt('Contact Us', 'contactform'); ?></h2>
<?php
	Yii::app()->clientScript->registerScriptFile('http://download.skype.com/share/skypebuttons/js/skypeCheck.js', CClientScript::POS_END);

	if(!Yii::app()->user->isGuest){
		if(!$model->name)
			$model->name = Yii::app()->user->username;
		if(!$model->phone)
			$model->phone = Yii::app()->user->phone;
		if(!$model->email)
			$model->email = Yii::app()->user->email;
	}

	if(param('adminPhone')){
		echo '<p>'.tt('Phone', 'contactform').': '.param('adminPhone').'</p>';
	}
	if(param('adminSkype')){
		$lenght = utf8_strlen(param('adminSkype'));
		$k = 15;

		if ($lenght < 5)
			$k = 25;
		if ($lenght > 10)
			$k = 11;
		if ($lenght > 20)
			$k = 10;

		$left = $lenght * $k;
		echo '<p>'.tt('Skype', 'contactform').': '.param('adminSkype').'</p>';
	}
	if(param('adminICQ')){
		echo '<p>'.tt('ICQ', 'contactform').': '.param('adminICQ').'</p>';
	}
	if(param('adminAddress')){
		echo '<p>'.tt('Address', 'contactform').': '.param('adminAddress').'</p>';
	}
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'contact-form',
	'enableClientValidation'=>false,
));
?>
	<p>
		<?php echo tt('You can fill out the form below to contact us.', 'contactform'); ?>
	</p>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name', array('size'=>60,'maxlength'=>128, 'class' => 'width240')); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email', array('size'=>60,'maxlength'=>128, 'class' => 'width240')); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone', array('size'=>60,'maxlength'=>128, 'class' => 'width240')); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>3, 'cols'=>50, 'class' => 'contact-textarea')); ?>
		<?php echo $form->error($model,'body'); ?>
	</div>

	<?php
	if (Yii::app()->user->isGuest){
	?>
		<div class="row">
			<?php echo $form->labelEx($model, 'verifyCode');?>
			<?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
			<?php echo $form->error($model, 'verifyCode');?>
			<?php
			$cAction = '/infopages/main/captcha';
			if($this->page == 'index'){
				$cAction = '/site/captcha';
			} elseif ($this->page == 'contactForm'){
				$cAction = '/contactform/main/captcha';
			}
			$this->widget('CustomCCaptcha',
				array(
					'captchaAction' => $cAction, 
					'buttonOptions' => array('class' => 'get-new-ver-code'),
					'clickableImage' => true,
					'imageOptions'=>array('id'=>'contactform_captcha'),
				)
			);?>
			<br/>
		</div>
	<?php
	}
	?>

	<div class="row buttons">
		<?php echo CHtml::submitButton(tt('Send message', 'contactform')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
