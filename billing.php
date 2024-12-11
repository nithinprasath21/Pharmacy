<?php
include('connect.php');

// Start session
session_start();

// Initialize an empty cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Retrieve medicines from the database based on search term
$filter = [];
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $filter = ['Name' => new MongoDB\BSON\Regex($search_term, 'i')]; // Case-insensitive regex search
}
$medicines = executeQuery('medicines', $filter); // Fetch medicines based on search

// Handle form submission for going to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['go_to_cart'])) {
    
    foreach ($_POST['medicine_names'] as $index => $medicine_name) {
        $quantity = $_POST['quantities'][$index] ?? 0; // Use null coalescing operator
        
        if (!empty($quantity) && $quantity > 0) {
            // Check if this medicine is already in the cart based on its name.
            foreach ($_SESSION['cart'] as &$cart_item) {
                if ($cart_item['name'] === $medicine_name) {
                    // If found, increase quantity.
                    $cart_item['quantity'] += (int)$quantity;
                    break 2; // Break out of both loops.
                }
            }
            
            // If not found, add new item.
            $_SESSION['cart'][] = [
                'name' => $medicine_name,
                'quantity' => (int)$quantity,
                'price' => $_POST['prices'][$index] ?? 0 // Use null coalescing operator.
            ];
        }
    }

    // Redirect to cart page if there are items in the cart.
    if (!empty($_SESSION['cart'])) {
        header("Location: cart.php");
        exit;
    } else {
        echo "<script>alert('Please enter a valid quantity for at least one medicine.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Billing</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
        <h1 style="flex-grow: 1; text-align: center;">Customer Billing</h1>
        <a href="index.php" class="button back-home">Back to Homepage</a> <!-- Back to Homepage Button -->
    </header>

    <main class="container">
        <!-- Available Medicines Section -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin-top: 20px;">Available Medicines</h2>
            <form method="get" class="search-form" style="display: flex; align-items: center;">
                <input type="text" name="search" placeholder="Search Medicine" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="margin-left: auto; height: 38px;">
                <button type="submit" class="button" style="height: 38px;">Search</button> <!-- Adjust button height here -->
            </form>
        </div>

        <!-- Medicines Table -->
        <form action="billing.php" method="post">
            <table border='1' style='width: 100%; border-collapse: collapse;'>
                <thead>
                    <tr style='background-color: #4CAF50; color: white;'>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Manufacturer</th>
                        <th>Batch No</th>
                        <th>Expiry Date</th>
                        <th>Quantity Available</th>
                        <th>MRP</th>
                        <th>Quantity to Add</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicines)): ?>
                        <tr><td colspan="8">No medicines found.</td></tr> <!-- Message when no medicines are found -->
                    <?php else: ?>
                        <?php foreach ($medicines as $medicine): ?>
                            <tr>
                                <td><?= htmlspecialchars($medicine->Name) ?></td>
                                <td><?= htmlspecialchars($medicine->Type) ?></td>
                                <td><?= htmlspecialchars($medicine->Manufacturer) ?></td>
                                <td><?= htmlspecialchars($medicine->Batch_No) ?></td>
                                <td><?= htmlspecialchars($medicine->Expiry_Date) ?></td>
                                <td><?= htmlspecialchars($medicine->Quantity) ?></td>
                                <td><?= htmlspecialchars($medicine->MRP) ?></td>

                                <!-- Hidden inputs for medicine details -->
                                <input type="hidden" name="medicine_names[]" value="<?= htmlspecialchars($medicine->Name) ?>">
                                <input type="hidden" name="prices[]" value="<?= htmlspecialchars($medicine->MRP) ?>">

                                <!-- Input for quantity with full width and height -->
                                <td><input type="number" name="quantities[]" min="0" max="<?= htmlspecialchars($medicine->Quantity) ?>" placeholder="Qty" style="width: 100%; height: 100%;"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Go to Cart Button -->
            <div style='text-align: center; margin-top: 20px;'>
                <button type="submit" name="go_to_cart" class="button">Go to Cart</button>
            </div>
        </form>

    </main>

</body>
</html>

