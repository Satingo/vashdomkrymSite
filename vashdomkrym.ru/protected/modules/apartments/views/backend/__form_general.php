<div class="tab-pane active" id="tab-main">

<?php
$callFrom = (isset($callFrom)) ? $callFrom : null;

echo CHtml::hiddenField('is_update', 0);

$rows = HFormEditor::getGeneralFields();
HFormEditor::renderFormRows($rows, $model, $form, $seasonalPricesModel, $callFrom);

echo '<br/>';

if ($model->type == Apartment::TYPE_CHANGE) {
    echo '<div class="clear">&nbsp;</div>';
    $this->widget('application.modules.lang.components.langFieldWidget', array(
        'model' => $model,
        'field' => 'exchange_to',
        'type' => 'text'
    ));
}

$canSet = $model->canSetPeriodActivity() ? 1 : 0;

echo '<div class="rowold" id="set_period" ' . ( !$canSet ? 'style="display: none;"' : '' ) . '>';
echo $form->labelEx($model, 'period_activity');
echo $form->dropDownList($model, 'period_activity', HApartment::getPeriodActivityList());
echo CHtml::hiddenField('set_period_activity', $canSet);
echo $form->error($model, 'period_activity');
echo '</div>';

if(!$canSet) {
    echo '<div id="date_end_activity"><b>'.Yii::t('common', 'The listing will be active till {DATE}', array('{DATE}' => $model->getDateEndActivityLongFormat())).'</b>';
    echo '&nbsp;' . CHtml::link(tc('Change'), 'javascript:;', array(
            'onclick' => '$("#date_end_activity").hide(); $("#set_period_activity").val(1); $("#set_period").show();',
        ));
    echo '</div>';
}

?>

</div>