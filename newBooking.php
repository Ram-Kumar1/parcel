<?php



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
                                                            placeholder="Enter Lr Number" name="manual_lr" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="payment-type">Payment Type<span class="mandatory-field text-danger">*</span></label><br>
                                                        <select class="form-control" id="payment-type" name="paymenttt-type" required>
                                                            <option value="">-- SELECT PAYMENT --</option>
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
                                                            <option value="">-- SELECT PAYMENT --</option>
                                                            <option value="ONLINE">Online</option>
                                                            <option value="CASE">Cash</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Number<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="invoice_number"
                                                            placeholder="Enter Cust Invoice Number" name="invoice_number" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Cust Invoice Value<span class="mandatory-field text-danger">*</span></label>
                                                        <input type="text" required class="form-control" id="cust_invoice_values"
                                                            placeholder="Enter Cust Invoice Value" name="cust_invoice_values" />
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <!-- SELECT for choosing existing mobile -->
                                                        <select id="from_mobile_select" class="form-control">
                                                            <option value="">Select Mobile</option>
                                                        </select>
                                                        <!-- INPUT for typing new mobile -->
                                                        <input type="number" class="form-control mt-2" id="from_mobile_input"
                                                            minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                            placeholder="Enter Mobile Number" />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>From Name<span class="mandatory-field text-danger">*</span></label>
                                                        <!-- SELECT for choosing existing name -->
                                                        <select id="from_name_select" class="form-control">
                                                            <option value="">Select Name</option>
                                                        </select>
                                                        <!-- INPUT for typing new name -->
                                                        <input type="text" class="form-control mt-2" id="from_name_input"
                                                            placeholder="Enter Sender Name" />
                                                    </div>
                                                </div>


                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="from_customer" id="from_customer"> &nbsp; <label for="payment_customer">From Customer?</label>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">To Mobile<span class="mandatory-field text-danger">*</span></label>
                                                        <select id="to_mobile_select" class="form-control">
                                                            <option value="">Select Mobile</option>
                                                        </select>
                                                        <input type="number" required class="form-control mt-2" id="to_mobile_input" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                            placeholder="Enter Mobile Number" name="to_mobile_input" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">To Name<span class="mandatory-field text-danger">*</span></label>
                                                        <select id="to_name_select" class="form-control">
                                                            <option value="">Select Name</option>
                                                        </select>
                                                        <input type="text" required class="form-control mt-2" id="to_name_input"
                                                            placeholder="Enter Receiver Name" name="to_name_input" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Payment Customer<span class="mandatory-field text-danger">*</span></label><br>
                                                        <input type="checkbox" name="to_customer" id="to_customer"> &nbsp; <label for="payment_customer">To Customer?</label>
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
                                                        <input type="text" required class="form-control" id="dcc"
                                                            name="dcc" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">

                                                    <div class="form-group">
                                                        <label for="">Transport Type<span class="mandatory-field text-danger">*</span></label>
                                                        <select class="form-control" id="transport_type" name="transport_type" required>
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
                                                        <input type="text" required class="form-control" id="total_fright" name="fright" readonly />
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Loading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="loading"
                                                            name="loading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">Unloading<span class="mandatory-field text-danger"></span></label>
                                                        <input type="text" required class="form-control" id="unloading"
                                                            name="unloading" />
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="">LR Amount<span class="mandatory-field text-danger"></span></label>
                                                        <input type="number" required class="form-control" id="lr_amount"
                                                            name="lr_amount" readonly />
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

                                            <input type="submit" class="btn btn-primary btn-lg" onclick="saveData()" value="Submit">
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
    function addRow() {
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
                <option value="Kg">Kg</option>
                <option value="Number">Number</option>
            </select>
        </td>
        <td class="text-center"><input type="number" class="form-control w-100 qty" placeholder="Qty"></td>
        <td class="text-center"><input type="number" class="form-control w-100 rate" placeholder="Rate"></td>
        <td class="text-center"><input type="number" class="form-control w-100 weight" placeholder="Weight"></td>
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
        loadItemOptions($('.item-select').last());
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

    //Get Items
    function loadItemOptions(targetSelect = $('.item-select')) {
        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'GET',
            data: {
                getItem: 1
            },
            success: function(response) {
                console.log('Server Response:', response);
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
    });


    //Get state and city and Rout
    $(document).ready(function() {
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

        $('#district').change(function() {
            var stateId = $('#to_state').val();
            var districtId = $('#district').val();
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
                        $('#rount_name').html('<option value="">-- SELECT ROUTE --</option>');
                        $('#rount_name').append(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading route: " + error);
                        $('#rount_name').html('<option value="">-- SELECT ROUTE --</option>');
                    }
                });
            }
        });
    });

    // Calculate freight and totals
    $(document).ready(function() {
        $(document).on('input', '.qty, .rate', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const rate = parseFloat(row.find('.rate').val()) || 0;
            const freight = qty * rate;

            row.find('.freight').val(freight.toFixed(2));
            calculateTotalFreight();
        });

        // Calculate weight when qty changes (optional)
        $(document).on('input', '.qty', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const uom = row.find('.uom').val();

            if (uom === 'Kg') {
                row.find('.weight').val(qty);
            }
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

    //Get from Mobile list
    $(document).ready(function() {
        let mobileToName = {};

        // Load dropdowns
        $.ajax({
            url: 'dataOperations.php',
            data: {
                getMobile: 1
            },
            type: 'GET',
            success: function(res) {
                $('#from_mobile_select').append(res);
            }
        });

        $.ajax({
            url: 'dataOperations.php',
            data: {
                getname: 1
            },
            type: 'GET',
            success: function(res) {
                $('#from_name_select').append(res);
            }
        });

        // Load Mobile->Name map
        $.ajax({
            url: 'dataOperations.php',
            data: {
                getMobileNameMapping: 1
            },
            type: 'GET',
            success: function(res) {
                mobileToName = JSON.parse(res);
            }
        });

        // When mobile is selected
        $('#from_mobile_select').on('change', function() {
            let selectedMobile = $(this).val();
            if (selectedMobile && mobileToName[selectedMobile]) {
                $('#from_name_select').val(mobileToName[selectedMobile]);
                $('#from_mobile_input').val(selectedMobile);
                $('#from_name_input').val(mobileToName[selectedMobile]);
            }
        });

        // When name is selected
        $('#from_name_select').on('change', function() {
            let selectedName = $(this).val();
            // Reverse search: find mobile by name
            let foundMobile = null;
            $.each(mobileToName, function(mobile, name) {
                if (name === selectedName) {
                    foundMobile = mobile;
                    return false; // Break
                }
            });

            if (foundMobile) {
                $('#from_mobile_select').val(foundMobile);
                $('#from_mobile_input').val(foundMobile);
                $('#from_name_input').val(selectedName);
            }
        });

        // When user manually types new mobile
        $('#from_mobile_input').on('input', function() {
            $('#from_mobile_select').val('');
            $('#from_name_select').val('');
            $('#from_name_input').val('');
        });

        // When user manually types new name
        $('#from_name_input').on('input', function() {
            $('#from_mobile_select').val('');
            $('#from_name_select').val('');
            $('#from_mobile_input').val('');
        });
    });

    //Get to Mobile list
    $(document).ready(function() {
        let mobileToName = {};

        // Load dropdowns
        $.ajax({
            url: 'dataOperations.php',
            data: {
                getToMobile: 1
            },
            type: 'GET',
            success: function(res) {
                $('#to_mobile_select').append(res);
            }
        });

        $.ajax({
            url: 'dataOperations.php',
            data: {
                getToname: 1
            },
            type: 'GET',
            success: function(res) {
                $('#to_name_select').append(res);
            }
        });

        // Load Mobile->Name map
        $.ajax({
            url: 'dataOperations.php',
            data: {
                getToMobileNameMapping: 1
            },
            type: 'GET',
            success: function(res) {
                mobileToName = JSON.parse(res);
            }
        });

        // When mobile is selected
        $('#to_mobile_select').on('change', function() {
            let selectedMobile = $(this).val();
            if (selectedMobile && mobileToName[selectedMobile]) {
                $('#to_name_select').val(mobileToName[selectedMobile]);
                $('#to_mobile_input').val(selectedMobile);
                $('#to_name_input').val(mobileToName[selectedMobile]);
            }
        });

        // When name is selected
        $('#to_name_select').on('change', function() {
            let selectedName = $(this).val();
            // Reverse search: find mobile by name
            let foundMobile = null;
            $.each(mobileToName, function(mobile, name) {
                if (name === selectedName) {
                    foundMobile = mobile;
                    return false; // Break
                }
            });

            if (foundMobile) {
                $('#to_mobile_select').val(foundMobile);
                $('#to_mobile_input').val(foundMobile);
                $('#to_name_input').val(selectedName);
            }
        });

        // When user manually types new mobile
        $('#to_mobile_input').on('input', function() {
            $('#to_mobile_select').val('');
            $('#to_name_select').val('');
            $('#to_name_input').val('');
        });

        // When user manually types new name
        $('#from_name_input').on('input', function() {
            $('#to_mobile_select').val('');
            $('#to_name_select').val('');
            $('#to_mobile_input').val('');
        });
    });

    //saveData
    function saveData() {
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
                id: "from_mobile_input",
                msg: "❌ Enter From Mobile Number!"
            },
            {
                id: "from_name_input",
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
            addNewBooking: 1,
            date_time: document.getElementById('date_time').value,
            bille_no: document.getElementById('bille_no').value,
            manual_lr: document.getElementById('manual_lr').value,
            payment_type: document.getElementById('payment-type').value,
            payment_method: document.getElementById('payment-method').value,
            invoice_number: document.getElementById('invoice_number').value,
            cust_invoice_values: document.getElementById('cust_invoice_values').value,
            from_mobile: document.getElementById('from_mobile_input').value,
            from_name: document.getElementById('from_name_input').value,
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
</script>


</html>