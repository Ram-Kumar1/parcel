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

        // Also set From Name if MOBILE was selected first
        if ($("#fromName").val() !== selectVal) {
            $("#fromName").select2({
                tags: true
            }).val(selectVal).trigger('change');
        }
    };

    let tocustomerNameSelectChanged = function(select) {
        let selectVal = $(select).val();
        $("#toMobile").val(tocustomerNameMobileMap[selectVal]);

        // Also set To Name if MOBILE was selected first
        if ($("#toName").val() !== selectVal) {
            $("#toName").select2({
                tags: true
            }).val(selectVal).trigger('change');
        }
    };

    let mobileNumberChanged = function(select) {
        let fromVal = $(select).val();
        let toVal = $("#toMobile").val();

        if (fromVal !== "" && fromVal === toVal) {
            alert("Please select a different FROM Mobile number. It cannot be the same as TO Mobile.");
            $(select).val("").trigger('change');
            $("#fromName").val("").trigger('change');
            return;
        }

        const name = customerMobileNameMap[fromVal];
        $("#fromName").select2({
            tags: true
        }).val(name).trigger('change');
    };

    let tomobileNumberChanged = function(select) {
        let toVal = $(select).val();
        let fromVal = $("#fromMobile").val();

        if (toVal !== "" && toVal === fromVal) {
            alert("Please select a different TO Mobile number. It cannot be the same as FROM Mobile.");
            $(select).val("").trigger('change');
            $("#toName").val("").trigger('change');
            return;
        }

        const name = tocustomerMobileNameMap[toVal];
        $("#toName").select2({
            tags: true
        }).val(name).trigger('change');
    };
</script>

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

        <?php include 'header.php';

        $userName = $_SESSION['userName'];
        echo  $branchName = $_SESSION['admin'];

        $getFromBranchId = "SELECT BRANCH_ID FROM branches WHERE ROUTE_NAME = ?";
        $stmt = $conn->prepare($getFromBranchId);
        $stmt->bind_param("s", $branchName);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $fromBranchId = $row['BRANCH_ID'];
        }
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
                                                <h2 class="m-t-p5 mb-4">NEW BOOKING</h2>
                                            </div>
                                            <button type="button" class="btn btn-info btn-md" onclick="window.location.href='viewBookingList.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="position-center">
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Date & Time<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="dateTime" value="<?php date_default_timezone_set('Asia/Kolkata');
                                                                                                                                echo date('d-m-Y & h:i:s A'); ?>" name="dateTime" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Bill No<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="billNo" placeholder="Auto Generate"
                                                            name="billNo" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Manual Lr<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="manualLr"
                                                            placeholder="Enter Lr Number" name="manualLr" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="paymentType">Payment Type<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="paymentType" name="paymentType" required>
                                                            <option value="">-- SELECT PAYMENT --</option>
                                                            <option value="PAID">Paid</option>
                                                            <option value="TO_PAY">To Pay</option>
                                                            <option value="ACCOUNT">Account</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="paymentMethod">Payment Method<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                                                            <option value="">-- SELECT PAYMENT --</option>
                                                            <option value="ONLINE">Online</option>
                                                            <option value="CASE">Cash</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Number<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="invoiceNumber"
                                                            placeholder="Enter Cust Invoice Number" name="invoiceNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Value<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="custInvoiceValues"
                                                            placeholder="Enter Cust Invoice Value" name="custInvoiceValues" />
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="fromMobile" name="fromMobile" onchange="mobileNumberChanged(this)">
                                                            <option value="">-- SELECT FROM MOBILE --</option>
                                                            <?php
                                                            $selectCity = "SELECT DISTINCT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['MOBILE'] ?>"><?php echo $row['MOBILE'] ?></option>
                                                                        <script>
                                                                            customerMobileNameMap[<?php echo "'" . $row['MOBILE'] . "'"; ?>] = <?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>
                                                                        </script>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Name<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="fromName" name="fromName" onchange="customerNameSelectChanged(this)">
                                                            <option value="">-- SELECT FROM NAME --</option>
                                                            <?php
                                                            $selectCity = "SELECT CUSTOMER_ID, TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['CUSTOMER_NAME'] ?>"><?php echo $row['CUSTOMER_NAME'] ?></option>
                                                                        <script>
                                                                            customerNameMobileMap[<?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>] = <?php echo "'" . $row['MOBILE'] . "'"; ?>
                                                                        </script>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="fromCustomer" id="fromCustomer"> &nbsp; <label for="paymentCustomer">From Customer?</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">To Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="toMobile" name="toMobile" onchange="tomobileNumberChanged(this)" required>
                                                            <option value="">-- SELECT TO MOBILE --</option>
                                                            <?php
                                                            $selectCity = "SELECT DISTINCT TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['MOBILE'] ?>"><?php echo $row['MOBILE'] ?></option>
                                                                        <script>
                                                                            tocustomerMobileNameMap[<?php echo "'" . $row['MOBILE'] . "'"; ?>] = <?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>
                                                                        </script>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">To Name<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="toName" name="toName" onchange="tocustomerNameSelectChanged(this)">
                                                            <option value="">-- SELECT TO NAME --</option>
                                                            <?php
                                                            $selectCity = "SELECT CUSTOMER_ID, TRIM(CUSTOMER_NAME) AS CUSTOMER_NAME, MOBILE FROM customer_details ORDER BY 2";
                                                            if ($result = mysqli_query($conn, $selectCity)) {
                                                                if (mysqli_num_rows($result) > 0) {
                                                                    while ($row = mysqli_fetch_array($result)) {
                                                            ?>
                                                                        <option value="<?php echo $row['CUSTOMER_NAME'] ?>"><?php echo $row['CUSTOMER_NAME'] ?></option>
                                                                        <script>
                                                                            tocustomerNameMobileMap[<?php echo "'" . $row['CUSTOMER_NAME'] . "'"; ?>] = <?php echo "'" . $row['MOBILE'] . "'"; ?>
                                                                        </script>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="toCustomer" id="toCustomer"> &nbsp; <label for="paymentCustomer">To Customer?</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="state">To State<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="toState" name="toState" required>
                                                            <option value="">-- SELECT TO STATE --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="district">District<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="district" name="district" required>
                                                            <option value="">-- SELECT DISTRICT --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="routeName">Route Name<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="routeName" name="routeName" required>
                                                            <option value="">-- SELECT ROUTE --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-center mb-3">
                                                <button class="btn btn-info" onclick="addRow()">
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
                                                        <label for="">DCC<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="dcc" placeholder="Enter Dcc"
                                                            name="dcc" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Transport Type<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="transportType" name="transportType" required>
                                                            <option value="">-- SELECT TRANSPORT TYPE --</option>
                                                            <option value="DD">DD</option>
                                                            <option value="LINE">LINE</option>
                                                            <option value="OFFICE">OFFICE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Total Fright<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="totalFright" name="totalFright" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Loading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="loading" placeholder="Enter Loading Amount"
                                                            name="loading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Unloading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="unloading" placeholder="Enter Unloading Amount"
                                                            name="unloading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">LR Amount<span class="mandatory-field text-danger"></span></label>
                                                        <input type="number" required class="form-control" id="lrAmount"
                                                            name="lrAmount" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Amount<span class="mandatory-field text-danger"></span></label>
                                                        <input type="number" required class="form-control" id="amount"
                                                            name="amount" readonly style="background-color: yellow;" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-success btn-lg" onclick="saveData()"><i class="fa fa-floppy-o"></i> &nbsp;Save</button>
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


    <?php include 'footer.php'; ?>

</body>
<!-- Select2 Fileter -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Table Filter -->
<script src="./js/ddtf.js"></script>
<!-- Prevent Number Scrolling -->
<script src="./js/chits/numberInputPreventScroll.js"></script>


<script type="text/javascript">
    //AddRow
    function addRow() {
        const tableBody = $('#itemTable tbody');
        const row = $(`
        <tr>
            <td class="text-center">
                <select class="form-control w-100 item-select" name="item[]">
                    <option value="">--Select Particular--</option>
                </select>
            </td>
            <td class="text-center">
                <select class="form-control w-100 uom">
                    <option value="">--Select UOM--</option>
                    <option value="Kg">Kg</option>
                    <option value="Number">Number</option>
                </select>
            </td>
            <td class="text-center"><input type="number" class="form-control w-100 qty" placeholder="Qty"></td>
            <td class="text-center"><input type="number" class="form-control w-100 rate" placeholder="Rate"></td>
            <td class="text-center"><input type="number" class="form-control w-100 weight" placeholder="Weight"></td>
            <td class="text-center" style="font-size:20px">
                <span class="clear-row" style="cursor: pointer;">
                    <i class="fa fa-eraser text-primary"></i>
                </span>&nbsp;&nbsp;
                <span class="delete-row" style="cursor: pointer;">
                    <i class="fa fa-trash text-danger"></i>
                </span>
            </td>
            <input type="hidden" class="form-control w-100 freight" readonly>
        </tr>
         `);
        tableBody.append(row);
        loadItemOptions(row.find('.item-select'));
    }
    // Delete entire row
    function deleteRow(el) {
        $(el).closest('tr').remove();
        calculateTotalFreight();
    }

    // Clear row inputs
    function clearRow(el) {
        const row = $(el).closest('tr');
        row.find('input').val('');
        row.find('select').val('');
        row.find('.freight').val('0.00');
        calculateTotalFreight();
    }

    // Get Items
    function loadItemOptions(targetSelect = $('.item-select')) {
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'GET',
            data: {
                getItem: 1
            },
            success: function(response) {
                targetSelect.each(function() {
                    if ($(this).children('option').length === 1) {
                        $(this).append(response);
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        loadItemOptions();

        // Delegate events for dynamically added buttons
        $(document).on('click', '.delete-row', function() {
            deleteRow(this);
        });

        $(document).on('click', '.clear-row', function() {
            clearRow(this);
        });
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

    //saveData
    function saveData() {
        // Validate required fields
        var fromBranchId = '<?php echo $fromBranchId; ?>';
        const fields = [{
                id: "paymentType",
                msg: "❌ Select Payment Type!"
            },
            {
                id: "paymentMethod",
                msg: "❌ Select Payment Method!"
            },
            {
                id: "invoiceNumber",
                msg: "❌ Enter Customer Invoice Number!"
            },
            {
                id: "custInvoiceValues",
                msg: "❌ Enter Customer Invoice Value!"
            },
            {
                id: "fromMobile",
                msg: "❌ Enter From Mobile Number!"
            },
            {
                id: "fromName",
                msg: "❌ Enter From Name!"
            },
            {
                id: "toMobile",
                msg: "❌ Enter To Mobile!"
            },
            {
                id: "toName",
                msg: "❌ Enter To Name!"
            },
            {
                id: "toState",
                msg: "❌ Select State!"
            },
            {
                id: "district",
                msg: "❌ Select District!"
            },
            {
                id: "routeName",
                msg: "❌ Select Route!"
            },
            {
                id: "transportType",
                msg: "❌ Select Transport Type!"
            },
            {
                id: "totalFright",
                msg: "❌ Total Freight is required!"
            }
        ];

        for (const field of fields) {
            const element = document.getElementById(field.id);
            if (!element || !element.value.trim()) {
                alert(field.msg);
                if (element) element.focus();
                return false;
            }
        }

        // Check if at least one row is added
        const rows = document.querySelectorAll('#itemTable tbody tr');
        if (rows.length === 0) {
            alert("❌ Please add at least one item!");
            return false;
        }

        // Collect all row data
        const items = [];
        let isValid = true;

        rows.forEach((row, index) => {
            const item = {
                particular: row.querySelector('.item-select').value,
                uom: row.querySelector('.uom').value,
                qty: row.querySelector('.qty').value,
                rate: row.querySelector('.rate').value,
                weight: row.querySelector('.weight').value,

            };

            // Validate row data
            if (!item.particular || !item.uom || !item.qty || !item.rate || !item.weight) {
                alert(`❌ Please fill all fields in row ${index + 1}!`);
                isValid = false;
                return;
            }

            items.push(item);
        });

        if (!isValid) return false;

        // Prepare form data
        const formData = {
            addNewBooking: 1,
            fromBranchId: fromBranchId,
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
            transport_type: document.getElementById('transportType').value,
            total_fright: document.getElementById('totalFright').value,
            loading: document.getElementById('loading').value,
            unloading: document.getElementById('unloading').value,
            lr_amount: document.getElementById('lrAmount').value,
            amount: document.getElementById('amount').value,
            items: items
        };

        // Send data via AJAX
        $.ajax({
            type: 'POST',
            url: 'bookingDataOperations.php',
            data: formData,
            dataType: 'text', // Changed from 'json' to 'text'
            success: function(response) {
                if (response.startsWith("Insert Successful")) {
                    alert("✔️ New Booking Added Successfully!");
                    let idMatch = response.match(/- (\d+)$/); // Better regex to get the ID
                    if (idMatch && idMatch[1]) {
                        window.location.href = "createPPF2.php?lr_Id=" + idMatch[1];
                    } else {
                        window.location.href = "createPPF2.php";
                    }
                } else {
                    alert("🚨 An error occurred: " + response);
                    console.log("Error response:", response);
                }
            },
            error: function(xhr, status, error) {
                alert("🚨 AJAX Error: " + error);
                console.log("AJAX Error:", xhr.responseText);
            }
        });
        return false;
    }

    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
     //Get state and City
     $(document).ready(function() {

$.ajax({
    url: 'dataOperations.php',
    type: 'GET',
    data: {
        getStates: true
    },
    success: function(response) {
        $('#toState').append(response);
    },
    error: function(xhr, status, error) {
        console.error("Error loading states:", error);
    }
});

// When State Changes -> Load Districts
$('#toState').on('change', function() {
    const stateId = $(this).val();
    if (stateId) {
        $.ajax({
            url: 'dataOperations.php',
            type: 'POST',
            data: {
                getCities: true,
                state_id: stateId
            },
            success: function(response) {
                $('#district').html('<option value="">-- SELECT DISTRICT --</option>').append(response);
            },
            error: function(xhr, status, error) {
                console.error("Error loading cities:", error);
                $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
            }
        });
    } else {
        $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
    }
});

// When District Changes -> Load Route Names
$('#district').on('change', function() {
    const stateId = $('#toState').val();
    const districtId = $(this).val();
    if (stateId && districtId) {
        $.ajax({
            url: 'dataOperations.php',
            type: 'POST',
            data: {
                getRouteName: 1,
                stateId: stateId,
                districtId: districtId
            },
            success: function(response) {
                $('#routeName').html('<option value="">-- SELECT ROUTE --</option>').append(response);
            },
            error: function(xhr, status, error) {
                console.error("Error loading route:", error);
                $('#routeName').html('<option value="">-- SELECT ROUTE --</option>');
            }
        });
    }
});
});
</script>



</html>