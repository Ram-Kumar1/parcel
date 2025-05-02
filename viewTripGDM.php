<?php
include 'dbConn.php';
$gdmNumbers = [];
if (isset($_GET['gdm'])) {
    $tripMappingId = $_GET['gdm'];
    $sql = "
        SELECT 
            g.GDM_ID, g.GDM_NUMBER, g.BOOKING_ID
        FROM 
            trip_gdm_mapping t
        JOIN 
            gdm_number g ON FIND_IN_SET(g.GDM_ID, t.GDM_ID)
        WHERE 
            t.TRIP_MAPPING_ID = ?
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $tripMappingId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $gdmNumbers[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./css/table-filter.css">
</head>

<body>
    <div id="main-wrapper">
        <?php include 'header.php'; ?>
        <div class="content-body">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="text-center mb-5">GDM NUMBER</h2>
                                <div class="table-responsive filterable mb-5">
                                    <table class="table table-striped tableFixHead" id="booking-table">
                                        <thead>
                                            <tr class="text-center">
                                                <th>#</th>
                                                <th>GDM NUMBER</th>
                                                <th>View Booking Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($gdmNumbers as $index => $gdm): ?>
                                                <tr class="text-center">
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($gdm['GDM_NUMBER']); ?></td>
                                                    <td>
                                                        <a class='a-view-icon' href='viewGDMdetails.php?gdm=<?php echo $gdm["GDM_ID"]; ?>'>
                                                            <i class='material-icons' style='cursor:pointer;'>remove_red_eye</i>
                                                        </a>
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'footer.php'; ?>
    </div>

    <!-- JS & Plugins -->
    <link href=" https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="./js/ddtf.js"></script>
    <script>

    </script>
</body>

</html>