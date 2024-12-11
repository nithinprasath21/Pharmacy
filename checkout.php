<?php
session_start();
include('connect.php');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: billing.php");
    exit;
}

// Process the checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_SESSION['cart'] as $item) {
        // Update inventory using utility function
        updateInventory($item['name'], $item['quantity']);
    }

    // Clear the cart after checkout
    $_SESSION['cart'] = [];
    
    echo "<script>alert('Bill processed successfully');</script>";
    header("Location: print_bill.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>

        <!-- Cart Summary -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Total Price -->
        <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['quantity'] * $item['price'];
            }
        ?>
        <p>Total: <?= htmlspecialchars($total) ?></p>

        <!-- Checkout Actions -->
        <form method="POST">
            <button type="submit" class="button">Finalize Bill</button>
        </form>

        <!-- Additional Actions -->
        <a href="billing.php" class="button">Add More Medicines</a><br/>
    </div>

</body>
</html>
