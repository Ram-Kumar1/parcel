<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/table-filter.css">

<style>

</style>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <?php

        date_default_timezone_set('Asia/Kolkata');
        $date_1 = date('d-m-Y H:i');
        $date = date('Y-m-d', strtotime($date_1));

        include 'header.php';




        $userName = $_SESSION['userName'] ?? 'GUEST';
        $branchName = $_SESSION['admin'] ?? 'GUEST';

        // Start base SQL query
        $sql = "SELECT BD.BOOKING_ID, BD.BOOKING_DATETIME, BD.FROM_NAME, BD.LR_NUMBER, BD.FROM_BRANCH_ID, BD.TO_BRANCH_ID,
            BD.PAYMENT_TYPE, BD.ITEMS, BD.TOTAL_AMOUNT FROM booking BD  WHERE IS_DELETE = 0";



        if (strtolower($userName) !== 'admin') {
            // Escape user input to prevent SQL injection
            $safeBranchName = mysqli_real_escape_string($conn, $branchName);
            $whereClauses[] = "BD.FROM_PLACE = '$safeBranchName'";
        }

        // Append WHERE clause if needed
        if (!empty($whereClauses)) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " ORDER BY BD.BOOKING_ID DESC";
        ?>


        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-validation">
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1 text-center">
                                                <h2 class="m-t-p5 mb-0">BOOKING LIST</h2>
                                            </div>

                                            <button type="button" class="btn btn-success btn-sm pull-right mb-3"
                                                style="margin-top: 0.75em;" onclick="window.location.href='newBooking.php';">
                                                <i class="material-icons">add</i>
                                            </button>
                                        </div>
                                    </div>
                                    <br>
                                    <?php
                                    if (isset($conn) && $result = mysqli_query($conn, $sql)) {
                                        if (mysqli_num_rows($result) > 0) {
                                    ?>
                                            <div class="panel-body">
                                                <div class="">
                                                    <div class="table-responsive filterable max-30">
                                                        <table class="table tableFixHead table-striped table-hover" id="data-table">
                                                            <thead>
                                                                <tr class="filters" style="color:#0c1211;">
                                                                    <th style="color:#0c1211">S. NO</th>
                                                                    <th style="color:#0c1211; text-align: center;">LR&nbsp;No</th>
                                                                    <th style="color:#0c1211">Customer</th>
                                                                    <th style="color:#0c1211">From</th>
                                                                    <th style="color:#0c1211">To</th>
                                                                    <th class="max-30" style="color:#0c1211">Booking&nbsp;Date</th>
                                                                    <th style="color:#0c1211">Payment&nbsp;Type</th>
                                                                    <th style="color:#0c1211; text-align: center;">Items</th>
                                                                    <th style="color:#0c1211; text-align: center;">Total&nbsp;Amount</th>
                                                                    <th style="color:#0c1211; text-align: center;">View&nbsp;PDF</th>
                                                                    <th style="color:#0c1211; text-align: center;">Edit</th>
                                                                    <th style="color:#0c1211; text-align: center;">Delete</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $i = 1;
                                                                while ($row = mysqli_fetch_array($result)) {

                                                                ?>

                                                                    <tr class="invoice-id- <?php echo $row['BOOKING_ID']; ?>">
                                                                        <td style="color:#0c1211;">
                                                                            <?php echo $i; ?>
                                                                        </td>
                                                                        <td class="max-30" style="color:#0c1211">
                                                                            <a data-toggle="modal" class="booking-id"
                                                                                id="booking-id-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                                <?php echo $row['LR_NUMBER'] ?? 'NO LR NUMBER'; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td style="color:#0c1211"><?php echo $row['FROM_NAME']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['FROM_BRANCH_ID']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['TO_BRANCH_ID']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['BOOKING_DATETIME']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['PAYMENT_TYPE']; ?></td>
                                                                        <td style="color:#0c1211">
                                                                            <?php
                                                                            $items = json_decode($row['ITEMS'], true); // decode JSON
                                                                            if (!empty($items)) {
                                                                                foreach ($items as $item) {
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

                                                                        <td style="color:#0c1211"><?php echo $row['TOTAL_AMOUNT']; ?></td>

                                                                        <td style="color:#0c1211; text-align: center;">
                                                                            <a class="a-view-icon" onclick="createPdf(<?php echo $row['BOOKING_ID']; ?>)">
                                                                                <i class="material-icons" style="cursor:pointer;">remove_red_eye</i>
                                                                            </a>
                                                                        </td>
                                                                        <td style="color:#0c1211; text-align: center; ">
                                                                            <a class="a-edit-icon" style="cursor:pointer;"
                                                                                data-id="<?php echo $row['BOOKING_ID']; ?>"
                                                                                onclick="editBooking(<?php echo $row['BOOKING_ID']; ?>)">
                                                                                <i class="fa fa-pencil font-x-large" aria-hidden="true"></i>
                                                                            </a>

                                                                        </td>
                                                                        <td>
                                                                            <a class="a-delete-icon" style="cursor:pointer;"
                                                                                data-id="<?php echo $row['BOOKING_ID']; ?>"
                                                                                onclick="deleteBooking(this)">
                                                                                <i class="fa fa-trash-o font-x-large" aria-hidden="true"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                    ++$i;
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                <?php
                                                mysqli_free_result($result);
                                            } else {
                                                echo '<div class="alert alert-info" style="margin: 1em;">';
                                                echo '    <strong>No record found 😥</strong>';
                                                echo '</div>';
                                            }
                                        }
                                                ?>
                                                <br><br>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal -->
                                            <!-- <div class="modal fade" id="cancelReasonModal" tabindex="-1" role="dialog" aria-labelledby="cancelReasonModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Cancel Booking</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="cancel-reason">Reason for Cancel:</label>
                                                            <select id="cancel-reason" class="form-control" onchange="handleReasonChange(this)">
                                                                <option value="">-- Select Reason --</option>
                                                                <option value="Customer Cancelled">Customer Cancelled</option>
                                                                <option value="Booking Mistake">Booking Mistake</option>
                                                                <option value="Payment Issue">Payment Issue</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                            <div id="other-reason-box" class="mt-2" style="display:none;">
                                                                <input type="text" id="other-reason" class="form-control" placeholder="Enter other reason">
                                                            </div>
                                                            <div id="" class="mt-2">
                                                                <input type="text" id="remark" class="form-control" placeholder="Enter Remark">
                                                            </div>
                                                            <input type="hidden" id="cancel-booking-id" value="<?php echo $row['BOOKING_ID']; ?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" onclick="deleteBooking()">Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trigger the modal with a button -->
    <div style="display: none;">
        <button type="button" class="btn btn-info btn-lg" id="hsn-btn" data-toggle="modal"
            data-target="#hsn-model">Open Modal
        </button>
        <input type="text" id="invoiceIdToCreate" />
    </div>
    <!-- Modal -->
    <div id="details-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Booking Details</h4>

                    <button type="button" class="close text-danger" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">LR Number</label>
                                <input type="text" class="form-control" id="lr-number" name="lr-number" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">Status </label>
                                <input type="text" class="form-control" id="invoice-status" name="invoice-status"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <input type="text" class="form-control" id="customer" name="customer" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Delivery To</label>
                                <input type="text" class="form-control" id="delivery-to" name="delivery-to"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Delivery Mobile</label>
                                <input type="text" class="form-control" id="delivery-mobile" name="delivery-mobile"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From</label>
                                <input type="text" class="form-control" id="from" name="from" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From Mobile</label>
                                <input type="text" class="form-control" id="from-address" name="from-address"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To</label>
                                <input type="text" class="form-control" id="to" name="to" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To Mobile</label>
                                <input type="text" class="form-control" id="to-address" name="to-address" readonly />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="quantity-details">Quantity Details</label>
                                <textarea class="form-control" id="quantity-details" rows="3" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Quantity</label>
                                <input type="text" class="form-control" id="quantity" name="quantity" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Quantity Description</label>
                                <input type="text" class="form-control" id="quantity-desc" name="quantity-desc"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Transportation Amount</label>
                                <input type="number" class="form-control" id="transportation-amount"
                                    name="transportation-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Loading Amount</label>
                                <input type="number" class="form-control" id="loading-amount"
                                    name="loading-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Additional Amount</label>
                                <input type="number" class="form-control" id="additional-amount"
                                    name="additional-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Total Amount</label>
                                <input type="number" class="form-control" id="total-amount"
                                    name="total-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Goods Value</label>
                                <input type="text" class="form-control" id="goods-value"
                                    name="goods-value" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Delivery Type</label>
                                <input type="text" class="form-control" id="notes"
                                    name="notes" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--<button type="button" class="btn btn-success" onclick="createPdf()">Create PDF</button>-->
                    <button type="button" class="btn btn-primary" data-dismiss="modal">&times; Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </section>
    <!-- Trigger the modal with a button -->
    <div style="display: none;">
        <button type="button" class="btn btn-info btn-lg" id="hsn-btn" data-toggle="modal"
            data-target="#hsn-model">Open Modal
        </button>
        <input type="text" id="invoiceIdToCreate" />
    </div>
    <!-- Modal -->
    <div id="details-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Booking Details</h4>

                    <button type="button" class="close text-danger" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">Invoice No</label>
                                <input type="text" class="form-control" id="invoice-no" name="invoice-no" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">LR Number</label>
                                <input type="text" class="form-control" id="lr-number" name="lr-number" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">Status </label>
                                <input type="text" class="form-control" id="invoice-status" name="invoice-status"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <input type="text" class="form-control" id="customer" name="customer" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Delivery To</label>
                                <input type="text" class="form-control" id="delivery-to" name="delivery-to"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Delivery Mobile</label>
                                <input type="text" class="form-control" id="delivery-mobile" name="delivery-mobile"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From</label>
                                <input type="text" class="form-control" id="from" name="from" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From Mobile</label>
                                <input type="text" class="form-control" id="from-address" name="from-address"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To</label>
                                <input type="text" class="form-control" id="to" name="to" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To Mobile</label>
                                <input type="text" class="form-control" id="to-address" name="to-address" readonly />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="quantity-details">Quantity Details</label>
                                <textarea class="form-control" id="quantity-details" rows="3" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Quantity</label>
                                <input type="text" class="form-control" id="quantity" name="quantity" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Quantity Description</label>
                                <input type="text" class="form-control" id="quantity-desc" name="quantity-desc"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Transportation Amount</label>
                                <input type="number" class="form-control" id="transportation-amount"
                                    name="transportation-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Loading Amount</label>
                                <input type="number" class="form-control" id="loading-amount"
                                    name="loading-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Additional Amount</label>
                                <input type="number" class="form-control" id="additional-amount"
                                    name="additional-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Total Amount</label>
                                <input type="number" class="form-control" id="total-amount"
                                    name="total-amount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Goods Value</label>
                                <input type="text" class="form-control" id="goods-value"
                                    name="goods-value" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Delivery Type</label>
                                <input type="text" class="form-control" id="notes"
                                    name="notes" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--                            <button type="button" class="btn btn-success" onclick="createPdf()">Create PDF</button>-->
                    <button type="button" class="btn btn-primary" data-dismiss="modal">&times; Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    </section>
    </div>
    </div>
    </div>
    </div>
    </div>

    </div> <!-- #/ container -->
    <!--**********************************
            Content body end
        ***********************************-->

    <?php include 'footer.php' ?>

</body>


<!-- Select2 Fileter -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Table Filter -->
<script src="./js/ddtf.js"></script>
<!-- Prevent Number Scrolling -->
<script src="./js/chits/numberInputPreventScroll.js"></script>
<script>
    // var createPdf = function(invoiceId) {
    function createPdf(invoiceId) {
        window.location.href = 'createPPF2.php?lr_Id=' + invoiceId;
    };

    // let editBooking = function(invoiceId) {
    function editBooking(invoiceId) {
        window.location.href = 'editBooking.php?bookingId=' + invoiceId;
    }



    // let getBookingStatus = function(statusCode) {
    function getBookingStatus(statusCode) {
        if (statusCode == 0) {
            return "Booked/Ready To Ship";
        } else if (statusCode == 1) {
            return "Package Reached To CBE Hub";
        } else if (statusCode == 2) {
            return "Package Reached To Nearest Destination Hub";
        } else if (statusCode == 3) {
            return "Delivered";
        }
    };

   

    $('.booking-id').click(function() {
    var id = this.id;
    var splitid = id.split('-');
    var bookingId = splitid[2];

    // AJAX request
    $.ajax({
        url: 'bookingDataOperations.php',
        type: 'post',
        data: {
            forBookingList: 1,
            bookingId: bookingId
        },
        success: function(response) {
            console.log(response);
            let res = JSON.parse(response);

            // Now fill the modal fields
            $('#lr-number').val(res['LR_NUMBER']);
            // $('#invoice-no').val(res['INVOICE_NUMBER']);
            $('#invoice-status').val(getBookingStatus(res['BOOKING_STATUS']));
            $('#customer').val(res['BOOKING_DATETIME']);
            $('#mobile').val(res['MANUAL_LR_NUMBER']);
            $('#delivery-to').val(res['CUSTOMER_INVOICE_NUMBER']);
            $('#delivery-mobile').val(res['CUSTOMER_INVOICE_VALUE']);
            $('#from').val(res['FROM_NAME']);
            $('#from-address').val(res['FROM_MOBILE']);
            $('#to').val(res['TO_NAME']);
            $('#to-address').val(res['TO_MOBILE']);
            $("#quantity-details").val(getQuantityDetails(res['ITEMS']));
            $('#quantity').val(res['TRANSPORT_TYPE']);
            $('#quantity-desc').val(res['FRIGHT']);
            $('#transportation-amount').val(res['PAYMENT_TYPE']);
            $('#loading-amount').val(res['PAYMENT_METHOD']);
            $('#additional-amount').val(res['LR_AMOUNT']);
            $('#total-amount').val(res['TOTAL_AMOUNT']);
            $('#goods-value').val(res['LOADING']);
            $('#notes').val(res['UNLOADING']);

            // Display Modal
            $('#details-model').modal('show');
        }
    });
});


    function getQuantityDetails(itemsJson) {
        let details = '';

        try {
            let items = JSON.parse(itemsJson);
            if (Array.isArray(items)) {
                items.forEach(function(item) {
                    details += 'Particular: ' + (item.particular || '') + ', ';
                    details += 'UOM: ' + (item.uom || '') + ', ';
                    details += 'Qty: ' + (item.qty || '') + ', ';
                    details += 'Rate: ' + (item.rate || '') + ', ';
                    details += 'Weight: ' + (item.weight || '') + '\n';
                });
            } else {
                details = 'No items found.';
            }
        } catch (e) {
            console.error('Invalid JSON in ITEMS:', e);
            details = 'Invalid item details.';
        }

        return details;
    }


    // var updateDetails = function(id) {
    function updateDetails(id) {
        var conform = confirm("Sure to create?");
        if (!conform) {
            return;
        } else {
            $("#invoiceIdToCreate").val('');
            $("#invoiceIdToCreate").val(id);
            $("#hsn-btn").click();
        }

    };

    // function openCancelPopup(bookingId) {
    //     document.getElementById('cancel-booking-id').value = bookingId;
    //     document.getElementById('cancel-reason').value = '';
    //     document.getElementById('other-reason-box').style.display = 'none';
    //     $('#cancelReasonModal').modal('show');
    // }

    // function handleReasonChange(select) {
    //     const otherBox = document.getElementById('other-reason-box');
    //     if (select.value === 'Other') {
    //         otherBox.style.display = 'block';
    //     } else {
    //         otherBox.style.display = 'none';
    //     }
    // }


    // function deleteBooking() {

    //     var conform = confirm("Sure to delete?");
    //     var cancelReason = document.getElementById('cancel-reason').value;
    //     var cancelBookingId = document.getElementById('cancel-booking-id').value;
    //     var remark = document.getElementById('remark').value;
    //     console.log(cancelReason, remark, cancelBookingId);
    //     if (cancelReason == '') {
    //         alert('Please select a reason for cancellation');
    //         return;
    //     }

    //     $.ajax({
    //         type: 'post',
    //         url: 'bookingDataOperations.php',
    //         data: {
    //             isDeleteBooking: 1,
    //             cancelReason: cancelReason,
    //             remark: remark,
    //             cancelBookingId: cancelBookingId

    //         },
    //         success: function() {
    //             alert('✔️ Deleted Successfully');
    //             window.location.reload();
    //         }
    //     });


    // };

    function deleteBooking(element) {
        let cnf = confirm("⚠️ Sure to delete?");
        if (!cnf) return;

        let bookingId = element.getAttribute('data-id');

        $.ajax({
            type: 'POST',
            url: 'bookingDataOperations.php',
            data: {
                isDeleteBooking: 1,
                bookingId: bookingId
            },
            success: function(response) {
                if (response.toString().startsWith("Update Successful")) {
                    alert('✔️ Booking Deleted Successfully!');
                    window.location.reload();
                } else {
                    alert('❌ Error deleting booking: ' + response);
                }
            }
        });
    }



    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>