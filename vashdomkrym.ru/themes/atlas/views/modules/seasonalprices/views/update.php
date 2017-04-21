<?php
if(/*param('useBootstrap')*/ Yii::app()->user->checkAccess('backend_access')) { # admin panel
	$this->breadcrumbs=array(
		tt('Manage apartments') => array('admin'),
		tt('Update apartment'),
	);

	$this->menu = array(
		array('label'=>tt('Manage apartments', 'apartments'), 'url'=>array('/apartments/backend/main/admin')),
		array('label'=>tt('Update apartment', 'apartments'), 'url'=>array('/apartments/backend/main/update', 'id' => $apartment->id)),
	);

	$this->adminTitle = tt('Update seasonal price', 'seasonalprices');
	
	# for datepicker - only styles
	Yii::app()->clientScript->registerCssFile(Yii::app()->clientScript->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
}
else { # userpanel
	$this->pageTitle .= ' - '.tt('Update seasonal price', 'seasonalprices');
	$this->breadcrumbs = array(
		Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
		tt('Update seasonal price', 'seasonalprices')
	);

	$this->menu = array(
		array('label'=>tt('Manage apartments'), 'url'=>array('/userads/main/index')),
		array('label'=>tt('Update apartment'), 'url'=>array('/userads/main/update', 'id' => $apartment->id)),
	);

	$this->pageTitle = tt('Update seasonal price', 'seasonalprices');
	
	echo '<h1 class="title highlight-left-right"><span>'.tt('Update seasonal price', 'seasonalprices').'</span></h1>';
}
?>

<div class="form form-seasonalprices-update">
	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>true,
		'htmlOptions'=>array('class'=>'white_noborder')
	)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>
	<input type="hidden" name="id" id="seasonal_id_value" value="<?php echo $seasonalPricesModel->id; ?>">
	
	<?php echo $form->errorSummary($seasonalPricesModel); ?>

	<?php 
		echo $this->renderPartial('_form', 
			array(
				'seasonalPricesModel'=>$seasonalPricesModel, 
				'form' => $form, 
				'apartment' => $apartment, 
				'showPricesTable' => false, 
				'showAddButton' => false,
				'showHelp' => false,
				'setDatepickerDate' => $setDatepickerDate,
				'datepickerDateStart' => $datepickerDateStart,
				'datepickerDateEnd' => $datepickerDateEnd,
			)
		); 
	?>

	<?php if (Yii::app()->user->checkAccess('backend_access')):?>
		<div class="rowold buttons">
			<?php echo $this->widget('bootstrap.widgets.TbButton',
				array('buttonType'=>'submit',
					'type'=>'primary',
					'icon'=>'ok white',
					'label'=> tc('Save'),
					'htmlOptions' => array(
						'class' => 'btn btn-primary',
					),
				)); ?>
		</div>
	<?php else:?>
		<div class="row buttons save">
		<?php echo CHtml::submitButton(tc('Save'), array('class' => 'big_button button-blue')); ?>
		</div>
	<?php endif; ?>
	
	<?php $this->endWidget(); ?>
</div><!-- form -->