<?php
$this->breadcrumbs=array(
	Yii::t('module_comments', 'Comments'),
);

$this->menu = array(
	array(),
);

$this->adminTitle = Yii::t('module_comments', 'Comments');

$this->widget('CustomGridView', array(
	'id'=>'comment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$(".rating-block input").rating({"readOnly":true}); $("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
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
			'name' => 'status',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "comment-grid")',
			'headerHtmlOptions' => array('class'=>'infopages_status_column'),
			'filter' => false,
			'sortable' => false,
		),
		array(
			'header' => tc('Sections'),
			'type' => 'raw',
			'value' => '$data->getLinkForSection()',
		),
		array(
			'header' => Yii::t('module_comments', 'Name'),
			'type' => 'raw',
			'value' => '$data->getUser()',
		),
		'body',
		array(
			'name' => 'dateCreated',
			'header' => Yii::t('module_comments', 'Creation date'),
			'headerHtmlOptions' => array('style' => 'width:130px;'),
			'filter' => false,
			'sortable' => true,
		),
		array(
			'name' => 'rating',
			'type' => 'raw',
			'value'=>'$this->grid->controller->widget("CStarRating", array(
				"name" => $data->id,
				"id" => $data->id,
				"value" => $data->rating,
				"readOnly" => true,
			), true)',
			'headerHtmlOptions' => array('style' => 'width:85px;'),
			'htmlOptions' => array('class' => 'rating-block'),
			'filter' => false,
			//'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
            'template' => '{update} {delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'viewButtonUrl' => '',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/comments/backend/main/itemsSelected',
	'id' => 'comment-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'delete' => Yii::t('common', 'Delete')
	),
));
?>