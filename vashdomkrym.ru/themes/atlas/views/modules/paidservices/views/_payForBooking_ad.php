<?php
$item = $booking->apartment;
?>

<div class="appartment_item block block_for_booking_ad">
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
            <div class="apartment_type"><?php echo HApartment::getNameByType($item->type); ?></div>

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
    </div>
</div>