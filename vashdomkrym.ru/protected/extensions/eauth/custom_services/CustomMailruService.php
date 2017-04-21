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

require_once dirname(dirname(__FILE__)) . '/services/MailruOAuthService.php';

class CustomMailruService extends MailruOAuthService {
	protected $jsArguments = array('popup' => array('width' => 750, 'height' => 450));
	protected $client_id = '';
	protected $client_secret = '';

	public function __construct() {
		$this->title = tt('mailru_label', 'socialauth');
	}

	protected function fetchAttributes() {
		$info = (array) $this->makeSignedRequest('http://www.appsmail.ru/platform/api', array(
				'query' => array(
					'uids' => $this->uid,
					'method' => 'users.getInfo',
					'app_id' => $this->client_id,
				),
			));

		$info = $info[0];

		$this->attributes['id'] = $info->uid;
		$this->attributes['firstName'] = $info->first_name;
		$this->attributes['email'] = $info->email;
		$this->attributes['mobilePhone'] = '';
		$this->attributes['homePhone'] = '';
		$this->attributes['url'] = $info->link;
		$this->attributes['photo'] = $info->pic_big;
	}

}