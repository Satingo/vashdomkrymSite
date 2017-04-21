<?php if($model->canShowInForm('floor_all')){ ?>
    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model, 'floor', array('class' => 'noblock')); ?> /
        <?php echo CHtml::activeLabelEx($model, 'floor_total', array('class' => 'noblock')); ?><br/>
        <?php echo HApartment::getTip('floor_all');?>
        <?php echo CHtml::activeDropDownList($model, 'floor',
            array_merge(
                array('0' => ''),
                range(1, param('moduleApartments_maxFloor', 30))
            ), array('class' => 'width50')); ?> /
        <?php echo CHtml::activeDropDownList($model, 'floor_total',
            array_merge(
                array('0' => ''),
                range(1, param('moduleApartments_maxFloor', 30))
            ), array('class' => 'width50')); ?>
        <?php echo CHtml::error($model, 'floor'); ?>
        <?php echo CHtml::error($model, 'floor_total'); ?>
        <?php echo HApartment::getTip('floor_all');?>
    </div>
<?php } ?>