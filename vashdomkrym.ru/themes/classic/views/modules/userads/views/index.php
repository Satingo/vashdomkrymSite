<?php
Yii::app()->getModule('userads');

//if(!Yii::app()->request->isAjaxRequest){
//    echo '<h1>'.tt('Manage apartments', 'apartments').'</h1>';
//}

$this->pageTitle .= ' - '.tt('Manage apartments', 'apartments');
if (!isset($this->breadcrumbs)) {
	$this->breadcrumbs = array(
		Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
		tt('Manage apartments', 'apartments')
	);
}

?>

<div class="flashes" style="display: none;"></div>

<?php
Yii::app()->clientScript->registerScript('ajaxSetStatus', "
		function ajaxSetStatus(elem, id, isUpdate){
			var isUpdate = isUpdate || 1;

			$.ajax({
				url: $(elem).attr('href'),
				success: function(result){
					$('.flashes').hide();

					if (isUpdate == 2) {
						$('.flashes').show();
						$('.flashes').html(result);
						$('#'+id).yiiGridView.update(id);
					}
					else {
						$('#'+id).yiiGridView.update(id);
					}
				}
			});
		}
	",
    CClientScript::POS_HEAD);


$columns = array(
	array(
		'name' => 'id',
		'headerHtmlOptions' => array(
			'class'=>'apartments_id_column',
		),
		'sortable' => false,
	),
	array(
		'name' => 'active',
		'type' => 'raw',
		'value' => 'UserAds::returnStatusHtml($data, "userads-grid", 0)',
		'headerHtmlOptions' => array(
			'class'=>'userads_status_column',
		),
		'filter' => Apartment::getModerationStatusArray(),
		'sortable' => false,
	),

	array(
		'name' => 'owner_active',
		'type' => 'raw',
		'value' => 'UserAds::returnStatusOwnerActiveHtml($data, "userads-grid", 1)',
		'headerHtmlOptions' => array(
			'class'=>'userads_owner_status_column',
		),
		'filter' => array(
			'0' => tc('Inactive'),
			'1' => tc('Active'),
		),
		'sortable' => false,
	),
	array(
		'name' => 'type',
		'type' => 'raw',
		'value' => 'HApartment::getNameByType($data->type)',
		'filter' => HApartment::getTypesArray(false, HApartment::isDisabledType()),
		/*'htmlOptions' => array(
			'style' => 'width: 100px;',
		),*/
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
	array(
		'header' => tc('Name'),
		'name' => 'title_'.Yii::app()->language,
		'type' => 'raw',
		'value' => 'CHtml::link(CHtml::encode($data->{"title_".Yii::app()->language}), $data->getUrl())',
		'sortable' => false,
	),
);

if (issetModule('location')) {
    $columns[]=array(
        'name' => 'loc_country',
        'value' => '$data->loc_country ? $data->locCountry->name : ""',
        'htmlOptions' => array(
            'style' => 'width: 150px;',
        ),
        'sortable' => false,
        'filter' => Country::getCountriesArray(0, 1),
    );
    $columns[]=array(
        'name' => 'loc_region',
        'value' => '$data->loc_region ? $data->locRegion->name : ""',
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
//        'htmlOptions' => array(
//            'style' => 'width: 150px;',
//        ),
        'sortable' => false,
        'filter' => ApartmentCity::getAllCity(),
    );
}
if (Yii::app()->user->type == User::TYPE_AGENCY) {
	$columns[] = array(
		'header'=>tc('Owner'),
		'value'=>'$data->user->username." (".$data->user->getTypeName().")"',
		'type'=>'raw',
		'htmlOptions' => array(
			'class' => 'width70 center',
		),
	);
}

if(issetModule('paidservices')){
	$columns[] = array(
		'header'=>tc('Paid services'),
		'value'=>'HApartment::getPaidHtml($data, false, false, true)',
		'type'=>'raw',
		'htmlOptions' => array(
            'class' => 'width70 center',
		),
	);
}

$columns[] = array(
	'class'=>'CButtonColumn',
	'template'=>(Yii::app()->user->type == User::TYPE_AGENCY) ? '{view}<br /><br />{update}<br /><br />{delete}<br /><br />{clone}<br /><br />{change_owner}' : '{view}<br /><br />{update}<br /><br />{delete}<br /><br />{clone}',
	'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
	'viewButtonUrl' => '$data->getUrl()',
    'buttons' => array(
        'update' => array(
            'url' => 'Yii::app()->createUrl("/userads/main/update", array("id" => $data->id))',
        ),
        'delete' => array(
            'url' => 'Yii::app()->createUrl("/userads/main/delete", array("id" => $data->id))',
        ),
		'clone' => array(
			'label' => tc('Clone'),
			'imageUrl' => Yii::app()->theme->baseUrl.'/images/default/copy.png',
			'url' => 'Yii::app()->createUrl("/userads/main/clone", array("id" => $data->id))',
			'visible' => 'param("enableUserAdsCopy",0)',
			'click' => "js: function() { ajaxRequest($(this).attr('href'), 'userads-grid'); return false;}",
		),
		'change_owner' => array(
			'label' => '',
			'imageUrl' => Yii::app()->theme->baseUrl.'/images/default/change-owner.png',
			'url'=>'Yii::app()->createUrl("userads/main/choosenewowner", array("id" => $data->id))',
			'options' => array('class'=>'icon-user', 'title' => tt('Set the owner of the listing', 'apartments')),
		),
    ),
);

$this->widget('NoBootstrapGridView', array(
	'id'=>'userads-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>$columns,
	'template'=>"{summary}\n{pager}<br />{items}\n{pager}",
)); ?>
