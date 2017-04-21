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

class commentListWidget extends CWidget {
	public $model;
	public $url;
	public $showRating = false;

	// TODO
	// уведомление на почту о комментариях
	// Reply

	public function getModelName(){
		return get_class($this->model);
	}

	public function getViewPath($checkTheme=true){
		if($checkTheme && ($theme=Yii::app()->getTheme())!==null){
			if (is_dir($theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'comments'.DIRECTORY_SEPARATOR.'views'))
				return $theme->getViewPath().DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'comments'.DIRECTORY_SEPARATOR.'views';
		}
		return Yii::getPathOfAlias('application.modules.comments.views');
	}

	public function createComment(){
		$comment = new Comment();
		$comment->model_name = $this->getModelName();
		$comment->model_id = $this->getModelId();
		return $comment;
	}

	protected function getModelId() {
		if (is_array($this->model->primaryKey)) {
			return implode('.', $this->model->primaryKey);
		} else {
			return $this->model->primaryKey;
		}
	}

	public function run() {
		$newComment = $this->createComment();
		$comments = $newComment->getCommentsThree();

		$form = new CommentForm();
		$form->url = $this->url;
		$form->modelName = $this->getModelName();
		$form->modelId = $this->getModelId();
		$form->defineShowRating();

		$this->render('commentsListWidget', array(
			'comments' => $comments,
			'newComment' => $newComment,
			'form' => $form,
		));
	}
}