<?php
include 'dbOperations.php';

$dbOperator = new DBOperations();

session_start();

if (isset($_POST['isForLogIn'])) {
    // Sanitize inputs (basic example)
    $userName = trim($_POST['userName']);
    $password = trim($_POST['password']);

    // Build condition array
    $selectCondition = array(
        "USER_NAME" => $userName,
        "PASSWORD"  => $password  // In production, use hashed password!
    );

    // Assuming $dbOperator is a valid DB helper instance
    $jsonResult = $dbOperator->selectQueryToJson("branches", "*", $selectCondition);
    $dataArray = json_decode($jsonResult, true);

    if (!empty($dataArray)) {
        // Set session values
        $_SESSION['userId']   = $dataArray[0]['BRANCH_ID'];
        $_SESSION['userName'] = $dataArray[0]['USER_NAME'];
        $_SESSION['admin']    = $dataArray[0]['ROUTE_NAME'];
        $_SESSION['place']    = $dataArray[0]['CITY'];

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Invalid credentials."]);
    }
}


if (isset($_POST['isForUpdatePassword'])) {
    $data = array(
        "PASSWORD"          => $_POST['newPassword']
    );
    $updateConditions = array(
        "USER_NAME"         => $_POST['userName']
    );
    echo $dbOperator->updateData("user_info", $data, $updateConditions);
}

if (isset($_POST['registerNewAgent'])) {
    $data = array(
        "AGENT_NAME"        => $_POST['agentName'],
        "MOBILE"            => $_POST['mobile'],
        "ALTERNATE_MOBILE"  => $_POST['alternateMobile'],
        "ADDRESS"           => $_POST['address'],
        "GST_NUMBER"        => $_POST['gstNum'],
        "USER_NAME"         => $_POST['userName'],
        "PASSWORD"          => $_POST['password']
    );
    echo $dbOperator->insertData("user_info", $data);
}
//getSaplePiDetails
if (isset($_POST['getBookingDetails'])) {
    if (empty($_POST['samplePiId'])) {
        echo json_encode(['status' => 'error', 'message' => 'No booking ID provided']);
        exit;
    }
    $selectCondition = array(
        "BOOKING_ID" => $_POST['samplePiId']
    );
    echo $dbOperator->selectQueryToJson("booking_details", "*", $selectCondition);
}

//Add place 
if (isset($_POST['addNewPlace'])) {
    $place = $_POST['place'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $selectCondition = array(
        "CITY_NAME" => $place
    );
    $jsonString = $dbOperator->selectQueryToJson("city", "CITY_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "PLACE_ALREADY_EXISTS";
    } else {
        $data = array(
            "CITY_NAME"  => $place,
            "STATE" => $state,
            "DISTRICT" => $district
        );
        echo $dbOperator->insertData("city", $data);
    }
}
// Delete place
if (isset($_POST['deletePlace'])) {
    $placeId = $_POST['place'];
    $conditions = array(
        "CITY_ID" => $placeId
    );
    echo $dbOperator->deleteRecord("city", $conditions);
}

// editPlace
if (isset($_POST['editPlace'])) {
    $placeId = $_POST['placeId'];
    $stateName = $_POST['stateName'];
    $districtName = $_POST['districtName'];
    $newPlaceName = $_POST['newPlaceName'];

    $data = array(
        "CITY_NAME" => $newPlaceName,
        "STATE" => $stateName,
        "DISTRICT" => $districtName
    );

    $conditions = array(
        "CITY_ID" => $placeId
    );
    echo $dbOperator->updateData("city", $data, $conditions);
}

// Get States
if (isset($_GET['getStates'])) {
    $sql = "SELECT STATE_ID, STATE_NAME FROM state";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['STATE_ID'] . '">' . $row['STATE_NAME'] . '</option>';
    }
}

// Get Cities
if (isset($_POST['getCities'])) {
    $state_id = $_POST['state_id'];

    $sql = "SELECT CITY_ID, CITY_NAME FROM cities WHERE STATE_ID = ? ORDER BY CITY_NAME ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['CITY_ID'] . '">' . $row['CITY_NAME'] . '</option>';
    }
    $stmt->close();
}

// Get Route
if (isset($_POST['getRouteName'])) {
    $stateId = $_POST['stateId'];
    $districtId = $_POST['districtId'];

    $sql = "SELECT ROUTE_NAME, BRANCH_ID FROM branches WHERE STATE = ? AND CITY = ? ORDER BY ROUTE_NAME ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $stateId, $districtId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) { 
        echo '<option value="' . $row['BRANCH_ID'] . '">' . $row['ROUTE_NAME'] . '</option>';
    }
    $stmt->close();
}

// addBranchDetails
if (isset($_POST['addBranchDetails'])) {

    // Sanitize inputs
    $inputs = [
        'branchName',
        'branchMobile',
        'branchAlternativeMobile',
        'branchAddress',
        'branchPlace',
        'state',
        'userName',
        'password',
        'isAgent',
        'bookingCommission',
        'receivedCommission'
    ];

    foreach ($inputs as $input) {
        $$input = isset($_POST[$input]) ? mysqli_real_escape_string($conn, $_POST[$input]) : '';
    }

    // Check for existing entries
    $uniqueChecks = [
        'ROUTE_NAME' => $branchName,
        'USER_NAME' => $userName,
        'MOBILE' => $branchMobile,
        'CITY' => $branchPlace
    ];

    foreach ($uniqueChecks as $field => $value) {
        $json = $dbOperator->selectQueryToJson("branches", $field, [$field => $value]);
        if (count(json_decode($json, true)) > 0) {
            echo strtoupper($field) . "_ALREADY_EXISTS";
            return;
        }
    }

    // Process expenses
    $expenseDescriptions = $_POST['expense_description'] ?? [];
    $expenseAmounts = $_POST['expense_amount'] ?? [];
    $expenses = [];
    $totalExpenseAmount = 0;

    for ($i = 0; $i < count($expenseDescriptions); $i++) {
        $desc = trim($expenseDescriptions[$i] ?? '');
        $amt = trim($expenseAmounts[$i] ?? '');

        if (!empty($desc) || !empty($amt)) {
            $amount = is_numeric($amt) ? floatval($amt) : 0;
            $expenses[] = [
                'description' => mysqli_real_escape_string($conn, $desc),
                'amount' => $amount
            ];
            $totalExpenseAmount += $amount;
        }
    }

    if ($isAgent != 1) {
        $bookingCommission = 0;
        $receivedCommission = 0;
    }
    // Prepare data for insertion
    $data = [
        "ROUTE_NAME" => $branchName,
        "MOBILE" => $branchMobile,
        "ALTERNATE_MOBILE" => $branchAlternativeMobile,
        "ADDRESS" => $branchAddress,
        "CITY" => $branchPlace,
        "STATE" => $state,
        "USER_NAME" => $userName,
        "PASSWORD" => $password,
        "PAID_COMMISION" => $bookingCommission,
        "TO_PAY_COMMISION" => $receivedCommission,
        "EXPENSES" => !empty($expenses) ? json_encode($expenses) : null,
        "TOTAL_EXPENSE_AMOUNT" => $totalExpenseAmount > 0 ? $totalExpenseAmount : null,
        "IS_AGENT" => $isAgent
    ];

    // Insert data
    echo $dbOperator->insertData("branches", $data);
}

// Fetch Mobile list
if (isset($_GET['getMobile'])) {
    $sql = "SELECT DISTINCT MOBILE FROM customer_details";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['MOBILE']) . '">' . htmlspecialchars($row['MOBILE']) . '</option>';
    }
    exit;
}

// Fetch Name list
if (isset($_GET['getname'])) {
    $sql = "SELECT DISTINCT CUSTOMER_NAME FROM customer_details ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['CUSTOMER_NAME']) . '">' . htmlspecialchars($row['CUSTOMER_NAME']) . '</option>';
    }
    exit;
}

// Mapping mobile to name
if (isset($_GET['getMobileNameMapping'])) {
    $sql = "SELECT MOBILE, CUSTOMER_NAME FROM customer_details";
    $result = $conn->query($sql);

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['MOBILE']] = $row['CUSTOMER_NAME'];
    }
    echo json_encode($map);
    exit;
}

// Fetch To Mobile list
if (isset($_GET['getToMobile'])) {
    $sql = "SELECT DISTINCT MOBILE FROM customer_details";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['MOBILE']) . '">' . htmlspecialchars($row['MOBILE']) . '</option>';
    }
    exit;
}

// Fetch To Name list
if (isset($_GET['getToname'])) {
    $sql = "SELECT DISTINCT CUSTOMER_NAME FROM customer_details ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['CUSTOMER_NAME']) . '">' . htmlspecialchars($row['CUSTOMER_NAME']) . '</option>';
    }
    exit;
}

// Mapping To mobile to name
if (isset($_GET['getToMobileNameMapping'])) {
    $sql = "SELECT MOBILE, CUSTOMER_NAME FROM customer_details";
    $result = $conn->query($sql);

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['MOBILE']] = $row['CUSTOMER_NAME'];
    }
    echo json_encode($map);
    exit;
}

// updateBranchDetails
// if (isset($_POST['updateBranchDetails'])) {
//     $branchOfficeId = mysqli_real_escape_string($conn, $_POST['branchOfficeId']);
//     $inputs = [
//         'branchName',
//         'branchMobile',
//         'branchAlternativeMobile',
//         'branchAddress',
//         'branchPlace',
//         'userName',
//         'password',
//         'paidCommission',
//         'totalCommission    ',
//         'isAgent'
//     ];

//     foreach ($inputs as $input) {
//         $$input = mysqli_real_escape_string($conn, $_POST[$input] ?? '');
//     }


//     $expenseDescriptions = $_POST['expense_description'] ?? [];
//     $expenseAmounts = $_POST['expense_amount'] ?? [];
//     $expenseValues = $_POST['expense'] ?? [];

//     $expenses = [];

//     for ($i = 0; $i < count($expenseDescriptions); $i++) {
//         $desc = trim($expenseDescriptions[$i]);
//         $amt = trim($expenseAmounts[$i]);

//         if ($desc !== '' || $amt !== '') {
//             $expenses[] = [
//                 'description' => mysqli_real_escape_string($conn, $desc),
//                 'amount' => mysqli_real_escape_string($conn, $amt)
//             ];
//         }
//     }


//     $totalExpenseAmount = 0;
//     foreach ($expenseValues as $val) {
//         $totalExpenseAmount += floatval($val);
//     }

//     $expenseJson = json_encode($expenses);


//     $data = [
//         "BRANCH_NAME" => $branchName,
//         "BRANCH_MOBILE" => $branchMobile,
//         "ALTERNATIVE_MOBILE" => $branchAlternativeMobile,
//         "ADDRESS" => $branchAddress,
//         "PLACE" => $branchPlace,
//         "USER_NAME" => $userName,
//         "PASSWORD" => $password,
//         "PAID_COMMISSION"      => !empty($paidCommission) ? $paidCommission : null,
//         "TOPAID_COMMISSION"    => !empty($totalCommission) ? $totalCommission : null,
//         "EXPENSE"              => !empty($expenseArray) ? json_encode($expenseArray) : null,
//         "TOTAL_EXPENSE_AMOUNT" => !empty($totalExpenseAmount) ? $totalExpenseAmount : null,
//         "STATUS" => 0,
//         "ISAGENT" => $isAgent ?: 0
//     ];
//     echo $dbOperator->updateData("branch_details", $data, ["BRANCH_OFFICE_ID" => $branchOfficeId]);
// }


//deleteBranch
if (isset($_POST['deleteBranch'])) {
    $branchId = mysqli_real_escape_string($conn, $_POST['BranchId']);
    $conditions = array(
        "BRANCH_ID" => $branchId
    );
    echo $dbOperator->deleteRecord("branches", $conditions);
}


//addHub
if (isset($_POST['addNewHub'])) {
    $hubName = $_POST['hub'];
    $hubMobile = $_POST['hubMobile'];
    $hubAddress = $_POST['hubAddress'];
    $selectCondition = array(
        "HUB_NAME" => $hubName
    );
    $jsonString = $dbOperator->selectQueryToJson("hub", "HUB_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);
    if ($rowCount > 0) {
        echo "HUB_NAME_ALREADY_EXISTS";
    } else {
        $data = array(
            "HUB_NAME" => $hubName,
            "HUB_MOBILE" => $hubMobile,
            "HUB_ADDRESS" => $hubAddress,
            "STATUS" => 0
        );
        echo $dbOperator->insertData("hub", $data);
    }
}

//editHub
if (isset($_POST['editHub'])) {
    $hubId = $_POST['hubId'];
    $hubName = $_POST['hubName'];
    $hubMobile = $_POST['hubMobile'];
    $hubAddress = $_POST['hubAddress'];
    $data = array(
        "HUB_NAME" => $hubName,
        "HUB_MOBILE" => $hubMobile,
        "HUB_ADDRESS" => $hubAddress
    );
    echo $dbOperator->updateData("hub", $data, ["HUB_ID" => $hubId]);
}

//deleteHub
if (isset($_POST['deleteHub'])) {
    $hubId = mysqli_real_escape_string($conn, $_POST['hubId']);
    $conditions = array(
        "HUB_ID" => $hubId
    );
    echo $dbOperator->deleteRecord("hub", $conditions);
}

//aad Driver
if (isset($_POST['addDriver'])) {
    $driverName = $_POST['driverName'];
    $driverMobile = $_POST['driverMobile'];
    $driverLicense = $_POST['driverLicense'];
    $vehicleno = $_POST['vehicleno'];
    $description = $_POST['description'];


    $selectCondition = array(
        "MOBILE" => $driverMobile
    );
    $jsonString = $dbOperator->selectQueryToJson("driver_details", "MOBILE", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);
    if ($rowCount > 0) {
        echo "MOBILE_NUMBER_ALREADY_EXISTS";
    } else {
        $data = array(
            "DRIVER_NAME" => $driverName,
            "MOBILE" => $driverMobile,
            "LICENSE" => $driverLicense,
            "VEHICLE_NUMBER" => $vehicleno,
            "VEHICLE_NAME" => $description


        );
        echo $dbOperator->insertData("driver_details", $data);
    }
}

// editDriver
if (isset($_POST['editdriver'])) {
    $driverid = $_POST['driverid'];
    $driverName = $_POST['driverName'];
    $driverMobile = $_POST['driverMobile'];
    $driverLicense = $_POST['driverLicense'];
    $vehicleno = $_POST['vehicleno'];
    $description = $_POST['description'];


    $checkQuery = $dbOperator->selectQueryToJson("driver_details", "MOBILE", array("MOBILE" => $driverName));
    $resultArray = json_decode($checkQuery, true);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "MOBILE_ALREADY_EXISTS";
    } else {
        $data = array(
            "DRIVER_NAME" => $driverName,
            "MOBILE" => $driverMobile,
            "LICENSE" => $driverLicense,
            "VEHICLE_NUMBER" => $vehicleno,
            "VEHICLE_NAME" => $description,

        );
        echo $dbOperator->updateData("driver_details", $data, ["DRIVER_ID" => $driverid]);
    }
}
// deleteDriver
if (isset($_POST['deleteDriver'])) {
    $driverId = mysqli_real_escape_string($conn, $_POST['driverId']);
    $conditions = array(
        "DRIVER_ID" => $driverId
    );
    echo $dbOperator->deleteRecord("driver_details", $conditions);
}

//add customer
if (isset($_POST['addCustomer'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        echo "INVALID_MOBILE_NUMBER";
        exit;
    }
    $selectCondition = array("MOBILE" => $mobile);
    $jsonString = $dbOperator->selectQueryToJson("customer_details", "MOBILE", $selectCondition);
    $resultArray = json_decode($jsonString, true);
    if (count($resultArray) > 0) {
        echo "MOBILE_NUMBER_ALREADY_EXISTS";
    } else {
        $data = array(
            "CUSTOMER_NAME" => $name,
            "MOBILE" => $mobile,
        );
        echo $dbOperator->insertData("customer_details", $data);
    }
}

// edit customer
if (isset($_POST['editcustomer'])) {
    $customerid = $_POST['customerid'];
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];

    $data = array(
        "CUSTOMER_NAME" => $name,
        "MOBILE" => $mobile,
    );
    echo $dbOperator->updateData("customer_details", $data, ["CUSTOMER_ID" => $customerid]);
}

//delete customer
if (isset($_POST['deleteCustomer'])) {
    $customerId = mysqli_real_escape_string($conn, $_POST['customerId']);
    $conditions = array(
        "CUSTOMER_ID" => $customerId
    );
    echo $dbOperator->deleteRecord("customer_details", $conditions);
}

//Add Item 
if (isset($_POST['addItem'])) {
    $ItemName = $_POST['ItemName'];
    $selectCondition = array(
        "ITEM_NAME" => $ItemName
    );
    $jsonString = $dbOperator->selectQueryToJson("items", "ITEM_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);

    if ($rowCount > 0) {
        echo "Item_ALREADY_EXISTS";
    } else {
        $data = array(
            "ITEM_NAME"  => $ItemName
        );
        echo $dbOperator->insertData("items", $data);
    }
}
// edit Item
if (isset($_POST['editItem'])) {
    $ItemId = $_POST['ItemId'];
    $ItemName = $_POST['ItemName'];

    $selectCondition = array(
        "ITEM_NAME" => $ItemName
    );

    $jsonString = $dbOperator->selectQueryToJson("items", "ITEM_NAME", $selectCondition);
    $resultArray = json_decode($jsonString);
    $rowCount = count($resultArray);



    $conditions = array(
        "ITEM_ID" => $ItemId
    );

    $data = array(
        "ITEM_NAME" => $ItemName
    );

    echo $dbOperator->updateData("items", $data, $conditions);
}

//delete Item
if (isset($_POST['deleteItem'])) {
    $ItemId = mysqli_real_escape_string($conn, $_POST['ItemId']);
    $conditions = array(
        "ITEM_ID" => $ItemId
    );
    echo $dbOperator->deleteRecord("items", $conditions);
}


// //UpdateBranchAccount
// if (isset($_POST['UpdateBranchAccount'])) {

//     $branchAccountId = $_POST['BRANCH_ACCOUNT_ID'];
//     $bookingAmount = $_POST['BOOKING_AMOUNT'];
//     $receivedAmount = $_POST['RECEIVED_AMOUNT'];
//     $bookingPercentage = $_POST['BOOKING_PERCENTAGE'];
//     $receivedPercentage = $_POST['RECEIVED_PERCENTAGE'];
//     $commissionamount = $_POST['COMMISSION_AMOUNT'];
//     $payment_type = $_POST['PAYMENT_TYPE'];
//     $paid_amount = $_POST['PAID_AMOUNT'];
//     $notes = $_POST['NOTES'];

//     $data = array(
//         "BOOKING_AMOUNT" => $bookingAmount,
//         "RECEIVED_AMOUNT" => $receivedAmount,
//         "BOOKING_PERCENTAGE" => $bookingPercentage,
//         "RECEIVED_PERCENTAGE" => $receivedPercentage,
//         "COMMISSION_AMOUNT" => $commissionamount,
//         "PAYAMENT_TYPE" => $payment_type,
//         "PAID_AMOUNT" => $paid_amount,
//         "NOTES" => $notes,
//         "IS_REQUEST" => 1
//     );


//     echo $dbOperator->updateData("branch_account", $data, ["BRANCH_ACCOUNT_ID" => $branchAccountId]);

// }

//Approvel
if (isset($_POST['approvel'])) {
    $branchAccountId = $_POST['branchAccountID'];

    $sql = "UPDATE branch_account SET 
            PAID_AMOUNT = PAID_AMOUNT + REQUESTED_AMOUNT,
            REQUESTED_AMOUNT = '0',
            IS_REQUEST = 0
        WHERE BRANCH_ACCOUNT_ID = '$branchAccountId'";

    mysqli_query($conn, $sql);
    echo "Update Successful";
}

//Cancel
if (isset($_POST['Calcel'])) {
    $id = $_POST['Id'];
    $reason = $_POST['Reason'];

    $data = array(
        'ACCOUNTS_CANCEL_NOTE' => $reason,
        "IS_REQUEST" => 2

    );
    echo $dbOperator->updateData("branch_account", $data, ["BRANCH_ACCOUNT_ID" => $id]);
}

