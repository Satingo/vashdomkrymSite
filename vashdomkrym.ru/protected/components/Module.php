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

class Module extends CWebModule {

	public $defaultController = 'main';

	public function init() {
		// this method is called when the module is being created
		// you may place code here to customize the module or the application
		// import the module-level models and components
		$this->setImport(array(
			'application.modules.'.$this->getName() . '.models.*',
			'application.modules.'.$this->getName() . '.components.*',
		));
		$this->setViewPath(Yii::app()->getBasePath() . '/modules/' . $this->getName(). '/views');
	}

	public static function t($str='',$params=array(),$dic=null) {
		if(Yii::app()->controller->module){
			if($dic === null){
				return Yii::t('module_'.Yii::app()->controller->module->id, $str, $params);
			}
			else{
				return Yii::t('module_'.Yii::app()->controller->module->id.'_'.$dic, $str, $params);
			}
		}
	}
}
