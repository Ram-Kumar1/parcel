<!DOCTYPE html>

<?php
    include 'dbConn.php';
?>

<html lang="en">

<!-- <link rel="stylesheet" href="./css/table-filter.css"> -->

<style>

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
                                                <h2 class="m-t-p5 mb-0">PAGE NAME</h2>
                                            </div>

                                            <button type="button" class="btn btn-info btn-md" onclick="window.location.href='memberView.php'">
                                                <i class="fa fa-eye" aria-hidden="true" style="font-size: medium !important;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="member-name">Member Name: <span class="mandatory-astrick">*</span></label>
                                        <input type="text" class="form-control" id="member-name" placeholder="Enter Name">
                                    </div>

                                    <div class="form-group flex-grow-1 text-center">
                                        <button type="button" class="btn btn-success" onclick="validateAndSaveSalaryDescription()">
                                            <i class="fa fa-floppy-o font-medium" aria-hidden="true"></i>
                                            Save
                                        </button>
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
<script>
    $(document).ready(function() {});
</script>

</html>