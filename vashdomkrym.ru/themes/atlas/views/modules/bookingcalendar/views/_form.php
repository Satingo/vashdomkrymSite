<div class="rowold padding-bottom10">
	<?php
	if(!$apartment->isNewRecord && $apartment->type == Apartment::TYPE_RENT): ?>
		<?php echo CHtml::label(tt('The periods of booking apartment', 'bookingcalendar'), 'bookings-db'); ?>

		<ul id="bookings-db">
			<?php $this->renderPartial('//modules/bookingcalendar/views/_booking_period_db', array('apartment' => $apartment)); ?>
		</ul>

		<ul id="bookings"></ul>

		<div class="rowold buttons">
			<?php echo CHtml::button(tc('Add'), array('id' => 'bookings-add'))?><br />
			<?php Yii::app()->clientScript->registerScript('prepare_booking_path', '
				var apId = "'.$apartment->id.'";
				var element = "'.$element.'";
				var booking_add_url = "'.Yii::app()->controller->createUrl("/bookingcalendar/main/addfieldbooking").'";
				var booking_save_url = "'.$this->createUrl("/bookingcalendar/main/savebooking").'";
				var booking_edit_url = "'.$this->createUrl("/bookingcalendar/main/editbooking").'";
				var booking_delete_url = "'.$this->createUrl("/bookingcalendar/main/deletebooking").'";
			', CClientScript::POS_END); ?>
		</div>
	<?php endif; ?>
</div>
<?php
	Yii::app()->clientScript->registerScript('bookings-add-script', '
		$("#bookings-add").click(function(){
				$.ajax({
					success: function(html){
						$("#bookings").append(html);
					},
					type: "get",
					url: booking_add_url,
					data: {
						element: $("#bookings li").size()
					},
					cache: false,
					dataType: "html"
				});
			});
	', CClientScript::POS_READY);

	Yii::app()->clientScript->registerScript('hideMessageDb-script', '
		function hideMessageDb(elem) {
			elem.removeClass("status-save-success").removeClass("status-save-error").html("");
		}
	', CClientScript::POS_END);
?>
