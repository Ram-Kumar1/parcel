<!DOCTYPE html>
<html lang="en">

<head>

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
                                                <h2 class="m-t-p5 mb-0 mb-5">VIEW TRIP DETAILS </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="table-data" class="table-responsive filterable max-30">
                                        <table id="data-table" class="table table-striped tableFixHead">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Sno</th>
                                                    <th>Trip Number</th>
                                                    <th>Opening KM</th>
                                                    <th>Closing KM</th>
                                                    <th>Diesel Of Litter</th>
                                                    <th>Diesel Amount</th>
                                                    <th>Advance Amount</th>
                                                    <th>Total Amount</th>
                                                    <th>View</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $userName = $_SESSION['userName'] ?? null;
                                                $branchName = $_SESSION['admin'] ?? null;
                                                $sno = 1;

                                                function getGDMDetails($conn)
                                                {
                                                    $sql = "SELECT * FROM v_viewtrip";
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
                                                $totalTotalAmount = 0;
                                                $totalDieselAmount = 0;
                                                $totalAdvanceAmount = 0;

                                                foreach ($gdmDetails as $row) {
                                                    $totalAmount = floatval($row['TOTAL_AMOUNT'] ?? 0);
                                                    $dieselAmount = floatval($row['DIESEL_AMOUNT'] ?? 0);
                                                    $dieselLitter = $row['DIESEL_LITTER'] ?? 'N/A';
                                                    $advanceAmount = floatval($row['ADVANCE_AMOUNT'] ?? 0);

                                                    $totalTotalAmount += $totalAmount;
                                                    $totalDieselAmount += $dieselAmount;
                                                    $totalAdvanceAmount += $advanceAmount;

                                                    echo "<tr class='text-center'>
              <td>" . $sno . "</td>
              <td>" . ($row['TRIP_NUMBER'] ?? 'N/A') . "</td>
              <td>" . ($row['OPENING_KM'] ?? 'N/A') . "</td>
              <td>" . ($row['CLOSING_KM'] ?? 'N/A') . "</td>
              <td>" . $dieselLitter . "</td>
              <td>" . number_format($dieselAmount, 2) . "</td>
              <td>" . number_format($advanceAmount, 2) . "</td>
              <td>" . number_format($totalAmount, 2) . "</td>
              <td>
                    <a class='a-view-icon' href='viewTripGDM.php?gdm=" . $row['tgm_trip_mapping_id'] . "'>
                    <i class='material-icons' style='cursor:pointer;'>remove_red_eye</i>
                    </a>
              </td>
              </tr>";
                                                    $sno++;
                                                }
                                                ?>
                                            </tbody>

                                            <tfoot>
                                                <tr class='text-center font-weight-bold'>
                                                    <td colspan="5">Grand Total</td>
                                                    <td><?= number_format($totalDieselAmount, 2) ?></td>
                                                    <td><?= number_format($totalAdvanceAmount, 2) ?></td>
                                                    <td><?= number_format($totalTotalAmount, 2) ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>



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
        $(document).ready(function() {
            $("#data-table").ddTableFilter();
            $('select').addClass('w3-select');
            $('select').select2();
        });
    </script>
</body>

</html>