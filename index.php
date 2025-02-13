<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$servername = "localhost";
$username = "phpmyadmin";
$password = "root";
$dbname = "scandiweb_store";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON file
$jsonData = file_get_contents("data.json");
$data = json_decode($jsonData, true);

if (!$data) {
    die("Invalid JSON format");
}

// Insert categories
foreach ($data['data']['categories'] as $category) {
    $name = $conn->real_escape_string($category['name']);
    $conn->query("INSERT INTO categories (name) VALUES ('$name') ON DUPLICATE KEY UPDATE name=name");
}

// Insert currencies
$currencies = [];
foreach ($data['data']['products'] as $product) {
    foreach ($product['prices'] as $price) {
        $label = $conn->real_escape_string($price['currency']['label']);
        $symbol = $conn->real_escape_string($price['currency']['symbol']);
        
        if (!isset($currencies[$label])) {
            $conn->query("INSERT INTO currencies (label, symbol) VALUES ('$label', '$symbol') ON DUPLICATE KEY UPDATE label=label");
            $currencies[$label] = $conn->insert_id;
        }
    }
}

// Insert attributes without duplication
$attributeIds = [];
foreach ($data['data']['products'] as $product) {
    foreach ($product['attributes'] as $attribute) {
        $attributeName = $conn->real_escape_string($attribute['name']);
        $attributeType = $conn->real_escape_string($attribute['type']);

        // Check if attribute already exists
        $result = $conn->query("SELECT id FROM attributes WHERE name='$attributeName'");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $attributeId = $row['id'];
        } else {
            // Insert new attribute
            $conn->query("INSERT INTO attributes (name, type) VALUES ('$attributeName', '$attributeType')");
            $attributeId = $conn->insert_id;
        }

        $attributeIds[$attributeName] = $attributeId;
    }
}

// Insert products
foreach ($data['data']['products'] as $product) {
    $id = $conn->real_escape_string($product['id']);
    $name = $conn->real_escape_string($product['name']);
    $in_stock = $product['inStock'] ? 1 : 0;
    $description = $conn->real_escape_string($product['description']);
    $category = $conn->real_escape_string($product['category']);
    $brand = $conn->real_escape_string($product['brand']);
    
    // Get category ID
    $categoryId = $conn->query("SELECT id FROM categories WHERE name='$category'")->fetch_assoc()['id'];
    
    $conn->query("INSERT INTO products (id, name, in_stock, description, category_id, brand) 
                  VALUES ('$id', '$name', $in_stock, '$description', $categoryId, '$brand')");

    // Insert gallery images
    $position = 1;
    foreach ($product['gallery'] as $image_url) {
        $image_url = $conn->real_escape_string($image_url);
        $conn->query("INSERT INTO galleries (product_id, image_url, position) 
                      VALUES ('$id', '$image_url', $position)");
        $position++;
    }
    
    // Insert attribute items
    foreach ($product['attributes'] as $attribute) {
        $attributeName = $conn->real_escape_string($attribute['name']);
        $attributeId = $attributeIds[$attributeName];
        
        foreach ($attribute['items'] as $item) {
            $displayValue = $conn->real_escape_string($item['displayValue']);
            $value = $conn->real_escape_string($item['value']);

            // Avoid duplicate attribute items
            $conn->query("INSERT INTO attribute_items (attribute_id, product_id, display_value, value) 
                          VALUES ($attributeId, '$id', '$displayValue', '$value')
                          ON DUPLICATE KEY UPDATE display_value=display_value");
        }
    }
    
    // Insert prices
    foreach ($product['prices'] as $price) {
        $amount = $price['amount'];
        $currencyLabel = $conn->real_escape_string($price['currency']['label']);
        
        $currencyId = $conn->query("SELECT id FROM currencies WHERE label='$currencyLabel'")->fetch_assoc()['id'];
        $conn->query("INSERT INTO prices (product_id, amount, currency_id) 
                      VALUES ('$id', $amount, $currencyId)");
    }
}

$conn->close();

echo "Database populated successfully.";
?>

