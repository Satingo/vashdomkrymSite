<?php if($model->canShowInForm('berths')){ ?>
    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model, 'berths'); ?>
        <?php echo HApartment::getTip('berths');?>
        <?php echo CHtml::activeTextField($model, 'berths', array('class' => 'width150', 'maxlength' => 255)); ?>
        <?php echo CHtml::error($model, 'berths'); ?>
    </div>
<?php } ?>