<?php
session_start();
include('connect.php');

// Fetch the last 100 transactions from the database
$filter = [];
$transactions = executeQuery('transactions', $filter); // Adjust this to fetch the last 100 transactions as needed

// Handle search functionality
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $transactions = array_filter($transactions, function($transaction) use ($search_term) {
        foreach ($transaction->items as $item) { // Access items as properties of stdClass
            if (stripos($item->name, $search_term) !== false) {
                return true;
            }
        }
        return false;
    });
}

// Handle clear transactions functionality
if (isset($_POST['clear_transactions'])) {
    $bulk_clear = new MongoDB\Driver\BulkWrite;
    $bulk_clear->delete([]);
    executeBulkWrite('transactions', $bulk_clear);
    echo "<script>alert('All transactions have been cleared.');</script>";
    header("Location: transactions.php"); // Redirect back to transactions page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="style.css">
    
    <script>
      function printTransactions() {
          window.print(); // Trigger print dialog
      }
  </script>
</head>
<body>
    <header style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style='flex-grow: 1;'>Transaction History</h1>
        <a href='index.php' class='button back-home'>Back to Homepage</a> <!-- Back to Homepage Button -->
    </header>

    <div class="container" style="margin-top: 20px;"> <!-- Added margin-top for spacing -->
        <!-- Search Box -->
        <form method="get" style="margin-bottom: 20px; display: flex; justify-content: flex-end; align-items: center;">
            <input type="text" name="search" placeholder="Search by Medicine Name" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="height: 38px; margin-right: 10px;">
            <button type="submit" class="button" style="height: 38px;">Search</button>
        </form>

        <!-- Transactions Table -->
        <table border='1'>
            <thead>
                <tr style='background-color: #4CAF50; color: white;'>
                    <th>ID</th><th>Items</th><th>Total Amount</th><th>Customer Name</th><th>Customer Mobile</th><th>Date/Time</th></tr></thead><tbody><?php 
                    foreach ($transactions as $transaction): 
                        ?>
                        <?php 
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($transaction->_id) . "</td>";
                        echo "<td>";
                        foreach ($transaction->items as $item) { // Access items as properties of stdClass
                            echo htmlspecialchars($item->name) . " (Qty: " . htmlspecialchars($item->quantity) . "), ";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($transaction->total_amount) . "</td>";
                        echo "<td>" . htmlspecialchars($transaction->customer_name) . "</td>";
                        echo "<td>" . htmlspecialchars($transaction->customer_mobile) . "</td>";
                        echo "<td>" . htmlspecialchars($transaction->timestamp->toDateTime()->format('Y-m-d H:i:s')) . "</td>"; // Format timestamp
                        echo "</tr>"; 
                    endforeach; 
                    ?>
                </tbody></table>

                <!-- Button Section -->
                <div class='button-group' style='text-align:center; margin-top: 20px; display: flex; justify-content: space-between; align-items: center;'>
                    <form method='post' style='margin-right: auto;'>
                        <button type='submit' name='clear_transactions' class='button' style='background-color: red;'>Clear Transactions</button>
                    </form>
                    <button onclick="printTransactions()" class="button">Print Transactions</button>
                </div>

            </div>

        </body>
        </html>

