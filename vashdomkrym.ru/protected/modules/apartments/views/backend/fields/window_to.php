<?php if($model->canShowInForm('window_to')){ ?>
    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model, 'window_to'); ?>
        <?php echo HApartment::getTip('window_to');?>
        <?php echo CHtml::activeDropDownList($model, 'window_to', WindowTo::getWindowTo(), array('class' => 'width150')); ?>
        <?php echo CHtml::error($model, 'window_to'); ?>
    </div>
<?php } ?>