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

class MyMemoryTranslated {
	private $_errors = "";

	public function _construct() {
		if (!function_exists('curl_init')) {
			$this->_errors = "No CURL support";
		}
	}

	public function translateText($text, $fromLanguage = "en", $toLanguage = "ru", $translit = false) {
		$text = strip_tags($text);
		
		if (mb_strlen($text) < 500) {
			$result = getRemoteDataInfo("http://mymemory.translated.net/api/get?q=".urlencode($text)."&langpair={$fromLanguage}|{$toLanguage}");
			
			if ($result) {
				$result = CJSON::decode($result);

				if (!empty($result) && isset($result['responseStatus']) && $result['responseStatus'] == 200 && isset($result['responseData'])) {
					return $result['responseData']['translatedText'];
				}
			}
		}
		
		return false;
	}
}

?>