<?php
include_once 'tpl_header.php';
// var_dump($p_images);
?>
<div uk-grid>
    <p>Products: <?=count($f_products)?></p>
</div>
<?php if(is_array($p_images)) : ?>
    <div class="uk-child-width-1-2@s uk-child-width-1-4@m uk-text-center uk-margin-left uk-margin-right" uk-grid>
    <?php foreach ($p_images as $p_image) : ?>
        <div class="uk-card uk-card-default uk-card-body">
            <h3 class="uk-card-title uk-padding-small"><?=$p_image['product_name']?></h3>
            <img uk-img src="/html_parser/img/thumbnails/<?=$p_image['product_image']?>" width="200vm"></img>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<? if ($message) :?>
<div uk-alert><?=$message?></div>
<? endif; ?>
<?php
include_once 'tpl_footer.php';
?>