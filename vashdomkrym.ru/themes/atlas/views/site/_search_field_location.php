<?php
	$cityArray = (issetModule('location')) ?  (City::getCitiesArray((isset($this->selectedRegion) ? $this->selectedRegion : 0), 0)) : $this->cityActive;
	$cityArray = CArray::merge(array(0 => tc('select city')), $cityArray);
	$metros = array();
	# для того, чтобы не было пустого select если в выбранном регионе нет объявлений
	$onlyWithAds = (issetModule('geo') && (param('geo_in_search') > 0)) ? 0 : 1;
?>

<?php if(issetModule('location')) : ?>
	<div class="<?php echo $divClass; ?>">
        <?php if($this->searchShowLabel){ ?>
		<div class="<?php echo $textClass; ?>"><?php echo tc('Country') ?>:</div>
        <?php } ?>
		<div class="<?php echo $controlClass; ?>">
			<?php
			echo CHtml::dropDownList(
				'country',
				isset($this->selectedCountry)?$this->selectedCountry:'',
				Country::getCountriesArray(2, 0, true),
				array(
					'class' => $fieldClass . ' searchField', 
					'id' => 'country',
					'ajax' => array(
						'type'=>'GET', //request type
						'url'=>$this->createUrl('/location/main/getRegions'), //url to call.
						'data'=>'js:"country="+$("#country").val()+"&type=2&onlyWithAds='.$onlyWithAds.'"',
						'success'=>'function(result){
							$("#region").html(result);
							$("#region").change();
							$("#region").removeClass("less-opacity-control");
						}'
					)
				)
			);
			?>
		</div>
	</div>

	<div class="<?php echo $divClass; ?>">
        <?php if($this->searchShowLabel){ ?>
		<div class="<?php echo $textClass; ?>"><?php echo tc('Region') ?>:</div>
        <?php } ?>
		<div class="<?php echo $controlClass; ?>">
			<?php $opacityClass = ((!isset($this->selectedRegion) || empty($this->selectedRegion)) && (!isset($this->selectedCountry) || empty($this->selectedCountry)))?' less-opacity-control':'';?>
			<?php
			echo CHtml::dropDownList(
				'region',
				isset($this->selectedRegion)?$this->selectedRegion:'',
				Region::getRegionsArray((isset($this->selectedCountry) ? $this->selectedCountry : 0), 2),
				array(
					'class' => $fieldClass . ' searchField'. $opacityClass, 
					'id' => 'region',
					'onchange'=>'$("#region").removeClass("less-opacity-control");',
					'ajax' => array(
						'type'=>'GET', //request type
						'url'=>$this->createUrl('/location/main/getCities'), //url to call.
						'data'=>'js:"region="+$("#region").val()+"&type=2&onlyWithAds='.$onlyWithAds.'"',
						'success'=>'function(result){
                            changeSearch();
							$("#city").removeClass("less-opacity-control");
							$("#city").html(result);'.((issetModule('metroStations')) ? '$("#city").change()' : '').
						'}'
					)
				)
			);
		?>
		</div>
	</div>
<?php endif; ?>

<?php
if (issetModule('metroStations')) {
	$metros = MetroStations::getMetrosArray(isset($this->selectedCity) ? $this->selectedCity : '', 0, 0, 1);
?>
	<div class="<?php echo $divClass; ?>">
		<?php if($this->searchShowLabel){ ?>
			<div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'City') ?>:</div>
		<?php } ?>
		<div class="<?php echo $controlClass; ?>">
			<?php $opacityClass = (!isset($this->selectedCity) || empty($this->selectedCity))?' less-opacity-control':'';?>
			<?php
			echo CHtml::dropDownList(
				'city[]',
				isset($this->selectedCity)?$this->selectedCity:'',
				$cityArray,
				array(
					'class' => $fieldClass.' width289 searchField'.$opacityClass,
					'onchange'=>'$("#city").removeClass("less-opacity-control");',
					'ajax' => array(
						'type'=>'GET', 
						'url'=>$this->createUrl('/metroStations/main/getMetroStations'),
						'data'=>'js:"city="+$("#city").val()+"&type=0&onlyWithAds='.$onlyWithAds.'"',
						'dataType'=>'json', 
						'success'=>'function(result){
							if (result.dropdownMetro) { 
								//$("#metro-block").show(); 
								$("#metro").removeAttr("disabled");
								$("#metro").html(result.dropdownMetro);
								$("#metro")[0].sumo.reload();
								$("#metro").parent().parent().removeClass("less-opacity-control");
							} 
							else { 
								//$("#metro-block").hide(); 
								$("#metro").attr("disabled", "disabled");
								$("#metro").html("");
								$("#metro")[0].sumo.reload();
							}
						}'
					),
				)
			);
			?>
		</div>
	</div>
	<div class="<?php echo $divClass; ?>" id="metro-block" style="display: block; <?php /* echo ($metros && count($metros) > 1) ? 'block;' : 'none;'; */?>">
		<?php if($this->searchShowLabel){ ?>
			<div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'Subway stations') ?>:</div>
		<?php } ?>
		<?php $opacityClass = ((!isset($this->selectedMetroStations) || empty($this->selectedMetroStations)) && empty($metros))?' less-opacity-control':'';?>
		<div class="<?php echo $controlClass; ?> <?php echo $opacityClass;?>">
			<?php
				echo CHtml::dropDownList('metro[]', isset($this->selectedMetroStations)?$this->selectedMetroStations:'', $metros,
					array('id'=>'metro', 'class' => $fieldClass.' width289 searchField', 'multiple' => 'multiple')
				);
				echo "<script>$('#metro').SumoSelect({captionFormat: '".tc('{0} Selected')."', selectAlltext: '".tc('check all')."', csvDispCount:1, placeholder: '".tt('Select metro stations', 'metroStations')."', filter: true, filterText: '".tc('enter initial letters')."'});</script>";
			?>
		</div>
	</div>
<?php } else { ?>
	<div class="<?php echo $divClass; ?>">
		<?php if($this->searchShowLabel){ ?>
			<div class="<?php echo $textClass; ?>"><?php echo Yii::t('common', 'City') ?>:</div>
		<?php } ?>
		<div class="<?php echo $controlClass; ?>">
			<?php
			echo CHtml::dropDownList(
				'city[]',
				isset($this->selectedCity)?$this->selectedCity:'',
				$cityArray,
				array(
					'class' => $fieldClass.' width289 searchField',
				)
			);
			?>
		</div>
	</div>
<?php } ?>