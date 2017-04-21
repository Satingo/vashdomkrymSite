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
	public $modelName = 'Entries';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('entries_admin')",
				'actions'=>array('admin', 'create', 'update', 'view', 'delete', 'deleteimg', 'suggesttags', 'move', 'activate', 'itemsselected', 'sortitems', 'resortitems', 'ajaxeditcolumn'),
			),
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('entries_news_product_admin')",
				'actions'=>array('product'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionCreate(){
		$model = new $this->modelName;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){
			$model->attributes=$_POST[$this->modelName];
			$model->entriesImage = CUploadedFile::getInstance($model,'entriesImage');
			if($model->save()){
				Yii::app()->user->setFlash('success', tc('Success'));
				#$this->redirect(array('update','id'=>$model->id));
				$this->redirect(array('admin'));
			}
		}

		$this->render('create', array('model'=>$model));
	}

	public function actionUpdate($id){
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->modelName])){			
			$model->attributes=$_POST[$this->modelName];
			$model->entriesImage = CUploadedFile::getInstance($model,'entriesImage');
			if($model->save()){
				Yii::app()->user->setFlash('success', tc('Success'));
				#$this->redirect(array('update','id'=>$model->id));
				$this->redirect(array('admin'));
			}
		}

		$this->render('update', array('model'=>$model));
	}

    public function actionProduct(){
        Yii::app()->user->setState('menu_active', 'entries.product');
		
		NewsProduct::getProductNews();

        $model = NewsProduct::model();
		$result = $model->getAllWithPagination();
		
		$sql = 'UPDATE {{news_product}} SET is_show = 1 WHERE is_show = 0';
		Yii::app()->db->createCommand($sql)->execute();

		$this->render('news_product', array(
			'items' => $result['items'],
			'pages' => $result['pages'],
		));
    }

	public function actionDeleteImg() {
		$entryId = Yii::app()->request->getParam('id');
		$imageId = Yii::app()->request->getParam('imId');

		if ($entryId && $imageId) {
			$entryModel = Entries::model()->findByPk($entryId);
			if ($entryModel->image_id != $imageId)
				throw404();

			$entryModel->image_id = 0;
			$entryModel->update('image_id');

			$imageModel = EntriesImage::model()->findByPk($imageId);
			$imageModel->delete();

			$this->redirect(array('/entries/backend/main/update', 'id' => $entryId));
		}
		throw404();
	}
	
	public function actionSuggestTags() {
		if(isset($_GET['q']) && ($keyword=trim($_GET['q']))!=='') {
			$tags = EntriesTags::model()->suggestTags($keyword);
			if($tags!==array())
				echo implode("\n", $tags);
		}
	}
}