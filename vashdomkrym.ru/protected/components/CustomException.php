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

class CustomException extends Exception{
	protected $message=array();

	private function renderRecursive($arr){
		foreach($arr as $el){
			if(is_array($el)){
				echo CHtml::openTag('ul');
				$this->renderRecursive($el);
				echo CHtml::closeTag('ul');
			}else{
				echo CHtml::openTag('li');
				echo $el;
				echo CHtml::closeTag('li');
			}
		}
	}

	public function __construct($message = array(), $code = 0){
		if(is_array($message)){
			ob_start();
			echo CHtml::openTag('ul');
			$this->renderRecursive($message);
			echo CHtml::closeTag('ul');
			$message=ob_get_contents();
			ob_end_clean();
		}
		$this->message=$message;
		$this->code=$code;
	}
}