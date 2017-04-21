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

class YandexRealty extends Apartment {
	public static function getAdsWithoutDateCriteria() {
		$lang = Yii::app()->language;
		
		$resActiveYandexRealtyAllAds = Yii::app()->cache->get("activeYandexRealtyAllAds{$lang}");
		if($resActiveYandexRealtyAllAds !== false)
			return $resActiveYandexRealtyAllAds;
		
		$userAdsCondition = $addSelect = $addSelectJoin = '';
		
		$where = ' AND ( ap.type = '.YandexRealty::TYPE_RENT.' OR ap.type = '.YandexRealty::TYPE_SALE.') '; // только аренда/продажа
		$where .= ' AND (ap.is_price_poa = 0) '; // "цена договорная" - не поддерживается Яндексом.
				
		if (issetModule('userads') && param('useModuleUserAds', 1)) {
			$userAdsCondition = ' AND owner_active = "'.self::STATUS_ACTIVE.'" ';
		}

		if (issetModule('location')) {
			$addSelect = '
					lc.name_'.$lang.' as loc_country_name,
					lr.name_'.$lang.' as loc_region_name,
					lcc.name_'.$lang.' as loc_city_name,
					ap.loc_country, ap.loc_region, ap.loc_city,
				';
			$addSelectJoin = '
					LEFT JOIN {{location_country}} lc ON lc.id = ap.loc_country
					LEFT JOIN {{location_region}} lr ON lr.id = ap.loc_region
					LEFT JOIN {{location_city}} lcc ON lcc.id = ap.loc_city
				';
		}

		if (issetModule('seo')) {
			$addSelect .= ' seo.url_'.$lang.' as seoUrl, ';
			$addSelectJoin .= ' LEFT JOIN {{seo_friendly_url}} seo ON (seo.model_id = ap.id) AND (seo.model_name = "Apartment")';
		}
		
		if (issetModule('seasonalprices')) {
			$addSelect .= ' seasonprice.price as price, seasonprice.price_type as price_type, ';
			$addSelectJoin .= ' LEFT JOIN {{seasonal_prices}} seasonprice ON seasonprice.apartment_id = ap.id';
		}
		else {
			$addSelect .= ' ap.price as price, ap.price_type as price_type, ';
		}

		$sql = '
				SELECT ap.id, ap.type, ap.obj_type_id,
				ap.city_id, ap.num_of_rooms, ap.floor, ap.floor_total, ap.square, ap.land_square, ap.window_to,
				ap.title_'.$lang.', ap.description_'.$lang.',
				ap.description_near_'.$lang.', ap.address_'.$lang.',
				ap.berths, ap.lat, ap.lng, ap.date_manual_updated, ap.date_created,
			 	'.$addSelect.'
				ac.name_'.$lang.' as city_name,
				awt.title_'.$lang.' as window_to_name,
				u.phone as owner_phone, u.email as owner_email, u.id as owner_id, u.username as owner_username,
				aop.name_'.$lang.' as obj_type_name
				FROM {{apartment}} ap
				'.$addSelectJoin.'
				LEFT JOIN {{apartment_obj_type}} aop ON aop.id = ap.obj_type_id
				LEFT JOIN {{apartment_city}} ac ON ac.id = ap.city_id
				LEFT JOIN {{apartment_window_to}} awt ON awt.id = ap.window_to
				LEFT JOIN {{users}} u ON u.id = ap.owner_id
				WHERE
					ap.lat > 1 AND ap.lng > 1
					AND ap.num_of_rooms < 80 AND ap.floor < 80 AND ap.floor_total < 90
					AND (LENGTH (u.phone) > 0) AND ap.active = "'.self::STATUS_ACTIVE.'" '.$userAdsCondition.' '.$where.'
				ORDER BY ap.id DESC
				LIMIT 4500
				';

		$activeAds = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (param('cachingTime'))
			Yii::app()->cache->set("activeYandexRealtyAllAds{$lang}", $activeAds, 60*60*param('cachingTime'));
		
		return $activeAds;
	}
}