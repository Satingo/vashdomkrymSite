<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/usercpanel.css');

$this->beginContent('//layouts/inner');
?>
	<div class="usercpanel-left floatleft">
		<div id="usermenu">
			<?php
			$this->widget('zii.widgets.CMenu', array(
				'items' => HUser::getMenu(),
				'htmlOptions' => array(
					'id' => 'navlist',
				),
			));
			?>
		</div>

		<?php
			if (issetModule('tariffPlans') && issetModule('paidservices')) {
				$this->widget('application.modules.tariffPlans.components.userTariffInfoWidget', array('userId' => Yii::app()->user->id, 'showChangeTariffLnk' => true ));
			}
		?>
	</div>

	<div class="usercpanel-right floatleft">
		<?php echo $content; ?>
	</div>
	<div class="clear"></div>
<?php $this->endContent(); ?>