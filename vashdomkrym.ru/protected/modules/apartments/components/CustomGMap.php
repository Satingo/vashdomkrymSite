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

class CustomGMap {
	private static $jsVars;
	private static $jsCode;

	public static function createMap($isAppartment = false, $scrollWheel = true, $draggable = true){
		//Yii::app()->getClientScript()->registerScriptFile('https://maps.google.com/maps/api/js??v=3.5&sensor=false&language='.Yii::app()->language.'', CClientScript::POS_END);
		// Yii::app()->getClientScript()->registerScriptFile('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', CClientScript::POS_END);

		self::$jsVars = '
		var mapGMap;
		var fenWayPanorama;
		var markersGMap = [];
		var markersForClasterGMap = [];
		var infoWindowsGMap = [];
		var latLngList = [];
		var markerClusterGMap;
		';

		self::$jsCode = '
		var initScrollWheel = "'.($scrollWheel).'";
		var initDraggable = "'.($draggable).'";
		var centerMapGMap = new google.maps.LatLng('.param('module_apartments_gmapsCenterY', 55.75411314653655).', '.param('module_apartments_gmapsCenterX', 37.620717508911184).');
			
		mapGMap = new google.maps.Map(document.getElementById("googleMap"), {
			zoom: '. param('module_apartments_gmapsZoomApartment', 15) .',
			center: centerMapGMap,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			maxZoom: 15,
			scrollwheel: initScrollWheel,
			draggable: initDraggable
		});
		';
	}

	public static function addMarker($model, $inMarker = null, $draggable = 'false'){
		if(is_object($model)) {
			$id = $model->id;
			$lat = $model->lat;
			$lng = $model->lng;
			$title = $model->getStrByLang('title');
			$iconFile = $model->getMapIconUrl();
		}
		elseif(is_array($model)) {
			$id = $model['id'];
			$lat = $model['lat'];
			$lng = $model['lng'];
			$title = $model['title_'.Yii::app()->language];
			$iconFile = ($model['objTypeIconFile']) ? Yii::app()->getBaseUrl().'/'.ApartmentObjType::model()->iconsMapPath.'/'.$model['objTypeIconFile'] : Yii::app()->theme->baseUrl."/images/house.png";
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

			self::$jsCode .= '
				var latLng'.$id.' = new google.maps.LatLng('.$lat.', '.$lng.');
				latLngList.push(latLng'.$id.');
				markersGMap['.$id.'] = new google.maps.Marker({
					position: latLng'.$id.',
					title: "'.CJavaScript::quote($title).'",
					content: "'.CJavaScript::quote($inMarker).'",
					icon: "'.$iconFile.'",
					map: mapGMap,
					draggable: '.$draggable.'
				});
				markersForClasterGMap.push(markersGMap['.$id.']);
				infoWindowsGMap['.$id.'] = new google.maps.InfoWindow({
					content: "'.CJavaScript::quote($inMarker).'"
				});
				google.maps.event.addListener(markersGMap['.$id.'], "click", function() {
					if (infoWindowsGMap && infoWindowsGMap.length) {
						for (i=0; i < infoWindowsGMap.length; i++) {
							if (typeof infoWindowsGMap[i] != "undefined" && infoWindowsGMap[i]) {
								infoWindowsGMap[i].close();
							}
						}
					}
					infoWindowsGMap['.$id.'].open(mapGMap, markersGMap['.$id.']);
				});
			';
		}
	}

	public static function clusterMarkers(){
		self::$jsCode .= 'var mcOptions = {zoomOnClick:false, maxZoom: 15, gridSize: 50};';
		self::$jsCode .= 'markerClusterGMap = new MarkerClusterer(mapGMap, markersForClasterGMap, mcOptions);';

		self::$jsCode .= '
			google.maps.event.addListener(markerClusterGMap, "clusterclick", function (cluster, $event) {
    			var newCenter = cluster.getCenter();
				var newCenterLat = newCenter.lat();
				var newCenterLng = newCenter.lng();
				var currentZoom = mapGMap.getZoom();

				mapGMap.panTo(new google.maps.LatLng(newCenterLat,newCenterLng));

				if(currentZoom < 15) {
					mapGMap.setZoom(currentZoom+1);
				}
				else {
					var markers = cluster.getMarkers();
					if (markers.length != 0) {
						var content = "<div class=\'gmap-marker-clusterer-infowindow\'>";

						$.each(markers, function(x, marker) {
							content = content + "<br />" + marker.content;
						});
						content = content + "</div>";

						var info = new google.maps.MVCObject;
    					info.set("position", cluster.center_);

						var infowindow = new google.maps.InfoWindow();
						infowindow.close();
						//infowindow.setPosition(newCenter);
						infowindow.setContent(content);
						infowindow.open(mapGMap, info);
						}
					}
				});
		';

		//self::$jsCode .= 'markerClusterGMap = new MarkerClusterer(mapGMap, markersForClasterGMap);';
	}

	public static function setCenter(){
		self::$jsCode .= '
			if(latLngList.length > 0){
				var bounds = new google.maps.LatLngBounds ();
				for (var i = 0, LtLgLen = latLngList.length; i < LtLgLen; i++) {
					bounds.extend (latLngList[i]);
				}
				mapGMap.fitBounds(bounds);
			}
		';
	}

	public static function render(){
		echo CHtml::tag('div', array('id' => 'googleMap'), '', true);

		$js1 = 'https://maps.google.com/maps/api/js?v=3.5&sensor=false&callback=initGmap&language='.Yii::app()->language;
		//$js2 = 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js';
		//$js2 = 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/src/markerclusterer_packed.js';

		//self::$jsVars .= "\n loadScript('$js1', true);\n loadScript('$js2', true);\n";
		self::$jsVars .= "\n loadScript('$js1', true);\n";

		//echo CHtml::script(self::$jsVars);
		echo CHtml::script(PHP_EOL . self::$jsVars . PHP_EOL . 'function initGmap() { ' . self::$jsCode . ' }');
	}


	public static function actionGmap($id, $model, $inMarker, $withPanorama = false){

		$isOwner = self::isOwner($model);

		// If we have already created marker - show it
		if ($model->lat && $model->lng) {
			self::createMap(true);
			self::$jsCode .= '
				mapGMap.setCenter(new google.maps.LatLng('.$model->lat.', '.$model->lng.'));
			';

			$draggable = $isOwner ? 'true' : 'false';

			self::addMarker($model, $inMarker, $draggable);

			if($isOwner){
				self::$jsCode .= '
					google.maps.event.addListener(markersGMap['.$model->id.'], "dragend", function (event) { $.ajax({
						type: "POST",
						url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
						data: ({"lat": event.latLng.lat(), "lng": event.latLng.lng()}),
						cache:false
					}); });
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
				$model->lat = param('module_apartments_gmapsCenterY', 37.620717508911184);
				$model->lng = param('module_apartments_gmapsCenterX', 55.75411314653655);
			}

			self::actionGmap($id, $model, $inMarker);
			return false;
		}

		if($withPanorama){
			self::$jsCode .= '
					var fenWayPanorama = new google.maps.LatLng('.$model->lat.', '.$model->lng.');
					if (($("#gmap-panorama").length > 0)) {
						var streetViewService = new google.maps.StreetViewService();
						streetViewService.getPanoramaByLocation(fenWayPanorama, 30, function (streetViewPanoramaData, status) {
							if (status === google.maps.StreetViewStatus.OK) {
								$("#gmap-panorama").show().css("visibility", "visible");
								google.maps.event.addDomListener(window, "load", initializeGmapPanorama);
							} else {
								$("#gmap-panorama").hide().css("visibility", "hidden");
							}
						});
					}
			';
		}

		self::render();
	}

	private static function isOwner($model){
		return Yii::app()->user->checkAccess('backend_access') || param('useUserads', 1) && !Yii::app()->user->isGuest && $model->isOwner();
	}
}