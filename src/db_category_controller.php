<?php

$categories_json = getJSON ('src/json/petsmart_category.json');

if (is_string($categories_json)) exit ($categories_json);

foreach ($categories_json as $key => $value) {
    if(array_key_exists('name',$value[0])) {
        $categories [] = [
            'category' => $key,
            'sub_category' => array_column ($value,'name')
        ];
    } else {
        $categories [] = [
            'category' => $key
        ];    
    }
}
if (isset($_GET['count-category']) && $_GET['count-category'] > 0) {
    $categories_id = $_GET['count-category'];
    $top_category_name = GLOBAL_TOP_CATEGORY_SHOP;
    $top_category_id = $categories_id;
    $sql_categories = "INSERT INTO `zen_categories` (`categories_id`, `parent_id`, `sort_order`, `date_added`) VALUES \n(".$top_category_id.",0,0,\"".date("Y-m-d H:i:s")."\"),\n";
    $sql_categories_desc = "\nINSERT INTO `zen_categories_description` (`categories_id`, `categories_name`) VALUES \n(".$categories_id++.",\"$top_category_name\"),\n";
    $all_count_categories = 1;
    foreach ($categories as $key=>$value) {
        $sql_categories .= "(".($categories_id).",".$top_category_id.",0,\"".date("Y-m-d H:i:s")."\"),\n";
        $sql_categories_desc .= "(".$categories_id.",\"".$value['category']."\"),\n";
        $current_category_id = $categories_id;
        $categories_id++;
        if(array_key_exists('sub_category',$value)) {
            foreach ($value['sub_category'] as $k=>$item) {
                $sql_categories .= "(".($categories_id).",".$current_category_id.",0,\"".date("Y-m-d H:i:s")."\"),\n";
                $sql_categories_desc .= "(".($categories_id).",\"".$item."\"),\n";
                $categories_id++;
                $all_count_categories += $k;
            }
        }
    }
    $all_count_categories += count($categories);
    $sql_categories_desc = substr_replace($sql_categories_desc,';',-2);
    $sql_categories = substr_replace($sql_categories,';',-2);
    // echo nl2br ($sql_categories);
    // echo nl2br ($sql_categories_desc);
}

?>