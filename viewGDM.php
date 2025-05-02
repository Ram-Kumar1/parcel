<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Your head content here -->
    <link rel="stylesheet" href="./css/table-filter.css">
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
                                                <h2 class="m-t-p5 mb-0 mb-5">VIEW GDM DETAILS </h2>
                                            </div>
                                            <button type="button" class="btn btn-success btn-sm pull-right" style="margin-top: 1em; margin-right: 1em;" onclick="printDiv()">
                                                <i class="material-icons" style="font-size: initial;">print</i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="table-data" class="table-responsive filterable max-30">
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
                                                          <a class='a-view-icon' href='viewGDMdetails.php?gdm=" . $row['GDM_ID'] . "'>
                                                            <i class='material-icons' style='cursor:pointer;'>remove_red_eye</i>
                                                          </a>
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

        $(document).ready(function() {
            $("#data-table").ddTableFilter();
            $('select').addClass('w3-select');
            $('select').select2();
        });
    </script>
</body>

</html>