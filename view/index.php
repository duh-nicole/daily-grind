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
$file = 'products.json';
$products = [];
if (file_exists($file) && filesize($file) > 0) {
    $json_string = file_get_contents($file);
    $products = json_decode($json_string, true);
    if ($products === null) {
        $products = []; // In case the file is corrupted
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
        if (str_contains(strtolower($product['name']), $search_term) || str_contains(strtolower($product['code']), $search_term) || str_contains(strtolower($product['description']), $search_term)) {
            $search_results[] = $product;
        }
    }
}

// Process the action
switch ($action) {
    case 'add_products':
        // Get the new products from the form
        $new_products_data = [
            'product_name' => $_POST['product_name'] ?? [],
            'product_code' => $_POST['product_code'] ?? [],
            'price' => $_POST['price'] ?? [],
            'description' => $_POST['description'] ?? []
        ];

        foreach ($new_products_data['product_name'] as $index => $name) {
            $name = trim(htmlspecialchars($name));
            if (!empty($name)) {
                $code = trim(htmlspecialchars($new_products_data['product_code'][$index]));
                $price = str_replace('$', '', trim(htmlspecialchars($new_products_data['price'][$index])));
                $description = trim(htmlspecialchars($new_products_data['description'][$index]));

                $products[] = [
                    'name' => $name,
                    'code' => $code,
                    'price' => $price,
                    'description' => $description
                ];
            }
        }
        $json_data = json_encode($products, JSON_PRETTY_PRINT);
        file_put_contents($file, $json_data);
        $message = "Products added successfully.";
        break;

    case 'bulk_delete':
        $indices_to_delete = filter_input(INPUT_POST, 'delete_indices', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        if (!empty($indices_to_delete)) {
            // Sort indices in descending order to prevent re-indexing issues
            rsort($indices_to_delete);
            foreach ($indices_to_delete as $index) {
                if (isset($products[$index])) {
                    unset($products[$index]);
                }
            }
            $products = array_values($products); // Re-index array
            $json_data = json_encode($products, JSON_PRETTY_PRINT);
            file_put_contents($file, $json_data);
            $message = "Products deleted successfully.";
        }
        break;

    case 'show_edit_form':
        $index = filter_input(INPUT_GET, 'index', FILTER_VALIDATE_INT);
        if ($index !== false && isset($products[$index])) {
            $edit_product_data = $products[$index];
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

            $products[$index] = [
                'name' => $name,
                'code' => $code,
                'price' => $price,
                'description' => $description
            ];

            $json_data = json_encode($products, JSON_PRETTY_PRINT);
            file_put_contents($file, $json_data);
            $message = "Product updated successfully.";
        } else {
            $message = "Error: Product not found for updating.";
        }
        $search_results = $products; // Reset search results
        break;

    case 'sort_products':
        $sort_by = filter_input(INPUT_GET, 'sort_by');
        if ($sort_by === 'price') {
            usort($search_results, function($a, $b) {
                return $a['price'] <=> $b['price'];
            });
        } elseif ($sort_by === 'name') {
            usort($search_results, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }
        break;

    default:
        $search_results = $products;
        break;
}

include 'view/home.php';
