<div class="user_item">
<?php

echo '<div class="user_item-ava">';
echo $data->renderAva();
echo $data->type == User::TYPE_AGENCY ? $data->agency_name : $data->username;
echo ', ' . $data->getTypeName();
echo '</div>';

echo '<div class="user_item-right">';

echo '<ul class="user_item-ul">';
    if ($data->phone) {
        $icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/phone-16.png');

        if (issetModule('tariffPlans') && issetModule('paidservices') && ($data->id != Yii::app()->user->id)) {
            if (Yii::app()->user->isGuest) {
                $defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

                if (!$defaultTariffInfo['showPhones']) {
                    echo '<li>'. $icon . Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login')).'</li>';
                }
                else {
                    echo '<li>'. $icon .'<span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</li>';
                }
            }
            else {
                if (TariffPlans::checkAllowShowPhone())
                    echo '<li>'.$icon.'<span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</li>';
                else
                    echo '<li>'.$icon.Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index')).'</li>';
            }
        }
        else {
            echo '<li>'.$icon.'<span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</li>';
        }
    }

	$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/ads-16.png') . ' ';
	echo '<li>' . $icon . $data->getLinkToAllListings() . '</li>';

	if (issetModule('messages') && $data->id != Yii::app()->user->id && !Yii::app()->user->isGuest){
		$icon = CHtml::image(Yii::app()->theme->baseUrl . '/images/design/email-16.png') . ' ';
		echo '<li>' . $icon . CHtml::link(tt('Send message', 'messages'), Yii::app()->createUrl('/messages/main/read', array('id' => $data->id))) . '</li>';
	}
echo '</ul>';

echo '</div>';

echo '<div class="clear"></div>';

$additionalInfo = 'additional_info_'.Yii::app()->language;
if (isset($data->$additionalInfo) && !empty($data->$additionalInfo)){
    echo '<div class="clear"></div>';
    echo CHtml::encode(truncateText($data->$additionalInfo, 20));
}

?>
</div>
