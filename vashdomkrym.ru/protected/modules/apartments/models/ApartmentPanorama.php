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

class ApartmentPanorama extends ParentModel {
	public $fileInstance = null;
	public $path = 'webroot.uploads.objects';
	public $url = 'uploads/objects';
	public $supportedExt = 'swf, jpg, png, gif';

	static $imageJs = false;
	static $swfJs = false;

	public $maxSize;
	public $maxSizeMb;

	public function init(){
		$fileMaxSize['postSize'] = toBytes(ini_get('post_max_size'));
		$fileMaxSize['uploadSize'] = toBytes(ini_get('upload_max_filesize'));
		$this->maxSize = min($fileMaxSize);
		$this->maxSizeMb = round($this->maxSize / (1024*1024));

		return parent::init();
	}

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return '{{apartment_panorama}}';
	}

	public function rules() {
		return array(

		);
	}

	public function relations() {
		return array(

		);
	}

	public function isFileExists(){
		$path = Yii::getPathOfAlias($this->path).DIRECTORY_SEPARATOR.$this->apartment_id;
		return file_exists($path.DIRECTORY_SEPARATOR.$this->name);
	}

	public function renderPanorama(){
		$info = pathinfo($this->name);
		$ext = $info['extension'];
		if($ext == 'swf'){
			$this->renderSwf();
		} else {
			$this->renderImage();
		}
	}

	public function renderSwf(){
		if(self::$swfJs === false){
			Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.swfobject.1-1-1.min.js');
			self::$swfJs = true;
		}
		echo '
		<div class="panorama-swf">
			<div id="swf-panorama-'.$this->id.'">
			</div>
		</div>';
		$url = Yii::app()->getBaseUrl().'/'.$this->url.'/'.$this->apartment_id.'/'.$this->name;
		Yii::app()->clientScript->registerScript('swf-panorama-'.$this->id, '
			$("#swf-panorama-'.$this->id.'").flash({
				swf: "'.$url.'",
				width: 890,
				height: 500
			});
		', CClientScript::POS_READY);
	}

	public function renderImage(){
		if(self::$imageJs === false){
			Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/jquery.panorama360.js');
			self::$imageJs = true;
		}
		echo '
		<div class="panorama-view" id="panorama-'.$this->id.'">
			<div class="panorama-container">
				<img src="'.Yii::app()->baseUrl.'/'.$this->url.'/'.$this->apartment_id.'/'.$this->name.'" alt="" data-width="'.$this->width.'" data-height="'.$this->height.'"/>
			</div>
		</div>
		';
		Yii::app()->clientScript->registerScript('panorama-'.$this->id, '
			$("#panorama-'.$this->id.'").panorama360();
		', CClientScript::POS_READY);
	}

	public function beforeSave(){
		if($this->fileInstance){
			$path = Yii::getPathOfAlias($this->path).DIRECTORY_SEPARATOR.$this->apartment_id;
			$name = $this->fileInstance->getName();

			$ext = $this->fileInstance->getExtensionName();

			while(file_exists($path.DIRECTORY_SEPARATOR.$name)){
				$name = rand(0, 9).$name;
			}

			$oldUMask = umask(0);
			if(!is_dir($path)){
				@mkdir($path, 0777, true);
			}
			umask($oldUMask);

			if($this->fileInstance->saveAs($path.DIRECTORY_SEPARATOR.$name)){
				$this->name = $name;
				if($ext == 'jpg' || $ext == 'png' || $ext == 'gif'){
					$image = new CImageHandler();
					if($image->load($path.DIRECTORY_SEPARATOR.$name)){
						$this->width = $image->getWidth();
						$this->height = $image->getHeight();
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		}

		return parent::beforeSave();
	}

	public function afterDelete(){
		@unlink(Yii::getPathOfAlias($this->path).DIRECTORY_SEPARATOR.$this->apartment_id.DIRECTORY_SEPARATOR.$this->name);
	}

	public function behaviors() {
		$arr = array();
		$arr['AutoTimestampBehavior'] = array(
			'class' => 'zii.behaviors.CTimestampBehavior',
			'createAttribute' => 'date_created',
			'updateAttribute' => null,
		);
		return $arr;
	}
}