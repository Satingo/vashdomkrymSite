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

class ChangeOwner extends CFormModel {
	public $futureOwner;
	public $futureApartments;

	public function rules() {
		return array(
			array('futureOwner, futureApartments', 'required'),
			array('futureOwner', 'numerical', 'integerOnly' => true),
			array('futureApartments', 'type', 'type'=>'array', 'allowEmpty'=>false),
		);
	}

	public function attributeLabels() {
		return array(
			'futureOwner' => tt('futureOwner', 'apartments'),
			'futureApartments' => tt('futureApartments', 'apartments'),
		);
	}
}