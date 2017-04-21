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
	public $modelName = 'Article';
	public $redirectTo = array('admin');

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('articles_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex(){
		//$this->layout='//layouts/inner';

		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';

		$pages = new CPagination(Article::model()->count($criteria));
		$pages->pageSize = param('module_articles_itemsPerPage', 10);
		$pages->applyLimit($criteria);

		$articles = Article::model()->findAll($criteria);

		$this->render('index',array(
			'articles' => $articles, 'pages' => $pages
		));
	}

	public function actionView($id){
		//$this->layout='//layouts/inner';

		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';
		$articles = Article::model()->findAll($criteria);

		$this->render('view',array(
			'model'=>$this->loadModel($id), 'articles' => $articles
		));
	}

	public function actionAdmin(){
		$this->getMaxSorter();
		$this->getMinSorter();

		parent::actionAdmin();
	}
}