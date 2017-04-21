<?php
$this->breadcrumbs=array(
	tt('Manage clients', 'clients') => array('admin'),
	tt('Add client', 'clients'),
);
$this->menu = array(
	array('label'=>tt('Manage clients', 'clients'), 'url'=>array('admin')),
);
$this->adminTitle = tt('Add client', 'clients');
?>

<?php
	$this->renderPartial('_form',array(
		'model'=>$model,
	));
?>