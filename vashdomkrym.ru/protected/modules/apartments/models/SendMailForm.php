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

class SendMailForm extends CFormModel {
	public $senderName;
	public $senderEmail;
	public $senderPhone;
	public $body;
	public $verifyCode;

	public $ownerId;
	public $ownerEmail;
	public $ownerName;

	public $apartmentUrl;

	public function rules()	{
		return array(
			array('senderName, senderEmail, body', 'required'),
			array('senderEmail', 'email'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest),
			array('senderPhone', 'safe'),
			array('senderName, senderEmail', 'length', 'max' => 128),
			array('senderPhone', 'length', 'max' => 16, 'min' => 5),
			array('body', 'length', 'max' => 1024),
		);
	}

	public function attributeLabels() {
		return array(
			'senderName' => tt('user_request_name', 'apartments'),
			'senderEmail' => tt('user_request_email', 'apartments'),
			'senderPhone' => tt('user_request_phone', 'apartments'),
			'body' => tt('user_request_message', 'apartments'),
			'verifyCode' => tt('user_request_ver_code', 'apartments'),
		);
	}
}