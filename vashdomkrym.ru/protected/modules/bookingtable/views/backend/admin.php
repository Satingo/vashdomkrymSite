<?php
$this->pageTitle=Yii::app()->name . ' - ' . tt('Booking apartment', 'booking');


$this->menu = array(
	array(),
);
$this->adminTitle = tt('Booking apartment', 'booking');
?>

<?php
if (issetModule('bookingcalendar')) {
	echo "<div class='flash-notice'>".tt('booking_table_to_calendar', 'booking')."</div>";
}
?>

<?php $this->widget('CustomGridView', array(
	'id'=>'admin-booking-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'ajaxUpdate' => false,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); jQuery("#Bookingtable_date_start").datepicker(jQuery.extend(jQuery.datepicker.regional["'.Yii::app()->controller->datePickerLang.'"],{"showAnim":"fold","dateFormat":"yy-mm-dd","changeMonth":"true","showButtonPanel":"true","changeYear":"true"}));}',
	'columns' => array(
		array(
			'name' => 'id',
			'htmlOptions' => array(
				'class' => 'id_column',
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
			'value' => 'HBooking::getChangeBookingStatus($data)',
			'htmlOptions' => array(
				'style' => 'width: 150px;',
			),
			'sortable' => false,
			'filter' => Bookingtable::getAllStatuses(),
		),
		array(
			'name' => 'apartment_id',
			'type' => 'raw',
			'value' => '(isset($data->apartment) && $data->apartment->id) ? CHtml::link($data->apartment->id, $data->apartment->getUrl()) : tc("No")',
			'sortable' => false,
		),
		array(
			'name' => 'username',
			'value' => '(isset($data->sender) && $data->sender->role != "admin") ? CHtml::link(CHtml::encode($data->sender->username), array("/users/backend/main/view","id" => $data->sender->id)) : $data->username',
			'type' => 'raw',
			'sortable' => false,
		),
		array(
			'name' => 'email',
			'value' => '$data->email',
			'sortable' => false,
		),
		array(
			'name' => 'phone',
			'value' => '$data->phone',
			'sortable' => false,
		),
		array(
			'name' => 'comment',
			'value' => '$data->comment',
			'sortable' => false,
		),
		array(
			'name' => 'date_start',
			'value' => '(isset($data->timein) && $data->time_in) ? $data->date_start . " (". $data->timein->getStrByLang("title").")" : "" ',
			'filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'date_start',
			'language' => Yii::app()->controller->datePickerLang,
			'options' => array(
				'showAnim'=>'fold',
				'dateFormat'=> 'yy-mm-dd',
				'changeMonth' => 'true',
				'changeYear'=>'true',
				'showButtonPanel' => 'true',
			),
		),true),
			'sortable' => false,
			'htmlOptions' => array('style' => 'width:150px;'),
		),
		array(
			'name' => 'date_end',
			'value' => '(isset($data->timeout) && $data->time_out) ? $data->date_end . " (". $data->timeout->getStrByLang("title").")" : "" ',
			'filter' => false,
			'sortable' => false,
			'htmlOptions' => array('style' => 'width:150px;'),
		),
		array(
			'name' => 'num_guest',
			'htmlOptions' => array(
				'class' => 'id_column',
			),
			'sortable' => false,
		),
		array(
			'header' => tt('Creation date', 'booking'),
			'value' => '$data->date_created',
			'type' => 'raw',
			'sortable' => false,
			//'htmlOptions' => array('style' => 'width:130px;'),
		),
	),
));
?>

<?php if(issetModule('bookingcalendar')):?>
	<?php $this->widget('FullCalendarWidget');?>
<?php endif; ?>
