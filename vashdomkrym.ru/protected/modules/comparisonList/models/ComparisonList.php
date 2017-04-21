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

class ComparisonList extends ParentModel {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{comparison_list}}';
	}

	public function rules(){
		return array(
			array('apartment_id, session_id' , 'required'),
			array('user_id, apartment_id', 'numerical', 'integerOnly' => true),
			array('id, user_id, apartment_id, session_id, date_updated', 'safe', 'on'=>'search'),
		);
	}


	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'user_id' => tс('User'),
			'apartment_id' => tс('Apartment ID'),
			'session_id' => tt('Session_id', 'comparisonList'),
			'date_updated' => tс('Date updated'),
		);
	}

	public function search(){

		$criteria = new CDbCriteria;
		$criteria->compare('id', $this->id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('apartment_id', $this->apartment_id);
		$criteria->compare('session_id', $this->session_id, true);
		$criteria->order = 'ID ASC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'date_updated DESC',
			),
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => null,
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public static function getCountListingsGuest($sessionId = '') {
		if ($sessionId) {
			$sql = 'SELECT COUNT(id) FROM {{comparison_list}}
						WHERE session_id = "'.$sessionId.'"';
			return Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return 0;
	}

	public static function getCountListingsUser($userId = '') {
		if ($userId) {
			$sql = 'SELECT COUNT(id) FROM {{comparison_list}}
						WHERE user_id = "'.$userId.'"';
			return Yii::app()->db->createCommand($sql)->queryScalar();
		}
		return 0;
	}

	public static function getRefCategories() {
		$criteria = new CDbCriteria;
		$criteria->addCondition('type = '.ReferenceCategories::TYPE_STANDARD);
		$criteria->order = 'sorter ASC';

		return ReferenceCategories::model()->findAll($criteria);
	}
}