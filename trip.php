<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Your head content here -->
    <link rel="stylesheet" href="./css/table-filter.css">
    <style>

    </style>

</head>

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
                                                <h2 class="m-t-p5 mb-0 mb-5">CREATE TRIP </h2>
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm pull-right" style="margin-top: 1em;" onclick="window.location.reload();">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </button> &nbsp; &nbsp;
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-6">
                                            <label for="openingKm">Opening KM <span class="text-danger">*</span></label>
                                            <input type="number" name="openingKm" id="openingKm" class="form-control">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label for="closingKm">Closing KM <span class="text-danger">*</span></label>
                                            <input type="number" name="closingKm" id="closingKm" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-sm-6">
                                            <label for="dieselAmount">Diesel Amount <span class="text-danger">*</span></label>
                                            <input type="number" name="dieselAmount" id="dieselAmount" class="form-control">
                                        </div>
                                        <div class="col-12 col-sm-6 d-none">
                                            <label for="dieselLitter">Diesel Of Litter</label>
                                            <input type="number" name="dieselLitter" id="dieselLitter" class="form-control" placeholder="None">
                                        </div>
                                    </div>
                                    <div class="row mb-5">
                                        <div class="col-12 col-sm-6">
                                            <label for="advanceAmount">Advance Amount <span class="text-danger">*</span></label>
                                            <input type="number" name="advanceAmount" id="advanceAmount" class="form-control">
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label for="totalAmount">Total Amount</label>
                                            <input type="number" name="totalAmount" id="totalAmount" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <div class="btnSection pull-right mb-2">
                                            <button type="button" class="btn btn-info btn-xs" id="check-all">
                                                Check All <i class="fa fa-check-square-o" aria-hidden="true"></i>
                                            </button>
                                            &nbsp;
                                            <button id="submit-selected" class="btn btn-success btn-sm" onclick="createTrip()">
                                                Submit Selected <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            </button>

                                        </div>


                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Sno</th>
                                                    <th>GDM Number</th>
                                                    <th>Shipment Area</th>
                                                    <th>Driver Name</th>
                                                    <th>Driver Mobile</th>
                                                    <th>View GDM Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $userName = $_SESSION['userName'] ?? null;
                                                $branchName = $_SESSION['admin'] ?? null;
                                                $sno = 1;

                                                function getGDMDetails($conn)
                                                {
                                                    $sql = "SELECT * FROM v_gdm_details";
                                                    $result = $conn->query($sql);

                                                    $data = [];
                                                    if ($result && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            $data[] = $row;
                                                        }
                                                    }

                                                    return $data;
                                                }

                                                $gdmDetails = getGDMDetails($conn);

                                                foreach ($gdmDetails as $row) {
                                                    echo "<tr class='text-center'>
                                                        <td>" . $sno . "</td>
                                                        <td>" . ($row['GDM_NUMBER'] ?? 'N/A') . "</td>
                                                        <td>" . ($row['SHIPMENT_AREA'] ?? 'N/A') . "</td>
                                                        <td>" . ($row['DRIVER_NAME'] ?? 'N/A') . "</td>
                                                        <td>" . ($row['DRIVER_NUMBER'] ?? 'N/A') . "</td>
                                                        <td>
                                                            <input type='checkbox' class='border-dark'
                                                             id='move-booking-id-" . $row['GDM_ID'] . "' 
                                                             data-booking-id='" . $row['GDM_ID'] . "' > 
                                                        </td>

                                                    </tr>";
                                                    $sno++;
                                                }

                                                ?>
                                            </tbody>
                                        </table>

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

        <?php include 'footer.php'; ?>

    </div>

    <!-- Select2 Fileter -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Table Filter -->
    <script src="./js/ddtf.js"></script>
    <!-- Prevent Number Scrolling -->
    <script src="./js/chits/numberInputPreventScroll.js"></script>
    <script type="text/javascript">
    $(document).ready(function () {
        // Show/hide dieselLitter field
        $('#dieselAmount').on('input', function () {
            const value = $(this).val().trim();
            if (value !== '') {
                $('#dieselLitter').closest('.col-12').removeClass('d-none');
            } else {
                $('#dieselLitter').closest('.col-12').addClass('d-none');
            }
        });

        // Update totalAmount
        function updateTotalAmount() {
            let diesel = parseFloat($('#dieselAmount').val()) || 0;
            let advance = parseFloat($('#advanceAmount').val()) || 0;
            let total = diesel + advance;
            $('#totalAmount').val(total.toFixed(2));
        }

        $('#dieselAmount, #advanceAmount').on('input', updateTotalAmount);

        // Check/Uncheck all checkboxes
        const checkAllBtn = document.querySelector("#check-all");
        checkAllBtn.addEventListener("click", function () {
            const checkboxes = document.querySelectorAll("input[type='checkbox'][data-booking-id]");
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.innerHTML = (!allChecked ? "Uncheck All" : "Check All") + ' <i class="fa fa-check-square-o" aria-hidden="true"></i>';
        });

        // Table filter and select2
        $("#data-table").ddTableFilter();
        $('select').addClass('w3-select');
        $('select').select2();
    });

    function createTrip() {
        let openingKm = $('#openingKm').val();
        if (!openingKm) return alert("❌ Starting KM is mandatory!");

        let closingKm = $('#closingKm').val();
        if (!closingKm) return alert("❌ Closing KM is mandatory!");

        let dieselAmount = $('#dieselAmount').val();
        if (!dieselAmount) return alert("❌ Diesel Amount is mandatory!");

        let dieselLitter = $('#dieselLitter').val() || 0;

        let advanceAmount = $('#advanceAmount').val();
        if (!advanceAmount) return alert("❌ Advance Amount is mandatory!");

        let totalAmount = $('#totalAmount').val() || 0;

        let selectedIds = [];
        $("input[type='checkbox']:checked").each(function () {
            selectedIds.push($(this).data("booking-id"));
        });

        if (selectedIds.length === 0) {
            alert("❌ Please select at least one GDM entry.");
            return;
        }

        $.ajax({
            url: 'bookingDataOperations.php',
            type: 'POST',
            data: {
                createTrip: 1,
                gdmIds: selectedIds,
                openingKm: openingKm,
                closingKm: closingKm,
                dieselAmount: dieselAmount,
                dieselLitter: dieselLitter,
                advanceAmount: advanceAmount,
                totalAmount: totalAmount
            },
            success: function (response) {
                console.log("Response from server:", response);
                response = response.toString().trim();

                if (response.startsWith("Insert Successful")) {
                    alert("✔️ Trip created successfully!");
                    window.location.reload();
                } else {
                    alert("🚨 Some error occurred. Please try again.");
                }
            }
        });
    }
</script>

</body>

</html>