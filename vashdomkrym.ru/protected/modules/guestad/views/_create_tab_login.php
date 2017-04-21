<div class="row">
    <?php echo $form->labelEx($model,'username'); ?>
    <?php echo $form->textField($model,'username', array('autocomplete' => 'off')); ?>
    <?php echo $form->error($model,'username'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'password'); ?>
    <?php echo $form->passwordField($model,'password', array('autocomplete' => 'off')); ?>
    <?php echo $form->error($model,'password'); ?>
</div>

<?php if ($model->scenario == 'withCaptcha' && CustomCCaptcha::checkRequirements()): ?>
	<div class="row">
		<?php echo $form->labelEx($model, 'verifyCode');?>
		<?php echo $form->textField($model, 'verifyCode',array('autocomplete' => 'off'));?><br/>
		<?php echo $form->error($model, 'verifyCode');?>
		<?php $this->widget('CustomCCaptcha',
			array(
				'captchaAction' => '/guestad/main/captcha',
				'buttonOptions' => array('class' => 'get-new-ver-code'),
				'clickableImage' => true,
				'imageOptions'=>array('id'=>'login_guestad_captcha'),
			)
		); ?><br/>
	</div>
<?php endif; ?>