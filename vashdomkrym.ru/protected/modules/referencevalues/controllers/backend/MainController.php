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
	public $modelName = 'ReferenceValues';
	public $maxSorters = array();
	public $minSorters = array();
	public $multyfield = 'title';

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

		$cat_id = Yii::app()->request->getParam('cat_id');
		if($cat_id)
			$model->reference_category_id = $cat_id;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				Yii::app()->user->setFlash('success', tt('The new reference value is successfully created.'));
				if(isset($_POST['addMore']))
					$this->redirect(array('create','cat_id'=>$model->reference_category_id));
				$this->redirect('admin');
			}
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionAdmin(){
		$sql = 'SELECT reference_category_id, MAX(sorter) as sorter FROM {{apartment_reference_values}} GROUP BY reference_category_id';
		$sorters = Yii::app()->db->createCommand($sql)->queryAll();
		foreach($sorters as $sorter){
			$this->maxSorters[$sorter['reference_category_id']] = $sorter['sorter'];
		}

		$sql = 'SELECT reference_category_id, MIN(sorter) as sorter FROM {{apartment_reference_values}} GROUP BY reference_category_id';
		$sorters = Yii::app()->db->createCommand($sql)->queryAll();
		foreach($sorters as $sorter){
			$this->minSorters[$sorter['reference_category_id']] = $sorter['sorter'];
		}

		if(isset($_GET['ReferenceValues']['category_filter'])){
			$this->params['currentCategory'] = intval($_GET['ReferenceValues']['category_filter']);
		}
		else{
			$this->params['currentCategory'] = 0;
		}

		parent::actionAdmin();

	}

	public function getCategories($withoutEmpty = 0){
		$sql = 'SELECT id, title_'.Yii::app()->language.' as lang FROM {{apartment_reference_categories}} ORDER BY sorter ASC';
		$categories = Yii::app()->db->createCommand($sql)->queryAll();

		if(!$withoutEmpty)
			$return[0] = '';
		foreach($categories as $category){
			$return[$category['id']] = $category['lang'];
		}
		return $return;
	}

}
