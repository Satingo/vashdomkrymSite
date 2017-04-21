<?php

$this->menu = array(
	array('label' => EntriesModule::t('Add entry'), 'url' => array('create')),
	array('label' => EntriesModule::t('Edit entry'), 'url' => array('update', 'id' => $model->id)),
	array('label' => tt('Delete entry', 'entries'), 'url' => '#',
		'url'=>'#',
		'linkOptions'=>array(
			'submit'=>array('delete','id'=>$model->id),
			'confirm'=> tt('Are you sure you want to delete this item?')
		),
	),

);

$this->renderPartial('//modules/entries/views/view', array(
	'model' => $model,
));