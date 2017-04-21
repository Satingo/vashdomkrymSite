<div class="form min-fancy-width <?php echo (isset($isFancy) && $isFancy) ? 'white-popup-block' : ''; ?>">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->controller->createUrl('/comments/main/writeComment'),
		'enableAjaxValidation'=>false,
	)); ?>
	<h2 class="title highlight-left-right">
		<span><?php echo Yii::t('module_comments','Leave a Comment'); ?></span>
	</h2>
	<div class="clear"></div><br />

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo $form->errorSummary($model); ?>

	<?php if(Yii::app()->user->isGuest){ ?>
	<div class="row">
		<?php echo $form->labelEx($model,'user_name'); ?>
		<?php echo $form->textField($model, 'user_name', array('class' => 'width200')); ?>
		<?php echo $form->error($model,'user_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_email'); ?>
		<?php echo $form->textField($model, 'user_email', array('class' => 'width200')); ?>
		<?php echo $form->error($model,'user_email'); ?>
	</div>
	<?php } ?>

	<div class="row">
		<?php echo $form->labelEx($model,'body'); ?>
		<?php echo $form->textArea($model,'body',array('rows'=>3, 'cols'=>50, 'class' => 'width500')); ?>
		<?php echo $form->error($model,'body'); ?>
	</div>

	<?php if($model->enableRating){ ?>
	<div class="clear"></div>
	<div class="row">
		<?php echo $form->labelEx($model,'rating'); ?>
		<?php $this->widget('CStarRating',array('name'=>'CommentForm[rating]', 'value'=>$model->rating, 'resetText' => tt('Remove the rate', 'comments'))); ?>
		<?php echo $form->error($model,'rating'); ?>
	</div>
	<div class="clear"></div>
	<?php } ?>

	<?php if(Yii::app()->user->isGuest || param('useCaptchaCommentsForRegistered', 1)){ ?>
		<br />
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
		<?php $this->widget('CustomCCaptcha',
			array(
				'captchaAction' => '/comments/main/captcha',
				'buttonOptions' => array('class' => 'get-new-ver-code'),
				'clickableImage' => true,
			)
		);?><br/>
	<?php } ?>

	<div class="row buttons">
		<?php
			echo $form->hiddenField($model, 'url');
			echo $form->hiddenField($model, 'rel');
			echo $form->hiddenField($model, 'modelName');
			echo $form->hiddenField($model, 'modelId');

			echo CHtml::submitButton(Yii::t('common', 'Add'), array('class' => 'button-blue'));
		?>
	</div>
	<?php $this->endWidget(); ?>
</div>