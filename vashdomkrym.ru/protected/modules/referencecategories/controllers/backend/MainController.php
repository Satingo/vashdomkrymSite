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

class MainController extends ModuleAdminController{
	public $modelName = 'ReferenceCategories';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_reference_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id){
		$this->redirect(array('admin'));
	}
	public function actionIndex(){
		$this->redirect(array('admin'));
	}

	public function actionCreate(){
		$model=new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				if(isset($_POST['addValues'])){
					Yii::app()->user->setFlash('success', tt('The new category is successfully created.').' '.tt('Please add values to the category now.'));
					$this->redirect(array('/referencevalues/backend/main/create','cat_id'=>$model->id));
				} else {
					Yii::app()->user->setFlash('success', tt('The new category is successfully created.'));
					$this->redirect(array('admin'));
				}
			}
		}

		$this->render('create',	array('model'=>$model));
	}

	public function actionAdmin(){
		$this->getMaxSorter();
		$this->getMinSorter();

		parent::actionAdmin();
	}


}
