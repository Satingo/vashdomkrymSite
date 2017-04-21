<?php
$this->pageTitle .= ' - '.tt('My payments');
$this->breadcrumbs = array(
	tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
	tt('My payments'),
);

if (!isset($model->payments)){
	echo tc('You do not have payments.');
}
else {
	Yii::app()->getModule('payment');

	$columns = array(
		array(
			'name' => 'id',
			'htmlOptions' => array(
				'class' => 'id_column',
			),
		),
		array(
			'name' => tc('Status'),
			'type' => 'raw',
			'value' => '$data->returnStatusHtml()',
			'htmlOptions' => array(
				'class' => 'width240',
			),
		),
		array(
			'name' => Yii::t('module_comments', 'Apartment_id'),
			'type' => 'raw',
			'value' => '(isset($data->ad) && $data->ad->id) ? CHtml::link($data->ad->id, $data->ad->getUrl()) : tc("No")',
			'filter' => false,
			'sortable' => false,
		),
	);
	if($model->type == User::TYPE_AGENCY) {
		$columns[] = array(
			'name' => tc('Agent name'),
			'type' => 'raw',
			'value' => '(isset($data->agent) && $data->agent->id) ? $data->agent->username : tc("No")',
			'filter' => false,
			'sortable' => false,
		);
	}
	if (issetModule('tariffPlans')) {
		$columns[] = array(
			'header' => tt('Tariff_id', 'tariffPlans'),
			'name' => 'tariff_id',
			'type' => 'raw',
			'value' => '(isset($data->tariffInfo) && $data->tariffInfo->name) ? $data->tariffInfo->name : tc("No")',
			'filter' => false,
			'sortable' => false
		);
	}

	$columns[] = array(
		'header' => tc('Paid Service'),
		'type' => 'raw',
		'value' => '$data->getPaidserviceName()',
	);

	$columns[] = array(
		'header' => tc('Name of Payment system'),
		'type' => 'raw',
		'value' => '$data->paysystem->name',
	);

	$columns[] = array(
		'header' => tc('Amount'),
		'type' => 'raw',
		'value' => '$data->amount . " " . $data->currency_charcode',
		'htmlOptions' => array('style' => 'width:70px;'),
	);

	$columns[] = array(
		'header' => tc('Date created'),
		'value' => '$data->date_created',
		'type' => 'raw',
		'filter' => false,
		'htmlOptions' => array('style' => 'width:130px;'),
	);

	$this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider' => new CArrayDataProvider($model->payments),
			'columns' => $columns
		)
	);
}
?>