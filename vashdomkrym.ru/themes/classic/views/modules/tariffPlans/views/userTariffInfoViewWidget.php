<?php if (issetModule('tariffPlans') && issetModule('paidservices')) : ?>
	<div class="tariff-info">
		<div class="sbox">
			<p class="name-param">
				<?php echo tt('Your current tariff plan', 'tariffPlans') . ':';?>
			</p>
			<p class="value-param">
				<strong><?php echo $name; ?></strong>
			</p>
		</div>

		<div class="user-tariff-plan-info-detail">
			<?php if (!$isDefaultTariffPlan && $tariffDateEnd):?>
				<div class="sbox">
					<?php $color = ($tariffDateEnd <= date('Y-m-d', strtotime("+5 day"))) ? '#FF0000' : '#008000'; ?>

					<p class="name-param">
						<?php echo tc('Date of completion') . ':';?>
					</p>
					<p class="value-param">
						<span style="color: <?php echo $color;?>;"><strong><?php echo $tariffDateEndFormat;?></strong></span>
					</p>
				</div>
			<?php endif;?>

			<div class="sbox">
				<p class="name-param">
					<?php echo tt('Show_address', 'tariffPlans').':';?>
				</p>
				<p class="value-param">
					<?php echo ($showAddress) ? tc("Yes") : tc("No");?>
				</p>
			</div>

			<div class="sbox">
				<p class="name-param">
					<?php echo tt('Show_phones', 'tariffPlans').':';?>
				</p>
				<p class="value-param">
					<?php echo ($showPhones) ? tc("Yes") : tc("No");?>
				</p>
			</div>

			<div class="sbox">
				<p class="name-param">
					<?php echo tt('Limit_objects', 'tariffPlans').':';?>
				</p>
				<p class="value-param">
					<?php echo ($limitObjects) ? $limitObjects : tt('Unlimited', 'tariffPlans');?>
				</p>
			</div>

			<div class="sbox">
				<p class="name-param">
					<?php echo tt('Limit_photos', 'tariffPlans').':';?>
				</p>
				<p class="value-param">
					<?php echo ($limitPhotos) ? $limitPhotos : tt('Unlimited', 'tariffPlans');?>
				</p>
			</div>
		</div>

		<?php if($this->showChangeTariffLnk):?>
			<div class="change-tariff-plan-lnk">
				<?php echo CHtml::link(tc('Change tariff plan'), Yii::app()->createUrl('/tariffPlans/main/index'));?>
			</div>
		<?php endif;?>
	</div>
<?php endif;?>