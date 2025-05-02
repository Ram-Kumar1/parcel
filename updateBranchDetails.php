<?php
include 'dbConn.php';

// Initialize variables
$branchNameFromQry = $branchMobileFromQry = $branchAlternativeMobileFromQry = $addressFromQry = '';
$placeFromQry = $userNameFromQry = $passwordFromQry = '';
$paidComissionFromQry = $totalComissionFromQry = $isAgentFromQry = $expenseAmountFromQry = 0;
$expenseDescriptions = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['branchOfficeId'])) {
    $branchOfficeId = intval($_GET['branchOfficeId']);

    // Sanitize inputs
    $branchName = mysqli_real_escape_string($conn, $_POST['branch-name']);
    $branchMobile = mysqli_real_escape_string($conn, $_POST['branch-mobile']);
    $branchAlternativeMobile = mysqli_real_escape_string($conn, $_POST['branch-alternative-mobile'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['branch-address']);
    $place = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $userName = mysqli_real_escape_string($conn, $_POST['user-name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $isAgent = isset($_POST['is_agent']) ? 1 : 0;

    // Handle agent commission or expenses
    if ($isAgent) {
        $paidCommission = floatval($_POST['paid_commission'] ?? 0);
        $totalCommission = floatval($_POST['total_commission'] ?? 0);
        $expense = '[]';
        $totalExpenseAmount = 0;
    } else {
        $paidCommission = 0;
        $totalCommission = 0;

        // Process expense items
        $expenseItems = [];
        if (isset($_POST['expense_description'])) {
            foreach ($_POST['expense_description'] as $index => $description) {
                if (!empty($description) && isset($_POST['expense_amount'][$index])) {
                    $expenseItems[] = [
                        'description' => mysqli_real_escape_string($conn, $description),
                        'amount' => floatval($_POST['expense_amount'][$index])
                    ];
                }
            }
        }
        $expense = json_encode($expenseItems);
        $totalExpenseAmount = array_sum(array_column($expenseItems, 'amount'));
    }

    // Update query
    $updateQuery = "UPDATE branches SET 
                    ROUTE_NAME = '$branchName',
                    MOBILE = '$branchMobile',
                    ALTERNATE_MOBILE = '$branchAlternativeMobile',
                    ADDRESS = '$address',
                    CITY = '$place',
                    STATE = '$state',
                    USER_NAME = '$userName',
                    PASSWORD = '$password',
                    IS_AGENT = $isAgent,
                    PAID_COMMISION = $paidCommission,
                    TO_PAY_COMMISION = $totalCommission,
                    EXPENSES = '$expense',
                    TOTAL_EXPENSE_AMOUNT = $totalExpenseAmount
                    WHERE BRANCH_ID = $branchOfficeId";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>
            alert('✔️ Branch office updated successfully');
            window.location.href = 'branchOfficeView.php';
        </script>";
    } else {
        $errorMessage = "Error updating branch office: " . mysqli_error($conn);
    }
}


// Fetch existing data
if (isset($_GET['branchOfficeId'])) {
    $branchOfficeId = intval($_GET['branchOfficeId']);
    $resultBranchOfficeQry = mysqli_query($conn, "SELECT * FROM branches WHERE BRANCH_ID = $branchOfficeId");

    if ($resultBranchOfficeQry && mysqli_num_rows($resultBranchOfficeQry) > 0) {
        $row = mysqli_fetch_array($resultBranchOfficeQry);
        $branchNameFromQry = htmlspecialchars($row['ROUTE_NAME']);
        $branchMobileFromQry = htmlspecialchars($row['MOBILE']);
        $branchAlternativeMobileFromQry = htmlspecialchars($row['ALTERNATE_MOBILE'] ?? '');
        $addressFromQry = htmlspecialchars($row['ADDRESS']);
        $placeFromQry = htmlspecialchars($row['CITY']);
        $state = htmlspecialchars($row['STATE']);
        $userNameFromQry = htmlspecialchars($row['USER_NAME']);
        $passwordFromQry = htmlspecialchars($row['PASSWORD']);
        $paidComissionFromQry = floatval($row['PAID_COMMISION']);
        $totalComissionFromQry = floatval($row['TO_PAY_COMMISION']);
        $isAgentFromQry = intval($row['IS_AGENT']);
        $expenseAmountFromQry = floatval($row['TOTAL_EXPENSE_AMOUNT']);
        $expenseFromQry = $row['EXPENSES'];
        $expenseDescriptions = json_decode($expenseFromQry, true) ?: [];
    } else {
        die("Invalid branch office ID");
    }
} else {
    die("Branch office ID not provided");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch Office Update</title>
    <link rel="stylesheet" href="./css/table-filter.css">
    <!-- Add your other CSS and JS includes here -->
</head>

<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <?php include 'header.php'; ?>
        <!-- Content body -->
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <?php if (isset($successMessage)): ?>
                                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                                <?php endif; ?>
                                <?php if (isset($errorMessage)): ?>
                                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                                <?php endif; ?>

                                <header class="panel-heading" style="text-align: center; font-size: 20px; color: #0c1211;">
                                    Branch Office Update
                                </header>
                                <div class="panel-body">
                                    <div class="position-center">
                                        <form action="?branchOfficeId=<?php echo $branchOfficeId; ?>" role="form" method="post" onsubmit="return validateForm()">
                                            <div class="form-group">
                                                <label for="branch-name">Branch Name<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" required class="form-control" id="branch-name" value="<?php echo $branchNameFromQry; ?>" name="branch-name" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-mobile">Mobile<span class="mandatory-field text-danger">*</span></label>
                                                <input type="number" required class="form-control" id="branch-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" value="<?php echo $branchMobileFromQry; ?>" name="branch-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-alternative-mobile">Alternative Mobile</label>
                                                <input type="number" class="form-control" id="branch-alternative-mobile" minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)" value="<?php echo $branchAlternativeMobileFromQry; ?>" name="branch-alternative-mobile" />
                                            </div>
                                            <div class="form-group">
                                                <label for="branch-address">Address<span class="mandatory-field text-danger">*</span></label>
                                                <textarea required class="form-control" id="branch-address" rows="4" name="branch-address"><?php echo $addressFromQry; ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="state">State<span class="mandatory-field  text-danger">*</span></label>
                                                        <select class="form-control" id="state" name="state" required>
                                                            <?php
                                                            $getState = "SELECT STATE_NAME,STATE_ID FROM state WHERE STATE_ID = ?";
                                                            $stmt = $conn->prepare($getState);
                                                            $stmt->bind_param("i", $state);
                                                            $stmt->execute();
                                                            $result = $stmt->get_result();

                                                            while ($row = $result->fetch_assoc()) {

                                                            ?>
                                                                <option value="<?php echo $row['STATE_ID']; ?>"><?php echo $row['STATE_NAME']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="city">City<span class="mandatory-field  text-danger">*</span></label>
                                                        <select class="form-control" id="city" name="city" required>
                                                            <?php
                                                            $getCity = "SELECT CITY_NAME,CITY_ID FROM cities WHERE CITY_ID = ?";
                                                            $stmt = $conn->prepare($getCity);
                                                            $stmt->bind_param("i", $placeFromQry);
                                                            $stmt->execute();
                                                            $result = $stmt->get_result();

                                                            while ($row = $result->fetch_assoc()) {

                                                            ?>
                                                                <option value="<?php echo $row['CITY_ID']; ?>"><?php echo $row['CITY_NAME']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="user-name">User Name<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" class="form-control" id="user-name" required value="<?php echo $userNameFromQry; ?>" name="user-name" />
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password<span class="mandatory-field text-danger">*</span></label>
                                                <input type="text" class="form-control" id="password" required value="<?php echo $passwordFromQry; ?>" name="password" />
                                            </div>
                                            <div class="form-group">
                                                <label for="is_agent">Is Agent</label>&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" id="is_agent" name="is_agent" <?php echo $isAgentFromQry ? 'checked' : ''; ?> />
                                            </div>

                                            <!-- Agent commission input -->
                                            <div id="agent_details_box" style="display: <?php echo $isAgentFromQry ? 'block' : 'none'; ?>;">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="paid_commission">Paid Commission Percentage %</label>
                                                            <input type="number" step="0.01" class="form-control" value="<?php echo $paidComissionFromQry; ?>" id="paid_commission" name="paid_commission" />
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="total_commission">ToPaid Commission Percentage %</label>
                                                            <input type="number" step="0.01" class="form-control" value="<?php echo $totalComissionFromQry; ?>" id="total_commission" name="total_commission" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Expense fields -->
                                            <div id="expense_section" style="display: <?php echo !$isAgentFromQry ? 'block' : 'none'; ?>;">
                                                <div class="form-group">
                                                    <label for="total_expense_amount">Total Expense</label>
                                                    <input type="text" id="total_expense_amount" name="total_expense_amount" readonly class="form-control" value="<?php echo number_format($expenseAmountFromQry, 2); ?>" />
                                                </div>

                                                <!-- Add More Button -->
                                                <div class="form-group text-center">
                                                    <button type="button" id="add_expense" class="btn btn-secondary" style="display: <?php echo !$isAgentFromQry ? 'inline-block' : 'none'; ?>;">
                                                        <i class="fa fa-plus font-medium menu-icon"></i> Add Expense
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label>DESCRIPTION</label>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>AMOUNT</label>
                                                    </div>
                                                </div>
                                                <?php if (!$isAgentFromQry): ?>
                                                    <?php foreach ($expenseDescriptions as $expense): ?>
                                                        <div class="row expense-row mt-2 align-items-end">
                                                            <div class="col-md-5">
                                                                <div class="form-group">
                                                                    <input type="text" name="expense_description[]" value="<?php echo htmlspecialchars($expense['description'] ?? ''); ?>" placeholder="Enter description" class="form-control" />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input type="number" step="0.01" name="expense_amount[]" value="<?php echo htmlspecialchars($expense['amount'] ?? ''); ?>" placeholder="Enter amount" class="form-control" />
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
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>

                                            <button type="submit" class="btn btn-success" style="margin-left: 40%">
                                                💾 Update
                                            </button>
                                        </form>
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
    </div>

    <script>
        // Toggle agent/expense sections
        document.getElementById('is_agent').addEventListener('change', function() {
            if (this.checked) {
                document.getElementById('agent_details_box').style.display = 'block';
                document.getElementById('expense_section').style.display = 'none';
                document.getElementById('add_expense').style.display = 'none';
            } else {
                document.getElementById('agent_details_box').style.display = 'none';
                document.getElementById('expense_section').style.display = 'block';
                document.getElementById('add_expense').style.display = 'inline-block';
            }
        });

        // Add expense row
        document.getElementById('add_expense')?.addEventListener('click', function() {
            addExpenseRow('', '');
        });

        // Delete expense row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-expense') || e.target.closest('.delete-expense')) {
                const row = e.target.closest('.expense-row');
                if (row) {
                    row.remove();
                    calculateTotalExpense();
                }
            }

            if (e.target.classList.contains('clear-expense') || e.target.closest('.clear-expense')) {
                const row = e.target.closest('.expense-row');
                if (row) {
                    row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => input.value = '');
                    calculateTotalExpense();
                }
            }
        });

        // Calculate total expense
        function calculateTotalExpense() {
            let total = 0;
            document.querySelectorAll('input[name="expense_amount[]"]').forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            document.getElementById('total_expense_amount').value = total.toFixed(2);
        }

        // Form validation
        function validateForm() {
            // Basic validation - you can add more specific checks
            const branchMobile = document.getElementById('branch-mobile').value;
            if (branchMobile.length !== 10) {
                alert('Mobile number must be 10 digits');
                return false;
            }

            const altMobile = document.getElementById('branch-alternative-mobile').value;
            if (altMobile && altMobile.length !== 10) {
                alert('Alternative mobile number must be 10 digits if provided');
                return false;
            }

            return true;
        }

        // Add new expense row
        function addExpenseRow(description = '', amount = '') {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'expense-row', 'mt-2', 'align-items-end');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" name="expense_description[]" value="${description}" placeholder="Enter description" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="number" step="0.01" name="expense_amount[]" value="${amount}" placeholder="Enter amount" class="form-control" />
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
            document.getElementById('expense_section').appendChild(newRow);

            // Add input event for the new amount field
            newRow.querySelector('input[name="expense_amount[]"]').addEventListener('input', calculateTotalExpense);
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
    </script>
</body>

</html>