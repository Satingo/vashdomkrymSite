<?php
$this->pageTitle .= ' - '.tc('My balance');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tc('My balance'),
);

$user = HUser::getModel();
?>

<h1 class="title highlight-left-right">
<span>
	<?php echo tc('My balance'); ?>
</span>
</h1>
<div class="clear"></div><br />

<?php
echo '<strong>' . tc('On the balance') . ': ' . $user->balance . ' ' . Currency::getDefaultCurrencyName() . '</strong>';
echo '<br />';

echo CHtml::link(tt('Replenish the balance'), Yii::app()->createUrl('/paidservices/main/index', array('paid_id' => PaidServices::ID_ADD_FUNDS)), array('class' => 'fancy mgp-open-ajax'));