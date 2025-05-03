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
            g.GDM_NUMBER,
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


                                <div class="d-flex justify-content-center my-4">
                                    <div class="card shadow rounded-3 border-0" style="width: 350px;">
                                        <div class="card-header fw-bold bg-light text-center">
                                            Driver Details
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-2">
                                                <div class="col-5 text-muted">Driver Name</div>
                                                <div class="col-2 text-muted">:</div>
                                                <div class="col-5 fw-bold"><?= $driverData['DRIVER_NAME'] ?? '' ?></div>

                                                <div class="col-5 text-muted">Mobile Number</div>
                                                <div class="col-2 text-muted">:</div>
                                                <div class="col-5 fw-bold"><?= $driverData['DRIVER_NUMBER'] ?? '' ?></div>

                                                <div class="col-5 text-muted">Vehicle Number</div>
                                                <div class="col-2 text-muted">:</div>
                                                <div class="col-5 fw-bold"><?= $driverData['VEHICLE_NUMBER'] ?? '' ?></div>

                                                <div class="col-5 text-muted">Vehicle Name</div>
                                                <div class="col-2 text-muted">:</div>
                                                <div class="col-5 fw-bold"><?= $driverData['VEHICLE_NAME'] ?? '' ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="table-responsive filterable mb-5">
                                    <table class="table table-striped tableFixHead" id="booking-table">
                                        <thead>
                                            <tr class="text-center">
                                                <th>GDM Number</th>
                                                <th>LR Number</th>
                                                <th>Manual LR Number</th>
                                                <th>Date</th>
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
                                                <th>Transport Type</th>
                                                <th>Dcc</th>
                                                <th>Loading</th>
                                                <th>Unloading</th>
                                                <th>Freight</th>
                                                <th>LR Amount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalDCC = 0;
                                            $totalLoading = 0;
                                            $totalUnloading = 0;
                                            $totalFright = 0;
                                            $totalLrAmount = 0;
                                            $totalSum = 0;

                                            if (!empty($bookingData)) {
                                                foreach ($bookingData as $booking) {
                                                    $totalDCC += (float)$booking['DCC'];
                                                    $totalLoading += (float)$booking['LOADING'];
                                                    $totalUnloading += (float)$booking['UNLOADING'];
                                                    $totalFright += (float)$booking['FRIGHT'];
                                                    $totalLrAmount += (float)$booking['LR_AMOUNT'];
                                                    $totalSum += (float)$booking['TOTAL_AMOUNT'];
                                            ?>
                                                    <tr class="text-center">
                                                        <td><?= $booking['GDM_NUMBER'] ?></td>
                                                        <td><?= $booking['LR_NUMBER'] ?></td>
                                                        <td><?= $booking['MANUAL_LR_NUMBER'] ?></td>
                                                        <td><?= $booking['BOOKING_DATETIME'] ?></td>
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
                                                        <td><?= $booking['TRANSPORT_TYPE'] ?></td>
                                                        <td><?= $booking['DCC'] ?></td>
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
                                                <td colspan="15" style="text-align: right;"><strong>Grand Totals:</strong></td>
                                                <td><strong><?= number_format($totalDCC, 2) ?></strong></td>
                                                <td></td>
                                                <td><strong><?= number_format($totalLoading, 2) ?></strong></td>
                                                <td><strong><?= number_format($totalUnloading, 2) ?></strong></td>
                                                <td><strong><?= number_format($totalFright, 2) ?></strong></td>
                                                <td><strong><?= number_format($totalLrAmount, 2) ?></strong></td>
                                                <td><strong><?= number_format($totalSum, 2) ?></strong></td>
                                            </tr>
                                        </tbody>


                                    </table>
                                </div>

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

    </script>
</body>

</html>