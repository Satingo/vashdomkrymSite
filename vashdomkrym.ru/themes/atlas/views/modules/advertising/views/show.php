<?php if (array_key_exists('url', $item) && $item['url']):?>
	<script type="text/javascript">
		function doAdvertClick() {
			$.post("<?php echo Yii::app()->createAbsoluteUrl('advertising/main/banneractivate');?>", { id: "<?php echo $item['id'];?>", "<?php echo Yii::app()->request->csrfTokenName;?>" : "<?php echo Yii::app()->request->csrfToken;?>"});
		}
	</script>
<?php endif; ?>

<?php
if ($item['type'] == 'file') {
	if ($item['url']) {
		echo CHtml::link(
			CHtml::image(Yii::app()->getBaseUrl(false)."/uploads/rkl/{$item['file_path']}", $item['alt_text']),
			$item['url'],
			array(
				'target' => '_blank',
				'onclick' => 'doAdvertClick()',
				'alt' => (isset($item['alt']) && $item['alt']) ? $item['alt'] : ''
			)
		);
	}
	else {
		echo CHtml::image(
			Yii::app()->getBaseUrl(false)."/uploads/rkl/{$item['file_path']}", $item['alt_text']
		);
	}
}
elseif ($item['type'] == 'html') {
	echo CHtml::decode($item['html']);
}
elseif ($item['type'] == 'js') {
	echo $item['js'];
}
?>
