<div class="rowold">
    <?php echo CHtml::activeLabelEx($model, 'type'); ?>
    <?php echo CHtml::activeDropDownList($model, 'type', HApartment::getTypesArray(), array('class' => 'span3', 'id' => 'ap_type')); ?>
    <?php echo CHtml::error($model, 'type'); ?>
</div>