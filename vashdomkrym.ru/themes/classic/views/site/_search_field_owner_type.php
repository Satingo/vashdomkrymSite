<div class="<?php echo $divClass; ?>">
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tt('Listing from', 'common'); ?>:</div></span>
	<span class="search">
		<?php
        $list = array(
            1 => tc('Private person'),
            2 => tc('Company'),
        );

        echo CHtml::dropDownList(
            'ot',
            isset($this->ot) ? CHtml::encode($this->ot) : '',
            $list,
            array(
                'empty' => tt('All', 'common'),
                'class' => $fieldClass . ' searchField'
            )
        );

        ?>
	</span>
</div>
