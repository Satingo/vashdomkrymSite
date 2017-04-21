<?php
$dataProvider = new CArrayDataProvider(array());
if (isset($apartment) && isset($apartment->seasonalPrices) && $apartment->seasonalPrices) {
	$dataProvider = new CArrayDataProvider('Seasonalprices');
	$dataProvider->setData($apartment->seasonalPrices);
	$dataProvider->setTotalItemCount(count($apartment->seasonalPrices));
}
?>

<?php
$CGridViewClass = (param('useBootstrap', false)) ? 'CustomGridView' : 'NoBootstrapGridView';
$CButtonClass = (param('useBootstrap', false)) ? 'bootstrap.widgets.TbButtonColumn' : 'CButtonColumn';
$javaScriptMethod = (param('useBootstrap', false)) ? 'ajaxMoveRequest' : 'ajaxRequest';

$columns = array(
	array(
		'header' => tt('Name', 'seasonalprices'),
		'name' => 'name_'.Yii::app()->language,
		'value' => 'CHtml::encode($data->{"name_".Yii::app()->language})',
		'sortable' => false,
		'filter' => false,
		'htmlOptions' => array('style'=>'width:120px;'),
	),
	array(
		'header' => tt('Price', 'seasonalprices'),
		'name' => 'price',
		'value' => '$data->priceWithType',
		'sortable' => false,
		'filter' => false,
	),
	array(
		'header' => tt('Min_rental_period', 'seasonalprices'),
		'name' => 'min_rental_period_with_type',
		'sortable' => false,
		'filter' => false,
	),
	array(
		'header' => tt('From', 'seasonalprices'),
		'name' => 'dateStart',
		'sortable' => false,
		'filter' => false,
		'htmlOptions' => array('style'=>'width:90px;'),
	),
	array(
		'header' => tt('To', 'seasonalprices'),
		'name' => 'dateEnd',
		'sortable' => false,
		'filter' => false,
		'htmlOptions' => array('style'=>'width:90px;'),
	)
);

if (isset($showDeleteButton) && $showDeleteButton) {
	$columns[] = array(
		'template' => '{up} {down} {update} {delete}',
		'class' => $CButtonClass,
		'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
		'htmlOptions' => array('style'=>'width: 60px; min-width: 60px; text-align: center;'),
		'headerHtmlOptions' => array('style'=>'width: 60px; min-width: 60px;'),
		'buttons' => array(
			'up' => array(
				'label' => tc('Move an item up'),
				'imageUrl' => $url = Yii::app()->assetManager->publish(
					Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
				),
				'url'=>'Yii::app()->createUrl("/seasonalprices/main/move", array("id"=>$data->id, "direction" => "up", "objectid"=>$data->apartment_id))',
				'options' => array('class'=>'arrow_image_up', 'rel' => ''),
				'visible' => '$data->sorter > Seasonalprices::getMinSorters($data->apartment_id)',
				'click' => "js: function() { {$javaScriptMethod}($(this).attr('href'), 'apartment-seasonal-prices-grid'); return false;}",
			),
			'down' => array(
				'label' => tc('Move an item down'),
				'imageUrl' => $url = Yii::app()->assetManager->publish(
					Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
				),
				'url'=>'Yii::app()->createUrl("/seasonalprices/main/move", array("id"=>$data->id, "direction" => "down", "objectid"=>$data->apartment_id))',
				'options' => array('class'=>'arrow_image_down', 'rel' => ''),
				'visible' => '$data->sorter < Seasonalprices::getMaxSorters($data->apartment_id)',
				'click' => "js: function() { {$javaScriptMethod}($(this).attr('href'), 'apartment-seasonal-prices-grid'); return false;}",
			),
			'update' => array(
				'url'=>'Yii::app()->controller->createUrl("/seasonalprices/main/update", array("id" => $data->id))',
			),
			'delete' => array(
				'url'=>'Yii::app()->createUrl("/seasonalprices/main/deleteprice", array("id"=>$data->id, "apId" => '.$apartment->id.'))',
				'options' => array('rel' => ''),
			),
		),
	);
}

$this->widget($CGridViewClass, array(
	'id'=>'apartment-seasonal-prices-grid',
	'dataProvider' => $dataProvider,
	'emptyText' => tt('No_prices', 'seasonalprices'),
	'columns' => $columns,
	'template' => (isset($showDeleteButton) && $showDeleteButton) ? "{summary}\n{pager}\n{items}\n{pager}" : "{pager}\n{items}\n{pager}",
));
?>