<?php
/**
 * ☕️ Product - A fresh brew of product data.
 *
 * This class represents a single product with all its details,
 * ensuring consistency and data integrity.
 */
class Product {
    private readonly int $id;

    public function __construct(
        private Category $category,
        private string $code,
        private string $name,
        private string $description,
        private float $price,
        private float $discount_percent,
        ?int $id = null
    ) {
		if ($id !== null){
	        $this->id = $id;
    	}
	}

    /**
     * Retrieves the category associated with the product.
     * @return Category
     */
    public function getCategory(): Category {
        return $this->category;
    }

    /**
     * Sets the category for the product.
     * @param Category $value
     */
    public function setCategory(Category $value): void {
        $this->category = $value;
    }

    /**
     * Retrieves the product's unique ID.
     * @return int
     */
    public function getID(): int {
        return $this->id;
    }

    /**
     * Retrieves the product's code.
     * @return string
     */
 
    public function getCode(): string { 
        return $this->code; 
    }


    /**
     * Retrieves the product's name.
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Sets the product's name.
     * @param string $value
     */
    public function setName(string $value): void {
        $this->name = $value;
    }

    /**
     * Retrieves the product's description.
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Sets the product's description.
     * @param string $value
     */
    public function setDescription(string $value): void {
        $this->description = $value;
    }

    /**
     * Retrieves the product's price.
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * Retrieves the product's price formatted as currency.
     * @return string
     */
    public function getPriceFormatted(): string {
        return number_format($this->price, 2);
    }

    /**
     * Sets the product's price.
     * @param float $value
     */
    public function setPrice(float $value): void {
        $this->price = $value;
    }

    /**
     * Retrieves the product's discount percentage.
     * @return float
     */
    public function getDiscountPercent(): float {
        return $this->discount_percent;
    }

    /**
     * Retrieves the product's discount percentage formatted without decimals.
     * @return string
     */
    public function getDiscountPercentFormatted(): string {
        return number_format($this->discount_percent, 0);
    }

    /**
     * Sets the product's discount percentage.
     * @param float $value
     */
    public function setDiscountPercent(float $value): void {
        $this->discount_percent = $value;
    }

    /**
     * Calculates the monetary amount of the discount.
     * @return float
     */
    public function getDiscountAmount(): float {
        $discount_percent = $this->getDiscountPercent() / 100;
        return $this->price * $discount_percent;
    }

    /**
     * Retrieves the formatted monetary amount of the discount.
     * @return string
     */
    public function getDiscountAmountFormatted(): string {
        $discount_amount = $this->getDiscountAmount();
        $discount_amount_r = round($discount_amount, 2);
        return number_format($discount_amount_r, 2);
    }

    /**
     * Calculates and formats the price after the discount.
     * @return string
     */
    public function getDiscountPriceFormatted(): string {
        $discount_price = $this->price - $this->getDiscountAmount();
        return number_format($discount_price, 2);
    }

    /**
     * Gets the filename for the product's image.
     * @return string
     */
    public function getImageFilename(): string {
        return $this->code . '_m.png';
    }

    /**
     * Gets the full image path for the product.
     * @param string $app_path
     * @return string
     */
    public function getImagePath(string $app_path): string {
        return $app_path . 'images/' . $this->getImageFilename();
    }

    /**
     * Gets a descriptive alt text for the product image.
     * ♿️ An essential for accessibility.
     * @return string
     */
    public function getImageAltText(): string {
        return 'Image of ' . $this->getName();
    }
}
