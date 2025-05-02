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
        include 'header.php';

        date_default_timezone_set('Asia/Kolkata');
        $date_1 = date('d-m-Y H:i');
        $date = date('Y-m-d', strtotime($date_1));
        $userName = $_SESSION['userName'];
        $branchName = $_SESSION['admin'];
        $whereSql = " WHERE BD.BOOKING_STATUS = 0 AND BD.IS_DELETE = 0 ";
        if (strtolower($userName) !== 'admin') {
            $stmt = $conn->prepare("SELECT BRANCH_ID FROM branches WHERE ROUTE_NAME = ?");
            $stmt->bind_param("s", $branchName);
            $stmt->execute();
            $branchResult = $stmt->get_result();
            $branchId = 0;
            if ($row = $branchResult->fetch_assoc()) {
                $branchId = $row['BRANCH_ID'];
                $whereSql .= " AND BD.FROM_BRANCH_ID = '$branchId'";
            }
            $stmt->close();
        }
        $sql = "SELECT * FROM booking BD" . $whereSql . " ORDER BY BD.BOOKING_DATETIME";
        $finalResult = mysqli_query($conn, $sql);
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
                                                <h2 class="m-t-p5 mb-0"> CREATE GDM</h2> 
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm pull-right" style="margin-top: 1em;" onclick="window.location.reload();">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm pull-right" style="margin-top: 1em; margin-right: 1em;" onclick="printDiv()">
                                        <i class="material-icons" style="font-size: initial;">print</i>
                                    </button>
                                    <br>
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="mobileNumber">Mobile Number <span class="text-danger">*</span></label>
                                                <select class="form-control select2" id="mobileNumber" name="mobileNumber" required>
                                                    <option value="">-- SELECT MOBILE NUMBER --</option>
                                                    <?php
                                                    $getMobiles = "SELECT MOBILE,DRIVER_NAME FROM driver_details"; 
                                                    $mobileResult = $conn->query($getMobiles);
                                                    if ($mobileResult && $mobileResult->num_rows > 0) {
                                                        while ($row = $mobileResult->fetch_assoc()) {
                                                            $mobile = htmlspecialchars($row['MOBILE']);
                                                            $name = htmlspecialchars($row['DRIVER_NAME']);
                                                            echo "<option value='{$mobile}'>{$mobile}/{$name}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="driverName">Driver Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Driver Name" id="driverName" name="driverName">
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="vehicleNumber">Vehicle Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Vehicle Number" id="vehicleNumber" name="vehicleNumber">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="vehicleName">Vehicle Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Vehicle Name" id="vehicleName" name="vehicleName">
                                            </div>
                                        </div>

                                        <?php
                                        $getHub = "SELECT * FROM hub";
                                        $hubResult = $conn->query($getHub);
                                        ?>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="shipment-via">
                                                    Shipment Via <span class="mandatory-field">*</span>
                                                </label>
                                                <select class="form-control select2" id="shipment-via" name="shipment_via" required>
                                                    <option value="">-- SELECT SHIPMENT ROUTE --</option>
                                                    <option value="Direct">Direct</option>
                                                    <option value="Hub">Hub</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4" id="hub-select-wrapper" style="display: none;">
                                            <div class="form-group">
                                                <label for="hub-select">
                                                    HUB <span class="mandatory-field">*</span>
                                                </label>
                                                <select class="form-control select2" id="hub-select" name="hub_name">
                                                    <option value="">-- SELECT HUB --</option>
                                                    <?php
                                                    if ($hubResult && $hubResult->num_rows > 0) {
                                                        while ($row = $hubResult->fetch_assoc()) {
                                                            $hubName = htmlspecialchars($row['HUB_NAME']);
                                                            echo "<option value='{$hubName}'>{$hubName}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Select2 Assets -->

                                    <script>
                                        $(document).ready(function() {
                                            $('#shipment-via').on('change', function() {
                                                if ($(this).val() === 'Hub') {
                                                    $('#hub-select-wrapper').show();
                                                    $('#hub-select').select2('destroy').select2();
                                                } else {
                                                    $('#hub-select-wrapper').hide();
                                                    $('#hub-select').val('').trigger('change');
                                                }
                                            });
                                            if ($('#shipment-via').val() === 'Hub') {
                                                $('#hub-select-wrapper').show();
                                            } else {
                                                $('#hub-select-wrapper').hide();
                                            }
                                        });
                                    </script>

                                    <div id="print-div">
                                        <?php
                                        // Make sure $finalResult is properly defined before this point
                                        if (isset($finalResult) && $finalResult && mysqli_num_rows($finalResult) > 0) {
                                        ?>
                                            <div id="table-data" class="table-responsive filterable max-30">
                                                <button type="button" class="btn btn-info btn-xs pull-right mb-3" onclick="updateAllValues()">
                                                    Check All <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                                </button>
                                                <table id="data-table" class="table table-striped tableFixHead">
                                                    <thead>
                                                        <tr class="filters text-center" style="color:#0c1211;">
                                                            <th>#</th>
                                                            <th style="text-align: center;">LR Number</th>
                                                            <th>Customer</th>
                                                            <th>From</th>
                                                            <th>From Mobile</th>
                                                            <th>To</th>
                                                            <th>To Mobile</th>
                                                            <th>Date</th>
                                                            <th class='hideout-row'>ShipOut</th>
                                                            <th class='hideout-row'>Cancel</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        while ($row = mysqli_fetch_assoc($finalResult)) {
                                                        ?>
                                                            <tr class="text-center invoice-id-<?php echo $row['BOOKING_ID']; ?>">
                                                                <td><?php echo $i++; ?></td>
                                                                <td>
                                                                    <a data-toggle="modal" class="booking-id text-dark"
                                                                        id="booking-id-<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                        <?php echo $row['LR_NUMBER'] ?? 'NO LR NUMBER'; ?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($row['TO_NAME']); ?></td>
                                                                <td>
                                                                    <?php
                                                                    $getFromBranchId = "SELECT ROUTE_NAME FROM branches WHERE BRANCH_ID = ?";
                                                                    $stmt = $conn->prepare($getFromBranchId);
                                                                    $stmt->bind_param("i", $row['FROM_BRANCH_ID']);
                                                                    $stmt->execute();
                                                                    $result = $stmt->get_result();

                                                                    while ($routeName = $result->fetch_assoc()) {
                                                                        echo htmlspecialchars($routeName['ROUTE_NAME']);
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($row['FROM_MOBILE']); ?></td>
                                                                <td>
                                                                    <?php
                                                                    $getToBranchId = "SELECT ROUTE_NAME FROM branches WHERE BRANCH_ID = ?";
                                                                    $stmt = $conn->prepare($getToBranchId);
                                                                    $stmt->bind_param("i", $row['TO_BRANCH_ID']);
                                                                    $stmt->execute();
                                                                    $result = $stmt->get_result();
                                                                    while ($TorouteName = $result->fetch_assoc()) {
                                                                        echo htmlspecialchars($TorouteName['ROUTE_NAME']);
                                                                    } ?>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($row['TO_MOBILE']); ?></td>
                                                                <td><?php echo htmlspecialchars($row['BOOKING_DATETIME']); ?></td>
                                                                <td>
                                                                    <a data-toggle="modal" onclick="createGDM(<?php echo (int)$row['BOOKING_ID']; ?>)"
                                                                        id="move-booking-id-<?php echo $row['BOOKING_ID']; ?>"
                                                                        data-booking-id="<?php echo $row['BOOKING_ID']; ?>"
                                                                        data-to-place="<?php echo $row['TO_BRANCH_ID']; ?>" href="">
                                                                        <i class="fa fa-random text-success" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a data-toggle="modal" onclick="updateToShipOutCancel(<?php echo (int)$row['BOOKING_ID']; ?>)"
                                                                        id="cancel-booking-id-<?php echo $row['BOOKING_ID']; ?>"
                                                                        data-booking-id="<?php echo $row['BOOKING_ID']; ?>" href="">
                                                                        <i class="fa fa-times text-danger" aria-hidden="true"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <div class="alert alert-info" style="margin: 1em;">
                                                <strong>No record found 😥 to create ship outward</strong>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Trigger the modal with a button -->
                            <div style="display: none;">
                                <button type="button" class="btn btn-info btn-lg" id="hsn-btn" data-toggle="modal" data-target="#hsn-model">Open Modal
                                </button>
                                <input type="text" id="invoiceIdToCreate" />
                            </div>
                            <!-- Modal -->
                            <div id="details-model" class="modal fade" role="dialog">
                                <div class="modal-dialog modal-lg">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="text-center">Booking Details</h4>
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
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
    var createPdf = function(invoiceId) {
        window.location.href = 'createPPF2.php?invoiceId=' + invoiceId;
    };

    function getKeyByValue(object, value) {
        // return Object.keys(object).find(key => object[key] === value);
        var resArr = [];
        for (var key in object) {
            if (object.hasOwnProperty(key) && object[key] === value) {
                resArr.push(key);
            }
        }
        return resArr;
    }

    function updateToShipOutCancel(bookingId) {
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                revertShipOutward: 1,
                bookingId: bookingId
            },
            success: function(response) {
                console.log(response);
                if (response.toString().includes("Success")) {
                    alert('✔️ Success Cancel');
                    window.location.reload();

                    $('.invoice-id-' + bookingId).removeClass("move-success");
                }
            }
        });
    };

    let createGDM = function(bookingId) {
        let shipmentVia = $("#shipment-via").val();
        let hubSelect = $("#hub-select").val();
        let driverName = $("#driverName").val();
        let mobileNumber = $("#mobileNumber").val();
        let vehicleNumber = $("#vehicleNumber").val();
        let vehicleName = $("#vehicleName").val();
        if (shipmentVia === undefined || shipmentVia === null || shipmentVia === "") {
            alert("❌ Shipment is mandatory!");
            return false;
        }
        if (driverName === undefined || driverName === null || driverName === "") {
            alert("❌ Driver Name is mandatory!");
            return false;
        }
        if (mobileNumber === undefined || mobileNumber === null || mobileNumber.trim() === "") {
            alert("❌ Mobile Number is mandatory!");
            return false;
        }

        if (mobileNumber.length !== 10 || isNaN(mobileNumber)) {
            alert("❌ Mobile Number must be exactly 10 digits!");
            return false;
        }

        if (vehicleNumber === undefined || vehicleNumber === null || vehicleNumber === "") {
            alert("❌ Vehicle Number is mandatory!");
            return false;
        }
        if (vehicleName === undefined || vehicleName === null || vehicleName === "") {
            alert("❌ Vehicle Name is mandatory!");
            return false;
        }
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'post',
            data: {
                createGDM: 1,
                bookingId: bookingId,
                shipmentVia: shipmentVia,
                hubSelect: hubSelect,
                driverName: driverName,
                mobileNumber: mobileNumber,
                vehicleNumber: vehicleNumber,
                vehicleName: vehicleName
            },
            success: function(response) {
                console.log(response);
                if (response.toString().includes("Update Success")) {
                    alert('✔️ ShipOut Success');
                    window.location.reload();
                }
            }
        });
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
                // Add response in Modal body
                let res = JSON.parse(response);
                $('#lrNumber').val(res['LR_NUMBER']);
                $('#bookingDate').val(res['BOOKING_DATETIME']);
                $('#mobile').val(res['MANUAL_LR_NUMBER']);
                $('#delivery-to').val(res['CUSTOMER_INVOICE_NUMBER']);
                $('#delivery-mobile').val(res['CUSTOMER_INVOICE_VALUE']);
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
                $('#customerInvoiceValue').val(res['CUSTOMER_INVOICE_NUMBER']);
                $('#customerInvoiceNumber').val(res['CUSTOMER_INVOICE_VALUE']);

                // Display Modal
                $('#details-model').modal('show');
            }
        });
        $('#details-model').modal('show');
    });
    var updateDetails = function(id) {
        var conform = confirm("Sure to create?");
        if (!conform) {
            return;
        } else {
            $("#invoiceIdToCreate").val('');
            $("#invoiceIdToCreate").val(id);
            $("#hsn-btn").click();
        }
    };

    let printDiv = function() {
        $("#print-out-details").show();

        // Hide specific columns before printing
        $('td:first-child, th:first-child').hide(); // First column
        $('td:nth-child(7), th:nth-child(7)').hide();
        $('td:nth-child(8), th:nth-child(8)').hide();
        $('td:last-child, th:last-child').hide(); // Last column

        // Print contents
        let printContents = document.getElementById('print-div').innerHTML;
        let originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        // Restore original visibility
        $("#print-out-details").hide();
        $('td:first-child, th:first-child').show();
        $('td:nth-child(7), th:nth-child(7)').show();
        $('td:nth-child(8), th:nth-child(8)').show();
        $('td:last-child, th:last-child').show();

        // Re-initialize select2 if needed
        $('select[data-select2-id="5"]').select();

        // Reload the page to restore JS state
        window.location.reload();
    };

    $('#driver-name').select2({
        tags: true
    });
    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });

    //Get Driver Details
    $(document).ready(function() {
        $('#mobileNumber').change(function() {
            var mobile = $(this).val();
            if (mobile !== "") {
                $.ajax({
                    url: 'bookingDataOperations.php',
                    type: 'POST',
                    data: {
                        getDriverDetails: 1,
                        mobileNumber: mobile
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#driverName').val(response.driverName);
                            $('#vehicleNumber').val(response.vehicleNumber);
                            $('#vehicleName').val(response.vehicleName);
                            $('#vehicleLincense').val(response.vehicleLincense);
                        } else {
                            alert('Driver details not found.');
                        }
                    },
                    error: function() {
                        alert('Error fetching driver details.');
                    }
                });
            }
        });
    });


    $("#data-table").ddTableFilter();
    $('select').addClass('w3-select');
    $('select').select2();
</script>

</html>