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

require_once dirname(dirname(__FILE__)).'/services/VKontakteOAuthService.php';

class CustomVKService extends VKontakteOAuthService {
	protected $jsArguments = array('popup' => array('width' => 750, 'height' => 450));
	protected $scope = 'users';
	protected $client_id = '';
	protected $client_secret = '';
	protected $providerOptions = array(
		'authorize' => 'http://api.vk.com/oauth/authorize',
		'access_token' => 'https://api.vk.com/oauth/access_token',
	);

	public function __construct() {
		$this->title = tt('vkontakte_label', 'socialauth');
	}

	protected function fetchAttributes() {
		if (isset($_REQUEST['captcha_sid']) && isset($_REQUEST['captcha_key'])) {
			$info = (array)$this->makeSignedRequest('https://api.vk.com/method/users.get',
				array(
					'query' => array(
						'uids' => $this->uid,
						'fields' => 'uid, first_name, contacts',
						'captcha_sid' => $_REQUEST['captcha_sid'],
						'captcha_key' => $_REQUEST['captcha_key'],
					),
				)
			);
		}
		else {
			$info = (array)$this->makeSignedRequest('https://api.vk.com/method/users.get', array(
				'query' => array(
					'uids' => $this->uid,
					'fields' => 'uid, first_name, contacts',
				),
			));
		}


		$info = $info['response'][0];

		$this->attributes['id'] = $info->uid;
		$this->attributes['firstName'] = $info->first_name;
		$this->attributes['email'] = '';
		$this->attributes['mobilePhone'] = (isset($info->mobile_phone) && $info->mobile_phone) ? $info->mobile_phone : '';
		$this->attributes['homePhone'] = (isset($info->home_phone) && $info->home_phone) ? $info->home_phone : '';
		$this->attributes['url'] = 'http://vk.com/id'.$info->uid;
	}

	protected function fetchJsonError($json) {
		if(isset($json->error)) {
			if (isset($json->error->error_code) && $json->error->error_code == 14) {
				$this->capcthaForm($json->error);
				exit;
			}
			return array(
				'code' => $json->error->error_code,
				'message' => $json->error->error_msg,
			);
		}
		else
			return null;
	}

	protected function capcthaForm($data) {
		if (isset($data->request_params)) {
			$action = array();
			foreach($data->request_params as $param) {
				$action[$param->key] = $param->value;
			}

			parse_str(Yii::app()->getRequest()->getRequestUri(), $output);
			if (isset($output['captcha_sid']))
				unset($output['captcha_sid']);

			$action['captcha_sid'] = $data->captcha_sid;
			$action = array_merge($action, $output);

			$action = http_build_query($action);
			$action = Yii::app()->getRequest()->getHostInfo().Yii::app()->getRequest()->getRequestUri().$action;

			echo '<form action="'.$action.'" method="POST">';
				echo tc('Verify Code').': <br />';
				echo '<img src="'.$data->captcha_img.'" /> <br /> <br />';
				echo '<input type="text" name="captcha_key" size="10"> <br /> <br />';
				echo '<input type="submit" value="'.tc('Send').'">';
			echo '</form>';
		}
	}
}
