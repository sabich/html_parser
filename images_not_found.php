<?php

define('FDT_CATEGORY_ID', 412);
define('ROOT_URL', 'https://www.petsmart.com');
define('SEARCH_URL', 'https://www.petsmart.com/search/?q=');
define('PRODUCT_IMAGE_JSON', 'src/json/notfoundimg.json');

require('../infoproduct/includes/configure.php');
require('../infoproduct/lib/dibi/dibi.phar');
require "vendor/autoload.php";

use DiDom\Document;

function getConnection() {
    try {
        $db = dibi::connect([
            'driver'            => 'mysqli',
            'host'              => DB_SERVER,
            'username'          => DB_SERVER_USERNAME,
            'password'          => DB_SERVER_PASSWORD,
            'database'          => DB_DATABASE,
            'charset'           => !empty($_POST['charset']) ? $_POST['charset'] : 'utf8',
            'result|formatDate' => 'resultDate',
        ]);
    } catch (Dibi\Exception $e) {
        exit(get_class($e).' : '.$e->getMessage());
    }
    
    return $db;
}

function getCategories($db) {
    $categories_obj = $db->query('SELECT c.categories_id, cd.categories_name FROM zen_categories c, zen_categories_description cd WHERE c.categories_id = cd.categories_id AND c.`categories_status` = 1');
    
    $categories = array();
    foreach ($categories_obj as $category_obj) {
        $categories[] = array(
            'categories_id' => $category_obj->categories_id,
            'categories_name' => $category_obj->categories_name
        );
    }
    
    return $categories;
}

function getExcludedСategories($db) {
    $excludedCategories_obj = $db->query('SELECT categories_id FROM zen_categories WHERE parent_id = ?', FDT_CATEGORY_ID);

    $excludedCategories = array();
    foreach ($excludedCategories_obj as $excludedCategory_obj) {
        $excludedCategories[] = $excludedCategory_obj->categories_id;
    }

    //Artisan 
    $excludedCategories[] = 409;

    return $excludedCategories;
}

function deleteExcludedCategories($categories, $excludedCategories) {
    $clearCategories = array();
    
    foreach ($categories as $category) {
        if (!in_array($category['categories_id'], $excludedCategories)) {
            $clearCategories[] = $category;    
        }
    }
    
    return $clearCategories;
}

function getBrokenProducts($db, $categories) {
    $result = array();
    
    foreach ($categories as $category) {
        $products_obj = $db->query('SELECT p.products_id, pd.products_name FROM zen_products p, zen_products_description pd WHERE p.products_id = pd.products_id AND p.products_image = "" AND p.master_categories_id = ?', $category['categories_id']);
        
        if ($products_obj->getRowCount()) {
        
            $products = array();
            foreach ($products_obj as $product_obj) {
                $products[] = array(
                    'products_id' => $product_obj->products_id,
                    'products_name' => $product_obj->products_name
                );
            }
            
            $result[] = array(
                'categories_id' => $category['categories_id'],
                'categories_name' => $category['categories_name'],
                'products' => $products
            );
        
        }
    }
    
    return $result;
}

function printBrokenProducts($brokenProducts) {
    $numberProducts = $numberNotFoundImages = 0;
    
    foreach ($brokenProducts as $brokenProduct) {
        echo "<b>" . $brokenProduct['categories_id'] . " " . $brokenProduct['categories_name'] . "</b><br>";
            
        $products = $brokenProduct['products'];
        foreach ($products as $product) {
            if ($product['image']) {
                $nameImage = filter_filename($product['products_name']) . '.jpeg';
                $sql = 'UPDATE zen_products SET products_image = "' . $nameImage . '" WHERE products_id = ' . $product['products_id'] . ";\n";
                
                echo " " . $product['products_id'] . " " . $product['products_name'] . " <a href='" . $product['image'] . "'>Images</a><br><textarea cols=150>" . $sql . "</textarea><br>";
            } else {
                echo " " . $product['products_id'] . " " . $product['products_name'] . " <b>IMAGES NOT FOUND</b><br>";
                $numberNotFoundImages++;
            }
            
            $numberProducts++;
        }
    }
    
    echo "Total products: " . $numberProducts . " (" . $numberNotFoundImages .")";
}

//получаем изображение продукта, если это возможно
function getProductImages($product_name) {
    $document = new Document(SEARCH_URL . urlencode($product_name), true);

    $image = $document->find('.hide-on-scene7-sdk-load img')[0]->src; 
    
    if ($image) return str_replace('?$pdp-placeholder-desktop$', '', $image);
    
    // $redirect = $document->find('.no-hits-search-term-suggest')[0]->href;
    // if ($redirect) {
    //     echo $redirect . "<br>";
        
    //     $document = new Document(ROOT_URL . $redirect);
        
    //     $image = $document->find('.hide-on-scene7-sdk-load img')[0]->src; 
    
    //     if ($image) return str_replace('?$pdp-placeholder-desktop$', '', $image);
    // }
    
    return false;
}

function getProductsImages($brokenProducts) {
    foreach ($brokenProducts as $categoryIndex => $brokenProduct) {
        $products = $brokenProduct['products'];
        foreach ($products as $productIndex => $product) {
            $brokenProducts[$categoryIndex]['products'][$productIndex]['image'] = getProductImages($product['products_name']);
            
        }
    }
    
    return $brokenProducts;
}

function filter_filename($filename, $beautify=true) {
    $filename = preg_replace(
        '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&°®™\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
        '-', $filename);
    $filename = ltrim($filename, '.-');
    if ($beautify) $filename = beautify_filename($filename);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
    return $filename;
}

function beautify_filename($filename) {
    $filename = preg_replace(array(
        '/ +/',
        '/_+/',
        '/-+/'
    ), '-', $filename);
    $filename = preg_replace(array(
        '/-*\.-*/',
        '/\.{2,}/'
    ), '.', $filename);
    $filename = mb_strtolower($filename, mb_detect_encoding($filename));
    $filename = trim($filename, '.-');
    return $filename;
}

function downloadImages($brokenProducts) {
    foreach ($brokenProducts as $categoryIndex => $brokenProduct) {
        $products = $brokenProduct['products'];
        foreach ($products as $productIndex => $product) {
            if ($product['image']) {
                $nameImage = filter_filename($product['products_name']) . '.jpeg';
                $brokenProducts[$categoryIndex]['products'][$productIndex]['image_name'] = $nameImage;
                
                $ch = curl_init($product['image']);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($ch);
                
                
                $result = json_decode(file_get_contents($product['image']));
                
                echo $result;
                echo curl_error($ch);
                curl_close($ch);
                file_put_contents('img/' . $nameImage, base64_decode($result));
                
                $sql = 'UPDATE zen_products SET products_image = ' . $nameImage . ' WHERE products_id = ' . $product['products_id'] . "\n";  
                file_put_contents('images_not_found.sql', $sql, FILE_APPEND);
            }
        }
    }

}

if (isset($_GET['printProducts'])) {
    $brokenProducts = json_decode(file_get_contents(PRODUCT_IMAGE_JSON), true);
    printBrokenProducts($brokenProducts);
}

if (isset($_GET['getImages']) && file_exists(PRODUCT_IMAGE_JSON)) {
    $brokenProducts = json_decode(file_get_contents(PRODUCT_IMAGE_JSON), true);
    downloadImages($brokenProducts);
    
    echo "Images Create. See images_not_found.sql";
}

if (isset($_GET['getProducts']))  {
    $db = getConnection();

    $categories = getCategories($db);
    $excludedCategories = getExcludedСategories($db);
    $categories = deleteExcludedCategories($categories, $excludedCategories);
    $brokenProducts = getBrokenProducts($db, $categories);
    $brokenProducts = getProductsImages($brokenProducts);
    
    file_put_contents(PRODUCT_IMAGE_JSON, json_encode($brokenProducts));
    echo "Choose 'Print product'.";
}

?>
<hr>
<a href="?getProducts=yes">Get broken products</a><?= (file_exists(PRODUCT_IMAGE_JSON)) ? ' (already exists)' : ''; ?><br>
<a href="?getImages=yes">Get images</a><br>
<a href="?printProducts=yes">Print products</a>