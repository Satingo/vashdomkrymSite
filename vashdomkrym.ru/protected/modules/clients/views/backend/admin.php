<?php
$this->breadcrumbs=array(
	tt('Manage clients', 'clients'),
);

$this->menu = array(
	array('label'=>tt('Add client', 'clients'), 'url'=>array('create')),
);
$this->adminTitle = tt('Manage clients', 'clients');

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
			'class'=>'id_column',
		),
		'sortable' => false,
	),
	array(
		'name' => 'state',
		'type' => 'raw',
		'value' => 'Yii::app()->controller->returnControllerClientStateHtml($data, "clients-grid")',
		'htmlOptions' => array(
			'class'=>'apartments_status_column',
		),
		'sortable' => false,
		'filter' => Clients::getClientsStatesArray(),
	),
	array(
		'name' => 'contract_number',
		'htmlOptions' => array(
			'class'=>'id_column',
		),
		'sortable' => false,
	),
	array(
		'name' => 'first_name',
		'sortable' => false,
	),
	array(
		'name' => 'second_name',
		'sortable' => false,
	),
	array(
		'name' => 'middle_name',
		'sortable' => false,
	),
	array(
		'name' => 'birthdate',
		'sortable' => false,
	),
	array(
		'name' => 'phone',
		'sortable' => false,
	),
);


$columns[] = array(
	'class'=>'bootstrap.widgets.TbButtonColumn',
	'template'=>'{view} {update} {delete}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
);
$this->widget('CustomGridView', array(
	'id'=>'clients-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove();}',
	'columns'=>$columns
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/clients/backend/main/itemsSelected',
	'id' => 'clients-grid',
	'model' => $model,
	'options' => array(
		'delete' => Yii::t('common', 'Delete')
	),
));


Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('editable_select_state', "
		function ajaxSetModerationClientState(elem, id, id_elem, items){
			$('#editable_select_state-'+id_elem).editable('".Yii::app()->controller->createUrl("activateclientstate")."', {
				data   : items,
				type   : 'select',
				cancel : '".tc('Cancel')."',
				submit : '".tc('Ok')."',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
	CClientScript::POS_HEAD);
?>