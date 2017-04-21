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

class HCookie {
	const PREFIX = '';
	const GEO = 'geo';
	const GUEST = 'guest';

	// 1 год по умолчанию 31104000, 864000 месяц
	const TIME_SAVE = 864000;

	public static function get($field, $decodeFromJson = true, $default = array()) {
		$value = isset(Yii::app()->request->cookies[self::PREFIX . $field]) ? Yii::app()->request->cookies[self::PREFIX . $field]->value : NULL;

		if($value){
			return $decodeFromJson ? CJSON::decode($value) : $value;
		}
		return $default;
	}

	public static function set($field, $value, $encodeInJson = true, $time = self::TIME_SAVE) {
		$cookie = new CHttpCookie(self::PREFIX . $field, ($encodeInJson ? CJSON::encode($value) : $value));

		$cookie->expire = time() + $time;
		Yii::app()->request->cookies[self::PREFIX . $field] = $cookie;
	}

	public static function delete($field) {
		unset(Yii::app()->request->cookies[self::PREFIX . $field]);
	}

}