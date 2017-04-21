<?php
$this->breadcrumbs=array(
	tt('Categories of entries', 'entries'),
);

$this->menu=array(
	//array('label' => tt('Entries', 'entries'), 'url'=>array('/entries/backend/main/admin')),
	array('label' => tt('Add category', 'entries'), 'url'=>array('/entries/backend/category/create')),
);

$this->adminTitle = tt('Categories of entries', 'entries');

$this->widget('CustomGridView', array(
	'id'=>'categories-entries-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable();}',
	'rowCssClassExpression'=>'"items[]_{$data->id}"',
	'rowHtmlOptionsExpression' => 'array("data-bid"=>"items[]_{$data->id}")',
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
			'header' => tc('Name'),
			'name' => 'name_'.Yii::app()->language,
			'sortable' => false,
		),
		array(
			'header' => tt('Link', 'menumanager'),
			'value' => '$data->getUrl()',
			'sortable' => false,
			'filter' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{up} {down} {update} {delete}',
			'deleteConfirmation' => tt('All materials for this category will be deleted. Are you sure?', 'entries'),
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'buttons' => array(
				'up' => array(
					'label' => tc('Move an item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/entries/backend/category/move", array("id"=>$data->id, "direction" => "up"))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > "'.$minSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'categories-entries-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/entries/backend/category/move", array("id"=>$data->id, "direction" => "down"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < "'.$maxSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'categories-entries-grid'); return false;}",
				),
			),
			'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").html(data); }'
		),
	),
)); ?>

<?php
$this->renderPartial('//site/admin-select-items', array(
	'url' => '/entries/backend/category/itemsSelected',
	'id' => 'categories-entries-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
