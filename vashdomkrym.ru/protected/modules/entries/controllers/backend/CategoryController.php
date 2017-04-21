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

class CategoryController extends ModuleAdminController{
	public $modelName = 'EntriesCategory';
	public $redirectTo = array('admin');
	
	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'category'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->getModule($this->id)->getName().DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'category';
		}
		return Yii::getPathOfAlias('application.modules.'.$this->getModule($this->id)->getName().'.views.backend.category');
	}

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('entries_category_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}
	
	public function actionAdmin() {
		Yii::app()->user->setState('menu_active', 'entries.category');
		
		$this->getMaxSorter();
		$this->getMinSorter();

		parent::actionAdmin();
	}
	
	public function actionCreate() {
		Yii::app()->user->setState('menu_active', 'entries.category');
		
		parent::actionCreate();
	}
	
	public function actionUpdate($id) {
		Yii::app()->user->setState('menu_active', 'entries.category');
		
		parent::actionUpdate($id);
	}

	public function actionView($id) {
		$this->redirect($this->redirectTo);
	}
}