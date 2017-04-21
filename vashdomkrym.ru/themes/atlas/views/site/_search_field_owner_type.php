<div class="<?php echo $divClass; ?>">
    <?php if($this->searchShowLabel){ ?>
        <span class="search"><div class="<?php echo $textClass; ?>"><?php echo tt('Listing from', 'common'); ?>:</div></span>
    <?php } ?>

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
                'empty' => tt('Listing from', 'common'),
                'class' => $fieldClass . ' searchField'
            )
        );

        ?>
	</span>
</div>
