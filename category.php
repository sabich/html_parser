<?php
define ('SHOW_ALL_PRODUCTS',false);
define ('PAGE_TITLE','Categories');

/***
 * очистка буффера для быстрого отображения результатов работы скриптов 
 * 
 *   while(ob_get_level() > 0){
 *     ob_end_clean();
 *   }
***/
if (ob_get_level()) ob_end_clean();

include_once 'src/data.php';
include_once 'src/categories_controller.php';
include_once 'src/images_controller.php';
include_once 'src/products_controller.php';
include_once 'template/tpl_categories.php';