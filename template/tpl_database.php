<?php
include_once 'tpl_header.php';
?>
<div class="uk-panel uk-panel-box uk-margin-top">
<? if ($message) :?>
  <div uk-alert><?=$message?></div>
<? endif; ?>
</div>
<?php
//dbio.Products.20190124-061020-435044
// var_dump('dbio.Products.'.date('Ymd-His-') . mt_rand(1000,999999));
die;
?>
<div uk-grid>
<table class="uk-table-condensed">
    <caption>Products</caption>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Name</th>
            <th>Image</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_products as $key => $all_product) :
            $all_product['product_description'] = substr(strip_tags($all_product['product_description']),0,100);
            ?>
        <tr>
            <td><?=$key?></td>
            <?php foreach ($all_product as $product_value) : ?>
            <td><?=$product_value?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total:</td>
            <td><?=$count_all_products?></td>
        </tr>
    </tfoot>
</table>
</div>
<?php
include_once 'tpl_footer.php';
?>