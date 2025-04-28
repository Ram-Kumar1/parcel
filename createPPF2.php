<?php

include 'dbConn.php';

function getIndianCurrency($number)
{
    // $decimal = round($number - ($no = floor($number)), 2) * 100;
    $no = ceil($number);
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    // $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    // return ($Rupees ? 'INR ' . $Rupees . ' only' : '') . $paise;
    return ($Rupees ? 'INR ' . $Rupees . ' only' : '');
}

if (isset($_GET['lr_Id'])) {
    $lrNumber = $_GET['lr_Id'];
    $invoiceDetails = "SELECT * FROM booking WHERE BOOKING_ID = $lrNumber";
    $invoiceResult = mysqli_query($conn, $invoiceDetails);
    while ($row = mysqli_fetch_array($invoiceResult)) {

        $fromPlace = $row['FROM_NAME'];
        $fromMobile = $row['FROM_MOBILE'];
        $toPlace = $row['TO_NAME'];
        $toMobile = $row['TO_MOBILE'];
        $bookingDate = $row['BOOKING_DATETIME'];
        $paymentType = $row['PAYMENT_TYPE'];
        $transportationCost = $row['TOTAL_AMOUNT'];
        $quantityDetails = $row['ITEMS'];
        $TplaceId = '';
        $FplaceId = '';

        $lrnumber = $row['LR_NUMBER'];

        date_default_timezone_set('Asia/Kolkata');
        $date_1 =  date('d-m-Y H:i');
        $curDate = date('Y-m-d', strtotime($date_1));
    }
    $quantityDetailsArr = json_decode($quantityDetails, true);

    $branchPhNoQuery = "SELECT MOBILE FROM branches WHERE BRANCH_ID = '$TplaceId'";
    $branchPhNoResult = mysqli_query($conn, $branchPhNoQuery);
    while ($row = mysqli_fetch_array($branchPhNoResult)) {
        $branchPhNo = $row['MOBILE'];
    }

    $branchPhNoQuery = "SELECT CITY, MOBILE, ALTERNATE_MOBILE FROM branches WHERE BRANCH_ID = '$FplaceId'";
    $branchPhNoResult = mysqli_query($conn, $branchPhNoQuery);
    while ($row = mysqli_fetch_array($branchPhNoResult)) {
        $fromBranchMobile = $row['MOBILE'] ?? '';
        $fromBranchAlternateMobile = $row['ALTERNATE_MOBILE'];
        $fromBranchPlace = $row['CITY'];
    }
    $fromPlaceMobile = '';
    if (empty($fromBranchAlternateMobile)) {
        $fromPlaceMobile  = $fromBranchMobile ?? '';
    } else {
        $fromPlaceMobile = $fromBranchMobile . " / " . $fromBranchAlternateMobile;
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <style>
        .red {
            color: red !important;
        }

        th {
            background: white !important;
        }

        .m-1 {
            /* margin: 1px; */
            margin-top: 3em;
        }

        .max-30 {
            max-width: 15em;
        }

        /* .b-r {
            border-right: 1px solid;
        } */

        td {
            color: black !important;
            border-color: black;
            margin-top: 0.5em;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        table.table-bordered {
            border: 1px solid #3d3d3d ! important;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid #3d3d3d ! important;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid #3d3d3d ! important;
            border-bottom: 1px solid #3d3d3d ! important;
        }

        min-hgt {
            height: 2px;
        }

        .table>thead>tr>th {
            padding: 0px !important;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            line-height: 1.42857143 !important;
        }

        .table td,
        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 1px !important;
        }

        html,
        body {
            font-size: 95% !important;
        }

        button.btn.m-2 {
            margin-top: 0.7em;
        }

        table {
            font-family: 'fangsong';
            font-size: 1.5em;
            border-collapse: collapse !important;
        }

        /* Apply this to your `table` element. */
        .page {
            border-collapse: collapse !important;
        }

        /* And this to your table's `td` elements. */
        .page td {
            padding: 0 !important;
            margin: 0 !important;
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

            <?php
            include 'header.php';
            ?>

            <!--**********************************
            Content body start
        ***********************************-->
            <div class="content-body">

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <section class="wrapper">
                                        <div class="form-w3layouts">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <button type="button" class="btn btn-success pull-left m-2" onclick="CreatePDFfromHTML('<?php echo $fromPlace; ?>')">
                                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                        Export PDF
                                                    </button>
                                                    <button type="button" class="btn btn-success pull-left m-2" onclick="printDiv('print-div')" style="margin-left: 5px;">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                        Print
                                                    </button>

                                                    <button type="button" name="button" class="btn btn-info pull-right m-2" onclick="location.href = 'viewBookingList.php';">
                                                        <i class="fa fa-backward" aria-hidden="true" style="color: black"></i>
                                                        Back
                                                    </button>&nbsp;
                                                    <!-- <button type="button" name="button" id="email-btn" class="btn btn-primary pull-right m-2" style="margin-right: 5px;" data-toggle="modal" data-target="#email-modal">
                                                  
                                                        <i class="fa fa-envelope-o" aria-hidden="true" style="color: white"></i>
                                                        Mail
                                                    </button>&nbsp; -->
                                                </div>
                                            </div>

                                            <!-- page start-->
                                            <!-- page start-->
                                            <div class="row html-content m-1">
                                                <div class="col-lg-12">
                                                    <section class="panel">
                                                        <div id="print-div">
                                                            <!-- 1st Copy -->
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless page" cellspacing="0" cellpadding="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="text-align: center;">
                                                                                <h2>
                                                                                    <b contenteditable="true">ZENITH TRAVELS & SPEED PARCEL SERVICE</b>
                                                                                </h2>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center;">
                                                                                <h3>
                                                                                    <b contenteditable="true" id="fromBranchPlace">
                                                                                        <?php echo $fromBranchPlace ?? 'null' . " / " . $fromPlace ?? 'null' ?>
                                                                                    </b>
                                                                                </h3>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center;">
                                                                                <h3>
                                                                                    <b contenteditable="true" id="fromPlaceMobile" oninput="validateMobile(this)">
                                                                                        <?php echo $fromPlaceMobile ?? 'null'; ?>
                                                                                    </b>
                                                                                </h3>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="table-responsive" style="margin-top:10px; width: 100%;">
                                                                <table class="table table-bordered table-sm page">
                                                                    <tbody>
                                                                        <tr class="text-center">
                                                                            <td>
                                                                                <span>Date: </span>
                                                                                <span contenteditable="true" id="bookingDate">
                                                                                    <?php echo date("d-m-Y", strtotime($bookingDate)) ?? ''; ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <span>LR.NO: </span>
                                                                                <b contenteditable="true" id="lrnumber"><?php echo $lrnumber; ?></b>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="text-center">
                                                                            <td>
                                                                                <span>Sender: </span>
                                                                                <b contenteditable="true" id="customerName"><?php echo $fromPlace; ?></b><br>
                                                                                <b contenteditable="true" id="mobile" oninput="validateMobile(this)"><?php echo $fromMobile; ?></b>
                                                                            </td>
                                                                            <td>
                                                                                <span>Receiver: </span>
                                                                                <b contenteditable="true" id="deliveryTo"><?php echo $toPlace; ?></b><br>
                                                                                <b contenteditable="true" id="deliveryMobile" oninput="validateMobile(this)"><?php echo $toMobile; ?></b>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="text-center">
                                                                            <td>
                                                                                <table class="table table-borderless" style="font-size: large; border: 0px solid white !important;">
                                                                                    <tbody>
                                                                                        <tr class="text-center">
                                                                                            <td contenteditable="true" id="quantityDetails">
                                                                                                <table border="1" style="width: 100%; border-collapse: collapse;">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th style="color: black;">Particular</th>
                                                                                                            <th style="color: black;">UOM</th>
                                                                                                            <th style="color: black;">Quantity</th>
                                                                                                            <th style="color: black;">Rate</th>
                                                                                                            <th style="color: black;">Weight</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <?php foreach ($quantityDetailsArr as $item): ?>
                                                                                                            <tr>
                                                                                                                <td><?php echo htmlspecialchars($item['particular']); ?></td>
                                                                                                                <td><?php echo htmlspecialchars($item['uom']); ?></td>
                                                                                                                <td><?php echo htmlspecialchars($item['qty']); ?></td>
                                                                                                                <td><?php echo htmlspecialchars($item['rate']); ?></td>
                                                                                                                <td><?php echo htmlspecialchars($item['weight']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endforeach; ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </td>

                                                                                        </tr>
                                                                                        <!-- <tr>
                                                                                            <td>
                                                                                                <span>Total: </span>
                                                                                                <b contenteditable="true" id="quantity"><?php echo "quantity"; ?></b>
                                                                                            </td>
                                                                                        </tr> -->
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                            <td style="text-align: center; vertical-align: middle;">
                                                                                <span>Payment Type: </span><br>
                                                                                <h3><b contenteditable="true" id="paymentType"><?php echo $paymentType; ?></b></h3>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="text-align: center; vertical-align: middle;">
                                                                                <span>Delivery Place: </span><br>
                                                                                <h2>
                                                                                    <b contenteditable="true" id="toPlace"><?php echo $toPlace; ?></b>
                                                                                </h2>
                                                                                <br>
                                                                                <span>Delivery Number: </span><br>

                                                                                <b contenteditable="true" id="toMobile" oninput="validateMobile(this)"><?php echo $toMobile; ?></b>
                                                                            </td>
                                                                            <td style="text-align: center; vertical-align: middle;">
                                                                                <span>Amount: </span>
                                                                                <h2><b contenteditable="true" id="transportationCost"><?php echo "₹" . $transportationCost; ?></b></h2>
                                                                                <br>
                                                                                <span class="text-center">
                                                                                    <span>Delivery Type: </span><br>
                                                                                    <b contenteditable="true" id="deliveryType"><?php echo "deliveryType"; ?></b>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <!-- 
                                                            <div class="text-center mt-3">
                                                                <button class="btn btn-primary" id="saveChanges">Save Changes</button>
                                                                <button class="btn btn-secondary" id="resetChanges">Reset</button>
                                                            </div> -->

                                                            <br><br><br><br><br>
                                                        </div>
                                                    </section>
                                                </div>

                                                <!-- Modal -->
                                                <div id="email-modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog modal-md">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                <h4 class="modal-title"><i class="fa fa-envelope" aria-hidden="true"></i> Send Email to Customer</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="mailAttachment.php" method="post" enctype="multipart/form-data">
                                                                    <div class="row" style="margin: 1em">
                                                                        <input type="hidden" class="form-control" name="custName" id="custName" value="<?php echo $customerName; ?>" />
                                                                        <input type="hidden" class="form-control" name="for_detail" value="pi" />

                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <input type="hidden" class="form-control" name="invoiceId" id="invoiceId" value="<?php echo $lrNumber; ?>" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="usr">To:</label>
                                                                                <input type="email" class="form-control" name="to" id="to" required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="usr">Cc:</label>
                                                                                <textarea class="form-control" rows="3" cols="60" name="cc" id="cc"> </textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="usr">Subject:</label>
                                                                                <input type="text" class="form-control" placeholder="Enter Your Subject" name="subject" id="subject" required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="usr"><i class="fa fa-paperclip"></i> Attachment:</label>
                                                                                <input id="file-upload" class="form-control" type="file" name="file" required />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-group">
                                                                                <label for="usr">Mail Content:</label>
                                                                                <textarea class="form-control" placeholder="Enter Your Message" rows="5" cols="60" name="message" id="message"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <button type="submit" class="btn btn-success pull-right" name="submit"><i class="fa fa-share-square-o" aria-hidden="true"></i> Send Mail</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                function validateMobile(element) {

                                                    let value = element.textContent.replace(/\D/g, '');
                                                    if (value.length > 10) {
                                                        value = value.substring(0, 10);
                                                    }
                                                    element.textContent = value;
                                                    if (value.length !== 10) {
                                                        element.style.borderBottom = "2px solid red";
                                                    } else {
                                                        element.style.borderBottom = "";
                                                    }
                                                }
                                                const originalValues = {};
                                                document.querySelectorAll('[contenteditable="true"]').forEach(el => {
                                                    originalValues[el.id || el.textContent.trim()] = el.textContent.trim();
                                                });
                                                document.getElementById('saveChanges').addEventListener('click', function() {

                                                    const mobileFields = ['fromPlaceMobile', 'mobile', 'deliveryMobile', 'toMobile'];
                                                    let isValid = true;

                                                    mobileFields.forEach(fieldId => {
                                                        const field = document.getElementById(fieldId);
                                                        if (field && field.textContent.trim().length !== 10) {
                                                            field.style.borderBottom = "2px solid red";
                                                            isValid = false;
                                                        }
                                                    });

                                                    if (!isValid) {
                                                        alert("Please enter valid 10-digit mobile numbers in all mobile fields!");
                                                        return;
                                                    }

                                                    const changes = {};
                                                    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
                                                        const id = el.id || el.textContent.trim();
                                                        changes[id] = el.textContent.trim();
                                                    });

                                                    console.log('Changes to save:', changes);
                                                    alert('Changes saved successfully!');
                                                });

                                                document.getElementById('resetChanges').addEventListener('click', function() {
                                                    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
                                                        const id = el.id || el.textContent.trim();
                                                        if (originalValues[id]) {
                                                            el.textContent = originalValues[id];
                                                            el.style.borderBottom = "";
                                                        }
                                                    });
                                                    alert('Changes reset to original values!');
                                                });
                                            </script>
                                    </section>
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
    <script>
        var a = ['', 'one ', 'two ', 'three ', 'four ', 'five ', 'six ', 'seven ', 'eight ', 'nine ', 'ten ', 'eleven ', 'twelve ', 'thirteen ', 'fourteen ', 'fifteen ', 'sixteen ', 'seventeen ', 'eighteen ', 'nineteen '];
        var b = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        $("#email-modal").on("show.bs.modal", function(e) {
            // var toReceipt = <?php echo "" ?>;
            var ccReceipt = "ram.hamel@gmail.com";
            var subject = "JP Travels - INVOICE COPY";
            var message = "Dear sir,\n\n" +
                "Good Day.. " +
                "\n\nPLEASE FIND THE ATTACHMENT OF INVOICE." +
                "\n\nPlease do feel free to revert for any details/clarification. We will be pleased to give the details." +
                "\n\nLooking forward to receive your valuable order at the earliest." +
                "\n\nThanking you and assuring you our best attention at all time." +
                "\n\nOur GST Details: 33MVVPS1642A1ZA" +
                "\n\n\nThanks & Best Regards," +
                // "\nM.SRINIVAS" +
                "\nJP TRAVELS," +
                "\n9585099110" +
                "\n9585099112";
            /* +
            "\n448-B,SRT Complex," +
            "\nSathy Road,Ganapathy," +
            "\nCoimbatore – 641006." +
            "\nTamil Nadu, \nIndia." +
            "\n\nMOBILE : +91-8124821191." +
            "\nEMAIL : salesbkhydraulics@gmail.com"; */
            $("#subject").val(subject);
            $("#message").val(message);
            // $("#to").val(toReceipt);
            // $("#to").val(toReceipt == 'sample@gmail.com' ? '' : toReceipt);
            // $("#cc").val(ccReceipt);
        });

        function inWords(num) {
            if ((num = num.toString()).length > 9) return 'overflow';
            n = ('000000000' + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);
            if (!n) return;
            var str = '';
            str += (n[1] != 0) ? (a[Number(n[1])] || b[n[1][0]] + ' ' + a[n[1][1]]) + 'crore ' : '';
            str += (n[2] != 0) ? (a[Number(n[2])] || b[n[2][0]] + ' ' + a[n[2][1]]) + 'lakh ' : '';
            str += (n[3] != 0) ? (a[Number(n[3])] || b[n[3][0]] + ' ' + a[n[3][1]]) + 'thousand ' : '';
            str += (n[4] != 0) ? (a[Number(n[4])] || b[n[4][0]] + ' ' + a[n[4][1]]) + 'hundred ' : '';
            str += (n[5] != 0) ? ((str != '') ? 'and ' : '') + (a[Number(n[5])] || b[n[5][0]] + ' ' + a[n[5][1]]) + 'only ' : '';
            return str;
        }

        // var updateDetails = function(id) {
        function updateDetails(id) {
            var conform = confirm("Sure to create?");
            if (!conform) {
                return;
            }
            //window.location.href='createPPF.php?id='+id; 

        };

        // var sendEmail = function() {
        function sendEmail() {
            $.ajax({
                type: 'post',
                url: 'mail.php',
                data: {
                    sendMail: 1,
                    message: $('#print-div').html()
                },
                success: function(response) {
                    alert("Mail Sent Successfully!");
                    //   window.location.href = "viewQuotationPDF.php";
                }
            });
        };

        // function CreatePDFfromHTML(custName) {
        //     var HTML_Width = $(".html-content").width();
        //     var HTML_Height = $(".html-content").height();
        //     var top_left_margin = 15;
        //     var PDF_Width = HTML_Width + (top_left_margin * 2);
        //     var PDF_Height = (PDF_Width * 1.5) + (top_left_margin * 2);
        //     var canvas_image_width = HTML_Width;
        //     var canvas_image_height = HTML_Height;

        //     var totalPDFPages = Math.ceil(HTML_Height / PDF_Height) - 1;

        //     html2canvas($(".html-content")[0]).then(function(canvas) {
        //         var imgData = canvas.toDataURL("image/jpeg", 1.0);
        //         var pdf = new jsPDF('p', 'pt', [PDF_Width, PDF_Height]);
        //         pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin, canvas_image_width, canvas_image_height);
        //         for (var i = 1; i <= totalPDFPages; i++) {
        //             pdf.addPage(PDF_Width, PDF_Height);
        //             pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height * i) + (top_left_margin * 4), canvas_image_width, canvas_image_height);
        //         }
        //         pdf.save(custName + ".pdf");
        //         $("#email-btn").attr("disabled", false);
        //     });

        // }

        // var printDiv = function(divName) {
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        };

        $(document).ready(function() {
            // $(".fa-bars").click();
            setTimeout(function() {
                printDiv('print-div');
            }, 2000);
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        const {
            jsPDF
        } = window.jspdf;

        function CreatePDFfromHTML(custName) {

            $("#email-btn").attr("disabled", true);


            const element = document.getElementById("print-div");


            const options = {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                logging: true,
                letterRendering: true
            };

            html2canvas(element, options).then(function(canvas) {

                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 210;
                const pageHeight = 295;
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                const pdf = new jsPDF('p', 'mm');
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;


                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }
                pdf.save(custName.replace(/[^a-z0-9]/gi, '_') + '.pdf');

                $("#email-btn").attr("disabled", false);
            }).catch(function(error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please check console for details.');
                $("#email-btn").attr("disabled", false);
            });
        }
    </script>

    </html>
<?php } ?>