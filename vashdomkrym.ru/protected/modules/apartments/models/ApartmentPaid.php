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

class ApartmentPaid extends CActiveRecord {
    const STATUS_ACTIVE = 1;
    const STATUS_NO_ACTIVE = 0;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	public function tableName() {
		return '{{apartment_paid}}';
	}

	public function behaviors() {
		$arr = array();
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}

	public function rules() {
		return array(
			array('apartment_id, user_id, paid_id, date_start, date_end', 'required'),
			array('apartment_id, user_id, paid_id, status', 'numerical', 'integerOnly'=>true),
			array('id, apartment_id, user_id, paid_id, date_start, date_end', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'paidService' => array(self::BELONGS_TO, 'PaidServices', 'paid_id')
		);
	}

    public function scopes() {
        return array(
            'active' => array(
                'condition'=>'status='.self::STATUS_ACTIVE
            )
        );
    }

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'apartment_id' => 'Apartment',
			'user_id' => 'User',
			'paid_id' => 'Paid',
			'date_start' => 'Date Start',
			'date_end' => 'Date End',
		);
	}

	public function search() {
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('apartment_id',$this->apartment_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('paid_id',$this->paid_id);
		$criteria->compare('date_start',$this->date_start,true);
		$criteria->compare('date_end',$this->date_end,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}