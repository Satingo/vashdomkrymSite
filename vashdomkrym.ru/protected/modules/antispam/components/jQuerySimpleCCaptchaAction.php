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

class jQuerySimpleCCaptchaAction extends CCaptchaAction {
	public function run() {

		Yii::app()->end();
	}

	public function validate($input,$caseSensitive) {
		$valid = false;

		if (isset($_POST) && isset($_POST['captchaSelection'])) {
			if (Yii::app()->user->hasState("simpleCaptchaAnswer") && $_POST['captchaSelection'] == Yii::app()->user->getState('simpleCaptchaAnswer')) {
				$valid = true;
			}
		}

		return $valid;
	}
}