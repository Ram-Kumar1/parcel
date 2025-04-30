<?php
include 'dbConn.php';

$FromBranchName = $toBranchName = '';
$driverData = $bookigData = [];

if (isset($_GET['gdm'])) {
    $gdmId = $_GET['gdm'];

    $stmt = $conn->prepare("
        SELECT 
            g.GDM_ID,
            g.BOOKING_ID,
            b.*,
            fb.ROUTE_NAME AS FROM_BRANCH_NAME,
            tb.ROUTE_NAME AS TO_BRANCH_NAME,
            d.DRIVER_NAME,
            d.DRIVER_NUMBER,
            d.VEHICLE_NUMBER,
            d.VEHICLE_NAME
        FROM gdm_number g
        INNER JOIN booking b ON g.BOOKING_ID = b.BOOKING_ID
        LEFT JOIN branches fb ON b.FROM_BRANCH_ID = fb.BRANCH_ID
        LEFT JOIN branches tb ON b.TO_BRANCH_ID = tb.BRANCH_ID
        LEFT JOIN gdm_mapping d ON g.GDM_ID = d.GDM_ID
        WHERE g.GDM_ID = ?
    ");
    $stmt->bind_param("i", $gdmId);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookingData = [];
    while ($row = $result->fetch_assoc()) {
        $bookingData[] = $row;
    
        // Set driver info and branch names only once (from first row)
        if (empty($driverData)) {
            $FromBranchName = $row['FROM_BRANCH_NAME'];
            $toBranchName = $row['TO_BRANCH_NAME'];
            $driverData = [
                'DRIVER_NAME' => $row['DRIVER_NAME'],
                'DRIVER_NUMBER' => $row['DRIVER_NUMBER'],
                'VEHICLE_NUMBER' => $row['VEHICLE_NUMBER'],
                'VEHICLE_NAME' => $row['VEHICLE_NAME'],
            ];
        }
    }
}    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./css/table-filter.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php'; ?>
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="text-center mb-5">GDM DETAILS</h2>
                                <div class="table-responsive filterable mb-5">
                                    <table class="table table-striped tableFixHead" id="booking-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>LR Number</th>
                                                <th>Manual LR Number</th>
                                                <th>Payment Type</th>
                                                <th>Payment Method</th>
                                                <th>Customer Invoice Number</th>
                                                <th>Customer Invoice Value</th>
                                                <th>From Mobile</th>
                                                <th>From Name</th>
                                                <th>To Name</th>
                                                <th>To Mobile</th>
                                                <th>From Branch</th>
                                                <th>To Branch</th>
                                                <th>Items</th>
                                                <th>Dcc</th>
                                                <th>Transport Type</th>
                                                <th>Loading</th>
                                                <th>Unloading</th>
                                                <th>Freight</th>
                                                <th>LR Amount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalSum = 0;
                                            if (!empty($bookingData)) {
                                                foreach ($bookingData as $booking) {
                                                    $totalSum += (float)$booking['TOTAL_AMOUNT'];
                                            ?>
                                                    <tr>
                                                        <td><?= $booking['BOOKING_DATETIME'] ?></td>
                                                        <td><?= $booking['LR_NUMBER'] ?></td>
                                                        <td><?= $booking['MANUAL_LR_NUMBER'] ?></td>
                                                        <td><?= $booking['PAYMENT_TYPE'] ?></td>
                                                        <td><?= $booking['PAYMENT_METHOD'] ?></td>
                                                        <td><?= $booking['CUSTOMER_INVOICE_NUMBER'] ?></td>
                                                        <td><?= $booking['CUSTOMER_INVOICE_VALUE'] ?></td>
                                                        <td><?= $booking['FROM_MOBILE'] ?></td>
                                                        <td><?= $booking['FROM_NAME'] ?></td>
                                                        <td><?= $booking['TO_NAME'] ?></td>
                                                        <td><?= $booking['TO_MOBILE'] ?></td>
                                                        <td><?= $FromBranchName ?></td>
                                                        <td><?= $toBranchName ?></td>
                                                        <td>
                                                            <?php
                                                            $items = json_decode($booking['ITEMS'], true);
                                                            if (!empty($items)) {
                                                                foreach ($items as $index => $item) {
                                                                    echo "<strong>Item " . ($index + 1) . ":</strong> ";
                                                                    echo "Particular: " . htmlspecialchars($item['particular']) . ", ";
                                                                    echo "UOM: " . htmlspecialchars($item['uom']) . ", ";
                                                                    echo "Qty: " . htmlspecialchars($item['qty']) . ", ";
                                                                    echo "Rate: " . htmlspecialchars($item['rate']) . ", ";
                                                                    echo "Weight: " . htmlspecialchars($item['weight']) . "<br>";
                                                                }
                                                            } else {
                                                                echo "No items found";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?= $booking['DCC'] ?></td>
                                                        <td><?= $booking['TRANSPORT_TYPE'] ?></td>
                                                        <td><?= $booking['LOADING'] ?></td>
                                                        <td><?= $booking['UNLOADING'] ?></td>
                                                        <td><?= $booking['FRIGHT'] ?></td>
                                                        <td><?= $booking['LR_AMOUNT'] ?></td>
                                                        <td><?= $booking['TOTAL_AMOUNT'] ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="20" style="text-align: right;"><strong>Grand Total:</strong></td>
                                                <td><strong><?= number_format($totalSum, 2) ?></strong></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <h2 class="text-center mb-5">DRIVER DETAILS</h2>

                                <!-- Driver Table -->
                                <div class="table-responsive filterable mt-5">
                                    <table class="table table-striped tableFixHead" id="driver-table">
                                        <thead>
                                            <tr>
                                                <th>Driver Name</th>
                                                <th>Mobile Number</th>
                                                <th>Vehicle Number</th>
                                                <th>Vehicle Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($driverData)) { ?>
                                                <tr>
                                                    <td><?= $driverData['DRIVER_NAME'] ?></td>
                                                    <td><?= $driverData['DRIVER_NUMBER'] ?></td>
                                                    <td><?= $driverData['VEHICLE_NUMBER'] ?></td>
                                                    <td><?= $driverData['VEHICLE_NAME'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- card-body -->
                        </div> <!-- card -->
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <!-- JS & Plugins -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="./js/ddtf.js"></script>
    <script>
        $(document).ready(function() {
            $("#booking-table, #driver-table").ddTableFilter();
            $('select').select2();
        });
    </script>
</body>

</html>