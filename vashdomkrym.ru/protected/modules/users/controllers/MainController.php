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
    public $modelName = 'User';

	public function getSizeLimit(){
		$min = min(toBytes(ini_get('post_max_size')), toBytes(ini_get('upload_max_filesize')));
		return min($min, param('maxImgFileSize', 8 * 1024 * 1024));
	}

	public function actionUploadAva(){
		if(Yii::app()->user->isGuest){
			throw404();
		}

		Yii::import("ext.EAjaxUpload.qqFileUploader");

        $id = Yii::app()->request->getQuery('id', 0);
        if($id && in_array(Yii::app()->user->role, array(User::ROLE_MODERATOR, User::ROLE_ADMIN))){
            $user = $this->loadModel($id);
        } else {
            $user = HUser::getModel();
        }

		$oldAva = $user->ava;

		$folder = HUser::getUploadDirectory($user, HUser::UPLOAD_AVA) . DIRECTORY_SEPARATOR;// folder for uploaded files
		$allowedExtensions = array("jpg","jpeg","gif", "png");//array("jpg","jpeg","gif","exe","mov" and etc...

		$sizeLimit = $this->getSizeLimit();// maximum file size in bytes

		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($folder);

		if($result['success'] == true){
			$fileSize = filesize($folder . $result['filename']);//GETTING FILE SIZE
			$fileNameReal = $result['filename'];//GETTING FILE NAME
			$fileName = time() . '_' . $user->id . '.' . pathinfo($fileNameReal, PATHINFO_EXTENSION);;

			Yii::import('ext.image.Image');

			$image = new Image($folder . $fileNameReal);
			$image->save($folder . $fileName);

			// генерим тумбу
			$thumbName = User::AVA_PREFIX . $fileName;

			$image = new Image($folder . $fileNameReal);
			$image->resize(96, 96);
			$image->save($folder . $thumbName);

			$user->ava = $fileName;
			$user->update('ava');

			@unlink($folder . $fileNameReal);

			$result['avaHtml'] = '<div class="user-ava-crop">'.CHtml::image($user->getAvaSrcThumb(), $user->username, array('class' => 'message_ava')).'</div>';

			if($oldAva){
				@unlink($folder . $oldAva);
				@unlink($folder . User::AVA_PREFIX . $oldAva);
			}
		}

		echo CJSON::encode($result);// it's array
	}


    public function actionAjaxDelAva(){
        if(Yii::app()->user->isGuest || !Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $id = Yii::app()->request->getQuery('id', 0);
        if($id && in_array(Yii::app()->user->role, array(User::ROLE_MODERATOR, User::ROLE_ADMIN))){
            $user = $this->loadModel($id);
        } else {
            $user = HUser::getModel();
        }

        $folder = HUser::getUploadDirectory($user, HUser::UPLOAD_AVA) . DIRECTORY_SEPARATOR;
        @unlink($folder . $user->ava);
        @unlink($folder . User::AVA_PREFIX . $user->ava);

        $user->ava = '';
        $user->update(array('ava'));

        $result['avaHtml'] = '<div class="user-ava-crop">'.CHtml::image(Yii::app()->theme->baseUrl . '/images/ava-default.jpg', $user->username, array('class' => 'message_ava')).'</div>';

        HAjax::jsonOk(tc('Success'), $result);
    }

	public function actionSearch($type = 'all'){
		if(!param('useShowUserInfo')){
			throw new CHttpException(403, tt('Displays information about the users is disabled by the administrator', 'users'));
		}

		$usersListPage = Menu::model()->findByPk(Menu::USERS_LIST_ID);
		if ($usersListPage) {
			if ($usersListPage->active == 0) {
				throw404();
			}
		}

		$this->showSearchForm = false;

		$existTypes = User::getTypeList('withAll');

		$criteria = new CDbCriteria();
		$type = in_array($type, array_keys($existTypes)) ? $type : 'all';
		if($type != 'all'){
			$criteria->compare('type', $type);
		}
		$criteria->with = array('countAdRel');

		$sort = new CSort();

		$sort->sortVar = 'sort';
		$sort->defaultOrder = 'date_created DESC';
		$sort->multiSort = true;

		$sort->attributes = array(
			'username'=>array(
				'label'=>tc('by username'),
				'default'=>'desc',
			),
			'date_created'=>array(
				'label' => tc('by date of registration'),
				'default'=>'desc',
			),
		);
		$dataProvider = new CActiveDataProvider(User::model()->active(),
			array(
				'criteria' => $criteria,
				'sort' => $sort,
				'pagination'=>array(
					'pageSize' => 12,
				),
			)
		);

		$this->render('search', array(
			'dataProvider' => $dataProvider,
			'type' => $type,
		));
	}

	public function actionGeneratePhone($id = null, $width=130, $font=3) {
		$user = User::model()->findByPk($id);
		$phone = '';
		if($user){
			$phone = $user->phone;
		}

		$image = imagecreate($width, 20);

		$font = (Yii::app()->theme->name == 'atlas') ? 4 : $font;

		$bg = imagecolorallocate($image, 255, 255, 255);
		$textcolor = imagecolorallocate($image, 37, 75, 137);

		imagestring($image, $font, 0, 0, $phone, $textcolor);

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Transfer-Encoding: binary');
		header("Content-type: image/png");
		imagepng($image);
		//echo $image;
		imagedestroy($image);
	}

    public function actionView($id = 0, $url = ''){
        $model = $this->loadModel($id);
        $renderData = HUser::getDataForListings($id);
        $renderData['model'] = $model;
        $this->render('webroot.protected.modules.users.views.view', $renderData);
    }

}