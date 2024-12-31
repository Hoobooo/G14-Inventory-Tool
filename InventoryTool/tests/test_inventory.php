<?php

class Product {
    public $id;
    public $name;
    public $quantity;

    public function __construct($id, $name, $quantity) {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
    }
}

class InventoryManager {
    private $products = [];

    public function addProduct(Product $product) {
        $this->products[$product->id] = $product;
    }

    public function updateQuantity($id, $quantity) {
        if (isset($this->products[$id])) {
            $this->products[$id]->quantity = $quantity;
            return true;
        }
        return false;
    }

    public function getProduct($id) {
        return $this->products[$id] ?? null;
    }

    public function getLowStockProducts($threshold = 10) {
        return array_filter($this->products, function($product) use ($threshold) {
            return $product->quantity < $threshold;
        });
    }
}

// Test Cases
class InventoryTest {
    private $inventory;

    public function __construct() {
        $this->inventory = new InventoryManager();
    }

    public function runTests() {
        $this->testAddProduct();
        $this->testUpdateQuantity();
        $this->testLowStock();
        echo "All tests completed!\n";
    }

    private function testAddProduct() {
        $product = new Product(1, "Test Item", 20);
        $this->inventory->addProduct($product);
        
        $result = $this->inventory->getProduct(1);
        assert($result->name === "Test Item", "Product add failed");
        echo "Add product test passed\n";
    }

    private function testUpdateQuantity() {
        $this->inventory->updateQuantity(1, 15);
        $result = $this->inventory->getProduct(1);
        assert($result->quantity === 15, "Quantity update failed");
        echo "Update quantity test passed\n";
    }

    private function testLowStock() {
        $product2 = new Product(2, "Low Stock Item", 5);
        $this->inventory->addProduct($product2);
        
        $lowStock = $this->inventory->getLowStockProducts();
        assert(count($lowStock) === 1, "Low stock detection failed");
        echo "Low stock test passed\n";
    }
}

// Run tests
$test = new InventoryTest();
$test->runTests();