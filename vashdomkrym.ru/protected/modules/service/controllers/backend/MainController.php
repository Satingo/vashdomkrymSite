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

class MainController extends ModuleAdminController{
	public $modelName = 'Service';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('service_site_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

    public function actionAdmin(){
		$model = $this->loadModel(Service::SERVICE_ID);
		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			if(demo()){
				throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
			}

			$model->attributes=$_POST[$this->modelName];
			if($model->save()){
				Yii::app()->user->setFlash('success', tt('success_saved', 'service'));
			}
			else
				Yii::app()->user->setFlash('error', tt('failed_save_try_later', 'service'));
		}

		$this->render('admin', array('model' => $model));
    }

	public function actionDoClear() {
		if (Yii::app()->request->isAjaxRequest) {
			$target = Yii::app()->request->getParam('target');

			$text = '';
			//$text = '<div class="flash-error">'.tc('Error. Repeat attempt later').'</div>';

			if(in_array($target, array('assets', 'runtime'))) {
				$cacheDir = '';
				switch ($target) {
					case 'assets':
						$cacheDir = Yii::app()->assetManager->basePath;
						break;
					case 'runtime':
						$cacheDir = Yii::app()->runtimePath;

						Yii::app()->cache->flush();
						break;
				}

				if ($cacheDir && is_dir($cacheDir)) {
					$excludeFiles = array('.empty', /* 'state.bin', */ 'already_install');
					$excludeDirs = array('cache', 'HTML', 'minScript', 'URI');

					$this->rrmdir($cacheDir, $excludeFiles, $excludeDirs);
					$text = '<div class="flash-success">'.Yii::t('module_service', 'Cache files in the folder {folder} have been successfully removed', array('{folder}' => $cacheDir)).'</div>';
				}
			}

			echo $text;
			Yii::app()->end();
		}
	}

	function rrmdir($dir, $excludeFiles = array(), $excludeDirs = array(), $depth = 0) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			if ($objects) {
				foreach ($objects as $object) {
					if ($object != "." && $object != ".." && !in_array($object, $excludeFiles)) {
						if (filetype($dir . DIRECTORY_SEPARATOR . $object) == "dir") {
							$depth = $depth + 1;
							$this->rrmdir($dir . DIRECTORY_SEPARATOR . $object, $excludeFiles, $excludeDirs, $depth);
						}
						else {
							@unlink($dir . DIRECTORY_SEPARATOR . $object);
						}
					}
				}
			}

			reset($objects);
			if (!in_array(substr($dir, strrpos($dir, '/') + 1), $excludeDirs) && $depth)
				@rmdir($dir);
		}
	}
}