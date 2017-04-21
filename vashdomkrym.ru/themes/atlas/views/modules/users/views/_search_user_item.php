<?php
$count = $index + 1;
$isLast = ($count % 3) ? false : true;
$addClass = ($isLast) ? 'last' : '';
?>

<div class="one_third <?php echo $addClass;?>">
	<h3>
		<?php echo CHtml::link( ($data->type == User::TYPE_AGENCY ? $data->agency_name : $data->username ), $data->getUrl() );?>
		<?php echo ', ' . $data->getTypeName();;?>
	</h3>

	<p class="meta">
		<?php
        if ($data->phone) {
            if (issetModule('tariffPlans') && issetModule('paidservices') && ($data->id != Yii::app()->user->id)) {
                if (Yii::app()->user->isGuest) {
                    $defaultTariffInfo = TariffPlans::getFullTariffInfoById(TariffPlans::DEFAULT_TARIFF_PLAN_ID);

                    if (!$defaultTariffInfo['showPhones']) {
                        //echo '<p><span>'. Yii::t('module_tariffPlans', 'Please <a href="{n}">login</a> to view', Yii::app()->controller->createUrl('/site/login')).'</span></p>';
                    }
                    else {
                        echo '<p><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</p>';
                    }
                }
                else {
                    if (TariffPlans::checkAllowShowPhone())
                        echo '<p><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</p>';
                    else {
                        //echo '<p><span>'.Yii::t('module_tariffPlans', 'Please <a href="{n}">change the tariff plan</a> to view', Yii::app()->controller->createUrl('/tariffPlans/main/index')).'</span></p>';
                    }
                }
            }
            else {
                echo '<p><span id="owner-phone">' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$data->id.');')) . '</span>' . '</p>';
            }
        }
		?>
		<?php echo '<p><span>' . $data->getLinkToAllListings() . '</span></p>'; ?>
	</p>

	<?php if (issetModule('messages') && $data->id != Yii::app()->user->id && !Yii::app()->user->isGuest):?>
		<p class="meta">
			<span><?php echo '<span>' . CHtml::link(tt('Send message', 'messages'), Yii::app()->createUrl('/messages/main/read', array('id' => $data->id))) . '</span>';?></span>
		</p>
	<?php endif;?>

	<p>
		<?php
        $data->renderAva(true, '', true);
        $additionalInfo = 'additional_info_'.Yii::app()->language;
        if (isset($data->$additionalInfo) && !empty($data->$additionalInfo)){
            echo CHtml::encode(truncateText($data->$additionalInfo, 40));
        }
		?>
	</p>
</div>