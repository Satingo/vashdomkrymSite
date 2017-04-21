<?php
$this->pageTitle=Yii::app()->name . ' - ' . EntriesModule::t('Manage entries');


$this->menu = array(
	array('label' => EntriesModule::t('Add entry'), 'url' => array('create')),
);
$this->adminTitle = EntriesModule::t('Manage entries');
?>

<?php $this->widget('CustomBootStrapGroupGridView', array(
	'extraRowColumns' => array('category_id'),
	'extraRowExpression' => '"<strong>{$data->category->getName()}</strong>"',
	'id'=>'entries-grid',
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
			'name' => 'active',
			'type' => 'raw',
			'value' => 'Yii::app()->controller->returnStatusHtml($data, "entries-grid", 1)',
			'headerHtmlOptions' => array(
				'class'=>'apartments_status_column',
			),
			'filter' => false,
			'sortable' => false,
		),
		array(
			'header' => tc('Name'),
			'name'=>'title_'.Yii::app()->language,
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->getStrByLang("title")), $data->url)',
			'sortable' => false,
		),
		array(
			'name' => 'category_id',
			'type' => 'raw',
			'value' => '($data->category_id && isset($data->category) && $data->category) ? $data->category->name : ""',
			'filter' => EntriesCategory::getAllCategories(),
			'sortable' => false,
		),
		array(
			'name' => 'tags',
			'value' => '$data->tags',
			'sortable' => false,
		),
		array(
			'name'=>'dateCreated',
			'type'=>'raw',
			'filter'=>false,
			'htmlOptions' => array('style' => 'width:130px;'),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'deleteConfirmation' => tc('Are you sure you want to delete this item?'),
			'viewButtonUrl' => '$data->url',
		),
	),
));

$this->renderPartial('//site/admin-select-items', array(
	'url' => '/entries/backend/main/itemsSelected',
	'id' => 'entries-grid',
	'model' => $model,
	'options' => array(
		'activate' => Yii::t('common', 'Activate'),
		'deactivate' => Yii::t('common', 'Deactivate'),
		'delete' => Yii::t('common', 'Delete'),
	),
));
?>