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

class MainController extends ModuleUserController {
	public $modelName = 'Lang';

    public function actionAjaxTranslate(){
        if(!Yii::app()->request->isAjaxRequest)
            throw404();

        $fromLang = Yii::app()->request->getPost('fromLang');
        $fields = Yii::app()->request->getPost('fields');
		$errors = false;
		$translateField = array();
		
        if(!$fromLang || !$fields)
            throw new CException('Lang no req data');

        $translate = new MyMemoryTranslated();
        $fromVal = $fields[$fromLang];
		
        foreach($fields as $lang=>$val){
            if($lang == $fromLang)
                continue;
			
			if ($answer = $translate->translateText($fromVal, $fromLang, $lang))
				$translateField[$lang] = $answer;
			else
				$errors = true;
        }

		if ($errors) {
			echo json_encode(array(
				'result' => 'no',
				'fields' => ''
			));
		}
        else {
			echo json_encode(array(
				'result' => 'ok',
				'fields' => $translateField
			));
		}
        Yii::app()->end();
    }
}