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

class MainController extends ModuleUserController {
	public $roomsCountMin;
	public $roomsCountMax;
	public $price;
	public $priceSlider = array();
	public $sApId;
    public $landSquare;
	public $term;
    public $bStart;
    public $bEnd;

	public function actionIndex(){
        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = ' . Apartment::STATUS_ACTIVE);
		if(param('useUserads')) {
			$criteria->addCondition('owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			), false, true);
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			));
		}
	}

	public function getExistRooms(){
		return Apartment::getExistsRooms();
	}

	public function actionMainsearch($rss = null){
        $countAjax = Yii::app()->request->getParam('countAjax');

        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		if(Yii::app()->request->getParam('currency')) {
			setCurrency();
			$this->redirect(array('mainsearch'));
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.active = ' . Apartment::STATUS_ACTIVE);
		$criteria->addCondition('t.deleted = 0');
		if(param('useUserads')) {
			$criteria->addCondition('t.owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		$criteria->addInCondition('t.type', HApartment::availableApTypesIds());
		$criteria->addInCondition('t.price_type', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true)));

		$this->sApId = (int) Yii::app()->request->getParam('sApId');
		if ($this->sApId) {
			$criteria->addCondition('id = :sApId');
			$criteria->params[':sApId'] = $this->sApId;

			$apCount = Apartment::model()->count($criteria);
            if($countAjax && Yii::app()->request->isAjaxRequest){
                $this->echoAjaxCount($apCount);
            }

			if ($apCount) {
				$apartmentModel = Apartment::model()->findByPk($this->sApId);
				Yii::app()->controller->redirect($apartmentModel->getUrl());
				Yii::app()->end();
			}
		}

		// rooms
		if(issetModule('selecttoslider') && param('useRoomSlider') == 1) {
			$roomsMin = Yii::app()->request->getParam('room_min');
			$roomsMax = Yii::app()->request->getParam('room_max');

			if($roomsMin || $roomsMax) {
				$criteria->addCondition('num_of_rooms >= :roomsMin AND num_of_rooms <= :roomsMax');
				$criteria->params[':roomsMin'] = $roomsMin;
				$criteria->params[':roomsMax'] = $roomsMax;

				$this->roomsCountMin = $roomsMin;
				$this->roomsCountMax = $roomsMax;
			}
		} else {
			$rooms = Yii::app()->request->getParam('rooms');
			if($rooms) {
				if($rooms == 4) {
					$criteria->addCondition('num_of_rooms >= :rooms');
				} else {
					$criteria->addCondition('num_of_rooms = :rooms');
				}
				$criteria->params[':rooms'] = $rooms;

				$this->roomsCount = $rooms;
			}
		}

        $this->bStart = Yii::app()->request->getParam('b_start');
        $this->bEnd = Yii::app()->request->getParam('b_end');
        if($this->bStart){
            $dateStart = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($this->bStart, Booking::getYiiDateFormat()));
            if($this->bEnd){
                $dateEnd = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($this->bEnd, Booking::getYiiDateFormat()));
            }else{
                $dateEnd = $dateStart;
            }

            if($dateStart && $dateEnd){
                $criteria->addCondition('t.id NOT IN (
                    SELECT DISTINCT b.apartment_id
                        FROM {{booking_calendar}} AS b
                        WHERE b.date_start BETWEEN :b_start AND :b_end
                            OR :b_start BETWEEN b.date_start AND b.date_end
                )');
                $criteria->params['b_start'] = $dateStart;
                $criteria->params['b_end'] = $dateEnd;
            }
        }

		// floor
		$floorMin = Yii::app()->request->getParam('floor_min');
		$floorMax = Yii::app()->request->getParam('floor_max');

		if($floorMin || $floorMax) {
			if ($floorMin) {
				$criteria->addCondition('floor >= :floorMin');
				$criteria->params[':floorMin'] = $floorMin;
			}
			if ($floorMax) {
				$criteria->addCondition('floor <= :floorMax');
				$criteria->params[':floorMax'] = $floorMax;
			}
			
			$this->floorCountMin = $floorMin;
			$this->floorCountMax = $floorMax;
		}


		$squareMin = Yii::app()->request->getParam('square_min');
		$squareMax = Yii::app()->request->getParam('square_max');

		if($squareMin || $squareMax) {
			if ($squareMin) {
				$criteria->addCondition('square >= :squareMin');
				$criteria->params[':squareMin'] = $squareMin;
			}
			if ($squareMax) {
				$criteria->addCondition('square <= :squareMax');
				$criteria->params[':squareMax'] = $squareMax;
			}
			
			$this->squareCountMin = $squareMin;
			$this->squareCountMax = $squareMax;
		}

		$landSquare = Yii::app()->request->getParam('land_square');
		if($landSquare) {
			$criteria->addCondition('land_square <= :land_square');
			$criteria->params[':land_square'] = $landSquare;

			$this->landSquare = $landSquare;
		}

		$this->selectedCity = Yii::app()->request->getParam('city', array());
		if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
			$this->selectedCity = array();
		}

		$this->wp = Yii::app()->request->getParam('wp');
		if($this->wp){
			$criteria->addCondition('count_img > 0');
		}

		$this->ot = Yii::app()->request->getParam('ot');
		if($this->ot){
			$criteria->join = 'INNER JOIN {{users}} AS u ON u.id = t.owner_id';
			if($this->ot == User::TYPE_PRIVATE_PERSON){
				$ownerTypes = array(
					User::TYPE_PRIVATE_PERSON,
					User::TYPE_ADMIN
				);
			}
			if($this->ot == User::TYPE_AGENCY){
				$ownerTypes = array(
					User::TYPE_AGENT,
					User::TYPE_AGENCY
				);
			}
			if (isset($ownerTypes) && $ownerTypes)
				$criteria->compare('u.type', $ownerTypes);
		}

		if (is_array($this->selectedCity) && !empty($this->selectedCity))
			$this->selectedCity = array_map("intval", $this->selectedCity);
		elseif (is_numeric($this->selectedCity) && !empty($this->selectedCity))
			$this->selectedCity = (int) $this->selectedCity;
		
        if (issetModule('location')) {
			$country = (int) Yii::app()->request->getParam('country');
			if($country) {
				$this->selectedCountry = $country;
				$criteria->compare('loc_country', $country);
			}

			$region = (int) Yii::app()->request->getParam('region');
			if($region) {
				$this->selectedRegion = $region;
				$criteria->compare('loc_region', $region);
			}

            if($this->selectedCity) {
                $criteria->compare('t.loc_city', $this->selectedCity);
            }
		}
		else {
			if($this->selectedCity) {
				$criteria->compare('t.city_id', $this->selectedCity);
			}
		}

		if (issetModule('metroStations')) {
			$this->selectedMetroStations = Yii::app()->request->getParam('metro', array());
			if(isset($this->selectedMetroStations[0]) && $this->selectedMetroStations[0] == 0){
				$this->selectedMetroStations = array();
			}

			if (!empty($this->selectedMetroStations)) {
				if (is_array($this->selectedMetroStations))
					$this->selectedMetroStations = array_map("intval", $this->selectedMetroStations);
				else
					$this->selectedMetroStations = (int) $this->selectedMetroStations;
				
				if ($this->selectedMetroStations) {
					if (!is_array($this->selectedMetroStations))
						$this->selectedMetroStations = array($this->selectedMetroStations);
					
					$sqlMetro = 'SELECT DISTINCT apartment_id FROM {{apartment_metro_stations}} WHERE metro_id IN ('.implode(',', $this->selectedMetroStations).')';
					$criteria->addCondition('(t.id IN('.$sqlMetro.'))');
				}
			}
		}

		$this->objType = (int) Yii::app()->request->getParam('objType');
		if($this->objType) {
			$criteria->compare('obj_type_id', $this->objType);
		}

		// type
		$this->apType = (int) Yii::app()->request->getParam('apType');

		//
		//$useSeasonalPrices = (issetModule('seasonalprices') && $this->apType && (in_array($this->apType, array(Apartment::PRICE_PER_HOUR, Apartment::PRICE_PER_DAY, Apartment::PRICE_PER_WEEK, Apartment::PRICE_PER_MONTH)))) ? true : false;
		$useSeasonalPrices = issetModule('seasonalprices');

		if($this->apType) {
			if ($useSeasonalPrices &&
			in_array($this->apType, array(
				Apartment::PRICE_PER_HOUR,
				Apartment::PRICE_PER_DAY,
				Apartment::PRICE_PER_WEEK,
				Apartment::PRICE_PER_MONTH)
			)) {
				
				$criteria->addCondition('(t.id IN(SELECT DISTINCT(apartment_id) FROM {{seasonal_prices}} WHERE price_type = '.$this->apType.'))');
			}
			else {
				$criteria->addCondition('t.price_type = :apType');
				$criteria->params[':apType'] = $this->apType;
			}
		}

		$type = (int) Yii::app()->request->getParam('type');
		if($type) {
			$criteria->addCondition('type = :type');
			$criteria->params[':type'] = $type;
		}

    	// price
		$priceMin = Yii::app()->request->getParam("price_min");
		$priceMax = Yii::app()->request->getParam("price_max");

		if($priceMin || $priceMax) {
			$this->priceSlider['min'] = $priceMin;
			$this->priceSlider['max'] = $priceMax;

			if(issetModule('currency')){
				$priceMin = floor(Currency::convertToDefault($priceMin));
				$priceMax = ceil(Currency::convertToDefault($priceMax));
			}
			else {
				$priceMin = (int) $priceMin;
				$priceMax = (int) $priceMax;
			}

			if($priceMin && $priceMax){
				if ($useSeasonalPrices) {
					// for non rent items
					$or = '
				(
					t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
					AND t.price >= :priceMin AND t.price <= :priceMax
				)';

					$criteria->addCondition(
						'
					(t.id IN(SELECT apartment_id FROM {{seasonal_prices}} WHERE price >= '.$priceMin.' AND price <= '.$priceMax.')
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
					);
					unset($or);
				}
				else {
					$criteria->addCondition('(price >= :priceMin AND price <= :priceMax) OR (is_price_poa = 1)');
				}

				$criteria->params[':priceMin'] = $priceMin;
				$criteria->params[':priceMax'] = $priceMax;

			}elseif($priceMin){
				if ($useSeasonalPrices) {
					// for non rent items
					$or = '
					(
						t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
						AND t.price >= :priceMin
					)';

					$criteria->addCondition(
						'
					(t.id IN (SELECT apartment_id FROM {{seasonal_prices}} WHERE price >= :priceMin)
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
					);
					unset($or);
				}
				else {
					$criteria->addCondition('price >= :priceMin OR is_price_poa = 1');
				}
				$criteria->params[':priceMin'] = $priceMin;
			}elseif($priceMax){
				if ($useSeasonalPrices) {
					// for non rent items
					$or = '
					(
						t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
						AND t.price <= :priceMax
					)';

					$criteria->addCondition(
						'
					(t.id IN (SELECT apartment_id FROM {{seasonal_prices}} WHERE price <= :priceMax)
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
					);
					unset($or);
				}
				else {
					$criteria->addCondition('price <= :priceMax OR is_price_poa = 1');
				}
				$criteria->params[':priceMax'] = $priceMax;
			}

		}

		// ключевые слова
		$term = Yii::app()->request->getParam('term');
		//$doTermSearch = Yii::app()->request->getParam('do-term-search');

		if ($term /*&& $doTermSearch == 1*/) {
			$term = utf8_substr($term, 0, 50);
			$term = cleanPostData($term);

			if ($term && utf8_strlen($term) >= $this->minLengthSearch) {
				$this->term = $term;

				$words = explode(' ', $term);
				foreach($words as $key=>$value){
					if(mb_strlen($value, "UTF-8") < $this->minLengthSearch ){
						unset($words[$key]);
					}
				}

				if (count($words) > 1) {
                    $cleanWords = array();
                    foreach($words as $word){
                        if(utf8_strlen($word) >= $this->minLengthSearch){
                            $cleanWords[] = $word;
                        }
                    }

					$searchString = '+'.implode('* +', $cleanWords).'* '; # https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html

					$sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("'.$searchString.'" IN BOOLEAN MODE)';
				}
				else {
					$sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("*'.$term.'*" IN BOOLEAN MODE)';
				}
				
				$criteria->addCondition('(t.id IN('.$sql.'))');
			}
		}

		// поиск объявлений владельца
		$this->userListingId = Yii::app()->request->getParam('userListingId');
		if($this->userListingId) {
			$criteria->addCondition('owner_id = :userListingId');
			$criteria->params[':userListingId'] = $this->userListingId;
		}

		$filterName = null;
		// Поиск по справочникам - клик в просмотре профиля анкеты
		if(param('useReferenceLinkInView')) {
			if(Yii::app()->request->getQuery('serviceId', false)) {
				$serviceId = Yii::app()->request->getQuery('serviceId', false);
				if($serviceId) {
					$serviceIdArray = explode('-', $serviceId);
					if(is_array($serviceIdArray) && count($serviceIdArray) > 0) {
						Yii::app()->getModule('referencevalues');
						$value = (int) $serviceIdArray[0];

						$sql = 'SELECT DISTINCT apartment_id FROM {{apartment_reference}} WHERE reference_value_id = ' . $value;						
						$criteria->addCondition('(t.id IN('.$sql.'))');						

						$sql = 'SELECT title_' . Yii::app()->language . ' FROM {{apartment_reference_values}} WHERE id = ' . $value;
						$filterName = Yii::app()->db->cache(param('cachingTime', 1209600), ReferenceValues::getDependency())->createCommand($sql)->queryScalar();

						if($filterName) {
							$filterName = CHtml::encode($filterName);
						}
					}
				}
			}
		}

		if(issetModule('formeditor')){
			$newFieldsAll = FormDesigner::getNewFields();
			$apps = $appsLike = array();
			foreach($newFieldsAll as $field){
				if($field->type == FormDesigner::TYPE_MULTY) {
					$value = Yii::app()->request->getParam($field->field);
					if(!$value || !is_array($value))
						continue;

					$fieldString = $field->field;
					$this->newFields[$fieldString] = $value;
					foreach($value as $val) {
						if ($field->compare_type == FormDesigner::COMPARE_LIKE) {
							$appsLike[] =  CHtml::listData(Reference::model()->findAllByAttributes(array('reference_value_id'=>$val), array('select'=>'apartment_id')),  'apartment_id', 'apartment_id');
						}
						else {
							$apps[] = CHtml::listData(Reference::model()->findAllByAttributes(array('reference_value_id'=>$val), array('select'=>'apartment_id')),  'apartment_id', 'apartment_id');
						}
					}
					
					if($appsLike) {
						$appsLike = (count($appsLike) > 1) ? call_user_func_array('array_merge', $appsLike) : $appsLike[0];
						$criteria->addInCondition('t.id', $appsLike);
					}
				} 
				else {
					$value = CHtml::encode(Yii::app()->request->getParam($field->field));
					if(!$value){
						continue;
					}
					$fieldString = $field->field;

					$this->newFields[$fieldString] = $value;

					switch($field->compare_type){
						case FormDesigner::COMPARE_EQUAL:
							$criteria->compare($fieldString, $value);
							break;

						case FormDesigner::COMPARE_LIKE:
							$criteria->compare($fieldString, $value, true);
							break;

						case FormDesigner::COMPARE_FROM:
							$value = intval($value);
							$criteria->compare($fieldString, ">={$value}");
							break;

						case FormDesigner::COMPARE_TO:
							$value = intval($value);
							$criteria->compare($fieldString, "<={$value}");
							break;
					}
				}
			}
			if($apps) {
				$apps = (count($apps) > 1) ? call_user_func_array('array_intersect', $apps) : $apps[0];
				$criteria->addInCondition('t.id', $apps);
			}
		}

		if($rss && issetModule('rss')) {
			$this->widget('application.modules.rss.components.RssWidget', array(
				'criteria' => $criteria,
			));
		}

		// find count
		$apCount = Apartment::model()->count($criteria);

        if($countAjax && Yii::app()->request->isAjaxRequest){
            $this->echoAjaxCount($apCount);
        }

        $searchParams = $_GET;
        if(isset($searchParams['is_ajax'])){
            unset($searchParams['is_ajax']);
        }
        Yii::app()->user->setState('searchUrl', Yii::app()->createUrl('/search', $searchParams));
        unset($searchParams);

		if(Yii::app()->request->isAjaxRequest) {
//			$modeListShow = User::getModeListShow();
//			if ($modeListShow == 'table') {
//				# нужны скрипты и стили, поэтому processOutput установлен в true только для table
//				$this->renderPartial('index', array(
//					'criteria' => $criteria,
//					'apCount' => $apCount,
//					'filterName' => $filterName,
//				), false, true);
//			}
//			else {
				$this->renderPartial('index', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'filterName' => $filterName,
				));
//			}
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => $apCount,
				'filterName' => $filterName,
			));
		}
	}

    public function echoAjaxCount($apCount){
//        if($apCount > 0){
//            $buttonLabel = Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount));
//        } else {
//            $buttonLabel = tc('Search');
//        }
        echo CJSON::encode(array(
            'count' => $apCount,
            'string' => Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount)),
        ));
        Yii::app()->end();
    }

    public function actionLoadForm(){
        if(!Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $this->objType = CHtml::encode(Yii::app()->request->getParam('obj_type_id'));
        $isInner = CHtml::encode(Yii::app()->request->getParam('is_inner'));

        $roomsMin = CHtml::encode(Yii::app()->request->getParam('room_min'));
        $roomsMax = CHtml::encode(Yii::app()->request->getParam('room_max'));
        if($roomsMin || $roomsMax) {
            $this->roomsCountMin = $roomsMin;
            $this->roomsCountMax = $roomsMax;
        }

        $this->sApId = CHtml::encode(Yii::app()->request->getParam('sApId'));

        $this->bStart = CHtml::encode(Yii::app()->request->getParam('b_start'));
        $this->bEnd = CHtml::encode(Yii::app()->request->getParam('b_end'));

        $floorMin = CHtml::encode(Yii::app()->request->getParam('floor_min'));
        $floorMax = CHtml::encode(Yii::app()->request->getParam('floor_max'));
        if($floorMin || $floorMax) {
            $this->floorCountMin = $floorMin;
            $this->floorCountMax = $floorMax;
        }

        $this->wp = CHtml::encode(Yii::app()->request->getParam('wp'));
        $this->ot = CHtml::encode(Yii::app()->request->getParam('ot'));

		$squareMin = CHtml::encode(Yii::app()->request->getParam('square_min'));
		$squareMax = CHtml::encode(Yii::app()->request->getParam('square_max'));
		if($squareMin || $squareMax) {
			$this->squareCountMin = $squareMin;
			$this->squareCountMax = $squareMax;
		}

        $this->selectedCity = Yii::app()->request->getParam('city', array());
        if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
            $this->selectedCity = array();
        }

        if (issetModule('location')) {
            $country = CHtml::encode(Yii::app()->request->getParam('country'));
            if($country) {
                $this->selectedCountry = $country;
            }

            $region = CHtml::encode(Yii::app()->request->getParam('region'));
            if($region) {
                $this->selectedRegion = $region;
            }
        }

		if (issetModule('metroStations')) {
			$this->selectedMetroStations = Yii::app()->request->getParam('metro', array());
			if(isset($this->selectedMetroStations[0]) && $this->selectedMetroStations[0] == 0){
				$this->selectedMetroStations = array();
			}
		}

        $this->objType = CHtml::encode(Yii::app()->request->getParam('objType'));
        $this->apType = CHtml::encode(Yii::app()->request->getParam('apType'));


		$this->term = CHtml::encode(Yii::app()->request->getParam('term'));

        if(issetModule('formeditor')){
            $newFieldsAll = FormDesigner::getNewFields();
            foreach($newFieldsAll as $field){
                $value = CHtml::encode(Yii::app()->request->getParam($field->field));
                if(!$value){
                    continue;
                }
                $fieldString = $field->field;
                $this->newFields[$fieldString] = $value;
            }
        }

        $compact = CHtml::encode(Yii::app()->request->getParam('compact', 0));

        HAjax::jsonOk('', array(
            'html' => $this->renderPartial('//site/_search_form', array('isInner' => $isInner, 'compact' => $compact), true),
            'sliderRangeFields' => SearchForm::getSliderRangeFields(),
            'cityField' => SearchForm::getCityField(),
            'countFiled' => SearchForm::getCountFiled(),
            'compact' => $compact,
        ));
    }

}