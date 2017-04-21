<?php
$this->pageTitle .= ' - '.tt('All messages', 'messages');
$this->breadcrumbs = array(
	tc('Control panel') => Yii::app()->createUrl('/usercpanel/main/index'),
	tt('My mailbox', 'messages') => Yii::app()->createUrl('/messages/main/index'),
	tt('All messages', 'messages')
);
?>

<h2 class="message-archive"><?php echo tt('History messages with user', 'messages'). ' "'.CHtml::encode($senderInfo->username).'"'; ?></h2>
<?php $this->pageTitle .= ' - '.tt('History messages with user', 'messages'). ' "'.CHtml::encode($senderInfo->username).'"'; ?>

<div class="form">
	<?php $this->renderPartial('//../modules/messages/views/backend/__form_message', array('model' => $model, 'uid' => $uid, 'apId' => $apId));?>
</div>

<div class="box_message">
	<?php if ($allMessages) : ?>
			<?php foreach($allMessages as $item) : ?>
				<?php
					$addClass = '';
					if ($item->id_userFrom == Yii::app()->user->id)
						$addClass = 'i-message';
					else
						$addClass = 'other-message';
				?>
				<div class="message_contact_read <?php echo $addClass; ?>">
					<div class="message_contact_message">
						<h3 class="author">
							<?php if ($item->id_userFrom == Yii::app()->user->id): ?>
								<?php echo tt('I am', 'messages');?>
							<?php else: ?>
								<?php echo CHtml::encode($item->userInfoFrom->username);?>
							<?php endif; ?>
						</h3>
						<span class="message_contact_date">
							<?php echo $item->date_send;?>
						</span>

						<blockquote><?php echo Messages::messageFormat($item);?></blockquote>
					</div>

					<?php if (isset($item->messagesFiles) && $item->messagesFiles) : ?>
						<div class="message_contact_message">
							<p class="files"><strong><?php echo tt('Files', 'messages');?></strong>:</p>
							<p><?php echo Messages::getFiles($item);?></p>
						</div>
					<?php endif;?>
				</div>
			<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php
if ($pages) {
	$this->widget('itemPaginator', array('pages' => $pages, 'header' => ''));
}
?>