<?php
include "dbConn.php";

if (!isset($_GET['bookingId'])) {
    die("Invalid request");
}
$bookingId = $_GET['bookingId'];

$getBooking = "SELECT * FROM booking WHERE BOOKING_ID = ?";
$stmt = $conn->prepare($getBooking);
$stmt->bind_param("s", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("No booking found.");
}
$row = $result->fetch_assoc();
$items = json_decode($row['ITEMS'], true);
$stmt->close();

$getRoute = <<<SQL
SELECT 
  b.BRANCH_ID,
  b.ROUTE_NAME,
  b.STATE   AS STATE_ID,
  b.CITY    AS CITY_ID,
  s.STATE_NAME,
  c.CITY_NAME
FROM branches b
JOIN state  s ON b.STATE = s.STATE_ID
JOIN cities c ON b.CITY  = c.CITY_ID
WHERE b.BRANCH_ID = ?
SQL;
$stmt = $conn->prepare($getRoute);
$stmt->bind_param("i", $row['TO_BRANCH_ID']);
$stmt->execute();
$result = $stmt->get_result();
$branchData = $result->fetch_assoc();
$stmt->close();

// extract for ease of use
$branchID   = $branchData['BRANCH_ID'] ?? '';
$routeName  = $branchData['ROUTE_NAME'] ?? '';
$State      = $branchData['STATE_ID'] ?? '';
$state      = $branchData['STATE_NAME'] ?? '';
$City       = $branchData['CITY_ID'] ?? '';
$city       = $branchData['CITY_NAME'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<style>
    hr {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .m-t-3 {
        margin-top: 0.5em;
    }

    .w-100 {
        width: 100%
    }

    #from_customer,
    #to_customer {
        accent-color: black;
        /* For modern browsers */
        width: 18px;
        height: 18px;
        border: 6px solid black;
        /* fallback for border */
        appearance: none;
        /* remove default checkbox style */
        -webkit-appearance: none;
        -moz-appearance: none;
        border-radius: 3px;
        cursor: pointer;
        position: relative;
    }
</style>

<script>
    let customerNameMobileMap = {};
    let tocustomerNameMobileMap = {};
    let customerMobileNameMap = {};
    let tocustomerMobileNameMap = {};

    let customerNameSelectChanged = function(select) {
        let selectVal = $(select).val();
        $("#fromMobile").val(customerNameMobileMap[selectVal]);
    };
    let tocustomerNameSelectChanged = function(select) {
        let selectVal = $(select).val();
        $("#toMobile").val(tocustomerNameMobileMap[selectVal]);
    };

    let mobileNumberChanged = function(select) {
        let selectVal = $(select).val();
        $("#fromName").select2({
            tags: true
        }).val(customerMobileNameMap[selectVal]).trigger('change');
    };

    let tomobileNumberChanged = function(select) {
        let selectVal = $(select).val();
        $("#toName").select2({
            tags: true
        }).val(tocustomerMobileNameMap[selectVal]).trigger('change');
    };
</script>

<body>

    <!--******************* Preloader start ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--******************* Preloader end ********************-->

    <!--********************************** Main wrapper start ***********************************-->
    <div id="main-wrapper">

        <?php include 'header.php'; ?>

        <!--********************************** Content body start ***********************************-->
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
                                                <h2 class="m-t-p5 mb-4">EDIT BOOKING</h2>
                                            </div>
                                            <button type="button" class="btn btn-info btn-md" onclick="window.location.href='viewBookingList.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="position-center">
                                            <input type="hidden" id="booking_id" value="<?php echo $row['BOOKING_ID']; ?>">
                                            <input type="hidden" name="fromBranchId" id="fromBranchId" value="<?php echo $row['FROM_BRANCH_ID']; ?>">

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Date & Time<span class="text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="dateTime"
                                                            value="<?php date_default_timezone_set('Asia/Kolkata');
                                                                    echo date('d-m-Y & h:i:s A'); ?>"
                                                            name="dateTime" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Bill No<span class="text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="billNo"
                                                            placeholder="Auto Generate" value="LR00" name="billNo" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Manual LR</label>
                                                        <input type="text" required class="form-control" id="manualLr"
                                                            placeholder="Enter LR Number" name="manualLr"
                                                            value="<?php echo $row['MANUAL_LR_NUMBER']; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="paymentType">Payment Type<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="paymentType" name="paymentType" required>
                                                            <option value="<?php echo $row['PAYMENT_TYPE']; ?>"><?php echo $row['PAYMENT_TYPE']; ?></option>
                                                            <option value="PAID">Paid</option>
                                                            <option value="TO_PAY">To Pay</option>
                                                            <option value="ACCOUNT">Account</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="paymentMethod">Payment Method<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                                                            <option value="<?php echo $row['PAYMENT_METHOD']; ?>"><?php echo $row['PAYMENT_METHOD']; ?></option>
                                                            <option value="ONLINE">Online</option>
                                                            <option value="CASH">Cash</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Cust Invoice Number<span class="text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="invoiceNumber"
                                                            placeholder="Enter Cust Invoice Number" name="invoiceNumber"
                                                            value="<?php echo $row['CUSTOMER_INVOICE_NUMBER']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Cust Invoice Value<span class="text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="custInvoiceValues"
                                                            placeholder="Enter Cust Invoice Value" name="custInvoiceValues"
                                                            value="<?php echo $row['CUSTOMER_INVOICE_VALUE']; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Mobile<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="fromMobile" name="fromMobile" onchange="mobileNumberChanged(this)">
                                                            <option value="<?php echo $row['FROM_MOBILE']; ?>"><?php echo $row['FROM_MOBILE']; ?></option>
                                                            <?php
                                                            $selectCust = "SELECT DISTINCT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY MOBILE";
                                                            $resCust = mysqli_query($conn, $selectCust);
                                                            while ($cd = mysqli_fetch_assoc($resCust)) {
                                                            ?>
                                                                <option value="<?php echo $cd['MOBILE']; ?>"><?php echo $cd['MOBILE']; ?></option>
                                                                <script>
                                                                    customerMobileNameMap['<?php echo $cd['MOBILE']; ?>'] = '<?php echo $cd['CUSTOMER_NAME']; ?>';
                                                                </script>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Name<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="fromName" name="fromName" onchange="customerNameSelectChanged(this)">
                                                            <option value="<?php echo $row['FROM_NAME']; ?>"><?php echo $row['FROM_NAME']; ?></option>
                                                            <?php
                                                            $selectCust = "SELECT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY CUSTOMER_NAME";
                                                            $resCust = mysqli_query($conn, $selectCust);
                                                            while ($cd = mysqli_fetch_assoc($resCust)) {
                                                            ?>
                                                                <option value="<?php echo $cd['CUSTOMER_NAME']; ?>"><?php echo $cd['CUSTOMER_NAME']; ?></option>
                                                                <script>
                                                                    customerNameMobileMap['<?php echo $cd['CUSTOMER_NAME']; ?>'] = '<?php echo $cd['MOBILE']; ?>';
                                                                </script>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Payment Customer</label><br>
                                                        <input type="checkbox" name="fromCustomer" id="fromCustomer"
                                                            <?php echo $row['IS_FROM_PAYMENT_CUSTOMER'] ? 'checked' : ''; ?>> &nbsp;
                                                        <label for="fromCustomer">From Customer?</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>To Mobile<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="toMobile" name="toMobile" onchange="tomobileNumberChanged(this)" required>
                                                            <option value="<?php echo $row['TO_MOBILE']; ?>"><?php echo $row['TO_MOBILE']; ?></option>
                                                            <?php
                                                            $resCust = mysqli_query($conn, $selectCust);
                                                            while ($cd = mysqli_fetch_assoc($resCust)) {
                                                            ?>
                                                                <option value="<?php echo $cd['MOBILE']; ?>"><?php echo $cd['MOBILE']; ?></option>
                                                                <script>
                                                                    tocustomerMobileNameMap['<?php echo $cd['MOBILE']; ?>'] = '<?php echo $cd['CUSTOMER_NAME']; ?>';
                                                                </script>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>To Name<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="toName" name="toName" onchange="tocustomerNameSelectChanged(this)" required>
                                                            <option value="<?php echo $row['TO_NAME']; ?>"><?php echo $row['TO_NAME']; ?></option>
                                                            <?php
                                                            $resCust = mysqli_query($conn, $selectCust);
                                                            while ($cd = mysqli_fetch_assoc($resCust)) {
                                                            ?>
                                                                <option value="<?php echo $cd['CUSTOMER_NAME']; ?>"><?php echo $cd['CUSTOMER_NAME']; ?></option>
                                                                <script>
                                                                    tocustomerNameMobileMap['<?php echo $cd['CUSTOMER_NAME']; ?>'] = '<?php echo $cd['MOBILE']; ?>';
                                                                </script>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Payment Customer</label><br>
                                                        <input type="checkbox" name="toCustomer" id="toCustomer"
                                                            <?php echo $row['IS_TO_PAYMENT_CUSTOMER'] ? 'checked' : ''; ?>> &nbsp;
                                                        <label for="toCustomer">To Customer?</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="toState">To State<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="toState" name="toState" required>
                                                            <option value="<?php echo $State; ?>"><?php echo htmlspecialchars($state); ?></option>
                                                            <option value="">-- SELECT TO STATE --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="district">District<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="district" name="district" required>
                                                            <option value="<?php echo $City; ?>"><?php echo htmlspecialchars($city); ?></option>
                                                            <option value="">-- SELECT DISTRICT --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="routeName">Route Name<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="routeName" name="routeName" required>
                                                            <option value="<?php echo $branchID; ?>"><?php echo htmlspecialchars($routeName); ?></option>
                                                            <option value="">-- SELECT ROUTE --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="d-flex justify-content-center mb-3">
                                                <button type="button" class="btn btn-info" onclick="addRow()">
                                                    <i class="fa fa-plus"></i> Add Row
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table" id="itemTable">
                                                    <thead class="thead-light">
                                                        <tr class="text-center">
                                                            <th>Particular</th>
                                                            <th>UOM</th>
                                                            <th>Qty</th>
                                                            <th>Rate</th>
                                                            <th>Weight</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>DCC</label>
                                                        <input type="text" required class="form-control" id="dcc"
                                                            name="dcc" value="<?php echo $row['DCC']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Transport Type<span class="text-danger">*</span></label>
                                                        <select class="form-control" id="transportType" name="transportType" required>
                                                            <option value="<?php echo $row['TRANSPORT_TYPE']; ?>"><?php echo $row['TRANSPORT_TYPE']; ?></option>
                                                            <option value="DD">DD</option>
                                                            <option value="LINE">LINE</option>
                                                            <option value="OFFICE">OFFICE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Total Freight<span class="text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="totalFright"
                                                            name="totalFright" value="<?php echo $row['FRIGHT']; ?>" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Loading</label>
                                                        <input type="text" required class="form-control" id="loading"
                                                            name="loading" value="<?php echo $row['LOADING']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Unloading</label>
                                                        <input type="text" required class="form-control" id="unloading"
                                                            name="unloading" value="<?php echo $row['UNLOADING']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>LR Amount</label>
                                                        <input type="number" required class="form-control" id="lrAmount"
                                                            name="lrAmount" value="<?php echo $row['LR_AMOUNT']; ?>" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <input type="number" required class="form-control" id="amount"
                                                            name="amount" value="<?php echo $row['TOTAL_AMOUNT']; ?>"
                                                            readonly style="background-color: yellow;" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-center mt-4">
                                                <button type="button" class="btn btn-primary btn-lg" onclick="updateData()">
                                                    Update Booking
                                                </button>
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

        <?php include 'footer.php'; ?>

    </div><!-- end main-wrapper -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="./js/ddtf.js"></script>
    <script src="./js/chits/numberInputPreventScroll.js"></script>
    <script>
        const existingItems = <?php echo json_encode($items); ?>;

        function addRow(particular = '', uom = '', qty = '', rate = '', weight = '') {
            const tableBody = document.querySelector('#itemTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-center">
                    <select class="form-control w-100 item-select" name="item[]">
                        <option value="">--Select Particular--</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="form-control w-100 uom">
                        <option value="">--Select UOM--</option>
                        <option value="Kg" ${uom === 'Kg' ? 'selected' : ''}>Kg</option>
                        <option value="Number" ${uom === 'Number' ? 'selected' : ''}>Number</option>
                    </select>
                </td>
                <td class="text-center"><input type="number" class="form-control w-100 qty" placeholder="Qty" value="${qty}"></td>
                <td class="text-center"><input type="number" class="form-control w-100 rate" placeholder="Rate" value="${rate}"></td>
                <td class="text-center"><input type="number" class="form-control w-100 weight" placeholder="Weight" value="${weight}"></td>
                <td class="text-center" style="font-size:20px">
                    <span onclick="clearRow(this)" style="cursor: pointer;"><i class="fa fa-eraser text-primary"></i></span>&nbsp;&nbsp;
                    <span onclick="deleteRow(this)" style="cursor: pointer;"><i class="fa fa-trash text-danger"></i></span>
                </td>
                <input type="hidden" class="freight" readonly>
            `;
            tableBody.appendChild(row);
            const itemSelect = row.querySelector('.item-select');
            loadItemOptions($(itemSelect), particular);
        }

        window.onload = function() {
            if (existingItems.length) {
                existingItems.forEach(item => {
                    addRow(item.particular, item.uom, item.qty, item.rate, item.weight);
                });
            }
        }

        function deleteRow(el) {
            $(el).closest('tr').remove();
            calculateTotalFreight();
        }

        function clearRow(el) {
            const row = $(el).closest('tr');
            row.find('input').val('');
            row.find('select').val('');
            row.find('.freight').val('0.00');
            row.find('.qty, .rate').trigger('input');
            calculateTotalFreight();
        }

        function loadItemOptions(targetSelect, selectedValue = '') {
            $.ajax({
                url: 'bookingDataOperations.php',
                type: 'GET',
                data: {
                    getItem: 1
                },
                success: function(response) {
                    targetSelect.each(function() {
                        const $this = $(this);
                        if ($this.children('option').length === 1) {
                            $this.append(response);
                        }
                        if (selectedValue) {
                            $this.val(selectedValue);
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            $.ajax({
                url: 'dataOperations.php',
                type: 'GET',
                data: {
                    getStates: true
                },
                success: function(response) {
                    $('#toState').append(response);
                }
            });

            $('#toState').on('change', function() {
                const stateId = $(this).val();
                $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
                if (stateId) {
                    $.ajax({
                        url: 'dataOperations.php',
                        type: 'POST',
                        data: {
                            getCities: true,
                            state_id: stateId
                        },
                        success: function(resp) {
                            $('#district').append(resp);
                        }
                    });
                }
            });

            $('#district').on('change', function() {
                const stateId = $('#toState').val();
                const districtId = $(this).val();
                $('#routeName').html('<option value="">-- SELECT ROUTE --</option>');
                if (stateId && districtId) {
                    $.ajax({
                        url: 'dataOperations.php',
                        type: 'POST',
                        data: {
                            getRouteName: 1,
                            stateId,
                            districtId
                        },
                        success: function(resp) {
                            $('#routeName').append(resp);
                        }
                    });
                }
            });

            $('select').select2();
            $("#data-table").ddTableFilter();
        });

        $(document).ready(function() {
            // Recalculate freight when rate or weight changes
            $(document).on('input', '.rate, .weight', function() {
                const row = $(this).closest('tr');
                const rate = parseFloat(row.find('.rate').val()) || 0;
                const weight = parseFloat(row.find('.weight').val()) || 0;
                const freight = rate * weight;

                row.find('.freight').val(freight.toFixed(2));
                calculateTotalFreight(); // update total freight and final amount
            });

            // Optional: Auto-fill weight based on qty when UOM is Kg
            $(document).on('input', '.qty', function() {
                const row = $(this).closest('tr');
                const qty = parseFloat(row.find('.qty').val()) || 0;
                const uom = row.find('.uom').val();

                if (uom === 'Kg') {
                    row.find('.weight').val(qty).trigger('input'); // trigger to update freight
                }
            });
        });

        // Calculate total freight and update amount
        function calculateTotalFreight() {
            let total = 0;
            $('.freight').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#totalFright').val(total.toFixed(2));
            calculateAmount();
        }

        // Calculate final amount
        function calculateAmount() {
            const totalFreight = parseFloat($('#totalFright').val()) || 0;
            const loading = parseFloat($('#loading').val()) || 0;
            const unloading = parseFloat($('#unloading').val()) || 0;
            const lrAmount = parseFloat($('#lrAmount').val()) || 0;

            const totalAmount = totalFreight + loading + unloading + lrAmount;
            $('#amount').val(totalAmount.toFixed(2));
        }

        // Update amount if loading/unloading/lrAmount changes
        $(document).on('input', '#loading, #unloading, #lrAmount', function() {
            calculateAmount();
        });


        function updateData() {
            const checks = [{
                    id: "paymentType",
                    msg: "Select Payment Type!"
                },
                {
                    id: "paymentMethod",
                    msg: "Select Payment Method!"
                },
                {
                    id: "invoiceNumber",
                    msg: "Enter Customer Invoice Number!"
                },
                {
                    id: "custInvoiceValues",
                    msg: "Enter Customer Invoice Value!"
                },
                {
                    id: "fromMobile",
                    msg: "Enter From Mobile Number!"
                },
                {
                    id: "fromName",
                    msg: "Enter From Name!"
                },
                {
                    id: "toMobile",
                    msg: "Enter To Mobile!"
                },
                {
                    id: "toName",
                    msg: "Enter To Name!"
                },
                {
                    id: "toState",
                    msg: "Select State!"
                },
                {
                    id: "district",
                    msg: "Select District!"
                },
                {
                    id: "routeName",
                    msg: "Select Route!"
                },
                {
                    id: "transportType",
                    msg: "Select Transport Type!"
                },
                {
                    id: "totalFright",
                    msg: "Total Freight is required!"
                }
            ];
            for (let f of checks) {
                let el = document.getElementById(f.id);
                if (!el || !el.value.trim()) {
                    alert("❌ " + f.msg);
                    if (el) el.focus();
                    return false;
                }
            }
            const rows = document.querySelectorAll('#itemTable tbody tr');
            if (!rows.length) {
                alert("❌ Please add at least one item!");
                return false;
            }
            const items = [];
            for (let [i, row] of rows.entries()) {
                const part = row.querySelector('.item-select')?.value;
                const uom = row.querySelector('.uom')?.value;
                const qty = row.querySelector('.qty')?.value;
                const rate = row.querySelector('.rate')?.value;
                const wgt = row.querySelector('.weight')?.value;
                if (!part || !uom || !qty || !rate || !wgt) {
                    alert(`❌ Please fill all fields in row ${i+1}!`);
                    return false;
                }
                items.push({
                    particular: part,
                    uom,
                    qty,
                    rate,
                    weight: wgt
                });
            }
            const payload = {
                updateNewBooking: 1,
                fromBranchId: document.getElementById('fromBranchId').value,
                booking_id: document.getElementById('booking_id').value,
                date_time: document.getElementById('dateTime').value,
                bill_no: document.getElementById('billNo').value,
                manual_lr: document.getElementById('manualLr').value,
                payment_type: document.getElementById('paymentType').value,
                payment_method: document.getElementById('paymentMethod').value,
                invoice_number: document.getElementById('invoiceNumber').value,
                cust_invoice_values: document.getElementById('custInvoiceValues').value,
                from_mobile: document.getElementById('fromMobile').value,
                from_name: document.getElementById('fromName').value,
                from_customer: document.getElementById('fromCustomer').checked ? 1 : 0,
                to_mobile: document.getElementById('toMobile').value,
                to_name: document.getElementById('toName').value,
                to_customer: document.getElementById('toCustomer').checked ? 1 : 0,
                to_state: document.getElementById('toState').value,
                district: document.getElementById('district').value,
                route_name: document.getElementById('routeName').value,
                dcc: document.getElementById('dcc').value,
                transportType: document.getElementById('transportType').value,
                totalFright: document.getElementById('totalFright').value,
                loading: document.getElementById('loading').value,
                unloading: document.getElementById('unloading').value,
                lrAmount: document.getElementById('lrAmount').value,
                amount: document.getElementById('amount').value,
                items: items
            };
            $.ajax({
                type: 'POST',
                url: 'bookingDataOperations.php',
                data: payload,
                dataType: 'text',
                success(resp) {
                    if (resp.startsWith("Update Successful")) {
                        alert("✔️ Booking updated!");
                        window.location.href = "viewBookingList.php";
                    } else {
                        alert("🚨 Error: " + resp);
                    }
                },
                error(xhr, status, err) {
                    alert("🚨 AJAX Error: " + err);
                }
            });
            return false;
        }
    </script>
</body>

</html>