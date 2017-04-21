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
	public $modelName = 'Entries';
	public $showSearchForm = false;

	public function actionIndex($catUrlName = '', $tag = ''){
		$catId = null;
		$categoryTitle = EntriesModule::t('Entries');
		
		if ($catUrlName) {
			if(issetModule('seo') && param('genFirendlyUrl')){
				$categoriesList = SeoFriendlyUrl::model()->findByAttributes(array('model_name' => 'EntriesCategory', 'url_'.Yii::app()->language => $catUrlName));
				if ($categoriesList)
					$catId = $categoriesList->model_id;
			}
			else {				
				if (is_numeric($index = array_search($catUrlName, EntriesCategory::$_freeRules)))
					$catId = $index;
				else {
					$tmpArr = explode(EntriesCategory::DELIMITER_URL, $catUrlName);
					if (count($tmpArr) && is_numeric($tmpArr[1]))
						$catId = $tmpArr[1];
				}
			}
		}
		
		if (!$catId)
			throw404();
		
		$model = EntriesCategory::model()->findByPk($catId);
		if (!$model)
			throw404();
		
		$categoryTitle = $model->getStrByLang('name');
				
		$criteria = new CDbCriteria;
		$criteria->addCondition('t.category_id = '.(int) $catId);
		$criteria->addCondition('t.active = '.Entries::STATUS_ACTIVE);
		if ($tag) {
			$tag = CHtml::encode($tag);
			$criteria->addSearchCondition('tags', $tag);
		}
		$criteria->order = 't.date_created DESC';
		
		$pages = new CPagination(Entries::model()->count($criteria));
		$pages->pageSize = param('moduleEntries_entriesPerPage', 10);
		$pages->applyLimit($criteria);

		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{entries}}');
		$criteria->with = array('image');
		
		$items = Entries::model()->cache(param('cachingTime', 1209600), $dependency)->findAll($criteria);
		
		if(issetModule('seo')){
			$seo = SeoFriendlyUrl::getForView($catUrlName, 'EntriesCategory');
			if($seo){
				$this->setSeo($seo);
			}
		}

		$this->render('index', array(
			'items' => $items,
			'pages' => $pages,
			'categoryTitle' => $categoryTitle,
			'tagName' => $tag,
		));
	}
	
	public function actionView($id = 0, $url = '') {
		$this->modelName = 'Entries';
				
		if($url && issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForView($url, $this->modelName);
			if(!$seo){
				throw404();
			}

			$this->setSeo($seo);

			$id = $seo->model_id;
		}
		
		$model = $this->loadModel($id, 1);	
		
		if ($model->active == Entries::STATUS_INACTIVE)
			throw404();
		
		$modelCategory = EntriesCategory::model()->findByPk($model->category_id);
		
		$categoriesRoutes = EntriesCategory::getEntriesCategoryRoute();	
				
		if(issetModule('seo') && param('genFirendlyUrl')){
			$catUrlName = 'content'.EntriesCategory::DELIMITER_URL.$model->category_id;									
			if (is_array($categoriesRoutes) && count($categoriesRoutes)) {
				foreach($categoriesRoutes as $catInfo) {
					foreach($catInfo as $info) {										
					if ($info['catId'] == $model->category_id && $info['lang'] == Yii::app()->language) {								
							$catUrlName = $info['url'];
						break;
					}
					elseif ($info['catId'] == $model->category_id) {
							$catUrlName = $info['url'];
						}
					}
				}
			}
		}
		else {
			$catUrlName = array_key_exists($model->category_id, EntriesCategory::$_freeRules) ? EntriesCategory::$_freeRules[$model->category_id] : 'content'.EntriesCategory::DELIMITER_URL.$model->category_id;
		}
		
		$this->render('view',array(
			'model'=>$model,
			'categoryTitle' => $modelCategory->getStrByLang('name'),
			'linkToCategory' => $catUrlName,
		));
	}
}