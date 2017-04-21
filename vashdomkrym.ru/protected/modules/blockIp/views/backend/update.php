<?php
$this->breadcrumbs=array(
	tt('Manage blockIp', 'blockIp')=>array('admin'),
	tt('Update blockIp', 'blockIp'),
);

$this->menu=array(
	array('label' => tt('Manage blockIp', 'blockIp'), 'url'=>array('admin')),
	array('label' => tt('Add blockIp', 'blockIp'), 'url'=>array('create')),
	array('label' => tt('Delete blockIp', 'blockIp'), 'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=>tc('Are you sure you want to delete this item?')
		)
	),
);

$this->adminTitle = tt('Update blockIp', 'blockIp');

echo $this->renderPartial('/backend/_form', array('model'=>$model));
?>