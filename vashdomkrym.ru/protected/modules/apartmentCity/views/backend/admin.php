<?php
$this->breadcrumbs=array(
	//Yii::t('common', 'objects') => array('/site/viewobjects'),
	tt('Manage apartment city')
);
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery.ui');
$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
$this->menu=array(
	array('label'=>tt('Add city'), 'url'=>array('/apartmentCity/backend/main/create')),
	array('label'=>tt('Add multiple cities'), 'url'=>array('/apartmentCity/backend/main/createMulty')),
);

$this->adminTitle = tt('Manage apartment city');

$this->widget('CustomGridView', array(
	'id'=>'apartment-city-grid',
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
			'class' => 'editable.EditableColumn',
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnCityActiveHtml($data)',
			'editable' => array(
				'url' => Yii::app()->controller->createUrl('/apartmentCity/backend/main/ajaxEditColumn', array('model' => 'ApartmentCity', 'field' => 'active')),
				'placement' => 'right',
				'emptytext' => '',
				'savenochange' => 'true',
				'title' => tc('Status'),
				'type' => 'select',
				'source' => ApartmentCity::getAvalaibleStatusArray(),
				'placement' => 'top',
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
			'filter' => ApartmentCity::getModerationStatusArray(),
		),
		array(
			'header' => tc('Name'),
			'name' => 'name_'.Yii::app()->language,
			'sortable' => false,
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{up} {down} {update} {delete}',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'htmlOptions' => array('class'=>'infopages_buttons_column'),
			'buttons' => array(
				'up' => array(
					'label' => tc('Move an item up'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentCity/backend/main/move", array("id"=>$data->id, "direction" => "up"))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > "'.$minSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartment-city-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/apartmentCity/backend/main/move", array("id"=>$data->id, "direction" => "down"))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < "'.$maxSorter.'"',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartment-city-grid'); return false;}",
				),
			),
            'afterDelete'=>'function(link,success,data){ if(success) $("#statusMsg").html(data); }'
		),
	),
)); ?>

<?php
	$this->renderPartial('//site/admin-select-items', array(
		'url' => '/apartmentCity/backend/main/itemsSelected',
		'id' => 'apartment-city-grid',
		'model' => $model,
		'options' => array(
			'activate' => Yii::t('common', 'Activate'),
			'deactivate' => Yii::t('common', 'Deactivate'),
			'delete' => Yii::t('common', 'Delete')
		),
	));
?>



<?php

$csrf_token_name = Yii::app()->request->csrfTokenName;
$csrf_token = Yii::app()->request->csrfToken;

$cs = Yii::app()->getClientScript();
$cs->registerCoreScript('jquery.ui');

$str_js = "
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		function reInstallSortable(id, data) {
			installSortable();
		}

		function updateGrid() {
			$.fn.yiiGridView.update('apartment-city-grid');
		}

		function installSortable() {
			$('#apartment-city-grid table.items tbody').sortable({
				forcePlaceholderSize: true,
				forceHelperSize: true,
				items: 'tr',
				update : function () {
					serial = $('#apartment-city-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'data-bid'}) + '&{$csrf_token_name}={$csrf_token}';
					$.ajax({
						'url': '" . $this->createUrl('/apartmentCity/backend/main/sortitems') . "',
						'type': 'post',
						'data': serial,
						'success': function(data){
							updateGrid();
						},
						'error': function(request, status, error){
							alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
						}
					});
				},
				helper: fixHelper
			});
		}

		installSortable();
";

$cs->registerScript('sortable-project', $str_js);