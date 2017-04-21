<?php
$this->pageTitle=Yii::app()->name . ' - ' . EntriesModule::t('Edit entry');

$this->menu = array(
    array('label' => tt('Manage entries'), 'url' => array('admin')),
	array('label' => EntriesModule::t('Add entry'), 'url' => array('create')),
	array('label' => tt('Delete entry', 'entries'),
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tc('Are you sure you want to delete this item?')
		),
	)
);
$this->adminTitle = EntriesModule::t('Edit entry').': <i>'.CHtml::encode($model->title).'</i>';
?>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>