<?php
$this->pageTitle .= ' - '.tt('Pay for booking', 'booking');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tt('Pay for booking', 'booking'),
);

$costBooking = HBooking::calculateAdvancePayment($booking);

?>

<div class="form">
    <h1 class="title highlight-left-right">
        <span><?php echo tt('Pay for booking', 'booking'); ?></span>
    </h1>

    <?php require '_payForBooking_ad.php'; ?>

    <div class="clear"></div>
    <strong>
        <?php
        echo HBooking::$bookedDays.' '.Yii::t('module_booking', 'day|days|days', HBooking::$bookedDays);
        echo ' - '.tc('from') . ' ' . HBooking::formatDate(strtotime($booking->date_start)) . ' '.tc('to') . ' ' . HBooking::formatDate(strtotime($booking->date_end));
        ?>
    </strong>
    <div class="clear"></div>
    <?php
    if($costBooking == $booking->amount){
        echo HBooking::$calculateHtml;
    }
    ?>

    <div class="pay_cost">
        <?php echo tt('Pay now').': '.$booking->amount.' '.Currency::getDefaultCurrencyName(); ?>
    </div>

    <div class="clear"></div><br />

    <div class="rowold">
        <?php

        echo CHtml::beginForm(array('/paidservices/main/index'));
        echo CHtml::hiddenField('pay_submit', 1);
        echo CHtml::hiddenField('id', $booking->apartment_id);
        echo CHtml::hiddenField('b_id', $booking->id);
        echo CHtml::hiddenField('b_amount', $booking->amount);

        Yii::app()->getModule('payment');
        $paySystems = Paysystem::getPaysystems();
        $paySystemsArray = CHtml::listData($paySystems, 'id', 'name');
        ?>

        <div class="rowold">
            <?php echo tc('Pay with'); ?>:
            <?php
            if(count($paySystems) > 1){
                echo CHtml::dropDownList('pay_id', null, $paySystemsArray,
                    array('class' => 'width300', 'onchange' => 'showDescriptionPayment(this.value);')
                );
            }
            else {
                echo CHtml::hiddenField('pay_id', reset($paySystems)->id);
                echo '<strong>'.reset($paySystems)->name.'</strong>';
            }
            ?>
        </div>
        <br/>
        <div class="rowold" id="descriptionPayment">
            <?php echo reset($paySystems)->getDescription(); ?>
        </div>

        <div class="rowold submit">
            <?php echo CHtml::submitButton(tc('Proceed'), array('id' => 'proceed', 'class' => 'button-blue')); ?>
        </div>

        <?php echo CHtml::endForm();?>

        <div class="form_tip">
            <?php //echo tt('help_buy_tariff_plan', 'tariffPlans');?><br />
        </div>
    </div>

    <?php
    $descriptionsPayment = '';
    foreach($paySystems as $model){
        $descriptionsPayment .= 'descrPayment['.$model->id.'] = "'.CJavaScript::quote($model->getDescription()).'";'."\n";
    }

    Yii::app()->clientScript->registerScript('showDescriptionPayment', '
				var descrPayment = new Array();
				'.$descriptionsPayment.'
				function showDescriptionPayment(id){
					$("#descriptionPayment").html("");
					if(descrPayment[id]){
						$("#descriptionPayment").html(descrPayment[id]);
					}
				}
			', CClientScript::POS_END);
    ?>
</div>