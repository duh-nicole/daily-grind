<?php
// home.php

include 'view/header.php';
?>

<h1>Inventory Management</h1>

<form action="index.php" method="get" class="search-form">
    <input type="hidden" name="action" value="search_products">
    <input type="text" name="search_term" placeholder="Search products...">
    <button type="submit">Search</button>
</form>

<?php if (isset($edit_index)) : ?>
    <section>
        <h2>Edit Product</h2>
        <form action="index.php" method="post" class="add-products-form">
            <input type="hidden" name="action" value="update_product">
            <input type="hidden" name="index" value="<?php echo htmlspecialchars($edit_index); ?>">

            <div class="product-entry-box">
                <div class="form-group">
                    <label>Product Name:</label>
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($edit_product_data[0]); ?>">
                </div>
                <div class="form-group">
                    <label>Product Code:</label>
                    <input type="text" name="product_code" value="<?php echo htmlspecialchars($edit_product_data[1]); ?>">
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="text" name="price" value="<?php echo htmlspecialchars($edit_product_data[2]); ?>">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description"><?php echo htmlspecialchars($edit_product_data[3]); ?></textarea>
                </div>
            </div>

            <button type="submit" class="coffee-button">
                <span class="icon">💾</span> Update Product
            </button>
            <a href="index.php?action=show_add_form" class="coffee-button" style="background-color: #555;">Cancel</a>
        </form>
    </section>
<?php endif; ?>

<section>
    <h2>Add New Products</h2>
    <form action="index.php" method="post" class="add-products-form">
        <input type="hidden" name="action" value="add_products">

        <?php for ($i = 0; $i < 5; $i++) : ?>
        <div class="product-entry-box">
            <h3>Product #<?php echo $i + 1; ?></h3>
            <div class="form-group">
                <label>Product Name:</label>
                <input type="text" name="product_name[]">
            </div>
            <div class="form-group">
                <label>Product Code:</label>
                <input type="text" name="product_code[]">
            </div>
            <div class="form-group">
                <label>Price:</label>
                <input type="text" name="price[]">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description[]"></textarea>
            </div>
        </div>
        <?php endfor; ?>

        <button type="submit" class="coffee-button">
            <span class="icon">☕️</span> Add Products
        </button>
    </form>
</section>

<section class="inventory-list-container">
    <h2>Current Inventory</h2>
    <?php if (!empty($search_results)) : ?>
        <form action="index.php" method="post" class="inventory-form">
            <input type="hidden" name="action" value="bulk_delete">
            <?php foreach ($search_results as $index => $product) :
                // We must use the original products array for the correct index
                $original_index = array_search($product, $products);
                list($name, $code, $price, $description) = explode('|', $product);
                if (!empty($name)) : ?>
                    <div class='product-item'>
                        <div class="grid-item checkbox-column">
                            <input type="checkbox" name="delete_indices[]" value="<?php echo htmlspecialchars($original_index); ?>" class="delete-checkbox">
                        </div>
                        <div class="grid-item product-info-column">
                            <h3 class="product-name"><?php echo htmlspecialchars($name); ?></h3>
                            <p><strong>Code:</strong> <?php echo htmlspecialchars($code); ?></p>
                            <p><strong>Price:</strong> $<?php echo htmlspecialchars($price); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($description); ?></p>
                        </div>
                        <div class="grid-item button-column">
                            <button class='coffee-button edit-button' data-index='<?php echo htmlspecialchars($original_index); ?>' style='background-color:#4a90e2;'>Edit</button>
                        </div>
                    </div>
                <?php endif;
            endforeach; ?>
            <button type="submit" class="coffee-button" style="background-color:#e24a4a; margin-top: 20px;">
                Delete Selected Products
            </button>
        </form>
    <?php else : ?>
        <p>No products in inventory yet.</p>
    <?php endif; ?>
</section>

<div id="status-notification" class="status-notification"></div>

<script>
    window.onload = function() {
        const message = "<?php echo isset($message) ? htmlspecialchars($message) : ''; ?>";
        const notification = document.getElementById('status-notification');
        if (message.length > 0) {
            notification.textContent = message;
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
    };
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', (event) => {
            const index = event.target.dataset.index;
            window.location.href = `index.php?action=show_edit_form&index=${index}`;
        });
    });
</script>

</main>

<?php
// This will insert the content of footer.php, which contains the closing body and HTML tags.
include 'view/footer.php';
?>
