<?php
$this->pageTitle=Yii::app()->name . ' - ' . ReviewsModule::t('Reviews_management');

$this->menu = array(
	array('label' => ReviewsModule::t('Add_feedback'), 'url' => array('create')),
);
$this->adminTitle = ReviewsModule::t('Reviews_management');
?>

<?php

$this->widget('CustomGridView', array(
	'id'=>'reviews-grid',
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
			'name' => 'user_ip',
			'value' => 'BlockIp::displayUserIP($data)',
			'headerHtmlOptions' => array('style' => 'width: 110px'),
			'editable' => array(
				'apply' => '$data->user_ip != "" && Yii::app()->user->checkAccess("blockip_admin")',
				'url' => Yii::app()->controller->createUrl('/blockIp/backend/main/ajaxAdd'),
				'placement' => 'right',
				'emptytext' => '',
				'savenochange' => 'true',
				'title' => tt('Add the IP address to the list of blocked', 'blockIp'),
				'options' => array(
					'ajaxOptions' => array('dataType' => 'json')
				),
				'onShown' => 'js: function() {
					var input = $(this).parent().find(".input-medium");

					$(input).attr("disabled", "disabled");
				}',
				'success' => 'js: function(response, newValue) {
					if (response.msg == "ok") {
						message("'.tt("Ip was success added", 'blockIp').'");
					}
					else if (response.msg == "already_exists") {
						var newValField = "'.tt("Ip was already exists", 'blockIp').'";

						return newValField;
					}
					else if (response.msg == "save_error") {
						var newValField = "'.tt("Error. Repeat attempt later", 'blockIp').'";

						return newValField;
					}
					else if (response.msg == "no_value") {
						var newValField = "'.tt("Enter Ip", 'blockIp').'";

						return newValField;
					}
				}',
			),
			'sortable' => false,
		),
		array(
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "reviews-grid", 1)',
			'htmlOptions' => array('class'=>'infopages_status_column'),
			'filter' => false,
			'sortable' => true,
		),
		array (
			'name'=>'name',
			//'htmlOptions' => array('class'=>'width120'),
			'sortable' => false,
			//'type' => 'raw',
			//'value' => 'CHtml::encode($data->name)',
			'value' => '$data->name',
		),
		'email',
		array (
			'name'=>'body',
			'sortable' => false,
			'type' => 'raw',
			'value' => 'CHtml::link(CHtml::encode(truncateText($data->body)),array("/reviews/backend/main/view","id" => $data->id))',
		),
		array (
			'name' => 'date_created',
			'headerHtmlOptions' => array('style' => 'width:130px;'),
			'filter' => false,
			'sortable' => false,
		),
		/*array (
			'name' => 'date_updated',
			'headerHtmlOptions' => array('style' => 'width:130px;'),
			'filter' => false,
			'sortable' => true,
		),*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {update} {delete}',
			'deleteConfirmation' => 'Вы действительно хотите удалить выбранный элемент?',
			'viewButtonUrl' => "Yii::app()->createUrl('/reviews/backend/main/view', array('id' => \$data->id))",
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/reviews/backend/main/itemsSelected',
	'id' => 'reviews-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));
?>