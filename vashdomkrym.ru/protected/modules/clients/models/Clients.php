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

class Clients extends ParentModel {
	private static $_client_states_arr;

	const STATE_WITH_OUR_HELP = 1;
	const STATE_ACCOMMODATING = 2;
	const STATE_INDEPENDENTLY = 3;
	const STATE_IGNORE = 4;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{clients}}';
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
		$rules = array(
			array('state, first_name, second_name, phone', 'required'),
			array('contract_number, first_name, second_name, middle_name, birthdate, phone, additional_phone', 'length', 'max' => 255),
			array('contract_number, first_name, second_name, middle_name, birthdate, phone, additional_phone, acts, additional_info', 'filter', 'filter' => array(new CHtmlPurifier(), 'purify')),
			array('id, state', 'numerical'),
			array('id, state, contract_number, first_name, second_name, middle_name, birthdate, phone, additional_phone, acts, additional_info, date_created, date_updated', 'safe', 'on' => 'search'),
		);

		return $rules;
	}

	public function attributeLabels() {
		return array(
			'id' => tt('ID', 'clients'),
			'state' => tt('State', 'clients'),
			'contract_number' => tt('Contract_number', 'clients'),
			'second_name' => tt('Second_name', 'clients'),
			'middle_name' => tt('Middle_name', 'clients'),
			'first_name' => tt('First_name', 'clients'),
			'birthdate' => tt('Birthdate', 'clients'),
			'phone' => tt('Phone', 'clients'),
			'additional_phone' => tt('Additional_phone', 'clients'),
			'acts' => tt('Acts', 'clients'),
			'additional_info' => tt('Additional_info', 'clients'),
			'date_created' => tc('Date created'),
			'date_updated' => tc('Date updated'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;
		$criteria->compare($this->getTableAlias().'.id', $this->id);
		$criteria->compare($this->getTableAlias().'.state', $this->state, true);
		$criteria->compare($this->getTableAlias().'.contract_number', $this->contract_number, true);
		$criteria->compare($this->getTableAlias().'.first_name', $this->first_name, true);
		$criteria->compare($this->getTableAlias().'.second_name', $this->second_name, true);
		$criteria->compare($this->getTableAlias().'.middle_name', $this->middle_name, true);
		$criteria->compare($this->getTableAlias().'.birthdate', $this->birthdate, true);
		$criteria->compare($this->getTableAlias().'.phone', $this->phone, true);
		$criteria->compare($this->getTableAlias().'.additional_phone', $this->additional_phone, true);
		$criteria->compare($this->getTableAlias().'.acts', $this->acts, true);
		$criteria->compare($this->getTableAlias().'.additional_info', $this->additional_info, true);
		$criteria->compare($this->getTableAlias().'.date_created', $this->date_created, true);
		$criteria->compare($this->getTableAlias().'.date_updated', $this->date_updated, true);

		$criteria->order = $this->getTableAlias().'.id DESC';

		return new CustomActiveDataProvider($this, array(
			'criteria' => $criteria,
			//'sort'=>array('defaultOrder'=>'sorter'),
			'pagination'=>array(
				'pageSize'=>param('adminPaginationPageSize', 20),
			),
		));
	}

	public static function getDependency(){
		return new CDbCacheDependency('SELECT MAX(date_updated) FROM {{clients}}');
	}

	public static function getClientsStatesArray($withAll = false) {
		$state = array();
		if($withAll){
			$state[''] = Yii::t('common', 'All');
		}

		$state[self::STATE_WITH_OUR_HELP] = tt('Live with our help', 'clients');
		$state[self::STATE_ACCOMMODATING] = tt('Accommodating', 'clients');
		$state[self::STATE_INDEPENDENTLY] = tt('Independently', 'clients');
		$state[self::STATE_IGNORE] = tt('Ignore', 'clients');

		return $state;
	}

	public static function getClientsState($state){
		if(!isset(self::$_client_states_arr)){
			self::$_client_states_arr = self::getClientsStatesArray();
		}

		if (array_key_exists($state, self::$_client_states_arr))
			return self::$_client_states_arr[$state];
		return null;
	}
}