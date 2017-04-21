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
	public $modelName = 'Themes';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_settings_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionAdmin(){
		parent::actionAdmin();
	}

	public function actionSetDefault(){
        if(demo()){
            throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
        }

		$id = (int) Yii::app()->request->getPost('id');

		$model = Themes::model()->findByPk($id);
		$model->setDefault();

		// delete assets js cache
		ConfigurationModel::clearGenerateJSAssets();

		Yii::app()->end();
	}

    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        if(isset($_POST["{$this->modelName}"])){
            $model->attributes = $_POST["{$this->modelName}"];
            $newImage = isset($_FILES['Themes']) && $_FILES['Themes']['name']['upload_img'];
            if($newImage){
                // delete old image
                $model->delImage();
                $model->scenario = 'upload';
            }

            if($model->validate()) {
                if($newImage){
                    $model->upload = CUploadedFile::getInstance($model,'upload_img');
                    $model->bg_image = md5(uniqid()).'.'.$model->upload->extensionName;
                }

                if($model->save()){
                    if($newImage) {
                        $model->upload->saveAs(Yii::getPathOfAlias($model->path) . '/' . $model->bg_image);

                        Yii::app()->user->setFlash(
                            'success', tt('Image successfully added', 'themes')
                        );
                    }else{
                        Yii::app()->user->setFlash(
                            'success', tc('Success')
                        );
                    }

                    $this->refresh();
                }
            }
        }

        $this->render('update', array('model' => $model));
    }

    public function actionDeleteImg($id){
        $model = $this->loadModel($id);

        $model->delImage();

        $model->bg_image = '';
        if($model->update('bg_image')){
            Yii::app()->user->setFlash(
                'success', tt('Image successfully deleted', 'themes')
            );
        } else {
            Yii::app()->user->setFlash(
                'error', HAjax::implodeModelErrors($model)
            );
        }
        $this->redirect(array('update', 'id' => $id));
    }

    public function actionView($id){
        $this->redirect(array('admin'));
    }
}
