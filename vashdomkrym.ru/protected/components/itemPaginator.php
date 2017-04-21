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

class itemPaginator extends CLinkPager {
	public $htmlOption = array();
	public $showHidden = false;

	public function init(){
		$this->cssFile = Yii::app()->theme->baseUrl.'/css/pager.css';
		parent::init();
	}

	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($hidden || $selected) {
			if ($this->showHidden) {
				$class.=' '.self::CSS_SELECTED_PAGE;
			}
			else {
				$class.=' '.($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);
			}
		}
		if ($hidden) {
			return '<li class="'.$class.'">'.CHtml::link($label, 'javascript: void(0);'/*, $this->htmlOption*/).'</li>';
		}
		else
			return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page), $this->htmlOption).'</li>';
	}
}