<?php
// index.php

// Define a base path for all your files.
$base_path = '/';

// Get the action from the POST or GET request
$action = filter_input(INPUT_POST, 'action');
if ($action === NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action === NULL) {
        $action = 'show_add_form'; // Default action
    }
}

// Get the product data from the file
$file = 'products.txt';
$products = [];
if (file_exists($file) && filesize($file) > 0) {
    $products_raw = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($products_raw as $product_line) {
        $products[] = $product_line;
    }
}

$message = '';
$edit_index = null;
$edit_product_data = [];
$search_results = $products; // Initialize search results with all products

// --- NEW CODE: Check for a search query
if ($action === 'search_products') {
    $search_term = strtolower(filter_input(INPUT_GET, 'search_term', FILTER_SANITIZE_STRING));
    $search_results = [];
    foreach ($products as $product) {
        // Search through all product fields
        if (str_contains(strtolower($product), $search_term)) {
            $search_results[] = $product;
        }
    }
    if (empty($search_results)) {
        $message = "No products found for '{$search_term}'.";
    }
}

// Check for a specific action
switch ($action) {
    case 'show_add_form':
        break;

    case 'add_products':
        $product_names = filter_input(INPUT_POST, 'product_name', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $product_codes = filter_input(INPUT_POST, 'product_code', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $prices = filter_input(INPUT_POST, 'price', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $descriptions = filter_input(INPUT_POST, 'description', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        for ($i = 0; $i < count($product_names); $i++) {
            $name = trim($product_names[$i]);
            if (!empty($name)) { // Only add if the name is not empty
                $code = trim($product_codes[$i]);
                $price = str_replace('$', '', trim($prices[$i]));
                $description = trim($descriptions[$i]);
                $data = "$name|$code|$price|$description\n";
                file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
            }
        }
        $message = 'Products have been added!';
        $products = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $search_results = $products; // Reset search results
        break;
    
    case 'bulk_delete':
        $indices_to_delete = filter_input(INPUT_POST, 'delete_indices', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if ($indices_to_delete) {
            rsort($indices_to_delete);
            foreach ($indices_to_delete as $index) {
                if (isset($products[$index])) {
                    unset($products[$index]);
                }
            }
            $products = array_values($products);
            file_put_contents($file, implode("\n", $products));
            $message = count($indices_to_delete) . " products deleted successfully.";
        } else {
            $message = "No products selected for deletion.";
        }
        $search_results = $products; // Reset search results
        break;
        
    case 'show_edit_form':
        $index = filter_input(INPUT_GET, 'index', FILTER_VALIDATE_INT);
        if ($index !== false && isset($products[$index])) {
            $edit_product_data = explode('|', $products[$index]);
            $edit_index = $index;
        } else {
            $message = "Error: Product not found for editing.";
        }
        break;
        
    case 'update_product':
        $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
        if ($index !== false && isset($products[$index])) {
            $name = trim(htmlspecialchars(filter_input(INPUT_POST, 'product_name')));
            $code = trim(htmlspecialchars(filter_input(INPUT_POST, 'product_code')));
            $price = str_replace('$', '', trim(htmlspecialchars(filter_input(INPUT_POST, 'price'))));
            $description = trim(htmlspecialchars(filter_input(INPUT_POST, 'description')));
            
            $products[$index] = "$name|$code|$price|$description";
            
            file_put_contents($file, implode("\n", $products));
            $message = "Product updated successfully.";
        } else {
            $message = "Error: Product not found for updating.";
        }
        $search_results = $products; // Reset search results
        break;
        
    case 'search_products':
        // The search logic is handled before the switch statement
        break;
        
    default:
        $message = "Unknown action.";
        break;
}

include 'home.php';

