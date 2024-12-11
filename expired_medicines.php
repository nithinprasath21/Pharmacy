<?php
session_start();
include('connect.php');

// Fetch expired medicines from the database
$expired_medicines = executeQuery('expiredMedicines', []); // Adjust this query as necessary

// Handle removal of an expired medicine
if (isset($_GET['delete_id'])) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($_GET['delete_id'])]);
    executeBulkWrite('expiredMedicines', $bulk);
    
    echo "<script>alert('Expired Medicine Removed Successfully');</script>";
    header("Location: expired_medicines.php"); // Redirect back to the same page after deletion
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Medicines</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<h1>Expired Medicines</h1>

<!-- Expired Medicines Table -->
<table border='1'>
<thead>
<tr style='background-color: #4CAF50; color: white;'>
<th>Name</th><th>Type</th><th>Manufacturer</th><th>Batch No</th><th>Expiry Date</th><th>Quantity</th><th>MRP</th><th>Actions</th></tr></thead><tbody><?php 
foreach ($expired_medicines as $medicine): 
?>
<?php 
echo "<tr><td>" . htmlspecialchars($medicine->Name) . "</td><td>" . htmlspecialchars($medicine->Type) . "</td><td>" . htmlspecialchars($medicine->Manufacturer) . "</td><td>" . htmlspecialchars($medicine->Batch_No) . "</td><td>" . htmlspecialchars($medicine->Expiry_Date) . "</td><td>" . htmlspecialchars($medicine->Quantity) . "</td><td>" . htmlspecialchars($medicine->MRP) . "</td>";
echo "<td><a href='expired_medicines.php?delete_id=".htmlspecialchars($medicine->_id)."' class='button delete' onclick=\"return confirm('Are you sure you want to delete this expired medicine?')\">Delete</a></td></tr>"; 
endforeach; 
?>
</tbody></table>

<!-- Back Button -->
<a href='inventory.php' class='button'>Back to Inventory</a>

</div></body></html>

