<?php
$f_products = [];

if ($top_category) {
$filename_json = 'src/json/products_category_'.filter_filename($top_category).'.json';
$full_filename_json = 'src/json/full_products_category_'.filter_filename($top_category).'.json';
    if (is_file($filename_json)) {
        $f_products = getJSON ($filename_json);
        if (count($f_products) > 0 && count($filename_images) > 0) {
            if (is_file($full_filename_json)) {
                $full_products = getJSON ($full_filename_json);
            } else {
                $full_products = setProducts ($f_products,$filename_images);
                $message = crateJSON ($full_filename_json, $full_products);
            }
        }
    } else {
        foreach ($categories[$top_category] as $value) { 
            for ($i=0; $i < $value['count']; $i+=24) { 
                $p_list = $value['link'].'?start='.$i;
                $products = domFind ($p_list,'.name-link');
                if(is_array($products)) {
                    foreach ($products as $key=>$product) {
                        // echo ($key+1).'/'.count($products),cleanURL ('https://www.petsmart.com'.$product->href),'<br>';
                        if(array_key_exists('name',$value)) {
                            echo ($key+1),'/',count($products),' ',$value['name'],'<br>';
                            $j_products [] = [
                                'category'          => $top_category,
                                'sub_category'      => $value['name'],
                                'product_link'      => cleanURL ('https://www.petsmart.com'.$product->href),
                                'product_name'      => trim($product->first('.product-tile .product-name')->text()),
                                'product_img_url'   => cleanURL ($product->first('.product-tile .product-image img')->src),
                            ];
                        } else {
                            echo ($key+1),'/',count($products),' ',$top_category,'<br>';
                            $j_products [] = [
                                'category'          => $top_category,
                                'product_link'      => cleanURL ('https://www.petsmart.com'.$product->href),
                                'product_name'      => trim($product->first('.product-tile .product-name')->text()),
                                'product_img_url'   => cleanURL ($product->first('.product-tile .product-image img')->src),
                            ];
                        }
                    }
                }
            }
        }
        if (is_array($j_products)) $message = crateJSON ($filename_json, $j_products);
    }
    if ($sub_category) {
        $temp_arr = [];
        foreach ($f_products as $f_product) {
            if ($f_product['sub_category'] == $sub_category) {
                $temp_arr [] = $f_product;
            }
        }
        $f_products = $temp_arr;
    }
} else {
    if (defined('SHOW_ALL_PRODUCTS')) {
        if (SHOW_ALL_PRODUCTS) {
            foreach ($top_categories as $top_category) {
                $filename_json = 'src/json/products_category_'.filter_filename($top_category).'.json';
                if (is_file($filename_json)) {
                    $f_products = array_merge ($f_products,getJSON ($filename_json));
                }
            }
        } else {
            $message = 'SHOW_ALL_PRODUCTS is disabled!';
        }
    } else {
        $message = 'SHOW_ALL_PRODUCTS is undefined!';
    }
}

if ($f_products) {
    $message = 'Found: <strong>'.count($f_products).'</strong> products';
}