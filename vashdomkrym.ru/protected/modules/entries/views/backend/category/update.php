<?php
$this->breadcrumbs=array(
	tt('Edit category', 'entries')
);

$this->menu=array(
	//array('label'=> tt('Entries', 'entries'), 'url'=>array('/entries/backend/main/admin')),
	array('label'=> tt('Categories of entries', 'entries'), 'url'=>array('/entries/backend/category/admin')),
);

$this->adminTitle = tt('Edit category', 'entries');

$this->renderPartial('_form', array(
	'model' => $model,
));
?>