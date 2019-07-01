<?php
require "vendor/autoload.php";

use DiDom\Document;

define ('GLOBAL_TOP_CATEGORY_SHOP','Small pet');
define ('GLOBAL_TOP_CATEGORY','small-pet');
define ('URL','https://www.petsmart.com/'.GLOBAL_TOP_CATEGORY);

function domFind ($url,$template) {
    if (!$data = @file_get_contents($url)) {
        $error = error_get_last();
        echo nl2br ("HTTP request failed. Error was: ".$error['message']."\n");
        echo nl2br ("Problem URL: <a href=".$url." target=\"_blank\">link</a>\n");
    } else {
        $document = new Document($url, true);
            if ($document->has($template)) {
                return $document->find($template);
            } else {
                echo nl2br ("Not found tepmlate: '".$template." in the path <a href=".$url." target=\"_blank\">link</a>'\n");
            }
        }
    return false;
}

function domFirst ($url,$template) {
    if (!$data = @file_get_contents($url)) {
        $error = error_get_last();
        echo nl2br ("HTTP request failed. Error was: ".$error['message']."\n");
        echo nl2br ("Problem URL: <a href=".$url." target=\"_blank\">link</a>\n");
    } else { 
        $document = new Document($url, true);
            if ($document->has($template)) {
                return $document->first($template);
            } else {
                echo nl2br ("Not found tepmlate: '".$template." in the path <a href=".$url." target=\"_blank\">link</a>'\n");
            }
    }
    return false;
}

function cleanURL ($url) {
    return strtok($url, '?');
}

function getCategories ($url) {
    $nav = domFirst ($url,'#category-landing-left-nav')->find('.nav-first-level-child');
    foreach ($nav as $item) {
        $cat_name = $item->first('.top-cats-text')->text();
        $cat_link = $item->first('.left-nav-link')->href;
        if ($cat_name != 'Pharmacy' && $cat_name != 'Starter Kits') {
            $categories [$cat_name] = $cat_link;
        }
    }
    return $categories;
}

function getTopCategory (string $url) {
    $count = intval (domFirst($url,'.results-hits')->text());
    $result [] = [
        'link' => $url,
        'count' => $count
    ];
    return $result;
}

function getSubCategories ($_dom) {
    if(!is_object($_dom)) return null;
    $nav = $_dom->find('.refinement-link');
    foreach ($nav as $item) {
        $count = intval($item->first('span')->text());
        $cat_name = trim(str_replace($item->first('span')->text(),'',$item->text()));
        $cat_link = cleanURL ($item->href);
        $categories_sub [] =[
            'name' => $cat_name,
            'link' => $cat_link,
            'count' => $count
        ];
    }
    return $categories_sub;
}

function getProductDescription ($_url) {
    set_time_limit (30);
    $_dom = domFirst($_url,'#product-content');
    if(!is_object($_dom)) return null;
    $p_brand = ($_dom->has('.brand-by')) ? trim($_dom->first('.brand-by')->text()) : '' ;
    if($_dom->first('.ispu-price')->has('span')) {
        $p_price = preg_replace("/[^,.0-9]/", '', trim($_dom->first('.ispu-price span')->text()));
    }
    
    $p_attributes = getAttributesBySite ($_url);
    
    $p_details_text = $_dom->first('.product-description')->innerHtml();

    $p_details = [
        'product_brand' => $p_brand,
        'product_price' => $p_price,
        'product_attributes' => $p_attributes,
        'product_description' => $p_details_text,
    ];

    return $p_details;
}

function getBrands ($products,$brands_key) {
    $brands = array_unique (array_column($products,'product_brand'));
    foreach ($brands as $key => $value) {
        $result [array_search($value,$brands_key)] = $value;
    }
    return $result;
}

function getAttributesBySite (string $link) {
    set_time_limit (300);
    $p_attributes_array = domFind ($link,'.product-variations .attribute');
    if(!is_array($p_attributes_array)) return;
    foreach ($p_attributes_array as $p_attribute) {
        $p_attribute_name = trim($p_attribute->first('.label span')->text());
        foreach ($p_attribute->find('.value') as $attr_val_vrap) {
            foreach ($attr_val_vrap->find('.selectable a') as $arr_val) {
                // $p_attributes [$p_attribute_name][] = trim(preg_replace('/\s+/', ' ', $arr_val->text()));
                $array_val = $arr_val->first('.ps-ele-select-size-price-cont');
                if ($array_val > 0) {
                    $attr = explode (': ',$arr_val->title)[1];
                    $p_attributes [$p_attribute_name][] = $attr.' - '.trim($array_val->text());
                } else {
                    $p_attributes [$p_attribute_name][] = explode (': ',$arr_val->title)[1];
                }
            }
        }
    }
    
    return $p_attributes;
}

function getAttributesFromAllProducts (array $products) {
    $result = array_column($products,'attributes');
    return $result;
}

function getAttributesNameAll (array $attr) {
    $keys = array();
    foreach (getAttributesFromAllProducts ($attr) as $key => $item) {
        if(is_array($item)) $keys = array_merge ($keys,array_keys($item));
    }
    return array_values(array_unique($keys));
}

function getAttributeOneProduct (array $product) {

    foreach ($product as $key => $value) {
        foreach ($value as $item) {
            if (count($item) > 0) {
                $value_name = explode(" - ",$item)[0];
                $value_amount = (explode(" - ",$item)[1]) ? explode(" - ",$item)[1] : '0.00';
                preg_match("/[0-9\.]+/", $value_amount,$matches);
                $value_amount = $matches[0];
                $attr_value [] = [
                    'name' => $value_name,
                    'price' => $value_amount
                ];
            } else {
                continue;
            }
        }
        $result [] = [
            'attribute_name' => $key,
            'attribute_value_price' => $attr_value
        ];
    }
    // var_dump($result);
    return $result;
}

function getAttributesValuesAll (array $attr) {
    $result = [];
    $attributes = getAttributesFromAllProducts ($attr);
    foreach ($attributes as $key=>$value) {
        if (is_array($value)) {
            foreach ($value as $value2) {
                array_walk ($value2,function (&$item) {$item = explode(" - ",$item)[0];});
                $result = array_merge($result,$value2);
            }
        }
    }
    
    natsort ($result);

    return array_values(array_unique ($result));
}

function getProductImage ($images,$category,$product) {
    foreach ($images as $image) {
        if ($image['product_category'] == $category && $image['product_name'] == $product)
            return $image['product_image'];
    }
    return '';
}

function setProducts ($f_products, $p_images) {
    $c = count($f_products);
    foreach ($f_products as $key =>$f_product) {
        $full_products [$key] = [
            'product_category' => $f_product['category'],
            'product_sub_category' => $f_product['sub_category'],
            'product_name' => $f_product['product_name'],
            'product_image' => getProductImage ($p_images,$f_product['sub_category'],$f_product['product_name'])
        ];

        $products_descriptions = getProductDescription ($f_product['product_link']);
        if (is_array($products_descriptions)) {
            $full_products [$key] += $products_descriptions;
        } elseif (empty($products_descriptions)) {
            echo "<p style=\"color:red\">".($key+1)."/".$c." - Not found description for the ".$f_product['product_name']."</p>";
        }
        if($full_products [$key]) {
            echo "<p style=\"color:green\">".($key+1)."/".$c." - Add product ".$f_product['product_name']."</p>";
        }
        // $full_products [$key] = array_merge($full_products [$key],['image' => getProductImage ($p_images,$f_product['sub_category'],$f_product['product_name'])]);
    }
    return $full_products;
}

function crateJSON (string $filename, array $data) {

    if (empty($data)) return "Not found data!!!";

    $json = json_encode($data,JSON_PRETTY_PRINT);

    if (file_put_contents($filename, $json)) {
        $result_message = "<p>File '.$filename.' created...<br>";
        $result_message .= "Found <strong>".count($data)."</strong> products<br>";
        $result_message .= "Please refreash page!</p>";
        return $result_message;
    } else { 
        return "File '.$filename.' not created...";
    }
}

function getJSON ($filename) {
    if (!is_file($filename)) return 'not found file: '.$filename;
    
    $json = file_get_contents($filename);

    return json_decode ($json, true);
}

function createProductsCSV (array $products) {

    $filename = 'src/csv/'.'dbio.Products.'.date('Ymd-His-') . mt_rand(1000,999999).'.csv';
    
    $content [] = [
        'v_products_id',
        'v_products_type',
        'v_products_quantity',
        'v_products_model',
        'v_products_image',
        'v_products_price',
        'v_products_virtual',
        'v_products_date_added',
        'v_products_last_modified',
        'v_products_date_available',
        'v_products_weight',
        'v_products_status',
        'v_products_ordered',
        'v_products_quantity_order_min',
        'v_products_quantity_order_units',
        'v_products_priced_by_attribute',
        'v_product_is_free',
        'v_product_is_call',
        'v_products_quantity_mixed',
        'v_product_is_always_free_shipping',
        'v_products_qty_box_status',
        'v_products_quantity_order_max',
        'v_products_sort_order',
        'v_products_discount_type',
        'v_products_discount_type_from',
        'v_products_price_sorter',
        'v_products_mixed_discount_quantity',
        'v_metatags_title_status',
        'v_metatags_products_name_status',
        'v_metatags_model_status',
        'v_metatags_price_status',
        'v_metatags_title_tagline_status',
        'v_products_guid',
        'v_products_family',
        'v_products_name_en',
        'v_products_description_en',
        'v_products_short_desc_en',
        'v_products_url_en',
        'v_products_viewed_en',
        'v_manufacturers_name',
        'v_tax_class_title',
        'v_categories_name',
        'v_dbio_command'
    ];

    foreach ($products as $key => $value) {
        $content [] = [
            $key,                           //v_products_id
            1,                              //v_products_type
            1000,                           //v_products_quantity
            'm_'.$key,                      //v_products_model
            $value['product_image'],        //v_products_image
            number_format (floatval ($value['product_price']),2),        //v_products_price
            0,                              //v_products_virtual
            date("Y-m-d H:i:s"),            //v_products_date_added - 'date'
            date("Y-m-d H:i:s"),            //v_products_last_modified - 'date'
            '',                             //v_products_date_available - ''
            0,                             //v_products_weight - 0
            1,                              //v_products_status - 1
            0,                              //v_products_ordered - 0
            1,                              //v_products_quantity_order_min - 1
            1,                              //v_products_quantity_order_units - 1
            0,                              //v_products_priced_by_attribute - 0
            0,                              //v_product_is_free - 0
            0,                              //v_product_is_call - 0
            1,                              //v_products_quantity_mixed - 1
            0,                              //v_product_is_always_free_shipping - 0
            1,                              //v_products_qty_box_status - 1
            0,                              //v_products_quantity_order_max - 0
            10,                             //v_products_sort_order - 10
            0,                              //v_products_discount_type - 0
            0,                              //v_products_discount_type_from - 0
            number_format (floatval ($value['product_price']),4),        //v_products_price_sorter - '0.0000'
            1,                              //v_products_mixed_discount_quantity - 1
            0,                              //v_metatags_title_status - 0
            0,                              //v_metatags_products_name_status - 0
            0,                              //v_metatags_model_status - 0
            0,                              //v_metatags_price_status - 0
            0,                              //v_metatags_title_tagline_status - 0
            '',                             //v_products_guid - ''
            '',                             //v_products_family - ''
            $value['product_name'],         //v_products_name_en - 'name'
            $value['product_description'],  //v_products_description_en - 'desc'
            '',                             //v_products_short_desc_en - 'short'
            '',                             //v_products_url_en - ''
            0,                              //v_products_viewed_en - 0
            $value['product_brand'],        //v_manufacturers_name - 'brand'
            '',                             //v_tax_class_title - ''
            GLOBAL_TOP_CATEGORY_SHOP.'^'.$value['product_category'].
            (($value['product_sub_category'] != null) ? '^'.$value['product_sub_category'] : ''), //v_categories_name - 'category'
            '',                             //v_dbio_command - ''
        ];
    }
    $fp = fopen($filename, 'w');
    foreach ($content as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
    return "File '.$filename.' created...";
}

// v_products_options_id,v_language_id,v_products_options_name,v_products_options_sort_order,v_products_options_type
function createOptionsCSV (array $atttibutes, int $offset=0) {

    $filename = 'src/csv/'.'dbio.ProductsOptions.'.date('Ymd-His-') . mt_rand(1000,999999).'.csv';

    $content [] = [
        'v_products_options_id',
        'v_language_id',
        'v_products_options_name',
        'v_products_options_sort_order', 
        'v_products_options_type'
    ];
    foreach ($atttibutes as $key => $value) {
        $content [] = [
            'v_products_options_id' => ($key+$offset),
            'v_language_id' => 1,
            'v_products_options_name' => $value,
            'v_products_options_sort_order' => 0, 
            'v_products_options_type' => 0
        ];
    }
    $fp = fopen($filename, 'w');
    foreach ($content as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
    return "File '.$filename.' created...";
}

//v_products_options_values_id,v_language_id,v_products_options_values_name,v_products_options_values_sort_order
function createOptionsValuesCSV (array $atttibutes_values, int $offset=0) {

    $filename = 'src/csv/'.'dbio.ProductsOptionsValues.'.date('Ymd-His-') . mt_rand(1000,999999).'.csv';

    $content [] = [
        'v_products_options_values_id',
        'v_language_id',
        'v_products_options_values_name'
    ];
    foreach ($atttibutes_values as $key => $value) {
        $content [] = [
            'v_products_options_id' => ($key+$offset),
            'v_language_id' => 1,
            'v_products_options_name' => $value
        ];
    }
    $fp = fopen($filename, 'w');
    foreach ($content as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
    return "File '.$filename.' created...";
}

function filter_filename($filename, $beautify=true) {
    // sanitize filename
    $filename = preg_replace(
        '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&°®™\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
        '-', $filename);
    // avoids ".", ".." or ".hiddenFiles"
    $filename = ltrim($filename, '.-');
    // optional beautification
    if ($beautify) $filename = beautify_filename($filename);
    // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    return $filename;
}

function beautify_filename($filename) {
    // reduce consecutive characters
    $filename = preg_replace(array(
        // "file   name.zip" becomes "file-name.zip"
        '/ +/',
        // "file___name.zip" becomes "file-name.zip"
        '/_+/',
        // "file---name.zip" becomes "file-name.zip"
        '/-+/'
    ), '-', $filename);
    $filename = preg_replace(array(
        // "file--.--.-.--name.zip" becomes "file.name.zip"
        '/-*\.-*/',
        // "file...name..zip" becomes "file.name.zip"
        '/\.{2,}/'
    ), '.', $filename);
    // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
    $filename = mb_strtolower($filename, mb_detect_encoding($filename));
    // ".file-name.-" becomes "file-name"
    $filename = trim($filename, '.-');
    return $filename;
}

/* 
$data_images = [
    'p_name',
    'p_img_link'
]
*/

function pathImages ($data_images) {
    $extension = '.jpeg';
    foreach ($data_images as $data_image) {
        $filename_images [] = [
            'product_category' => $data_image['p_category'],
            'product_name' => $data_image['p_name'],
            'product_image' => filter_filename($data_image['p_name']).$extension,
        ];
    }
    return $filename_images;
}

function createImages ($data_images) {
    set_time_limit (300);
    $extension = '.jpeg';
    foreach ($data_images as $key => $data_image) {
        $link = ($data_image['p_img_link']);
        $filename = filter_filename($data_image['p_name']).$extension;
        echo ($key+1).'/'.count($data_images);
        // echo 'Sub-Category: '.$data_image['p_category'],'<br>';
        if(@copy($link,'img/'.$filename)) {
            echo ' Image '.$filename.' successfully created!<br>';
        } else {
            echo '<p style=\"color:red\">'.' Image '.$filename.' not created!<p>';
        }
    }
}

function renameImages ($dir) {
    foreach (glob($dir.'*') as $filename){
        $name = pathinfo($filename,PATHINFO_BASENAME);
        $dir = pathinfo($filename,PATHINFO_DIRNAME)."/";
        if (substr($name, 0, 3) == "tn_") {
            rename($filename, $dir . substr($name,3,strlen($name)));
            echo "file $name rename<br>";
        }
    }
}