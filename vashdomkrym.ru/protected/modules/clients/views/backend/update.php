<?php
$this->breadcrumbs=array(
	tt('Manage clients', 'clients') => array('admin'),
	tt('Update client', 'clients'),
);

$this->menu = array(
	array('label'=>tt('Manage clients', 'clients'), 'url'=>array('admin')),
	array('label'=>tt('Add client', 'clients'), 'url'=>array('create')),
	array('label'=>tt('Delete client', 'clients'), 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>tc('Are you sure you want to delete this item?'))),
);

$this->adminTitle = tt('Update client', 'clients');
?>

<?php
	if(isset($show) && $show){
		/*Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/scrollto.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScript('scroll-to','
				scrollto("'.CHtml::encode($show).'");
			',CClientScript::POS_READY
		);*/
	}

	$this->renderPartial('_form',array(
			'model'=>$model,
	));
?>