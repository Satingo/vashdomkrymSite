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

class BlockIp extends ParentModel {
	public $deleteIpAfterDays;

	private static $_cache;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{block_ip}}';
	}
	
	public function behaviors() {
		$arr = array();
		$arr['ERememberFiltersBehavior'] = array(
			'class' => 'application.components.behaviors.ERememberFiltersBehavior',
			'defaults' => array(),
			'defaultStickOnClear' => false
		);
		$arr['AutoTimestampBehavior'] = array(
			'class' => 'zii.behaviors.CTimestampBehavior',
			'createAttribute' => 'date_created',
			'updateAttribute' => 'date_updated',
		);
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}

	public function rules() {
		return array(
			array('ip', 'required', 'except' => 'upd_settings_day'),
			array('ip, ip_long', 'length', 'max' => 60),

			array('deleteIpAfterDays', 'required', 'on' => 'upd_settings_day'),
			array('deleteIpAfterDays', 'numerical', 'integerOnly' => true, 'min' => 1, 'on' => 'upd_settings_day'),
			array('deleteIpAfterDays', 'length', 'max' => 5, 'on' => 'upd_settings_day'),

			array('ip, ip_long', 'safe', 'on' => 'search'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => tt('ID', 'apartments'),
			'ip' => tt('IP', 'blockIp'),
			'ip_long' => tt('Ip_long', 'blockIp'),
			'date_updated' => tc('Last updated on'),
			'deleteIpAfterDays' => tt('Added IP are automatically deleted'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('ip', $this->ip, true);
		$criteria->compare('ip_long', $this->ip_long, true);

		return new CustomActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 50),
			),
		));
	}

	public function beforeSave() {
		if ($this->ip)
			$this->ip_long = ip2long($this->ip);

		return parent::beforeSave();
	}

	public static function checkAllowIp($ip = null) {
		if(!isset(self::$_cache)){
			self::setCache();
		}

		if ($ip) {
			//$ipLong = ip2long($ip);
			$ipLong = $ip;

			if (self::$_cache && isset(self::$_cache[$ipLong]))
				return false;
		}

		return true;
	}

	private static function setCache() {
		$blockIps = BlockIp::model()
			->cache(param('cachingTime', 1209600), self::getDependency())
			->findAll();

		if ($blockIps) {
			foreach($blockIps as $item) {
				self::$_cache[$item->ip_long] = $item->ip;
			}
		}
	}

	public static function displayUserIP($model) {
		$return = '-';
		if (isset($model->user_ip) && $model->user_ip) {
			$return = $model->user_ip;
		}
		return $return;
	}

	public static function getDependency(){
		return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{block_ip}}');
	}
}