<?php $callFrom = (isset($callFrom)) ? $callFrom : null; ?>

<?php if (issetModule('location')): ?>
    <?php $countries = Country::getCountriesArray();?>
    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model,'loc_country'); ?>
        <?php echo CHtml::activeDropDownList($model,'loc_country',$countries,
            array(
                'id'=>'ap_country',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getRegions'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"country="+$("#ap_country").val()',
                    'success'=>'function(result){
                                $("#ap_region").html(result);
                                $("#ap_region").change();
                            }'
                    //leave out the data key to pass all form values through
                ),
                'class' => 'span3'
            )
        ); ?>
        <?php echo CHtml::error($model,'loc_country'); ?>
    </div>

    <?php
    //при создании города узнаём id первой в дропдауне страны
    if ($model->loc_country) {
        $country = $model->loc_country;
    } else {
        $country_keys = array_keys($countries);
        $country = isset($country_keys[0]) ? $country_keys[0] : 0;
    }

    $regions=Region::getRegionsArray($country);

    if ($model->loc_region) {
        $region = $model->loc_region;
    } else {
        $region_keys = array_keys($regions);
        $region = isset($region_keys[0]) ? $region_keys[0] : 0;
    }

    $cities = City::getCitiesArray($region, 0, 2);

    if ($model->loc_city) {
        $city = $model->loc_city;
    } else {
        $city_keys = array_keys($cities);
        $city = isset($city_keys[0]) ? $city_keys[0] : 0;
    }
    ?>

    <div class="rowold">
        <?php echo CHtml::activeLabelEx($model,'loc_region'); ?>
        <?php echo CHtml::activeDropDownList($model,'loc_region',$regions,
            array('id'=>'ap_region',
                'ajax' => array(
                    'type'=>'GET', //request type
                    'url'=>$this->createUrl('/location/main/getCities'), //url to call.
                    //Style: CController::createUrl('currentController/methodToCall')
                    'data'=>'js:"region="+$("#ap_region").val()',
                    'success'=>'function(result){
						$("#ap_city").html(result);'.((issetModule('metroStations')) ? '$("#ap_city").change()' : '').
					'}'

                ),
                'class' => 'span3'
            )
        ); ?>
        <?php echo CHtml::error($model,'loc_region'); ?>
    </div>

	<?php if (issetModule('metroStations')):?>
		<?php $metros = MetroStations::getMetrosArray($city, 0);?>
		<div class="rowold" id="locationCity"<?php if(param('allowCustomCities') && $model->customCity){echo ' style="display: none;"';}?>>
			<?php echo CHtml::activeLabelEx($model,'loc_city'); ?>
			<?php echo CHtml::activeDropDownList($model,'loc_city',$cities,
					array(
						'id'=>'ap_city',
						'ajax' => array(
							'type'=>'GET',
							'url'=>$this->createUrl('/metroStations/main/getMetroStations'),
							'data'=>'js:"city="+$("#ap_city").val()+"&type=0"',
							'dataType'=>'json',
							'success'=>'function(result){
								if (result.dropdownMetro) {
									$("#metro-block-apartment").show();
									$("#ap_metro").html(result.dropdownMetro);
									$("#ap_metro").trigger("chosen:updated");
								}
								else {
									$("#ap_metro").html("");
									$("#ap_metro").trigger("chosen:updated");
									$("#metro-block-apartment").hide();
								}
							}'

						),
						'class' => 'span3'
					)
				); ?>
			<?php echo CHtml::error($model,'loc_city'); ?>
			<?php if (param('allowCustomCities'))
				echo CHtml::link(tt('Custom city', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
		</div>
		<?php if (param('allowCustomCities')):?>
			<div class="rowold" id="customCity"<?php if(!$model->customCity){echo ' style="display: none;"';}?>>
				<?php echo $form->labelEx($model,'customCity'); ?>
				<?php echo $form->textField($model,'customCity',array('class' => 'span3')); ?>
				<?php echo $form->error($model,'customCity'); ?>
				<?php echo CHtml::link(tt('City from list', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
				<?php echo CHtml::hiddenField('isCustomCity', ($model->customCity) ? 1 : 0, array('id'=>'isCustomCity')); ?>
			</div>
		<?php endif;?>
		<div class="rowold" id="metro-block-apartment" style="display: <?php echo ($metros && count($metros) > 1) ? 'block;' : 'none;';?>">
			<?php echo CHtml::activeLabelEx($model,'metroStations'); ?>
			<?php
				echo Chosen::multiSelect(get_class($model).'[metroStations]', $model->metroStations, $metros,
					array('id'=>'ap_metro', 'class' => 'width500', 'data-placeholder' => tt('Select metro stations', 'metroStations'))
				);
				if ($callFrom != 'guestAdModule') echo "<script>$('#ap_metro').chosen();</script>";
			?>
			<?php echo CHtml::error($model,'metroStations'); ?>
		</div>
	<?php else:?>
		<div class="rowold" id="locationCity"<?php if(param('allowCustomCities') && $model->customCity){echo ' style="display: none;"';}?>>
			<?php echo CHtml::activeLabelEx($model,'loc_city'); ?>
			<?php echo CHtml::activeDropDownList($model,'loc_city',$cities,array('id'=>'ap_city', 'class' => 'span3')); ?>
			<?php echo CHtml::error($model,'loc_city'); ?>

			<?php if (param('allowCustomCities'))
				echo CHtml::link(tt('Custom city', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
		</div>
		<?php if (param('allowCustomCities')):?>
			<div class="rowold" id="customCity"<?php if(!$model->customCity){echo ' style="display: none;"';}?>>
				<?php echo $form->labelEx($model,'customCity'); ?>
				<?php echo $form->textField($model,'customCity',array('class' => 'span3')); ?>
				<?php echo $form->error($model,'customCity'); ?>
				<?php echo CHtml::link(tt('City from list', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
				<?php echo CHtml::hiddenField('isCustomCity', ($model->customCity) ? 1 : 0, array('id'=>'isCustomCity')); ?>
			</div>
		<?php endif;?>
	<?php endif;?>

<?php else: ?>
	<?php
		$cities = ApartmentCity::getCityArray(false, 2);

		if ($model->city_id) {
			$city = $model->city_id;
		}
		else {
			$city_keys = array_keys($cities);
			$city = isset($city_keys[0]) ? $city_keys[0] : 0;
		}
	?>
	<?php if (issetModule('metroStations')):?>
		<?php $metros = MetroStations::getMetrosArray($city, 0);?>
		<div class="rowold" id="locationCity"<?php if(param('allowCustomCities') && $model->customCity){echo ' style="display: none;"';}?>>
			<?php echo CHtml::activeLabelEx($model, 'city_id'); ?>
			<?php echo CHtml::activeDropDownList($model, 'city_id', $cities,
					array(
						'id'=>'ap_city',
						'ajax' => array(
							'type'=>'GET',
							'url'=>$this->createUrl('/metroStations/main/getMetroStations'),
							'data'=>'js:"city="+$("#ap_city").val()+"&type=0"',
							'dataType'=>'json',
							'success'=>'function(result){
								if (result.dropdownMetro) {
									$("#metro-block-apartment").show();
									$("#ap_metro").html(result.dropdownMetro);
									$("#ap_metro").trigger("chosen:updated");
								}
								else {
									$("#ap_metro").html("");
									$("#ap_metro").trigger("chosen:updated");
									$("#metro-block-apartment").hide();
								}
							}'

						),
						'class' => 'span3'
					)
				); ?>
			<?php echo CHtml::error($model, 'city_id'); ?>
			<?php if (param('allowCustomCities'))
				echo CHtml::link(tt('Custom city', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
		</div>
		<?php if (param('allowCustomCities')):?>
			<div class="rowold" id="customCity"<?php if(!$model->customCity){echo ' style="display: none;"';}?>>
				<?php echo $form->labelEx($model,'customCity'); ?>
				<?php echo $form->textField($model,'customCity',array('class' => 'span3')); ?>
				<?php echo $form->error($model,'customCity'); ?>
				<?php echo CHtml::link(tt('City from list', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
				<?php echo CHtml::hiddenField('isCustomCity', ($model->customCity) ? 1 : 0, array('id'=>'isCustomCity')); ?>
			</div>
		<?php endif;?>
		<div class="rowold" id="metro-block-apartment" style="display: <?php echo ($metros && count($metros) > 1) ? 'block;' : 'none;';?>">
			<?php echo CHtml::activeLabelEx($model,'metroStations'); ?>
			<?php
				echo Chosen::multiSelect(get_class($model).'[metroStations]', $model->metroStations, $metros,
					array('id'=>'ap_metro', 'class' => 'width500', 'data-placeholder' => tt('Select metro stations', 'metroStations'))
				);
				if ($callFrom != 'guestAdModule') echo "<script>$('#ap_metro').chosen();</script>";
			?>
			<?php echo CHtml::error($model,'metroStations'); ?>
		</div>
		<div class="clear"></div>
	<?php else:?>
		<div class="rowold" id="locationCity"<?php if(param('allowCustomCities') && $model->customCity){echo ' style="display: none;"';}?>>
			<?php echo CHtml::activeLabelEx($model, 'city_id'); ?>
			<?php echo CHtml::activeDropDownList($model, 'city_id', $cities, array('class' => 'span3')); ?>
			<?php echo CHtml::error($model, 'city_id'); ?>
			<?php if (param('allowCustomCities'))
				echo CHtml::link(tt('Custom city', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
		</div>
		<?php if (param('allowCustomCities')):?>
			<div class="rowold" id="customCity"<?php if(!$model->customCity){echo ' style="display: none;"';}?>>
				<?php echo $form->labelEx($model,'customCity'); ?>
				<?php echo $form->textField($model,'customCity',array('class' => 'span3')); ?>
				<?php echo $form->error($model,'customCity'); ?>
				<?php echo CHtml::link(tt('City from list', 'apartments'),'#',array('onclick'=>"switchCity(); return false;"));?>
				<?php echo CHtml::hiddenField('isCustomCity', ($model->customCity) ? 1 : 0, array('id'=>'isCustomCity')); ?>
			</div>
		<?php endif;?>
	<?php endif;?>

<?php endif; ?>

<br />

<?php
Yii::app()->clientScript->registerScript('switch-city','
	var isMetro = '.((issetModule('metroStations')) ? 1 : 0).';
	var isCustomCity = '.(($model->customCity) ? 1 : 0).'

	function switchCity() {
		if (isCustomCity){
			$("#locationCity").show();
			$("#customCity").hide();
			$("#isCustomCity").val(0);
			isCustomCity = 0;
			if(isMetro && $("#ap_metro option").length)
				$("#metro-block-apartment").show();
		} else {
			$("#locationCity").hide();
			$("#customCity").show();
			$("#isCustomCity").val(1);
			isCustomCity = 1;
			if(isMetro)
				$("#metro-block-apartment").hide();
		}

	}
', CClientScript::POS_END)
?>