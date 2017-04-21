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

class MainController extends ModuleUserController{
	public $modelName = 'Apartment';


	public function actionUpload($id){
		$model = $this->checkOwner($id);

		$maxImgs = 0; # unlimited
		$currImgCount = 0;

		if (issetModule('tariffPlans') && issetModule('paidservices')) {
			$sql = 'SELECT COUNT(id) FROM {{images}} WHERE id_object = '.$model->id;
			$currImgCount = Yii::app()->db->createCommand($sql)->queryScalar();

			$userTariffInfo = TariffPlans::getTariffInfoByUserId($model->owner_id);
			$maxImgs = $userTariffInfo['limitPhotos'];

			if (Yii::app()->user->checkAccess("backend_access")) # admin or moderator
				$maxImgs = 0;
		}

		if ($maxImgs > 0 && $currImgCount >= $maxImgs) {
			$result['error'] = Yii::t("module_tariffPlans", "You are trying to download more than {num} pictures ( your tariff limit )", array("{num}" => $maxImgs));

			$result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			echo $result;
			Yii::app()->end();
		}

		Yii::import("ext.EAjaxUpload.qqFileUploader");

		$allowedExtensions = param('allowedImgExtensions', array('jpg', 'jpeg', 'gif', 'png'));

		//$sizeLimit = param('maxImgFileSize', 8 * 1024 * 1024);
		$sizeLimit = Images::getMaxSizeLimit();

		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

		$path = Yii::getPathOfAlias('webroot.uploads.objects.'.$model->id.'.'.Images::ORIGINAL_IMG_DIR);
		$pathMod = Yii::getPathOfAlias('webroot.uploads.objects.'.$model->id.'.'.Images::MODIFIED_IMG_DIR);

		$oldUMask = umask(0);
		if(!is_dir($path)){
			@mkdir($path, 0777, true);
		}
		if(!is_dir($pathMod)){
			@mkdir($pathMod, 0777, true);
		}
		umask($oldUMask);

		if(is_writable($path) && is_writable($pathMod)){
			touch($path.DIRECTORY_SEPARATOR.'index.htm');
			touch($pathMod.DIRECTORY_SEPARATOR.'index.htm');

			$result = $uploader->handleUpload($path.DIRECTORY_SEPARATOR, false, uniqid());

			if(isset($result['success']) && $result['success']){
				$resize = new CImageHandler();
				if($resize->load($path.DIRECTORY_SEPARATOR.$result['filename'])){
					$resize->thumb(param('maxImageWidth', 1024), param('maxImageHeight', 768), Images::KEEP_PHOTO_PROPORTIONAL)
						->save();

					$image = new Images();
					$image->id_object = $model->id;
					$image->id_owner = $model->owner_id;
					$image->file_name = $result['filename'];

                    if($image->save() && $model->hasAttribute('count_img')){
                        $model->count_img++;
                        $model->update('count_img');

						if (issetModule('historyChanges')) {
							HistoryChanges::addApartmentInfoToHistory('add_image', $model->id, 'create');
						}
                    }
				} else {
					$result['error'] = 'Wrong image type.';
					@unlink($path.DIRECTORY_SEPARATOR.$result['filename']);
				}
			}
		} else {
			$result['error'] = 'Access denied.';
		}

		// to pass data through iframe you will need to encode all html tags
		$result = htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		echo $result;
	}

	public function	checkOwner($id){
		$model = $this->loadModel($id);
		if(!$model || (!Yii::app()->user->checkAccess('backend_access') && !$model->isOwner())){
			throw404();
		}
		return $model;
	}

	public function	checkOwnerImage($id){
		$this->modelName = 'Images';
		$model = $this->loadModel($id);

		if(!$model)
			throw404();

		if (Yii::app()->user->type == User::TYPE_AGENCY) {
			$user = User::model()->findByPk($model->id_owner);
			if(!($user && ($user->agency_user_id == Yii::app()->user->id || Yii::app()->user->id == $model->id_owner)))
				throw404();
		} else {
			if(!Yii::app()->user->checkAccess('backend_access') && Yii::app()->user->id != $model->id_owner){
				throw404();
			}
		}

		return $model;
	}

	public function actionGetImagesForAdmin($id){
		$model = $this->checkOwner($id);
		$this->widget('application.modules.images.components.AdminViewImagesWidget', array(
			'objectId' => $model->id,
		));
	}

	public function actionSetMainImage($id){
		$model = $this->checkOwnerImage($id);

		$sql = 'UPDATE {{images}} SET is_main=0 WHERE id_object=:id';
		Yii::app()->db->createCommand($sql)->execute(array(':id' => $model->id_object));

		if (issetModule('historyChanges')) {
			HistoryChanges::addApartmentInfoToHistory('update_main_image', $model->id_object, 'update');
		}

		$model->is_main = 1;
		$model->update('is_main');
	}

	public function actionDeleteImage($id){
		$model = $this->checkOwnerImage($id);

		unset($model->sorter);
        $ad = Apartment::model()->findByPk($model->id_object);

        if($model->delete() && $ad){
            $ad->count_img--;
            $ad->update('count_img');
        }

		if (issetModule('historyChanges')) {
			HistoryChanges::addApartmentInfoToHistory('delete_image', $ad->id, 'delete');
		}

		if($model->is_main){
			$sql = 'SELECT id FROM {{images}} WHERE is_main=1 AND id_object=:id';
			echo Yii::app()->db->createCommand($sql)->queryScalar(array(':id' => $model->id_object));

			if (issetModule('historyChanges')) {
				HistoryChanges::addApartmentInfoToHistory('update_main_image', $ad->id, 'update');
			}
		}
	}

	public function actionSort($id){
		$model = $this->checkOwner($id);

		$ids = Yii::app()->request->getPost('image');

		//$ids = Yii::app()->request->getParam('image');

		if($ids){
			$sorter = 0;
			foreach($ids as $id){
				$sql = 'UPDATE {{images}} SET sorter=:sorter WHERE id=:id AND id_object=:idObject';
				Yii::app()->db->createCommand($sql)->execute(array(
					':sorter' => $sorter,
					':id' => $id,
					':idObject' => $model->id,
				));
				$sorter++;
			}
		}
	}
	
	public function actionRotateImage($id) {
		$model = $this->checkOwnerImage($id);
		
		$imageHandler = new CImageHandler();
		$originalPath = Images::returnOrigPath($model);	
		if(@getimagesize($originalPath) && $imageHandler->load($originalPath)){
			$imageHandler->rotate(90)->save($originalPath);
			
			$model->file_name_modified = '';
			$model->update(array('file_name_modified'));
		} else {
			return false;
		}
		
		$names = array(
			'thumb_*x*_'.$model->file_name_modified,
			'full_'.$model->file_name_modified,
		);

		foreach($names as $name){
			$mask = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.
				Images::UPLOAD_DIR.DIRECTORY_SEPARATOR.
				Images::OBJECTS_DIR.DIRECTORY_SEPARATOR.
				$model->id_object.DIRECTORY_SEPARATOR.
				Images::MODIFIED_IMG_DIR.DIRECTORY_SEPARATOR.$name;
			@array_map( "unlink", glob( $mask ) );
		}
		
		if (issetModule('historyChanges')) {
			HistoryChanges::addApartmentInfoToHistory('rotate_image', $model->id, 'update');
		}
		
		$filePathModified = Images::getThumbUrl($model, 150, 100);
				
		echo CJSON::encode(array(
			'id' => $model->id,
			'file_name' => Yii::app()->request->getBaseUrl(true).'/'.Images::UPLOAD_DIR.'/'.Images::OBJECTS_DIR.'/'.$model->id_object.'/'.Images::ORIGINAL_IMG_DIR.'/'.$model->file_name,
			'file_name_modified' => $filePathModified
		));
		Yii::app()->end();
	}

	/*public function actionSaveComments($id){
		$model = $this->checkOwner($id);

		$comments = Yii::app()->request->getPost('photo_comment');
		if($comments){
			foreach($comments as $id => $comment){
				$sql = 'UPDATE {{images}} SET comment=:comment WHERE id=:id AND id_object=:idObject';
				Yii::app()->db->createCommand($sql)->execute(array(
					':id' => $id,
					':comment' => $comment,
					':idObject' => $model->id,
				));
			}
		}
	}*/

}