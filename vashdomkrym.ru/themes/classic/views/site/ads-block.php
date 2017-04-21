<div id="ore-ads-block">
	<div style="margin: 0 auto; width: 960px;">
	<ul>
		<li>
			<?php
			$isFree = (isFree()) ? true : false;
			$linkTitle = Yii::t('module_install', 'Download', array(), 'messagesInFile', Yii::app()->language);
			$linkHref = (Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/download-open-real-estate' : 'http://open-real-estate.info/en/download-open-real-estate';
			if (!$isFree) {
				$linkTitle = Yii::t('module_install', 'Buy', array(), 'messagesInFile', Yii::app()->language);
				$linkHref = (Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/contact-us?from=pro' : 'http://open-real-estate.info/en/contact-us?from=pro';
			}

			echo CHtml::link(
				'<span class="download"></span>'.$linkTitle,
				$linkHref,
				array (
					'class' => 'button green'
				)
			);
			?>
		</li>
		<?php if (isFree()):?>
			<li>
				<?php
				echo CHtml::link(
					Yii::t('module_install', 'PRO version demo', array(), 'messagesInFile', Yii::app()->language),
					'http://re-pro.monoray.net/',
					array(
						'class' => 'button green'
					)
				);
				?>
			</li>

			<li>
				<?php
				echo CHtml::link(
					Yii::t('module_install', 'Add-ons', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/open-real-estate-modules' : 'http://open-real-estate.info/en/open-real-estate-modules',
					array(
						'class' => 'button cyan'
					)
				);
				?>
			</li>
		<?php endif;?>
		<li>
			<?php
				echo CHtml::link(
					Yii::t('module_install', 'About product', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/about-open-real-estate' : 'http://open-real-estate.info/en/about-open-real-estate',
					array (
						'class' => 'button cyan'
					)
				);
			?>
		</li>
		<li>
			<?php
				echo CHtml::link(
					Yii::t('module_install', 'Contact us', array(), 'messagesInFile', Yii::app()->language),
					(Yii::app()->language == 'ru') ? 'http://open-real-estate.info/ru/contact-us' : 'http://open-real-estate.info/en/contact-us',
					array (
						'class' => 'button cyan'
					)
				);
			?>
		</li>
	</ul>
	</div>
</div>