<div class="<?php echo $divClass; ?>">
    <div class="<?php echo $controlClass; ?>">
        <?php
        echo CHtml::checkBox('wp', (isset($this->wp) && $this->wp) ? CHtml::encode($this->wp) : '', array(
            'class' => 'search-input-new searchField',
            'id' => 'search_with_photo'
        ));

        echo CHtml::label(Yii::t('common', 'Only with photo'), 'search_with_photo', array('class' => 'formalabel'));
        ?>
    </div>
</div>
