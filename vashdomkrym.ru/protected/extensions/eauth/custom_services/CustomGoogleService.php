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

require_once dirname(dirname(__FILE__)).'/services/GoogleOAuthService.php';

class CustomGoogleService extends GoogleOAuthService {
	protected $jsArguments = array('popup' => array('width' => 750, 'height' => 450));
	protected $scope = 'https://www.googleapis.com/auth/userinfo.profile+https://www.googleapis.com/auth/userinfo.email';
	protected $client_id = '';
	protected $client_secret = '';
	protected $providerOptions = array(
		'authorize' => 'https://accounts.google.com/o/oauth2/auth',
		'access_token' => 'https://accounts.google.com/o/oauth2/token',
	);

	public function __construct() {
		$this->title = tt('google_label', 'socialauth');
	}

	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');

		$this->attributes['id'] = $info['id'];
		$this->attributes['name'] = $info['name'];

		if (!empty($info['link']))
			$this->attributes['url'] = $info['link'];

		$this->attributes['id'] = $info['id'];
		$this->attributes['firstName'] = $info['given_name'];
		$this->attributes['email'] = (isset($info['verified_email']) && $info['verified_email']) ? $info['email'] : '';
		$this->attributes['mobilePhone'] = '';
		$this->attributes['homePhone'] = '';
	}
}
