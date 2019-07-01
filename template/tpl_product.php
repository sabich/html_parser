<?php
include_once 'tpl_header.php';
?>

<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky">
  <nav class="uk-navbar-container" uk-navbar>
      <div class="uk-navbar-left">
          <ul class="uk-navbar-nav">
          <?php foreach ($categories as $name => $url) : ?>
            <li class="<?=($name===$_GET['category']) ? 'uk-active' : '' ?>">
              <a href="?category=<?=urlencode($name)?>" class="uk-link"><?=$name?></a>
            </li>
          <?php endforeach; ?>
          </ul>
      </div>
  </nav>
</div>

<? if ($message) :?>
<div class="uk-container uk-container-center">
  <div class="uk-alert-success" uk-alert>
  <? if ($c_products) :?>
    <span>Found: <span class="uk-badge"><?=$c_products;?></span> products</span>
  <? else : ?>
    <span><?=$message;?></span>
  <? endif; ?>
    <a class="uk-alert-close" uk-close></a>
  </div>
</div>
<? endif; ?>

  <div class="uk-container uk-container-large">
    <div uk-grid class="uk-child-width-1-4@m uk-child-width-1-2@s uk-margin">
      <?php foreach ($f_products as $f_product) : ?>
          <div class="uk-card uk-card-default uk-card-body uk-text-center">
            <a href="<?=$f_product['product_link']?>" target="_blank">
              <p><?=$f_product['product_name']?></p>
            </a>
            <a href="<?=$f_product['product_img_url']?>">
              <img uk-img src="<?=$f_product['product_img_url']?>"></img>
            </a>
          </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php
include_once 'tpl_footer.php';
?>