<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="./css/table-filter.css">

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
        $sql = "SELECT * FROM booking BD  WHERE IS_DELETE = 0";

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
                                                                    <th class="max-30" style="color:#0c1211">Booking&nbsp;Date</th>
                                                                    <th style="color:#0c1211">From Branch</th>
                                                                    <th style="color:#0c1211">To Branch</th>
                                                                    <th style="color:#0c1211">Customer</th>
                                                                    <th style="color:#0c1211">Mobile</th>
                                                                    <th style="color:#0c1211">To Name</th>
                                                                    <th style="color:#0c1211">To Mobile</th>
                                                                    <th style="color:#0c1211">Payment&nbsp;Type</th>
                                                                    <th style="color:#0c1211">Payment&nbsp;Method</th>
                                                                    <th style="color:#0c1211">Loading</th>
                                                                    <th style="color:#0c1211">Unloading</th>
                                                                    <th style="color:#0c1211">Transport Type</th>
                                                                    <th style="color:#0c1211; text-align: center;">Items</th>
                                                                    <th style="color:#0c1211; text-align: center;">Fright</th>
                                                                    <th style="color:#0c1211; text-align: center;">LR Amount</th>
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
                                                                    $BranchId = $row['FROM_BRANCH_ID'];
                                                                    $toBranchId = $row['TO_BRANCH_ID'];

                                                                    $sql = "SELECT ROUTE_NAME FROM branches WHERE BRANCH_ID = ?";
                                                                    $stmt = $conn->prepare($sql);

                                                                    if ($stmt) {
                                                                        $stmt->bind_param("i", $BranchId);
                                                                        $stmt->execute();
                                                                        $resultRoute = $stmt->get_result();
                                                                        $routeName = '';
                                                                        if ($rows = $resultRoute->fetch_assoc()) {
                                                                            $routeName = $rows['ROUTE_NAME'];
                                                                        }
                                                                    }
                                                                    $sql = "SELECT ROUTE_NAME FROM branches WHERE BRANCH_ID = ?";
                                                                    $stmt = $conn->prepare($sql);

                                                                    if ($stmt) {
                                                                        $stmt->bind_param("i", $toBranchId);
                                                                        $stmt->execute();
                                                                        $resultRoute = $stmt->get_result();
                                                                        $torouteName = '';
                                                                        if ($rows = $resultRoute->fetch_assoc()) {
                                                                            $torouteName = $rows['ROUTE_NAME'];
                                                                        }
                                                                    }
                                                                ?>

                                                                    <tr class="invoice-id-<?php echo $row['BOOKING_ID']; ?>">
                                                                        <td style="color:#0c1211;">
                                                                            <?php echo $i; ?>
                                                                        </td>
                                                                        <td class="max-30" style="color:#0c1211">
                                                                            <a data-toggle="modal" class="booking-id"
                                                                                id="booking-id-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                                <?php echo $row['LR_NUMBER'] ?? 'NO LR NUMBER'; ?>
                                                                            </a>
                                                                        </td>
                                                                        <td style="color:#0c1211"><?php echo $row['BOOKING_DATETIME']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $routeName; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $torouteName; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['FROM_NAME']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['FROM_MOBILE']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['TO_NAME']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['TO_MOBILE']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['PAYMENT_TYPE']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['PAYMENT_METHOD']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['LOADING']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['UNLOADING']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['TRANSPORT_TYPE']; ?></td>
                                                                        <td style="color:#0c1211">
                                                                            <?php
                                                                            $items = json_decode($row['ITEMS'], true);
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
                                                                        <td style="color:#0c1211"><?php echo $row['FRIGHT']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['LR_AMOUNT']; ?></td>
                                                                        <td style="color:#0c1211"><?php echo $row['TOTAL_AMOUNT']; ?></td>

                                                                        <td style="color:#0c1211; text-align: center;">
                                                                            <a class="a-view-icon" onclick="createPdf(<?php echo $row['BOOKING_ID']; ?>)">
                                                                                <i class="material-icons" style="cursor:pointer;">remove_red_eye</i>
                                                                            </a>
                                                                        </td>
                                                                        <td style="color:#0c1211; text-align: center;">
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
                                <input type="text" class="form-control" id="lrNumber" name="lrNumber" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="invoice-no">Booking Date</label>
                                <input type="text" class="form-control" id="bookingDate" name="bookingDate" readonly />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From Name</label>
                                <input type="text" class="form-control" id="fromName" name="fromName" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">From Mobile</label>
                                <input type="text" class="form-control" id="fromMobile" name="fromMobile" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To Name</label>
                                <input type="text" class="form-control" id="toName" name="toName"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">To Mobile</label>
                                <input type="text" class="form-control" id="toMobile" name="toMobile" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Payment Type</label>
                                <input type="text" class="form-control" id="paymentType" name="paymentType"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Payment Method</label>
                                <input type="text" class="form-control" id="paymentMethod" name="paymentMethod" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">Transport Type</label>
                                <input type="text" class="form-control" id="TransportType" name="TransportType"
                                    readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="customer">DCC</label>
                                <input type="text" class="form-control" id="dcc" name="dcc" readonly />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Customer Invoice Number</label>
                                <input type="text" class="form-control" id="customerInvoiceNumber"
                                    name="customerInvoiceNumber" readonly />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Customer Invoice Value</label>
                                <input type="text" class="form-control" id="customerInvoiceValue"
                                    name="customerInvoiceValue" readonly />
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="quantity-details">Items</label>
                                <textarea class="form-control" id="items" name="items" rows="3" readonly></textarea>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Unloading Amount</label>
                                <input type="number" class="form-control" id="unloading"
                                    name="unloading" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Loading Amount</label>
                                <input type="number" class="form-control" id="loading"
                                    name="loading" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">LR Amount</label>
                                <input type="number" class="form-control" id="lrAmount"
                                    name="lrAmount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Total Amount</label>
                                <input type="number" class="form-control" id="totalAmount"
                                    name="totalAmount" readonly />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="total-amount">Frights</label>
                                <input type="text" class="form-control" id="fright"
                                    name="fright" readonly />
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
            type: 'POST',
            data: {
                forBookingList: 1,
                bookingId: bookingId
            },
            success: function(response) {
                console.log(response);

                let res = JSON.parse(response);

                // Set form fields
                $('#lrNumber').val(res['LR_NUMBER']);
                $('#bookingDate').val(res['BOOKING_DATETIME']);
                $('#mobile').val(res['FROM_MOBILE']);
                $('#delivery-to').val(res['TO_NAME']);
                $('#delivery-mobile').val(res['TO_MOBILE']);
                $('#fromName').val(res['FROM_NAME']);
                $('#fromMobile').val(res['FROM_MOBILE']);
                $('#toName').val(res['TO_NAME']);
                $('#toMobile').val(res['TO_MOBILE']);
                let rawItems = res['ITEMS'];
                let cleanedItems = rawItems.replace(/\\+/g, '').replace(/^"|"$/g, '');
                let parsedItems;
                try {
                    parsedItems = JSON.parse(cleanedItems);
                } catch (e) {
                    parsedItems = [];
                }
                let finalString = parsedItems
                    .filter(item => Object.keys(item).length > 0)
                    .map(item => {
                        return Object.entries(item).map(([key, value]) => `${key}:${value}`).join(',');
                    }).join('\n');
                $('#items').val(finalString);

                $('#transportType').val(res['TRANSPORT_TYPE']);
                $('#fright').val(res['FRIGHT']);
                $('#paymentType').val(res['PAYMENT_TYPE']);
                $('#paymentMethod').val(res['PAYMENT_METHOD']);
                $('#lrAmount').val(res['LR_AMOUNT']);
                $('#totalAmount').val(res['TOTAL_AMOUNT']);
                $('#loading').val(res['LOADING']);
                $('#unloading').val(res['UNLOADING']);
                $('#dcc').val(res['DCC']);
                $('#customerInvoiceNumber').val(res['CUSTOMER_INVOICE_NUMBER']);
                $('#customerInvoiceValue').val(res['CUSTOMER_INVOICE_VALUE']);

                // Show modal
                $('#details-model').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });





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