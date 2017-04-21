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


class HGeo
{
    public static function getGeoCountryID()
    {
        $country = self::getGeoValue('country', 'id');
        $list = Country::getCountriesArray(4);
        if(in_array($country, array_keys($list))){
            return $country;
        }
        return '';
    }
    public static function getGeoRegionID()
    {
        return self::getGeoValue('region', 'id');
    }
    public static function getGeoCityID()
    {
        return self::getGeoValue('city', 'id');
    }

    public static function getGeoValue($loc, $key, $default = '')
    {
        if(!Yii::app()->controller->geo){
            return $default;
        }
        $geo = Yii::app()->controller->geo;
        return (isset($loc) && isset($geo[$loc][$key])) ? $geo[$loc][$key] : $default;
    }

    public static function init()
    {
        if(!issetModule('location') || !issetModule('geo')){
            return false;
        }

        $geoCache = HCookie::get('geo');
        if($geoCache){
            Yii::app()->controller->geo = $geoCache;
        }else{
            $module = Yii::app()->getModule('geo');
            $ip = Yii::app()->request->userHostAddress;
            //$ip = '109.194.101.182'; # Йошкар-Ола
            //$ip = '72.229.28.185'; # New York

            Yii::app()->controller->geo = $module->getGeoData($ip);
            if(Yii::app()->controller->geo){
                HCookie::set('geo', Yii::app()->controller->geo, true, param('geo_time_cache', 86400));
            }
        }

        if(Yii::app()->controller->geo && param('geo_in_search') && !isset($_GET['country'])){
            $country = self::getGeoCountryID();

            $countryInSearch = Country::getCountriesArray(2, 0, true);
            if($country && in_array($country, array_keys($countryInSearch))){
                $region = self::getGeoRegionID();
                $city = self::getGeoCityID();

                Yii::app()->controller->selectedCountry = $country;
                if($region && param('geo_in_search') > 1){
                    Yii::app()->controller->selectedRegion = $region;
                    if($city && param('geo_in_search') == 3){
                        Yii::app()->controller->selectedCity = $city;
                    }
                }
            }
        }
    }

    /**
     * @param Apartment $ad
     * @return Apartment
     */
    public static function setForAd($ad)
    {
        if(!issetModule('location') || !issetModule('geo') || !Yii::app()->controller->geo){
            return $ad;
        }

        if(!$ad->loc_country && ($ad->isNewRecord || $ad->active == Apartment::STATUS_DRAFT)){
            $country = self::getGeoCountryID();
            $region = self::getGeoRegionID();
            $city = self::getGeoCityID();
            $countryActive = Country::getCountriesArray(0, 1);
            if($country && param('geo_in_ad') && in_array($country, array_keys($countryActive))){
                $ad->loc_country = $country;
                if($region && param('geo_in_ad') > 1){
                    $ad->loc_region = $region;
                    if($city && param('geo_in_ad') == 3){
                        $ad->loc_city = $city;
                    }
                }
            }
        }
        return $ad;
    }

    public static function setForIndexCriteria(CDbCriteria $criteria)
    {
        if(issetModule('location') && issetModule('geo') && param('geo_in_index') && Yii::app()->controller->geo){
            $country = self::getGeoCountryID();
            $region = self::getGeoRegionID();
            $city = self::getGeoCityID();

            // фильтруем только по странам с объявлениями
            if(param('geo_in_index_flag')){
                $countryActive = Country::getCountriesArray(2, 0, true);
            }else{
                $countryActive = Country::getCountriesArray(0, 1);
            }
            if($country && in_array($country, array_keys($countryActive))){
                $criteria->compare('t.loc_country', $country);

                if($region){
                    if(param('geo_in_index') > 1){
                        $criteria->compare('t.loc_region', $region);
                    }
                    if($city && param('geo_in_index') == 3){
                        $criteria->compare('t.loc_city', $region);
                    }
                }
            }
        }

        return $criteria;
    }
}