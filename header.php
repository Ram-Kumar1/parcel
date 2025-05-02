<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'dbConn.php';

// require_once 'CaesarCipher.php';
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- theme meta -->
    <meta name="theme-name" content="quixlab" />

    <title>Zenith</title>

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Pignose Calender -->
    <link href="./plugins/pg-calendar/css/pignose.calendar.min.css" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="./plugins/chartist/css/chartist.min.css">
    <link rel="stylesheet" href="./plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css">
    <!-- Custom Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Font Awsome -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> -->

    <!-- Select2 Fileter -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS -->

    <script src="./js/encryptNum.js"></script>
    <style>
        button {
            color: white !important;
        }

        label {
            color: black;
        }

        th {
            background: #7676ff;
            color: white;
        }

        .m-50 {
            margin-left: 50%;
        }

        .m-t-p5 {
            margin-top: 0.5em;
        }

        .m-45 {
            margin-left: 45%;
        }

        .mandatory-astrick {
            color: red !important;
        }

        .header {
            background: #f6f8ff !important;
            text-align: center !important;
        }

        .a-edit-icon {
            color: #5352ed !important;
        }

        .a-delete-icon {
            color: #ff4757 !important;
        }

        .a-view-icon {
            color: #00c000 !important;
        }

        .font-medium {
            font-size: medium !important;
        }

        .a-hover {
            cursor: pointer;
        }

        /* Hide spin buttons in Chrome, Safari, and newer versions of Edge */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
            pointer-events: none;
        }

        .logo {
            margin-top: -5%;
            margin-left: -5%;
        }

        .select2 select2-container {
            width: 100% !important;
        }

        @media print {
            .p {
                display: initial;
            }

            .np {
                display: none;
            }

            a[href]:after {
                display: none;
            }
        }

        @media print {

            .col-sm-1,
            .col-sm-2,
            .col-sm-3,
            .col-sm-4,
            .col-sm-5,
            .col-sm-6,
            .col-sm-7,
            .col-sm-8,
            .col-sm-9,
            .col-sm-10,
            .col-sm-11,
            .col-sm-12 {
                float: left;
            }

            .col-sm-12 {
                width: 100%;
            }

            .col-sm-11 {
                width: 91.66666667%;
            }

            .col-sm-10 {
                width: 83.33333333%;
            }

            .col-sm-9 {
                width: 75%;
            }

            .col-sm-8 {
                width: 66.66666667%;
            }

            .col-sm-7 {
                width: 58.33333333%;
            }

            .col-sm-6 {
                width: 50%;
            }

            .col-sm-5 {
                width: 41.66666667%;
            }

            .col-sm-4 {
                width: 33.33333333%;
            }

            .col-sm-3 {
                width: 25%;
            }

            .col-sm-2 {
                width: 16.66666667%;
            }

            .col-sm-1 {
                width: 8.33333333%;
            }
        }
    </style>

</head>
<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <div class="brand-logo">
        <a href="" onclick="event.preventDefault(); javascript:navigateToIndexPage();">
            <b class="logo-abbr"><img src="images/Zenith_Logo_Compact.png" alt=""> </b>
            <span class="logo-compact"><img src="./images/logo-compact.png" alt=""></span>
            <!-- <span class="brand-title">
                <img src="images/logo-text.png" alt="">
            </span> -->
            <span class="brand-title">
                <img class="logo" src="images/ZENITH_LOGO_1.png" alt="">
                <!-- <b class="text-light">COURIER</b> -->
            </span>
        </a>
    </div>
</div>
<!--**********************************
            Nav header end
        ***********************************-->

<!--**********************************
            Header start
        ***********************************-->
<div class="header">
    <div class="header-content clearfix">

        <div class="nav-control">
            <div class="hamburger">
                <span class="toggle-icon"><i class="icon-menu"></i></span>
            </div>
        </div>

        <div class="header-right">
            <ul class="clearfix">
                <li class="icons dropdown">

                    <div class="user-img c-pointer position-relative" data-toggle="dropdown">
                        <span class="activity active"></span>
                        <img src="images/Zenith_Logo_Compact.png" height="40" width="40" alt="">
                    </div>

                    <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                        <div class="dropdown-content-body">
                            <ul>
                                <!-- <li>
                                    <a href="app-profile.html"><i class="icon-user"></i>
                                        <span>Profile</span></a>
                                </li> -->

                                <li>
                                    <b><i class="icon-user"></i>
                                        <span><?php echo $_SESSION['userName'] ?? "GUEST"; ?></span></b>
                                </li>
                                <li id="change-password">
                                    <a href="changePassword.php">
                                        <i class="fa fa-unlock-alt font-medium menu-icon"></i> Change Password
                                    </a>
                                </li>
                                <li>
                                    <a class="a-hover" onclick="logOff()">
                                        <i class="icon-key"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--**********************************
            Header end ti-comment-alt
        ***********************************-->

<!--**********************************
            Sidebar start
        ***********************************-->
<div class="nk-sidebar">
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li class="sub-menu for-marketing" id="admin-list" style="display: none;">
                <a href="javascript:navigateToIndexPage()" aria-expanded="false" class="has-arrow">
                    <i class="fa fa-user-plus font-medium menu-icon"></i><span class="nav-text">Admin</span>
                </a>
                <ul class="sub">
                    <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Office Management</span>
                        </a>
                        <ul class="sub">
                            <!-- <li><a href="placeEntry.php">Place</a></li> -->
                            <li><a href="branchOffice.php">Add Route</a></li>
                            <li><a href="branchOfficeView.php">View Route</a></li>
                            <li><a href="addDriver.php">Add Driver</a></li>
                            <li><a href="addhub.php">Add Hub</a></li>
                            <!-- <li><a href="gstAddUpdate.php">Add GST</a></li> -->
                        </ul>
                    </li>
                    <!-- Customer Panel -->
                    <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Booking Management</span>
                        </a>
                        <ul class="sub">
                            <li><a href="addCustomer.php">Add Customer</a></li>
                            <li><a href="addItems.php">Add Items</a></li>
                        </ul>
                    </li>
                    <!-- Accounts Panel -->
                    <!-- <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Accounts Management</span>
                        </a>
                        <ul class="sub">
                            <li><a href="addCustomer.php">Add Customer</a></li>
                        </ul>
                    </li> -->
                </ul>
            </li>



            <li class="sub-menu for-marketing" id="booking-list">

                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-book font-medium menu-icon"></i> <span class="nav-text"> Booking</span>
                </a>
                <ul class="sub">
                    <li><a href="newBooking.php">New Booking</a></li>
                    <li><a href="viewBookingList.php">Booking List</a></li>
                    <li><a href="createGDM.php">Create GDM</a></li>
                    <li><a href="viewGDM.php">View GDM</a></li>
                    <li id="cbe-hub-li"><a href="trip.php">Create Trip</a></li>
                    <li><a href="viewTrip.php">View Trip</a></li>
                    <li><a href="shipInward.php">Ship Inward</a></li>
                    <!-- <li><a href="production_Flow_report.php">Report</a></li> -->
                </ul>
            </li>

            <li class="sub-menu for-marketing d-none" id="report-list">

                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-university font-medium menu-icon"></i> <span class="nav-text">Report</span>
                </a>
                <ul class="sub">
                    <li><a href="reportCountWise.php">Count - Brach Wise</a></li>
                    <li><a href="reportPaymentWise.php">Payment - Branch Wise</a></li>
                </ul>
            </li>
            <li class="sub-menu for-marketing" id="accounts-list">

                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-inr font-medium menu-icon"></i> <span class="nav-text">Accounts</span>
                </a>
                <ul class="sub d-none" id="admin-accounts">
                    <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Admin Accounts</span>
                        </a>
                        <ul class="sub">
                            <li><a href="accounts.php">Accounts</a></li>
                            <li><a href="BranchOutstandingAccounts.php">Outstanding Accounts</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="sub">
                    <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Branch</span>
                        </a>
                        <ul class="sub">
                            <li><a href="BranchAccount.php">Branch Accounts</a></li>
                            <li><a href="settled_account.php">Settled Accounts</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="sub">
                    <li class="sub-menu">
                        <a href="javascript:;" class="has-arrow">
                            <i class="fa fa-bullhorn font-medium menu-icon"></i>
                            <span>Customer</span>
                        </a>
                        <ul class="sub">
                            <li><a href="customer_account.php">Customer Account</a></li>
                            <li><a href="customer_transaction.php">Transactions</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="sub-menu for-marketing d-none" id="report-list">

                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                    <i class="fa fa-money font-medium menu-icon"></i> <span class="nav-text">Customer Account</span>
                </a>
                <ul class="sub">
                    <li><a href="customer_account.php">Account</a></li>
                    <li><a href="customer_transaction.php">Customer Transaction</a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!--**********************************
            Sidebar end
        ***********************************-->
<script src="./js/ddtf.js"></script>
<script>
    function navigateToIndexPage() {
        window.location.href = "index.php";
    }

    function logOff() {
        debugger;
        window.location.href = "logOff.php"
    };

    $(document).ready(function() {
        $("#chit-management").click();
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(".fa-bars").click();
        let userType = <?php echo "'" . $_SESSION['admin'] . "'"; ?>;
        console.log('userType: ' + userType);
        let userName = <?php echo "'" . $_SESSION['userName'] . "'"; ?>;

        $('#user-type').text(userName.toString().toUpperCase());
        if (userName.toLowerCase().includes("admin")) {
            $("#admin-list").show();
            $("#production-list").show();
            $("#report-list").show();
            $("#admin-accounts").removeClass("d-none");
        } else {
            $("#admin-list").hide();
            $("#report-list").hide();
            $("#production-list").show();
        }

        if (userName.toLowerCase().includes("ganapathy") || userName.toLowerCase().includes("admin")) {
            $("#cbe-hub-li").show();
        } else {
            $("#cbe-hub-li").hide();
        }
        $("#sidebar").show();
    });
</script>