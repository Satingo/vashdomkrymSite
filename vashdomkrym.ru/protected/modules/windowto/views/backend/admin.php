<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference (window to..)'),
);

$this->menu=array(
	/*array('label'=>tt('Manage reference (window to..)'), 'url'=>array('index')),
	array('label'=>tt('Add value'), 'url'=>array('create')),*/
	array('label'=>tt('Add value'), 'url'=>array('/windowto/backend/main/create')),
);

$this->adminTitle = tt('Manage reference (window to..)');

$this->widget('CustomGridView', array(
	'id'=>'windowto-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
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
			'class' => 'editable.EditableColumn',
			'header' => tc('Name'),
			'name' => 'title_'.Yii::app()->language,
			'value' => '$data->getStrByLang("title")',
			'editable' => array(
				'url' => Yii::app()->controller->createUrl('/windowto/backend/main/ajaxEditColumn', array('model' => 'WindowTo', 'field' => 'title_'.Yii::app()->language)),
				'placement' => 'right',
				'emptytext' => '',
				'savenochange' => 'true',
				'title' => tc('Name'),
				'options' => array(
					'ajaxOptions' => array('dataType' => 'json')
				),
				'success' => 'js: function(response, newValue) {
					if (response.msg == "ok") {
						message("'.tc("Success").'");
					}
					else if (response.msg == "save_error") {
						var newValField = "'.tt("Error. Repeat attempt later", 'blockIp').'";

						return newValField;
					}
					else if (response.msg == "no_value") {
						var newValField = "'.tt("Enter the required value", 'configuration').'";

						return newValField;
					}
				}',
			),
			'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{update} {delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/windowto/backend/main/itemsSelected',
	'id' => 'windowto-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>