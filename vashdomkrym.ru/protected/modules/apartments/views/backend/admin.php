<?php

// for modal apply paid service
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery.ui');
$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');


$this->breadcrumbs=array(
	tt('Manage apartments'),
);

$this->menu = array(
	array('label'=>tt('Add apartment'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage apartments');

if(Yii::app()->user->hasFlash('mesIecsv')){
	echo "<div class='flash-success'>".Yii::app()->user->getFlash('mesIecsv')."</div>";
}

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
	),
	array(
		'name' => 'id',
		'htmlOptions' => array(
			'class'=>'apartments_id_column',
		),
		'sortable' => true,
	),
	array(
		'class' => 'editable.EditableColumn',
		'name' => 'active',
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnApartmentActiveHtml($data)',
		'editable' => array(
			'url' => Yii::app()->controller->createUrl('/apartments/backend/main/ajaxEditColumn', array('model' => 'Apartment', 'field' => 'active')),
			'placement' => 'right',
			'emptytext' => '',
			'savenochange' => 'true',
			'title' => tt('Status', 'apartments'),
			'type' => 'select',
			'source' => Apartment::getAvalaibleStatusArray(),
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
		'filter' => Apartment::getModerationStatusArray(),
	),
	array(
		'name' => 'owner_active',
		'type' => 'raw',
		'value' => 'Apartment::getApartmentsStatus($data->owner_active)',
		'htmlOptions' => array(
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getApartmentsStatusArray(),
	),
	array(
		'name' => 'deleted',
		'type' => 'raw',
		'value' => 'Apartment::getApartmentsDeleted($data->deleted)',
		'htmlOptions' => array(
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Apartment::getApartmentsDeletedArray(),
		'visible' => param("notDeleteListings", 0),
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'HApartment::getNameByType($data->type)',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => HApartment::getTypesArray(false, HApartment::isDisabledType()),//CHtml::dropDownList('Apartment[type_filter]', $currentType, HApartment::getTypesArray(true)),
		'sortable' => false,
	),
	array(
		'name' => 'price',
		'type' => 'raw',
		'value' => '$data->getPrettyPrice(false)',
		'htmlOptions' => array(
			'style' => 'width: 100px;',
		),
		'filter' => false,
		'sortable' => false,
	),
	array(
		'name' => 'obj_type_id',
		'type' => 'raw',
		'value' => '(isset($data->objType) && $data->objType) ? $data->objType->name : ""',
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
		'filter' => Apartment::getObjTypesArray(),
		'sortable' => false,
	),
);
if (issetModule('location')) {
	$columns[]=array(
		'name' => 'loc_country',
		'value' => '($data->loc_country && isset($data->locCountry)) ? $data->locCountry->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Country::getCountriesArray(0, 1),
	);
	$columns[]=array(
		'name' => 'loc_region',
		'value' => '($data->loc_region && isset($data->locRegion)) ? $data->locRegion->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => Region::getRegionsArray($model->loc_country, 0, 1),
	);
	$columns[]=array(
		'name' => 'loc_city',
		'value' => '($data->loc_city && isset($data->locCity)) ? $data->locCity->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => City::getCitiesArray($model->loc_region, 0, 1),
	);
} else {
	$columns[]=array(
		'name' => 'city_id',
		'value' => '($data->city_id && isset($data->city)) ? $data->city->name : ""',
		'htmlOptions' => array(
			'style' => 'width: 150px;',
		),
		'sortable' => false,
		'filter' => ApartmentCity::getAllCity(),
	);
}

$columns[]=array(
	'name' => 'ownerEmail',
	'htmlOptions' => array(
		'style' => 'width: 150px;',
	),
	'type' => 'raw',
	'value' => '(isset($data->user) && $data->user->role != "admin") ? CHtml::link(CHtml::encode($data->user->email), array("/users/backend/main/view","id" => $data->user->id)) : tt("administrator", "common")',
);

//$columns[]=array(
//    'name' => 'ownerUsername',
//    'htmlOptions' => array(
//        'style' => 'width: 150px;',
//    ),
//    'value' => 'isset($data->user->username) ? $data->user->username : ""'
//);


$columns[]=array(
	'header' => tc('Name'),
	'name' => 'title_'.Yii::app()->language,
	'type' => 'raw',
	'value' => 'CHtml::encode($data->{"title_".Yii::app()->language})',
	'sortable' => false,
);

if(issetModule('paidservices')){
	$columns[] = array(
		'name' => 'searchPaidService',
		'value'=>'HApartment::getPaidHtml($data, true, true)',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
		'sortable' => false,
		'filter' => $paidServicesArray,
	);
}

if (Yii::app()->user->checkAccess("change_apartment_visits")) {
	$columns[] = array(
		'class' => 'editable.EditableColumn',
		'name' => 'visits',
		'value' => '$data->visits',
		'editable' => array(
			'url' => Yii::app()->controller->createUrl('/apartments/backend/main/ajaxEditColumn', array('model' => 'Apartment', 'field' => 'visits')),
			'placement' => 'right',
			'emptytext' => '',
			'savenochange' => 'true',
			'title' => tt('Views', 'apartments'),
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
		'sortable' => true,
		'filter' => false,
	);
}

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',

	'template'=>'{up} {down} {change_owner} {clone} {restore}<br /><br />{view} {update} {delete}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'htmlOptions' => array('style'=>'width: 145px; min-width: 145px;'),
	'headerHtmlOptions' => array('style'=>'width: 145px; min-width: 145px;'),
	'buttons' => array(
		'change_owner' => array(
			'label' => '',
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/choosenewowner", array("id" => $data->id))',
			//'options' => array('class'=>'icon-user tempModal', 'title' => tt('Set the owner of the listing', 'apartments')),
			'options' => array('class'=>'icon-user', 'title' => tt('Set the owner of the listing', 'apartments')),
			'visible' => 'Yii::app()->user->checkAccess("apartments_admin") ? true : false',
		),
		'delete' => array(
			'visible'=> '(!param("notDeleteListings", 0) || (param("notDeleteListings", 0) && !$data->deleted))'
		),
		'view' => array(
			'url'=>'$data->getUrl()',
			'options'=>array('target'=>'_blank'),
		),
		'up' => array(
			'label' => tc('Move an item up'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/up.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "down", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_up'),

			'visible' => '$data->sorter < "'.$maxSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
		'clone' => array(
			'label' => tc('Clone'),
			'url' => 'Yii::app()->createUrl("/apartments/backend/main/clone", array("id" => $data->id))',
			'imageUrl' => Yii::app()->request->baseUrl. '/images/interface/copy_admin.png',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
		'restore' => array(
			'label' => '',
			'url' => 'Yii::app()->createUrl("/apartments/backend/main/restore", array("id" => $data->id))',
			'options' => array('class'=>'icon-retweet', 'title' => tt('Restore', 'apartments')),
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
			'visible' => '$data->deleted'
		),

		'down' => array(
			'label' => tc('Move an item down'),
			'imageUrl' => $url = Yii::app()->assetManager->publish(
				Yii::getPathOfAlias('zii.widgets.assets.gridview').'/down.gif'
			),
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/move", array("id"=>$data->id, "direction" => "up", "catid" => "0"))',
			'options' => array('class'=>'infopages_arrow_image_down'),
			'visible' => '$data->sorter > "'.$minSorter.'"',
			'click' => "js: function() { ajaxMoveRequest($(this).attr('href'), 'apartments-grid'); return false;}",
		),
	),
);

$this->widget('CustomGridView', array(
	'id'=>'apartments-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); reInstallSortable();}',
	'rowCssClassExpression'=>'"items[]_{$data->id}"',
	'rowHtmlOptionsExpression' => 'array("data-bid"=>"items[]_{$data->id}")',
	'columns'=>$columns
));

$options = array(
	'activate' => Yii::t('common', 'Activate'),
	'deactivate' => Yii::t('common', 'Deactivate'),
	'delete' => Yii::t('common', 'Delete'),
	'clone' => Yii::t('common', 'Clone')
);

if(Apartment::model()->countByAttributes(array('deleted'=>1)))
	$options['restore'] = tt('Restore', 'apartments');

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/apartments/backend/main/itemsSelected',
	'id' => 'apartments-grid',
	'model' => $model,
	'options' => $options,
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
			$.fn.yiiGridView.update('apartments-grid');
		}

		function installSortable() {
			$('#apartments-grid table.items tbody').sortable({
				forcePlaceholderSize: true,
				forceHelperSize: true,
				items: 'tr',
				update : function () {
					serial = $('#apartments-grid table.items tbody').sortable('serialize', {key: 'items[]', attribute: 'data-bid'}) + '&{$csrf_token_name}={$csrf_token}';
					$.ajax({
						'url': '" . $this->createUrl('/apartments/backend/main/sortitems') . "',
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