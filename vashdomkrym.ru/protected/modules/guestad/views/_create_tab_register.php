<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/common/js/chosen/chosen.min.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/common/js/chosen/chosen.jquery.js', CClientScript::POS_END);
?>

<div class="rowold">
    <?php echo $form->labelEx($user,'type'); ?>
    <?php echo $form->dropDownList($user, 'type', User::getTypeList(), array('class'=>'width200')); ?>
    <?php echo $form->error($user,'type'); ?>
</div>

<div class="rowold" id="row_agency_name">
    <?php echo $form->labelEx($user,'agency_name'); ?>
    <?php echo $form->textField($user,'agency_name',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
    <?php echo $form->error($user,'agency_name'); ?>
</div>

<?php
echo '<div class="rowold" id="row_agency_user_id">';
$agency = HUser::getListAgency();

echo $form->labelEx($user, 'agency_user_id');
echo $form->dropDownList($user, 'agency_user_id', $agency, array('id' => 'agency_user_id', 'data-placeholder' => ' '));
echo $form->error($user, 'agency_user_id');
echo '</div>';
?>

<div class="rowold">
    <?php echo $form->labelEx($user,'username'); ?>
    <?php echo $form->textField($user,'username',array('size'=>20,'maxlength'=>128, 'class'=>'width200')); ?>
    <?php echo $form->error($user,'username'); ?>
</div>

<div class="rowold">
    <?php echo $form->labelEx($user,'email'); ?>
    <?php echo $form->textField($user,'email',array('size'=>20,'maxlength'=>128, 'class'=>'width200')); ?>
    <?php echo $form->error($user,'email'); ?>
</div>

<?php if (param('user_registrationMode') == 'without_confirm'):?>
	<div class="rowold">
		<?php echo $form->labelEx($user,'password'); ?>
		<?php echo $form->passwordField($user,'password',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
		<?php echo $form->error($user,'password'); ?>
	</div>

	<div class="rowold">
		<?php echo $form->labelEx($user,'password_repeat'); ?>
		<?php echo $form->passwordField($user,'password_repeat',array('size'=>20,'maxlength'=>128,'class'=>'width200')); ?>
		<?php echo $form->error($user,'password_repeat'); ?>
	</div>
<?php endif;?>

<div class="rowold">
    <?php echo $form->labelEx($user,'phone'); ?>
    <?php echo $form->textField($user,'phone',array('size'=>20,'maxlength'=>15, 'class'=>'width200')); ?>
    <?php echo $form->error($user,'phone'); ?>
</div>

<div class="rowold">
	<?php echo $form->labelEx($user, 'verifyCode');?>
	<?php echo $form->textField($user, 'verifyCode',array('autocomplete' => 'off'));?><br/>
	<?php echo $form->error($user, 'verifyCode');?>
    <?php $this->widget('CustomCCaptcha',
		array(
			'captchaAction' => '/guestad/main/captcha',
			'buttonOptions' => array('class' => 'get-new-ver-code'),
			'clickableImage' => true,
			'imageOptions'=>array('id'=>'register_guestad_captcha'),
		)
	); ?><br/>
</div>

<div class="rowold rememberMe">
    <?php echo $form->checkBox($user,'agree'); ?>
    <?php echo $form->label($user,'agree'); ?>
    <?php echo $form->error($user,'agree'); ?>
</div>

<script type="text/javascript">
    $(function(){
        $("#agency_user_id").chosen({no_results_text: " "});

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
</script>