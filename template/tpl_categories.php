<?php
include_once 'tpl_header.php';
// var_dump($categories);die;
?>

<div class="uk-container uk-container-center uk-margin-top">
    <form class="uk-form-horizontal" method="get">
        <label class="uk-form-label" for="form-h-select">Category</label>
        <div class="uk-form-controls">
            <select class="uk-select uk-form-width-large" id="form-h-select" name="category">
                <?php
                foreach ($categories as $name => $url) : 
                ?>
                <option value="<?=$name?>" <?=($name===$_GET['category']) ? 'selected' : '' ?>><?=$name?></option>
                <?php endforeach; ?>
            </select>
            <?php 
                if ($top_category && SUB_CATEGORIES) :
            ?>
            <select class="uk-select uk-form-width-large" id="form-h-select" name="sub-category">
                <?php
                foreach ($sub_categories as $sub_category) : 
                ?>
                <option value="<?=$sub_category?>" <?=($sub_category===$_GET['sub-category']) ? 'selected' : '' ?>><?=$sub_category?></option>
                <?php endforeach; ?>
            </select>
            <?php endif;?>
            <button class="uk-button uk-button-primary">Show Products</button>
        </div>
    </form>
</div>
<? if ($message) :?>
<div uk-alert><?=$message?></div>
<? endif; ?>
<?php
include_once 'tpl_products.php';
include_once 'tpl_footer.php';
?>