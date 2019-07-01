<?php include_once 'tpl_header.php'; ?>
<div class="uk-section uk-padding-remove">
    <div class="uk-container">
        <div class="uk-grid-collapse uk-child-width-expand@s" uk-grid>
            <form>
                <fieldset class="uk-fieldset">

                    <legend class="uk-legend">Please input next id_category for <?= GLOBAL_TOP_CATEGORY_SHOP ?></legend>

                    <div class="uk-margin">
                        <input class="uk-input uk-form-width-medium" type="text" placeholder="id_category" name="count-category">
                        <button class="uk-button uk-button-primary">Submit</button>
                    </div>
                </fieldset>

<?php if($categories_id) : ?>
                <p>Please insert text below:</p>
                <div class="uk-grid-divider uk-child-width-expand@s" uk-grid>
                    <div>
                        <textarea class="uk-textarea uk-background-muted" rows="<?=($all_count_categories+1)?>" placeholder="Textarea"><?= $sql_categories ?></textarea>
                    </div>
                    <div>
                        <textarea class="uk-textarea uk-background-muted" rows="<?=($all_count_categories+1)?>" placeholder="Textarea"><?= $sql_categories_desc ?></textarea>
                    </div>
                </div>
<?php endif; ?>

            </form>

        </div>
    </div>
</div>

<?php include_once 'tpl_footer.php'; ?>