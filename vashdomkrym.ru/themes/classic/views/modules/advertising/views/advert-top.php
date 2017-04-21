<?php if ($this->advertPos1 || $this->advertPos2): ?>
	<div class="rkl-blocks-top">
		<?php if ($this->advertPos1):?>
			<?php $i = 0; ?>
			<div class="rkl-blocks-top-left">
				<?php
					foreach ($this->advertPos1 as $item) : ?>
						<div <?php if ($i != 0) echo 'style="padding-top: 5px;"'; ?>>
							<?php
								$this->renderPartial('//modules/advertising/views/show', array('item' => $item));

								$i++;
							?>
						</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ($this->advertPos2): ?>
			<?php $i = 0; ?>
			<div class="rkl-blocks-top-right">
				<?php
					foreach ($this->advertPos2 as $item) : ?>
						<div <?php if ($i != 0) echo 'style="padding-top: 5px;"'; ?>>
							<?php
								$this->renderPartial('//modules/advertising/views/show', array('item' => $item));

								$i++;
							?>
						</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if($this->advertPos3): ?>
	<?php $i = 0; ?>
	<div class="rkl-blocks-top rkl-blocks-top-center">
		<?php
			foreach ($this->advertPos3 as $item) : ?>
				<div <?php if ($i != 0) echo 'style="padding-top: 5px;"'; ?>>
					<?php
						$this->renderPartial('//modules/advertising/views/show', array('item' => $item));

						$i++;
					?>
				</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>