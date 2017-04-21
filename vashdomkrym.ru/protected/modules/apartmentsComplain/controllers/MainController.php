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

class MainController extends ModuleUserController {
	public $modelName = 'ApartmentsComplain';

	public function actions() {
		$return = array();
		if (param('useJQuerySimpleCaptcha', 0)) {
			$return['captcha'] = array(
				'class' => 'jQuerySimpleCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}
		else {
			$return['captcha'] = array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}

		return $return;
	}

	public function actionComplain() {
		$id = Yii::app()->request->getParam('id', 0);

		if (!$id)
			throw404();

		$model = new $this->modelName;

		$modelApartment = Apartment::model()->findByPk($id);
		if (!$modelApartment)
			throw404();

		if(isset($_POST[$this->modelName]) && BlockIp::checkAllowIp(Yii::app()->controller->currentUserIpLong)){
			$model->attributes = $_POST[$this->modelName];

			$model->apartment_id = $id;
			$model->session_id = Yii::app()->session->sessionId;
			$model->user_id = 0;

			$model->user_ip = Yii::app()->controller->currentUserIp;
			$model->user_ip_ip2_long = Yii::app()->controller->currentUserIpLong;

			if(!Yii::app()->user->isGuest){
				$model->email = Yii::app()->user->email;
				$model->name = Yii::app()->user->username;
				$model->user_id = Yii::app()->user->id;
			}

			if ($model->validate()) {
				if ($this->checkAlreadyComplain($model->apartment_id, $model->user_id, $model->session_id)) {
					if ($model->save(false)) {
						$notifier = new Notifier;
						$notifier->raiseEvent('onNewComplain', $model);

						Yii::app()->user->setFlash('success', tt('Thanks_for_complain', 'apartmentsComplain'));
						$model = new $this->modelName; // clear fields
					}
				}
				else
					Yii::app()->user->setFlash('notice', tt('your_already_post_complain', 'apartmentsComplain'));
			}
		}

		if(Yii::app()->request->isAjaxRequest){
			Yii::app()->clientscript->scriptMap['jquery.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery.min.js'] = false;
			Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;

			$this->renderPartial('complain_form', array(
				'model' => $model,
				'apId' => $id,
				'isFancy' => true,
				'modelApartment' => $modelApartment,
			), false, true);
		}
		else{
			$this->render('complain_form', array('model' => $model, 'apId' => $id, 'modelApartment' => $modelApartment));
		}
	}

	public function checkAlreadyComplain($apartmentId = 0, $userId = 0, $sessionId = 0) {
		if (!$apartmentId)
			return false;

		if ($userId) { // авторизированный пользователь
			$result = ApartmentsComplain::model()->findByAttributes(array('user_id' => $userId, 'apartment_id' => $apartmentId));
			if ($result)
				return false;
		}
		elseif ($sessionId) { // гость
			$result = ApartmentsComplain::model()->findByAttributes(array('session_id' => $sessionId, 'apartment_id' => $apartmentId));
			if ($result)
				return false;
		}
		return true;
	}
}