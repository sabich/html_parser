<?php
define ('CATEGORIES_FILENAME','src/json/petsmart_category.json');
define ('SUB_CATEGORIES',false);

$categories = [];

if (is_file(CATEGORIES_FILENAME)) {
    $categories = getJSON (CATEGORIES_FILENAME);
} else {
    $main_categories = getCategories (URL);
    foreach ($main_categories as $name => $url) {
        $sub_categories = getSubCategories(domFirst($url,'#category-level-1'));
        if ($sub_categories === null) {
            $categories [$name] = getTopCategory ($url);
        } else {
            $categories [$name] = $sub_categories;
        }
    }
    $message = crateJSON (CATEGORIES_FILENAME, $categories);
}

if (isset ($_GET['category'])) {
    $top_category = $_GET['category'];
    if(is_array($categories[$top_category])) {
        $sub_categories = array_column($categories[$top_category],'name');
    }
    if (isset ($_GET['sub-category'])) {
        $sub_category = $_GET['sub-category'];
    }
} else {
    foreach ($categories as $name=>$sub_category) {
        $top_categories [] = $name;
        $sub_categories [$name] = $sub_category;
    }
}
