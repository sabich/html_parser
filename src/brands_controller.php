<?php

if ($categories) {
    $top_categories = array_keys($categories);
    $all_products = [];

    $all_products_filename_json = 'src/json/all_products.json';
    $all_brands_filename_json = 'src/json/all_brands.json';
    $db_brands = getJSON ("src/json/db_brands.json");

    foreach ($top_categories as $top_category) {
        $full_filename_json = 'src/json/full_products_category_'.filter_filename($top_category).'.json';
        $all_products = array_merge($all_products,getJSON ($full_filename_json));

        // $arr_products_category = getJSON ($full_filename_json);
        // foreach ($arr_products_category as $key=>$product_category) {
        //     if (!array_key_exists('product_brand', $product_category)) {
        //         $message .= $key."--- Category: ".$top_category.
        //             "<br>Product ".$product_category['product_name'].
        //             " not content product description<br>";
        //     }
        // }
    }
    if (is_file($all_products_filename_json)) {
        $all_products = getJSON ($all_products_filename_json);
        $message = 'All products count: '.count($all_products);
    } else {
        $message = crateJSON ($all_products_filename_json, $all_products);
    }
} else {
    $message = 'Not found products!';
}

if (is_array($all_products)) {
    if (is_file($all_brands_filename_json)) {
        $all_products_brands = getJSON ($all_brands_filename_json);
        $message = 'All Brands count: '.count($all_products_brands);
    } else {
        $all_products_brands = getBrands ($all_products,$db_brands);
        asort($all_products_brands);
        $message = crateJSON ($all_brands_filename_json, $all_products_brands);
    }
    $count_all_products_brands = count($all_products_brands);
    
}