<?php
$this->pageTitle .= ' - '.ReviewsModule::t('Reviews') .' - '.tt('Add_feedback');
$this->breadcrumbs=array(
	tt('Reviews') => array('/reviews/main/index'),
	tt('Add_feedback'),
);
?>
<div class="min-fancy-width <?php echo (isset($isFancy) && $isFancy) ? 'white-popup-block' : ''; ?>">
	<h1 class="title highlight-left-right">
		<span><?php echo tt('Add_feedback'); ?></span>
	</h1>
	<div class="clear"></div><br />

	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'action' => Yii::app()->controller->createUrl('/reviews/main/add'),
			'id'=>'commentform',
			'enableAjaxValidation'=>false,
			//'htmlOptions' => array('class' => 'bestform'),
			'clientOptions'=>array('validateOnSubmit'=>false),
		));

		if(!Yii::app()->user->isGuest){
			if($model->name == ''){
				$model->name = Yii::app()->user->getState('username');
			}
			if($model->email == ''){
				$model->email = Yii::app()->user->getState('email');
			}
		}

		?>

		<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

		<?php echo $form->errorSummary($model); ?>

			<div class="row">
				<?php echo $form->labelEx($model,'name'); ?>
				<?php echo $form->textField($model,'name', array('class' => 'width200')); ?>
				<?php echo $form->error($model,'name'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'email'); ?>
				<?php echo $form->textField($model,'email', array('class' => 'width200')); ?>
				<?php echo $form->error($model,'email'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model,'body'); ?>
				<?php echo $form->textArea($model,'body', array('class' => 'width500 height100')); ?>
				<?php echo $form->error($model,'body'); ?>
			</div>

			<div class="row">
				<?php echo $form->labelEx($model, 'verifyCode');?>
				<?php echo $form->textField($model, 'verifyCode', array('autocomplete' => 'off'));?><br/>
				<?php echo $form->error($model, 'verifyCode');?>
				<?php
					$this->widget('CustomCCaptcha',
					array(
						'captchaAction' => '/reviews/main/captcha',
						'buttonOptions' => array('class' => 'get-new-ver-code'),
						'imageOptions'=>array('id'=>'review_captcha'),
						'clickableImage' => true,
					)
				);?><br/>
			</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton(tc('Add'), array('class' => 'button-blue')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>
</div>