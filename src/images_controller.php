<?php
$img_is_empty=count(glob(dirname(__DIR__).'/img/*')) ? true : false;
$thumb_is_empty=count(glob(dirname(__DIR__).'/img/thumbnails/tn_*')) ? true : false;

$products_images_json = 'src/json/products_images.json';
$filename_images_json = 'src/json/filename_images.json';
if (is_file($products_images_json)) {
    $f_images = getJSON ($products_images_json);
    $filename_images = pathImages ($f_images);
    if (is_file($filename_images_json)) {
        $p_images = getJSON ($filename_images_json);
    } else {
        $message = crateJSON ($filename_images_json, $filename_images);
    }
    if ($img_is_empty) {
        if ($thumb_is_empty) {
            renameImages (dirname(__DIR__).'/img/thumbnails/');
        } elseif (!is_dir(dirname(__DIR__).'/img/thumbnails')) {
            echo '<p style="color:red">Please create thumbnails images!';
        }
    } else {
        createImages ($f_images);
    }
} else {
    if (count($f_products) > 0) {
        foreach ($f_products as $product) {
            $images [] = [
                'p_category' => $product['sub_category'],
                'p_name' => $product['product_name'],
                'p_img_link' => $product['product_img_url'],
            ];
        }
        $message = crateJSON ($products_images_json, $images);
    } else {
        echo '<p style="color:red">Please create images for products!</p>';
    }
}