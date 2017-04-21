<?php


$this->menu=array(
	array('label'=>tt('Manage apartment city'), 'url'=>array('admin')),
	array('label'=>tt('Add city'), 'url'=>array('/apartmentCity/backend/main/create')),
);
$this->adminTitle = tt('Add multiple cities');
?>
<div class="flash-notice"><?php echo tt('Add multiple cities help');?></div>
<div class="form">

	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model, 'multy'); ?>
		<?php echo $form->textArea($model, 'multy',array('class'=>'width500','rows'=>7)); ?>
		<?php echo $form->error($model, 'multy'); ?>
	</div>

	<div class="clear"></div>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'label'=> $model->isNewRecord ? tc('Add') : tc('Save'),
			)); ?>
	</div>

	<?php $this->endWidget(); ?>

</div><!-- form -->