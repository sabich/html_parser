<?php
if ($categories) {
    $top_categories = array_keys($categories);
    $filename_json = 'src/json/products_category_'.filter_filename($top_category).'.json';
    $filename_attributes_json = 'src/json/products_attributes_category_'.filter_filename($top_category).'.json';
    if (is_file($filename_json)) {
        if (is_file($filename_attributes_json)) {
            $f_products_attributes = getJSON ($filename_attributes_json);
        } else {
            $f_products = getJSON ($filename_json);
            // $f_products = array_slice($f_products,0,10);
            foreach ($f_products as $key=>$item) {
                $f_products_attributes [] = [
                    'category' => $item['category'],
                    'sub_category' => $item['sub_category'],
                    'product_name' => $item['product_name'],
                    'attributes' => getAttributesBySite ($item['product_link'])
                ];
                echo nl2br (($key+1)."/".count($f_products)." Add attribute for ".$item['product_name']." success!\n");
            }

            if (count($f_products_attributes) > 0) {
                $message = crateJSON ($filename_attributes_json,$f_products_attributes);
            }
        }
    } else {
        $filename_attributes_full_json = 'src/json/products_attributes_full.json';
        if (is_file($filename_attributes_full_json)) {
            $attributes = getAttributesFromAllProducts (getJSON ($filename_attributes_full_json));
        }
        
        foreach ($attributes as $value) {
            if (is_array($value)) $attributes_full [] = getAttributeOneProduct ($value);
        }
        var_dump($attributes_full);
        // $attribute_names = getAttributesNameAll ($attributes);
        // $attribute_values = getAttributesValuesAll ($attributes);
        // echo createOptionsCSV($attribute_names,205933);
        // echo createOptionsValuesCSV($attribute_values,206514);
    }
}
// if (defined('SHOW_ALL_PRODUCTS')) {
//     if (SHOW_ALL_PRODUCTS) {
//         foreach ($top_categories as $top_category) {
//             $filename_attributes_json = 'src/json/products_attributes_category_'.filter_filename($top_category).'.json';
//             if (is_file($filename_json)) {
//                 $f_products_attributes = array_merge ($f_products_attributes,getJSON ($filename_attributes_json));
//             }
//         }
//         $message = crateJSON ('src/json/products_attributes_full.json',$f_products_attributes);
//     } else {
//         $message = 'SHOW_ALL_PRODUCTS is disabled!';
//     }
// } else {
//     $message = 'SHOW_ALL_PRODUCTS is undefined!';
// }
