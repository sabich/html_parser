<?php
include_once 'tpl_header.php';
// var_dump($all_products_attributes);
?>
<div class="uk-panel uk-panel-box uk-margin-top">
<? if ($message) :?>
  <div uk-alert><?=$message?></div>
<? endif; ?>
</div>

<div uk-grid class="uk-margin-left">
<table class="uk-table-condensed">
    <caption>Brands</caption>
    <thead>
        <tr>
            <th>id</th>
            <th>Brand</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_products_brands as $key=>$brand) : ?>
        <tr>
            <td><?=$key?></td>
            <td><?=$brand?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total:</td>
            <td><?=$count_all_products_brands?></td>
        </tr>
    </tfoot>
</table>
</div>
<?php
include_once 'tpl_footer.php';
?>