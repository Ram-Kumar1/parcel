<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" href="./css/table-filter.css">
<style>
    #is_agent {
        box-shadow: 2px 2px 2px 2px black;
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
                                                <h2 class="m-t-p5 mb-0">ADD BRANCH</h2>
                                            </div>

                                            <button type="button" class="btn btn-success btn-sm pull-right"
                                                style="margin-top: 0.75em;" onclick="goToAddBranchDetails()">
                                                <i class="material-icons">remove_red_eye</i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="panel-body">
                                        <div class="position-center">
                                            <div class="form-group">
                                                <label for="branch-name">Route Name<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" required class="form-control" id="branch-name"
                                                    placeholder="Enter Branch Name" name="branch-name" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-mobile">Mobile<span class="mandatory-field  text-danger">*</span></label>
                                                <input type="number" required class="form-control" id="branch-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                    placeholder="Enter Mobile No" name="branch-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-alternative-mobile">Alternative Mobile</label>
                                                <input type="number" class="form-control" id="branch-alternative-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)"
                                                    placeholder="Enter Alternative Mobile" name="branch-alternative-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-address">Address<span class="mandatory-field  text-danger">*</span></label>
                                                <textarea required class="form-control" id="branch-address" rows="4"
                                                    placeholder="Enter Address" name="branch-address"></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="state">State<span class="mandatory-field  text-danger">*</span></label>
                                                        <select class="form-control" id="state" name="state" required>
                                                            <option value="">Select State</option>
                                                            <!-- State options will be loaded dynamically -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="city">City<span class="mandatory-field  text-danger">*</span></label>
                                                        <select class="form-control" id="city" name="city" required>
                                                            <option value="">Select City</option>
                                                            <!-- City options will be loaded based on selected state -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label for="user-name">User Name<span class="mandatory-field  text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-name" required
                                                    placeholder="Enter User Name" name="user-name" />
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password<span class="mandatory-field  text-danger">*</span></label>
                                                <input type="text" class="form-control" id="password" required
                                                    placeholder="Enter Password" name="password" />
                                            </div>

                                            <div class="form-group">
                                                <label for="is_agent">Is Agent <span class="mandatory-field text-danger"></span></label>&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" id="is_agent" />
                                            </div>

                                            <!-- Agent commission input -->
                                            <div id="agent_details_box" style="display: none;">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="commission_percentage2">Booking Commission Percentage %</label>
                                                            <input type="number" class="form-control" id="booking_commission" value="15" name="booking_commission" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="commission_percentage3">Received Commission Percentage %</label>
                                                            <input type="number" class="form-control" id="recived_commission" value="5" name="recived_commission" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Expense fields -->
                                            <div id="expense_section">
                                                <div class="form-group">
                                                    <label for="">Expense</label>
                                                    <input type="text" class="form-control" readonly placeholder="Expense" name="expense[]" id="expense" value="Expense" />
                                                </div>



                                                <!-- Add More Button -->
                                                <div class="form-group text-center">
                                                    <button type="button" id="add_expense" class="btn btn-secondary">
                                                        <i class="fa fa-plus font-medium menu-icon"></i> Add Expense
                                                    </button>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <label for="">DESCRIPTION</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="">AMOUNT</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- ✅ Container for dynamic rows -->
                                            <div id="expense_rows_container"></div>

                                            <script>
                                                const isAgentCheckbox = document.getElementById('is_agent');
                                                const agentDetailsBox = document.getElementById('agent_details_box');
                                                const expenseSection = document.getElementById('expense_section');
                                                const addExpenseBtn = document.getElementById('add_expense');
                                                const expenseContainer = document.getElementById('expense_rows_container');

                                                const bookingCommission = document.getElementById('booking_commission');
                                                const recivedCommission = document.getElementById('recived_commission');

                                                isAgentCheckbox.addEventListener('change', function() {
                                                    if (this.checked) {
                                                        agentDetailsBox.style.display = 'block';
                                                        expenseSection.style.display = 'none';
                                                        addExpenseBtn.style.display = 'none';

                                                        bookingCommission.required = true;
                                                        recivedCommission.required = true;

                                                        const expenseRows = document.querySelectorAll('.expense-row');
                                                        expenseRows.forEach(row => {
                                                            row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                                                                input.value = '';
                                                            });
                                                        });
                                                        calculateTotalExpense();
                                                    } else {
                                                        agentDetailsBox.style.display = 'none';
                                                        expenseSection.style.display = 'block';
                                                        addExpenseBtn.style.display = 'inline-block';

                                                        bookingCommission.required = false;
                                                        recivedCommission.required = false;
                                                        bookingCommission.value = '';
                                                        recivedCommission.value = '';
                                                    }
                                                });

                                                document.querySelector('form')?.addEventListener('submit', function(e) {
                                                    if (!isAgentCheckbox.checked) {
                                                        bookingCommission.disabled = true;
                                                        recivedCommission.disabled = true;
                                                    }
                                                });

                                                function calculateTotalExpense() {
                                                    let total = 0;
                                                    const amountFields = document.querySelectorAll('input[name="expense_amount[]"]');
                                                    amountFields.forEach(field => {
                                                        const val = parseFloat(field.value);
                                                        if (!isNaN(val)) total += val;
                                                    });

                                                    const expenseInputs = document.querySelectorAll('input[name="expense[]"]');
                                                    expenseInputs.forEach(input => input.value = total.toFixed(2));
                                                }

                                                function attachAmountListeners() {
                                                    const amountFields = document.querySelectorAll('input[name="expense_amount[]"]');
                                                    amountFields.forEach(field => {
                                                        field.removeEventListener('input', calculateTotalExpense);
                                                        field.addEventListener('input', calculateTotalExpense);
                                                    });
                                                }

                                                addExpenseBtn.addEventListener('click', function() {
                                                    const newRow = document.createElement('div');
                                                    newRow.classList.add('row', 'expense-row', 'mt-2', 'align-items-end');

                                                    newRow.innerHTML = `
            <div class="col-md-5">
                <div class="form-group">
                    <input type="text" name="expense_description[]" placeholder="Enter description" class="form-control" />
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <input type="number" name="expense_amount[]" placeholder="Enter amount" class="form-control" />
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" class="btn btn-warning btn-sm clear-expense">
                        <i class="fa fa-eraser font-medium menu-icon"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-sm delete-expense">
                        <i class="fa fa-minus font-medium menu-icon"></i>
                    </button>
                </div>
            </div>
        `;

                                                    // ✅ Append to the dynamic container
                                                    expenseContainer.appendChild(newRow);

                                                    // Attach listeners
                                                    attachAmountListeners();

                                                    // Delete Row
                                                    newRow.querySelector('.delete-expense').addEventListener('click', function() {
                                                        newRow.remove();
                                                        calculateTotalExpense();
                                                    });

                                                    // Clear Row
                                                    newRow.querySelector('.clear-expense').addEventListener('click', function() {
                                                        const row = this.closest('.expense-row');
                                                        row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => input.value = '');
                                                        calculateTotalExpense();
                                                    });
                                                });

                                                // Call initially on page load for static rows (if any)
                                                attachAmountListeners();
                                            </script>




                                            <button class="btn btn-success" style="margin-left: 40%" onclick="addBranchDetails()">
                                                <div class="fa fa-floppy-o font-medium menu-icon " aria-hidden="true"></div>
                                                &nbsp; Submit
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


<script type="text/javascript">
    $('select').select2();

    // let goToAddBranchDetails = function () {
    function goToAddBranchDetails() {
        window.location.href = "branchOfficeView.php";
    };


    //add Branch Details
    function addBranchDetails() {
        const fields = [{
                id: "branch-name",
                msg: "❌ Please Enter Branch Name!"
            },
            {
                id: "branch-mobile",
                msg: "❌ Please Enter Mobile No!"
            },

            {
                id: "branch-address",
                msg: "❌ Please Enter Address!"
            },
            {
                id: "state",
                msg: "❌ State Select Place!"
            },
            {
                id: "city",
                msg: "❌ City Select Place!"
            },
            {
                id: "user-name",
                msg: "❌ Please Enter User Name!"
            },
            {
                id: "password",
                msg: "❌ Please Enter Password!"
            }
        ];

        for (let field of fields) {
            let value = document.getElementById(field.id).value.trim();
            if (!value) {
                alert(field.msg);
                return false;
            }
        }
        console.log($("#booking_commission").val())
        console.log($("#recived_commission").val())
        $.ajax({
            type: 'post',
            url: 'dataOperations.php',
            data: {
                addBranchDetails: 1,
                branchName: $("#branch-name").val(),
                branchMobile: $("#branch-mobile").val(),
                branchAlternativeMobile: $("#branch-alternative-mobile").val(),
                branchAddress: $("#branch-address").val(),
                branchPlace: $("#city").val(),
                state: $("#state").val(),
                userName: $("#user-name").val(),
                password: $("#password").val(),
                bookingCommission: $("#booking_commission").val(),
                receivedCommission: $("#recived_commission").val(),
                expense: $("input[name='expense[]']").map(function() {
                    return $(this).val();
                }).get(),

                'expense_description[]': $("input[name='expense_description[]']").map(function() {
                    return $(this).val();
                }).get(),
                'expense_amount[]': $("input[name='expense_amount[]']").map(function() {
                    return $(this).val();
                }).get(),
                isAgent: $("#is_agent").is(":checked") ? 1 : 0
            },
            success: function(response) {
                console.log("Server response:", response);
                if (response.toString().startsWith("Insert Successful")) {
                    alert("✔️ Branch Added Successfully!");
                    window.location.reload();
                } else if (

                    response.toString() == "BRANCH_NAME_ALREADY_EXISTS" ||
                    response.toString() == "USER_NAME_ALREADY_EXISTS" ||
                    response.toString() == "BRANCH_MOBILE_ALREADY_EXISTS"

                ) {
                    let errorMsg = {
                        "BRANCH_NAME_ALREADY_EXISTS": "❌ Branch Already Exists!",
                        "USER_NAME_ALREADY_EXISTS": "❌ User Name Already Exists!",
                        "BRANCH_MOBILE_ALREADY_EXISTS": "❌ Mobile Number Already Exists!",

                    } [response.toString()];
                    alert(errorMsg);
                } else if (response.toString() == "DB_ERROR") {
                    alert("🚨 Database error. Please try again later.");
                } else {
                    alert("🚨 An error occurred. Please try again.");
                    console.log("Unexpected response:", response);
                }
            }

        });

        return false;
    }


    //Get State and City 
    $(document).ready(function() {
        // Load states on page load
        $.ajax({
            url: 'dataOperations.php',
            type: 'GET',
            data: {
                getStates: 1
            },
            success: function(response) {
                $('#state').append(response);
            }
        });

        // Load cities based on selected state
        $('#state').on('change', function() {
            var state_id = $(this).val();
            $('#city').html('<option value="">Select City</option>'); // Reset city dropdown

            if (state_id !== '') {
                $.ajax({
                    url: 'dataOperations.php',
                    type: 'POST',
                    data: {
                        getCities: 1,
                        state_id: state_id
                    },
                    success: function(response) {
                        $('#city').append(response);
                    }
                });
            }
        });
    });




    $(document).ready(function() {
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });
</script>

</html>