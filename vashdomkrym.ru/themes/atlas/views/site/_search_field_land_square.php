<div class="<?php echo $divClass; ?>">
    <?php if($this->searchShowLabel){ ?>
	<div class="<?php echo $textClass; ?>"><?php echo tc('Apartment square to'); ?>:</div>
    <?php } ?>
	<div class="<?php echo $controlClass; ?>">
		<input onblur="changeSearch();" type="number" name="land_square"
               placeholder="<?php echo tc('Apartment square to') ?>"
			   class="width120 search-input-new"
			   value="<?php echo isset($this->landSquare) && $this->landSquare ? CHtml::encode($this->landSquare) : ""; ?>"/>&nbsp;
		<span class="measurement"><?php echo tc("site_land_square"); ?></span>
	</div>
</div>
