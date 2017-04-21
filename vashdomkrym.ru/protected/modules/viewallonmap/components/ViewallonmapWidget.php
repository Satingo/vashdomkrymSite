<?php
/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.16.1
 * 	copyright			:	(c) 2015 Monoray
 * 							http://monoray.net
 *							http://monoray.ru
 *
 * 	website				:	http://open-real-estate.info/en
 *
 * 	contact us			:	http://open-real-estate.info/en/contact-us
 *
 * 	license:			:	http://open-real-estate.info/en/license
 * 							http://open-real-estate.info/ru/license
 *
 * This file is part of Open Real Estate
 *
 * ********************************************************************************************* */

class ViewallonmapWidget extends CWidget {
	public $usePagination = 1;
	public $selectedIds = array();
	public $count = null;
	public $filterOn = true;
	public $withCluster = true;
	public $filterPriceType;
	public $filterObjType;
	public $scrollWheel = true;
	public $draggable = true;

	public function run() {

		Yii::app()->getModule('apartments');

		if($this->filterOn){
			Yii::app()->controller->aData['searchOnMap'] = true;
			$this->renderFilter();
		}

		if(param('useYandexMap', 1)) {
			echo $this->render('application.modules.apartments.views.backend._ymap', '', true);
			CustomYMap::init()->createMap($this->scrollWheel, $this->draggable);
		}
		elseif (param('useGoogleMap', 1)) {
			//Yii::app()->getClientScript()->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', CClientScript::POS_END);
			Yii::app()->getClientScript()->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js', CClientScript::POS_HEAD);
			CustomGMap::createMap(false, $this->scrollWheel, $this->draggable);
		}
		else {
			echo '<div id="osmap"></div>';
			CustomOSMap::createMap(false, $this->scrollWheel, $this->draggable);
		}

		$lang = Yii::app()->language;
		$select = $ownerActiveCond = '';
		$joinTables = '
			LEFT JOIN {{apartment_obj_type}} objType ON (a.obj_type_id = objType.id)
		';
		if (param('useUserads'))
			$ownerActiveCond .= ' AND owner_active = '.Apartment::STATUS_ACTIVE.' ';

		$whereCondition = ' lat <> "" AND lat <> "0" AND active='.Apartment::STATUS_ACTIVE.' AND (owner_id=1 OR owner_id>1 '.$ownerActiveCond.') ';

		if (issetModule('seo')) {
			$select .= ' ,seo.url_'.$lang.' as seoUrl ';
			$joinTables .= ' LEFT JOIN {{seo_friendly_url}} seo ON (seo.model_id = a.id) AND (seo.model_name = "Apartment")';
		}

		$this->filterPriceType = (int) Yii::app()->request->getParam('filterPriceType');
		if ($this->filterPriceType) {
			$whereCondition .= ' AND a.price_type = '.$this->filterPriceType;
		}
		$this->filterObjType = (int) Yii::app()->request->getParam('filterObjType');
		if ($this->filterObjType) {
			$whereCondition .= ' AND a.obj_type_id = '.$this->filterObjType;
		}

		if (!empty($this->selectedIds)) {
			$whereCondition .= ' AND a.id IN ('.  implode(', ', $this->selectedIds).') ';
		}

		$types = HApartment::availableApTypesIds();
		if($types)
			$whereCondition .= ' AND a.type IN ('.  implode(', ', $types).') ';

		$sqlApartments = '
			SELECT
				a.id, a.type, a.address_'.$lang.', a.title_'.$lang.', a.owner_id, a.lat, a.lng,
				objType.name_'.$lang.' as objTypeName, objType.icon_file as objTypeIconFile
				'.$select.'
			FROM {{apartment}} a
			'.$joinTables.'
			WHERE
			'.$whereCondition.'
			LIMIT '.mt_rand(15578, 15778).'
		';
		$apartments = Yii::app()->db->createCommand($sqlApartments)->queryAll();

		if ($apartments && is_array($apartments)) {
			foreach($apartments as $r) {
				$allIds[] = $r['id'];
			}

			if (isset($allIds) && is_array($allIds)) {
				$sqlImages = 'SELECT id, id_object, id_owner, file_name, file_name_modified, comment, is_main, sorter FROM {{images}} WHERE id_object IN('.implode(', ', $allIds).') AND is_main = 1';
				$resImages = Yii::app()->db->createCommand($sqlImages)->queryAll();

				$apartments = array_combine($allIds, $apartments);
				unset($allIds);

				if($resImages && is_array($resImages)) {
					foreach($resImages as $rImage) {
						if (isset($apartments[$rImage['id_object']])) {
							$apartments[$rImage['id_object']]['images'][] = $rImage;
						}
					}

					unset($resImages);
				}
			}
		}

		if (!count($apartments))
			return false;

		if(param('useYandexMap', 1)) {
			$lats = array();
			$lngs = array();
			foreach($apartments as $apartment){
				$lats[]= $apartment['lat'];
				$lngs[]= $apartment['lng'];
				CustomYMap::init()->addMarker(
					$apartment['lat'], $apartment['lng'],
					null, /*$this->render('application.modules.apartments.views.backend._marker', array('model' => $apartment), true),*/
					true, $apartment
				);
			}

			if($lats && $lngs){
				CustomYMap::init()->setBounds(min($lats),max($lats),min($lngs),max($lngs));
				if($this->withCluster){
					CustomYMap::init()->setClusterer();
				}else{
					CustomYMap::init()->withoutClusterer();
				}
			}
			else {
				$minLat = param('module_apartments_ymapsCenterX') - param('module_apartments_ymapsSpanX')/2;
				$maxLat = param('module_apartments_ymapsCenterX') + param('module_apartments_ymapsSpanX')/2;

				$minLng = param('module_apartments_ymapsCenterY') - param('module_apartments_ymapsSpanY')/2;
				$maxLng = param('module_apartments_ymapsCenterY') + param('module_apartments_ymapsSpanY')/2;

				CustomYMap::init()->setBounds($minLng,$maxLng,$minLat,$maxLat);
			}
			CustomYMap::init()->changeZoom(0, '+');
			CustomYMap::init()->processScripts(true);

		}
		elseif (param('useGoogleMap', 1)) {
			foreach($apartments as $apartment){
				CustomGMap::addMarker($apartment,
					null /*$this->render('application.modules.apartments.views.backend._marker', array('model' => $apartment), true)*/
				);
			}
			if($this->withCluster){
				CustomGMap::clusterMarkers();
			}
			CustomGMap::setCenter();
			CustomGMap::render();
		}
		elseif (param('useOSMMap', 1)) {
			foreach($apartments as $apartment){
				CustomOSMap::init()->addMarker($apartment,
					null /*$this->render('application.modules.apartments.views.backend._marker', array('model' => $apartment), true)*/
				);
			}
			if($this->withCluster){
				CustomOSMap::clusterMarkers();
			}
			CustomOSMap::setCenter();
			CustomOSMap::render();
		}
	}

	public function renderFilter(){
		// start set filter
		$this->filterPriceType = Yii::app()->request->getParam('filterPriceType');
		$this->filterObjType = Yii::app()->request->getParam('filterObjType');
		// end set filter

		// echo filter form
		$data = SearchForm::apTypes();

		echo '<div class="block-filter-viewallonmap">';
		echo '<form method="GET" action="" id="form-filter-viewallonmap">';

		echo CHtml::dropDownList('filterPriceType',
			isset($this->filterPriceType) ? CHtml::encode($this->filterPriceType) : '',
			$data['propertyType']
		);

		echo CHtml::dropDownList('filterObjType',
			isset($this->filterObjType) ? CHtml::encode($this->filterObjType) : 0,
			CMap::mergeArray(array(0 => Yii::t('common', 'Please select')),
				Apartment::getObjTypesArray()
			)
		);

		echo CHtml::button(tc('Filter'), array('onclick' => '$("#form-filter-viewallonmap").submit();',
			'id' => 'click-filter-viewallonmap',
			'class' => 'inline button-blue',
		));
		echo '</form>';
		echo '</div>';
	}
}