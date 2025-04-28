<?php
include "dbConn.php";
if (isset($_GET['bookingId'])) {
    $bookingId = $_GET['bookingId'];

    $getBooking = "SELECT * FROM booking WHERE BOOKING_ID = ?";

    $stmt = $conn->prepare($getBooking);

    if ($stmt) {
        $stmt->bind_param("s", $bookingId);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $items = json_decode($row['ITEMS'], true);
        } else {
            echo "No booking found.";
        }

        $stmt->close();
    } else {
        echo "Query failed: " . $conn->error;
    }
}
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

        <?php include 'header.php'; ?>

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
                                                <input type="hidden" id="booking_id" value="<?php echo $row['BOOKING_ID'];  ?>">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Date & Time<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="date_time" value="<?php date_default_timezone_set('Asia/Kolkata');
                                                                                                                                echo date('d-m-Y & h:i:s A'); ?>" name="date_time" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Bill No<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="bille_no" placeholder="Auto Generate"
                                                            value="LR00" name="bille_no" readonly <?php echo "" ?> />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Manual Lr<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="manual_lr"
                                                            placeholder="Enter Lr Number" name="manual_lr" value="<?php echo $row['MANUAL_LR_NUMBER'];  ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="payment-type">Payment Type<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="payment-type" name="paymenttt-type" required>
                                                            <option value="<?php echo $row['PAYMENT_TYPE'];  ?>"><?php echo $row['PAYMENT_TYPE'];  ?></option>
                                                            <option value="PAID">Paid</option>
                                                            <option value="TO_PAY">To Pay</option>
                                                            <option value="ACCOUNT">Account</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="payment-method">Payment Method<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="payment-method" name="payment-method" required>
                                                            <option value="<?php echo $row['PAYMENT_METHOD'];  ?>"><?php echo $row['PAYMENT_METHOD'];  ?></option>
                                                            <option value="ONLINE">Online</option>
                                                            <option value="CASE">Cash</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Number<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="invoice_number" value="<?php echo $row['CUSTOMER_INVOICE_NUMBER']; ?>"
                                                            placeholder="Enter Cust Invoice Number" name="invoice_number" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Value<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="cust_invoice_values" value="<?php echo $row['CUSTOMER_INVOICE_VALUE']; ?>"
                                                            placeholder="Enter Cust Invoice Value" name="cust_invoice_values" />
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">From Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="number" required class="form-control" id="from_mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                            placeholder="Enter Mobile Number" name="from_mobile" value="<?php echo $row['FROM_MOBILE']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">From Name<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="from_name"
                                                            placeholder="Enter Sender Name" name="from_name" value="<?php echo $row['FROM_NAME']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="from_customer" id="from_customer"> &nbsp; <label for="payment_customer" value="<?php echo $row['IS_FROM_PAYMENT_CUSTOMER']; ?>">From Customer?</label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">To Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="number" required class="form-control" id="to_mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                            placeholder="Enter Mobile Number" name="to_mobile" value="<?php echo $row['TO_MOBILE']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">To Name<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="to_name"
                                                            placeholder="Enter Receiver Name" name="to_name" value="<?php echo $row['TO_NAME']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="to_customer" id="to_customer"> &nbsp; <label for="payment_customer" value="<?php echo $row['IS_TO_PAYMENT_CUSTOMER']; ?>">To Customer?</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="state">To State<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="to_state" name="to_state" required>
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
                                                        <label for="route-name">Route Name<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="rount_name" name="rount_name" required>
                                                            <option value="">-- SELECT ROUTE --</option>
                                                            <option value="test">test</option>
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
                                            <!-- <div id="table-data" class="table-responsive filterable max-30">
                                                <table class="table table-striped tableFixHead" id="itemTable"> -->
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
                                                        <input type="text" required class="form-control" id="dcc" value="<?php echo $row['DCC']; ?>"
                                                            name="dcc" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Transport Type<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="transport_type" name="transport_type" required>
                                                            <option value="">-- SELECT TRANSPORT TYPE --</option>
                                                            <option value="test">test</option>TRANSPORT_TYPE
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Total Fright<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="total_fright" name="fright" value="<?php echo $row['FRIGHT']; ?>" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Loading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="loading" value="<?php echo $row['LOADING']; ?>"
                                                            name="loading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Unloading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="unloading" value="<?php echo $row['UNLOADING']; ?>"
                                                            name="unloading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">LR Amount<span class="mandatory-field text-danger"></span></label>
                                                        <input type="number" required class="form-control" id="lr_amount" value="<?php echo $row['LR_AMOUNT']; ?>"
                                                            name="lr_amount" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Amount<span class="mandatory-field text-danger"></span></label>
                                                        <input type="number" required class="form-control" id="amount" value="<?php echo $row['TOTAL_AMOUNT']; ?>"
                                                            name="amount" readonly style="background-color: yellow;" />
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="submit" class="btn btn-primary btn-lg" onclick="updateData()" value="Update">
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Table Filter -->
<script src="./js/ddtf.js"></script>
<!-- Prevent Number Scrolling -->
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
            <span onclick="clearRow(this)" style="cursor: pointer;">
                <i class="fa fa-eraser text-primary"></i>
            </span>&nbsp;&nbsp;
            <span onclick="deleteRow(this)" style="cursor: pointer;">
                <i class="fa fa-trash text-danger"></i>
            </span>
        </td>
        <input type="hidden" class="form-control w-100 freight" readonly>
    `;

        tableBody.appendChild(row);
        const itemSelect = row.querySelector('.item-select');
        loadItemOptions($(itemSelect), particular); // Load options and set selected
    }

    window.onload = function() {
        if (existingItems.length) {
            existingItems.forEach(item => {
                addRow(item.particular, item.uom, item.qty, item.rate, item.weight);
            });
        }
    }

    // Delete row
    function deleteRow(el) {
        $(el).closest('tr').remove();
        calculateTotalFreight();
    }

    // Clear row inputs and recalculate
    function clearRow(el) {
        const row = $(el).closest('tr');
        row.find('input').val('');
        row.find('select').val('');
        row.find('.freight').val('0.00');
        row.find('.qty, .rate').trigger('input'); // Force recalculation
        calculateTotalFreight();
    }

    // Load item options via AJAX
    function loadItemOptions(targetSelect = $('.item-select'), selectedValue = '') {
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

    // Document ready
    $(document).ready(function() {
        loadItemOptions(); // Initially load item options for new rows

        // Load states
        $.ajax({
            url: 'dataOperations.php',
            type: 'GET',
            data: {
                getStates: true
            },
            success: function(response) {
                $('#to_state').append(response);
            },
            error: function(xhr, status, error) {
                console.error("Error loading states: " + error);
            }
        });

        // Load districts when state changes
        $('#to_state').change(function() {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    url: 'dataOperations.php',
                    type: 'POST',
                    data: {
                        getCities: true,
                        state_id: stateId
                    },
                    success: function(response) {
                        $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
                        $('#district').append(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading cities: " + error);
                        $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
                    }
                });
            } else {
                $('#district').html('<option value="">-- SELECT DISTRICT --</option>');
            }
        });

        // Recalculate freight on qty or rate change
        $(document).on('input', '.qty, .rate', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const rate = parseFloat(row.find('.rate').val()) || 0;
            const freight = qty * rate;

            row.find('.freight').val(freight.toFixed(2));
            calculateTotalFreight();
        });

        // Auto-fill weight if UOM is Kg
        $(document).on('input', '.qty', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const uom = row.find('.uom').val();

            if (uom === 'Kg') {
                row.find('.weight').val(qty);
            }
        });

        // Recheck weight when UOM changes
        $(document).on('change', '.uom', function() {
            $(this).closest('tr').find('.qty').trigger('input');
        });

        // Recalculate when item changes
        $(document).on('change', '.item-select', function() {
            const row = $(this).closest('tr');
            row.find('.qty, .rate').trigger('input');
        });
    });

    // Calculate total freight
    function calculateTotalFreight() {
        let total = 0;
        $('.freight').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_fright').val(total.toFixed(2));
    }

    //saveData
    function updateData() {
        // Validate required fields
        const fields = [{
                id: "payment-type",
                msg: "❌ Select Payment Type!"
            },
            {
                id: "payment-method",
                msg: "❌ Select Payment Method!"
            },
            {
                id: "invoice_number",
                msg: "❌ Enter Customer Invoice Number!"
            },
            {
                id: "cust_invoice_values",
                msg: "❌ Enter Customer Invoice Value!"
            },
            {
                id: "from_mobile",
                msg: "❌ Enter From Mobile Number!"
            },
            {
                id: "from_name",
                msg: "❌ Enter From Name!"
            },
            {
                id: "to_mobile",
                msg: "❌ Enter To Mobile!"
            },
            {
                id: "to_name",
                msg: "❌ Enter To Name!"
            },
            {
                id: "to_state",
                msg: "❌ Select State!"
            },
            {
                id: "district",
                msg: "❌ Select District!"
            },
            {
                id: "rount_name",
                msg: "❌ Select Route!"
            },
            {
                id: "transport_type",
                msg: "❌ Select Transport Type!"
            },
            {
                id: "total_fright",
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
            updateNewBooking: 1,
            booking_id: document.getElementById('booking_id').value,
            date_time: document.getElementById('date_time').value,
            bille_no: document.getElementById('bille_no').value,
            manual_lr: document.getElementById('manual_lr').value,
            payment_type: document.getElementById('payment-type').value,
            payment_method: document.getElementById('payment-method').value,
            invoice_number: document.getElementById('invoice_number').value,
            cust_invoice_values: document.getElementById('cust_invoice_values').value,
            from_mobile: document.getElementById('from_mobile').value,
            from_name: document.getElementById('from_name').value,
            from_customer: document.getElementById('from_customer').checked ? 1 : 0,
            to_mobile: document.getElementById('to_mobile').value,
            to_name: document.getElementById('to_name').value,
            to_customer: document.getElementById('to_customer').checked ? 1 : 0,
            to_state: document.getElementById('to_state').value,
            district: document.getElementById('district').value,
            rount_name: document.getElementById('rount_name').value,
            dcc: document.getElementById('dcc').value,
            transport_type: document.getElementById('transport_type').value,
            total_fright: document.getElementById('total_fright').value,
            loading: document.getElementById('loading').value,
            unloading: document.getElementById('unloading').value,
            lr_amount: document.getElementById('lr_amount').value,
            amount: document.getElementById('amount').value,
            items: items
        };

        // Send data via AJAX
        $.ajax({
            type: 'POST',
            url: 'bookingDataOperations.php',
            data: formData,
            dataType: 'text',
            success: function(response) {
                if (response.startsWith("Update Successful")) {
                    alert("✔️ New Booking Update Successfully!");
                    window.location.href = "viewBookingList.php";

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
</script>


</html>