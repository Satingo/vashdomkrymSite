<?php
/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.17.1
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

class MainController extends ModuleUserController{
	public $modelName = 'YandexRealty';
	public $defaultAction = 'viewfeed';
	public $generationDate;
	public $country;
	public $region;
	public $currency;
	public static $squareUnit = 'кв.м';

	# http://help.yandex.ru/webmaster/?id=1113400
	# Сейчас принимаются объявления только о продаже и аренде жилой недвижимости: квартир, комнат, домов и участков.

	# http://help.yandex.ru/realty/actual.xml
	# актуальные объявления
	# для продажи квартир на вторичке — созданные не более 90 дней назад, либо обновлённые не более 45 дней назад;
	# для длительной аренды квартир — созданные не более 7 дней назад, либо обновлённые не более 14 дней назад;
	# для продажи комнат — созданные не более 120 дней назад, либо обновлённые не более 45 дней назад;
	# для длительной аренды комнат — созданные не более 25 дней назад, либо обновлённые не более 24 дней назад;
	# для длительной аренды домов — созданные не более 30 дней назад, либо обновлённые не более 30 дней назад;

	# проверка файла - http://webmaster.yandex.ru/xsdtest.xml
	# общие условия размещения объявлений - http://help.yandex.ru/webmaster/?id=1113378

	public static $typeApartment = 1; // id типа "квартира" из таблицы {{apartment_obj_type}}
	public static $typeHouse = 2; // id типа "дом" из таблицы {{apartment_obj_type}}
	public static $typeRoom = 0; // если нет такого типа, то оставить 0
	public static $typeLand = 4; // если нет такого типа, то оставить 0

	public function init() {
		# php.ini - date.timezone
		$this->generationDate = date('c', time());

		# если нет модуля "Страна->регион->город" задаём строго
		$this->country = 'Россия';
		$this->region = 'Республика Крым';

		# валюта
		$this->currency = 'RUR'; # param('siteCurrency', 'RUR');

		if (!isFree()) {
			$activeCurrencyId = Currency::getDefaultValuteId();
			$activeCurrency = Currency::model()->findByPk($activeCurrencyId);
			$this->currency = ($activeCurrency && isset($activeCurrency->char_code)) ? $activeCurrency->char_code : $this->currency;
		}
	}

	public function actionViewFeed() {
		$oldLang = Yii::app()->language;

		Controller::disableProfiler();

		$defaultLangs = Lang::getDefaultLang();
		Yii::app()->language = $defaultLangs;

		// если есть русский или украинский языки, но они не дефолтные. установим на время их.
		if ($defaultLangs != 'ru' || $defaultLangs != 'uk') {
			$allLangs = Lang::getActiveLangs();

			if (array_key_exists('ru', $allLangs))
				Yii::app()->language = 'ru';
			elseif (array_key_exists('uk', $allLangs))
				Yii::app()->language = 'uk';
		}

		$items = $this->generateFeed();

		if (is_array($items) && count($items) > 0) {
			header('Content-type: text/xml');
			header('Pragma: public');
			header('Cache-control: private');
			header('Expires: -1');
			
			$lang = Yii::app()->language;
			$resYandexRealtyXml = Yii::app()->cache->get("activeYandexRealtyXml{$lang}");
			if($resYandexRealtyXml !== false){
				echo $resYandexRealtyXml;
				Yii::app()->end();
			}

			
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><realty-feed/>');
			$xml->addAttribute('xmlns', 'http://webmaster.yandex.ru/schemas/feed/realty/2010-06');
			$xml->addChild('generation-date', $this->generationDate);
			
			foreach ($items as $item){
				if (isset($item['id'])) {
					$this->prepareItem($item, $xml);
				}
			}

			$res = $xml->asXML();
			
			if (param('cachingTime'))
				Yii::app()->cache->set("activeYandexRealtyXml{$lang}", $res, 60*60*param('cachingTime'));
			
			echo $res;
			Yii::app()->end();
		}
		else {
			echo 'no elements';
		}

		// установим обратно пользовательский язык
		Yii::app()->language = $oldLang;
	}

	private function generateFeed() {
		$activeAds = array();	
		$adWithoutDateCriteria = YandexRealty::getAdsWithoutDateCriteria();
		$allObjects = $adWithoutDateCriteria;  
				
		if (!is_array($adWithoutDateCriteria) || empty($adWithoutDateCriteria)) {
			echo 'Нет объявлений для импорта.';
			Yii::app()->end();
		}
		
		########################################################################
		# проверка на актуальность даты создания / обновления
		# 
		# 
		# http://help.yandex.ru/realty/actual.xml
		# для продажи квартир на вторичке — созданные не более 90 дней назад, либо обновлённые не более 45 дней назад;
		# для длительной аренды квартир — созданные не более 7 дней назад, либо обновлённые не более 14 дней назад;
		# для продажи комнат — созданные не более 120 дней назад, либо обновлённые не более 45 дней назад;
		# для длительной аренды комнат — созданные не более 25 дней назад, либо обновлённые не более 24 дней назад;
		# для длительной аренды домов — созданные не более 30 дней назад, либо обновлённые не более 30 дней назад;
				
		foreach($adWithoutDateCriteria as $preKey => $preAd) {
			// только день, месяц
			if (!in_array($preAd['price_type'], array(YandexRealty::PRICE_PER_DAY, YandexRealty::PRICE_PER_MONTH, YandexRealty::PRICE_SALE)))
				unset($adWithoutDateCriteria[$preKey]);
			
			# подборка по дате создания / обновления
			if ($preAd['obj_type_id'] == self::$typeApartment && $preAd['type'] == YandexRealty::TYPE_SALE 
					&& 
					(
						strtotime($preAd['date_created']) < strtotime("-89 days", time()) 
						|| strtotime($preAd['date_manual_updated']) < strtotime("-44 days", time())
					)
				) 
			{
				unset($adWithoutDateCriteria[$preKey]);
			}
			
			if ($preAd['obj_type_id'] == self::$typeApartment && $preAd['type'] == YandexRealty::TYPE_RENT && $preAd['price_type'] == YandexRealty::PRICE_PER_MONTH
					&& 
					(
						strtotime($preAd['date_created']) < strtotime("-6 days", time()) 
						|| strtotime($preAd['date_manual_updated']) < strtotime("-13 days", time())
					)
				) 
			{
				unset($adWithoutDateCriteria[$preKey]);
			}
			
			if ($preAd['obj_type_id'] == self::$typeRoom && $preAd['type'] == YandexRealty::TYPE_SALE
					&& 
					(
						strtotime($preAd['date_created']) < strtotime("-119 days", time()) 
						|| strtotime($preAd['date_manual_updated']) < strtotime("-44 days", time())
					)
				) 
			{
				unset($adWithoutDateCriteria[$preKey]);
			}
			
			if ($preAd['obj_type_id'] == self::$typeRoom && $preAd['type'] == YandexRealty::TYPE_RENT && $preAd['price_type'] == YandexRealty::PRICE_PER_MONTH
					&& 
					(
						strtotime($preAd['date_created']) < strtotime("-24 days", time()) 
						|| strtotime($preAd['date_manual_updated']) < strtotime("-23 days", time())
					)
				) 
			{
				unset($adWithoutDateCriteria[$preKey]);
			}
			
			if ($preAd['obj_type_id'] == self::$typeHouse && $preAd['type'] == YandexRealty::TYPE_RENT && $preAd['price_type'] == YandexRealty::PRICE_PER_MONTH
					&& 
					(
						strtotime($preAd['date_created']) < strtotime("-29 days", time()) 
						|| strtotime($preAd['date_manual_updated']) < strtotime("-29 days", time())
					)
				) 
			{
				unset($adWithoutDateCriteria[$preKey]);
			}
		}
		
		$activeAds = $adWithoutDateCriteria;
		unset($adWithoutDateCriteria);		
		########################################################################
		
		if (count($activeAds) < 100) {
			echo 'Актуальных объявлений меньше 100. Всего:'.count($activeAds);
			echo '<br><br>Если есть объявления от администратора - проверьте указание телефона, т.к это поле обязательное.';
			echo '<br><br><a href="http://help.yandex.ru/realty/actual.xml" target="_blank">http://help.yandex.ru/realty/actual.xml</a>';
			echo '<br><a href="http://help.yandex.ru/webmaster/?id=1113400" target="_blank">http://help.yandex.ru/webmaster/?id=1113400</a>';
			echo '<br>Не прошедшие валидацию объекты: <pre>';        
			print_r(array_diff_key($allObjects, $activeAds));
			echo '</pre>'; 
			//echo '<br><br>Если указано, что менее ста валидных объявлений, это не значит, ноль — это тоже меньше ста. Такое сообщение появляется автоматически и при отсутствии контента за последние пять дней.';
			echo '<br><br>Пытаться изменять дату создания на более новую — бессмысленно, поскольку сервис Яндекса запоминает первоначальную дату, с которой объявление попало на сервис.';
			Yii::app()->end();
		}
		
		foreach($activeAds as $ad) {
			$allIds[] = $ad['id'];
		}

		if (isset($allIds) && is_array($allIds)) {
			$sqlImages = 'SELECT id, id_object, id_owner, file_name, file_name_modified, comment, is_main, sorter FROM {{images}} WHERE id_object IN('.implode(', ', $allIds).')';
			$resImages = Yii::app()->db->createCommand($sqlImages)->queryAll();

			$sqlReferences = '
				SELECT reference.apartment_id as refApartmentId, reference_values.title_'.Yii::app()->language.' as refName, reference_values.id as refValueId
				FROM {{apartment_reference}} reference
				INNER JOIN {{apartment_reference_values}} reference_values ON reference_values.id = reference.reference_value_id
				WHERE reference.apartment_id IN ('.implode(', ', $allIds).')
			';
			$resReferences = Yii::app()->db->createCommand($sqlReferences)->queryAll();

			$activeAds = array_combine($allIds, $activeAds);
			if($resImages && is_array($resImages)) {
				foreach($resImages as $rImage) {
					if (isset($activeAds[$rImage['id_object']])) {
						$activeAds[$rImage['id_object']]['images'][] = $rImage;
					}
				}

				unset($sqlImages, $resImages);
			}

			if($resReferences && is_array($resReferences)) {
				foreach($resReferences as $rRef) {
					if (isset($activeAds[$rRef['refApartmentId']])) {
						$activeAds[$rRef['refApartmentId']]['reference'][$rRef['refValueId']] = $rRef['refName'];
					}
				}

				unset($sqlReferences, $resReferences);
			}
			
			if (issetModule('metroStations')) {
				$sqlMetros = 'SELECT ams.apartment_id as refApartmentId, ms.name_'.Yii::app()->language.' as metroName, ms.id as metroId
					FROM {{apartment_metro_stations}} ams, {{metro_stations}} ms
					WHERE ams.metro_id = ms.id AND ams.apartment_id IN ('.implode(', ', $allIds).')';
				$resMetros = Yii::app()->db->createCommand($sqlMetros)->queryAll();
								
				if($resMetros && is_array($resMetros)) {
					foreach($resMetros as $rMetro) {
						if (isset($activeAds[$rMetro['refApartmentId']])) {
							$activeAds[$rMetro['refApartmentId']]['metros'][$rMetro['metroId']] = $rMetro['metroName'];
						}
					}

					unset($sqlMetros, $resMetros);
				}				
			}

			unset($allIds);
		}

		return $activeAds;
	}

	public function prepareItem($item = array(), $xml = null) {
		if (count($item) > 0 && $xml && array_key_exists('type', $item)) {
			/* type */
			if ($item['type'] == YandexRealty::TYPE_RENT)
				$type = 'аренда';
			elseif ($item['type'] == YandexRealty::TYPE_SALE)
				$type = 'продажа';
			else
				return;

			# Не указано количество комнат в квартире
			if($item['obj_type_id'] == self::$typeApartment && !$item['num_of_rooms'])
				return;

			# только день, месяц для аренды
			if ($item['type'] == YandexRealty::TYPE_RENT) {
				if ($item['price_type'] == YandexRealty::PRICE_PER_HOUR || $item['price_type'] == YandexRealty::PRICE_PER_WEEK)
					return;
			}

			$elem = $xml->addChild('offer');
			$elem->addAttribute('internal-id', $item['id']);
				$elem->addChild('type', $type);
			
				/* property-type */
				if ($item['obj_type_id'] != self::$typeLand) {
					$elem->addChild('property-type', 'жилая');
				}

				/* category */
				$elem->addChild('category', $item['obj_type_name']);

				/* url */
				$url = (isset($item['seoUrl']) && $item['seoUrl']) ? Yii::app()->createAbsoluteUrl('/apartments/main/view', array('url' => $item['seoUrl'] . (param('urlExtension') ? '.html' : ''))) : Yii::app()->createAbsoluteUrl('/apartments/main/view', array('id' => $item['id']));
				$elem->addChild('url', $url);

				/* creation-date */
				$creationDate = date('c', strtotime($item['date_created']));
				$elem->addChild('creation-date', $creationDate);

				/* last-update-date */
				if ($item['date_manual_updated'] != '0000-00-00 00:00:00') {
					$updateDate = date('c', strtotime($item['date_manual_updated']));
					$elem->addChild('last-update-date', $updateDate);
				}

				/* manually-added */
				$elem->addChild('manually-added', 1);

				/* location */
				$location = $elem->addChild('location');
				if (issetModule('location')) {
					if ($item['loc_country_name'])
						$location->addChild('country', $item['loc_country_name']);
					if ($item['loc_region_name'])
						$location->addChild('region', $item['loc_region_name']);
					if ($item['loc_city_name'])
						$location->addChild('locality-name', $item['loc_city_name']);
				}
				else {
					$location->addChild('country', $this->country);
					$location->addChild('region', $this->region);
					if ($item['city_name'])
						$location->addChild('locality-name', $item['city_name']);
				}

				if ($item['address_'.Yii::app()->language])
					$location->addChild('address', $item['address_'.Yii::app()->language]);

				if ($item['lat'] && $item['lng']) {
					$location->addChild('latitude', $item['lat']);
					$location->addChild('longitude', $item['lng']);
				}

				if (isset($item['metros']) && count($item['metros'])) {
					foreach($item['metros'] as $metroName) {
						$metro = $location->addChild('metro');
						$metro->addChild('name', $metroName);
					}
				}

				/* sales info */
				$salesAgent = $elem->addChild('sales-agent');
				if ($item['owner_username'])
					$salesAgent->addChild('name', $item['owner_username']);
				if ($item['owner_phone'])
					$salesAgent->addChild('phone', $item['owner_phone']);
				if ($item['owner_email'])
					$salesAgent->addChild('email', $item['owner_email']);
				$salesAgent->addChild('agency-id', $item['owner_id']);

				/* price */
				$price = $elem->addChild('price');
				$price->addChild('value', $item['price']);
				$price->addChild('currency', $this->currency);
				if ($item['type'] == YandexRealty::TYPE_RENT) {
					// только день, месяц
					if ($item['price_type'] == YandexRealty::PRICE_PER_DAY)
						$price->addChild('period', 'день');
					if ($item['price_type'] == YandexRealty::PRICE_PER_MONTH)
						$price->addChild('period', 'месяц');
				}

				/* images */
				if (isset($item['images']) && is_array($item['images'])) {
					foreach ($item['images'] as $value) {
						if ($value['file_name_modified']) {
							$imageUrl = Yii::app()->getBaseUrl(true).'/uploads/objects/'.$item['id'].'/modified/full_'.$value['file_name_modified'];
							$elem->addChild('image', $imageUrl);
						}
					}
				}

				/* description */
				if($item['description_'.Yii::app()->language]) {
					$elem->addChild('description', strip_tags($item['description_'.Yii::app()->language]));
				}

				/* area */
				if($item['square'] || $item['land_square']) {
					// если участок
					if ($item['obj_type_id'] == self::$typeLand) {
						if ($item['square']) {
							$lotArea = $elem->addChild('lot-area');
							$lotArea->addChild('value', $item['square']);
							$lotArea->addChild('unit', self::$squareUnit);
						}
					}
					else { // комната, квартира, дом
						if ($item['square']) {
							$area = $elem->addChild('area');
							$area->addChild('value', $item['square']);
							$area->addChild('unit', self::$squareUnit);
						}

						if ($item['land_square']) {
							$lotArea = $elem->addChild('lot-area');
							$lotArea->addChild('value', $item['land_square']);
							$lotArea->addChild('unit', self::$squareUnit);
						}
					}
				}

				/* кол-во комнат */
				if ($item['num_of_rooms'])
					$elem->addChild('rooms', $item['num_of_rooms']);

				/* кол-во комнат в сделке  */
				if ($item['type'] == YandexRealty::TYPE_RENT || $item['type'] == YandexRealty::TYPE_SALE) {
					if ($item['obj_type_id'] == self::$typeRoom) {
						if ($item['num_of_rooms'])
							$elem->addChild('rooms-offered', $item['num_of_rooms']);
						else
							$elem->addChild('rooms-offered', 1);
					}
				}

				/* наличие телефона */
				if (isset($item['reference']) && isset($item['reference'][29]))
					$elem->addChild('phone', 1);

				/* наличие интернета */
				if (isset($item['reference']) && isset($item['reference'][30]))
					$elem->addChild('internet', 1);

				/* наличие телевизора */
				if (isset($item['reference']) && isset($item['reference'][39]))
					$elem->addChild('television', 1);

				/* наличие стиральной машины */
				if (isset($item['reference']) && isset($item['reference'][11]))
					$elem->addChild('washing-machine', 1);

				/* наличие холодильника */
				if (isset($item['reference']) && isset($item['reference'][27]))
					$elem->addChild('refrigerator', 1);

				/* тип санузла */
				if (isset($item['reference']) && isset($item['reference'][10]))
					$elem->addChild('bathroom-unit', 'раздельный');
				else
					$elem->addChild('bathroom-unit', 'совмещенный');

				/* вид из окон */
				if ($item['window_to']) {
					$elem->addChild('window-view', $item['window_to_name']);
				}

				/* этаж */
				if ($item['floor'])
					$elem->addChild('floor', $item['floor']);

				/* всего этажей */
				if ($item['floor_total'])
					$elem->addChild('floors-total', $item['floor_total']);

				/* для аренды: можно ли с животными */
				if ($item['type'] == YandexRealty::TYPE_RENT) {
					if (isset($item['reference']) && isset($item['reference'][42]))
						$elem->addChild('with-pets', 0);
				}


				/* // если участок
				if ($item['obj_type_id'] == self::$typeLand) {
					//
				}
				else {  // комната, квартира, дом
					//
				}*/
		}
		return;
	}
}