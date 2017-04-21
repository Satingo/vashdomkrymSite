<?php
$item = $booking->apartment;
?>

<div class="appartment_item block">
    <div class="title_block">
        <?php
        $title = CHtml::encode($item->getStrByLang('title'));

        $description = '';
        if ($item->canShowInView('description')) {
            $description = $item->getStrByLang('description');
        }

        echo CHtml::link($title, $item->getUrl(), array('title' => $title, 'target' => '_blank'));
        ?>
    </div>

    <div class="before-image">
        <div class="image_block">
            <div class="apartment_type"><?php echo Apartment::getNameByType($item->type); ?></div>

            <?php if ($item->is_special_offer):?>
                <div class="like"></div>
            <?php endif;?>

            <?php if($item->rating):?>
                <div class="rating">
                    <?php
                    $this->widget('CStarRating',array(
                        'model'=>$item,
                        'attribute' => 'rating',
                        'readOnly'=>true,
                        'id' => 'rating_' . $item->id,
                        'name'=>'rating'.$item->id,
                        'cssFile' => Yii::app()->theme->baseUrl.'/css/rating/rating.css'
                    ));
                    ?>
                </div>
            <?php endif;?>

            <?php
            $res = Images::getMainThumb(610,342, $item->images);
            $img = CHtml::image($res['thumbUrl'], $item->getStrByLang('title'), array(
                'title' => $item->getStrByLang('title'),
                'class' => 'apartment_type_img'
            ));
            echo CHtml::link($img, $item->getUrl(), array('title' =>  $item->getStrByLang('title'), 'alt' => $item->getStrByLang('title')));
            ?>
        </div>
    </div>

    <div class="clear"></div>

    <div class="mini_block_full_description">
        <?php if ($item->canShowInView('description')) { ?>
            <div class="desc">
                <div class="desc">
                    <?php
                    if (utf8_strlen($description) > 110)
                        $description = utf8_substr($description, 0, 110) . '...';

                    echo $description;
                    ?>
                </div>
            </div>
        <?php } ?>
        <div class="mini_block">
            <div class="price">
                <?php
                if ($item->is_price_poa)
                    echo tt('is_price_poa', 'apartments');
                else
                    echo $item->getPrettyPrice();
                ?>
            </div>
        </div>

        <div class="clear"></div>

        <?php if($item->square || $item->berths):?>
            <dl class="mini_desc">
                <?php $showBerth = false;?>
                <?php if($item->canShowInView('berths')):?>
                    <?php $showBerth = true;?>
                    <dt>
                        <span class="icon-bedroom icon-mrgr"></span>
                        <?php echo Yii::t('module_apartments', 'berths').': '.CHtml::encode($item->berths);?>
                    </dt>
                <?php endif;?>
                <?php if($item->canShowInView('square')):?>
                    <dt>
                        <span class="icon-square icon-mrgr <?php echo ($showBerth) ? 'icon-mrgl' : '';?>"></span>
                        <?php echo Yii::t('module_apartments', 'total square: {n}', $item->square)." ".tc('site_square');?>
                    </dt>
                <?php endif;?>
            </dl>
        <?php endif;?>
    </div>
</div>