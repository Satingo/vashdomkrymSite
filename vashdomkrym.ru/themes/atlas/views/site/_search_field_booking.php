<div class="<?php echo $divClass; ?>">
    <div class="<?php echo $controlClass; ?>">
        <?php
        $datePickerData = array(
            'b_start' => array(
                'showAnim'=>'fold',
                'dateFormat'=>Booking::getJsDateFormat(),
                'minDate'=>'new Date()',
                //'maxDate'=>'+12M',
            ),
            'b_end' => array(
                'showAnim'=>'fold',
                'dateFormat'=>Booking::getJsDateFormat(),
                'minDate'=>'new Date()',
            ),
        );

        $bStart = isset($this->bStart) ? $this->bStart : HDate::formatForDatePicker(time());
        $bEnd = isset($this->bEnd) ? $this->bEnd : null;

        echo tc('Booking from').':&nbsp;';
        $this->widget('application.extensions.FJuiDatePicker', array(
            'name'=>'b_start',
            'value' => $bStart,
            'range' => 'eval_period',
            'language' => Yii::app()->controller->datePickerLang,
            'options'=>$datePickerData['b_start'],
            'htmlOptions' => array(
                'class' => 'width80',
				'readonly' => 'true',
            ),
        ));

        echo ' '.tc('to').':&nbsp;';
        $this->widget('application.extensions.FJuiDatePicker', array(
            'name' => 'b_end',
            'value' => $bEnd,
            'range' => 'eval_period',
            'language' => Yii::app()->controller->datePickerLang,
            'options'=>$datePickerData['b_end'],
            'htmlOptions' => array(
                'class' => 'width80',
				'readonly' => 'true',
            ),
        ));
        ?>
    </div>
</div>

<script>
    useDatePicker = <?php echo CJavaScript::encode($datePickerData); ?>
</script>