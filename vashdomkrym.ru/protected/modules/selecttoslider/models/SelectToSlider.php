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

class SelectToSlider extends CFormModel {

	public $modulePathBase;
	public $sitePath;
	public $assetsPath;

	public function init() {
		$this->preparePaths();
		$this->publishAssets();
	}

	public function preparePaths() {
		$this->modulePathBase = dirname(__FILE__) . '/../';
		$this->sitePath = Yii::app()->basePath . '/../';
		$this->assetsPath = $this->modulePathBase . '/assets';

	}

	public function publishAssets() {
		if (is_dir($this->assetsPath)) {
			$baseUrl = Yii::app()->assetManager->publish($this->assetsPath);

			$cs = Yii::app()->clientScript;

//			$cs->registerCssFile(Yii::app()->theme->baseUrl.'/css/ui/jquery-ui.multiselect.css');
//			$cs->registerCssFile($baseUrl . '/css/redmond/jquery-ui-1.7.1.custom.css');
//			$cs->registerCssFile($baseUrl . '/css/ui.slider.extras.css');
//			$cs->registerCssFile($baseUrl . '/css/search-form-select.css');
//			Yii::app()->clientScript->registerCoreScript('jquery-ui');

			$cs->registerScriptFile($baseUrl.'/js/selectToUISlider.jQuery.js', CClientScript::POS_HEAD);

			$cs->registerScript('fixToolTipColor', '
				function fixToolTipColor(){
					/*grab the bg color from the tooltip content - set top border of pointer to same*/
					$(".ui-tooltip-pointer-down-inner").each(function(){
						var bWidth = $(".ui-tooltip-pointer-down-inner").css("borderTopWidth");
						var bColor = $(this).parents(".ui-slider-tooltip").css("backgroundColor")
						$(this).css("border-top", bWidth+" solid "+bColor);
					});
				}
			', CClientScript::POS_READY);
		}
	}
}