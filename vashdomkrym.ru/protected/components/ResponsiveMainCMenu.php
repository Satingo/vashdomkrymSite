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

class ResponsiveMainCMenu extends CMenu {
	protected function renderMenu($items)
	{
		if(count($items))
		{
			echo CHtml::openTag('ul',$this->htmlOptions)."\n";
			$this->renderMenuRecursive($items, 1);
			echo CHtml::closeTag('ul');
		}
	}

	protected function renderMenuRecursive($items, $level = 1) {
		$count=0;
		$n=count($items);
		foreach($items as $item)
		{
			$count++;
			$options=isset($item['itemOptions']) ? $item['itemOptions'] : array();
			$class=array();
			if($item['active'] && $this->activeCssClass!='')
				$class[]=$this->activeCssClass;
			if($count===1 && $this->firstItemCssClass!==null)
				$class[]=$this->firstItemCssClass;
			if($count===$n && $this->lastItemCssClass!==null)
				$class[]=$this->lastItemCssClass;
			if($this->itemCssClass!==null)
				$class[]=$this->itemCssClass;
			if($class!==array())
			{
				if(empty($options['class']))
					$options['class']=implode(' ',$class);
				else
					$options['class'].=' '.implode(' ',$class);
			}

			echo CHtml::openTag('li', $options);

			$menu=$this->renderMenuItem($item);
			if(isset($this->itemTemplate) || isset($item['template']))
			{
				$template=isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template,array('{menu}'=>$menu));
			}
			else
				echo $menu;

			if(isset($item['items']) && count($item['items']))
			{
				echo "\n".'<div class="mobnav-subarrow-levels subarrow-level-'.$level.'"></div>'."\n";
				echo "\n".CHtml::openTag('ul',isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
				$this->renderMenuRecursive($item['items'], $level+1);
				echo CHtml::closeTag('ul')."\n";
			}

			echo CHtml::closeTag('li')."\n";
		}
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
