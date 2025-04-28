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
function getLrNumber($conn)
{
    $cdate = date('Y-m-d');
    $datePart = date('Ymd');

    // Check if LR number for today exists
    $checkDateQry = "SELECT LR_NUMBER FROM lr_number WHERE DATE = '$cdate' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkDateQry);

    if (!$checkResult) {
        throw new Exception("Failed to check LR number: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) > 0) {
        // Fetch current LR and update it
        $row = mysqli_fetch_assoc($checkResult);
        $currentLR = (int)$row['LR_NUMBER'];
        $newLR = str_pad($currentLR + 1, 3, '0', STR_PAD_LEFT);

        $updateLRNumberQry = "UPDATE lr_number SET LR_NUMBER = '$newLR' WHERE DATE = '$cdate'";
        if (!mysqli_query($conn, $updateLRNumberQry)) {
            throw new Exception("Failed to update LR number: " . mysqli_error($conn));
        }

        $lrSerial = $newLR;
    } else {
        // Insert initial LR number for the day
        $lrSerial = '001';
        $insertLRQry = "INSERT INTO lr_number (DATE, LR_NUMBER, STATUS) VALUES ('$cdate', '$lrSerial', 0)";
        if (!mysqli_query($conn, $insertLRQry)) {
            throw new Exception("Failed to insert LR number: " . mysqli_error($conn));
        }
    }

    // Construct final LR number format
    $finalLRNumber = 'ZH-' . $datePart . '-' . $lrSerial;
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

    $bille_no = $_POST['bille_no'];
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
    $rount_name = $_POST['rount_name'];
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
                'FROM_BRANCH_ID' => 0,
                'TO_BRANCH_ID' => 0,
                'ITEMS' => $itemsJson, // Use JSON string instead of array
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
    $bookingId = $_POST['booking_id'];
    $bille_no = $_POST['bille_no'];
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
    $rount_name = $_POST['rount_name'];
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
                'FROM_BRANCH_ID' => 0,
                'TO_BRANCH_ID' => 0,
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

            $bookingId = trim($bookingId); 
            $where = array(
                "BOOKING_ID" => $bookingId
            );
            
            $dbOperator->insertData("booking_history", $bookingData);
            echo $dbOperator->updateData("booking", $bookingData,$where);
            
          
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
               
            }
        }
    }
    print_r(json_encode($bookingDetails));
}

if (isset($_POST['moveToShipOutward'])) {
    $bookingId = $_POST['bookingId'];
    $driverDetails = $_POST['driverDetails'];
    $shipmentVia = $_POST['shipmentVia'];
    $shipmentVia = $_POST['shipmentVia'];
    $bookingStatus = $shipmentVia == "Via_Coimbatore" ? 1 : 2;
    $showInShipOutward = $shipmentVia == "Via_Coimbatore" ? 1 : 0;

    $driverDetailsJson = json_decode($driverDetails, true);

    try {
        echo $updateQuery = "UPDATE booking_details SET 
                                BOOKING_STAUTS = $bookingStatus, 
                                LAST_UPDATE_DATE = '$date',
                                SHIPMENT_VIA = '$shipmentVia',
                                SHOW_IN_VIEW_SHIPOUTWARD = $showInShipOutward
                             WHERE BOOKING_ID = $bookingId";
        if (isset($conn)) {
            mysqli_query($conn, $updateQuery);

            echo $updateDriverDetailsQry = "
                        INSERT INTO shipment_details 
                            (BOOKING_ID, SHIPMENT_1_DATE, SHIPMENT_1_DATE_TIME, DRIVER_1_DETAILS)
                        VALUES
                            ($bookingId, '$date', '$dateTime', '$driverDetails')
                        ";

            mysqli_query($conn, $updateDriverDetailsQry);
            print_r("Success");

            $driverName = $driverDetailsJson['DRIVER_NAME'];
            $driverMobile = $driverDetailsJson['DRIVER_MOBILE'];
            $advanceAmount = $driverDetailsJson['ADVANCE_AMOUNT'];
            $updateDriverDetailsQuery = "INSERT INTO driver_details (DRIVER_NAME, MOBILE)
                                            SELECT * FROM (SELECT '$driverName', '$driverMobile',$advanceAmount) AS tmp
                                            WHERE NOT EXISTS (
                                                SELECT DRIVER_NAME, MOBILE FROM driver_details WHERE DRIVER_NAME = '$driverName' AND MOBILE = '$driverMobile' AND ADVANCE_AMOUNT = '$advanceAmount'
                                            ) LIMIT 1
                                        ";
            mysqli_query($conn, $updateDriverDetailsQuery);
        }
    } catch (Exception $e) {
        print_r("Error: " . $e);
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
