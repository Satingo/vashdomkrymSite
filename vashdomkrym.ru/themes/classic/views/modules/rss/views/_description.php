<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="170px">
			<?php

			$res = Images::getMainThumb(150,100, $item->images);
			$img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
				'title' => $item->getStrByLang('title'),
			));
			echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title')));
			?>
		</td>
		<td valign="top">
            <strong>
				<?php
				echo utf8_ucfirst($item->objType->name) . ' ' . tt('type_view_'.$item->type, 'apartments');
				if ($item->num_of_rooms){
					echo ',&nbsp;';
					echo Yii::t('module_apartments',
						'{n} bedroom|{n} bedrooms|{n} bedrooms', array($item->num_of_rooms));
				}
				if(isset($item->city) && isset($item->city->name)){
					echo ',&nbsp;';
					echo $item->city->name;
				}
				?>
            </strong>
            <p class="cost"><?php echo $item->getPrettyPrice(); ?></p>
		</td>
	</tr>
</table>