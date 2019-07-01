<?php
define ('PAGE_TITLE','Attributes');
define ('SHOW_ALL_PRODUCTS',false);
if (ob_get_level()) ob_end_clean();

include_once 'src/data.php';
include_once 'src/categories_controller.php';
include_once 'src/attributs_controller.php';
include_once 'template/tpl_attributs.php';
?>