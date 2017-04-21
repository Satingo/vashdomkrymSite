<div class="<?php echo $divClass; ?>">
	<?php			
	if (issetModule('selecttoslider') && param('useSquareSlider') == 1) {
	?>
    <div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Square range').', '.tc("site_square");?> :</div>
	<div class="<?php echo $controlClass; ?>">
		<?php
			$squareAll = Apartment::getSquareMinMax();

			$squareAll['square_min'] = isset($squareAll['square_min']) ? floor($squareAll['square_min']) : 0;
			$squareAll['square_max'] = isset($squareAll['square_max']) ? ceil($squareAll['square_max']) : 100;

			$diffSquare = $squareAll['square_max'] - $squareAll['square_min'];

			$step = SearchForm::getSliderStep($diffSquare);

			$squareItems = array_combine(
				range($squareAll['square_min'], $squareAll['square_max'], $step),
				range($squareAll['square_min'], $squareAll['square_max'], $step)
			);

			// add last element if step less
			if (max($squareItems) != $squareAll["square_max"]) {
				$squareItems[$squareAll["square_max"]] = $squareAll["square_max"];
			}

			$squareMin = isset($this->squareCountMin) ? CHtml::encode($this->squareCountMin) : $squareAll['square_min'];
			$squareMax = isset($this->squareCountMax) ? CHtml::encode($this->squareCountMax) : max($squareItems);

			SearchForm::renderSliderRange(array(
				'field' => 'square',
				'min' => $squareAll['square_min'],
				'max' => $squareAll['square_max'],
				'min_sel' => $squareMin,
				'max_sel' => $squareMax,
				'step' => $step,
				//'measure_unit' => tc("site_square"),
				'class' => 'square-search-select',
			));
			echo '</div>';

			}
			else {
				?>
				<?php if($this->searchShowLabel){ ?>
					<div class="<?php echo $textClass; ?>"><?php echo tc('Square'); ?>:</div>
				<?php } ?>

				<?php
				$squareMin = isset($this->squareCountMin) ? CHtml::encode($this->squareCountMin) : '';
				$squareMax = isset($this->squareCountMax) ? CHtml::encode($this->squareCountMax) : '';
				?>
					
				<div class="<?php echo $controlClass; ?>">
					<input onblur="changeSearch();" type="number" id="squareMin" name="square_min" class="width120 search-input-new" placeholder="<?php echo tc('Square from') ?>" value="<?php echo CHtml::encode($squareMin); ?>"/>&nbsp;
					<input onblur="changeSearch();" type="number" id="squareMax" name="square_max" class="width120 search-input-new" placeholder="<?php echo tc('Square to') ?>" value="<?php echo CHtml::encode($squareMax); ?>"/>&nbsp;
					<span class=""><?php echo tc("site_square"); ?></span>
				</div>
				<?php

				Yii::app()->clientScript->registerScript('squareFocusSubmit', '
					focusSubmit($("input#squareMin"));
					focusSubmit($("input#squareMax"));
				', CClientScript::POS_READY);
			}
		?>
</div>
