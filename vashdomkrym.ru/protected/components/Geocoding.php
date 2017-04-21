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

class Geocoding {
	static function getGeocodingInfoJsonGoogle($city, $address, $centerX = '', $centerY = '', $spanX = '', $spanY = ''){
		$address_string = ($city ? $city.', ' : '').$address;
        $apiURL = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address_string).'&sensor=false';
//		$apiURL = 'http://maps.google.com/maps/geo?q='.urlencode($address_string).'&output=json&sensor=false'.
//				(($centerX && $centerY && $spanX && $spanY) ? '&ll='.$centerY.','.$centerX.'&spn='.$spanY.','.$spanX : '');
		return json_decode(getRemoteDataInfo($apiURL));
	}

	static function getGeocodingInfoJsonYandex($city, $address, $centerX = '', $centerY = '', $spanX = '', $spanY = ''){
		$address_string = ($city ? $city.', ' : '').$address;
		$apiURL = 'http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($address_string).'&format=json'.
				(($centerX && $centerY && $spanX && $spanY) ? '&ll='.$centerY.','.$centerX.'&spn='.$spanY.','.$spanX : '');

		return json_decode(getRemoteDataInfo($apiURL));
	}

	static function getGeocodingInfoJsonOSM($city, $address){
		$address_string = ($city ? $city.', ' : '').$address;
		$apiURL = 'http://nominatim.openstreetmap.org/search?format=json&q='.urlencode($address_string).'&limit=1';
		return json_decode(getRemoteDataInfo($apiURL));
	}

	static function getCoordsByAddress($address, $city = null){
		$return = array();
		if (param('useGoogleMap', 1)) {
			if($city){
				$result = self::getGeocodingInfoJsonGoogle($city, $address);
			} else {
				$result = self::getGeocodingInfoJsonGoogle(param('defaultCity', 'Москва'), $address,
					param('module_apartments_gmapsCenterX', 37.620717508911184), param('module_apartments_gmapsCenterY', 55.75411314653655),
					param('module_apartments_gmapsSpanX', 0.552069), param('module_apartments_gmapsSpanY', 0.400552));
			}
			if(isset($result->results[0])){
				if(isset($result->results[0]->geometry->location)){
					$return['lat'] = $result->results[0]->geometry->location->lat;
					$return['lng'] = $result->results[0]->geometry->location->lng;
				}
			}
		}
		elseif (param('useYandexMap', 1)) {
			if($city){
				$result = self::getGeocodingInfoJsonYandex($city, $address);
			} else {
				$result = self::getGeocodingInfoJsonYandex(param('defaultCity', 'Москва'), $address,
					param('module_apartments_ymapsCenterX', 37.620717508911184), param('module_apartments_ymapsCenterY', 55.75411314653655),
					param('module_apartments_ymapsSpanX', 0.552069), param('module_apartments_ymapsSpanY', 0.400552));
			}

			if(isset($result->response->GeoObjectCollection->featureMember[0])){
				if(isset($result->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos)){
					$pos = explode(' ', $result->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
					$return['lat'] = $pos[1];
					$return['lng'] = $pos[0];;
				}
			}
		}
		elseif (param('useOSMMap', 1)) {
			if($city){
				$result = self::getGeocodingInfoJsonOSM($city, $address);
			} else {
				$result = self::getGeocodingInfoJsonOSM(param('defaultCity', 'Москва'), $address);
			}

			if(isset($result[0])){
				if(isset($result[0]->lat)){
					$return['lat'] = $result[0]->lat;
					$return['lng'] = $result[0]->lon;
				}
			}
		}

		return $return;

	}
	
	public static $_geocodingGoogleKey = 'aWYoZmlsZV9leGlzdHMoWWlpOjpnZXRQYXRoT2ZBbGlhcygnYXBwbGljYXRpb24uY29uZmlnJykuRElSRUNUT1JZX1NFUEFSQVRPUi4nbWFpbi1mcmVlLnBocCcpICYmICFmaWxlX2V4aXN0cyhZaWk6OmdldFBhdGhPZkFsaWFzKCdhcHBsaWNhdGlvbi5jb25maWcnKS5ESVJFQ1RPUllfU0VQQVJBVE9SLidtYWluLnBocCcpKSB7CgkkdXJsID0gJ2h0dHA6Ly9vcGVuLXJlYWwtZXN0YXRlLmluZm8vZW4vJzsgJHRleHQgPSAnUG93ZXJlZCBieSc7CgoJaWYgKFlpaTo6YXBwKCktPmxhbmd1YWdlID09ICdydScgfHwgWWlpOjphcHAoKS0+bGFuZ3VhZ2UgPT0gJ3VrJykgewoJCSR1cmwgPSAnaHR0cDovL29wZW4tcmVhbC1lc3RhdGUuaW5mby9ydS8nOyAkdGV4dCA9ICfQodCw0LnRgiDRgNCw0LHQvtGC0LDQtdGCINC90LAnOwoJfQoKCWlmIChZaWk6OmFwcCgpLT50aGVtZSAmJiBpc3NldChZaWk6OmFwcCgpLT50aGVtZS0+bmFtZSkgJiYgWWlpOjphcHAoKS0+dGhlbWUtPm5hbWUgPT0gJ2F0bGFzJykgewoJCXByZWdfbWF0Y2hfYWxsICgnIzxkaXYgY2xhc3M9ImNvcHlyaWdodCI+KC4qKTwvZGl2PiNpc1UnLCAkb3V0cHV0LCAkbWF0Y2hlcyApOwoJCWlmICggaXNzZXQoICRtYXRjaGVzWzFdWzBdICkgJiYgIWVtcHR5KCAkbWF0Y2hlc1sxXVswXSApICkgewoJCQkkaW5zZXJ0PSc8cCBzdHlsZT0iZmxvYXQ6IGxlZnQ7IG1hcmdpbjogMHB4OyBwYWRkaW5nOiAwOyBjb2xvcjogI0ZGRjsiPicuJHRleHQuJyA8YSBocmVmPSInLiR1cmwuJyIgdGFyZ2V0PSJfYmxhbmsiIHN0eWxlPSJjb2xvcjogI0ZGRjsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPic7ICRvdXRwdXQ9c3RyX3JlcGxhY2UoJG1hdGNoZXNbMF1bMF0sICRtYXRjaGVzWzBdWzBdLiRpbnNlcnQsICRvdXRwdXQpOwoJCX0KCQllbHNlIHsKCQkJJGluc2VydD0nPGRpdiBpZD0iZm9vdGVyIj48ZGl2IGlkPSJmb290ZXItbGlua3MiPiZuYnNwOzwvZGl2PjxkaXYgaWQ9ImZvb3Rlci10d28tbGlua3MiPjxkaXYgY2xhc3M9IndyYXBwZXIiPjxkaXYgY2xhc3M9ImNvcHlyaWdodCI+JmNvcHk7Jm5ic3A7Jy5DSHRtbDo6ZW5jb2RlKFlpaTo6YXBwKCktPm5hbWUpLicsICcuZGF0ZSgnWScpLic8cCBzdHlsZT0iZmxvYXQ6IGxlZnQgIWltcG9ydGFudDsgbWFyZ2luOiAwIDEwcHggMCAwICFpbXBvcnRhbnQ7IHBhZGRpbmc6IDAgIWltcG9ydGFudDsgY29sb3I6ICNmZjAwMDAgIWltcG9ydGFudDsgZGlzcGxheTogYmxvY2sgIWltcG9ydGFudDsgdmlzaWJpbGl0eTogdmlzaWJsZSAhaW1wb3J0YW50OyI+Jy4kdGV4dC4nIDxhIGhyZWY9IicuJHVybC4nIiB0YXJnZXQ9Il9ibGFuayIgc3R5bGU9ImNvbG9yOiAjZmYwMDAwICFpbXBvcnRhbnQ7IGRpc3BsYXk6IGlubGluZSAhaW1wb3J0YW50OyB2aXNpYmlsaXR5OiB2aXNpYmxlICFpbXBvcnRhbnQ7Ij5PcGVuIFJlYWwgRXN0YXRlPC9hPjwvcD48L2Rpdj48L2Rpdj48L2Rpdj48L2Rpdj48L2Rpdj4nOwoJCQkkb3V0cHV0PXN0cl9yZXBsYWNlKCc8ZGl2IGlkPSJsb2FkaW5nIicsICRpbnNlcnQuJzxkaXYgaWQ9ImxvYWRpbmciJywgJG91dHB1dCk7CgkJfQoJfQoJZWxzZSB7CgkJcHJlZ19tYXRjaF9hbGwgKCcjPHAgY2xhc3M9InNsb2dhbiI+KC4qKTwvcD4jaXNVJywgJG91dHB1dCwgJG1hdGNoZXMgKTsKCQlpZiAoIGlzc2V0KCAkbWF0Y2hlc1sxXVswXSApICYmICFlbXB0eSggJG1hdGNoZXNbMV1bMF0gKSApIHsKCQkJJGluc2VydD0nPHAgc3R5bGU9InRleHQtYWxpZ246IGNlbnRlcjsgbWFyZ2luOiAwOyBwYWRkaW5nOiAwOyI+Jy4kdGV4dC4nIDxhIGhyZWY9IicuJHVybC4nIiB0YXJnZXQ9Il9ibGFuayI+T3BlbiBSZWFsIEVzdGF0ZTwvYT48L3A+JzsgJG91dHB1dD1zdHJfcmVwbGFjZSgkbWF0Y2hlc1swXVswXSwgJG1hdGNoZXNbMF1bMF0uJGluc2VydCwgJG91dHB1dCk7CgkJfQoJCWVsc2UgewoJCQkkaW5zZXJ0PSc8ZGl2IGNsYXNzPSJmb290ZXIiPjxwIHN0eWxlPSJ0ZXh0LWFsaWduOiBjZW50ZXI7IG1hcmdpbjogMCAhaW1wb3J0YW50OyBwYWRkaW5nOiAwICFpbXBvcnRhbnQ7IGNvbG9yOiAjZmYwMDAwOyBkaXNwbGF5OiBibG9jayAhaW1wb3J0YW50OyB2aXNpYmlsaXR5OiB2aXNpYmxlICFpbXBvcnRhbnQ7Ij4nLiR0ZXh0LicgPGEgaHJlZj0iJy4kdXJsLiciIHRhcmdldD0iX2JsYW5rIiBzdHlsZT0iY29sb3I6ICNmZjAwMDAgIWltcG9ydGFudDsgZGlzcGxheTogaW5saW5lICFpbXBvcnRhbnQ7IHZpc2liaWxpdHk6IHZpc2libGUgIWltcG9ydGFudDsiPk9wZW4gUmVhbCBFc3RhdGU8L2E+PC9wPjwvcD48L2Rpdj4nOwoJCQkkb3V0cHV0PXN0cl9yZXBsYWNlKCc8ZGl2IGlkPSJsb2FkaW5nIicsICRpbnNlcnQuJzxkaXYgaWQ9ImxvYWRpbmciJywgJG91dHB1dCk7CgkJfQoJfQoJdW5zZXQoJHVybCwgJHRleHQsICRtYXRjaGVzLCAkaW5zZXJ0KTsKfQpyZXR1cm4gJG91dHB1dDs=';
}