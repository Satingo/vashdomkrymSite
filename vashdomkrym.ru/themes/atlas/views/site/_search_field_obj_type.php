<div class="<?php echo $divClass; ?>">
    <?php if($this->searchShowLabel){ ?>
	<div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Property type'); ?>:</div>
    <?php } ?>
	<div class="<?php echo $controlClass; ?>">
    <?php
    echo CHtml::dropDownList(
        'objType',
        isset($this->objType) ? $this->objType : 0, CMap::mergeArray(array(0 => Yii::t('common', 'Property type')),
        Apartment::getObjTypesArray()),
        array('class' => $fieldClass)
    );
    Yii::app()->clientScript->registerScript('objType', '
		focusSubmit($("select#objType"));
	', CClientScript::POS_READY);
    ?>
	</div>
</div>