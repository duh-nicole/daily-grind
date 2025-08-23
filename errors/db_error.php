<?php include 'view/header.php'; ?>

<main role="main">
    <section class="error-container">
        <h1 class="error-title">Something went wrong with our brew.</h1>
        <p class="error-message">We're sorry, a database error occurred on our end.</p>

        <?php if (str_contains($error_message, 'SQLSTATE[HY000]')) : ?>
            <p class="error-detail">It looks like we're having trouble connecting to the database. We're on it!</p>
        <?php elseif ($error_message) : ?>
            <p class="error-detail">**For developers:** `<?php echo htmlspecialchars($error_message); ?>`</p>
        <?php endif; ?>

        <p class="error-action">
            <a href="/" class="button">Go back to the homepage and try again.</a>
        </p>
    </section>
</main>

<?php include 'view/footer.php'; ?>
