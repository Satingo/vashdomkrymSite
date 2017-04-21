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
	public $modelName = 'ApartmentObjType';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_reference_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id){
		$this->redirect(array('admin'));
	}
	public function actionIndex(){
		$this->redirect(array('admin'));
	}

	public function actionAdmin(){
		$this->getMaxSorter();
		$this->getMinSorter();
		parent::actionAdmin();
	}

	public function actionCreate(){
		$model = new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			if($model->validate()) {

				$model->iconUpload = CUploadedFile::getInstance($model, 'icon_file');
				if ($model->iconUpload) {
					$iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';

					//$model->icon_file = $model->iconUpload->name;
					$model->icon_file = md5(uniqid()).'.'.$model->iconUpload->extensionName;

					// загружаем и ресайзим иконку
					$model->iconUpload->saveAs($iconUploadPath.$model->icon_file);

					Yii::import('application.extensions.image.Image');
					$icon = new Image($iconUploadPath.$model->icon_file);

					$icon->resize(ApartmentObjType::MAP_ICON_MAX_WIDTH, ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					$icon->save();
				}

				if($model->save(false)){
					$this->redirect(array('admin'));
				}
			}
		}

		$this->render('create',array_merge(
			array('model'=>$model),
			$this->params
		));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$isUploadIcon = false;

			$iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';
			$model->iconUpload = CUploadedFile::getInstance($model, 'icon_file');

			if ($model->iconUpload)
				$isUploadIcon = true;

			if ($isUploadIcon) {
				if ($model->icon_file) { // если уже есть - удаляем старую иконку
					$oldIconPath = $iconUploadPath.$model->icon_file;
					if (file_exists($oldIconPath)) {
						@unlink($oldIconPath);
					}
				}
			}

			$model->attributes=$_POST[$this->modelName];

			if($model->validate()) {
				if ($isUploadIcon) {
					//$model->icon_file = $model->iconUpload->name;
					$model->icon_file = md5(uniqid()).'.'.$model->iconUpload->extensionName;

					// загружаем и ресайзим иконку
					$model->iconUpload->saveAs($iconUploadPath.$model->icon_file);

					Yii::import('application.extensions.image.Image');
					$icon = new Image($iconUploadPath.$model->icon_file);

					$icon->resize(ApartmentObjType::MAP_ICON_MAX_WIDTH, ApartmentObjType::MAP_ICON_MAX_HEIGHT);
					$icon->save();
				}

				if($model->save(false)){
					$this->redirect(array('admin'));
				}
			}
		}

		$this->render('update',
			array_merge(
				array('model'=>$model),
				$this->params
			)
		);
	}

    public function actionDelete($id){

        // Не дадим удалить последний тип
        if(ApartmentObjType::model()->count() <= 1){
            if(!isset($_GET['ajax'])){
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
            }
            Yii::app()->end();
        }

        parent::actionDelete($id);
    }

	public function actionDeleteIcon($id = null) {
	    if ($id) {
		 	$model = $this->loadModel($id);
		    if ($model->icon_file) {
			    $iconUploadPath = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$model->iconsMapPath.'/';

			    $oldIconPath = $iconUploadPath.$model->icon_file;
			    if (file_exists($oldIconPath)) {
				    @unlink($oldIconPath);
			    }

			    $model->icon_file = '';
			    $model->update(array('icon_file'));
		    }
	    }
		$this->redirect(array('update', 'id' => $id));
	}
}
