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

class InfoPages extends ParentModel {
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	const MAIN_PAGE_ID = 1;
	const LICENCE_PAGE_ID = 4;
	const PRIVATE_POLICY_PAGE_ID = 5;

    const POSITION_BOTTOM = 1;
    const POSITION_TOP = 2;
	
	public $seasonalPricesIds = array();

    public static function getPositionList(){
        return array(
            self::POSITION_BOTTOM => tt('Bottom', 'infopages'),
            self::POSITION_TOP => tt('Top', 'infopages'),
        );
    }

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{infopages}}';
	}

	public function rules() {
		return array(
			array('title', 'i18nRequired'),
			array('title', 'i18nLength', 'max' => 255),
			array('active, widget, widget_data, widget_position', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
			array('active', 'safe', 'on' => 'search'),
		);
	}

	public function relations(){
		return array(
			'menuPage' => array(self::HAS_MANY, 'Menu', 'pageId'),
			'menuPageOne' => array(self::HAS_ONE, 'Menu', 'pageId'),
		);
	}

	public function i18nFields(){
		return array(
			'title' => 'varchar(255) not null',
			'body' => 'text not null',
		);
	}

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'body'
		);
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_created',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'active' => tc('Status'),
			'title' => tt('Page title'),
			'body' => tt('Page body'),
			'date_created' => tt('Creation date'),
			'widget' => tt('Widget', 'infopages'),
			'widget_position' => tt("Widget's position", 'infopages'),
		);
	}

	public function getUrl($absolute = true) {
		$baseUrl = $absolute ? Yii::app()->getBaseUrl(true) : Yii::app()->getBaseUrl();
		$method = $absolute ? 'createAbsoluteUrl' : 'createUrl';

		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'InfoPages');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->direct_url){
					return $baseUrl . '/' . $seo->$field . ( param('urlExtension') ? '.html' : '' );
				}
				return Yii::app()->{$method}('/infopages/main/view', array(
					'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
				));
			}
		}

		return Yii::app()->{$method}('/infopages/main/view', array(
			'id' => $this->id,
		));
	}

	public static function getUrlById($id) {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($id, 'InfoPages');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field){
					if($seo->direct_url){
						return Yii::app()->getBaseUrl(true) . '/' . $seo->$field . ( param('urlExtension') ? '.html' : '' );
					}
					return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
						'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
					));
				}
			}
		}

		return Yii::app()->createAbsoluteUrl('/infopages/main/view', array(
			'id' => $id,
		));
	}

	public function search() {
		$criteria = new CDbCriteria;

        $titleField = 'title_'.Yii::app()->language;
		$criteria->compare($titleField, $this->$titleField, true);
        $bodyField = 'body_'.Yii::app()->language;
		$criteria->compare($bodyField, $this->$bodyField, true);

		$criteria->compare($this->getTableAlias().'.active', $this->active, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function getWidgetOptions($widget = null){
		$arrWidgets =  array(
			'' => tc('No'),
			'apartments' => tc('Listing'),
			'entries' => tt('Entries', 'entries'),
			'specialoffers' => tc('Special offers'),
			'randomapartments' => tc('Listing (random)'),
			'viewallonmap' => tc('Search for listings on the map'),
			'contactform' => tc('The form of the section "Contact Us"'),
		);

		if ($widget && array_key_exists($widget, $arrWidgets))
			return $arrWidgets[$widget];

		return $arrWidgets;
	}

	public static function getInfoPagesAddList() {
		$return = array();
		
		$criteria = new CDbCriteria;
		$criteria->addCondition('active = '.self::STATUS_ACTIVE);
		$criteria->order = 'id DESC';
		
		$result = InfoPages::model()->findAll($criteria);
		if ($result) {
			foreach($result as $item) {
				$return[$item->id] = $item->getStrByLang('title');
			}
		}

		return $return;
	}

	public function getTitle(){
		return CHtml::encode($this->getStrByLang('title'));
	}

	public function getBody(){
		return $this->getStrByLang('body');
	}

	public function beforeSave(){
        if($this->widget == 'apartments' && isset($_POST['filter'])){
            $this->widget_data = CJSON::encode($_POST['filter']);
        }
		if($this->widget == 'entries' && isset($_POST['filterEntries'])){
            $this->widget_data = CJSON::encode($_POST['filterEntries']);
        }

		return parent::beforeSave();
	}


	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		return parent::afterSave();
	}

	public function beforeDelete() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "InfoPages"';
			Yii::app()->db->createCommand($sql)->execute();
		}

		return parent::beforeDelete();
	}

	private $_filter;
	
	public function getCriteriaForAdList(){
		$criteria = new CDbCriteria();
		if($this->widget_data){
			$this->_filter = CJSON::decode($this->widget_data);

			if(issetModule('location')){
				$this->setForCriteria($criteria, 'country_id', 'loc_country');
				$this->setForCriteria($criteria, 'region_id', 'loc_region');
				$this->setForCriteria($criteria, 'city_id', 'loc_city');

				if(isset($this->_filter['country_id']) && $this->_filter['country_id'])
					Yii::app()->controller->selectedCountry = $this->_filter['country_id'];
				if(isset($this->_filter['region_id']) && $this->_filter['region_id'])
					Yii::app()->controller->selectedRegion = $this->_filter['region_id'];
				if(isset($this->_filter['city_id']) && $this->_filter['city_id'])
					Yii::app()->controller->selectedCity = $this->_filter['city_id'];
			} else {
				$this->setForCriteria($criteria, 'city_id', 'city_id');

				if(isset($this->_filter['city_id']) && $this->_filter['city_id'])
					Yii::app()->controller->selectedCity = $this->_filter['city_id'];
			}
			
			if (issetModule('metroStations')) {
				$this->setForCriteria($criteria, 'metro', 'metro');
				
				if(isset($this->_filter['metro']) && $this->_filter['metro'])
					Yii::app()->controller->selectedMetroStations = $this->_filter['metro'];
			}

			$this->setForCriteria($criteria, 'type', 't.price_type');
			$this->setForCriteria($criteria, 'obj_type_id', 't.obj_type_id');
			
			if (!(issetModule('selecttoslider') && param('useRoomSlider') == 1))
				$this->setForCriteria($criteria, 'rooms', 't.num_of_rooms');

			$this->setForCriteria($criteria, 'ot', 't.ot');
			
			$this->setForCriteria($criteria, 'wp', 't.count_img');
			
			$this->setForCriteria($criteria, 'square_min', 't.square');
			$this->setForCriteria($criteria, 'square_max', 't.square');
			
			$this->setForCriteria($criteria, 'floor_min', 't.floor');
			$this->setForCriteria($criteria, 'floor_max', 't.floor');

			if(isset($this->_filter['type']) && $this->_filter['type'])
				Yii::app()->controller->apType = $this->_filter['type'];
			if(isset($this->_filter['obj_type_id']) && $this->_filter['obj_type_id'])
				Yii::app()->controller->objType = $this->_filter['obj_type_id'];
			if(isset($this->_filter['rooms']) && $this->_filter['rooms'])
				Yii::app()->controller->roomsCount = $this->_filter['rooms'];
			if(isset($this->_filter['ot']) && $this->_filter['ot'])
				Yii::app()->controller->ot = $this->_filter['ot'];
			if(isset($this->_filter['wp']) && $this->_filter['wp'])
				Yii::app()->controller->wp = $this->_filter['wp'];
			
			if(isset($this->_filter['square_min']) && $this->_filter['square_min'])
				Yii::app()->controller->squareCountMin = $this->_filter['square_min'];
			if(isset($this->_filter['square_max']) && $this->_filter['square_max'])
				Yii::app()->controller->squareCountMax = $this->_filter['square_max'];
			
			if(isset($this->_filter['floor_min']) && $this->_filter['floor_min'])
				Yii::app()->controller->floorCountMin = $this->_filter['floor_min'];
			if(isset($this->_filter['floor_max']) && $this->_filter['floor_max'])
				Yii::app()->controller->floorCountMax = $this->_filter['floor_max'];

			# new fields
			$newFieldsAll = InfoPages::getAddedFields();
			if ($newFieldsAll) {
				foreach($newFieldsAll as $field) {
					$this->setForCriteria($criteria, $field['field'], 't.'.$field['field'], true, $field);

					if(isset($this->_filter[$field['field']]) && $this->_filter[$field['field']])
						Yii::app()->controller->newFields[$field['field']] = $this->_filter[$field['field']];
				}
			}
		}

		//deb($criteria);
		
		$limit = param('countListitng'.User::getModeListShow(), 10);
		Yii::import('application.modules.apartments.helpers.apartmentsHelper');
		$newcriteria = apartmentsHelper::getApartments($limit, 1, 0, $criteria);
		$result = HApartment::findAllWithCache($newcriteria['criteria']);
		
		if ($result) {
			$result = CHtml::listData($result, 'id', 'id');
			
			if ($result && !empty($this->seasonalPricesIds)) {
				$ids = CMap::mergeArray($result, $this->seasonalPricesIds);
				$criteria = new CDbCriteria;
				$criteria->addInCondition('t.id', $ids);
			}
		}
		

		return $criteria;
	}

	private function setForCriteria($criteria, $key, $field, $isNewField = false, $newFieldArr = array()){
		if(isset($this->_filter[$key]) && ($this->_filter[$key] || $key == 'type')){
			if ($isNewField && count($newFieldArr)) {
				switch($newFieldArr['compare_type']){
					case FormDesigner::COMPARE_EQUAL:
						$criteria->compare($field, $this->_filter[$key]);
						break;

					case FormDesigner::COMPARE_LIKE:
						$criteria->compare($field, $this->_filter[$key], true);
						break;

					case FormDesigner::COMPARE_FROM:
						$value = intval($this->_filter[$key]);
						$criteria->compare($field, ">={$value}");
						break;

					case FormDesigner::COMPARE_TO:
						$value = intval($this->_filter[$key]);
						$criteria->compare($field, "<={$value}");
						break;
				}
			}
			else {
				if ($key == 'rooms') {
					if($this->_filter[$key] == 4) {
						$criteria->addCondition($field.' >= '.$this->_filter[$key]);
					} else {
						$criteria->addCondition($field.' = '.$this->_filter[$key]);
					}
				}
				elseif ($key == 'ot') {
					$criteria->join = 'INNER JOIN {{users}} AS u ON u.id = t.owner_id';

					if($this->_filter[$key] == User::TYPE_PRIVATE_PERSON){
						$ownerTypes = array(
							User::TYPE_PRIVATE_PERSON,
							User::TYPE_ADMIN
						);
					}
					if($this->_filter[$key] == User::TYPE_AGENCY){
						$ownerTypes = array(
							User::TYPE_AGENT,
							User::TYPE_AGENCY
						);
					}
					if (isset($ownerTypes) && $ownerTypes)
						$criteria->compare('u.type', $ownerTypes);
				}
				elseif ($key == 'wp') {
					$criteria->addCondition('t.count_img > 0');
				}
				elseif ($key == 'metro') {
					$apartmentIds = MetroStations::getApartmentsListByMetro($this->_filter[$key]);				
					if ($apartmentIds)
						$criteria->addInCondition('t.id', $apartmentIds);
				}
				elseif ($key == 'type') {
					$type = Apartment::convertPriceToType($this->_filter[$key]);
					if ($type) {

						$criteria->compare('t.type', $type);
						if ($type == Apartment::TYPE_RENT) {
							if (issetModule('seasonalprices')) {
								$sql = 'SELECT DISTINCT(apartment_id) FROM {{seasonal_prices}} WHERE price_type = '.(int) $this->_filter[$key];
								$res = Yii::app()->db->createCommand($sql)->queryColumn();
								$this->seasonalPricesIds = ($res) ? ($res) : array();
							}
							else {
								$criteria->addCondition('t.price_type = '.$this->_filter[$key].' OR is_price_poa = 1');
							}
						}
						else {
							$criteria->addCondition('t.price_type = '.$this->_filter[$key].' OR is_price_poa = 1');
						}
					}
				}
				elseif ($key == 'square_min' || $key == 'square_max') {
					if(!empty($this->_filter[$key])) {
						if ($key == 'square_min') {
							$criteria->addCondition('square >= '.$this->_filter[$key]);
						}
						if ($key == 'square_max') {
							$criteria->addCondition('square <= '.$this->_filter[$key]);
						}
					}
				}
				elseif ($key == 'floor_min' || $key == 'floor_max') {
					if(!empty($this->_filter[$key])) {
						if ($key == 'floor_min') {
							$criteria->addCondition('floor >= '.$this->_filter[$key]);
						}
						if ($key == 'floor_max') {
							$criteria->addCondition('floor <= '.$this->_filter[$key]);
						}
					}
				}
				else {
					$criteria->compare($field, $this->_filter[$key]);
				}
			}
		}
	}
	
	private $_filterEntries;
	
	public function getCriteriaForEntriesList() {
		$criteria = new CDbCriteria();
		if($this->widget_data){
			$this->_filterEntries = CJSON::decode($this->widget_data);
			
			$this->setForCriteriaEntries($criteria, 'category_id', 't.category_id');
		}
		
		//deb($criteria); exit;
		
		return $criteria;
	}
	
	private function setForCriteriaEntries($criteria, $key, $field){
		if(isset($this->_filterEntries[$key]) && $this->_filterEntries[$key]){
			$criteria->compare($field, $this->_filterEntries[$key]);
		}
	}

	public static function getAddedFields() {
		$addedFields = null;

		if (issetModule('formdesigner')) {
			$newFieldsAll = FormDesigner::getNewFields();
			if ($newFieldsAll && count($newFieldsAll)) {
				foreach($newFieldsAll as $key => $field){
					$addedFields[$key]['field'] = $field->field;
					$addedFields[$key]['type'] = $field->type;
					$addedFields[$key]['compare_type'] = $field->compare_type;
					$addedFields[$key]['label'] = $field->getStrByLang('label');

					if ($field->type == FormDesigner::TYPE_REFERENCE) {
						$addedFields[$key]['listData'] = FormDesigner::getListByCategoryID($field->reference_id);
					}
				}
			}
		}

		return $addedFields;
	}
}