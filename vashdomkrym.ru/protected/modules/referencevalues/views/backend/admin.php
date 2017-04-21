<?php
$this->breadcrumbs=array(
	Yii::t('common', 'References') => array('/site/viewreferences'),
	tt('Manage reference values'),
);

$this->menu=array(
	array('label'=>tt('Create value'), 'url'=>array('/referencevalues/backend/main/create')),
	array('label'=>tt('Create multiple reference values'), 'url'=>array('createMulty')),
);

$this->adminTitle = tt('Manage reference values');

$this->widget('CustomBootStrapGroupGridView', array(
	'extraRowColumns' => array('reference_category_id'),
	'extraRowExpression' => '"<strong>{$data->category->getTitle()}</strong>"',
	//'mergeColumns' => array('reference_category_id'),
	'id'=>'reference-values-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(id, data){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable(id, data);}',
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
			'name' => 'reference_category_id',
			'header' => tt('Category'),
			'sortable' => false,
			'value' => '$data->category->getTitle()',
			'filter' => CHtml::dropDownList('ReferenceValues[category_filter]', $currentCategory, $this->getCategories()),
			'htmlOptions' => array(
				//'class' => 'referencevalues_category_column',
				//'onChange' => '',
			),
		),
		array(
			'class' => 'editable.EditableColumn',
			'header' => tc('Name'),
			'name' => 'title_'.Yii::app()->language,
			'value' => '$data->getStrByLang("title")',
			'editable' => array(
				'url' => Yii::app()->controller->createUrl('/referencevalues/backend/main/ajaxEditColumn', array('model' => 'ReferenceValues', 'field' => 'title_'.Yii::app()->language)),
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
			'name' => 'for_sale',
			'type' => 'raw',
			'value' => 'ReferenceValues::returnForStatusHtml($data, "for_sale", "reference-values-grid")',
			'sortable' => false,
			'filter' => false
		),
		array(
			'name' => 'for_rent',
			'type' => 'raw',
			'value' => 'ReferenceValues::returnForStatusHtml($data, "for_rent", "reference-values-grid")',
			'sortable' => false,
			'filter' => false
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
					'url'=>'Yii::app()->createUrl("/referencevalues/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid"=>$data->category->id))',
					'options' => array('class'=>'infopages_arrow_image_up'),
					'visible' => '$data->sorter > Yii::app()->controller->minSorters[$data->reference_category_id]',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'reference-values-grid'); return false;}",
				),
				'down' => array(
					'label' => tc('Move an item down'),
					'imageUrl' => $url = Yii::app()->assetManager->publish(
						Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
					),
					'url'=>'Yii::app()->createUrl("/referencevalues/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid"=>$data->category->id))',
					'options' => array('class'=>'infopages_arrow_image_down'),
					'visible' => '$data->sorter < Yii::app()->controller->maxSorters[$data->reference_category_id]',
					'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'reference-values-grid'); return false;}",
				),
			),
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/referencevalues/backend/main/itemsSelected',
	'id' => 'reference-values-grid',
	'model' => $model,
	'options' => array(
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
			installSortable($(data).find('#ReferenceValues_category_filter option:selected').val());
		}

		function updateGrid() {
			$.fn.yiiGridView.update('reference-values-grid');
		}

		function installSortable(areaIdSel) {
			if (areaIdSel > 0) {
				$('#reference-values-grid table.items tbody').sortable({
					forcePlaceholderSize: true,
					forceHelperSize: true,
					items: 'tr',
					update : function () {
						serial = $('#reference-values-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'data-bid'}) + '&{$csrf_token_name}={$csrf_token}';
						$.ajax({
							'url': '" . $this->createUrl('/referencevalues/backend/main/sortitems') . "',
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
				}).disableSelection();
			}
		}

		installSortable('".intval($model->reference_category_id)."');
";

$cs->registerScript('sortable-project', $str_js);