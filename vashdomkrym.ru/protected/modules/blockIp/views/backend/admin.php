<?php
$this->menu = array(
	array('label'=>tt('Add blockIp', 'blockIp'), 'url'=>array('/blockIp/backend/main/create')),
);
$this->adminTitle = tt('Manage blockIp', 'blockIp');

echo "<div class='flash-notice'>".tt('help_admin', 'blockIp')."</div> <br />";

?>

<div class="form">
	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableClientValidation'=>false,
	)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model, 'deleteIpAfterDays'); ?>
		<?php echo $form->textField($model, 'deleteIpAfterDays', array('size' => 3, 'class' => 'span1')); ?>&nbsp;<?php echo tt('days', 'blockIp');?>
		<?php echo $form->error($model, 'deleteIpAfterDays'); ?>
	</div>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'label'=> tc('Save'),
			)
		); ?>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->
<br />

<?php
$this->widget('CustomGridView', array(
	'id'=>'block-ip-grid',
	'dataProvider'=>$model->search(),
	//'filter'=>$model,
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
			'id'=>'itemsSelected',
			'selectableRows' => '2',
			'htmlOptions' => array(
				'class'=>'center',
			),
		),
		array(
			'header' => tt('IP', 'blockIp'),
			'name' => 'ip',
			'filter' => false,
			'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/blockIp/backend/main/itemsSelected',
	'id' => 'block-ip-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>