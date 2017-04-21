<?php
$this->pageTitle=Yii::app()->name . ' - ' . tt('Service site', 'common');
$this->adminTitle = tt('Service site', 'common');
$this->menu = array(
	array(),
);
?>
<div class="form well form-vertical">
	<div class="row-fluid">
		<div id="result"></div>
	</div>
	
	<div class="row-fluid">
		<div class="span8">
			<div class="span4">
				<?php $this->widget('bootstrap.widgets.TbButton',
					array('buttonType'=>'link',
						//'type'=>'warning',
						'type'=>'info',
						'icon'=>'repeat white',
						'label'=> tt('Clear assets', 'service'),
						'htmlOptions' => array('class' => 'confirm-reset', 'value' => 'assets')
					));
			?>
			</div>
			<div class="span4">
				<?php $this->widget('bootstrap.widgets.TbButton',
					array('buttonType'=>'link',
						'type'=>'inverse',
						//'icon'=>'off white',
						'icon'=>'repeat white',
						'label'=> tt('Clear runtime', 'service'),
						'htmlOptions' => array('class' => 'confirm-reset', 'value' => 'runtime')

					));
				?>
			</div>
		</div>
	</div>
</div>

<div class="clear"></div>

<?php echo $this->renderPartial('/backend/_form', array('model'=>$model)); ?>

<?php
Yii::app()->clientScript->registerScript('confirm-clear-cache-maintenance', '
	$("a.confirm-reset").on("click", function () {
		if ($(this).attr("disabled") == "disabled" || !confirm("'.tt('Are you sure you want to empty the cache?', 'service').'")) {
			return false;
		}

		$(this).attr("disabled", "disabled");

		$.ajax({
			url: "'.Yii::app()->createUrl('/service/backend/main/doclear').'",
			data: {target:$(this).attr("value")},
			success: function(result){
				$("#result").html(result);
				$("a.confirm-reset").removeAttr("disabled");
			}
		});
	});
', CClientScript::POS_READY);