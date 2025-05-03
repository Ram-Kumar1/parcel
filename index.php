<?php
session_start();
include 'dbConn.php';

date_default_timezone_set('Asia/Kolkata');
$firstDay = date('Y-m-01');
$lastDay = date('Y-m-t');

if (!isset($_SESSION['userName'])) {
    header("Location: agentLogin.php");
    exit();
}

$whereSql = "";
$userName = $_SESSION['userName'] ?? null;
$branchName = $_SESSION['admin'] ?? null;

if (strtolower($userName) !== 'admin') {
    $whereSql = "AND FROM_BRANCH_ID = '$branchName'";
}

$bookingCount = 0;
$bookingCountSql = "SELECT COUNT(*) AS CNT FROM booking";
if (!empty($whereSql)) {
    $bookingCountSql .= " WHERE 1=1 $whereSql";
}


if ($result = mysqli_query($conn, $bookingCountSql)) {
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $bookingCount = $row['CNT'];
    }
}

// ---------------- Total Amount ----------------
$totalAmount = 0;
$totalAmountSql = "SELECT SUM(TOTAL_AMOUNT) AS TOTAL_AMOUNT FROM booking";
if (!empty($whereSql)) {
    $totalAmountSql .= " WHERE 1=1 $whereSql";
}

if ($result = mysqli_query($conn, $totalAmountSql)) {
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $totalAmount = $row['TOTAL_AMOUNT'];
    }
}
if (empty($totalAmount)) {
    $totalAmount = 0;
}

// ---------------- Shipping Details ----------------
$shipingDetailsSql = "SELECT BD.BOOKING_ID, BD.LR_NUMBER, 
         BD.FROM_BRANCH_ID, BD.TO_BRANCH_ID, BD.BOOKING_DATETIME
         FROM booking BD"; // Fixed table name from 'bookin' to 'booking'

if (strtolower($userName) !== 'admin') {
    $shipingDetailsSql .= " WHERE FROM_BRANCH_ID = '$branchName'";
}

// ---------------- Booking Count by Payment Type ----------------
$sql1 = "SELECT PAYMENT_TYPE, COUNT(*) as count FROM booking GROUP BY PAYMENT_TYPE";
$stmt1 = $conn->prepare($sql1);
$stmt1->execute();
$result1 = $stmt1->get_result();

$countPaid = $countToPay = $countAccount = 0;
while ($row = $result1->fetch_assoc()) {
    switch (strtolower($row['PAYMENT_TYPE'])) {
        case 'paid':
            $countPaid = $row['count'];
            break;
        case 'to_pay':
            $countToPay = $row['count'];
            break;
        case 'account':
            $countAccount = $row['count'];
            break;
    }
}
$stmt1->close();

// ---------------- Total Amount by Payment Type ----------------
$sql2 = "SELECT PAYMENT_TYPE, SUM(TOTAL_AMOUNT) as total FROM booking GROUP BY PAYMENT_TYPE";
$stmt2 = $conn->prepare($sql2);
$stmt2->execute();
$result2 = $stmt2->get_result();

$amountPaid = $amountToPay = $amountAccount = 0;
while ($row = $result2->fetch_assoc()) {
    switch (strtolower($row['PAYMENT_TYPE'])) {
        case 'paid':
            $amountPaid = $row['total'];
            break;
        case 'to_pay':
            $amountToPay = $row['total'];
            break;
        case 'account':
            $amountAccount = $row['total'];
            break;
    }
}
$stmt2->close();
?>


<!DOCTYPE html>
<html lang="en">

<?php
function formatToIndianCurrency($amount)
{
	// Format the amount according to Indian currency format
	return number_format($amount, 0, '.', ',');
}
?>

<link rel="stylesheet" href="./css/table-filter.css">
<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>

<style>
	.fa {
		font-size: xxx-large !important;
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
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-lg-6 col-sm-6">
						<div class="card gradient-1">
							<div class="card-body">
								<h3 class="card-title text-white">Booking</h3>
								<div class="d-inline-block">
									<h2 class="text-white"><?php echo $bookingCount; ?></h2>
									<p>No of parcels <br>booked this month</p>

								</div>
								<span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-sm-6">
						<div class="card gradient-2">
							<div class="card-body">
								<h3 class="card-title text-white">Amount</h3>
								<div class="d-inline-block">
									<h2 class="text-white"><?php echo $totalAmount; ?></h2>
									<p>Total Revenue <br>generated this month</p>

								</div>
								<span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
							</div>
						</div>
					</div>

				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="card">
							<div class="card-body">

								<h3 style="text-align:center;">Total Booking Amount </h3>
								<div id="charts">
									<div id="pieChart" class="chart-container" style="width: 90%; height: 300px;"></div>

									<script>
										anychart.onDocumentReady(function() {
											// --------- Pie Chart (Amount) ---------
											var pieData = [
												["Paid", <?= $amountPaid ?>],
												["To_Pay", <?= $amountToPay ?>],
												["Account", <?= $amountAccount ?>]
											];

											var pieChart = anychart.pie(pieData);
											pieChart.title("");
											pieChart.labels().format("₹{%Value}{groupsSeparator:','}");
											pieChart.tooltip().format("Payment Type: {%X}\nAmount: ₹{%Value}{groupsSeparator:','}");
											pieChart.container("pieChart");
											pieChart.draw();
											// Custom colors for the pie chart

											pieChart.palette(["#4CAF50", "#FFC107", "#F44336"]); // Green, Yellow, Red

										});
									</script>
								</div>
							</div>
						</div>
					</div>



					<div class="col-sm-6">
						<div class="card">
							<div class="card-body">
								<h3 style="text-align:center;">No Of Parcels Booked</h3>

								<!-- Chart container placed inside its own wrapper -->
								<div class="chart-wrapper">
									<div id="barChartContainer" style="width: 90%; height: 310px;"></div>
								</div>

								<script>
									// --------- Horizontal Bar Chart (Booking Count) ---------
									var barData = [{
											x: "PAID",
											value: <?= $countPaid ?>,
											normal: {
												fill: "#E91E63" // Pink
											}
										},
										{
											x: "To Pay",
											value: <?= $countToPay ?>,
											normal: {
												fill: "#FFC107" // Amber
											}
										},
										{
											x: "Account",
											value: <?= $countAccount ?>,
											normal: {
												fill: "#3F51B5" // Indigo
											}
										}
									];

									var barChart = anychart.bar(); // Horizontal bar chart
									barChart.data(barData);
									barChart.title("");

									// Axis labels
									barChart.xAxis().title("Payment Type");
									barChart.yAxis().title("Number of Bookings");

									// Bar labels & tooltip
									barChart.labels().enabled(true);
									barChart.labels().format("{%Value}");
									barChart.tooltip().format("Type: {%X}\nCount: {%Value}");

									// Use the container
									barChart.container("barChartContainer");
									barChart.draw();
								</script>

							</div>
						</div>
					</div>
				</div>

				
			</div>
			<!-- #/ container -->
		</div>

		<div id="refNo-modal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Booking Details</h4>

						<button type="button" class="close text-danger" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="invoice-no">LR Number</label>
									<input type="text" class="form-control" id="lr-number" name="lr-number" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="invoice-no">Status </label>
									<input type="text" class="form-control" id="invoice-status" name="invoice-status"
										readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Customer</label>
									<input type="text" class="form-control" id="customer-name" name="customer-name" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Mobile</label>
									<input type="text" class="form-control" id="customer-mobile" name="customer-mobile" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Delivery To</label>
									<input type="text" class="form-control" id="delivery-to" name="delivery-to"
										readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Delivery Mobile</label>
									<input type="text" class="form-control" id="delivery-mobile" name="delivery-mobile"
										readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">From</label>
									<input type="text" class="form-control" id="from" name="from" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">From Mobile</label>
									<input type="text" class="form-control" id="from-address" name="from-address"
										readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">To</label>
									<input type="text" class="form-control" id="to" name="to" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">To Mobile</label>
									<input type="text" class="form-control" id="to-address" name="to-address" readonly />
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="quantity-details">Quantity Details</label>
									<textarea class="form-control" id="quantity-details" rows="3" readonly></textarea>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Quantity</label>
									<input type="text" class="form-control" id="quantity" name="quantity" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="customer">Quantity Description</label>
									<input type="text" class="form-control" id="quantity-desc" name="quantity-desc"
										readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Transportation Amount</label>
									<input type="number" class="form-control" id="transportation-amount"
										name="transportation-amount" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Loading Amount</label>
									<input type="number" class="form-control" id="loading-amount"
										name="loading-amount" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Additional Amount</label>
									<input type="number" class="form-control" id="additional-amount"
										name="additional-amount" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Total Amount</label>
									<input type="number" class="form-control" id="customer-total"
										name="customer-total" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Goods Value</label>
									<input type="text" class="form-control" id="goods-value"
										name="goods-value" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="total-amount">Delivery Type</label>
									<input type="text" class="form-control" id="notes"
										name="notes" readonly />
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<!--<button type="button" class="btn btn-success" onclick="createPdf()">Create PDF</button>-->
						<button type="button" class="btn btn-primary" data-dismiss="modal">&times; Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--**********************************
            Content body end
        ***********************************-->


	<!--**********************************
            Footer start
        ***********************************-->
	<div class="footer">
		<div class="copyright">
			<p>Copyright &copy; Designed & Developed by <a href="#">ZENITH</a>
				2024</p>
		</div>
	</div>
	<!--**********************************
            Footer end
        ***********************************-->
	</div>
	<!--**********************************
        Main wrapper end
    ***********************************-->

	<!--**********************************
        Scripts
    ***********************************-->
	<script src="plugins/common/common.min.js"></script>
	<script src="js/custom.min.js"></script>
	<script src="js/settings.js"></script>
	<script src="js/gleek.js"></script>
	<script src="js/styleSwitcher.js"></script>

	<!-- Chartjs -->
	<script src="./plugins/chart.js/Chart.bundle.min.js"></script>
	<!-- Circle progress -->
	<script src="./plugins/circle-progress/circle-progress.min.js"></script>
	<!-- Datamap -->
	<script src="./plugins/d3v3/index.js"></script>
	<script src="./plugins/topojson/topojson.min.js"></script>
	<script src="./plugins/datamaps/datamaps.world.min.js"></script>
	<!-- Morrisjs -->
	<script src="./plugins/raphael/raphael.min.js"></script>
	<script src="./plugins/morris/morris.min.js"></script>
	<!-- Pignose Calender -->
	<script src="./plugins/moment/moment.min.js"></script>
	<script src="./plugins/pg-calendar/js/pignose.calendar.min.js"></script>
	<!-- ChartistJS -->
	<script src="./plugins/chartist/js/chartist.min.js"></script>
	<script src="./plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js"></script>

	<script src="./js/dashboard/dashboard-1.js"></script>

	<!-- Select2 Fileter -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<!-- Table Filter -->
	<script src="./js/ddtf.js"></script>
	<script>
		var buildTable = function(sizeArray) {
			var columns = addAllColumnHeaders(sizeArray);
			for (var i = 0; i < sizeArray.length; i++) {
				var row$ = $('<tr/>');
				for (var colIndex = 0; colIndex < columns.length; colIndex++) {
					var cellValue = sizeArray[i][columns[colIndex]];

					if (cellValue == null) {
						cellValue = "";
					}

					row$.append($('<td/>').html(cellValue));
				}
				$("#report-table").append(row$);
			}
		}

		var addAllColumnHeaders = function(sizeArray) {
			var columnSet = [];
			var headerTr$ = $('<tr/>');

			for (var i = 0; i < sizeArray.length; i++) {
				var rowHash = sizeArray[i];
				for (var key in rowHash) {
					if ($.inArray(key, columnSet) == -1) {
						columnSet.push(key);
						headerTr$.append($('<th/>').html(key));
					}
				}
			}

			$("#report-table").append(headerTr$);
			return columnSet;
		};

		$(document).ready(function() {
			let userType = <?php echo "'" . $_SESSION['admin'] . "'"; ?>;
			console.log('userType: ' + userType);
			let userName = <?php echo "'" . $_SESSION['admin'] . "'"; ?>;

			$("#data-table").ddTableFilter();
			$('select').addClass('w3-select');
			$('select').select2();

			$('.refNo-info').click(function() {
				var id = this.id;
				var splitid = id.split('-');
				var samplePiId = splitid[1];
				console.log("samplePiId: ", samplePiId);
				// AJAX request
				$.ajax({
					// url: 'account_transaction.php',
					url: 'dataOperations.php',
					type: 'post',
					data: {
						getBookingDetails: 1,
						samplePiId: samplePiId
					},
					success: function(response) {
						console.log(response);
						$("#modal-title").html('');
						$("#report-table tr").detach();
						let res = JSON.parse(response);
						$("#lr-number").val(res["LR_NUMBER"]);
						$("#invoice-status").val(res["STATUS"]);
						$("#customer-name").val(res["CUSTOMER_NAME"]);
						$("#customer-mobile").val(res["MOBILE"]);
						$("#delivery-to").val(res["DELIVERY_TO"]);
						$("#delivery-mobile").val(res["DELIVERY_MOBILE"]);
						$("#from").val(res["FROM"]);
						$("#from-address").val(res["FROM_MOBILE"]);
						$("#to").val(res["TO"]);
						$("#to-address").val(res["TO_MOBILE"]);
						$("#quantity-details").val(res["QUANTITY_DETAILS"]);
						$("#quantity").val(res["QUANTITY"]);
						$("#quantity-desc").val(res["QUANTITY_DESC"]);
						$("#transportation-amount").val(res["TRANSPORTATION_AMOUNT"]);
						$("#loading-amount").val(res["LOADING_AMOUNT"]);
						$("#additional-amount").val(res["ADDITIONAL_AMOUNT"]);
						$("#customer-total").val(res["TOTAL_AMOUNT"]);
						$("#goods-value").val(res["GOODS_VALUE"]);
						$("#notes").val(res["DELIVERY_TYPE"]);

						// Display Modal
						$('#refNo-modal').modal('show');
					}
				});
			});

			$('.transaction-info').click(function() {
				var id = this.id;
				var splitid = id.split('-');
				var userid = splitid[1];

				// AJAX request
				$.ajax({
					url: 'account_transaction.php',
					type: 'post',
					data: {
						userid: userid
					},
					success: function(response) {
						// Add response in Modal body
						$("#modal-title").html('');
						$("#report-table tr").detach();
						let res = JSON.parse(response);
						$("#modal-title").html($("#custName-" + res[1]).text());
						console.log(res[1]);
						buildTable(res[0]);

						// Display Modal
						$('#myModal').modal('show');
					}
				});
			});

			$('.color-condition').each(function() {
				var $this = $(this);
				var value = $this.text().trim();
				if (value == "Pending") {
					$(this).children().removeClass("fa-check").addClass("fa-times");
					$this.addClass('red');
				} else {

				}
				console.log(value);
			});


			//BOX BUTTON SHOW AND CLOSE
			jQuery('.small-graph-box').hover(function() {
				jQuery(this).find('.box-button').fadeIn('fast');
			}, function() {
				jQuery(this).find('.box-button').fadeOut('fast');
			});
			jQuery('.small-graph-box .box-close').click(function() {
				jQuery(this).closest('.small-graph-box').fadeOut(200);
				return false;
			});

			//CHARTS
			function gd(year, day, month) {
				return new Date(year, month - 1, day).getTime();
			}

			graphArea2 = Morris.Area({
				element: 'hero-area',
				padding: 10,
				behaveLikeLine: true,
				gridEnabled: false,
				gridLineColor: '#dddddd',
				axes: true,
				resize: true,
				smooth: true,
				pointSize: 0,
				lineWidth: 0,
				fillOpacity: 0.85,
				data: [{
						period: '2015 Q1',
						iphone: 2668,
						ipad: null,
						itouch: 2649
					},
					{
						period: '2015 Q2',
						iphone: 15780,
						ipad: 13799,
						itouch: 12051
					},
					{
						period: '2015 Q3',
						iphone: 12920,
						ipad: 10975,
						itouch: 9910
					},
					{
						period: '2015 Q4',
						iphone: 8770,
						ipad: 6600,
						itouch: 6695
					},
					{
						period: '2016 Q1',
						iphone: 10820,
						ipad: 10924,
						itouch: 12300
					},
					{
						period: '2016 Q2',
						iphone: 9680,
						ipad: 9010,
						itouch: 7891
					},
					{
						period: '2016 Q3',
						iphone: 4830,
						ipad: 3805,
						itouch: 1598
					},
					{
						period: '2016 Q4',
						iphone: 15083,
						ipad: 8977,
						itouch: 5185
					},
					{
						period: '2017 Q1',
						iphone: 10697,
						ipad: 4470,
						itouch: 2038
					},

				],
				lineColors: ['#eb6f6f', '#926383', '#eb6f6f'],
				xkey: 'period',
				redraw: true,
				ykeys: ['iphone', 'ipad', 'itouch'],
				labels: ['All Visitors', 'Returning Visitors', 'Unique Visitors'],
				pointSize: 2,
				hideHover: 'auto',
				resize: true
			});
		});
	</script>
	<!-- calendar -->
	<script type="text/javascript" src="js/monthly.js"></script>
	<script type="text/javascript">
		var deleteQuotation = function(span) {
			if ($(span).hasClass("quo-0")) {
				return false;
			} else {
				console.log("quotationId: ", span);
				$.ajax({
					type: 'post',
					url: 'index_backend.php',
					data: {
						quotationId: $(span).attr("data-quoId")
					},
					success: function(response) {
						$("#quotation-" + $(span).attr("data-quoId")).hide();
					}
				});
			}

		};

		$(window).load(function() {
			// var $table = $('table.max-10');
			// $table.floatThead();

			$('#mycalendar').monthly({
				mode: 'event',

			});

			$('#mycalendar2').monthly({
				mode: 'picker',
				target: '#mytarget',
				setWidth: '250px',
				startHidden: true,
				showTrigger: '#mytarget',
				stylePast: true,
				disablePast: true
			});

			switch (window.location.protocol) {
				case 'http:':
				case 'https:':
					// running on a server, should be good.
					break;
				case 'file:':
					alert('Just a heads-up, events will not work when run locally.');
			}

		});
	</script>
	<script>
		$(document).ready(function() {
			$("table").ddTableFilter();
			$('select').addClass('w3-select');
			$('select').select2();
		});
	</script>

</body>

</html>