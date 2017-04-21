
<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo $search->getLabel(); ?>:</div> </span>
    <?php
    if($search->formdesigner->type == FormDesigner::TYPE_INT){
        $width = 'search-input-new width70';
    } elseif($search->formdesigner->type == FormDesigner::TYPE_REFERENCE || $search->formdesigner->type == FormDesigner::TYPE_MULTY){
        $width = 'search-input-new width290';
    } else {
        $width = 'search-input-new width285';
    }

    $value =  ($search->formdesigner->type == FormDesigner::TYPE_MULTY) ?
        ((isset($this->newFields[$search->field]) && is_array($this->newFields[$search->field])) ? $this->newFields[$search->field] : array()) :
        (isset($this->newFields[$search->field]) ? CHtml::encode($this->newFields[$search->field]) : '');

    echo '<span class="search sumo-pos-abs">';

    if($search->formdesigner->type == FormDesigner::TYPE_REFERENCE){
        echo CHtml::dropDownList($search->field, $value, CMap::mergeArray(array(0 => Yii::t('common', 'Please select')), $references),
            array('class' => 'searchField ' . $fieldClass)
        );
    }
	elseif($search->formdesigner->type == FormDesigner::TYPE_MULTY) {
        /*echo Chosen::multiSelect($search->field, $value, FormDesigner::getListByCategoryID($search->formdesigner->reference_id),
            array('class' => 'searchField ' . $fieldClass, 'data-placeholder' => $search->getLabel())
        );
        echo "<script>$('#" . $search->field . "').chosen();</script>";*/

		echo CHtml::dropDownList($search->field, $value, $references,
			array('class' => 'searchField ' . $fieldClass, 'placeholder' => tc('Please select'), 'multiple' => 'multiple')
		);
		echo "<script>$('#".$search->field."').SumoSelect({captionFormat:'".tc('{0} Selected')."', selectAlltext: '".tc('check all')."', csvDispCount:1, filter: true, filterText: '".tc('enter initial letters')."', filter: true, filterText: '".tc('enter initial letters')."'});</script>";
    }
	else{
        echo CHtml::textField($search->field, $value, array(
            'class' => $width,
            'onChange' => 'changeSearch();',
        ));

        if($search->formdesigner->type == FormDesigner::TYPE_INT && $search->formdesigner->measure_unit){
            echo '&nbsp;<span>' . $search->formdesigner->measure_unit.'</span>';
        }
    }

    echo '</span>';
    ?>
</div>