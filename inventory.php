<?php
include('connect.php');

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete(['_id' => new MongoDB\BSON\ObjectId($_GET['delete_id'])]);
    executeBulkWrite('medicines', $bulk);
    echo "<script>alert('Medicine Deleted Successfully');</script>";
    header("Location: inventory.php");
    exit;
}

// Move expired medicines to expiredMedicines collection and delete from medicines collection
$current_date = new DateTime();
$expired_filter = ['Expiry_Date' => ['$lt' => $current_date->format('Y-m-d')]];
$expired_medicines = executeQuery('medicines', $expired_filter);

if (!empty($expired_medicines)) {
    $bulk_expired = new MongoDB\Driver\BulkWrite;
    foreach ($expired_medicines as $medicine) {
        // Insert into expiredMedicines collection
        $bulk_expired->insert($medicine);
        
        // Delete from medicines collection
        $bulk_delete = new MongoDB\Driver\BulkWrite;
        $bulk_delete->delete(['_id' => $medicine->_id]);
        executeBulkWrite('medicines', $bulk_delete);
    }
    executeBulkWrite('expiredMedicines', $bulk_expired);
}

// Move low stock medicines to lowStock collection
$low_stock_filter = ['Quantity' => ['$lt' => 5]];
$low_stock_medicines = executeQuery('medicines', $low_stock_filter);

// Insert low stock medicines into lowStock collection if they don't already exist
if (!empty($low_stock_medicines)) {
    foreach ($low_stock_medicines as $medicine) {
        // Check if the medicine already exists in lowStock collection
        $existing_medicine = executeQuery('lowStock', ['_id' => $medicine->_id]);
        
        if (empty($existing_medicine)) {
            // Insert into lowStock collection only if it doesn't exist
            $bulk_low_stock = new MongoDB\Driver\BulkWrite;
            $bulk_low_stock->insert($medicine);
            executeBulkWrite('lowStock', $bulk_low_stock);
        }
    }
}

// Check for low stock medicines and remove them from lowStock if their quantity is now >= 5
$all_low_stock_items = executeQuery('lowStock', []);
foreach ($all_low_stock_items as $low_stock_item) {
    // Check current quantity in medicines collection
    $current_quantity = executeQuery('medicines', ['_id' => $low_stock_item->_id])[0]->Quantity ?? 0;

    if ($current_quantity >= 5) {
        // Remove from lowStock collection if quantity is now sufficient
        $bulk_remove_low_stock = new MongoDB\Driver\BulkWrite;
        $bulk_remove_low_stock->delete(['_id' => $low_stock_item->_id]);
        executeBulkWrite('lowStock', $bulk_remove_low_stock);
    } elseif ($current_quantity <= 0) {
        // If quantity is zero, remove from lowStock as well
        $bulk_remove_low_stock_zero = new MongoDB\Driver\BulkWrite;
        $bulk_remove_low_stock_zero->delete(['_id' => $low_stock_item->_id]);
        executeBulkWrite('lowStock', $bulk_remove_low_stock_zero);
    }
}

// Fetch medicines based on search
$filter = [];
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $filter = ['Name' => new MongoDB\BSON\Regex($search_term, 'i')];
}
$medicines = executeQuery('medicines', $filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Inventory</h1>
        <a href="index.php" class="button back-home">Back to Homepage</a>
    </header>
    
    <main class="container">
        <div class="search-section">
            <h2>Available Medicines</h2>
            <form method="get" class="search-form">
                <input type="text" name="search" placeholder="Search Medicine" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="button">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Manufacturer</th>
                    <th>Batch No</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>MRP</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicines as $medicine): ?>
                    <tr>
                        <td><?= htmlspecialchars($medicine->Name) ?></td>
                        <td><?= htmlspecialchars($medicine->Type) ?></td>
                        <td><?= htmlspecialchars($medicine->Manufacturer) ?></td>
                        <td><?= htmlspecialchars($medicine->Batch_No) ?></td>
                        <td><?= htmlspecialchars($medicine->Expiry_Date) ?></td>
                        <td><?= htmlspecialchars($medicine->Quantity) ?></td>
                        <td><?= htmlspecialchars($medicine->MRP) ?></td>
                        <td class="action-cell">
                            <a href="edit_medicine.php?id=<?= htmlspecialchars($medicine->_id) ?>" class="button edit">Edit</a>
                            <a href="inventory.php?delete_id=<?= htmlspecialchars($medicine->_id) ?>" class="button delete" 
                               onclick="return confirm('Are you sure you want to delete this medicine?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add New Medicine Button -->
        <div class="button-group" style="text-align: center;">
            <a href="add_medicine.php" class="button">Add New Medicine</a> 
        </div>

    </main>

    <!-- Expired and Low Stock Buttons Outside Main -->
    <div class="button-group" style="text-align: center; margin-top: 20px;">
        <a href="expired_medicines.php" class="button">Expired Medicines</a> 
        <a href="low_stock.php" class="button">Low Stock Medicines</a> 
    </div>

</body></html>

