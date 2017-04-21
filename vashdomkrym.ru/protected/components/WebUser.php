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

class WebUser extends CWebUser {
	private $_model = null;
	private $_keyPrefix;
	private $_access=array();

	function getRole() {
		if($user = $this->getModel()){
			// в таблице User есть поле role
			return $user->role;
		}
	}

	function getType() {
		if($user = $this->getModel()){
			return $user->type;
		}
	}

	private function getModel(){
		if (!$this->isGuest && $this->_model === null){
			$this->_model = User::model()->resetScope()->findByPk($this->id, array('select' => 'role, type'));
		}
		return $this->_model;
	}

	public function checkAccess($operation, $params=array(), $allowCaching=true) {
		if (issetModule('rbac')) {
			if($allowCaching && $params===array() && isset($this->_access[$operation]))
				return $this->_access[$operation];

			$access=Yii::app()->getAuthManager()->checkAccess($operation,$this->getId(),$params);
			if($allowCaching && $params===array())
				$this->_access[$operation]=$access;

			return $access;
		}
		else {
			if (Yii::app()->user->isGuest) { # гость
				if ($operation == 'guest')
					return true;
			}
			else {
				if (Yii::app()->user->getState('isAdmin')) #админ
					return true;
				else { # авторизированный пользователь
					if ($operation == 'registered' || $operation == 'guest')
						return true;
				}
			}

			return false;
		}
	}
}