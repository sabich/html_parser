<?php
define ('PAGE_TITLE','Images');
define ('SHOW_ALL_PRODUCTS',true);

if (ob_get_level()) ob_end_clean();

include_once 'src/data.php';
include_once 'src/categories_controller.php';
include_once 'src/products_controller.php';
include_once 'src/images_controller.php';
include_once 'template/tpl_images.php';
?>