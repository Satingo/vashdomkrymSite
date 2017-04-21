
<div id="change_status_form">
    <?php
    $this->renderPartial('_changeStatus_form', array(
        'model' => $model,
    ));
    ?>
</div>

<script type="text/javascript">
    var status_need_pay = <?php echo Bookingtable::STATUS_NEED_PAY ?>;
    var start_cs = false;

    var changeStatus = {
        apply: function(){
            if(start_cs){
                return false;
            }
            start_cs = true;

            $.ajax({
                url: '<?php echo Yii::app()->createUrl('/bookingtable/backend/main/changeStatus'); ?>',
                type: 'post',
                dataType: 'json',
                data: $('#cs-form').serialize(),
                success: function(data){
                    if(data.status == 'ok'){
                        message(data.msg);
                        $('#cs_el_'+data.id).replaceWith(data.html);
                        tempModal.close();
                        tempModal.init();
                    }else{
                        $('#change_status_form').html(data.html);
                        checkStatus();
                    }

                    start_cs = false;
                },
                error: function(){
                    error('<?php echo tc('Error. Repeat attempt later'); ?>');
                    start_cs = false;
                }
            });
        }
    }

    $(function(){
        checkStatus();

        $('#cs-form').on('change', 'input:radio', function(){
            checkStatus();
        });
    });

    function checkStatus(){
        var status = $('#cs-form input:radio:checked').val();

        if(status == status_need_pay){
            $('#amount_row').show();
        }else{
            $('#amount_row').hide();
        }
    }
</script>
