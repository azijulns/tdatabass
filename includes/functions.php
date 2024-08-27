<?php

namespace Tdatabass\Includes;

defined('ABSPATH') || die();



/**
 * Creates a new table in the WordPress database.
 *
 * @param string $tableName The name of the table to be created.
 * @param array $columns An array of column definitions. Each column definition is an associative array with keys for the column name, data type, and any additional constraints.
 * @return void
 *
 * @throws \Exception If the table creation fails.
 *
 * @since 1.0.0
 */
function create_table($tableName, $columns) {
    // Access the global $wpdb object to interact with the database.
    global $wpdb;

    // Get the charset and collation for the database.
    $charset_collate = $wpdb->get_charset_collate();

    // Generate the full table name with the WordPress prefix.
    $table_name = $wpdb->prefix . $tableName;

    // Construct the SQL statement for creating the table.
    $sql = "CREATE TABLE $table_name (";

    // Add column definitions to the SQL statement.
    foreach ($columns as $column) {
        $sql .= "{$column['name']} {$column['type']}";

        // Add additional constraints if present.
        if (isset($column['constraints'])) {
            $sql .= " {$column['constraints']}";
        }

        $sql .= ",\n";
    }

    // Add the primary key constraint.
    $sql .= "PRIMARY KEY  (id)";

    // Close the SQL statement.
    $sql .= ") $charset_collate;";

    // Include the upgrade.php file to use the dbDelta function.
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Execute the SQL statement using the dbDelta function.
    // This function will create the table if it doesn't exist, or update it if it does.
    dbDelta($sql);
}

// Usage:
// Define the column definitions.
// $columns = [
//     ['name' => 'id', 'type' => 'int(9)', 'constraints' => 'NOT NULL AUTO_INCREMENT'],
//     ['name' => 'name', 'type' => 'tinytext', 'constraints' => 'NOT NULL'],
//     ['name' => 'email', 'type' => 'tinytext', 'constraints' => 'NOT NULL'],
// ];

// Create the table with dynamic column definitions.
// create_table('test', $columns);

/**
 * Inserts data into a specified WordPress table and saves the data in JSON format.
 *
 * @param string $tableName The name of the table to insert data into.
 * @param array $data An associative array containing the data to be inserted.
 *
 * @return int|false The ID of the inserted row or false if the insertion failed.
 *
 * @throws \Exception If the insertion fails and an error message is available.
 *
 * @since 1.0.0
 */
function insert_data($tableName, $data) {
    global $wpdb;

    // Sanitize the table name to prevent SQL injection.
    $table_name = sanitize_text_field($wpdb->prefix . $tableName);

    // Validate that $data is an array and not empty.
    if (!is_array($data) || empty($data)) {
        throw new \Exception("Invalid data provided for insertion.");
    }

    // Sanitize each item in the $data array.
    $sanitized_data = array_map('sanitize_text_field', $data);

    // Insert the sanitized data into the table.
    $result = $wpdb->insert($table_name, $sanitized_data);

    // Check if the insertion was successful.
    if ($result === false) {
        // Get last error message from $wpdb
        $error_message = $wpdb->last_error;
        throw new \Exception("Failed to insert data into the table: $tableName. Error: $error_message");
    }

    // Return the ID of the inserted row.
    return $wpdb->insert_id;
}


// Usage:
// Define the data to be inserted.
// $data = [
//     'name' => 'Azijul',
//     'email' => 'niftybrown2@deliveryotter.com',
// ];
//   array(
//         'column1' => 'foo',
//         'column2' => 'bar',
//     )
// Insert the data into the 'test' table.
// insert_data('test', $data);
/**
 * Retrieves data from a specified row in a WordPress table based on a given column.
 *
 * @param string $table_name The name of the table to retrieve data from.
 * @param array $conditions An associative array containing the column name and its value to filter by.
 *
 * @return array The data retrieved from the table as an associative array.
 *
 * @throws \Exception If the query fails.
 */
function get_table_data($table_name, $conditions, $order_by = '', $limit = '', $offset = '') {
    global $wpdb;

    // Generate the full table name with the WordPress prefix.
    $full_table_name = $wpdb->prefix . $table_name;

    // Construct the SQL query with dynamic WHERE clause based on conditions.
    $where_clause = [];
    $values = [];
    foreach ($conditions as $column => $condition) {
        if (is_array($condition)) {
            // Use the specified operator or default to '='.
            $operator = isset($condition['operator']) ? $condition['operator'] : '=';
            $value = $condition['value'];
        } else {
            // If only value is provided, use '=' as the operator.
            $operator = '=';
            $value = $condition;
        }

        // Add the condition to the WHERE clause.
        $where_clause[] = "`$column` $operator %s";
        $values[] = $value;
    }

    // Construct the full SQL query.
    $sql = "SELECT * FROM `$full_table_name` WHERE " . implode(' AND ', $where_clause);

    // Add ORDER BY if provided.
    if (!empty($order_by)) {
        $sql .= " ORDER BY $order_by";
    }

    // Add LIMIT and OFFSET if provided.
    if (!empty($limit)) {
        if (!empty($offset)) {
            $sql .= " LIMIT %d OFFSET %d";
            $values[] = $limit;
            $values[] = $offset;
        } else {
            $sql .= " LIMIT %d";
            $values[] = $limit;
        }
    }

    // Prepare the SQL query with the values.
    $prepared_sql = $wpdb->prepare($sql, ...$values);

    // Execute the query and get the results.
    $results = $wpdb->get_results($prepared_sql, ARRAY_A);

    // Check if the query was successful.
    if ($wpdb->last_error) {
        throw new \Exception("Failed to retrieve data from the table: $table_name. Error: " . $wpdb->last_error);
    }

    // Return the results.
    return $results;
}

// Example usage:
$conditions = [
    'name' => [
        'operator' => "LIKE",
        'value' => 'U%'
    ]
];

$order_by = "id DESC";
$limit = 5;
$offset = '';

// $data = get_table_data('test', $conditions, $order_by, $limit, $offset);

// echo '<pre>';
// print_r($data);
// echo '</pre>';

// $conditions = ['name' => 'Azijul'];

function update_table_data($table_name, $data, $conditions) {
    global $wpdb;

    // Generate the full table name with the WordPress prefix.
    $full_table_name = $wpdb->prefix . $table_name;

    // Sanitize and prepare the data to be updated.
    $data_clause = [];
    $values = [];
    foreach ($data as $column => $value) {
        $data_clause[] = "`$column` = %s";
        $values[] = $value;
    }

    // Sanitize and prepare the WHERE clause based on conditions.
    $where_clause = [];
    foreach ($conditions as $column => $condition) {
        if (is_array($condition)) {
            // Use the specified operator or default to '='.
            $operator = isset($condition['operator']) ? $condition['operator'] : '=';
            $value = $condition['value'];
        } else {
            // If only value is provided, use '=' as the operator.
            $operator = '=';
            $value = $condition;
        }

        $where_clause[] = "`$column` $operator %s";
        $values[] = $value;
    }

    // Construct the full SQL query for updating.
    $sql = "UPDATE `$full_table_name` SET " . implode(', ', $data_clause) . " WHERE " . implode(' AND ', $where_clause);

    // Prepare the SQL query with the values.
    $prepared_sql = $wpdb->prepare($sql, ...$values);

    // Execute the query.
    $result = $wpdb->query($prepared_sql);

    // Check if the query was successful.
    if ($wpdb->last_error) {
        throw new \Exception("Failed to update data in the table: $table_name. Error: " . $wpdb->last_error);
    }

    // Return true if the update was successful.
    return $result !== false;
}

// Example usage:
$data = [
    'name' => 'Updated Name',
    'email' => 'relaxedardinghelli5@deliveryotter.com',
];

// $conditions = [
//     'name' => [
//         'operator' => "LIKE",
//         'value' => 'A%'
//     ]
// ];

// $result = update_table_data('test', $data, $conditions);

// echo '<pre style="margin-left:200px;">$result:';
// print_r( $result );
// echo '</pre>';
// die;


function delete_table_data($table_name, $conditions) {
    global $wpdb;

    // Generate the full table name with the WordPress prefix.
    $full_table_name = $wpdb->prefix . $table_name;

    // Sanitize and prepare the WHERE clause based on conditions.
    $where_clause = [];
    $values = [];
    foreach ($conditions as $column => $condition) {
        if (is_array($condition)) {
            // Use the specified operator or default to '='.
            $operator = isset($condition['operator']) ? $condition['operator'] : '=';
            $value = $condition['value'];
        } else {
            // If only value is provided, use '=' as the operator.
            $operator = '=';
            $value = $condition;
        }

        $where_clause[] = "`$column` $operator %s";
        $values[] = $value;
    }

    // Construct the full SQL query for deletion.
    $sql = "DELETE FROM `$full_table_name` WHERE " . implode(' AND ', $where_clause);

    // Prepare the SQL query with the values.
    $prepared_sql = $wpdb->prepare($sql, ...$values);

    // Execute the query.
    $result = $wpdb->query($prepared_sql);

    // Check if the query was successful.
    if ($wpdb->last_error) {
        throw new \Exception("Failed to delete data from the table: $table_name. Error: " . $wpdb->last_error);
    }

    // Return true if the deletion was successful.
    return $result !== false;
}

// Example usage:
// $conditions = [
//     'name' => [
//         'operator' => "LIKE",
//         'value' => 'A%'
//     ]
// ];

// $result = delete_table_data('test_table', $conditions);

// if ($result) {
//     echo "Data deleted successfully.";
// } else {
//     echo "Failed to delete data.";
// }
