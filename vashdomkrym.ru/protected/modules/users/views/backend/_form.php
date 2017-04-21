<div class="form">
<?php
	$form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableAjaxValidation'=>false,
	));
	$model->password = '';
	$model->password_repeat = '';
	?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <div class="profile-ava">
        <?php
        echo $model->renderAva();

        $this->widget('ext.EAjaxUpload.EAjaxUpload',
            array(
                'id'=>'uploadFile',
                'label' => tc('Upload file'),
                'config'=>array(
                    'action'=>Yii::app()->createUrl('/users/main/uploadAva', array('id' => $model->id)),
                    'allowedExtensions'=>array("jpg","jpeg","gif", "png"),
                    'sizeLimit'=>1*1024*1024,// maximum file size in bytes
                    'minSizeLimit'=>1024,// minimum file size in bytes
                    'onComplete'=>"js:function(id, fileName, responseJSON){ profile.showAva(responseJSON); }",
                    'multiple'=>false,
                    'showMessage'=>"js:function(message){ error(message); }",
                )
            ));

        echo CHtml::link(tc('Delete'), 'javascript:;', array('id' => 'delete_ava', 'style' => 'display: show;'));
        ?>
    </div>

    <div class="clear"></div>
    <br>

    <div class="rowold">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model, 'type', User::getTypeList(), array('class'=>'span2')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

	<?php if (issetModule('rbac') && (Yii::app()->user->role == User::ROLE_MODERATOR) && $model->role != User::ROLE_ADMIN && $model->role != User::ROLE_MODERATOR) : ?>
		<div class="rowold">
			<?php echo $form->labelEx($model,'role'); ?>
			<?php echo $form->dropDownList($model, 'role', array_merge(array('' => tt('Select role')), User::$createUserWithoutRolesModeration), array('class'=>'span4')); ?>
			<?php echo $form->error($model,'role'); ?>
		</div>
	<?php elseif (issetModule('rbac') && (Yii::app()->user->role == User::ROLE_ADMIN) && $model->role != User::ROLE_ADMIN):?>
		<div class="rowold">
			<?php echo $form->labelEx($model,'role'); ?>
			<?php echo $form->dropDownList($model, 'role', array_merge(array('' => tt('Select role')), User::$createUserWithoutRolesAdmin), array('class'=>'span4')); ?>
			<?php echo $form->error($model,'role'); ?>
		</div>
	<?php endif;?>

    <div class="rowold" id="row_agency_name">
        <?php echo $form->labelEx($model,'agency_name'); ?>
        <?php echo $form->textField($model,'agency_name',array('size'=>20,'maxlength'=>128,'class'=>'span2')); ?>
        <?php echo $form->error($model,'agency_name'); ?>
    </div>

    <?php
    echo '<div class="rowold"  id="row_agency_user_id">';
    $agency = HUser::getListAgency();

    echo $form->labelEx($model, 'agency_user_id');
    echo Chosen::dropDownList(get_class($model).'[agency_user_id]', $model->agency_user_id, $agency,
			array('id'=>'agency_user_id', 'data-placeholder' => ' ', 'class' => 'span3')
		);
	echo "<script>$('#agency_user_id').chosen();</script>";
    echo $form->error($model, 'agency_user_id');
    echo '</div><br />';
    ?>

	<div class="rowold">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>15,'class' => 'span2')); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<?php if(issetModule('paidservices')){ ?>
    <div class="rowold">
		<?php echo $form->labelEx($model,'balance'); ?>
		<?php echo $form->textField($model,'balance',array('size'=>20,'maxlength'=>15,'class' => 'span2')); ?>
		<?php echo $form->error($model,'balance'); ?>
    </div>
	<?php } ?>

	<div class="clear">&nbsp;</div>
	<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
				'model' => $model,
				'field' => 'additional_info',
				'type' => 'text'
			));
		?>
	<div class="clear">&nbsp;</div>

	<?php if ($model->role != User::ROLE_ADMIN) : ?>
		<?php if(!$model->isNewRecord) : ?>
			<div class="padding-bottom10">
				<span class="label label-info">
					<?php echo tt('admin_change_pass_user_help');?>
				</span>
			</div>
		<?php endif; ?>

		<div class="rowold">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>

		<div class="rowold">
			<?php echo $form->labelEx($model,'password_repeat'); ?>
			<?php echo $form->passwordField($model,'password_repeat',array('size'=>20,'maxlength'=>128,'class' => 'span2')); ?>
			<?php echo $form->error($model,'password_repeat'); ?>
		</div>
	<?php endif; ?>

    <div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
		array('buttonType'=>'submit',
			'type'=>'primary',
			'icon'=>'ok white',
			'label'=> $model->isNewRecord ? tc('Create') : tc('Save'),
		)); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $(function(){
        regCheckUserType();

        $('#User_type').change(function(){
            regCheckUserType();
        });
    });

    function regCheckUserType(){
        var type = $('#User_type').val();
        if(type == <?php echo CJavaScript::encode(User::TYPE_AGENCY);?>){
            $('#row_agency_name').show();
        } else {
            $('#row_agency_name').hide();
        }

        if(type == <?php echo CJavaScript::encode(User::TYPE_AGENT);?>){
            $('#row_agency_user_id').show();
        } else {
            $('#row_agency_user_id').hide();
        }
    }

    var ava = <?php echo $model->ava ? 1 : 0 ?>;

    var profile = {
        showAva: function(data){
            if(data.success == true){
                $('#user-ava-<?php echo $model->id;?>').html(data.avaHtml);
                $('#delete_ava').show();
            }
        }
    }

    $(function(){
        if(ava){
            $('#delete_ava').show();
        } else {
            $('#delete_ava').hide();
        }

        $('#delete_ava').on('click', function(){
            $.ajax({
                url: '<?php echo Yii::app()->createUrl('/users/main/ajaxDelAva', array('id' => $model->id)) ?>',
                dataType: 'json',
                type: 'get',
                success: function(data){
                    if(data.status == 'ok'){
                        $('#user-ava-<?php echo $model->id;?>').html(data.avaHtml);
                        $('#delete_ava').hide();
                    }
                }
            });
        });
    });
</script>