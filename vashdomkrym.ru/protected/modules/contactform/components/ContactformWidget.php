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

class ContactformWidget extends CWidget {
	public $page;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'contactform'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'contactform'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.contactform.views');
	}

	public function run() {
		Yii::import('application.modules.contactform.models.ContactForm');
		$model = new ContactForm;
		$model->scenario = 'insert';

		if(isset($_POST['ContactForm']) && BlockIp::checkAllowIp(Yii::app()->controller->currentUserIpLong)){
			$model->attributes=$_POST['ContactForm'];

			if(!Yii::app()->user->isGuest){
				$model->email = Yii::app()->user->email;
				$model->username = Yii::app()->user->username;
			}

			if($model->validate()){
				$notifier = new Notifier;
				$notifier->raiseEvent('onNewContactform', $model);

				Yii::app()->user->setFlash('success', tt('Thanks_for_message', 'contactform'));
				$model = new ContactForm; // clear fields
			} else {
                $model->unsetAttributes(array('verifyCode'));
				Yii::app()->user->setFlash('error', tt('Error_send', 'contactform'));
			}
		}

		$this->render('widgetContactform', array('model' => $model));
	}
}