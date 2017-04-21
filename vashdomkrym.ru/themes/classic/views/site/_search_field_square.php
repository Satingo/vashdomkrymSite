<div class="<?php echo $divClass; ?>">
    <?php
    if (issetModule('selecttoslider') && param('useSquareSlider') == 1) {
    ?>
    <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Square range'); ?>:</div> </span>
        <span class="search">
            <?php
            $squareAll = Apartment::getSquareMinMax();

            $squareAll['square_min'] = isset($squareAll['square_min']) ? floor($squareAll['square_min']) : 0;
            $squareAll['square_max'] = isset($squareAll['square_max']) ? ceil($squareAll['square_max']) : 100;

            $diffSquare = $squareAll['square_max'] - $squareAll['square_min'];

            if ($diffSquare <= 10) {
                $step = 1;
            } else {
                $step = 5;
            }

            if ($diffSquare > 100) {
                $step = 10;
            }
            if ($diffSquare > 1000) {
                $step = 100;
            }
            if ($diffSquare > 10000) {
                $step = 1000;
            }
            if ($diffSquare > 100000) {
                $step = 10000;
            }

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
                'measure_unit' => tc("site_square"),
                'class' => 'square-search-select',
            ));
            echo '</span>';

            }
            else {
                ?>
			
				<?php
				$squareMin = isset($this->squareCountMin) ? CHtml::encode($this->squareCountMin) : '';
				$squareMax = isset($this->squareCountMax) ? CHtml::encode($this->squareCountMax) : '';
				?>
			
                <span class="search"><div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Square'); ?>:</div></span>
                <span class="search">
					<input onblur="changeSearch();" id="squareMin" name="square_min" class="width120 search-input-new" placeholder="<?php echo tc('Square from') ?>" value="<?php echo CHtml::encode($squareMin); ?>"/>&nbsp;
					<input onblur="changeSearch();" id="squareMax" name="square_max" class="width120 search-input-new" placeholder="<?php echo tc('Square to') ?>" value="<?php echo CHtml::encode($squareMax); ?>"/>&nbsp;
                    <span><?php echo tc("site_square"); ?></span>
                </span>
                <?php

                Yii::app()->clientScript->registerScript('squareFocusSubmit', '
					focusSubmit($("input#squareMin"));
					focusSubmit($("input#squareMax"));
				', CClientScript::POS_READY);
            }
            ?>
</div>
