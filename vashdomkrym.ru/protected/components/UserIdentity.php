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

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

	private $_id;

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		$user = User::model()->find('LOWER(email)=?', array(strtolower($this->username)));
		if ($user === null){
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return 0;
		}

		if (!$user->validatePassword($this->password)){
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return 0;
		}
		elseif (!$user->active) {
			showMessage(Yii::t('common', 'Login'), Yii::t('common', 'Your account not active. The reasons: you not followed the link in the letter which has been sent at registration. Or administrator deactivate your account'), null, true);
			return 0;
		}
		else {
			$this->_id = $user->id;
			$this->username = $user->username;

			$this->setState('email', $user->email);
			$this->setState('username', $user->username);
			$this->setState('phone', $user->phone);

			if($user->role == User::ROLE_ADMIN){
				$this->setState('isAdmin', 1);
			}

			if (issetModule('rbac')) {
				$auth=Yii::app()->getAuthManager();
				if(!$auth->isAssigned($user->role,$this->_id)) {
					if($auth->assign($user->role,$this->_id)) {
						//Yii::app()->authManager->save();
					}
				}
			}
			else {
				if($user->role == User::ROLE_MODERATOR) {
					$this->errorCode = self::ERROR_PASSWORD_INVALID;
					return 0;
				}
			}

			$this->errorCode = self::ERROR_NONE;
		}
		return $this->errorCode == self::ERROR_NONE;
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId() {
		return $this->_id;
	}
}