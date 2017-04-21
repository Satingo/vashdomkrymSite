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
	public $modelName = 'Comment';

	public function actions() {
		$return = array();
		if (param('useJQuerySimpleCaptcha', 0)) {
			$return['captcha'] = array(
				'class' => 'jQuerySimpleCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}
		else {
			$return['captcha'] = array(
				'class' => 'MathCCaptchaAction',
				'backColor' => 0xFFFFFF,
			);
		}

		return $return;
	}

	public function actionWriteComment(){
		$model = new CommentForm();

		if(isset($_POST['CommentForm']) && BlockIp::checkAllowIp(Yii::app()->controller->currentUserIpLong)){
			$model->attributes=$_POST['CommentForm'];
			$model->defineShowRating();
			if($model->validate() && Comment::checkExist(null, $model->modelName, $model->modelId)){

				if(
					($model->modelName == 'Entries' && !param('enableCommentsForEntries', 1))
					|| ($model->modelName == 'Apartment' && !param('enableCommentsForApartments', 1))
					|| ($model->modelName == 'Menu' && !param('enableCommentsForPages', 0))
					|| ($model->modelName == 'Article' && !param('enableCommentsForFaq', 1))
					|| ($model->modelName == 'InfoPages' && !param('enableCommentsForPages', 0))
				 ){
					throw404();
				}

				$comment = new Comment();
				$comment->body = $model->body;
				$comment->parent_id = $model->rel;

				$comment->user_ip = Yii::app()->controller->currentUserIp;
				$comment->user_ip_ip2_long = Yii::app()->controller->currentUserIpLong;

				if($model->rel == 0){
					$comment->rating = $model->rating;
				} else {
					$comment->rating = -1;
				}

				$comment->model_name = $model->modelName;
				$comment->model_id = $model->modelId;

				if(Yii::app()->user->isGuest){
					$comment->user_name = $model->user_name;
					$comment->user_email = $model->user_email;
				} else {
					$comment->owner_id = Yii::app()->user->id;
				}

				if(param('commentNeedApproval', 1) && !Yii::app()->user->checkAccess('backend_access')){
					$comment->status = Comment::STATUS_PENDING;
					Yii::app()->user->setFlash('success', Yii::t('module_comments','Thank you for your comment. Your comment will be posted once it is approved.'));
				} else {
					$comment->status = Comment::STATUS_APPROVED;
					Yii::app()->user->setFlash('success', Yii::t('module_comments','Thank you for your comment.'));
				}
				$comment->save(false);

				$this->redirect($model->url);
			}
		}

		$this->render('commentForm', array('model' => $model));
	}

	public function actionDeleteComment(){
		$return['status'] = 0;

		$id = Yii::app()->request->getPost('id');

		$model = $this->loadModel($id);
		if(!$model || (!$model->isOwner() && !Yii::app()->user->checkAccess('backend_access'))){
			$return['message'] = tt('commentNotFound', 'comments');
		} else {
			$model->delete();
			$return['status'] = 1;
		}

		echo CJavaScript::jsonEncode($return);
	}


}
