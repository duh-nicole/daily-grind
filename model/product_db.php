<?php
require_once('database.php');
require_once('category_db.php');

/**
 * ☕️ ProductDB - A brewing ground for your database interactions.
 *
 * This class handles all data access for products, providing a clean,
 * secure layer for interacting with the database.
 */
class ProductDB {
    /**
     * Retrieves a list of products by category ID.
     *
     * @param int $category_id The ID of the category.
     * @return array An array of Product objects.
     */
    public static function getProductsByCategory(int $category_id): array {
        $db = Database::getDB();
        $category = CategoryDB::getCategory($category_id);
        $query = 'SELECT categoryID, productID, productCode, productName,
                         description, listPrice, discountPercent
                  FROM products
                  WHERE categoryID = :category_id
                  ORDER BY productID';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            $statement->execute();

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();

            $products = [];
            foreach ($rows as $row) {
                $products[] = self::loadProduct($row, $category);
            }
            return $products;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    /**
     * A private method to hydrate a row into a Product object.
     *
     * @param array $row The database row.
     * @param object $category The Category object.
     * @return Product The instantiated Product object.
     */
    private static function loadProduct(array $row, object $category): Product {
        $product = new Product(
            $category,
            $row['productCode'],
            $row['productName'],
            $row['description'],
            $row['listPrice'],
            $row['discountPercent'],
            (int)$row['productID']
        );
        return $product;
    }

    /**
     * Retrieves a single product by its ID.
     *
     * @param int $product_id The ID of the product.
     * @return Product The instantiated Product object.
     */
    public static function getProduct(int $product_id): Product {
        $db = Database::getDB();
        $query = 'SELECT categoryID, productID, productCode, productName,
                         description, listPrice, discountPercent
                  FROM products
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $statement->closeCursor();

            $category = CategoryDB::getCategory($row['categoryID']);
            return self::loadProduct($row, $category);
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    /**
     * Adds a new product to the database.
     *
     * @param Product $product The Product object to add.
     * @return int The ID of the newly inserted product.
     */
    public static function addProduct(Product $product): int {
        $db = Database::getDB();
        $query = 'INSERT INTO products
                     (categoryID, productCode, productName, description,
                      listPrice, discountPercent, dateAdded)
                  VALUES
                     (:category_id, :code, :name, :description, :price,
                      :discount_percent, NOW())';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $product->getCategory()->getID(), PDO::PARAM_INT);
            $statement->bindValue(':code', $product->getCode());
            $statement->bindValue(':name', $product->getName());
            $statement->bindValue(':description', $product->getDescription());
            $statement->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
            $statement->bindValue(':discount_percent', $product->getDiscountPercent(), PDO::PARAM_STR);
            $statement->execute();
            $statement->closeCursor();

            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    /**
     * Updates an existing product in the database.
     *
     * @param Product $product The Product object to update.
     * @return int The number of rows updated.
     */
    public static function updateProduct(Product $product): int {
        $db = Database::getDB();
        $query = 'UPDATE products
                  SET productName = :name, productCode = :code,
                      description = :description, listPrice = :price,
                      discountPercent = :discount_percent,
                      categoryID = :category_id
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $product->getCategory()->getID(), PDO::PARAM_INT);
            $statement->bindValue(':code', $product->getCode());
            $statement->bindValue(':name', $product->getName());
            $statement->bindValue(':description', $product->getDescription());
            $statement->bindValue(':price', $product->getPrice(), PDO::PARAM_STR);
            $statement->bindValue(':discount_percent', $product->getDiscountPercent(), PDO::PARAM_STR);
            $statement->bindValue(':product_id', $product->getID(), PDO::PARAM_INT);
            $statement->execute();

            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    /**
     * Deletes a product from the database by ID.
     *
     * @param int $product_id The ID of the product to delete.
     * @return int The number of rows deleted.
     */
    public static function deleteProduct(int $product_id): int {
        $db = Database::getDB();
        $query = 'DELETE FROM products
                  WHERE productID = :product_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $statement->execute();

            $row_count = $statement->rowCount();
            $statement->closeCursor();
            return $row_count;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
}
?>
