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
	public $modelName = 'Article';
	public $showSearchForm = false;

	public function init() {
		parent::init();

		$articlePage = Menu::model()->findByPk(Menu::ARTICLES_ID);
		if ($articlePage) {
			if ($articlePage->active == 0) {
				throw404();
			}
		}
	}

	public function actionIndex(){
		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';

		$pages = new CPagination(Article::model()->count($criteria));
		$pages->pageSize = param('module_articles_itemsPerPage', 10);
		$pages->applyLimit($criteria);

		$articles = Article::model()->cache(param('cachingTime', 1209600), Article::getCacheDependency())->findAll($criteria);

		$this->render('index',array(
			'articles' => $articles, 'pages' => $pages
		));
	}

	public function actionView($id = 0, $url = ''){
		$criteria=new CDbCriteria;
		$criteria->order = 'sorter';
		$criteria->condition = 'active=1';

		$articles = Article::model()->cache(param('cachingTime', 1209600), Article::getCacheDependency())->findAll($criteria);

		if($url && issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);

			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}

		$this->render('view',array(
			'model'=>$this->loadModel($id), 'articles' => $articles
		));
	}
}