<?php
session_start();
include('connect.php');

// Check if there are transaction details in session
if (!isset($_SESSION['transaction_details'])) {
    header("Location: index.php"); // Redirect if no transaction details are available
    exit;
}

// Fetch transaction details from session
$transaction_details = $_SESSION['transaction_details'];
$cart_items = $transaction_details['items'];
$customer_name = isset($transaction_details['customer_name']) ? htmlspecialchars($transaction_details['customer_name']) : "Unknown";
$customer_mobile = isset($transaction_details['customer_mobile']) ? htmlspecialchars($transaction_details['customer_mobile']) : "Unknown";

// Calculate total amount for display (if needed)
$total_amount = $transaction_details['total_amount'];

// Clear transaction details from session after fetching them (optional)
unset($_SESSION['transaction_details']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report</title>
    <link rel="stylesheet" href="style.css">
    
<script>
// Automatically trigger print dialog on load.
window.onload = function() {
   window.print();
};
</script>

</head>
<body>
<div class="container">
<h1>Bill Report</h1>

<!-- Customer Information -->
<p><strong>Customer Name:</strong> <?= $customer_name ?></p>
<p><strong>Customer Mobile:</strong> <?= $customer_mobile ?></p>

<!-- Cart Summary Table -->
<table border='1'>
<thead>
<tr style='background-color: #4CAF50; color: white;'>
<th>Name</th><th>Quantity Selected</th><th>MRP</th><th>Total Price</th></tr></thead><tbody><?php 
foreach ($cart_items as $item): 
?>
<?php 
echo "<tr><td>" . htmlspecialchars($item['name']) . "</td><td>" . htmlspecialchars($item['quantity']) . "</td><td>" . htmlspecialchars($item['price']) . "</td><td>" . htmlspecialchars($item['quantity'] * $item['price']) . "</td></tr>"; 
endforeach; 
?>
</tbody></table>

<!-- Total Price Calculation -->
Total Amount: <?= htmlspecialchars($total_amount); ?><br/><br/>

<!-- Back Button -->
<a href='index.php' class='button'>Back to Homepage</a>

</div></body></html>

