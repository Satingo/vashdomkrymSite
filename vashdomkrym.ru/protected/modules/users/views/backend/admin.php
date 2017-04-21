<?php
$this->breadcrumbs=array(
	Yii::t('common', 'User managment'),
);

$this->menu=array(
	array('label'=>tt('Add user'), 'url'=>array('/users/backend/main/create')),
);

$this->adminTitle = Yii::t('common', 'User managment');

$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '2',
		'htmlOptions' => array(
			'class'=>'center',
		),
		'disabled' => '$data->role == "'.User::ROLE_ADMIN.'"',
	),
	array(
		'name' => 'active',
		'header' => tt('Status'),
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnStatusHtml($data, "user-grid", 1, 1)',
		'headerHtmlOptions' => array(
			'class'=>'infopages_status_column',
		),
		'filter' => array(0 => tt('Inactive'), 1 => tt('Active')),
	),
	array(
		'name' => 'type',
		'value' => '$data->getTypeName()',
		'filter' => User::getTypeList(),
	),
	array(
		'name' => 'role',
		'value' => '$data->getRoleName()',
		'filter' => User::$roles,
	),
	array(
		'name' => 'username',
		'header' => tt('User name'),
	),
	'phone',
	'email',
	array(
		'header' => '',
		'value' => 'HUser::getLinkForRecover($data)',
		'type' => 'raw'
	),
	array(
		'name'=>'date_created',
		'type'=>'raw',
		'filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'date_created',
			'language' => Yii::app()->controller->datePickerLang,
			'options' => array(
				'showAnim'=>'fold',
				'dateFormat'=> 'yy-mm-dd',
				'changeMonth' => 'true',
				'changeYear'=>'true',
				'showButtonPanel' => 'true',
			),
		),true),
		'htmlOptions' => array('style' => 'width:130px;'),
	),
);

if(issetModule('paidservices')) {
	//$columns[] = 'balance';
}

if(issetModule('tariffPlans') && issetModule('paidservices') && Yii::app()->user->checkAccess('tariff_plans_admin')){
	// for modal apply
	$cs = Yii::app()->clientScript;
	$cs->registerCoreScript('jquery.ui');
	$cs->registerScriptFile($cs->getCoreScriptUrl(). '/jui/js/jquery-ui-i18n.min.js');
	$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');

	$columns[] = array(
		'header' => tc('Tariff Plans'),
		'value'=>'TariffPlans::getTariffPlansHtml(true, true, $data)',
		'type'=>'raw',
		'htmlOptions' => array(
			'style' => 'width: 200px;',
		),
	);
}

$columns[] = array(
	'class' => 'editable.EditableColumn',
	'name' => 'balance',
	'value' => '$data->balance',
	'editable' => array(
		'url' => Yii::app()->controller->createUrl('/apartments/backend/main/ajaxEditColumn', array('model' => 'User', 'field' => 'balance')),
		'placement' => 'right',
		'emptytext' => '',
		'savenochange' => 'true',
		'title' => tc('balance'),
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
	'filter' => false,
);

$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'template'=>'{listings} {send} {view} {update} {delete}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'htmlOptions' => array('style'=>'width: 155px; min-width: 155px;'),
	'headerHtmlOptions' => array('style'=>'width: 155px; min-width: 155px;'),
	'buttons' => array(
		'update' => array(
			'visible' => '(Yii::app()->user->checkAccess("users_admin") && $data->role != "'.User::ROLE_ADMIN.'") || ($data->role == "'.User::ROLE_ADMIN.'" && Yii::app()->user->checkAccess("admin"))',
		),
		'view' => array(
			'visible' => '$data->role != "'.User::ROLE_ADMIN.'"',
		),
		'delete' => array(
			'visible' => '$data->role != "'.User::ROLE_ADMIN.'"',
		),
		'listings' => array(
			'label' => '',
			'url'=>'Yii::app()->createUrl("/apartments/backend/main/admin", array("Apartment[ownerEmail]" => $data->email))',
			'options' => array('class'=>'icon-list-alt', 'title' => tt('member_listings', 'apartments')),
			'visible' => 'Yii::app()->user->checkAccess("apartments_admin") ? true : false',
		),
		'send' => array(
			'label' => '',
			'url'=>'Yii::app()->createUrl("/messages/backend/main/read", array("id" => $data->id))',
			'options' => array('class'=>'icon-envelope', 'title' => tt('Message', 'messages')),
			'visible' => '(issetModule("messages") && Yii::app()->user->checkAccess("messages_admin") && $data->id != Yii::app()->user->id && $data->role == "registered" && $data->active == 1) ? true : false',
		),
	)
);

$this->widget('CustomGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); jQuery("#User_date_created").datepicker(jQuery.extend(jQuery.datepicker.regional["'.Yii::app()->controller->datePickerLang.'"],{"showAnim":"fold","dateFormat":"yy-mm-dd","changeMonth":"true","showButtonPanel":"true","changeYear":"true"}));}',
	'columns'=>$columns,
    'ajaxUpdate' => false,
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/users/backend/main/itemsSelected',
	'id' => 'user-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete')
	),
));

