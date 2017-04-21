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

class RssWidget extends CWidget {
	public $criteria;

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'rss'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'rss'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.rss.views');
	}

	public function run() {
		if(!$this->criteria){
			throw404();
		}

        $this->criteria->order = 't.date_created DESC';

		$subCriteria = clone $this->criteria;
		$subCriteria->select = 'MAX(t.date_updated) as date_updated';

		$maxDateUpdated = Apartment::model()->find($subCriteria);
		$maxDateUpdated = $maxDateUpdated->date_updated;

		if (!$maxDateUpdated)
			$maxDateUpdated = date("r");

		header('Content-type: text/xml');
		header('Pragma: public');
		header('Cache-control: private');
		header('Expires: -1');

		$xmlWriter = new XMLWriter();
		$xmlWriter->openMemory();
		$xmlWriter->setIndent(true);
		$xmlWriter->startDocument('1.0', 'UTF-8');
		$xmlWriter->startElement('rss');
		$xmlWriter->writeAttribute('version', '2.0');
		$xmlWriter->startElement("channel");

		$xmlWriter->writeElement('title', tt('listings_from', 'rss').' '.CHtml::encode(Yii::app()->name));
		$xmlWriter->writeElement('link', Yii::app()->getBaseUrl(true));
		$xmlWriter->writeElement('description', tt('description_rss_from', 'rss'));
		$xmlWriter->writeElement('lastBuildDate', $this->getDateFormat(strtotime($maxDateUpdated)));

		$this->prepareItems($xmlWriter);

		$xmlWriter->endElement(); // end channel
		$xmlWriter->endElement(); // end rss
		echo $xmlWriter->outputMemory();

		Yii::app()->end();
	}

	private function prepareItems($xmlWriter = null) {
		$this->criteria->limit = param('module_rss_itemsPerFeed', 20);
		$items = Apartment::model()->findAll($this->criteria);

		if($items){
			foreach($items as $item){
				$xmlWriter->startElement("item");
				$xmlWriter->writeElement('title', CHtml::encode($item->getStrByLang('title')));
				$xmlWriter->writeElement('link', $item->getUrl());
				$xmlWriter->writeElement('description', $this->getDescription($item));
				$xmlWriter->writeElement('pubDate', $this->getDateFormat(strtotime($item->date_updated)));
				$xmlWriter->endElement(); // end item
			}
		}
	}

	private function getDescription($item = null) {
		if ($item) {
			return $this->render('_description', array('item' => $item), true);
		}
		return false;
	}

	private function getDateFormat($date = null) {
		if (!$date)
			$date = date("r");

		return  date('D, d M Y H:i:s O', $date);
	}
}