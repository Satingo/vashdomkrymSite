<div class="<?php echo $divClass; ?>">
	<span class="search-term<?php echo $isInner ? ' search-term-inner' : ''?>">
		<?php
			$term = '';
			if(isset($this->term)){
				$term = $this->term;
			}
			echo CHtml::textField('term', $term, array(
				'class' => 'textbox',
				'id' => 'search_term_text',
				'maxlength' => 50,
				'placeholder' => tc("Search by description or address"),
			));
		?>
		<input type="button" class="search-icon" value="<?php echo tc("Search");?>" onclick="prepareSearch(); return false;">
		<input type="hidden" value="0" id="do-term-search" name="do-term-search">
	</span>
</div>