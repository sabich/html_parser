<?php
include_once 'tpl_header.php';
// var_dump(array_column($all_products,'product_attributes','product_name'));
// var_dump($beds_furniture[0]['product_link']);
// die;
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
<div class="uk-container uk-container-center">
<? if (count($f_products_attributes)) :?>
  <div class="uk-alert-success" uk-alert>
    <span>Found: <span class="uk-badge"><?=count($f_products_attributes);?></span> products</span>
    <a class="uk-alert-close" uk-close></a>
  </div>
  <? else : ?>
    <div class="uk-panel uk-panel-box uk-margin-top">
        <div uk-alert><?=$message?></div>
    <a class="uk-alert-close" uk-close></a>
    </div>
<? endif; ?>
</div>
<div uk-grid>
<?php if(count ($f_products_attributes)) : ?>
<table class="uk-table-condensed">
    <caption>Products</caption>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>sub_category</th>
            <th>product_name</th>
            <th>attributes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($f_products_attributes as $key => $all_product) : ?>
        <tr>
            <td><?=$key?></td>
            <td><?=$all_product['category']?></td>
            <td><?=$all_product['sub_category']?></td>
            <td><?=$all_product['product_name']?></td>
            <? if (count($all_product['attributes'])) :?>
            <?php foreach ($all_product['attributes'] as $k=>$product_value) : ?>
                <td><?=$k?></td>
                <?php foreach ($product_value as $v) : ?>
                <td><?=$v?></td>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total:</td>
            <td><?=count($f_products_attributes)?></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>
</div>
<?php
include_once 'tpl_footer.php';
?>