<?php
/**
 * @var $user User
 * @var $paidService PaidServices
 */
?>
<div class="form min-fancy-width <?php echo (isset($isFancy) && $isFancy) ? 'white-popup-block' : ''; ?>">

<h2 class="title highlight-left-right">
	<span><?php echo tc('Paid service'); ?>: <?php echo $paidService->name; ?></span>
</h2>
<div class="clear"></div><br />

<?php if($paidService->description){
    echo '<p>'.$paidService->description.'</p>';
}

echo CHtml::beginForm(array('/paidservices/main/index'));
	echo CHtml::hiddenField('pay_submit', 1);
?>

<?php if($paidService->id == PaidServices::ID_ADD_FUNDS || $paidService->id == PaidServices::ID_ADD_FUNDS_TO_AGENT){ ?>
	<p>
		<div class="rowold">
			<?php echo tc('Add amount of'); ?>
			<?php echo CHtml::textField('amount', 100) . ' ' . Currency::getDefaultCurrencyName(); ?>
		</div>
	</p>
<?php }else{ ?>
	<p>
		<?php echo tc('Will be used for listings'); ?>
		ID: <?php echo CHtml::link($ad_id, Apartment::getUrlById($ad_id)); ?>
	</p>
<?php } ?>

<?php
Yii::app()->getModule('payment');
if($paidService->id != PaidServices::ID_ADD_FUNDS){
	$paySystems = Paysystem::getPaysystems();
}else{
	$paySystems = Paysystem::getPaysystemsWithoutBalance();
}
$paySystemsArray = CHtml::listData($paySystems, 'id', 'name');

echo '<p>';
$options = $paidService->getListOptions();
if($options){
	$optionsId = array_keys($options);
	echo CHtml::radioButtonList('option_id', $optionsId[0], $options, array('labelOptions' => array('class' => 'noblock')));
}
echo '</p>';
?>

<div class="rowold">
	<?php echo tc('Pay with'); ?>:
	<?php
		if(count($paySystems) > 1){
			echo CHtml::dropDownList('pay_id', null, $paySystemsArray,
					array('class' => 'width300', 'onchange' => 'showDescription(this.value);')
			);
		} else {
			echo CHtml::hiddenField('pay_id', reset($paySystems)->id);
			echo '<strong>'.reset($paySystems)->name.'</strong>';
		}
	?>
</div>
<br/>
<div class="rowold" id="description">
	<?php echo reset($paySystems)->getDescription(); ?>
</div>
<div class="rowold submit">
	<?php
		echo CHtml::hiddenField('paid_id', $paidService->id);
		echo CHtml::hiddenField('id', $ad_id);
		echo CHtml::hiddenField('agent_id', $agent_id);
		echo CHtml::submitButton(tc('Proceed'), array('class' => 'button-blue'));
	?>
</div>
<?php
echo CHtml::endForm();

$descriptions = '';
foreach($paySystems as $model){
	$descriptions .= 'descr['.$model->id.'] = "'.CJavaScript::quote($model->getDescription()).'";'."\n";
}

Yii::app()->clientScript->registerScript('showDescription', '
	var descr = new Array();
	'.$descriptions.'
	function showDescription(id){
		$("#description").html("");
		if(descr[id]){
			$("#description").html(descr[id]);
		}
	}
', CClientScript::POS_END);

?>
</div>