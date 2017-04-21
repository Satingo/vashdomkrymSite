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

class EntriesCategory extends ParentModel {
	private static $_allCategories;
	private static $_allCategoriesRoutes;
	
	const NEWS_CATEGORY_ID = 1;
	const ARTICLES_CATEGORY_ID = 2;
	const DELIMITER_URL = '-';
	
	public static $_freeRules = array(
		self::NEWS_CATEGORY_ID => 'news',
		self::ARTICLES_CATEGORY_ID => 'articles',
	);

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{entries_category}}';
	}

	public function rules() {
		return array(
			array('name', 'i18nRequired'),
			array('sorter', 'numerical', 'integerOnly' => true),
			array('name', 'i18nLength', 'max' => 255),
			array('id, sorter, date_updated', 'safe', 'on' => 'search'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

	public function i18nFields() {
		return array(
			'name' => 'varchar(255) not null',
		);
	}
	
	public function behaviors() {
		$arr = array();
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}
	
	public function seoFields() {
		return array(
			'fieldTitle' => 'name',
			'fieldDescription' => 'name'
		);
	}
		
	public function relations(){		
		$relations = array();
		$relations['entries'] = array(self::HAS_MANY, 'Entries', 'category_id');

		if (issetModule('seo')) {
			$relations['seo'] = array(self::HAS_ONE, 'SeoFriendlyUrl', 'model_id', 'on' => 'model_name="EntriesCategory"');
		}
		
		return $relations;
	}
	
	public function getName() {
		return $this->getStrByLang('name');
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'name' => tc('Name'),
			'sorter' => 'Sorter',
			'date_updated' => 'Date Updated',
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$tmp = 'name_' . Yii::app()->language;
		$criteria->compare($tmp, $this->$tmp, true);
		$criteria->order = 'sorter ASC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}
	
	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		return parent::afterSave();
	}

	public function beforeSave() {
		if ($this->isNewRecord) {
			$maxSorter = Yii::app()->db->createCommand()
				->select('MAX(sorter) as maxSorter')
				->from($this->tableName())
				->queryScalar();
			$this->sorter = $maxSorter + 1;
		}

		return parent::beforeSave();
	}
	
	public function beforeDelete() {
		$result = Entries::model()->findAllByAttributes(array('category_id' => $this->id));
		if ($result && count($result)) {
			foreach($result as $res) {
				$res->delete();
			}
		}
		
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "EntriesCategory"';
			Yii::app()->db->createCommand($sql)->execute();
		}
		
		return parent::beforeDelete();
	}

	public static function getAllCategories($val = null) {
		if (self::$_allCategories === null) {
			$sql = 'SELECT name_' . Yii::app()->language . ' AS name, id
                    FROM {{entries_category}}
                    ORDER BY sorter';

			$results = Yii::app()->db->createCommand($sql)->queryAll();

			if ($results)
				self::$_allCategories = CHtml::listData($results, 'id', 'name');
		}
		
		if ($val && is_array(self::$_allCategories) && array_key_exists($val, self::$_allCategories))
			return self::$_allCategories[$val];

		return self::$_allCategories;
	}
		
	public function getUrl() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'EntriesCategory');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field){
					return Yii::app()->createAbsoluteUrl('/entries/main/index', array(
						'catUrlName' => $seo->$field,
					));
				}
			}
		}
		
		return Yii::app()->createAbsoluteUrl('/entries/main/index', array(
			'catUrlName' => array_key_exists($this->id, self::$_freeRules) ? self::$_freeRules[$this->id] : 'content'.self::DELIMITER_URL.$this->id,
		));
	}
	
	public static function getEntriesCategoryRoute() {	
		if (oreInstall::isInstalled()) {
			if (self::$_allCategoriesRoutes === null) {
				if(issetModule('seo') && param('genFirendlyUrl')){
					$categoriesList = SeoFriendlyUrl::model()->findAllByAttributes(array('model_name' => 'EntriesCategory'));
					if ($categoriesList && is_array($categoriesList)) {						
						$langs = Lang::getActiveLangs();
						foreach($categoriesList as $category) {
							foreach ($langs as $lang) {
								self::$_allCategoriesRoutes[$category->id][$lang] = array('catId' => $category->model_id, 'lang' => $lang, 'url' => $category->{'url_' . $lang});
							}
						}
					}
				}
				else {
					$categoriesList = self::getAllCategories();
					if ($categoriesList && is_array($categoriesList)) {
						foreach($categoriesList as $id => $name) {
							if (array_key_exists($id, self::$_freeRules))
								self::$_allCategoriesRoutes[self::$_freeRules[$id]] = $id;
							else
								self::$_allCategoriesRoutes['content'.self::DELIMITER_URL.$id] = $id;
						}
					}
				}
			}
		}
				
		return self::$_allCategoriesRoutes;
	}
}