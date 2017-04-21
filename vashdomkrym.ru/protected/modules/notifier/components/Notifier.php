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

class Notifier {
	private $_adminRules;
	private $_userRules;
	private $init = 0;
	private $lang;
    private $sendToAdmin = false;

    private $_params = array();

	public function init(){
        Yii::import('application.modules.notifier.models.NotifierModel');

		$this->lang['admin'] = Lang::getAdminMailLang();
		$this->lang['default'] = Lang::getDefaultLang();
		$this->lang['current'] = Yii::app()->language;

		Yii::app()->setLanguage($this->lang['admin']);

		$this->_adminRules = array(
			'onNewBooking' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end', 'apartment_id', 'ownerEmail'),
				'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewCity' => array(
				'fields' => array('name'),
				'i18nFields' => array(),
				'active' => 1,
			),
			'onNewSimpleBookingForRent' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end'),
				'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onNewSimpleBookingForBuy' => array(
				'fields' => array('username', 'comment', 'useremail', 'phone'),
				'i18nFields' => array('type'),
				'active' => param('module_notifier_adminNewBooking', 1),
			),
			'onBookingChangeStatus' => array(
				'fields' => array('username', 'amount', 'apartmentUrl', 'apartmentTitle'),
				'active' => 1,
			),
			'onNewUser_without_confirm' => array(
				'fields' => array('email', 'username'),
				'url' => array(
					'/users/backend/main/admin',
				),
				'active' => param('module_notifier_adminNewUser', 1),
			),
			'onNewUser_with_confirm' => array(
				'fields' => array('email', 'username'),
				'url' => array(
					'/users/backend/main/admin',
				),
				'active' => param('module_notifier_adminNewUser', 1),
			),
			'onNewContactform' => array(
				'fields' => array('name', 'email', 'phone', 'body'),
				'active' => param('module_notifier_adminNewContactform', 1),
			),
			'onOfflinePayment' => array(
				'fields' => array('amount', 'currency_charcode'),
				'active' => param('module_notifier_adminNewPayment', 1)
			),
			'onRequestProperty' => array(
				'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'ownerEmail', 'apartmentUrl'),
				'active' => param('module_request_property_send_admin', 1),
			),
			'onNewComment' => array(
				'fields' => array('rating', 'user_email', 'body', 'user_name'),
				'active' => param('module_notifier_userNewComment', 1),
			),
			'onNewApartment' => array(
				'fields' => array('id'),
				'active' => param('module_notifier_adminNewApartment', 1),
				'url' => array(
					'/apartments/backend/main/admin',
				),
			),
			'onNewComplain' => array(
				'fields' => array('apartment_id', 'email', 'name', 'body'),
				'active' => param('module_notifier_adminNewComplain', 1),
			),
			'onNewReview' => array(
				'fields' => array('name', 'body'),
				'active' => param('module_notifier_adminNewReview', 1),
			),
		);

		Yii::app()->setLanguage($this->lang['current']);
		$this->setLang();

		$this->_userRules = array(
            'onNewBooking' => array(
                'fields' => array('username', 'comment', 'useremail', 'phone', 'date_start', 'date_end', 'apartment_id'),
                'i18nFields' => array('time_inVal', 'time_outVal', 'type'),
                'active' => param('module_notifier_ownerNewBooking', 1),
            ),
			'onBookingNeedPay' => array(
				'fields' => array('username', 'amount', 'apartmentUrl', 'apartmentTitle', 'date_start', 'date_end', 'currency', 'comment_admin', 'calcForMail'),
				'active' => 1,
			),
			'onBookingChangeStatus' => array(
				'fields' => array('username', 'apartmentUrl', 'apartmentTitle', 'date_start', 'date_end', 'currency'),
				'active' => 1,
			),
			'onBookingConfirm' => array(
				'fields' => array('username', 'apartmentUrl', 'apartmentTitle', 'date_start', 'date_end', 'currency'),
				'active' => 1,
			),

			'onNewUser_without_confirm' => array(
				'fields' => array('email', 'password'),
				'url' => array('/usercpanel/main/index'),
				'active' => (param('module_notifier_userNewUser', 1) || $this->getFromParam('forceSendUser')),
			),
			'onNewUser_with_confirm' => array(
				'fields' => array('email', 'password', 'activateLink'),
				'url' => array('/usercpanel/main/index'),
				'active' => 1,
			),
			'onRecoveryPassword' => array(
				'fields' => array('email', 'temprecoverpassword', 'recoverPasswordLink'),
				'url' => array('/usercpanel/main/index'),
				'active' => 1,
			),
            'onNewComment' => array(
                'fields' => array('rating', 'user_email', 'body', 'user_name'),
                'active' => param('module_notifier_adminNewComment', 1),
            ),
			'onRequestProperty' => array(
                'fields' => array('senderName', 'senderEmail', 'senderPhone', 'body', 'ownerName', 'apartmentUrl', 'ownerEmail'),
                'active' => param('module_request_property_send_user', 1),
            ),
			'onNewAgent' => array(
                'fields' => array('username', 'email', 'phone'),
                'active' => param('module_notifier_new_agent', 1),
            ),
			'onNewPrivateMessage' => array(
				'fields' => array('username', 'messageEmailSend'),
				'url' => array('/usercpanel/main/index'),
				'active' => param('module_messages_private_message', 1),
			),
    	);

        $this->init = 1;

        $this->restoreLang();
	}

	public function setLang(){
		if(Yii::app()->user->checkAccess('backend_access')){
			Yii::app()->setLanguage($this->lang['default']);
		}
	}
	public function restoreLang(){
		if(Yii::app()->user->checkAccess('backend_access') && $this->lang['current']){
			Yii::app()->setLanguage($this->lang['current']);
		}
	}

    public static function getRules(){
        $notify = new Notifier();
        $notify->init();

        return array(
            'admin' => $notify->_adminRules,
            'user' => $notify->_userRules,
        );
    }

	public function raiseEvent($eventName, $model, $params = array()){
        Yii::import('application.modules.notifier.models.NotifierModel');

        if($this->init == 0){
            $this->init();
        }

        $notifyModel = NotifierModel::model()->findByAttributes(array('event' => $eventName));

        if(!$notifyModel){
            return false;
        }

        $this->_params = $params;

        $forceEmail = $this->getFromParam('forceEmail');

        $to = '';
        if ($forceEmail) {
            $to = $forceEmail;
        } else {
            $user = $this->getFromParam('user');
            if ($user){
                $to = $user->email;
            }
        }

        if(isset($this->_userRules[$eventName]) && $to){
            $rules = $this->_userRules[$eventName];

            $rules['subject'] = $notifyModel->getStrByLang('subject');
            $rules['body'] = $notifyModel->getStrByLang('body');

            if($rules['active']){
                $this->_processEvent($rules, $model, $to);
            }
        }

        if(isset($this->_adminRules[$eventName])){
            $rules = $this->_adminRules[$eventName];

            $rules['subject'] = $notifyModel->getStrByLang('subject_admin');
            $rules['body'] = $notifyModel->getStrByLang('body_admin');

            $this->sendToAdmin = true;

            if($rules['active']){
				if ($eventName == 'onNewApartment' || $eventName == 'onNewComment' || $eventName == 'onNewComplain' || $eventName == 'onNewBooking' || $eventName == 'onNewSimpleBookingForRent' || $eventName == 'onNewSimpleBookingForBuy' || $eventName == 'onNewReview')
					$this->_processEvent($rules, $model, param('adminEmail'), true);
                else
					$this->_processEvent($rules, $model, param('adminEmail'));
            }
        }
	}

    private function getFromParam($key){
        return isset($this->_params[$key]) ? $this->_params[$key] : NULL;
    }

	private function _processEvent($rule, $model, $to, $toModerators = false){
        $user = $this->getFromParam('user');

		if($this->sendToAdmin)
			$lang = 'admin';
		else
			$lang = Yii::app()->user->checkAccess('backend_access') ? 'default' : 'current';

		$body = '';
		if(isset($rule['body'])){

			$body = $rule['body'];
			$body = str_replace('{host}', IdnaConvert::checkDecode(Yii::app()->request->hostInfo), $body);
			$body = str_replace('{fullhost}', IdnaConvert::checkDecode(Yii::app()->getBaseUrl(true)), $body);

			if($user && !isset($model->username) && !isset($model->ownerName)){
				$body = str_replace('{username}', $user->username, $body);
			}

			if(isset($rule['url']) && $model){
				$params = array();
				if(isset($rule['url'][1])){
					foreach($rule['url'][1] as $param){
						$params[$param] = $model->$param;
					}
					$params['lang'] = $lang;
				}
				$url = Yii::app()->controller->createAbsoluteUrl($rule['url'][0], $params);
				$body = str_replace('{url}', IdnaConvert::checkDecode($url), $body);
			}

			if(isset($rule['fields']) && $model){
				foreach($rule['fields'] as $field){
                    $val = isset($model->$field) ? $model->$field : tc('No information');
                    $body = str_replace('{'.$field.'}', IdnaConvert::checkDecode($val), $body);
				}
			}

			if(isset($rule['i18nFields']) && $model){
				foreach($rule['i18nFields'] as $field){
					$field_val = $model->$field;
					$body = str_replace('{'.$field.'}', (isset($field_val[$lang]) ? CHtml::encode($field_val[$lang]) : tc('No information')), $body);
				}
			}
			$body = str_replace("\n.", "\n..", $body);
		}

        if($body){
	        Yii::import('application.extensions.YiiMailer.YiiMailer');
	        $mailer = new YiiMailer();

            if (param('mailUseSMTP', 1)) {
				$mailer->setSmtp(
					param('mailSMTPHost', 'mail.vashdomkrym.ru'), 
					param('mailSMTPPort', 25), 
					param('mailSMTPSecure', 'tls'), 
					true, 
					param('mailSMTPLogin', 'admin@vashdomkrym.ru'), 
					param('mailSMTPPass', 'uT373PnV')
				);
            }

			$mailer->setFrom(param('adminEmail'), param('mail_fromName', User::getAdminName()));
		   
			$mailer->setTo($to);
			//$mailer->setAddresses('to', $to);

			if ($toModerators && issetModule('rbac')) {
				$moderators = User::model()->findAllByAttributes(array('role' => User::ROLE_MODERATOR));

				if (is_array($moderators) && !empty($moderators)) {
					$moderatorEmails = CHtml::listData($moderators, 'id', 'email');
					$mailer->setBcc($moderatorEmails);
					unset($moderatorEmails);
					
					/*foreach ($moderators as $moderator) {
						if (isset($moderator->email) && $moderator->email) {
							//$mailer->setAddresses('bcc', $moderator->email);
						}
					}*/
				}
			}

            if(isset($rule['subject'])) {
				$mailer->setSubject($rule['subject']);
			}
			
			$mailer->setBody($body);
			
			$mailer->CharSet = 'UTF-8';
			$mailer->IsHTML(false);

			$mailer->SMTPDebug = true;
			$mailer->Send();
			echo $mailer->ErrorInfo;
			exit;

            if (!$mailer->Send()){
                throw new CHttpException(503, tt('message_not_send', 'notifier') . ' ErrorInfo: ' . $mailer->getError());
                //showMessage(tc('Error'), tt('message_not_send', 'notifier'));
            }
        }
	}

}