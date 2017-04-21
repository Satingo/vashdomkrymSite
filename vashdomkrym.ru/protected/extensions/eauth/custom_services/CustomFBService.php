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

require_once dirname(dirname(__FILE__)).'/services/FacebookOAuthService.php';

class CustomFBService extends FacebookOAuthService {
	protected $jsArguments = array('popup' => array('width' => 750, 'height' => 450));
	protected $scope = 'email';
	protected $client_id = '';
	protected $client_secret = '';
	protected $providerOptions = array(
		'authorize' => 'https://www.facebook.com/dialog/oauth',
		'access_token' => 'https://graph.facebook.com/oauth/access_token',
	);

	public function __construct() {

		$this->title = tt('facebook_label', 'socialauth');
	}

	protected function fetchAttributes() {
		$info = (object) $this->makeSignedRequest('https://graph.facebook.com/me');
        //logs($info);

		$this->attributes['id'] = $info->id;
		$this->attributes['firstName'] = (isset($info->name) && $info->name) ? $info->name : '';
		$this->attributes['email'] = (isset($info->email) && $info->email) ? $info->email : '';
		$this->attributes['mobilePhone'] = '';
		$this->attributes['homePhone'] = '';
		$this->attributes['url'] = (isset($info->link) && $info->link) ? $info->link : '';
	}
}
