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

class Service extends ParentModel {
	const SERVICE_ID = 1;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{service}}';
	}

	public function rules() {
		return array(
			array('page, is_offline, allow_ip', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'page' => tt('Page', 'service'),
			'is_offline' => tt('Closed_maintenance', 'service'),
			'allow_ip' => tt('Allow_ip', 'service'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare('page', $this->page, true);
		$criteria->compare('is_offline', $this->is_offline, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'date_created DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}
}