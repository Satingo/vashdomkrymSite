<div class="<?php echo $divClass; ?>">
	<?php if($this->searchShowLabel){ ?>
		<div class="<?php echo $textClass; ?>"><?php echo $search->getLabel(); ?>:</div>
	<?php } ?>

	<?php
		if($search->formdesigner->type == FormDesigner::TYPE_INT){
			$width = 'search-input-new width120';
		} elseif($search->formdesigner->type == FormDesigner::TYPE_REFERENCE || $search->formdesigner->type == FormDesigner::TYPE_MULTY){
			$width = 'search-input-new width290';
		} else {
			$width = 'search-input-new width285';
		}

		$value =  ($search->formdesigner->type == FormDesigner::TYPE_MULTY) ?
			((isset($this->newFields[$search->field]) && is_array($this->newFields[$search->field])) ? $this->newFields[$search->field] : array()) :
			(isset($this->newFields[$search->field]) ? CHtml::encode($this->newFields[$search->field]) : '');
	?>
	<div class="<?php echo $controlClass; ?> sumo-pos-abs">
		<?php
			if($search->formdesigner->type == FormDesigner::TYPE_REFERENCE){
				echo CHtml::dropDownList($search->field, $value, CMap::mergeArray(array(0 => $search->getLabel()), $references),
					array('class' => 'searchField ' . $fieldClass)
				);
			}
			elseif($search->formdesigner->type == FormDesigner::TYPE_MULTY) {
				/*echo Chosen::multiSelect($search->field, $value, FormDesigner::getListByCategoryID($search->formdesigner->reference_id),
					array('class' => 'searchField ' . $fieldClass, 'data-placeholder' => $search->getLabel())
				);
				echo "<script>$('#".$search->field."').chosen();</script>";*/

				echo CHtml::dropDownList($search->field, $value, $references,
					array('class' => 'searchField ' . $fieldClass, 'placeholder' => $search->getLabel(), 'multiple' => 'multiple')
				);
				echo "<script>$('#".$search->field."').SumoSelect({captionFormat: '".tc('{0} Selected')."', selectAlltext: '".tc('check all')."', csvDispCount:1, filter: true, filterText: '".tc('enter initial letters')."', filter: true, filterText: '".tc('enter initial letters')."'});</script>";
			}
			else{
				if (!$this->searchShowLabel) {
					echo CHtml::textField($search->field, $value, array(
						'class' => $width,
						'onChange' => 'changeSearch();',
						'placeholder' => $search->getLabel()
					));
				}
				else {
					echo CHtml::textField($search->field, $value, array(
						'class' => $width,
						'onChange' => 'changeSearch();',
					));
				}

				if($search->formdesigner->type == FormDesigner::TYPE_INT && $search->formdesigner->measure_unit){
					echo '&nbsp;<span class="measurement">' . $search->formdesigner->measure_unit.'</span>';
				}
			}
		?>
	</div>
</div>
