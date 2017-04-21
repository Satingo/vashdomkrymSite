<?php
$this->pageTitle=Yii::app()->name . ' - '.tc('Error');
$this->breadcrumbs=array(
	tc('Error'),
);
?>

<h2><?php echo tc('Error');?> <?php echo CHtml::encode($code); ?></h2>

<div class="error">
	<?php echo CHtml::encode($message); ?>
</div>

<div class="exception-detail-info">
	<?php
		echo "<h1>PHP Error [$code]</h1>\n";
		echo "<p>$message ($file:$line)</p>\n";
		echo '<pre>';

		$trace=debug_backtrace();
		if(count($trace)>3)
			$trace=array_slice($trace,3);
		foreach($trace as $i=>$t)
		{
			if(!isset($t['file']))
				$t['file']='unknown';
			if(!isset($t['line']))
				$t['line']=0;
			if(!isset($t['function']))
				$t['function']='unknown';
			echo "#$i {$t['file']}({$t['line']}): ";
			if(isset($t['object']) && is_object($t['object']))
				echo get_class($t['object']).'->';
			echo "{$t['function']}()\n";
		}

		echo '</pre>';
	?>
</div>