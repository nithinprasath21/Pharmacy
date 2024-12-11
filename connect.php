<?php
// MongoDB connection setup using MongoDB\Driver\Manager
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Utility function to execute queries and return results
function executeQuery($collection, $filter = [], $options = []) {
    global $manager;
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("pharmacy.$collection", $query);
    return iterator_to_array($cursor); // Return the result as an array
}

// Utility function to execute bulk write operations
function executeBulkWrite($collection, $bulk) {
    global $manager;
    $manager->executeBulkWrite("pharmacy.$collection", $bulk);
}
?>