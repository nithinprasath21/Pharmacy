<?php
include('connect.php');

// Handle adding new medicines
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulk = new MongoDB\Driver\BulkWrite;

    // Validate input before inserting
    if (!empty($_POST['medicine_name']) && !empty($_POST['quantity']) && !empty($_POST['mrp'])) {
        $medicine_name = $_POST['medicine_name'];
        $quantity_to_add = (int)$_POST['quantity'];
        $mrp = (float)$_POST['mrp'];
        $type = $_POST['type'];
        $manufacturer = $_POST['manufacturer'];
        $batch_no = $_POST['batch_no'];
        $expiry_date = $_POST['expiry_date'];

        // Check if the medicine already exists with the same details
        $existing_medicine = executeQuery('medicines', [
            'Name' => $medicine_name,
            'Batch_No' => $batch_no,
            'Type' => $type,
            'Manufacturer' => $manufacturer,
            'Expiry_Date' => $expiry_date
        ]);

        if (!empty($existing_medicine)) {
            // If it exists, update the quantity
            $existing_quantity = (int)$existing_medicine[0]->Quantity; // Get existing quantity
            $new_quantity = $existing_quantity + $quantity_to_add; // Calculate new quantity

            // Prepare update operation
            $bulk->update(
                ['_id' => $existing_medicine[0]->_id], // Match by ID
                ['$set' => ['Quantity' => $new_quantity]] // Update quantity
            );
            executeBulkWrite('medicines', $bulk);
            echo "<script>alert('Medicine quantity updated successfully.');</script>";
        } else {
            // If it doesn't exist, insert new medicine
            $medicine = [
                'Name' => $medicine_name,
                'Type' => $type,
                'Manufacturer' => $manufacturer,
                'Batch_No' => $batch_no,
                'Expiry_Date' => $expiry_date,
                'Quantity' => $quantity_to_add,
                'MRP' => $mrp
            ];

            // Insert medicine into the database
            $bulk->insert($medicine);
            executeBulkWrite('medicines', $bulk);
            echo "<script>alert('Medicine added successfully.');</script>";
        }

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
    <title>Add Medicine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container" style="max-height: 80vh; padding: 30px;">
        <h2 class="form-heading">Add New Medicine</h2>
        <form action="add_medicine.php" method="post">
            <div class="input-row">
                <label for="medicine_name">Medicine Name:</label>
                <input type="text" id="medicine_name" name="medicine_name" required>

                <label for="type">Type:</label>
                <select id="type" name="type">
                    <option value="Tablet">Tablet</option>
                    <option value="Syrup">Syrup</option>
                    <option value="Capsule">Capsule</option>
                    <option value="Injection">Injection</option>
                </select>
            </div>

            <div class="input-row">
                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer" name="manufacturer" required>

                <label for="batch_no">Batch No:</label>
                <input type="text" id="batch_no" name="batch_no">
            </div>

            <div class="input-row">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" id="expiry_date" name="expiry_date">

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <div class="input-row">
                <label for="mrp">MRP:</label>
                <input type="number" id="mrp" name="mrp" step="0.01" required>
            </div>

            <div class="button-group" style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button type="submit" class="button">Add Medicine</button>
                <a href="inventory.php" class="button">Back to Inventory</a>
            </div>
        </form>
    </div>
</body>
</html>

