<?php
$this->pageTitle .= ' - '.tt('My bookings', 'usercpanel');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tt('My bookings', 'usercpanel'),
);


if (issetModule('bookingcalendar')) {
    //echo "<div class='flash-notice'>".tt('booking_table_to_calendar', 'booking')."</div>";
}
?>

<?php
$this->widget('zii.widgets.grid.CGridView',
    array(
        'id'=>'users-booking-grid',
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'columns' => array(
            /*array(
                'name' => 'id',
                'htmlOptions' => array(
                    'class' => 'id_column',
                ),
            ),*/
            array(
                'name' => 'active',
                'type' => 'raw',
                'value' => '$data->getMyBookingButton()',
                'htmlOptions' => array(
                    'style' => 'width: 220px; text-align: center;',
                ),
                'sortable' => false,
                'filter' => Bookingtable::getAllStatuses(),
            ),
            array(
                'name' => 'apartment_id',
                'type' => 'raw',
                'value' => '(isset($data->apartment) && $data->apartment->id) ? CHtml::link($data->apartment->id, $data->apartment->getUrl()) : tc("No")',
                'filter' => false,
                'sortable' => false,
            ),
            array(
                'name' => 'num_guest',
                'filter' => false,
                'sortable' => false,
            ),
            array(
                'name' => 'comment',
                'value' => 'truncateText($data->comment)',
                'filter' => true,
                'sortable' => false,
            ),
            array(
                'name' => 'date_start',
                'value' => '(isset($data->timein) && $data->time_in) ? $data->date_start . " (". $data->timein->getStrByLang("title").")" : "" ',
                'filter' => true,
                'sortable' => false,
                'htmlOptions' => array('style' => 'width:150px;'),
            ),
            array(
                'name' => 'date_end',
                'value' => '(isset($data->timeout) && $data->time_out) ? $data->date_end . " (". $data->timeout->getStrByLang("title").")" : "" ',
                'filter' => true,
                'sortable' => false,
                'htmlOptions' => array('style' => 'width:150px;'),
            ),
            array(
                'header' => tt('Creation date', 'booking'),
                'value' => '$data->date_created',
                'type' => 'raw',
                'filter' => false,
                'sortable' => false,
                //'htmlOptions' => array('style' => 'width:130px;'),
            ),
        ),
    )
);
//echo 'userID = '.Yii::app()->user->id;

Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('editable_select_booking_table', "
		function ajaxSetBookingTableStatus(elem, id, id_elem, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("bookingtableactivate")."', {
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
