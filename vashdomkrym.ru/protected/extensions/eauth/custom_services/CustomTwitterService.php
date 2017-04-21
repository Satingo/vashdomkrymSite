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

require_once dirname(dirname(__FILE__)).'/services/TwitterOAuthService.php';

class CustomTwitterService extends TwitterOAuthService {
	protected $jsArguments = array('popup' => array('width' => 750, 'height' => 450));
	protected $key = '';
	protected $secret = '';
	protected $providerOptions = array(
		'request' => 'https://api.twitter.com/oauth/request_token',
		'authorize' => 'https://api.twitter.com/oauth/authorize',  //'https://api.twitter.com/oauth/authenticate',
		'access' => 'https://api.twitter.com/oauth/access_token',
	);

	public function __construct() {
		$this->title = tt('twitter_label', 'socialauth');
	}

	protected function fetchAttributes() {
		$info = $this->makeSignedRequest('https://api.twitter.com/1.1/account/verify_credentials.json');

		$this->attributes['id'] = $info->id;
		$this->attributes['firstName'] = $info->name; // $info->screen_name;
		$this->attributes['email'] = '';
		$this->attributes['mobilePhone'] = '';
		$this->attributes['homePhone'] = '';
		$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id='.$info->id_str;
	}
}