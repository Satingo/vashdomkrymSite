<?php
$this->pageTitle .= ' - '.tc('Tariff Plans');
$this->breadcrumbs = array(
	tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
	tc('Tariff Plans'),
);
?>

<div class="form min-fancy-width <?php echo (isset($isFancy) && $isFancy) ? 'white-popup-block' : ''; ?>">
	<h1 class="title highlight-left-right">
		<span><?php echo tc('Tariff Plans'); ?></span>
	</h1>
	<div class="clear"></div><br />

	<?php if (count($tariffsArray)):?>
		<div class="rowold">
			<?php
				echo CHtml::beginForm(array('/paidservices/main/index'));
				echo CHtml::hiddenField('pay_submit', 1);

				Yii::app()->getModule('payment');
				$paySystems = Paysystem::getPaysystems();
				$paySystemsArray = CHtml::listData($paySystems, 'id', 'name');
			?>
			<?php
				echo CHtml::dropDownList('tariffid', null, $tariffsArray,
					array('class' => 'width300', 'onchange' => 'showDescription(this.value);', 'id' => 'tariffid')
				);
			?>
			<br/>

			<div class="rowold" id="description_tariff">
				<?php
					$tmp = reset($tariffPlans);
					echo $tmp['descriptionForBuy'];
				?>
			</div>
			<br/>

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
				<?php echo CHtml::submitButton(tc('Proceed'), array('id' => 'proceed', 'class' => 'button-blue', 'confirm'=> tt('Are you sure you want to change the tariff plan?', 'tariffPlans'))); ?>
			</div>

			<?php echo CHtml::endForm();?>

			<div class="form_tip">
				<?php echo tt('help_buy_tariff_plan', 'tariffPlans');?><br />
				<?php echo tt('help_buy_tariff_plan2', 'tariffPlans');?>
			</div>
		</div>

		<?php
			$descriptions = '';
			foreach($tariffPlans as $tariffPlan){
				$descriptions .= 'descr['.$tariffPlan["id"].'] = "'.CJavaScript::quote($tariffPlan['descriptionForBuy']).'";'."\n";
			}

			Yii::app()->clientScript->registerScript('showDescription', '
				var descr = new Array();
				'.$descriptions.'

				function showDescription(id){
					$("#description_tariff").html("");

					if(descr[id]){
						$("#description_tariff").html(descr[id]);
					}
				}
			', CClientScript::POS_END);

			Yii::app()->clientScript->registerScript('change_tariff_init', '
					var selTariff = $("#tariffid").val();

					showDescription(selTariff);
				', CClientScript::POS_READY);

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
	<?php else:?>
		<?php echo tt('No active tariff plans', 'tariffPlans');?>
	<?php endif;?>
</div>