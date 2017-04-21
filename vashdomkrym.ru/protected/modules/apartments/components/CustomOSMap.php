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

class CustomOSMap {

	private static $_instance;
	private static $jsVars;
	private static $jsCode;
	protected static $icon = array();

	public static function init(){
		self::$icon['href'] = Yii::app()->theme->baseUrl."/images/house.png";
		self::$icon['size'] = array('x' => 32, 'y' => 37);
		self::$icon['offset'] = array('x' => -16, 'y' => -35);

		if (!isset(self::$_instance)) {
			$className = __CLASS__;
			self::$_instance = new $className;
		}
		return self::$_instance;
	}

	public static function createMap($isAppartment = false, $scrollWheel = true, $draggable = true){
		$baseUrl = Yii::app()->request->baseUrl;
		//Yii::app()->clientScript->registerScriptFile('http://cdn.leafletjs.com/leaflet-0.7/leaflet.js', CClientScript::POS_END);
		//Yii::app()->clientScript->registerCssFile('http://cdn.leafletjs.com/leaflet-0.7/leaflet.css');

		Yii::app()->clientScript->registerScriptFile($baseUrl . '/common/js/leaflet/leaflet-0.7.2/leaflet.js', CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerCssFile($baseUrl . '/common/js/leaflet/leaflet-0.7.2/leaflet.css');

		Yii::app()->clientScript->registerScriptFile($baseUrl . '/common/js/leaflet/leaflet-0.7.2/dist/leaflet.markercluster-src.js', CClientScript::POS_HEAD);
		Yii::app()->clientScript->registerCssFile($baseUrl . '/common/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.css');
		Yii::app()->clientScript->registerCssFile($baseUrl . '/common/js/leaflet/leaflet-0.7.2/dist/MarkerCluster.Default.css');

		self::$jsVars = '
		var mapOSMap;
		var markerClusterOSMap;
		var markersOSMap = [];
		var markersForClasterOSMap = [];
		var latLngList = [];
		';

		self::$jsCode = '
		var initScrollWheel = "'.($scrollWheel).'";
		var initDraggable = "'.($draggable).'";
		var zoomOSMap = '.param('module_apartments_osmapsZoomApartment', 15).';
		mapOSMap = L.map("osmap", {scrollWheelZoom: initScrollWheel, dragging: initDraggable}).setView(['.param('module_apartments_osmapsCenterY', 55.75411314653655).', '.param('module_apartments_osmapsCenterX', 37.620717508911184).'], zoomOSMap);
		L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
		attribution: "&copy; <a href=\'http://osm.org/copyright\'>OpenStreetMap</a> contributors"
		}).addTo(mapOSMap);
		';
	}

	public static function addMarker($model, $inMarker, $draggable = 'false'){
		if(is_object($model)) {
			$id = $model->id;
			$lat = $model->lat;
			$lng = $model->lng;
		}
		elseif(is_array($model)) {
			$id = $model['id'];
			$lat = $model['lat'];
			$lng = $model['lng'];
		}
		else return false;

		if($lat && $lng) {
			if (!$inMarker) {
				if(is_object($model)) {
					$id = $model->id;
					$title = $model->getStrByLang('title');
					$address = $model->getStrByLang('address');
					$url = $model->getUrl();
					$images = $model->images;
				}
				elseif(is_array($model)) {
					$id = $model['id'];
					$title = $model['title_'.Yii::app()->language];
					$address = $model['address_'.Yii::app()->language];
					$url = (isset($model['seoUrl']) && $model['seoUrl']) ? Yii::app()->createAbsoluteUrl('/apartments/main/view', array('url' => $model['seoUrl'] . (param('urlExtension') ? '.html' : ''))) : Yii::app()->createAbsoluteUrl('/apartments/main/view', array('id' => $id));
					$images = (isset($model['images'])) ? $model['images'] : null;
				}
				$res = Images::getMainThumb(150, 100, $images);
				$inMarker = '<div class="gmap-marker"><div align="center" class="gmap-marker-adlink">';
				$inMarker .= CHtml::link('<strong>'.tt("ID", "apartments").': '.$id.'</strong>, '.CHtml::encode($title), $url);
				$inMarker .= '</div><div align="center" class="gmap-marker-img">';
				$inMarker .= CHtml::image($res['thumbUrl'], $title, array('title' => $title)).'</div>';
				$inMarker .= '<div align="center" class="gmap-marker-adress">';
				$inMarker .= CHtml::encode($address).'</div></div>';
			}

			self::setIconType($model);

			self::$jsCode .= '
				var markerIcon = L.icon({
					iconUrl: "'.self::$icon['href'].'",
					iconSize: ['.self::$icon['size']['x'].', '.self::$icon['size']['y'].'],
					className : "marker-icon-class"
				});
				markersOSMap['.$id.'] = L.marker(['.$lat.', '.$lng.'], {icon: markerIcon, draggable : '.$draggable.'})
					.addTo(mapOSMap)
					.bindPopup("'.CJavaScript::quote($inMarker).'");

				latLngList.push(['.$lat.', '.$lng.']);
				markersForClasterOSMap.push(markersOSMap['.$id.']);
			';
		}
	}

	public static function clusterMarkers(){
		self::$jsCode .= '
			if(markersForClasterOSMap.length > 0){
				var markersCluster = L.markerClusterGroup({spiderfyOnMaxZoom: true, showCoverageOnHover: true, zoomToBoundsOnClick: true, removeOutsideVisibleBounds: true, maxClusterRadius: 30});
				for (var i = 0, markerCluster = markersForClasterOSMap.length; i < markerCluster; i++) {
					markersCluster.addLayer(markersForClasterOSMap[i]);
				}
				mapOSMap.addLayer(markersCluster);
				/*markersCluster.on("clusterclick", function (a) {
					a.layer.zoomToBounds();
				});*/
				mapOSMap.fitBounds(latLngList, {reset: true});
				//mapOSMap.fitBounds(new L.LatLngBounds(latLngList), {padding: [50, 50]});
			}
		';
	}

	public static function setCenter(){
		self::$jsCode .= '
			if(latLngList.length > 0){
				if (latLngList.length == 1) {
					mapOSMap.setView(latLngList[0]);
				}
				else {
					mapOSMap.fitBounds(latLngList,{reset: true});
					//mapOSMap.fitBounds(new L.LatLngBounds(latLngList), {reset: true});
				}
			}
		';
	}

	public static function render(){
		//echo CHtml::tag('div', array('id' => 'OSMMap', 'style' => 'width: 100%; height: 586px;'), '', true);
		echo CHtml::script(self::$jsVars);
		echo CHtml::script(PHP_EOL . '$(function(){' . self::$jsCode . '});');
	}


	public static function actionOSmap($id, $model, $inMarker){
		self::init();

		$isOwner = self::isOwner($model);

		// If we have already created marker - show it

		if ($model->lat && $model->lng) {
			self::createMap(true);
			self::$jsCode .= '

			';

			$draggable = $isOwner ? 'true' : 'false';

			self::addMarker($model, $inMarker, $draggable);

			if($isOwner){
				self::$jsCode .= '
					markersOSMap['.$model->id.'].on("dragend", function(event) {
						var marker = event.target;
						var result = marker.getLatLng();

						if (result) {
							$.ajax({
								type:"POST",
								url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
								data:({lat: result.lat, lng: result.lng}),
								cache:false
							});
						}
					});
				';
			}

		} else {
			if(!$isOwner){
				return '';
			}

			$coordinates = NULL;

			if ($coordinates) {
				$model->lat = $coordinates->lat;
				$model->lng = $coordinates->lng;
			} else {
				$model->lat = param('module_apartments_osmapsCenterY', 55.75411314653655);
				$model->lng = param('module_apartments_osmapsCenterX', 37.620717508911184);
			}

			self::actionOSmap($id, $model, $inMarker);
			return false;
		}

		self::setCenter();
		self::render();
	}

	private static function isOwner($model){
		return Yii::app()->user->checkAccess('backend_access') || param('useUserads', 1) && !Yii::app()->user->isGuest && $model->isOwner();
	}

	public static function setIconType($model = null) {
		// каждому типу свой значок
		if ($model) {
			if(is_object($model)) {
				if (isset($model->objType->icon_file) && $model->objType->icon_file) {
					self::$icon['href'] = Yii::app()->getBaseUrl().'/'.$model->objType->iconsMapPath.'/'.$model->objType->icon_file;
					self::$icon['size'] = array('x' => ApartmentObjType::MAP_ICON_MAX_WIDTH, 'y' => ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					/*$icon['offset'] = array('x' => -16, 'y' => -2);*/
					self::$icon['offset'] = array('x' => -16, 'y' => -35);
				}
			}
			elseif (is_array($model)) {
				if ($model['objTypeIconFile']) {
					self::$icon['href'] = Yii::app()->getBaseUrl().'/'.ApartmentObjType::model()->iconsMapPath.'/'.$model['objTypeIconFile'];
					self::$icon['size'] = array('x' => ApartmentObjType::MAP_ICON_MAX_WIDTH, 'y' => ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					/*$icon['offset'] = array('x' => -16, 'y' => -2);*/
					self::$icon['offset'] = array('x' => -16, 'y' => -35);
				}
			}
		}
	}
}