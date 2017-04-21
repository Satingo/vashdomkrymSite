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

class CustomCCaptcha extends CCaptcha {
	public function run() {
		if (param('useJQuerySimpleCaptcha', 0)) {
			$this->renderJQuerySimpleCaptchaImage();
			$this->registerClientScriptJQuerySimpleCaptchaImage();
		}
		else {
			if(self::checkRequirements('imagick') || self::checkRequirements('gd'))
			{
				$this->renderImage();
				$this->registerClientScript();
			}
			else
				throw new CException(Yii::t('yii','GD with FreeType or ImageMagick PHP extensions are required.'));
		}
	}

	public function renderJQuerySimpleCaptchaImage () {
		if(!isset($this->imageOptions['id']))
			$this->imageOptions['id'] = $this->getId();
		else
			$id = $this->imageOptions['id'];

		//echo CHtml::tag('div', array('id' => $this->imageOptions['id']));
		echo '<div id="'.$this->imageOptions['id'].'"></div>';
	}

	public function registerClientScriptJQuerySimpleCaptchaImage() {
		Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/common/js/antispam/jquerySimpleCCaptcha/jquery.simpleCaptcha.css');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->getBaseUrl(true) . '/common/js/antispam/jquerySimpleCCaptcha/jquery.simpleCaptcha.js');

		$cs=Yii::app()->clientScript;
		$id=$this->imageOptions['id'];

		$js="";
		$js.="
$(document).ready(function() {
			var parentBlockTmp = $('#".$this->imageOptions['id']."').parent();
			//var verifyCodeInput = $(parentBlockTmp).find('input[type=text]');
			var verifyCodeInput = $(parentBlockTmp).find('input[id$=\"_verifyCode\"]');

			if (verifyCodeInput && typeof verifyCodeInput !== 'undefined') {
				verifyCodeInput.hide();
			}

			$('#".$this->imageOptions['id']."')
				.simpleCaptcha({
					numImages: 4,
					introText: '".tc('jquerySimpleCaptchaIntroText')."',
					allowRefresh : false,
					scriptPath: '".$this->getController()->createUrl('/antispam/jquerysimpleccaptcha/renderimages')."',
					language : '".Yii::app()->language."',
					verifyCodeInput : verifyCodeInput
				});

		});
";
		$cs->registerScript('Yii.CCaptcha#'.$id,$js);
	}
}
