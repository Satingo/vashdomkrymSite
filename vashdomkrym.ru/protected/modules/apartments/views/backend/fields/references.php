<?php if ($model->canShowInForm('references')) { ?>

    <div class="apartment-description-item">
        <?php

        $prev = '';
        $column1 = 0;
        $column2 = 0;
        $column3 = 0;

        $count = 0;
        foreach ($model->references as $catId => $category) {

            if(!isset($category['type'])){
                continue;
            }

            if ($category['type'] == ReferenceCategories::TYPE_STANDARD) {
                if (isset($category['values']) && $category['values'] && isset($category['title'])) {

                    if ($prev != $category['style']) {
                        $column2 = 0;
                        $column3 = 0;
                        echo '<div class="clear">&nbsp;</div>';
                    }
                    $$category['style']++;
                    $prev = $category['style'];
                    echo '<div class="' . $category['style'] . '">';
						echo '<input type="checkbox" id="ref-check-all-'.$catId.'" class="ref-check-all" title="' . CHtml::encode(tc('check all')) . '"/>';
						echo '<label for="ref-check-all-'.$catId.'" class="viewapartment-subheader subheader-clickable">'. $category['title'] . '</label>';

						echo '<ul class="no-disk">';
							foreach ($category['values'] as $valId => $value) {
								if ($value) {
									$checked = $value['selected'] ? 'checked="checked"' : '';
									if (array_key_exists('title', $value)) {
										echo '<li><input type="checkbox"  class="s-categorybox" id="category[' . $catId . '][' . $valId . ']" name="category[' . $catId . '][' . $valId . ']" ' . $checked . '/>
												<label for="category[' . $catId . '][' . $valId . ']" />' . $value['title'] . '</label></li>';
									}
								}
							}
						echo '</ul>';
                    echo '</div>';
                    if (($category['style'] == 'column2' && $column2 == 2) || $category['style'] == 'column3' && $column3 == 3) {
                        echo '<div class="clear"></div>';
                    }
                }
            }
        }
        Yii::app()->clientScript->registerScript('ref-check-all', '
				$(".ref-check-all").on("click", function(){
					var elems = $(this).closest("div").find(".s-categorybox");
					if($(this).is(":checked")){
						elems.prop("checked", true);
					} else {
						elems.prop("checked", false);
					}
				});

				$(".ref-check-all").each(function(){
					var elems = $(this).closest("div").find(".s-categorybox");
					if($(this).closest("div").find(".s-categorybox:checked").length == elems.length){
						$(this).attr("checked", true);
					}
				});
			', CClientScript::POS_READY);
        ?>
        <div class="clear"></div>
    </div>

    <div class="clear">&nbsp;</div>
<?php } ?>