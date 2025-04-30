<?php
include 'dbConn.php';

class DBOperations
{
    /* 
    * Insert the records into given table name 
    * params: tableName (string), tableData (Array) 
    */
    function insertData($table, $data)
    {
        global $conn;
        // Build the columns and values strings
        $columns = "";
        $values = "";
        foreach ($data as $key => $value) {
            $columns .= "`$key`, ";
            $values .= "'" . mysqli_real_escape_string($conn, $value) . "', ";
        }

        // Remove the trailing comma and space
        $columns = rtrim($columns, ", ");
        $values = rtrim($values, ", ");

        // Build the complete SQL query
        $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";

        // Execute the query
        $result = mysqli_query($conn, $sql);

        // Check for errors
        if ($result) {
            return "Insert Successful - ". mysqli_insert_id($conn);
            
        } else {
            $error = mysqli_error($conn);
            return "Insert Error " . $error;
        }
    }

    /* 
    * Insert bulk records into given table name 
    * params: tableName (string), tableData (Array) 
    */
    function insertBulkData($table, $dataArray, $batchSize = 500)
    {
        global $conn;

        if (empty($dataArray)) {
            return "No data to insert.";
        }

        $totalRows = count($dataArray);
        mysqli_begin_transaction($conn); // Start the transaction

        // Prepare the columns from the first element of the data array
        $columns = "";
        if (isset($dataArray[0]) && is_array($dataArray[0])) {
            $columns = implode(", ", array_map(function ($key) {
                return "`$key`";
            }, array_keys($dataArray[0])));
        }

        for ($i = 0; $i < $totalRows; $i += $batchSize) {
            // Slice the data array for the current batch
            $batch = array_slice($dataArray, $i, $batchSize);
            $values = [];

            foreach ($batch as $data) {
                $rowValues = [];
                foreach ($data as $value) {
                    $rowValues[] = "'" . mysqli_real_escape_string($conn, $value) . "'";
                }
                $values[] = "(" . implode(", ", $rowValues) . ")";
            }

            // Create the complete SQL query for the batch
            $sql = "INSERT INTO `$table` ($columns) VALUES " . implode(", ", $values);
            $result = mysqli_query($conn, $sql);

            // Check for errors
            if (!$result) {
                mysqli_rollback($conn); // Rollback if there's an error
                $error = mysqli_error($conn);
                return "Insert Error: " . $error;
            }
        }

        mysqli_commit($conn); // Commit the transaction
        return "Insert Successful";
    }

    /* 
    * Update the records into given table name 
    * params: tableName (string), tableData (Array) 
    */
    function updateData($table, $data, $conditions, $limit = null)
    {
        global $conn;

        // Build the SET clause
        $set = "";
        foreach ($data as $key => $value) {
            $set .= "`$key` = '" . mysqli_real_escape_string($conn, $value) . "', ";
        }
        $set = rtrim($set, ", ");

        // Build the WHERE clause
        $where = "";
        foreach ($conditions as $key => $value) {
            $where .= "`$key` = '" . mysqli_real_escape_string($conn, $value) . "' AND ";
        }
        $where = rtrim($where, " AND ");

        // Build the complete SQL query
        $sql = "UPDATE `$table` SET $set WHERE $where";

        // Add LIMIT clause if provided
        if (!empty($limit)) {
            $sql .= $limit;
        }

        // echo "Update SQL: " . $sql;
        // Execute the query
        $result = mysqli_query($conn, $sql);

        // Check for errors
        if ($result) {
            $affectedRows = mysqli_affected_rows($conn);
            echo "Update Successful" . $affectedRows;
        } else {
            $error = mysqli_error($conn);
            echo "Update Error" . $error;
        }
    }

    function deleteRecord($table, $condition)
    {
        global $conn;

        // Build the WHERE clause
        $where = "";
        foreach ($condition as $key => $value) {
            $where .= "`$key` = '" . mysqli_real_escape_string($conn, $value) . "' AND ";
        }
        $where = rtrim($where, " AND ");

        // Build the complete SQL query
        $sql = "DELETE FROM `$table` WHERE $where";

        // Execute the query
        $result = mysqli_query($conn, $sql);

        // Check for errors
        if ($result) {
            $affectedRows = mysqli_affected_rows($conn);
            echo "Delete Successful " . $affectedRows;
        } else {
            $error = mysqli_error($conn);
            echo "Deletion Error " . $error;
        }
    }

    function executePlainSql($sql)
    {
        global $conn;
        mysqli_query($conn, $sql);
    }

    function selectQueryToJson($tableName, $conditions = "*", $additionalConditions = array(), $orderBy = null)
    {
        global $conn;

        $query = "SELECT $conditions FROM $tableName";

        if (!empty($additionalConditions)) {
            $query .= " WHERE ";
            $conditionsArr = array();
            foreach ($additionalConditions as $column => $value) {
                $conditionsArr[] = "$column = '$value'";
            }
            $query .= implode(" AND ", $conditionsArr);
        }

        // Add ORDER BY clause if provided
        if (!empty($orderBy)) {
            // Validate the orderBy format
            if (preg_match('/^[\w\.\s]+(?:\s(?:ASC|DESC))?$/i', $orderBy)) {
                $query .= " ORDER BY $orderBy";
            } else {
                die("Invalid ORDER BY clause.");
            }
        }

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_free_result($result);

        // Encode the result array to JSON
        return json_encode($rows);
    }

    function selectQueryToJsonWithAdditionalWhereClause($tableName, $conditions = "*", $additionalConditions = array(), $orderBy = null)
    {
        global $conn;

        $query = "SELECT $conditions FROM $tableName";

        if (!empty($additionalConditions)) {
            $query .= " WHERE ";
            $conditionsArr = array();

            foreach ($additionalConditions as $column => $condition) {
                if (is_array($condition)) {
                    if (isset($condition['operator']) && isset($condition['value'])) {
                        $operator = $condition['operator'];
                        $value = mysqli_real_escape_string($conn, $condition['value']);
                        $conditionsArr[] = "$column $operator '$value'";
                    } else {
                        // Handle multiple conditions for a single column
                        foreach ($condition as $subCondition) {
                            if (isset($subCondition['operator']) && isset($subCondition['value'])) {
                                $operator = $subCondition['operator'];
                                $value = mysqli_real_escape_string($conn, $subCondition['value']);
                                $conditionsArr[] = "$column $operator '$value'";
                            } else {
                                die("Invalid condition format.");
                            }
                        }
                    }
                } else {
                    die("Invalid condition format.");
                }
            }

            $query .= implode(" AND ", $conditionsArr);
        }

        // Add ORDER BY clause if provided
        if (!empty($orderBy)) {
            if (preg_match('/^[\w\.\s]+(?:\s(?:ASC|DESC))?$/i', $orderBy)) {
                $query .= " ORDER BY $orderBy";
            } else {
                die("Invalid ORDER BY clause.");
            }
        }

        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);

        return json_encode($rows);
    }

    function selectQueryToJsonWithAdditionalWhereClauseAndGroupBy($tableName, $conditions = "*", $additionalConditions = array(), $groupBy = null, $orderBy = null)
    {
        global $conn;

        $query = "SELECT $conditions FROM $tableName";

        if (!empty($additionalConditions)) {
            $query .= " WHERE ";
            $conditionsArr = array();

            foreach ($additionalConditions as $column => $condition) {
                if (is_array($condition) && isset($condition['operator']) && isset($condition['value'])) {
                    $operator = $condition['operator'];
                    $value = mysqli_real_escape_string($conn, $condition['value']); // Escaping value for safety

                    $conditionsArr[] = "$column $operator '$value'";
                } else {
                    die("Invalid condition format.");
                }
            }
            $query .= implode(" AND ", $conditionsArr);
        }

        // Add GROUP BY clause if provided
        if (!empty($groupBy)) {
            $query .= " GROUP BY " . mysqli_real_escape_string($conn, $groupBy);
        }

        // Add ORDER BY clause if provided
        if (!empty($orderBy)) {
            $query .= " ORDER BY " . mysqli_real_escape_string($conn, $orderBy);
        }

        $result = mysqli_query($conn, $query);
        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_free_result($result);

        // Encode the result array to JSON
        return json_encode($rows);
    }


    function getRecords($sql)
    {
        global $conn;
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            return $row;
        } else {
            return "No records";
        }
    }

    function getRecordsCount($sql)
    {
        global $conn;
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            return mysqli_num_rows($result);
        } else {
            return 0;
        }
    }
}
