<?php
require_once('database.php');
require_once('category.php');

/**
 * ☕️ CategoryDB - Pouring data for our Category model.
 *
 * This class handles all data access for categories.
 */
class CategoryDB {
    /**
     * Retrieves all categories from the database.
     *
     * @return array An array of Category objects.
     */
    public static function getCategories(): array {
        $db = Database::getDB();
        $query = 'SELECT categoryID, categoryName
                  FROM categories
                  ORDER BY categoryID';
        try {
            $statement = $db->prepare($query);
            $statement->execute();
            $rows = $statement->fetchAll();
            $statement->closeCursor();

            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new Category($row['categoryID'], $row['categoryName']);
            }
            return $categories;
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }

    /**
     * Retrieves a single category by its ID.
     *
     * @param int $category_id The ID of the category.
     * @return Category The instantiated Category object.
     */
    public static function getCategory(int $category_id): Category {
        $db = Database::getDB();
        $query = 'SELECT categoryID, categoryName
                  FROM categories
                  WHERE categoryID = :category_id';
        try {
            $statement = $db->prepare($query);
            $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $statement->closeCursor();

            return new Category($row['categoryID'], $row['categoryName']);
        } catch (PDOException $e) {
            Database::displayError($e->getMessage());
        }
    }
}
?>

