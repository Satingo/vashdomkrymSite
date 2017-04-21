<?php


$this->menu=array(
	array('label'=>tt('Manage reference values'), 'url'=>array('admin')),
	//array('label'=>tt('Create value'), 'url'=>array('/referencevalues/backend/main/create')),
);
$this->adminTitle = tt('Create multiple reference values');
?>
<div class="flash-notice"><?php echo tt('Add multiple reference values help');?></div>
<div class="form">

	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'reference_category_id'); ?>
		<?php echo $form->dropDownList($model,'reference_category_id', $this->getCategories(1)); ?>
		<?php echo $form->error($model,'reference_category_id'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->checkboxRow($model,'for_sale'); ?>
		<?php echo $form->error($model,'for_sale'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->checkboxRow($model,'for_rent'); ?>
		<?php echo $form->error($model,'for_rent'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model, 'multy'); ?>
		<?php echo $form->textArea($model, 'multy', array('class'=>'width500','rows'=>7)); ?>
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