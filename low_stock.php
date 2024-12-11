<?php
session_start();
include('connect.php');

// Fetch low stock medicines from the database
$low_stock_medicines = executeQuery('lowStock', []); // Adjust this query as necessary

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Medicines</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<h1>Low Stock Medicines</h1>

<!-- Low Stock Medicines Table -->
<table border='1'>
<thead>
<tr style='background-color: #4CAF50; color: white;'>
<th>Name</th><th>Type</th><th>Manufacturer</th><th>Batch No</th><th>Expiry Date</th><th>Quantity</th><th>MRP</th></tr></thead><tbody><?php 
foreach ($low_stock_medicines as $medicine): 
?>
<?php 
echo "<tr><td>" . htmlspecialchars($medicine->Name) . "</td><td>" . htmlspecialchars($medicine->Type) . "</td><td>" . htmlspecialchars($medicine->Manufacturer) . "</td><td>" . htmlspecialchars($medicine->Batch_No) . "</td><td>" . htmlspecialchars($medicine->Expiry_Date) . "</td><td>" . htmlspecialchars($medicine->Quantity) . "</td><td>" . htmlspecialchars($medicine->MRP) . "</td></tr>"; 
endforeach; 
?>
</tbody></table>

<!-- Back Button -->
<a href='inventory.php' class='button'>Back to Inventory</a>

</div></body></html>

