<?php
$this->pageTitle .= ' - '.tt('Messages', 'messages');
$this->breadcrumbs = array(
	tc('Control panel') => Yii::app()->createUrl('/usercpanel/main/index'),
	tt('My mailbox', 'messages'),
);
?>

<h1 class="title highlight-left-right">
<span>
	<?php echo tt('My mailbox', 'messages'); ?>
</span>
</h1>
<div class="clear"></div><br />

<?php
$columns[]=array(
	'header' => '',
	'type' => 'raw',
	'value'=>function($data,$row){
			return (Messages::getCountUnreadFromUser($data->id)) ? CHtml::image(Yii::app()->theme->baseUrl.'/images/new_message.png', tt('New message', 'messages'), array('title' => tt('New message', 'messages'))) : '';
		},
	'htmlOptions' => array(
		'style' => 'width: 20px;',
	),
	'sortable' => false,
	'filter' => false,
);

$columns[]=array(
	'header' => tt('User', 'messages'),
	'value' => 'Yii::app()->controller->returnHtmlMessageSenderName($data, false)',
	'sortable' => false,
	'filter' => false,
);

$columns[] = array(
	'class'=>'CButtonColumn',
	'deleteConfirmation' => tc('Are you sure?'),
	//'template' => '{read}{delete}',
	'template' => '{read}',
	'buttons' => array(
		'read' => array(
			'label' => tt('Read', 'messages'),
			'url' => 'Yii::app()->createUrl("/messages/main/read", array("id" => $data->id))',
		),
		/*'delete' => array(
			'imageUrl' => '',
			'label' => tt('Delete', 'messages'),
			'url' => 'Yii::app()->createUrl("/messages/main/delete", array("id" => $data->id))',
			'options' => array('class' => 'messages-delete-icon'),
		),*/
	),
);

if ($allUsers && count($allUsers)) {
	$this->widget(
		'NoBootstrapGridView',
		array(
			'id' => 'messages-grid',
			'dataProvider' => $itemsProvider,
			'columns' => $columns,
		)
	);
}
else {
	echo "<div class='flash-notice'>".tt('no_messages', 'messages')."</div>";
}
?>