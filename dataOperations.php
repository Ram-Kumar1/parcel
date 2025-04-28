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
    $sql = "SELECT DISTINCT FROM_MOBILE FROM booking";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['FROM_MOBILE']) . '">' . htmlspecialchars($row['FROM_MOBILE']) . '</option>';
    }
    exit;
}

// Fetch Name list
if (isset($_GET['getname'])) {
    $sql = "SELECT DISTINCT FROM_NAME FROM booking ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['FROM_NAME']) . '">' . htmlspecialchars($row['FROM_NAME']) . '</option>';
    }
    exit;
}

// Mapping mobile to name
if (isset($_GET['getMobileNameMapping'])) {
    $sql = "SELECT FROM_MOBILE, FROM_NAME FROM booking";
    $result = $conn->query($sql);

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['FROM_MOBILE']] = $row['FROM_NAME'];
    }
    echo json_encode($map);
    exit;
}

// Fetch To Mobile list
if (isset($_GET['getToMobile'])) {
    $sql = "SELECT DISTINCT TO_MOBILE FROM booking";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['TO_MOBILE']) . '">' . htmlspecialchars($row['TO_MOBILE']) . '</option>';
    }
    exit;
}

// Fetch To Name list
if (isset($_GET['getToname'])) {
    $sql = "SELECT DISTINCT TO_NAME FROM booking ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['TO_NAME']) . '">' . htmlspecialchars($row['TO_NAME']) . '</option>';
    }
    exit;
}

// Mapping To mobile to name
if (isset($_GET['getToMobileNameMapping'])) {
    $sql = "SELECT TO_MOBILE, TO_NAME FROM booking";
    $result = $conn->query($sql);

    $map = [];
    while ($row = $result->fetch_assoc()) {
        $map[$row['TO_MOBILE']] = $row['TO_NAME'];
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
            "VEHICLE_DESCRIPTION" => $description


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
            "VEHICLE_DESCRIPTION" => $description,

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




if (isset($_POST['updateExistingMember'])) {
    $memberId = $_POST['updateExistingMember'];
    $memberName = $_POST['memberName'];
    $memberMobile = $_POST['memberMobile'];
    $memberAddress = $_POST['memberAddress'];

    $data = array(
        "MEMBER_NAME" => $memberName,
        "MEMBER_MOBILE" => $memberMobile,
        "MEMBER_ADDRESS" => $memberAddress
    );
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->updateData("members", $data, $conditions);
}


if (isset($_POST['updateExistingMember'])) {
    $memberId = $_POST['updateExistingMember'];
    $memberName = $_POST['memberName'];
    $memberMobile = $_POST['memberMobile'];
    $memberAddress = $_POST['memberAddress'];

    $data = array(
        "MEMBER_NAME" => $memberName,
        "MEMBER_MOBILE" => $memberMobile,
        "MEMBER_ADDRESS" => $memberAddress
    );
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->updateData("members", $data, $conditions);
}

if (isset($_POST['deleteMemberId'])) {
    $memberId = $_POST['deleteMemberId'];
    $conditions = array(
        "MEMBER_ID" => $memberId,
    );
    echo $dbOperator->deleteRecord("members", $conditions);
}

if (isset($_POST['saveNewChitMaster'])) {
    $chitAmount             = $_POST['chitAmount'];
    $noOfChits              = $_POST['noOfChits'];
    $chitAmountDetails      = $_POST['chitAmountDetails'];
    $agentId                = $_POST['agentId'];

    $data = array(
        "CHIT_NAME"                 => $chitAmount,
        "NO_OF_CHITS"               => $noOfChits,
        "CREATED_BY_AGENT"          => $agentId
    );
    $dbOperator->insertData("chit_details", $data);

    /* Get the CHIT_MASTER_ID for correspondig chit */
    $selectCondition = array(
        "CHIT_NAME"                       => $chitAmount,
        "CREATED_BY_AGENT"                => $agentId
    );
    $chitJson = $dbOperator->selectQueryToJson("chit_details", "*", $selectCondition);
    $dataArray = json_decode($chitJson, true);
    $chitId = $dataArray[0]['CHIT_MASTER_ID'];

    /* Insert Chit Amount Mappings */
    $chitAmountDetailsObj = json_decode($chitAmountDetails, true);
    // print_r($chitAmountDetailsObj);
    for ($i = 0; $i < count($chitAmountDetailsObj); $i++) {
        $data = array(
            "CHIT_MASTER_ID"           => $chitId,
            "MONTHLY_AMOUNT"    => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
            "KASAR"             => $chitAmountDetailsObj[$i]["KASAR_AMOUNT"],
            "TOTAL_AMOUNT"      => $chitAmountDetailsObj[$i]["TOTAL_AMOUNT"]
        );
        $dbOperator->insertData("chit_master_amount_mappings", $data);
    }
}

if (isset($_POST['saveNewGroup'])) {
    $groupName              = $_POST['groupName'];
    $noOfMembers            = $_POST['noOfMembers'];
    $noOfChits              = $_POST['noOfChits'];
    $startDate              = $_POST['startDate'];
    $endDate                = $_POST['endDate'];
    $membersArrayObj        = $_POST['membersArrayObj'];
    $chitAmountDetails      = $_POST['chitAmountDetails'];
    $agentId                = $_POST['agentId'];

    $data = array(
        "GROUP_NAME"                => $groupName,
        "NO_OF_MEMBERS"             => $noOfMembers,
        "NO_OF_CHITS"               => $noOfChits,
        "START_DATE"                => $startDate,
        "END_DATE"                  => $endDate,
        "IS_ACTIVE"                 => 1,
        "CREATED_BY_AGENT"          => $agentId
    );
    $dbOperator->insertData("group_info", $data);

    /* Get the GROUP_ID for correspondig group */
    $selectCondition = array(
        "GROUP_NAME"                => $groupName,
        "START_DATE"                => $startDate
    );
    $groupJson = $dbOperator->selectQueryToJson("group_info", "*", $selectCondition);
    $dataArray = json_decode($groupJson, true);
    $groupId = $dataArray[0]['GROUP_ID'];

    $membersArrayObj = json_decode($membersArrayObj, true);

    /* Insert Chit Amount Mappings */
    $chitAmountDetailsObj = json_decode($chitAmountDetails, true);
    // print_r($chitAmountDetailsObj);
    for ($i = 0; $i < count($chitAmountDetailsObj); $i++) {
        $data = array(
            "GROUP_ID" => $groupId,
            "CHIT_MONTH" => $chitAmountDetailsObj[$i]["CHIT_MONTH"] . "-01",
            "MONTHLY_AMOUNT" => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
            "KASAR" => $chitAmountDetailsObj[$i]["KASAR_AMOUNT"],
            "TOTAL_AMOUNT" => $chitAmountDetailsObj[$i]["TOTAL_AMOUNT"],
            "IS_ACTIVE" => 1,
        );
        $dbOperator->insertData("chit_amount_mappings", $data);

        for ($j = 0; $j < count($membersArrayObj); $j++) {
            $data = array(
                "GROUP_ID" => $groupId,
                "CHIT_MONTH" => $chitAmountDetailsObj[$i]["CHIT_MONTH"] . "-01",
                "MONTHLY_AMOUNT" => $chitAmountDetailsObj[$i]["MONTHLY_AMOUNT"],
                "MEMBER_ID" => $membersArrayObj[$j]["MEMBER_ID"]
            );
            $dbOperator->insertData("accounts_details", $data);
        }
    }

    /* Insert Member Details Mapping */
    for ($i = 0; $i < count($membersArrayObj); $i++) {
        $memberId = $membersArrayObj[$i]["MEMBER_ID"];
        $data = array(
            "GROUP_ID" => $groupId,
            "MEMBER_ID" => $memberId
        );
        $dbOperator->insertData("chit_member_mappings", $data);
    }
}

if (isset($_POST['viewChitMasterDetails'])) {
    $selectCondition = array(
        "CHIT_MASTER_ID" => $_POST['groupId']
    );
    $groupChitsAmountInfo = $dbOperator->selectQueryToJson("v_chit_master_details", "*", $selectCondition);

    $response = array(
        "CHIT_AMOUNT" => $groupChitsAmountInfo
    );

    print_r(json_encode($response));
}

if (isset($_POST['viewGroupAndMemberDetails'])) {
    $selectCondition = array(
        "GROUP_ID" => $_POST['groupId']
    );
    $groupChitsAmountInfo = $dbOperator->selectQueryToJson("v_chit_group_details", "*", $selectCondition);
    $groupMembersInfo = $dbOperator->selectQueryToJson("v_chit_group_members", "*", $selectCondition);

    $response = array(
        "CHIT_AMOUNT" => $groupChitsAmountInfo,
        "MEMBER_INFO" => $groupMembersInfo
    );

    print_r(json_encode($response));
}

if (isset($_POST['deleteGroupId'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['deleteGroupId']
    );
    echo $dbOperator->deleteRecord("chit_amount_mappings", $conditions);
    echo $dbOperator->deleteRecord("chit_member_mappings", $conditions);
    echo $dbOperator->deleteRecord("group_info", $conditions);
}

if (isset($_POST['deleteChitMasterId'])) {
    $conditions = array(
        "CHIT_MASTER_ID" => $_POST['deleteChitMasterId']
    );
    echo $dbOperator->deleteRecord("chit_master_amount_mappings", $conditions);
    echo $dbOperator->deleteRecord("chit_details", $conditions);
}

if (isset($_POST['getChitDatesForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "DISTINCT CHIT_MONTH", $conditions, "CHIT_MONTH ASC"));
}

if (isset($_POST['getChitDatesForSelectedMember'])) {
    $conditions = array(
        "MEMBER_ID" => $_POST['memberId']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "DISTINCT CHIT_MONTH", $conditions, "CHIT_MONTH ASC"));
}

if (isset($_POST['getChitPaymentDetailsForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId'],
        "CHIT_MONTH" => $_POST['month']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "*", $conditions, "MEMBER_NAME ASC"));
}

if (isset($_POST['getChitPaymentDetailsForSelectedMember'])) {
    $conditions = array(
        "MEMBER_ID" => $_POST['memberId'],
        "CHIT_MONTH" => $_POST['month']
    );
    print_r($dbOperator->selectQueryToJson("v_chit_accounts_details", "*", $conditions, "GROUP_NAME ASC"));
}

if (isset($_POST['getChitPaymentDetailsForOutstanding'])) {
    $conditions = array(
        'MEMBER_ID'     => array('operator' => '=', 'value' => $_POST['memberId']),
        'CHIT_MONTH'    => array('operator' => '<', 'value' => date("Y-m-d"))
    );
    print_r($dbOperator->selectQueryToJsonWithAdditionalWhereClause("v_chit_accounts_details", "*", $conditions));
}

if (isset($_POST['updateChitWisePayment'])) {
    $dataArray = json_decode($_POST['dataArray'], true);
    print_r($dataArray);

    for ($i = 0; $i < count($dataArray); $i++) {
        $data = array(
            "PAID_AMOUNT" => ($dataArray[$i]["ADVANCE_AMOUNT"] + $dataArray[$i]["PAID_AMOUNT"]),
            "BALANCE" => $dataArray[$i]["CURRENT_BALANCE"]
        );
        $updateConditions = array(
            "ACCOUNT_ID" => $dataArray[$i]["ACCOUNT_ID"]
        );
        echo $dbOperator->updateData("accounts_details", $data, $updateConditions);

        $data = array();
        $data = array(
            "ACCOUNT_ID" => $dataArray[$i]["ACCOUNT_ID"],
            "ADVANCE_AMOUNT" => $dataArray[$i]["ADVANCE_AMOUNT"],
            "EXISTING_BALANCE" => $dataArray[$i]["EXISTING_BALANCE"],
            "PAID_AMOUNT" => $dataArray[$i]["PAID_AMOUNT"],
            "NEW_BALANCE" => $dataArray[$i]["CURRENT_BALANCE"]
        );
        echo $dbOperator->insertData("transaction_details", $data);
    }
}

if (isset($_POST['getMemberDetailsForSelectedGroup'])) {
    $conditions = array(
        "GROUP_ID" => $_POST['groupId'],
        "IS_TAKEN" => 0
    );
    print_r($dbOperator->selectQueryToJson("v_chit_group_members", "MEMBER_ID, CONCAT(MEMBER_NAME, ' - ', MEMBER_MOBILE) AS MEMBER_NAME", $conditions));
}

if (isset($_POST['updateChitTaker'])) {
    $data = array(
        "MEMBER_TAKEN" => $_POST['memberId']
    );
    $updateConditions = array(
        "CHIT_ID" => $_POST['chitId'],
        "GROUP_ID" => $_POST['groupId'],
    );
    echo $dbOperator->updateData("chit_amount_mappings", $data, $updateConditions);

    $data = array();
    $data = array(
        "IS_TAKEN" => 1
    );
    $updateConditions = array();
    $updateConditions = array(
        "MEMBER_ID" => $_POST['memberId'],
        "GROUP_ID" => $_POST['groupId'],
        "IS_TAKEN" => 0,
    );
    echo $dbOperator->updateData("chit_member_mappings", $data, $updateConditions, " LIMIT 1");
}

if (isset($_POST['getChitAndMemberNamesForGivenDate'])) {
    $conditions = array(
        'USER_ID'       => array('operator' => '=', 'value' => $_POST['agentId']),
        'PAID_AMOUNT'   => array('operator' => '>', 'value' => '0'),
        'PAID_DATE'     => array('operator' => '>=', 'value' => $_POST['startDate']),
        'PAID_DATE'     => array('operator' => '<=', 'value' => $_POST['endDate'])
    );
    print_r(
        $dbOperator->selectQueryToJsonWithAdditionalWhereClause(
            "v_chit_transaction_details",
            "GROUP_NAME, CONCAT(MEMBER_NAME, ' - ', MEMBER_MOBILE) AS MEMBER_NAME",
            $conditions
        )
    );
}

if (isset($_POST['isForOutstandingReportBasedOnMember'])) {
    $conditions = array(
        'MEMBER_ID'     => array('operator' => '=', 'value' => $_POST['memberId']),
        'CHIT_MONTH'    => array('operator' => '=', 'value' => $_POST['chitMonth']),
        'GROUP_ID'      => array('operator' => '=', 'value' => $_POST['groupId']),
        'PAID_AMOUNT'   => array('operator' => '>', 'value' => 0)
    );

    $selectConditions = "GROUP_NAME, CHIT_MONTH, PAID_AMOUNT, NEW_BALANCE AS BALANCE, DATE_FORMAT(PAID_DATE, '%Y-%m-%d') AS PAID_DATE";
    print_r($dbOperator->selectQueryToJsonWithAdditionalWhereClause(
        "v_chit_transaction_details",
        $selectConditions,
        $conditions
    ));
}
