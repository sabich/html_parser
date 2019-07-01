<?php
// var_dump($full_products[2]['product_attributes']);die;
?>
<?php if($full_products) : ?>
<div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s uk-text-center uk-padding-small">
<?php foreach ($full_products as $key=>$full_product) : ?>
    <div class="uk-card uk-card-default uk-card-body">
      <div class="uk-text-left">
        <span><strong>Brand: </strong><?=$full_product['product_brand']?></span><br>
        <span><strong>Category: </strong><?=$full_product['product_category']?></span><br>
        <span><strong>Sub-Category: </strong><?=$full_product['product_sub_category']?></span><br>
      </div>
      <h3 class="uk-card-title uk-padding-small"><?=$full_product['product_name']?></h3>
      <img uk-img src="/html_parser/img/thumbnails/<?=$full_product['product_image']?>" width="200vm"></img>
      <div class="uk-margin">
        <label class="uk-form-label" for="form-stacked-select">Attributes</label>
        <div class="uk-form-controls">
            <?php
            if (is_array($full_product['product_attributes'])) :
            foreach ($full_product['product_attributes'] as $name => $attribute) : 
            ?>
            <select class="uk-select uk-form-width-medium" id="form-stacked-select" name="attributes">
              <?php foreach ($attribute as $value) : ?>
              <option value="<?=$name?>"><?=$value?></option>
              <?php endforeach; ?>
            </select>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
      <div class="uk-text-large">$ <?=$full_product['product_price']?></div>
      <div class="uk-text-small"><?=substr(strip_tags($full_product['product_description']),0,100)?>...</div>
    </div>
<?php endforeach; ?>
</div>
<?php else : ?>
<div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s uk-text-center uk-padding-small">
<?php foreach ($f_products as $f_product) : ?>
    <div class="uk-card uk-card-default uk-card-body">
      <a href="<?=$f_product['product_link']?>" target="_blank">
        <p><?=$f_product['product_name']?></p>
      </a>
      <a href="<?=$f_product['product_img_url']?>">
        <img uk-img src="<?=$f_product['product_img_url']?>"></img>
      </a>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>