<?php
include('connect.php');

// Fetch the existing medicine details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch medicine by ID
    $filter = ['_id' => new MongoDB\BSON\ObjectId($id)];
    
    // Ensure we get a single medicine object
    $medicine = current(iterator_to_array(executeQuery('medicines', $filter)));
}

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
   // Validate input before updating
   if (!empty($_POST['medicine_name']) && !empty($_POST['quantity']) && !empty($_POST['mrp'])) {
       $bulk = new MongoDB\Driver\BulkWrite;

       // Prepare updated data
       $updatedData = [
           'Name' => $_POST['medicine_name'],
           'Type' => $_POST['type'],
           'Manufacturer' => $_POST['manufacturer'],
           'Batch_No' => $_POST['batch_no'],
           'Expiry_Date' => $_POST['expiry_date'],
           'Quantity' => (int)$_POST['quantity'],
           'MRP' => (float)$_POST['mrp']
       ];

       // Update medicine in the database
       $bulk->update(['_id' => new MongoDB\BSON\ObjectId($_POST['id'])], ['$set' => $updatedData]);
       executeBulkWrite('medicines', $bulk);

       echo "<script>alert('Medicine Updated Successfully');</script>";
       header("Location: inventory.php");
       exit;
   } else {
       echo "<script>alert('Please fill all required fields.');</script>";
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medicine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container" style="max-height: 80vh; padding: 30px;">
        <h2 class="form-heading">Edit Medicine</h2>
        <form action="edit_medicine.php" method="post">
            <input type="hidden" name="id" value="<?= $medicine->_id ?>">

            <div class="input-row">
                <label for="medicine_name">Medicine Name:</label>
                <input type="text" id="medicine_name" name="medicine_name" value="<?= $medicine->Name ?>" required>

                <label for="type">Type:</label>
                <select id="type" name="type">
                    <option value="Tablet" <?= $medicine->Type === 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                    <option value="Syrup" <?= $medicine->Type === 'Syrup' ? 'selected' : '' ?>>Syrup</option>
                    <option value="Capsule" <?= $medicine->Type === 'Capsule' ? 'selected' : '' ?>>Capsule</option>
                    <option value="Injection" <?= $medicine->Type === 'Injection' ? 'selected' : '' ?>>Injection</option>
                </select>
            </div>

            <div class="input-row">
                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer" name="manufacturer" value="<?= $medicine->Manufacturer ?>" required>

                <label for="batch_no">Batch No:</label>
                <input type="text" id="batch_no" name="batch_no" value="<?= $medicine->Batch_No ?>">
            </div>

            <div class="input-row">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" id="expiry_date" name="expiry_date" value="<?= $medicine->Expiry_Date ?>">

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?= $medicine->Quantity ?>" required>
            </div>

            <div class="input-row">
                <label for="mrp">MRP:</label>
                <input type="number" id="mrp" name="mrp" value="<?= $medicine->MRP ?>" step="0.01" required>
            </div>

            <div class="button-group" style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button type="submit" class="button">Update Medicine</button>
                <a href="inventory.php" class="button">Back to Inventory</a>
            </div>
        </form>
    </div>
</body>
</html>
