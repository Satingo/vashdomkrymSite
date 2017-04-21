<?php
/**
 * @var $model User
 */

$title = $model->getNameForType() . ', ' . $model->getTypeName();
$this->pageTitle = $title;
$this->breadcrumbs=array(
    $title,
);

?>

<h1 class="title highlight-left-right"><span><?php echo $title;?></span></h1>

<div class="user_page">
    <p class="meta">
        <?php
        if (issetModule('tariffPlans') && issetModule('paidservices')) {
            if (TariffPlans::checkAllowShowPhone())
                echo '<span>' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$model->id.');')) . '</span>';
        }
        else {
            echo '<span>' . CHtml::link(tc('Show phone'), 'javascript: void(0);', array('onclick' => 'getPhoneNum(this, '.$model->id.');')) . '</span>';
        }
        ?>
    </p>

    <?php if (issetModule('messages') && $model->id != Yii::app()->user->id && !Yii::app()->user->isGuest):?>
        <p class="meta">
            <span><?php echo '<span>' . CHtml::link(tt('Send message', 'messages'), Yii::app()->createUrl('/messages/main/read', array('id' => $model->id))) . '</span>';?></span>
        </p>
    <?php endif;?>

    <p>
        <?php
        $model->renderAva(false, '', true);
        $additionalInfo = 'additional_info_'.Yii::app()->language;
        if (isset($model->$additionalInfo) && !empty($model->$additionalInfo)){
            echo CHtml::encode(truncateText($model->$additionalInfo, 40));
        }
        ?>
    </p>

</div>

<div class="clear"></div>
<br>

<?php $this->widget('application.modules.apartments.components.ApartmentsWidget', array(
    'criteria' => $criteria,
    'widgetTitle' => tt('all_member_listings', 'apartments'). ' '.$userName,
));

Yii::app()->clientScript->registerScript('generate-phone', '
				function getPhoneNum(elem, id){
					$(elem).closest("span").html(\'<img src="'.Yii::app()->controller->createUrl('/users/main/generatephone').'?id=\' + id + \'" />\');
				}
			', CClientScript::POS_END);

?>