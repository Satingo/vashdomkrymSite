<?php

$this->pageTitle=Yii::app()->name . ' - ' . tt('Manage lang messages', 'translateMessage');

$this->menu=array(
	array('label'=>tt('Add message'), 'url'=>array('create')),
);

$this->adminTitle = tt('Manage lang messages', 'translateMessage');

$this->widget('CustomGridView', array(
	'id'=>'translate-message-grid',
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
			'name' => 'status',
			'filter' => TranslateMessage::getStatusArray(),
			'type' => 'raw',
			'value' => '$data->getStatusHtml()',
			'htmlOptions' => array(
				'class'=>'width120',
			),
		),
		array(
			'name' => 'category',
			'filter' => TranslateMessage::getCategoryFilter(),
			'htmlOptions' => array(
				'class'=>'width200',
			),
		),
		'message',
		array(
			'class' => 'editable.EditableColumn',
			'name' => 'translation',
			'value' => '$data->getStrByLang("translation")',
			'editable' => array(
				'type' => 'textarea',
				'url' => Yii::app()->controller->createUrl('/translateMessage/backend/main/ajaxEditColumn', array('model' => 'TranslateMessage', 'field' => 'translation_'.Yii::app()->language)),
				'placement' => 'right',
				'emptytext' => '',
				'savenochange' => 'true',
				'title' => tt('Constant value (translation)', 'translateMessage'),
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
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'template'=>'{update} {delete}',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/translateMessage/backend/main/itemsSelected',
	'id' => 'translate-message-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));
?>