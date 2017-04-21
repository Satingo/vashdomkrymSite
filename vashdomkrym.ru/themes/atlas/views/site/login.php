<?php
$this->pageTitle=Yii::app()->name . ' - '.Yii::t('common','Login');
$this->breadcrumbs=array(
	Yii::t('common','Login')
);
?>

<h1 class="title highlight-left-right">
	<span><?php echo Yii::t('common', 'Login'); ?></span>
</h1>
<div class="clear"></div><br />

<?php if(demo()):?>
	<div class="row buttons demo-auth-buttons" style="padding: 10px 0 20px 0;">
		<p>
			<a href="#" class="button-blue" onclick="demoLogin(); return false;"><?php echo tc('Log in as user'); ?></a>&nbsp;
			<?php //echo tc('or'); ?>
			<a href="#" class="button-blue" onclick="adminLogin(); return false;"><?php echo tc('log in as administrator'); ?></a>&nbsp;
			<?php if (issetModule('rbac')):?>
				<?php //echo tc('or'); ?>
				<a href="#" class="button-blue" onclick="moderatorLogin(); return false;"><?php echo tc('log in as moderator'); ?></a>&nbsp;
			<?php endif;?>
		</p>
	</div>
<?php endif; ?>

<p><?php echo Yii::t('common', 'Already used our services? Please fill out the following form with your login credentials'); ?>:</p>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>false,
	/*'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),*/
)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username', array('class' => 'width240')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password', array('class' => 'width240')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<?php if ($model->scenario == 'withCaptcha' && CustomCCaptcha::checkRequirements()): ?>
		<div class="row">
			<?php echo $form->labelEx($model, 'verifyCode');?>
			<?php echo $form->textField($model, 'verifyCode',array('autocomplete' => 'off'));?><br/>
			<?php echo $form->error($model, 'verifyCode');?>
			<?php $this->widget('CustomCCaptcha',
				array(
					'captchaAction' => '/site/captcha',
					'buttonOptions' => array('class' => 'get-new-ver-code'),
					'clickableImage' => true,
					'imageOptions'=>array('id'=>'login_captcha')
				)
			); ?><br />
		</div>
	<?php endif; ?>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton(Yii::t('common', 'Login'), array('class' => 'button-blue')); ?>
	</div>
	
	<div class="row">
		<?php if(param('useUserRegistration')): ?> <?php echo CHtml::link(tt("Join now"), 'register'); ?> | <?php endif; ?> <?php echo CHtml::link(tt("Forgot password?"), 'recover'); ?>
	</div>
	
<?php $this->endWidget(); ?>
</div><!-- form -->

<?php if(issetModule('socialauth')) :?>
	<?php $this->widget('ext.eauth.EAuthWidget', array('action' => 'site/login')); ?>
<?php endif;?>

<?php

if(demo()){
	Yii::app()->clientScript->registerScript('login-js', '
		function demoLogin(){
			login("demore@monoray.net", "demo");
		}

		function adminLogin(){
			login("adminre@monoray.net", "admin");
		}

		function moderatorLogin(){
			login("moderatorre@monoray.net", "moderator");
		}

		function login(username, password){
			$("#LoginForm_username").val(username);
			$("#LoginForm_password").val(password);
			$("#login-form").submit();
		}
	', CClientScript::POS_END);
}