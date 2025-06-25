<?php
require_once 'config.php';

// Create database connection
function db_connect() {
    static $connection;
    
    if (!isset($connection)) {
        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (!$connection) {
            die('Database Connection Failed: ' . mysqli_connect_error());
        }
        
        mysqli_set_charset($connection, 'utf8mb4');
    }
    
    return $connection;
}

// Execute query
function db_query($query) {
    $connection = db_connect();
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection) . '<br>Query: ' . $query);
    }
    
    return $result;
}

// Fetch data
function db_fetch_array($result) {
    return mysqli_fetch_assoc($result);
}

// Fetch all rows
function db_fetch_all($result) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Get number of rows
function db_num_rows($result) {
    return mysqli_num_rows($result);
}

// Escape string
function db_escape($string) {
    $connection = db_connect();
    return mysqli_real_escape_string($connection, $string);
}

// Get last insert ID
function db_insert_id() {
    $connection = db_connect();
    return mysqli_insert_id($connection);
}

// Close connection
function db_close() {
    $connection = db_connect();
    mysqli_close($connection);
}
?>