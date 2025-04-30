<?php
session_start();
include 'dbConn.php';


include 'dbOperations.php';

$dbOperator = new DBOperations();


// date
date_default_timezone_set('Asia/Kolkata');
$date_1 =  date('d-m-Y H:i');
$date = date('Y-m-d', strtotime($date_1));
$dateTime = date('Y-m-d H:i', strtotime($date_1));


// Get Lr Number
// function getLrNumber($conn)
// {
//     $cdate = date('Y-m-d');
//     $datePart = date('Ymd');

//     // Check if LR number for today exists
//     $checkDateQry = "SELECT LR_NUMBER FROM lr_number WHERE DATE = '$cdate' LIMIT 1";
//     $checkResult = mysqli_query($conn, $checkDateQry);

//     if (!$checkResult) {
//         throw new Exception("Failed to check LR number: " . mysqli_error($conn));
//     }

//     if (mysqli_num_rows($checkResult) > 0) {
//         // Fetch current LR and update it
//         $row = mysqli_fetch_assoc($checkResult);
//         $currentLR = (int)$row['LR_NUMBER'];
//         $newLR = str_pad($currentLR + 1, 3, '0', STR_PAD_LEFT);

//         $updateLRNumberQry = "UPDATE lr_number SET LR_NUMBER = '$newLR' WHERE DATE = '$cdate'";
//         if (!mysqli_query($conn, $updateLRNumberQry)) {
//             throw new Exception("Failed to update LR number: " . mysqli_error($conn));
//         }

//         $lrSerial = $newLR;
//     } else {
//         // Insert initial LR number for the day
//         $lrSerial = '001';
//         $insertLRQry = "INSERT INTO lr_number (DATE, LR_NUMBER, STATUS) VALUES ('$cdate', '$lrSerial', 0)";
//         if (!mysqli_query($conn, $insertLRQry)) {
//             throw new Exception("Failed to insert LR number: " . mysqli_error($conn));
//         }
//     }

//     // Construct final LR number format
//     $finalLRNumber = 'ZH-' . $datePart . '-' . $lrSerial;
//     return $finalLRNumber;
// }
function getLrNumber($conn)
{
    // Get the latest LR number
    $checkQry = "SELECT LR_NUMBER FROM lr_number ORDER BY id DESC LIMIT 1";
    $checkResult = mysqli_query($conn, $checkQry);

    if (!$checkResult) {
        throw new Exception("Failed to check LR number: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) > 0) {
        // Fetch current LR number and increment
        $row = mysqli_fetch_assoc($checkResult);
        $currentLR = (int)$row['LR_NUMBER'];
        $newLR = str_pad($currentLR + 1, 3, '0', STR_PAD_LEFT);
    } else {
        // No record yet, start from 001
        $newLR = '001';
    }

    // Insert the new LR number
    $insertQry = "INSERT INTO lr_number (LR_NUMBER, STATUS) VALUES ('$newLR', 0)";
    if (!mysqli_query($conn, $insertQry)) {
        throw new Exception("Failed to insert LR number: " . mysqli_error($conn));
    }

    // Return final LR format
    $finalLRNumber = 'LR-' . $newLR;
    return $finalLRNumber;
}


//getItem
if (isset($_GET['getItem'])) {

    $sql = "SELECT ITEM_ID, ITEM_NAME FROM items";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['ITEM_ID'] . '">' . $row['ITEM_NAME'] . '</option>';
    }
    exit;
}
//Insert Booking Details
if (isset($_POST['addNewBooking'])) {

    $fromBranchId = $_POST['fromBranchId'];
    $bill_no = $_POST['bill_no'];
    $manual_lr = $_POST['manual_lr'];
    $payment_type = $_POST['payment_type'];
    $payment_method = $_POST['payment_method'];
    $invoice_number = $_POST['invoice_number'];
    $cust_invoice_values = $_POST['cust_invoice_values'];
    $from_mobile = $_POST['from_mobile'];
    $from_name = $_POST['from_name'];
    $from_customer = $_POST['from_customer'];
    $to_mobile = $_POST['to_mobile'];
    $to_name = $_POST['to_name'];
    $to_customer = $_POST['to_customer'];
    $to_state = $_POST['to_state'];
    $district = $_POST['district'];
    $route_name = $_POST['route_name'];
    $dcc = $_POST['dcc'];
    $transport_type = $_POST['transport_type'];
    $total_fright = $_POST['total_fright'];
    $loading = $_POST['loading'];
    $unloading = $_POST['unloading'];
    $lr_amount = $_POST['lr_amount'];
    $amount = $_POST['amount'];
    $items = $_POST['items'];
    try {
        if (isset($conn)) {

            $lrNumber = getLrNumber($conn);


            // Convert items array to JSON string for database storage
            $itemsJson = json_encode($items);

            $bookingData = array(
                'LR_NUMBER' => $lrNumber,
                'MANUAL_LR_NUMBER' => $manual_lr,
                'PAYMENT_TYPE' => $payment_type,
                'PAYMENT_METHOD' => $payment_method,
                'CUSTOMER_INVOICE_NUMBER' => $invoice_number,
                'CUSTOMER_INVOICE_VALUE' => $cust_invoice_values,
                'FROM_MOBILE' => $from_mobile,
                'FROM_NAME' => $from_name,
                'IS_FROM_PAYMENT_CUSTOMER' => $from_customer,
                'TO_MOBILE' => $to_mobile,
                'TO_NAME' => $to_name,
                'IS_TO_PAYMENT_CUSTOMER' => $to_customer,
                'FROM_BRANCH_ID' => $fromBranchId,
                'TO_BRANCH_ID' => $route_name,
                'ITEMS' => $itemsJson,
                'DCC' => $dcc,
                'TRANSPORT_TYPE' => $transport_type,
                'FRIGHT' => $total_fright,
                'LOADING' => $loading,
                'UNLOADING' => $unloading,
                'LR_AMOUNT' => $lr_amount,
                'TOTAL_AMOUNT' => $amount,
                'VERSION' => 0,
            );
            $response = $dbOperator->insertData("booking", $bookingData);
            $bookingId = trim($response);
            // mysqli_commit($conn);
            print_r($bookingId);
        }
    } catch (Exception $e) {
        if (isset($conn)) {
            mysqli_rollback($conn);
        }
        print_r("Error Occurred: " . $e->getMessage());
    }
}
//update Booking Details
if (isset($_POST['updateNewBooking'])) {
    $fromBranchId = $_POST['fromBranchId'];
    $bookingId = $_POST['booking_id'];
    $bill_no = $_POST['bill_no'];
    $manual_lr = $_POST['manual_lr'];
    $payment_type = $_POST['payment_type'];
    $payment_method = $_POST['payment_method'];
    $invoice_number = $_POST['invoice_number'];
    $cust_invoice_values = $_POST['cust_invoice_values'];
    $from_mobile = $_POST['from_mobile'];
    $from_name = $_POST['from_name'];
    $from_customer = $_POST['from_customer'];
    $to_mobile = $_POST['to_mobile'];
    $to_name = $_POST['to_name'];
    $to_customer = $_POST['to_customer'];
    $to_state = $_POST['to_state'];
    $district = $_POST['district'];
    $route_name = $_POST['route_name'];
    $dcc = $_POST['dcc'];
    $transport_type = $_POST['transportType'];
    $total_fright = $_POST['totalFright'];
    $loading = $_POST['loading'];
    $unloading = $_POST['unloading'];
    $lr_amount = $_POST['lrAmount'];
    $amount = $_POST['amount'];
    $items = $_POST['items'];

    try {
        if (isset($conn)) {

            $lrNumber = getLrNumber($conn);
            $itemsJson = json_encode($items);
            $bookingData = array(
                'LR_NUMBER' => $lrNumber,
                'MANUAL_LR_NUMBER' => $manual_lr,
                'PAYMENT_TYPE' => $payment_type,
                'PAYMENT_METHOD' => $payment_method,
                'CUSTOMER_INVOICE_NUMBER' => $invoice_number,
                'CUSTOMER_INVOICE_VALUE' => $cust_invoice_values,
                'FROM_MOBILE' => $from_mobile,
                'FROM_NAME' => $from_name,
                'IS_FROM_PAYMENT_CUSTOMER' => $from_customer,
                'TO_MOBILE' => $to_mobile,
                'TO_NAME' => $to_name,
                'IS_TO_PAYMENT_CUSTOMER' => $to_customer,
                'FROM_BRANCH_ID' => $fromBranchId,
                'TO_BRANCH_ID' => $route_name,
                'ITEMS' => $itemsJson,
                'DCC' => $dcc,
                'TRANSPORT_TYPE' => $transport_type,
                'FRIGHT' => $total_fright,
                'LOADING' => $loading,
                'UNLOADING' => $unloading,
                'LR_AMOUNT' => $lr_amount,
                'TOTAL_AMOUNT' => $amount
            );
            $where = array(
                "BOOKING_ID" => $bookingId
            );
            $dbOperator->insertData("booking_history", $bookingData);
            echo $dbOperator->updateData("booking", $bookingData, $where);
        }
    } catch (Exception $e) {
        if (isset($conn)) {
            mysqli_rollback($conn);
        }
        print_r("Error Occurred: " . $e->getMessage());
    }
}

if (isset($_POST['forBookingList'])) {
    $bookingId = $_POST['bookingId'];
    $selectSql = "SELECT * FROM booking WHERE BOOKING_ID = $bookingId";
    $bookingDetails = array();
    if (isset($conn) && $result = mysqli_query($conn, $selectSql)) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {

                $itemsJson = json_encode($row['ITEMS']);

                $bookingDetails['BOOKING_ID'] = $row['BOOKING_ID'];
                $bookingDetails['BOOKING_DATETIME'] = $row['BOOKING_DATETIME'];
                $bookingDetails['MANUAL_LR_NUMBER'] = $row['MANUAL_LR_NUMBER'];
                $bookingDetails['LR_NUMBER'] = $row['LR_NUMBER'];
                $bookingDetails['CUSTOMER_INVOICE_NUMBER'] = $row['CUSTOMER_INVOICE_NUMBER'];
                $bookingDetails['CUSTOMER_INVOICE_VALUE'] = $row['CUSTOMER_INVOICE_VALUE'];
                $bookingDetails['FROM_NAME'] = $row['FROM_NAME'];
                $bookingDetails['FROM_MOBILE'] = $row['FROM_MOBILE'];
                $bookingDetails['TO_NAME'] = $row['TO_NAME'];
                $bookingDetails['TO_MOBILE'] = $row['TO_MOBILE'];
                $bookingDetails['ITEMS'] = $itemsJson;
                $bookingDetails['TRANSPORT_TYPE'] = $row['TRANSPORT_TYPE'];
                $bookingDetails['FRIGHT'] = $row['FRIGHT'];
                $bookingDetails['PAYMENT_TYPE'] = $row['PAYMENT_TYPE'];
                $bookingDetails['PAYMENT_METHOD'] = $row['PAYMENT_METHOD'];
                $bookingDetails['TOTAL_AMOUNT'] = $row['TOTAL_AMOUNT'];
                $bookingDetails['LR_AMOUNT'] = $row['LR_AMOUNT'];
                $bookingDetails['LOADING'] = $row['LOADING'];
                $bookingDetails['UNLOADING'] = $row['UNLOADING'];
                $bookingDetails['DCC'] = $row['DCC'];
            }
        }
    }
    print_r(json_encode($bookingDetails));
}
//getDriverDetails
if (isset($_POST['getDriverDetails'])){
    $mobileNumber = $_POST['mobileNumber'];
    $stmt = $conn->prepare("SELECT DRIVER_NAME, VEHICLE_NUMBER, VEHICLE_NAME,LICENSE FROM driver_details WHERE MOBILE = ?");
    $stmt->bind_param("s", $mobileNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'driverName' => $row['DRIVER_NAME'],
            'vehicleNumber' => $row['VEHICLE_NUMBER'],
            'vehicleName' => $row['VEHICLE_NAME'],
            'vehicleLicense' => $row['LICENSE']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

}

if (isset($_POST['moveToShipOutward'])) {
    $bookingId      = $_POST['bookingId'];
    $shipmentVia    = $_POST['shipmentVia'];
    $hubSelect = ($shipmentVia === 'Hub') ? $_POST['hubSelect'] : $shipmentVia;
    $driverName     = $_POST['driverName'];
    $mobileNumber   = $_POST['mobileNumber'];
    $vehicleNumber  = $_POST['vehicleNumber'];
    $vehicleName    = $_POST['vehicleName'];

    try {
        $bookingData = [
            "BOOKING_STATUS" => 10
        ];
        $dbOperator->updateData("booking", $bookingData, ['BOOKING_ID' => $bookingId]);
        $GDMNumber = getGDMNo($conn);
        if (!$GDMNumber) {
            throw new Exception("Failed to generate GDM Number.");
        }
        $gdmData = [
            "BOOKING_ID" => $bookingId,
            "GDM_NUMBER" => $GDMNumber
        ];
        $gdmIdResponse = $dbOperator->insertData('gdm_number', $gdmData);
        if (!$gdmIdResponse) {
            throw new Exception("Failed to insert into gdm_number table.");
        }
        
        // Extract the numeric ID
        preg_match('/\d+$/', $gdmIdResponse, $matches);
        $gdmId = $matches[0] ?? null;
        
        if (!$gdmId) {
            throw new Exception("Invalid GDM ID returned.");
        }
        
         $mappingData = [
            "GDM_ID"         => $gdmId,
            "DRIVER_NAME"    => $driverName,
            "DRIVER_NUMBER"  => $mobileNumber,
            "VEHICLE_NUMBER" => $vehicleNumber,
            "VEHICLE_NAME"   => $vehicleName,
            "SHIPMENT_AREA"  => $hubSelect
        ];

        if (!$dbOperator->insertData('gdm_mapping', $mappingData)) {
            throw new Exception("Failed to insert into gdm_mapping table.");
        }
        echo "Success: GDM ID is $gdmId";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function getGDMNo($conn)
{
    $cdate = date('Y-m-d');
    $datePart = date('Ymd');
    mysqli_begin_transaction($conn);

    try {
        $checkDateQry = "SELECT GDM_NO FROM ref_no_gdm WHERE DATE = '$cdate' LIMIT 1";
        $checkResult = mysqli_query($conn, $checkDateQry);

        if (!$checkResult) {
            throw new Exception("Failed to check GDM number: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($checkResult) > 0) {
            $row = mysqli_fetch_assoc($checkResult);
            $currentLR = (int)$row['GDM_NO'];
            $newLR = str_pad($currentLR + 1, 3, '0', STR_PAD_LEFT);

            $updateLRNumberQry = "UPDATE ref_no_gdm SET GDM_NO = '$newLR' WHERE DATE = '$cdate'";
            if (!mysqli_query($conn, $updateLRNumberQry)) {
                throw new Exception("Failed to update GDM number: " . mysqli_error($conn));
            }

            $lrSerial = $newLR;
        } else {
            $lrSerial = '001';
            $insertLRQry = "INSERT INTO ref_no_gdm (DATE, GDM_NO) VALUES ('$cdate', '$lrSerial')";
            if (!mysqli_query($conn, $insertLRQry)) {
                throw new Exception("Failed to insert GDM number: " . mysqli_error($conn));
            }
        }

        $finalLRNumber = 'GDM-' . $datePart . '-' . $lrSerial;
        mysqli_commit($conn);
        return $finalLRNumber;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error in GDM generation: " . $e->getMessage();
        return false;
    }
}

if (isset($_POST['revertShipOutward'])) {
    $bookingId = $_POST['bookingId'];

    try {
        /* Update the booking_status in booking_details */
        // $data = array(
        //     'BOOKING_STAUTS' => 0,
        // );
        // $where = array('BOOKING_ID' => $bookingId);
        // echo $dbOperator->updateData("booking_details", $data, $where);

        echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 4 WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);
            print_r("Success");
        }
        /* Delete the existing records in shipment_details */


        // echo $updateQuery = $dbOperator->deleteRecord("shipment_details", ["BOOKING_ID" => $bookingId]);

        echo $updateQuery = "DELETE FROM shipment_details WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);
            print_r("Success");
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['moveToCBEShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    $driverDetails = $_POST['driverDetails'];
    try {

        $data = array(
            'BOOKING_STAUTS' => 2,
            'LAST_UPDATE_DATE' => $date,

        );
        $where = array('BOOKING_ID' => $bookingId);
        echo $dbOperator->updateData("booking_details", $data, $where);

        // echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 2, LAST_UPDATE_DATE = '$date' WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);


        //     // $data = array(
        //     //     'BOOKING_ID' => $bookingId,
        //     //     'SHIPMENT_2_DATE' => $date,
        //     //     'SHIPMENT_2_DATE_TIME' => $dateTime,
        //     //     'DRIVER_2_DETAILS' => $driverDetails
        //     // );
        //     // $where = array('BOOKING_ID' => $bookingId);
        //     // echo $updateDriverDetailsQry = $dbOperator->updateData("shipment_details", $data, $where);

        //     echo $updateDriverDetailsQry = "
        //                 UPDATE shipment_details SET 
        //                     SHIPMENT_2_DATE = '$date', 
        //                     SHIPMENT_2_DATE_TIME = '$dateTime', 
        //                     DRIVER_2_DETAILS = '$driverDetails'
        //                 WHERE BOOKING_ID = $bookingId
        //                 ";

        //     mysqli_query($conn, $updateDriverDetailsQry);
        //     print_r("Success");
        // }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['revertCBEShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    try {

        $data = array(
            'BOOKING_STAUTS' => 1,
            'LAST_UPDATE_DATE' => $date,
        );
        $where = array('BOOKING_ID' => $bookingId);
        echo $dbOperator->updateData("booking_details", $data, $where);

        // echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 1 WHERE BOOKING_ID = $bookingId";
        // if (isset($conn)) {
        //     mysqli_query($conn, $updateQuery);
        //     // echo $deleteDriverDetailsQry = "DELETE FROM `shipoutward_details` WHERE `BOOKING_ID` = $bookingId";
        //     // mysqli_query($conn, $deleteDriverDetailsQry);
        //     print_r("Success");
        // }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['moveToShipInward'])) {
    $bookingId = $_POST['bookingId'];
    try {
        // $data = array(
        //     'BOOKING_STAUTS' => 3,
        //     'LAST_UPDATE_DATE' => $date,
        // );
        // $where = array('BOOKING_ID' => $bookingId);
        // echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

        echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 3, LAST_UPDATE_DATE = '$date' WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);
            print_r("Success");
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['revertShipinward'])) {
    $bookingId = $_POST['bookingId'];
    $shipmentVia = $_POST['shipmentVia'];
    try {
        if ($shipmentVia == "Via_Coimbatore") {

            // $data = array(
            //     'BOOKING_STAUTS' => 2,
            //     'LAST_UPDATE_DATE' => $date,
            // );
            // $where = array('BOOKING_ID' => $bookingId);
            // echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

            echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 1 WHERE BOOKING_ID = $bookingId";
            if (isset($conn)) {
                mysqli_query($conn, $updateQuery);
                print_r("Success");
            }
        } else {

            // $data = array(
            //             'BOOKING_STAUTS' => 0,
            //             'LAST_UPDATE_DATE' => $date,
            //         );
            //         $where = array('BOOKING_ID' => $bookingId);
            //         echo $updateQuery = $dbOperator->updateData("booking_details", $data, $where);

            echo $updateQuery = "UPDATE booking_details SET BOOKING_STAUTS = 0 WHERE BOOKING_ID = $bookingId";
            if (isset($conn)) {
                mysqli_query($conn, $updateQuery);
                print_r("Success");
            }
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
    }
}

if (isset($_POST['newCustomerName'])) {
    /* $mobileNumber = $_POST['mobileNumber'];
    $newCustomerName = $_POST['newCustomerName'];
    
    $updateQuery = "
                        UPDATE customer_details SET
                          CUSTOMER_NAME = '$newCustomerName'
                        WHERE MOBILE = '$mobileNumber'
                   ";
    mysqli_query($conn, $updateQuery); */
    echo "inserted";
}

if (isset($_POST['updateMobileNumber'])) {
    //    $customerName = $_POST['customerName'];
    /* $mobileNumber = $_POST['mobileNumber'];

    $data = array(
        'MOBILE' => $mobileNumber,
    );
    $where = array(
        'MOBILE' => $mobileNumber
    );
    $dbOperator->insertData("customer_details", $data, $where);

    $upsertQuery = "
                        INSERT INTO customer_details (MOBILE)
                        SELECT * FROM (SELECT '$mobileNumber') AS tmp
                        WHERE NOT EXISTS (
                            SELECT MOBILE FROM customer_details WHERE MOBILE = '$mobileNumber'
                        ) LIMIT 1
                   ";
    mysqli_query($conn, $upsertQuery); */
}

if (isset($_POST['getBookingDetailsUnderCBEHub'])) {
    $toPlace = $_POST['toPlace'];


    // $data = array(
    //     'TO_PLACE' => $toPlace,
    //     'BOOKING_STATUS' => 1
    // );
    // $where = array(
    //     'TO_PLACE' => $toPlace,
    //     'BOOKING_STATUS' => 1
    // );
    // $dbOperator->selectQueryToJson("booking_details", $data, $where);

    $selectSql = "
                    SELECT *
                    WHERE TO_PLACE = '$toPlace' AND BOOKING_STATUS = 1
                ";
    $bookingDetails = array();
    if (isset($conn) && $result = mysqli_query($conn, $selectSql)) {
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $bookingDetails['BOOKING_ID'] = $row['BOOKING_ID'];
                $bookingDetails['CUSTOMER'] = $row['CUSTOMER'];
                $bookingDetails['MOBILE'] = $row['MOBILE'];
                $bookingDetails['DELIVERY_TO'] = $row['DELIVERY_TO'];
                $bookingDetails['DELIVERY_MOBILE'] = $row['DELIVERY_MOBILE'];
                $bookingDetails['FROM_PLACE'] = $row['FROM_PLACE'];
                $bookingDetails['FROM_MOBILE'] = $row['FROM_MOBILE'];
                $bookingDetails['TO_PLACE'] = $row['TO_PLACE'];
                $bookingDetails['TO_MOBILE'] = $row['TO_MOBILE'];
                $bookingDetails['QUANTITY'] = $row['QUANTITY'];
                $bookingDetails['QUANTITY_DETAILS'] = $row['QUANTITY_DETAILS'];
                $bookingDetails['QTY_DESCRIPTION'] = $row['QTY_DESCRIPTION'];
                $bookingDetails['PAYMENT_TYPE'] = $row['PAYMENT_TYPE'];
                $bookingDetails['TOTAL_AMOUNT'] = $row['TOTAL_AMOUNT'];
                $bookingDetails['TRANSPORTATION_COST'] = $row['TRANSPORTATION_COST'];
                $bookingDetails['LOADING_COST'] = $row['LOADING_COST'];
                $bookingDetails['ADDITIONAL_COST'] = $row['ADDITIONAL_COST'];
                $bookingDetails['GOODS_VALUE'] = $row['GOODS_VALUE'];
                $bookingDetails['DELIVERY_TYPE'] = $row['DELIVERY_TYPE'];
                $bookingDetails['INVOICE_NUMBER'] = $row['INVOICE_NUMBER'];
                $bookingDetails['BOOKING_STAUTS'] = $row['BOOKING_STAUTS'];
            }
        }
    }
    print_r(json_encode($bookingDetails));
}

//Delete Booking Details
if (isset($_POST['isDeleteBooking'])) {
    $bookingId = $_POST['bookingId'];

    $data = array(
        'IS_DELETE' => 1,
    );
    echo $dbOperator->updateData("booking", $data, ["BOOKING_ID" => $bookingId]);
}

//UpdatePayment
if (isset($_POST['UpdatePayment'])) {
    $customerId = $_POST['customerId'];
    $balanceamt = $_POST['balanceamt'];
    $paidAmount = $_POST['paidAmount'];
    $paymentType = $_POST['paymentType'];
    $notes = $_POST['notes'];
    $newbalance = $_POST['newbalance'];

    $updateQuery = "UPDATE customer_account SET PAID_AMOUNT = PAID_AMOUNT + $paidAmount WHERE CUSTOMER_ID = $customerId";
    mysqli_query($conn, $updateQuery);

    $data1 = array(
        'CUSTOMER_ID' => $customerId,
        'BALANCE_AMOUNT' => $balanceamt,
        'PAID_AMOUNT' => $paidAmount,
        'PAYMENT_TYPE' => $paymentType,
        'NOTES' => $notes,
        'NEW_BALANCE' => $newbalance,
        'status' => 0
    );
    echo $dbOperator->insertData('customer_transaction', $data1);
}
