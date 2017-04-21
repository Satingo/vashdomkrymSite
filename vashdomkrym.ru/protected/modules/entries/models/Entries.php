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

class Entries extends ParentModel {
	public $title;
	public $dateCreated;
	public $dateCreatedLong;
	public $supportedExt = 'jpg, png, gif';

	public $entriesImage;
	public $maxImageSize;
	public $maxImageSizeMb;

	private static $_lastEntries;
	private static $_allEntriesForRoute;
	private static $_allEntriesRoutes;
	
	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;
	
	private $_oldTags;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{entries}}';
	}

	public function rules() {
		return array(
			array('title, body', 'i18nRequired'),
			array('category_id', 'required'),
			array('category_id, active', 'numerical', 'integerOnly'=>true),
			array('title', 'i18nLength', 'max' => 128),
			array(
				'entriesImage', 'file',
				'types' => $this->supportedExt,
				'maxSize' => $this->maxImageSize,
				'tooLarge' => Yii::t('module_apartments', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $this->maxImageSizeMb)),
				'allowEmpty' => true,
			),
			array('tags', 'match', 'pattern'=>'/^[\w\s,]+$/iux', 'message'=> tt('Tags can only contain word characters.', 'entries')),
			array('tags', 'normalizeTags'),
			array($this->getI18nFieldSafe(), 'safe'),
		);
	}

    public function i18nFields(){
        return array(
            'title' => 'varchar(255) not null',
            'body' => 'text not null',
			'announce' => 'text not null',
        );
    }

	public function seoFields() {
		return array(
			'fieldTitle' => 'title',
			'fieldDescription' => 'body'
		);
	}

	public function	init(){
		$fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
		$fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));
		$this->maxImageSize = min($fileMaxSize);
		$this->maxImageSizeMb = round($this->maxImageSize / (1024*1024));


		parent::init();
	}

    public function getTitle(){
        return $this->getStrByLang('title');
    }

    public function getBody(){
        return $this->getStrByLang('body');
    }

	public function getAnnounce(){
		return $this->getStrByLang('announce');
	}

	public function relations(){		
		$relations = array();
		$relations['image'] = array(self::BELONGS_TO, 'EntriesImage', 'image_id');
		$relations['category'] = array(self::BELONGS_TO, 'EntriesCategory', 'category_id');
		
		if (issetModule('seo')) {
			$relations['seo'] = array(self::HAS_ONE, 'SeoFriendlyUrl', 'model_id', 'on' => 'model_name="Entries"');
		}
		
		return $relations;
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'active' => tc('Status'),
			'title' => tt('Entry title', 'entries'),
			'body' => tt('Entry body', 'entries'),
			'date_created' => tt('Creation date', 'entries'),
			'dateCreated' => tt('Creation date', 'entries'),
			'announce' => tt('Announce', 'entries'),
			'entriesImage' => tt('Image for entry', 'entries'),
			'category_id' => tt('Category', 'entries'),
			'tags' => tt('Tags', 'entries'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

        $titleField = 'title_'.Yii::app()->language;
		$criteria->compare($this->getTableAlias().'.'.$titleField, $this->$titleField, true);
        
		$bodyField = 'body_'.Yii::app()->language;	
		$criteria->compare($this->getTableAlias().'.'.$bodyField, $this->$bodyField, true);
		
		$criteria->compare($this->getTableAlias().'.category_id', $this->category_id);
		$criteria->addSearchCondition($this->getTableAlias().'.tags', $this->tags);

		$criteria->compare($this->getTableAlias().'.active', $this->active, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort' => array(
				'defaultOrder' => 'id DESC',
			),
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function behaviors() {
		$arr = array();
		$arr['AutoTimestampBehavior'] = array(
			'class' => 'zii.behaviors.CTimestampBehavior',
			'createAttribute' => 'date_created',
			'updateAttribute' => 'date_updated',
		);
		if (issetModule('historyChanges')) {
			$arr['ArLogBehavior'] = array(
				'class' => 'application.modules.historyChanges.components.ArLogBehavior',
			);
		}

		return $arr;
	}

	protected function afterFind() {		
		$dateFormat = param('dateFormat', 'd.m.Y H:i:s');
		$this->dateCreated = date($dateFormat, strtotime(HSite::convertDateToDateWithTimeZone($this->date_created)));
		$this->dateCreatedLong = Yii::app()->dateFormatter->format(Yii::app()->locale->getDateFormat('long'), CDateTimeParser::parse(HSite::convertDateToDateWithTimeZone($this->date_created), 'yyyy-MM-dd hh:mm:ss'));

		$this->_oldTags=$this->tags;
		
		parent::afterFind();
	}

	public function beforeSave(){			
		if($this->entriesImage){
			if($this->image){
				$this->image->delete();
			}
			$image = new EntriesImage();
			$image->imageInstance = $this->entriesImage;
			$image->save();
			if($image->id){
				$this->image_id = $image->id;
			}
		}

		return parent::beforeSave();
	}


	public function afterSave() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			SeoFriendlyUrl::getAndCreateForModel($this);
		}
		
		EntriesTags::model()->updateFrequency($this->_oldTags, $this->tags);
		
		return parent::afterSave();
	}

	public function beforeDelete() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$sql = 'DELETE FROM {{seo_friendly_url}} WHERE model_id="'.$this->id.'" AND model_name = "Entries"';
			Yii::app()->db->createCommand($sql)->execute();
		}
		if($this->image){
			$this->image->delete();
		}

		$sql = 'DELETE FROM {{comments}} WHERE model_id=:id AND model_name="Entries"';
		Yii::app()->db->createCommand($sql)->execute(array(':id' => $this->id));
		
		EntriesTags::model()->updateFrequency($this->tags, '');

		return parent::beforeDelete();
	}

	public function getAllWithPagination($inCriteria = null){
		if($inCriteria === null){
			$criteria = new CDbCriteria;
		} 
		else {
			$criteria = $inCriteria;
		}		
		
		$criteria->addCondition('t.active = '.Entries::STATUS_ACTIVE);
		$criteria->order = 't.date_created DESC';

		$pages = new CPagination($this->count($criteria));
		$pages->pageSize = param('moduleEntries_entriesPerPage', 10);
		$pages->applyLimit($criteria);

		$dependency = new CDbCacheDependency('SELECT MAX(date_updated) FROM {{entries}}');

		$criteria->with = array('image');
		$items = $this->cache(param('cachingTime', 1209600), $dependency)->findAll($criteria);

		return array(
			'items' => $items,
			'pages' => $pages,
		);
	}

	public static function getLastNews(){
		if(self::$_lastEntries === null){
			$criteriaNews = new CDbCriteria();
			$criteriaNews->addCondition('t.category_id = '.EntriesCategory::NEWS_CATEGORY_ID);
			$criteriaNews->limit = 4;
			$criteriaNews->order = 't.date_created DESC';
			$criteriaNews->with = array('image');

			self::$_lastEntries = Entries::model()->findAll($criteriaNews);
		}
		return self::$_lastEntries;
	}
	
	public function getTagLinks() {
		$links = array();
		
		if(issetModule('seo') && param('genFirendlyUrl')){
			$catUrlName = 'content'.EntriesCategory::DELIMITER_URL.$this->category_id;
			
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'Entries');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field){
					$entriesCategoriesRoute = EntriesCategory::getEntriesCategoryRoute();													
					
					if (is_array($entriesCategoriesRoute) && count($entriesCategoriesRoute)) {
						foreach($entriesCategoriesRoute as $catInfo) {
							foreach($catInfo as $info) {										
							if ($info['catId'] == $this->category_id && $info['lang'] == Yii::app()->language) {								
									$catUrlName = $info['url'];
								break;
							}
							elseif ($info['catId'] == $this->category_id) {
									$catUrlName = $info['url'];
								}
							}
						}
					}		
				}
			}
		}
		else {
			$catUrlName = array_key_exists($this->category_id, EntriesCategory::$_freeRules) ? EntriesCategory::$_freeRules[$this->category_id] : 'content'.EntriesCategory::DELIMITER_URL.$this->category_id;
		}
		
		foreach(EntriesTags::string2array($this->tags) as $tag) {
			$links[]=CHtml::link(CHtml::encode($tag), Yii::app()->createAbsoluteUrl('/entries/main/index', array('catUrlName' => $catUrlName, 'tag' => $tag)));
		}
		
		return $links;
	}

	public function normalizeTags($attribute,$params) {
		$this->tags = EntriesTags::array2string(array_unique(EntriesTags::string2array($this->tags)));
	}
	
	public static function getAllEntriesForRoute($val = null) {
		if (self::$_allEntriesForRoute === null) {
			if(issetModule('seo') && param('genFirendlyUrl')){
				$entriesSeoList = SeoFriendlyUrl::model()->findAllByAttributes(array('model_name' => 'Entries'));				
				$entries = Yii::app()->db->createCommand('SELECT category_id, id FROM {{entries}}')->queryAll();
				if ($entries)
					$entries = CHtml::listData($entries, 'id', 'category_id');
				
				if ($entriesSeoList && is_array($entriesSeoList) && is_array($entries)) {
					$langs = Lang::getActiveLangs();
					foreach($entriesSeoList as $entry) {
						foreach ($langs as $lang) {
							if (isset($entries[$entry->model_id]))
								self::$_allEntriesForRoute[$entry->{'url_' . $lang}] = array('id' => $entry->model_id, 'catId' => $entries[$entry->model_id]);
						}
					}
				}
			}
			else {
				$sql = 'SELECT category_id, id FROM {{entries}}';
				$result = Yii::app()->db->createCommand($sql)->queryAll();

				if ($result)
					self::$_allEntriesForRoute = CHtml::listData($result, 'id', 'category_id');
			}
		}
		
		if ($val && is_array(self::$_allEntriesForRoute) && array_key_exists($val, self::$_allEntriesForRoute))
			return self::$_allEntriesForRoute[$val];
		
		return self::$_allEntriesForRoute;
	}
	
	public function getUrl() {
		if(issetModule('seo') && param('genFirendlyUrl')){
			$catUrlName = 'content'.EntriesCategory::DELIMITER_URL.$this->category_id;
			
			$seo = SeoFriendlyUrl::getForUrl($this->id, 'Entries');

			if($seo){
				$field = 'url_'.Yii::app()->language;
				if($seo->$field){
					$entriesCategoriesRoute = EntriesCategory::getEntriesCategoryRoute();				
					
					if (is_array($entriesCategoriesRoute) && count($entriesCategoriesRoute)) {
						foreach($entriesCategoriesRoute as $catInfo) {
							foreach($catInfo as $info) {										
							if ($info['catId'] == $this->category_id && $info['lang'] == Yii::app()->language) {								
									$catUrlName = $info['url'];
								break;
							}
							elseif ($info['catId'] == $this->category_id) {
									$catUrlName = $info['url'];
								}
							}
						}
					}		
										
					return Yii::app()->createAbsoluteUrl('/entries/main/view', array(
						'url' => $seo->$field . ( param('urlExtension') ? '.html' : '' ),
						'catUrlName' => $catUrlName,
					));
				}
			}
		}
		
		return Yii::app()->createAbsoluteUrl('/entries/main/view', array(
			'id' => $this->id,
			'catUrlName' => array_key_exists($this->category_id, EntriesCategory::$_freeRules) ? EntriesCategory::$_freeRules[$this->category_id] : 'content'.EntriesCategory::DELIMITER_URL.$this->category_id,
		));
	}
	
	public static function getEntriesRoute() {	
		if (oreInstall::isInstalled()) {
			if (self::$_allEntriesRoutes === null) {
				$entriesList = self::getAllEntriesForRoute();
				$entriesCategoriesRoute = EntriesCategory::getEntriesCategoryRoute();	
							
				if(issetModule('seo') && param('genFirendlyUrl')){					
					if ($entriesList && is_array($entriesList)) {
						foreach($entriesList as $url => $info) {
							$catUrlName = 'content'.EntriesCategory::DELIMITER_URL.$info['catId'];
							
							if (is_array($entriesCategoriesRoute) && count($entriesCategoriesRoute)) {
								foreach($entriesCategoriesRoute as $catInfo) {
									foreach($catInfo as $item) {										
										if ($info['catId'] == $item['catId'] && $item['lang'] == Yii::app()->language) {								
											$catUrlName = $item['url'];
										break;
									}
										elseif ($info['catId'] == $item['catId']) {
											$catUrlName = $item['url'];
										}
									}
								}
							}
							
							self::$_allEntriesRoutes[$url] = $catUrlName;
						}
					}
				}
				else {
					if ($entriesList && is_array($entriesList)) {
						foreach($entriesList as $url => $catId) {
							if (is_array($tmp = array_keys($entriesCategoriesRoute, $catId))) {
								if (isset($tmp[0]))
									self::$_allEntriesRoutes[$url] = $tmp[0];
							}
						}
					}
				}
			}
		}
				
		return self::$_allEntriesRoutes;
	}
}