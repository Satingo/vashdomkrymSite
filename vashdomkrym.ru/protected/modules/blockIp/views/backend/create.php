<?php
$this->breadcrumbs=array(
	tt("Manage blockIp")=>array('admin'),
	tt("Add blockIp"),
);

$this->menu=array(
	array('label' => tt('Manage blockIp', 'blockIp'), 'url'=>array('admin')),
);

$this->adminTitle = tt("Add blockIp");

echo $this->renderPartial('/backend/_form', array('model'=>$model));
?>