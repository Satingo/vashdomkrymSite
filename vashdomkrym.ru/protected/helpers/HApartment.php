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


class HApartment {

    private static $_price_arr;
    private static $_type_arr;
    private static $_available_type_arr;


    public static function saveOther(Apartment $ad){
        if(ApartmentVideo::saveVideo($ad)){
            $ad->panoramaFile = CUploadedFile::getInstance($ad, 'panoramaFile');
            $ad->scenario = 'panorama';
            if(!$ad->validate()) {
                return false;
            }
        }

        $city = "";
        if (issetModule('location')) {
            $city .= $ad->locCountry ? $ad->locCountry->getStrByLang('name') : "";
            $city .= ($city && $ad->locCity) ? ", " : "";
            $city .= $ad->locCity ? $ad->locCity->getStrByLang('name') : "";
        } else
            $city = $ad->city ? $ad->city->getStrByLang('name') : "";

        // data
        if(($ad->address && $city) && (param('useGoogleMap', 1) || param('useYandexMap', 1) || param('useOSMMap', 1))){
            if (!$ad->lat && !$ad->lng) { # уже есть
                $coords = Geocoding::getCoordsByAddress($ad->address, $city);

                if(isset($coords['lat']) && isset($coords['lng'])){
                    $ad->lat = $coords['lat'];
                    $ad->lng = $coords['lng'];
                }
            }
        }

        return true;
    }

    public static function getRequestType(){
        $type = Yii::app()->getRequest()->getQuery('type');
        $existType = array_keys(HApartment::getTypesArray());
        if(!in_array($type, $existType)){
            $type = Apartment::TYPE_DEFAULT;
        }
        return $type;
    }

    /** Сохраняем данные выбранных справочников
     * @return array
     */
    public static function getCategoriesForUpdate(Apartment $ad)
    {
        if (isset($_POST['category']) && is_array($_POST['category'])) {
            $ad->references = HApartment::getCategories(null, $ad->type);
            foreach ($_POST['category'] as $cat => $categoryArray) {
                foreach ($categoryArray as $key => $value) {
                    $ad->references[$cat]['values'][$key]['selected'] = true;
                }
            }
        } else {
            $ad->references = HApartment::getCategories($ad->id, $ad->type);
        }

        return $ad->references;
    }


    public static function getModeShowList() {
		$return = array(
            'block' => tt('Display block', 'apartments'),
            'table' => tt('Display table', 'apartments'),
            'map' => tt('Display with a map', 'apartments'),
        );
		
		$useAdditionalView = Themes::getParam('additional_view');
		if ($useAdditionalView && $useAdditionalView == Themes::ADDITIONAL_VIEW_FULL_WIDTH_MAP) {
			unset($return['map']);
		}
		
        return $return;
    }


    public static function getPeriodActivityList()
    {
        // key for strtotime - http://php.net/manual/ru/function.strtotime.php
        return array(
            '+1 week' => tt('a week', 'apartments'),
            '+1 month' => tt('a month', 'apartments'),
            '+3 month' => tt('3 months', 'apartments'),
            '+6 month' => tt('6 months', 'apartments'),
            '+1 year' => tt('a year', 'apartments'),
            'always' => tt('always', 'apartments'),
        );
    }

    public static function getPriceArray($type, $all = false, $with_all = false) {
        if ($all) {
            $price = array(
                '0' => '', //price on ask
                Apartment::PRICE_SALE => tt('Sale price', 'apartments'),
                Apartment::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                Apartment::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                Apartment::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                Apartment::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
                Apartment::PRICE_RENTING => '',
                Apartment::PRICE_BUY => '',
                Apartment::PRICE_CHANGE => '',
            );


			if (!param('useTypeRentHour', 1))
				unset($price[Apartment::PRICE_PER_HOUR]);


			if (!param('useTypeRentDay', 1))
				unset($price[Apartment::PRICE_PER_DAY]);


			if (!param('useTypeRentWeek', 1))
				unset($price[Apartment::PRICE_PER_WEEK]);


			if (!param('useTypeRentMonth', 1))
				unset($price[Apartment::PRICE_PER_MONTH]);
			
			if (!param('useTypeSale', 1))
				unset($price[Apartment::PRICE_SALE]);
			
			if (!param('useTypeRenting', 1))
				unset($price[Apartment::PRICE_RENTING]);
			
			if (!param('useTypeBuy', 1))
				unset($price[Apartment::PRICE_BUY]);
			
			if (!param('useTypeChange', 1))
				unset($price[Apartment::PRICE_CHANGE]);

            return $price;
        }

        if ($type == Apartment::TYPE_SALE) {
            $price = array(
                Apartment::PRICE_SALE => tt('Sale price', 'apartments'),
            );
        }
        elseif ($type == Apartment::TYPE_RENT) {
            $price = array(
                Apartment::PRICE_PER_HOUR => tt('Price per hour', 'apartments'),
                Apartment::PRICE_PER_DAY => tt('Price per day', 'apartments'),
                Apartment::PRICE_PER_WEEK => tt('Price per week', 'apartments'),
                Apartment::PRICE_PER_MONTH => tt('Price per month', 'apartments'),
            );

            if (!param('useTypeRentHour', 1) && array_key_exists(Apartment::PRICE_PER_HOUR, $price))
                unset($price[Apartment::PRICE_PER_HOUR]);

            if (!param('useTypeRentDay', 1) && array_key_exists(Apartment::PRICE_PER_DAY, $price))
                unset($price[Apartment::PRICE_PER_DAY]);

            if (!param('useTypeRentWeek', 1) && array_key_exists(Apartment::PRICE_PER_WEEK, $price))
                unset($price[Apartment::PRICE_PER_WEEK]);

            if (!param('useTypeRentMonth', 1) && array_key_exists(Apartment::PRICE_PER_MONTH, $price))
                unset($price[Apartment::PRICE_PER_MONTH]);
        }
        elseif ($type == Apartment::TYPE_RENTING) {
            $price = array(
                Apartment::PRICE_RENTING => '',
            );
        }
        elseif ($type == Apartment::TYPE_BUY) {
            $price = array(
                Apartment::PRICE_BUY => '',
            );
        }
        elseif ($type == Apartment::TYPE_CHANGE) {
            $price = array(
                Apartment::PRICE_CHANGE => '',
            );
        }

        if ($with_all) {
            $price[0] = tt('All');
        }
        return $price;
    }

    public static function getPriceMinMax($objTypeId = 1, $all = false) {
        $ownerActiveCond = '';
        if (param('useUserads'))
            $ownerActiveCond = ' AND owner_active = ' . Apartment::STATUS_ACTIVE . ' ';

        if ($all)
            $sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE price_type IN(' . implode(",", array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))) . ') AND active = ' . Apartment::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND is_price_poa = 0';
        else
            $sql = 'SELECT MIN(price) as price_min, MAX(price) as price_max FROM {{apartment}} WHERE price_type IN(' . implode(",", array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))) . ') AND obj_type_id = "' . $objTypeId . '" AND active = ' . Apartment::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND is_price_poa = 0';
        $result = Yii::app()->db->cache(param('cachingTime', 1209600), Apartment::getDependency())->createCommand($sql)->queryRow();

        if (issetModule('seasonalprices')) {
            if ($all)
                $sql = 'SELECT MIN(s.price) as price_min, MAX(s.price) as price_max FROM {{seasonal_prices}} s LEFT JOIN {{apartment}} a ON a.id = s.apartment_id WHERE s.price_type IN(' . implode(",", array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))) . ') AND a.active = ' . Apartment::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND a.is_price_poa = 0';
            else
                $sql = 'SELECT MIN(s.price) as price_min, MAX(s.price) as price_max FROM {{seasonal_prices}} s LEFT JOIN {{apartment}} a ON a.id = s.apartment_id WHERE s.price_type IN(' . implode(",", array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))) . ') AND a.obj_type_id = "' . $objTypeId . '" AND a.active = ' . Apartment::STATUS_ACTIVE . ' ' . $ownerActiveCond . ' AND a.is_price_poa = 0';
            $resultSeasonalPrices = Yii::app()->db->cache(param('cachingTime', 1209600), Apartment::getDependency())->createCommand($sql)->queryRow();

            if ($resultSeasonalPrices['price_min'] > $result['price_min'])
                $resultSeasonalPrices['price_min'] = $result['price_min'];

            if ($resultSeasonalPrices['price_max'] < $result['price_max'])
                $resultSeasonalPrices['price_max'] = $result['price_max'];

            return $resultSeasonalPrices;
        }


        return $result;
    }


    public static function getCountModeration()
    {
        $sql = "SELECT COUNT(id) FROM {{apartment}} WHERE price_type IN (" . implode(',', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))) . ") AND active=" . Apartment::STATUS_MODERATION;
        return (int)Yii::app()->db->createCommand($sql)->queryScalar();
    }

    public static function findAllWithCache($criteria)
    {
        return Apartment::model()
            ->cache(param('cachingTime', 1209600), HApartment::getImagesDependency())
            ->with(array('images', 'objType'))
            ->findAll($criteria);
    }

    public static function getImagesDependency()
    {
        return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
    }

    public static function getTip($field) {
        if (issetModule('formdesigner')) {
            Yii::import('application.modules.formdesigner.models.*');
            return FormDesigner::getTipForm($field);
        }
        return '';
    }

    public static function availableApTypesIds() {
        if (!isset(HApartment::$_available_type_arr)) {
            HApartment::$_available_type_arr = array();

            if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
                HApartment::$_available_type_arr[] = Apartment::TYPE_RENT;
            }
            if (param('useTypeSale', 1)) {
                HApartment::$_available_type_arr[] = Apartment::TYPE_SALE;
            }
            if (param('useTypeRenting', 1)) {
                HApartment::$_available_type_arr[] = Apartment::TYPE_RENTING;
            }
            if (param('useTypeBuy', 1)) {
                HApartment::$_available_type_arr[] = Apartment::TYPE_BUY;
            }
            if (param('useTypeChange', 1)) {
                HApartment::$_available_type_arr[] = Apartment::TYPE_CHANGE;
            }
        }


        return HApartment::$_available_type_arr;
    }

    public static function countPriceTypes() {
        return param('useTypeRentHour', 1) + param('useTypeRentDay', 1) + param('useTypeRentWeek', 1) + param('useTypeRentMonth', 1) +
        param('useTypeSale', 1) + param('useTypeRenting', 1) + param('useTypeBuy', 1) + param('useTypeChange', 1);
    }


    public static function getNameByType($type)
    {
        if (!isset(HApartment::$_type_arr)) {
            HApartment::$_type_arr = HApartment::getTypesArray();
        }

        if (!in_array($type, array_keys(HApartment::$_type_arr))) {
            return tt('Disabled type', 'apartments');
        }

        return HApartment::$_type_arr[$type];
    }


    public static function getTypesArray($withAll = false, $withDisabled = false)
    {
        $types = array();

        if ($withAll) {
            $types[0] = tt('All', 'apartments');
        }

        if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $types[Apartment::TYPE_RENT] = tt('Rent', 'apartments');
        }
        if (param('useTypeSale', 1)) {
            $types[Apartment::TYPE_SALE] = tt('Sale', 'apartments');
        }
        if (param('useTypeRenting', 1)) {
            $types[Apartment::TYPE_RENTING] = tt('Rent a', 'apartments');
        }
        if (param('useTypeBuy', 1)) {
            $types[Apartment::TYPE_BUY] = tt('Buy a', 'apartments');
        }
        if (param('useTypeChange', 1)) {
            $types[Apartment::TYPE_CHANGE] = tt('Exchange', 'apartments');
        }
        if ($withDisabled) {
            $types[Apartment::TYPE_DISABLED] = tt('Disabled type', 'apartments');
        }
        return $types;
    }

    public static function isDisabledType() {
        return !((param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1))
            && param('useTypeSale', 1) && param('useTypeRenting', 1) && param('useTypeBuy', 1) &&
            param('useTypeChange', 1));
    }

    public static function getTypesWantArray()
    {
        $types = array();

        if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $types[Apartment::TYPE_RENTING] = tt('Want rent property form smb', 'apartments');
        }
        if (param('useTypeSale', 1)) {
            $types[Apartment::TYPE_BUY] = tt('Want buy', 'apartments');
        }
        if (param('useTypeRenting', 1)) {
            $types[Apartment::TYPE_RENT] = tt('Want rent property to smb', 'apartments');
        }
        if (param('useTypeBuy', 1)) {
            $types[Apartment::TYPE_SALE] = tt('Want sale', 'apartments');
        }
        if (param('useTypeChange', 1)) {
            $types[Apartment::TYPE_CHANGE] = tt('Want exchange', 'apartments');
        }

        return $types;
    }


    /** For Notifier
     * @return array
     */
    public static function getI18nTypesArray()
    {
        $types = array();

        self::fillI18nArray($types, 'current', Yii::app()->language);
        self::fillI18nArray($types, 'default', Lang::getDefaultLang());
        self::fillI18nArray($types, 'admin', Lang::getAdminMailLang());

        return $types;
    }

    public static function getInvertedI18nTypesArray()
    {
        $types = array();

        self::fillInvertedI18nArray($types, 'current', Yii::app()->language);
        self::fillInvertedI18nArray($types, 'default', Lang::getDefaultLang());
        self::fillInvertedI18nArray($types, 'admin', Lang::getAdminMailLang());

        return $types;
    }

    private static function fillI18nArray(&$types, $field, $lang)
    {
        if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $vs[Apartment::TYPE_RENT] = 'Want rent property to smb';
        }
        if (param('useTypeSale', 1)) {
            $vs[Apartment::TYPE_SALE] = 'Want sale';
        }
        if (param('useTypeRenting', 1)) {
            $vs[Apartment::TYPE_RENTING] = 'Want rent property form smb';
        }
        if (param('useTypeBuy', 1)) {
            $vs[Apartment::TYPE_BUY] = 'Want buy';
        }
        if (param('useTypeChange', 1)) {
            $vs[Apartment::TYPE_CHANGE] = 'Want exchange';
        }

        foreach ($vs as $type => $langField) {
            $types[$type][$field] = tt($langField, 'apartments', $lang);
        }
    }

    private static function fillInvertedI18nArray(&$types, $field, $lang)
    {
        if (param('useTypeRentHour', 1) || param('useTypeRentDay', 1) || param('useTypeRentWeek', 1) || param('useTypeRentMonth', 1)) {
            $vs[Apartment::TYPE_RENTING] = 'Want rent property form smb';
        }
        if (param('useTypeSale', 1)) {
            $vs[Apartment::TYPE_BUY] = 'Want buy';
        }
        if (param('useTypeRenting', 1)) {
            $vs[Apartment::TYPE_RENT] = 'Want rent property to smb';
        }
        if (param('useTypeBuy', 1)) {
            $vs[Apartment::TYPE_SALE] = 'Want sale';
        }
        if (param('useTypeChange', 1)) {
            $vs[Apartment::TYPE_CHANGE] = 'Want exchange';
        }

        foreach ($vs as $type => $langField) {
            $types[$type][$field] = tt($langField, 'apartments', $lang);
        }
    }

    public static function getPriceName($price_type) {
        if (!isset(self::$_price_arr)) {
            self::$_price_arr = HApartment::getPriceArray(NULL, true);
        }
        return isset(self::$_price_arr[$price_type]) ? self::$_price_arr[$price_type] : '';
    }


    public static function getFullInformation($apartmentId, $type = Apartment::TYPE_DEFAULT, $catId = null)
    {

        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

        if ($catId)
            $addWhere .= ' AND reference_categories.id = ' . (int)$catId . ' ';

        $sql = '
			SELECT	style, type,
					reference_categories.title_' . Yii::app()->language . ' as category_title,
					reference_values.title_' . Yii::app()->language . ' as value,
					reference_categories.id as ref_id,
					reference_values.id as ref_value_id
			FROM	{{apartment_reference}} reference,
					{{apartment_reference_categories}} reference_categories,
					{{apartment_reference_values}} reference_values
			WHERE	reference.apartment_id = "' . intval($apartmentId) . '"
					AND reference.reference_id = reference_categories.id
					AND reference.reference_value_id = reference_values.id
					' . $addWhere . '
			ORDER BY reference_categories.sorter, reference_values.sorter';

        // Таблица apartment_reference меняется только при измении объявления (т.е. таблицы apartment)
        // Достаточно зависимости от apartment вместо apartment_reference
        $dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "' . intval($apartmentId) . '") as t
		');

        $results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

        $return = array();
        foreach ($results as $result) {
            if (!isset($return[$result['ref_id']])) {
                $return[$result['ref_id']]['title'] = $result['category_title'];
                $return[$result['ref_id']]['style'] = $result['style'];
                $return[$result['ref_id']]['type'] = $result['type'];
            }
            $return[$result['ref_id']]['values'][$result['ref_value_id']] = $result['value'];
        }
        return $return;
    }

    public static function getCategories($id = null, $type = Apartment::TYPE_DEFAULT, $selected = array())
    {
        $addWhere = '';
        $addWhere .= (Apartment::TYPE_RENT == $type) ? ' AND reference_values.for_rent=1' : '';
        $addWhere .= (Apartment::TYPE_SALE == $type) ? ' AND reference_values.for_sale=1' : '';

        $sql = '
			SELECT	style, type,
					reference_values.title_' . Yii::app()->language . ' as value_title,
					reference_categories.title_' . Yii::app()->language . ' as category_title,
					reference_category_id, reference_values.id
			FROM	{{apartment_reference_values}} reference_values,
					{{apartment_reference_categories}} reference_categories
			WHERE	reference_category_id = reference_categories.id
			' . $addWhere . '
			ORDER BY reference_categories.sorter, reference_values.sorter';

        $dependency = new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{apartment_reference_values}}
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_reference_categories}}) as t
		');

        $results = Yii::app()->db->cache(param('cachingTime', 1209600), $dependency)->createCommand($sql)->queryAll();

        $return = array();

        if ($id) {
            $selected = HApartment::getFullInformation($id, $type);
        } else {
            // При добавлении объявления
            if ($selected && count($selected)) {
                $tmp = array();
                foreach ($selected as $selKey => $selVal) {
                    $tmp[$selKey]['values'] = $selVal;
                }
                $selected = $tmp;
            }
        }
        if ($results) {
            foreach ($results as $result) {
                $return[$result['reference_category_id']]['title'] = $result['category_title'];
                $return[$result['reference_category_id']]['style'] = $result['style'];
                $return[$result['reference_category_id']]['type'] = $result['type'];
                $return[$result['reference_category_id']]['values'][$result['id']]['title'] = $result['value_title'];
                if (isset($selected[$result['reference_category_id']]['values'][$result['id']])) {
                    $return[$result['reference_category_id']]['values'][$result['id']]['selected'] = true;
                } else {
                    $return[$result['reference_category_id']]['values'][$result['id']]['selected'] = false;
                }
            }
        }

        return $return;
    }

    public static function getFullDependency($id)
    {
        return new CDbCacheDependency('
			SELECT MAX(val) FROM
				(SELECT MAX(date_updated) as val FROM {{comments}} WHERE model_id = "' . intval($id) . '" AND model_name="Apartment"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment}} WHERE id = "' . intval($id) . '"
				UNION
				SELECT MAX(date_updated) as val FROM {{apartment_window_to}}
				UNION
				SELECT MAX(date_updated) as val FROM {{images}}) as t
		');
    }

    public static function genTitle(Apartment $ad)
    {
        $field = 'title_'.Yii::app()->language;

        $title = '';
        if($ad->objType){
            $title .= utf8_ucfirst($ad->objType->name).', ';
        }
        if($ad->type){
            $title .= HApartment::getNameByType($ad->type).', ';
        }
        if ($ad->num_of_rooms){
            $title .= ', ';
            $title .= Yii::t('module_apartments',
                '{n} bedroom|{n} bedrooms|{n} bedrooms', array($ad->num_of_rooms));
        }
        if (issetModule('location')) {
            if($ad->locCity){
                if($ad->locCountry || $ad->locRegion)
                    $title .= ', ' . $ad->locCity->getStrByLang('name');
            }
        } else {
            if(isset($ad->city) && isset($ad->city->name)){
                $title .= ', ';
                $title .= $ad->city->name;
            }
        }

        return $title;
    }

    public static function getParentList($objTypeID)
    {
        $user = HUser::getModel();
        $addWhere = '';
        if(!in_array($user->role, array(User::ROLE_ADMIN, User::ROLE_MODERATOR))){
            $addWhere = " AND owner_id = ".Yii::app()->user->id;
        }

        $sql = "SELECT id, title_".Yii::app()->language." AS name FROM {{apartment}} WHERE obj_type_id=:obj_id " . $addWhere;

        $data = Yii::app()->db->createCommand($sql)->queryAll(true, array(':obj_id' => $objTypeID));

        $list = CHtml::listData($data, 'id', 'name');
        return $list;
    }

    public static function getPaidHtml($data, $withDateEnd = false, $withAddLink = false, $icon = false)
    {
        $content = '';
        $htmlArray = $issetPaids = array();

        # применённые платные услуги
        if (isset($data->paids)) {
            foreach ($data->paids as $apartmentPaid) {
                if (isset($apartmentPaid->paidService) && strtotime($apartmentPaid->date_end) > time()) {
                    $issetPaids[$apartmentPaid->paidService->id] = $apartmentPaid;
                }
            }
        }

        # все платные услуги
        $allPaidServices = PaidServices::model()->findAll('type = :type AND active = 1', array(':type' => PaidServices::TYPE_FOR_AD));
        if ($allPaidServices) {
            foreach ($allPaidServices as $service) {
                if (!Yii::app()->user->checkAccess('backend_access')) { # пользователь
                    if (array_key_exists($service->id, $issetPaids)) {
                        $html = '<div class="paid_row">' . CHtml::link(
                                $icon ? $service->getImageIcon(tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end) : $service->name,
                                array('/paidservices/main/index',
                                    'id' => $data->id,
                                    'paid_id' => $service->id,
                                ),
                                array('class' => 'fancy mgp-open-ajax'));
                        $html .= $withDateEnd ? '<span class="valid_till"> (' . tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end . ')</span>' : '';
                        $html .= '</div>';
                    } else {
                        $html = '<div class="paid_row_no"><span class="boldText">' . CHtml::link(
                                $icon ? $service->getImageIcon() : $service->name,
                                array('/paidservices/main/index',
                                    'id' => $data->id,
                                    'paid_id' => $service->id,
                                ),
                                array('class' => 'fancy mgp-open-ajax')) . '</span>';
                        $html .= '</div>';
                    }

                    if (isset($html) && $html) {
                        $htmlArray[] = $html;
                        unset($html);
                    }
                } else { # администратор
                    if (array_key_exists($service->id, $issetPaids) && $withDateEnd) {
                        $html = '<div class="paid_row"><span class="boldText">' . $service->name . '</span>';
                        $html .= $withDateEnd ? '<span class="valid_till"> (' . tc('is valid till') . ' ' . $issetPaids[$service->id]->date_end . ')</span>' : '';
                        $html .= '</div>';
                    }

                    if (isset($html) && $html) {
                        $htmlArray[] = $html;
                        unset($html);
                    }
                }
            }
        }

        if (count($htmlArray) > 0) {
            $content = implode('', $htmlArray);
        } else {
            $content = '<div class="paid_row">' . tc('No') . '</div>';
        }

        if (Yii::app()->user->checkAccess('backend_access') && $withAddLink) {
            $addUrl = Yii::app()->createUrl('/paidservices/backend/main/addPaid', array(
                'id' => $data->id,
                'withDate' => (int)$withDateEnd,
            ));

            $content .= CHtml::link(tc('Add'), $addUrl, array(
                'class' => 'tempModal boldText',
                'title' => tc('Apply a paid service to the listing')
            ));
        }

        return CHtml::tag('div', array('id' => 'paid_row_el_' . $data->id), $content);
    }
}