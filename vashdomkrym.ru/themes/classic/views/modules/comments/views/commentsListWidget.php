<div id="comments-block">
	<?php
		echo '<h2>'.Yii::t('module_comments','Comments').'</h2>';

		$this->render('commentsList', array('comments' => $comments));

		if(param('commentAllowForGuests', 0) && Yii::app()->user->isGuest || !Yii::app()->user->isGuest){
			echo CHtml::link(Yii::t('module_comments','Leave a Comment'), '#comment-form', array('class' => 'fancy mgp-open-inline'));
		}
	?>
</div>
<div class="hidden">
	<div id="comment-form" class="white-popup-block">
		<?php
			$this->render('_commentForm', array('model' => $form));
		?>
	</div>
</div>