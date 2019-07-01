<?php

if ($categories) {
    $top_categories = array_keys($categories);
    $all_products = [];

    $all_products_filename_json = 'src/json/all_products.json';

    foreach ($top_categories as $top_category) {
        $full_filename_json = 'src/json/full_products_category_'.filter_filename($top_category).'.json';
        $all_products = array_merge($all_products,getJSON ($full_filename_json));
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

    $ids = 20574;

    foreach ($all_products as $key => $all_product) {
        unset($all_product['product_attributes']);
        $products [$key+$ids] = $all_product;
    }
    $all_products = $products;
    // $all_products=array_slice($all_products,0,2000,true);
    //dbio.Products.20190124-061020-435044
    $message = createProductsCSV ($all_products);
    $count_all_products = count($all_products);
}