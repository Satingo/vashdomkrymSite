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

class HSite {
	public static $_allTimeZones;
	
    public static function registerMainAssets(){
        $cs = Yii::app()->clientScript;
        //$cs->coreScriptPosition = CClientScript::POS_BEGIN;
        $baseThemeUrl = Yii::app()->theme->baseUrl;

        $cs->registerCoreScript('jquery');
        $cs->registerCoreScript('jquery.ui');
        $cs->registerCoreScript('rating');
        $cs->registerCssFile($baseThemeUrl . '/css/ui/jquery-ui.multiselect.css');
        $cs->registerCssFile($baseThemeUrl . '/css/redmond/jquery-ui-1.7.1.custom.css');
        $cs->registerScriptFile($cs->getCoreScriptUrl() . '/jui/js/jquery-ui-i18n.min.js'); // fix datePicker lang in free
        $cs->registerCssFile($baseThemeUrl . '/css/ui.slider.extras.css');
		$cs->registerScriptFile($baseThemeUrl . '/js/sumoselect/jquery.sumoselect.js', CClientScript::POS_BEGIN);
		$cs->registerCssFile($baseThemeUrl . '/js/sumoselect/sumoselect.css');
        $cs->registerScriptFile($baseThemeUrl . '/js/jquery.dropdownPlain.js', CClientScript::POS_BEGIN);
        $cs->registerScriptFile($baseThemeUrl . '/js/common.js', CClientScript::POS_BEGIN);
        $cs->registerScriptFile($baseThemeUrl . '/js/habra_alert.js', CClientScript::POS_END);
        $cs->registerScriptFile($baseThemeUrl . '/js/jquery.cookie.js', CClientScript::POS_END);
        $cs->registerScriptFile($baseThemeUrl . '/js/scrollto.js', CClientScript::POS_END);
        $cs->registerCssFile($baseThemeUrl . '/css/form.css', 'screen');

        // superfish menu
        $cs->registerCssFile($baseThemeUrl . '/js/superfish/css/superfish.css', 'screen');
        $cs->registerScriptFile($baseThemeUrl . '/js/superfish/js/superfish.js', CClientScript::POS_END);

        if(Yii::app()->theme->name == 'atlas'){
            $cs->registerCssFile($baseThemeUrl . '/css/rating/rating.css');
            $colorTheme = Themes::getParam('color_theme');
            if($colorTheme){
                $cs->registerCssFile($baseThemeUrl . '/css/colors/'.$colorTheme);
            }
            $cs->registerScriptFile($baseThemeUrl . '/js/jquery.easing.1.3.js', CClientScript::POS_BEGIN);
            $cs->registerScript('initizlize-superfish-menu', '
			$("#sf-menu-id").superfish( {hoverClass: "sfHover", delay: 100, animationOut: {opacity:"hide"}, animation: {opacity:"show"}, cssArrows: false, dropShadows: false, speed: "fast", speedOut: 1 });
		', CClientScript::POS_READY);
        }

        if(Yii::app()->theme->name == 'classic'){
            $cs->registerCssFile($cs->getCoreScriptUrl().'/rating/jquery.rating.css');
            $cs->registerCssFile($baseThemeUrl.'/js/superfish/css/superfish-vertical.css', 'screen');
            $cs->registerScriptFile($baseThemeUrl.'/js/superfish/js/hoverIntent.js', CClientScript::POS_HEAD);

            $cs->registerScript('initizlize-superfish-menu', '
			$("#sf-menu-id").superfish( {delay: 100, autoArrows: false, dropShadows: false, pathClass: "overideThisToUse", speed: "fast" });
		', CClientScript::POS_READY);
        }
		
		if (param('useShowInfoUseCookie')) {
			$cs->registerScriptFile(Yii::app()->request->baseUrl . '/common/js/cookiebar/jquery.cookiebar.js', CClientScript::POS_END);
			$cs->registerCssFile(Yii::app()->request->baseUrl . '/common/js/cookiebar/jquery.cookiebar.css');
		}
		
		//$cs->registerScriptFile(Yii::app()->request->baseUrl . '/common/js/browser_fix.js');
    }
	
	public static function markdown( $str ) {
        $md = new CMarkdownParser;
        // http://htmlpurifier.org/live/configdoc/plain.html
        $md->purifierOptions = array(
            'AutoFormat.AutoParagraph' => true,
            //'AutoFormat.DisplayLinkURI' => true,
            'AutoFormat.Linkify' => true,
        );
        return $md->safeTransform($str);
    }
	
	
	public static function setSiteTimeZone() {
		/*if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
			$timeZone = @date_default_timezone_get();
			
			date_default_timezone_set($timeZone);			
			Yii::app()->timeZone = $timeZone;
		}*/
	}
	
	public static function setSettingTimeZone() {
		/*$settingTimeZone = param('site_timezone');
		if (empty($settingTimeZone) || utf8_strlen($settingTimeZone) < 1) {
			if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
				$timeZone = @date_default_timezone_get();
				if ($timeZone) {
					ConfigurationModel::updateValue('site_timezone', $timeZone);
				}
			}
		}*/
	}

	public static function getListTimeZonesArr($default = '') {
		if (!isset(self::$_allTimeZones)) {
			$unsortedTimezones = DateTimeZone::listIdentifiers();

			self::$_allTimeZones = array();
			foreach ($unsortedTimezones as $timezone) {
				$tz = new DateTimeZone($timezone);
				$dt = new DateTime('now', $tz);
				$offset = $dt->getOffset();
				$current_time = $dt->format(param('dateFormat', 'd.m.Y H:i:s'));
				$offset_string = self::formatTimezoneOffset($offset, true);
				self::$_allTimeZones['UTC' . $offset_string . ' - ' . $timezone] = array(
					'tz' => $timezone,
					'offset' => $offset_string,
					'current' => $current_time,
					'optgroup' => 'UTC' . $offset_string . ' - ' . $current_time,
				);
			}

			unset($unsortedTimezones);
			uksort(self::$_allTimeZones, array('self', 'timezoneSelectCompare'));
			
			self::$_allTimeZones = CHtml::listData(self::$_allTimeZones, 'tz', 'tz', 'optgroup');
		}
		
		return self::$_allTimeZones;
	}
			
	public static function formatTimezoneOffset($tz_offset, $showNull = false) {
		$sign = ($tz_offset < 0) ? '-' : '+';
		$time_offset = abs($tz_offset);

		if ($time_offset == 0 && $showNull == false) {
			return '';
		}

		$offset_seconds	= $time_offset % 3600;
		$offset_minutes	= $offset_seconds / 60;
		$offset_hours	= ($time_offset - $offset_seconds) / 3600;

		$offset_string	= sprintf("%s%02d:%02d", $sign, $offset_hours, $offset_minutes);
		return $offset_string;
	}
	
	public static function timezoneSelectCompare($a, $b) {
		$a_sign = $a[3];
		$b_sign = $b[3];
		if ($a_sign != $b_sign) {
			return $a_sign == '-' ? -1 : 1;
		}

		$a_offset = substr($a, 4, 5);
		$b_offset = substr($b, 4, 5);
		if ($a_offset == $b_offset) {
			$a_name = substr($a, 12);
			$b_name = substr($b, 12);
			if ($a_name == $b_name) {
				return 0;
			}
			else if ($a_name == 'UTC') {
				return -1;
			}
			else if ($b_name == 'UTC') {
				return 1;
			}
			else {
				return $a_name < $b_name ? -1 : 1;
			}
		}
		else {
			if ($a_sign == '-') {
				return $a_offset > $b_offset ? -1 : 1;
			}
			else {
				return $a_offset < $b_offset ? -1 : 1;
			}
		}
	}
	
	public static function convertDateToDateWithTimeZone($date = '', $format = 'Y-m-d H:i:s') {
		/*if (!empty($date) && $date != '0000-00-00 00:00:00') {
			$timeZone = param('site_timezone');
		
			if (empty($timeZone) || utf8_strlen($timeZone) < 1) {
				$timeZone = 'UTC';
			}
								
			$dateTime = new DateTime($date);
			$dateTime->setTimezone(new DateTimeZone($timeZone));
			
			return $dateTime->format($format);
		}*/
		
		return $date;
	}
	
	public static function convertDateWithTimeZoneToDate($date = '', $format = 'Y-m-d H:i:s') {
		/*if (!empty($date) && $date != '0000-00-00 00:00:00') {
			$dateTime = new DateTime($date, new DateTimeZone(param('site_timezone')));
			$dateTime->setTimezone(new DateTimeZone(Yii::app()->timeZone));
			
			return $dateTime->format($format);
		}*/
		
		return $date;
	}
	
	public static function createNowDateWithTimeZone($format = 'Y-m-d H:i:s') {
		/*$dateTime = new DateTime("now", new DateTimeZone(param('site_timezone')));
		return $dateTime->format($format);*/
	}
}