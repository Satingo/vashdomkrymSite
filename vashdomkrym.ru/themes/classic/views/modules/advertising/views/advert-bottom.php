<?php if ($this->advertPos4 || $this->advertPos5): ?>
	<div class="rkl-blocks-bottom">
		<?php if ($this->advertPos4):?>
			<?php $i = 0; ?>
			<div class="rkl-blocks-bottom-left">
				<?php
					foreach ($this->advertPos4 as $item) : ?>
						<div <?php if ($i != 0) echo 'style="padding-top: 5px;"'; ?>>
							<?php
								$this->renderPartial('//modules/advertising/views/show', array('item' => $item));

								$i++;
							?>
						</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ($this->advertPos5): ?>
			<?php $i = 0; ?>
			<div class="rkl-blocks-bottom-right">
				<?php
					foreach ($this->advertPos5 as $item) : ?>
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

<?php if($this->advertPos6): ?>
	<?php $i = 0; ?>
	<div class="rkl-blocks-bottom rkl-blocks-bottom-center">
		<?php
			foreach ($this->advertPos6 as $item) : ?>
				<div <?php if ($i != 0) echo 'style="padding-top: 5px;"'; ?>>
					<?php
						$this->renderPartial('//modules/advertising/views/show', array('item' => $item));

						$i++;
					?>
				</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
