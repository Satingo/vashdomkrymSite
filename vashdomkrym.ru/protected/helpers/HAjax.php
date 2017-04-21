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

class HAjax {
	const STATUS_OK = 'ok';
	const STATUS_NONE = 'none';
	const STATUS_ERROR = 'error';

	public static function getAjaxLoadingImage(){
		return CHtml::image(Yii::app()->baseUrl.'/images/ajax-loader.gif', tc('Loading content...'));
	}

	public static function jsonError($msg = 'Error', $params = array()){
        $msg = $msg == 'Error' ? tc('Error') : $msg;
		$params = CMap::mergeArray(array(
			'status' => self::STATUS_ERROR,
			'msg' => $msg
		), $params);
		echo CJSON::encode($params);
		Yii::app()->end();
	}


	public static function jsonOk($msg = 'Success', $params = array()){
        $msg = $msg == 'Success' ? tc('Success') : $msg;
		$params = CMap::mergeArray(array(
			'status' => self::STATUS_OK,
			'msg' => $msg
		), $params);
		echo CJSON::encode($params);
		Yii::app()->end();
	}

	public static function jsonNone(){
		echo CJSON::encode(array(
			'status' => self::STATUS_NONE,
		));
		Yii::app()->end();
	}

	public static function implodeModelErrors($model, $glue = '<br><br>'){
		if(empty($model->errors) || !is_array($model->errors)){
			return '';
			//throw new CException('HAjax::implodeModelErrors - нет модели');
		}

		$errorArray = array();

		foreach($model->errors as $field => $errors){
			$errorArray[] = implode($glue, $errors);
		}

		return implode($glue, $errorArray);
	}

	public static function loadScrips($viewUrl = '', $scripts){

		foreach($scripts as $script){
			$jsUrl = $viewUrl . '/' . $script . '.js';
			echo "<script src=\"$jsUrl\"></script>";
		}

	}
}
