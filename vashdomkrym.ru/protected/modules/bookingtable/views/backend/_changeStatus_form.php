<?php
$costBooking = HBooking::calculateAdvancePayment($model);

?>

<div class="change_booking_status">
    <div class="row-fluid">
        <?php
        $form = $this->beginWidget('CustomForm', array(
            'id' => 'cs-form',
            'htmlOptions' => array(
                'class' => 'width500',
            ),
        ));
        echo $form->errorSummary($model);

        echo CHtml::hiddenField('id', $model->id);
        ?>

        <div class="span10">
            <?php echo $form->radioButtonList($model, 'active', Bookingtable::getAllStatuses(true), array(
                'uncheckValue' => null,
            ));?>
        </div>

        <div class="span10" id="amount_row" style="display: none;">
            <?php
            if ($model->apartment) {
				/** @var Apartment $ad */
                echo '<div class="ad-info">';
					$ad = $model->apartment;
					$title = CHtml::encode($ad->getTitle());
					echo CHtml::link($title, $ad->getUrl(), array('target' => '_blank'));

					$res = Images::getMainThumb(150, 100, $ad->images);
					$img = CHtml::image($res['thumbUrl'], $title, array(
						'title' => $title,
						'class' => 'apartment_type_img'
					));
					echo '<div class="ad-info-img">';
						echo CHtml::link($img, $ad->getUrl(), array('title' => $title, 'target' => '_blank'));
					echo '</div>';

					echo '<div class="ad-info-img">';
						if ($ad->is_price_poa)
							echo tt('is_price_poa', 'apartments');
						else
							echo $ad->getPrettyPrice();
                echo '</div>';

                echo '</div>';

                echo '<div class="flash-notice">' . tt('The user will receive an email with a request to pay the booking.', 'booking') . '</div>';
            }
            ?>

			<?php
            $currency = issetModule('currency') ? Currency::getDefaultCurrencyName() : param('siteCurrency');
            echo tt('The client wants to book', 'booking').': '.HBooking::$bookedDays.' '.Yii::t('module_booking', 'day|days|days', HBooking::$bookedDays);
            echo ' - '.$model->date_start . ' - ' . $model->date_end;
            echo '<br>';
            //echo tt('The program is considered the cost for', 'booking').': '.HBooking::$calculateDay.' '.Yii::t('module_booking', 'day|days|days', HBooking::$calculateDay);
            echo '<hr>';
            //if($costBooking == $model->amount){
                echo HBooking::$calculateHtml;
            //}
            echo $form->textFieldRow($model, 'amount', array('class' => 'span2')).' '.$currency;

            echo '<div class="flash-notice">' . tt('Booking admin comment help', 'booking') . '</div>';
            echo $form->textAreaRow($model, 'comment_admin');
            ?>
        </div>

        <div class="clear"></div><br />
        <div class="rowold">
            <?php
            $this->widget('bootstrap.widgets.TbButton',
                array(
                    'type' => 'primary',
                    'icon' => 'ok white',
                    'label' => tc('Apply'),
                    'htmlOptions' => array(
                        'onclick' => "changeStatus.apply(); return false;",
                    )
                ));
            $this->endWidget();
            ?>
        </div>
    </div>
</div>