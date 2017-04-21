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

Yii::import('zii.widgets.CMenu');

class CustomMenu extends CMenu {
	protected function renderMenuItem($item){
		if(isset($item['url'])){
			if(isset($item['linkOptions']['submit'])){
				$item['linkOptions']['csrf'] = true;
			}

			$label=$this->linkLabelWrapper===null ? $item['label'] : CHtml::tag($this->linkLabelWrapper, $this->linkLabelWrapperHtmlOptions, $item['label']);
			return CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array());
		}
		else
			return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
	}

	protected function isItemActive($item,$route) {
		if(isset($item['url']) && is_array($item['url'])) {			
			if (!strcasecmp(trim($item['url'][0],'/'),$route)) {
				unset($item['url']['#']);
				if(count($item['url'])>1)
				{
					foreach(array_splice($item['url'],1) as $name=>$value)
					{
						if(!isset($_GET[$name]) || $_GET[$name]!=$value)
							return false;
					}
				}
				return true;
			}
			
			# for module entries
			if (isset($item['url'][0]) && isset($_GET) && isset($_GET['catUrlName']) && trim($item['url'][0],'/') === $_GET['catUrlName'])
				return true;
			
			// for other					
			if (isset($item['url'][0]) && $route && strstr($item['url'][0], '/')) {
				$trimRoute = trim($route,'/');
				$trimUrl = trim($item['url'][0],'/');							
				# remove all after last slash and compare
				if (substr($trimRoute, 0, strrpos($trimRoute, '/') + 1) == substr($trimUrl, 0, strrpos($trimUrl, '/') + 1))
					return true;
			}
		}
		elseif (isset($item['url']) && !is_array($item['url'])) {
			$activeModule = (Yii::app()->getController()->getModule() && Yii::app()->getController()->getModule()->getId()) ? Yii::app()->getController()->getModule()->getId() : '';
			$tUrl = trim($item['url'], '/');
			$tUrlExplode = explode('/', $tUrl);
			$tUrl = (count($tUrlExplode) >  1) ? $tUrlExplode[count($tUrlExplode) - 1] : null;

			if ($activeModule == 'infopages' && is_array(Yii::app()->getController()->getActionParams())) {
				if ($tUrl) {
					$activeMenuPage = Yii::app()->getController()->getActionParams();

					if (is_array($activeMenuPage) && array_key_exists('url', $activeMenuPage)) {
						if ($activeMenuPage['url'] == $tUrl) {
							return true;
						}
					}
				}

				return false;
			}
			elseif (isset($_GET) && isset($_GET['catUrlName']) && $tUrl === $_GET['catUrlName']) { ## for module entries
				return true;
			}
		}
		return false;
	}
}