<div class="rowold no-mrg">
    <?php echo CHtml::activeLabel($model, 'price', array('required' => true)); ?>

    <?php echo CHtml::activeCheckBox($model, 'is_price_poa'); ?>
    <?php echo CHtml::activeLabelEx($model, 'is_price_poa', array('class' => 'noblock')); ?>
    <?php echo CHtml::error($model, 'is_price_poa'); ?>

    <div id="price_fields">
        <?php
        if($model->type == Apartment::TYPE_RENT && issetModule('seasonalprices')) {
			if (!isset($seasonalPricesModel)) {
				$seasonalPricesModel = new Seasonalprices;
			}
            $this->renderPartial('//modules/seasonalprices/views/_form', array('apartment' => $model, 'seasonalPricesModel' => $seasonalPricesModel, 'form' => $form));
        } else {
            if ($model->isPriceFromTo()) {
                echo tc('price_from') . ' ' . CHtml::activeTextField($model, 'price', array('class' => 'width100 noblock'));
                echo ' ' .tc('price_to') . ' ' . CHtml::activeTextField($model, 'price_to', array('class' => 'width100'));
            } else {
                echo CHtml::activeTextField($model, 'price', array('class' => 'width100'));
            }

            if(issetModule('currency')){
                echo '&nbsp;' . Currency::getDefaultCurrencyName();
                $model->in_currency = Currency::getDefaultCurrencyModel()->char_code;
                echo CHtml::activeHiddenField($model, 'in_currency');
                // CHtml::activedropDownList($model, 'in_currency', Currency::getActiveCurrencyArray(2), array('class' => 'width120'))
            } else {
                echo '&nbsp;'.param('siteCurrency', '$');
            }

            if($model->type == Apartment::TYPE_RENT){
                $priceArray = HApartment::getPriceArray($model->type);		
                if(!in_array($model->price_type, array_keys($priceArray))){
                    $model->price_type = Apartment::PRICE_PER_HOUR;
                }
                echo '&nbsp;'.CHtml::activeDropDownList($model, 'price_type', HApartment::getPriceArray($model->type), array('class' => 'width150'));
            }
        }
        ?>
    </div>
    <?php echo CHtml::error($model, 'price'); ?>
</div>

<div class="clear"></div>
<br />
