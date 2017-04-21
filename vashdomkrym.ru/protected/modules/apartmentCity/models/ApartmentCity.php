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

class ApartmentCity extends ParentModel {
	private static $_activeCity;
	private static $_allCity;
	public $multy;

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_MODERATION = 2;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_city}}';
	}

	public function rules()	{
		return array(
			array('name', 'i18nRequired', 'except'=>'multiply'),
			array('multy', 'required', 'on'=>'multiply'),
			array('sorter', 'numerical', 'integerOnly'=>true),
			array('name', 'i18nLength', 'max'=>255),
			array('id, sorter, date_updated', 'safe', 'on'=>'search'),
			array('active', 'safe'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

	public function i18nFields(){
		return array(
			'name' => 'varchar(255) not null',
		);
	}

	public function getName(){
		return $this->getStrByLang('name');
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'name' => tt('Name'),
			'multy' => tt('Name'),
			'sorter' => 'Sorter',
			'active' => tc('Status'),
			'date_updated' => 'Date Updated',
		);
	}

	public function search(){
		$criteria=new CDbCriteria;

		$tmp = 'name_'.Yii::app()->language;
		$criteria->compare($tmp, $this->$tmp, true);
		$criteria->compare('t.active', $this->active);
		$criteria->order = 'sorter ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public function beforeSave(){
		if($this->isNewRecord){
			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter+1;
		}

		return parent::beforeSave();
	}

	public static function getActiveCity(){
		if(self::$_activeCity === null){
			$ownerActiveCond = '';

			if (param('useUserads'))
				$ownerActiveCond = ' AND ap.owner_active = '.Apartment::STATUS_ACTIVE.' ';

			$sql = 'SELECT ac.name_'.Yii::app()->language.' AS name, ac.id AS id
					FROM {{apartment}} ap, {{apartment_city}} ac
					WHERE ac.id = ap.city_id AND ac.active=1
					AND ap.price_type IN ('.implode(',', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true))).')
					AND ap.active = '.Apartment::STATUS_ACTIVE.' '.$ownerActiveCond.'
					ORDER BY ac.sorter';

			$results = Yii::app()->db->createCommand($sql)->queryAll();

			self::$_activeCity = CHtml::listData($results, 'id', 'name');
		}
		return self::$_activeCity;
	}

	public static function getAllCity(){
		if(self::$_allCity === null){
			$sql = 'SELECT name_'.Yii::app()->language.' AS name, id
					FROM {{apartment_city}} WHERE active = 1
					ORDER BY sorter';

			$results = Yii::app()->db->createCommand($sql)->queryAll();

			self::$_allCity = CHtml::listData($results, 'id', 'name');
		}
		return self::$_allCity;
	}

	public function beforeDelete(){
		if($this->model()->count() <= 1){
			return false;
		}

		$sql = "UPDATE {{apartment}} SET city_id=0, active=0 WHERE city_id=".$this->id;
		Yii::app()->db->createCommand($sql)->execute();

		return parent::beforeDelete();
	}

	public static function getModerationStatusArray($withAll = false)
	{
		$status = array();
		if ($withAll) {
			$status[''] = tt('All', 'common');
		}

		$status[self::STATUS_INACTIVE] = CHtml::encode(tt('Inactive', 'common'));
		$status[self::STATUS_ACTIVE] = CHtml::encode(tt('Active', 'common'));
		$status[self::STATUS_MODERATION] = CHtml::encode(tt('Awaiting moderation', 'common'));

		return $status;
	}

	public static function getAvalaibleStatusArray() {
		$statusesArr = self::getModerationStatusArray();
		if (!param('allowCustomCities', 0)) {
			if (array_key_exists(self::STATUS_MODERATION, $statusesArr))
				unset($statusesArr[self::STATUS_MODERATION]);
		}

		return $statusesArr;
	}

	public static function getCountModeration()
	{
		$sql = "SELECT COUNT(id) FROM {{apartment_city}} WHERE active=" . self::STATUS_MODERATION;
		return (int)Yii::app()->db->createCommand($sql)->queryScalar();
	}

	public static function getCityArray($with_all = false, $all = 0) {
		$cityArr = array();

		switch ($all){
			case 2:
				$active_str = 'active = '.ApartmentCity::STATUS_ACTIVE.' OR active = '.ApartmentCity::STATUS_MODERATION;
				break;
			case 1:
				$active_str = "1";
				break;
			default:
				$active_str = 'active = '.ApartmentCity::STATUS_ACTIVE.' ';
		}

		$name = ($all == ApartmentCity::STATUS_MODERATION) ? 'CONCAT(name_'.Yii::app()->language.', CASE WHEN active = 2 THEN "'.' ('.tt('Awaiting moderation', 'common').')'.'" ELSE "" END )' :
			'name_'.Yii::app()->language.'';


		$sql = 'SELECT id, '.$name.' AS name FROM {{apartment_city}} WHERE '.$active_str.' ORDER BY sorter ASC';

		$res = Yii::app()->db->createCommand($sql)->queryAll(true);

		$res = CHtml::listData($res, 'id', 'name');

		if ($with_all) {
			$res = CArray::merge(array(0 => tt('All city', 'apartments')), $res);
		}
		return $res;
	}
}