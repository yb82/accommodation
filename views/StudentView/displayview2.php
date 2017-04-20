
<?php
// include html header and display php-login message/error
include ('./_header.php');

?>


<link href="./Metro/ui/css/metro.css" rel="stylesheet">


<script type="text/javascript" src="../jquery/jquery-1.9.1.js"></script>
<script type="text/javascript"
	src="../jquery/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript"
	src="../jquery/jquery-ui-1.10.3.custom.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>

<link type="text/css" href="../datepicker/jquery.datepick.css"
	rel="stylesheet" />
<script type="text/javascript" src="../timepicker/jquery.timepicker.js"></script>

<script type="text/javascript" src="../datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="../timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css"
	href="../timepicker/jquery.timepicker.css" />


<script src="./Metro/ui/js/metro.js"></script>



<script type="text/javascript">
$(function() {
	//('#').datepick({minDate: '-100y'});
	$('#popupDatepicker').datepick({yearRange: 'c+0:c-100',dateFormat: 'dd/mm/yyyy'});
	$('#checkin1').datepick({minDate: +2,dateFormat: 'dd/mm/yyyy'});
	//$('#popupDatepicker').datepick({minDate: -1});
	
	$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'}); 
	$('#timepick').timepicker({'step':10,timeFormat: 'H:i'});
	
	
	
	$("#boform").submit(function(e) {
		
	   
	   	 alert("Your form has been submitted! Please OK button");
		return true;
	    
	});	
});
</script>
<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>

</section>
<section id="page_template" class="display_inner_content_body_wrapper">
	<div class="inner_content_body">

		<div class="">

			<div class="gform_wrapper display">
      <?php
						$display = new DisplayStudent ();
						
						?>
<div class="display_right_menu">

<?php

echo $phplogin_lang ['You are logged in as'] . '<b>' . $_SESSION ['user_email'] . '</b>&nbsp;&nbsp;&nbsp;&nbsp;';

echo '<img src="../images/logout_icon02.gif">&nbsp;&nbsp;<a href="login.php?logout">' . $phplogin_lang ["Logout"] . '</a>';

?>
</div>


				<form method="post" action="update.php" class="boform"
					id="boform" name="boform">
					<input type="hidden" name='student_changes'
						value='1' readonly />
					
					<input type="hidden" name='ref_num'
						value=<?php echo $display->getReferenceNumber();?> readonly />
					<input type="hidden" name="status"
						value=<?php echo $display->getBookingStatus();?> readonly /> 
						<input	type="hidden" name='booking_id'
						value=<?php echo $display->getID();?> readonly />
						<input	type="hidden" name='fee_added'
						value=<?php echo $display->getFeeAdded();?> readonly />
						<input	type="hidden" name='gender'
						value=<?php echo $display->getGender();?> readonly />
						<input	type="hidden" name='email'
						value=<?php echo $display->getEmail();?> readonly />
						<input	type="hidden" name='nationality'
						value=<?php echo $display->getNationality();?> readonly />
						
						
						
					<div class="accordion large-heading" data-role="accordion">
						

						<div class="frame active">
							<div class="heading">Booking Information</div>
							<div class="content">
							
						
									<div class="gform_group_wrapper">
									<div class="gfield gsection">
										<img src="../images/join_arrow.gif"
											style="vertical-align: middle">&nbsp;<b>Booking information </b>
									</div>
									<div class="checkin date">
										<ul class="full_gform_fields">
											<li class="gfield"><label class="gfield_label"> Room</label>
											<?php echo $display->getRoomType();?></li>
											<li class="gfield"><label class="gfield_label"> Weeks</label>
												<?php echo $display->getWeeks(); ?> </li>
			
											<li class="gfield"><label class="gfield_label"> Check in date</label>
												<?php echo $display->getCheckInDate(); ?></li>
											<li class="gfield_option">
											
											
										</ul>
									</div>
								</div>
								
								<div class="gform_group_wrapper">
									<div class="gfield  gsection">
										<img src="../images/join_arrow.gif"
											style="vertical-align: middle">&nbsp;<b>Entry Codes</b>
									</div>
								
									<div class="checkin date">
									<ul class="full_gform_fields">

										<li class="gfield"><label class="gfield_label"> Bed Number</label>
										<input type="text" name='bed_code' value=<?php echo $display->getBedCode();?> readonly /> </li>
										
								</ul>
								</div>
									<div style="text-align: center;"><img class="bo" src="../images/roomcode_info1.png" style="max-width:100%;max-height:100%;margin-left: 10px; padding-left: 5px;"></img></div>
									<br/><br/>
								<div class="checkin date">
									<ul class="full_gform_fields">
										
										<li class="gfield"><label class="gfield_label"> Room Entry Code </label>
										
										<input type="text" name='room_code' value=<?php echo $display->getRoomCode(); ?> readonly />	</li>

										<li class="gfield"><label class="gfield_label"> Front Door Code </label>
										<input type="text" name='front_code' value=<?php echo $display->getFrontDoorCode(); ?>	readonly /></li>
										<li class="gfield"><label class="gfield_label">	Gate Code </label>
											
										<input type="text" name='gate_code' value=<?php echo $display->getPGateCode(); ?> readonly />
										
										
										
										
										</li>

									</ul>
								</div>
								<div style="text-align: center;"><img src="../images/gate_info.png"  ></img></div>
								</div>

								<!-- Room Information -->
							
								
							</div>

						</div>
						<div class="frame " >
							<div class="heading">Student Profile</div>
							<div class="content">

								<!-- student information area -->
								<div class="gform_group_wrapper">
									<div class="gfield  gsection">
										<img src="../images/join_arrow.gif"
											style="vertical-align: middle">&nbsp;<b>Student information </b>
									</div>


									<div class="gform_group_pane_right ">
										<ul class="gform_fields">

										</ul>

									</div>
									<div class="gform_group_pane_left">

										<ul class="gform_fields">

											<li class="gfield"><label class="gfield_label"> Student ID</label><input
												type="text" name='st_id'
												value=<?php echo $display->getStudentID(); ?> /></li>
											<li class="gfield gfield_contains_"><label
												class="gfield_label"> First name </label><input type="text"
												name='f_name'
												value=<?php echo $display->getStudentFName();?> /></li>
											<li class="gfield"><label class="gfield_label">Last name</label><input
												type="text" name='l_name'
												value=<?php echo $display->getStudentLName(); ?> /></li>
											<li class="gfield"><label class="gfield_label"> Contact </label>
												<input type="text" name="contact"
												value=<?php echo $display->getContact() ?> /></li>
											<li class="gfield"><label class="gfield_label"> Date of birth
											</label> <input type="text" name="dob" id="popupDatepicker"
												value=<?php echo $display->getDOB() ?> /></li>
											<li class="gfield"><label class="gfield_label"> Gender </label>
											<?php echo $display->getStudentGender();?>
											</li>
										</ul>
									</div>
								</div>

							

							</div>
						</div>
						
						<div class="frame ">
							<div class="heading">Pickup Information</div>
							<div class="content">
								<!-- Airport Pick Up -->
								<div class="gform_group_wrapper">

									<div class="gfield gsection">
										<img src="../images/join_arrow.gif"
											style="vertical-align: middle">&nbsp;<b>Airport Pick Up</b>
									</div>
									<div>
										<ul class="full_gform_fields">
											<li class="gfield_option">
											<li class="gfield"><label class="gfield_label"> Airport
											</label>
			<?php echo $display->getAirport(); ?>
			</li>
											<li class="gfield"><label class="gfield_label"> Arrival date
											</label><?php echo $display->getArrivalDate();?></li>
											<li class="gfield"><label class="gfield_label"> Arrival time
											</label> <input type="text" name="arrival_time" id="timepick"
												value=<?php echo $display->getArrivalTime();?> /></li>
											<li class="gfield"><label class="gfield_label"> Flight number
											</label> <input type="text" name="f_num" id="f_num"
												value=<?php echo $display->getFlightNo()?> /></li>
											<li class="gfield"><label class="gfield_label"> Enter comment
											</label> <input type="text" name="comment"
												value=<?php echo $display->getComment();?> /></li>

										</ul>
									</div>
								</div>
							</div>
						</div>

					</div>
					<!-- ALS Booking information area -->












					<div class="gform_page_footer"></div>
				<button class="command-button icon-right warning" type="submit"
									value="u_submit">
									<span class="icon mif-share"></span> Press This Button to Submit the changes <small>We are
										looking forward to seeing you soon :)</small>
								</button>
			</div>

			</form>
			