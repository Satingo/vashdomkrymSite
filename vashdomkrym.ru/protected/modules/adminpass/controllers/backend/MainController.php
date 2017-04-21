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

class MainController extends ModuleAdminController {
	public $modelName = 'User';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_settings_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex(){

		$model=$this->loadModel(Yii::app()->user->id);

		if(isset($_POST[$this->modelName])){
			$model->scenario = 'changeAdminPass';

			$model->old_password = $_POST[$this->modelName]['old_password'];
			if($model->validatePassword($model->old_password)){
				if(demo()){
					Yii::app()->user->setFlash('error', tc('Sorry, this action is not allowed on the demo server.'));
					$this->redirect(array('index'));
				}

				$model->attributes=$_POST[$this->modelName];
				if($model->validate()){
					$model->setPassword();
					$model->save(false);
					Yii::app()->user->setFlash('success', Yii::t('module_usercpanel', 'Your password successfully changed.'));
					$this->redirect(array('index'));
				}
			} else {
				Yii::app()->user->setFlash('error', Yii::t('module_adminpass', 'Wrong admin password! Try again.'));
				$this->redirect(array('index'));
			}
		}
		$this->render('index', array('model' => $model));
	}
}