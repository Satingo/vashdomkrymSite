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

class MainController extends ModuleUserController{
	public $modelName = 'InfoPages';
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
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			array(
				'ESetReturnUrlFilter + index, view, create, update, bookingform, complain, mainform, add, edit',
			),
		);
	}

	public function accessRules(){
		return array(
			array(
				'allow',
				'actions' => array('view', 'captcha'),
				'users'=>array('*'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}

	public function actionView($id = 0, $url = ''){
		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);
			if(!$seo){
				throw404();
			}
			$this->setSeo($seo);
			$id = $seo->model_id;
		}

		$model = $this->loadModel($id, 1);

		// избавляемся от дублей
		$modelUrl = $model->getUrl(false);

		if(issetModule('seo') && strpos(Yii::app()->request->url, $modelUrl) !== 0){
			$stringParams = array();
			if (isset($_GET['sort']))
				$stringParams['sort'] = $_GET['sort'];			
			if (isset($_GET['page']))
				$stringParams['page'] = $_GET['page'];
			
			if (count($stringParams))
				$this->redirect($modelUrl.'?'.http_build_query($stringParams), true, 301);
			else
				$this->redirect($modelUrl, true, 301);
		}

		if (!$model->active)
			throw404();

		if($model->id == 4) { //User Agreement
			$field = 'body_'.Yii::app()->language;
			$model->$field = str_replace('{site_domain}', IdnaConvert::checkDecode(Yii::app()->getBaseUrl(true)), $model->$field);
			$model->$field = str_replace('{site_title}', CHtml::encode(Yii::app()->name), $model->$field);
		}

		$this->showSearchForm = ($model->widget && $model->widget == 'apartments') ? true : false;

		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('view', array(
				'model'=>$model,
			));
		}
		else {
			$this->render('view',array(
				'model'=>$model,
			));
		}
	}

}