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

class Themes extends ParentModel {
	private static $_defaultTheme;
	private static $_params;

    public $path = 'webroot.uploads.rkl';
    public $urlRoute = 'uploads/rkl';
    public $upload;
    public $upload_img;
    public $maxHeight;
    public $maxWidth;
    public $supportExt = 'jpg, png, gif';
    public $fileMinSize = 10485; /* 1024 * 1024 * 0.01 - 10 KB */
    public $fileMaxSize = 10485760; /* 1024 * 1024 * 10 - 10 MB */
	
	const ADDITIONAL_VIEW_FULL_WIDTH_SLIDER = 1;
	const ADDITIONAL_VIEW_FULL_WIDTH_MAP = 2;

    public function init() {
        $fileMaxSize['set'] = $this->fileMaxSize;
        $fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
        $fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));
        $this->fileMaxSize = min($fileMaxSize);

        return parent::init();
    }

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{themes}}';
	}

	public function behaviors(){
		return array(
			'AutoTimestampBehavior' => array(
				'class' => 'zii.behaviors.CTimestampBehavior',
				'createAttribute' => 'date_updated',
				'updateAttribute' => 'date_updated',
			),
		);
	}

	public function rules() {
		return array(
            array(
                'upload_img', 'file',
                'types' => "{$this->supportExt}",
                'minSize' => $this->fileMinSize,
                'maxSize' => $this->fileMaxSize,
                'tooSmall' => Yii::t('module_themes', 'The file was less than {size}MB. Please upload a larger file.', array('{size}' => $this->fileMinSize)),
                'tooLarge' => Yii::t('module_slider', 'The file was larger than {size}MB. Please upload a smaller file.', array('{size}' => $this->fileMaxSize)),
                'allowEmpty' => true,
                'on' => 'upload',
            ),
			array('title, is_default, date_updated', 'required'),
			array('additional_view, is_default', 'numerical', 'integerOnly' => true),
			array('title', 'length', 'max' => 20),
			array('color_theme, bg_image', 'length', 'max' => 100),
			array('id, title, is_default, date_updated', 'safe', 'on' => 'search'),
		);
	}

	public function relations() {
		return array();
	}

	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'title' => tt('title'),
			'additional_view' => tc('Addition'),
			'is_default' => tt('Is Default'),
			'color_theme' => tt('Color theme', 'themes'),
			'bg_image' => tt('Background image', 'themes'),
			'upload_img' => tt('Background image', 'themes'),
			'date_updated' => tc('Last updated on'),
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('is_default', $this->is_default);
		$criteria->compare('date_updated', $this->date_updated, true);
		$criteria->order = 'title ASC';


		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => param('adminPaginationPageSize', 20),
			),
		));
	}

	public function beforeSave() {
		if ($this->scenario == 'set_default') {
			$sql = "UPDATE " . $this->tableName() . " SET is_default=0 WHERE id !=" . $this->id;
			Yii::app()->db->createCommand($sql)->execute();
		}

		return parent::beforeSave();
	}

	public static function getDefaultTheme() {
		if (!isset(self::$_defaultTheme)) {
			$sql = "SELECT title, additional_view, color_theme, bg_image FROM {{themes}} WHERE is_default=1";			
            $data = Yii::app()->db->createCommand($sql)->queryRow();
			
			self::$_params['additional_view'] = $data['additional_view'];
			
            if(demo()){
                if(isset($_GET['theme']) &&  array_key_exists($_GET['theme'], Themes::getColorThemesList())){
                    self::$_params['color_theme'] = $_GET['theme'];
                } else {
                    self::$_params['color_theme'] = isset(Yii::app()->request->cookies['theme']) ? (string)Yii::app()->request->cookies['theme'] : $data['color_theme'];
                }
            }else{
                self::$_params['color_theme'] = $data['color_theme'];
            }

            if(self::$_params['color_theme'] == 'color-green.css'){
                self::$_params['bg_image'] = $data['bg_image'] ? $data['bg_image'] : 'demo-cloud.jpg';
            } else {
                self::$_params['bg_image'] = $data['bg_image'];
            }

			self::$_defaultTheme = $data['title'];
		}
		return self::$_defaultTheme;
	}

	public function getIsDefaultHtml() {
		if ($this->is_default == 1) {
			$onclick = 'return false;';
		} else {
			$onclick = "changeDefault(" . $this->id . ");";
		}
		return CHtml::radioButton("is_default", ($this->is_default == 1), array('onclick' => $onclick));
	}

	public function setDefault()
	{
		if ($this->is_default) {
			return false;
		}

		$this->scenario = 'set_default';
		$this->is_default = 1;
		$this->update('is_default');

		return true;
	}

    public static function getParam($key){
        return isset(self::$_params[$key]) ? self::$_params[$key] : '';
    }

    public static function getColorThemesList(){
        return array(
            '-' => 'Default',
            'color-fresh.css' => 'Fresh',
            'color-bagway-gradient.css' => 'Bagway gradient',
            'color-green.css' => 'Green',
			'color-sandstone.css' => 'Sandstone',
        );
    }
	
	public static function getAdditionalViewList($translateFromMessageFile = false) {
		return array(
            0 => (!$translateFromMessageFile) ? tc('No') : Yii::t('module_install', 'No', array(), 'messagesInFile', Yii::app()->language),
            self::ADDITIONAL_VIEW_FULL_WIDTH_SLIDER => (!$translateFromMessageFile) ? tt('Use_full_width_slider_homepage') : Yii::t('module_install', 'Use_full_width_slider_homepage', array(), 'messagesInFile', Yii::app()->language),
			self::ADDITIONAL_VIEW_FULL_WIDTH_MAP => (!$translateFromMessageFile) ? tt('Use_full_width_map_homepage') : Yii::t('module_install', 'Use_full_width_map_homepage', array(), 'messagesInFile', Yii::app()->language),
        );
	}

    public static function getBgUrl($bgImage = null){
        $bgImage = $bgImage ? $bgImage : Themes::getParam('bg_image');
        $model = self::model();
        $path = Yii::getPathOfAlias($model->path);
        $filePath = $path.DIRECTORY_SEPARATOR.$bgImage;
        if($bgImage && file_exists($filePath)){
            return Yii::app()->baseUrl . '/' .$model->urlRoute . '/' . $bgImage;
        } else {
            return false;
        }
    }

    public function delImage(){
        $path = Yii::getPathOfAlias($this->path);
        $filePath = $path.DIRECTORY_SEPARATOR.$this->bg_image;
        if($this->bg_image && file_exists($filePath)){
            return unlink($filePath);
        }
        return false;
    }
}