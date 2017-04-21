<?php

$this->menu=array(
	array('label'=>tt('Add value', 'windowto'), 'url'=>array('create')),
);

$this->adminTitle = tt('Manage reference', 'windowto');

$this->widget('CustomGridView', array(
	'id'=>'timesin-grid',
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
				'url' => Yii::app()->controller->createUrl('/timesin/backend/main/ajaxEditColumn', array('model' => 'TimesIn', 'field' => 'title_'.Yii::app()->language)),
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
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'template'=>'{update} {delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/timesin/backend/main/itemsSelected',
	'id' => 'timesin-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>
