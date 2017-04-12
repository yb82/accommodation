


<?php
include ('./_header.php');

?>

<link type="text/css" href="../datepicker/jquery.datepick.css"
	rel="stylesheet" />


<link rel="stylesheet" href="../css/smoothness/jquery-ui-1.10.3.custom.css">

<script type="text/javascript" src="../jquery/jquery-1.9.1.js"></script>

<script type="text/javascript" src="../jquery/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui-1.10.3.custom.min.js"></script>

<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>


<script type="text/javascript" src="../timepicker/jquery.timepicker.js"></script>

<script type="text/javascript" src="../datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="../timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="../timepicker/jquery.timepicker.css" />

<script type="text/javascript" src="../js/gen_validatorv4.js" ></script>
<div id="dialog-confirm" title="Booking Detail" style="display:none" >
 
 
 
	<li class="gfield"><label>Student ID : </label><label id="confirm_st_id"></label><br/>
	<li class="gfield"><label>Student Name : </label><label id="confirm_st_name"></label><br/>
	<li class="gfield"><label>Student Date of Birth : </label><label id="confirm_dob"></label><br/>
	<li class="gfield"><label>Student Gender : </label><label id="confirm_gender"></label><br/>
	<li class="gfield"><label>Student Contact : </label><label id="confirm_contact"></label><br/>
	<li class="gfield"><label>Student Email : </label><label id="confirm_st_email"></label><br/>
	<li class="gfield"><label>Agent Email : </label><label id="confirm_agent_email"></label><br/>
	<li class="gfield"><label>Student Nationality : </label><label id="confirm_st_nation"></label><br/>
	<li class="gfield"><label>Room Type : </label><label id="confirm_room_type"></label><br/>
	<li class="gfield"><label>Check-in Date : </label><label id="confirm_check_in"></label><br/>
	<li class="gfield"><label>Pickup : </label><label id="confirm_pickup"></label><br/>
	<li class="gfield"><label>Airport : </label><label id="confirm_airport"></label><br/>
	<li class="gfield"><label>Arrival Date : </label><label id="confirm_arrival_date"></label><br/>
	<li class="gfield"><label>Arrival Time : </label><label id="confirm_arrival_time"></label><br/>
	<li class="gfield"><label>Flight number : </label><label id="confirm_f_num"></label><br/>
	<li class="gfield"><label>Comment : </label><label id="confirm_comment"></label><br/><br/>
	<label>Please confirm this information is correct</label>
	
	
 
</div>
<div id="banner" title="Information!" style="display:none" >
 
 	<img src="../images/ihalslogo.png" height="160"></img><br/><br/><br/>
 	<p align="center"><label><font size =5pt>No vacancy until 6/10/2014</font></label></p><br/>
 	<p align="center"><label><font size =5pt>Sorry for any inconvenience</font></label></p>
 	
	
 
</div>
 

 

<?php
$booking = new Booking(); 
if (!$booking->isBooked()) {?>
<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>
</section>
<section id="page_template" class="inner_content_body_wrapper">
	<div class="inner_content_body  container ">
		<div class="inner_content_position offset-by-half">
			<div class="inner_content_position sixteen columns" id="post-101">
				<div class="offset-by-half">
					<div class="inner_content_position fifteen columns">

						<div id="gform_title" class="page_title">
							<img src="../images/book-accomo-title.gif">
						</div>

						<div class="inner_content_position offset-by-half">

							<div class="gform_wrapper">
								<form method="post" id="boform" name="boform" onSubmit="return checkFields()" >
									<!-- student information area -->
									<div class="gform_group_wrapper">
										<div class="gfield  gsection">

											<img src="../images/join_arrow.gif"
												style="vertical-align: middle">&nbsp;<label><b>Student
													information </b></label>

										</div>


										<div class="gform_group_pane_right ">
											<ul class="gform_fields">

												<li class="gfield"><label class="gfield_label">&nbsp;&nbsp;&nbsp;</label>
												
												<li class="gfield"><label class="gfield_label"> Last name </label>
													<input type="text" placeholder="Last name" name='l_name' id="book_st_lname"
													tabindex='3' /></li>
												<li></li>
												<li class="gfield"><label class="gfield_label"> Contact </label>
													<input type="text" placeholder="Contact" name="contact" id="book_contact"
													tabindex='5' /></li>
												<li class="gfield" ><input type="button" class="login_button" style="float: left" name="check_email"
											id="check_email" value="Check Email"/></li>
												<li class="gfield" ></li>
												<li class="gfield"><label class="gfield_label"> Date of
														birth </label> <input type="text" tabindex='9'
													placeholder="Date of Birth dd/mm/yyyy" name="dob" 
													id="popupDatepicker" /></li>

											</ul>

										</div>
										<div class="gform_group_pane_left">

											<ul class="gform_fields">

												<li class="gfield"><label class="gfield_label"> Student ID</label>
													<input type="text" placeholder="Student ID, If Known" id="book_st_id"
													name='st_id' tabindex='1' /></li>
												<li class="gfield gfield_contains_"><label
													class="gfield_label"> First name </label> <input
													type="text" placeholder="First name" name='f_name' id="book_st_fname"
													tabindex='2' /></li>
												<li class="gfield"><label class="gfield_label">Gender </label>
													<select name='gender' tabindex='4' id="book_gender">
														<option value="1">Male</option>
														<option value="0">Female</option>
												</select></li>
												<li class="gfield"><label class="gfield_label"> Email
														address </label> <input type="text"
													placeholder="Email address" name="email" tabindex='6' id="book_st_email" /></li>
											
												<li class="gfield"><label class="gfield_label">Agent Email</label>
													<input type="text" placeholder="Email Address" id="book_agent_email"
													name="agent_email" tabindex='7'  /></li>
												
												<li class="gfield"><label class="gfield_label"> Nationality
												</label> <select name='nationality' tabindex='8'>
														<option value="none">Select Your Nationality</option>
	 <?php  echo $booking->printCountry()?>
	</select> <i></i></li>
											</ul>
										</div>
									</div>


									

									<!-- Room Information -->
									<div class="gform_group_wrapper">

										<div class="gfield gsection">
											<img src="../images/join_arrow.gif"
												style="vertical-align: middle">&nbsp;<b>Room Type</b>
										</div>
										<img src="../images/Uniresort123.png" style = "max-width:100%;
max-height:100%;" ></img><br/><br/><br/>
										<div class="checkin date">
											<ul class="full_gform_fields">
												<li class="gfield"><label class="gfield_label"> Check in
														date</label> <input type="text"
													placeholder="Check in date dd/mm/yyyy" name="check_in" 
													id="popupDate1" tabindex='16' /></li>
											
												<li class="gfield"><label class="gfield_label">Duration   </label> <select name='weeks' id="book_weeks">
														<option value="4">4 weeks</option>
														<option value="5">5 weeks</option>
														<option value="6">6 weeks</option>
														<option value="7">7 weeks</option>
														<option value="8">8 weeks</option>
												</select></li></li>
											</ul>
										</div>

										


										<div class="checkin date ">
											<ul class="gform_fields">
												<li class="gfield_option"><label class="gfield_label">Room
														Type</label>
												</li>
												<li class="gfield_option">
				
					
					<?php echo $booking->printRoomType();?> 
					
			</li>
												
												
											</ul>
										</div>

									</div>

									<!-- Airport Pick Up -->
									<div class="gform_group_wrapper">

										<div class="gfield gsection">
											<img src="../images/join_arrow.gif"
												style="vertical-align: middle">&nbsp;<b>Airport Pick Up</b>
										</div>
										<div>
											<ul class="full_gform_fields">
												<li class="gfield_option">
													<br /> <!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! disable!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
			<?php echo $booking->printAirport();?>
			<input type="radio" value=0 name="pickup" id="pickup0" checked="true" /> No pickup desired <br />
			</li>
			
												<li class="gfield"><label class="gfield_label"> Arrival date
												</label><input type="text" tabindex='18'
													placeholder="Arrival Date dd/mm/yyyy" name="arrival_date" 
													id="arrivalDate" /></li>
												<li class="gfield"><label class="gfield_label"> Arrival time
												</label><input type="text" tabindex='19'
													placeholder="Arrival time" name="arriaval_time" 
													id="timepick" /></li>
												<li class="gfield"><label class="gfield_label"> Flight
														number </label><input type="text" tabindex='20'
													placeholder="Flight Number" id="f_num" name="f_num" /></li>
												<li class="gfield"><label class="gfield_label"> Enter
														comment </label><input type="text" tabindex='21'
													placeholder="Comment" name="comment" id="book_comment" /></li>
												
											</ul>
										</div>
									</div>
</form>
			<div class="gform_group_wrapper">

										<div class="gfield gsection">
											<img src="../images/join_arrow.gif"
												style="vertical-align: middle">&nbsp;<b>Accommodation Terms and Condition</b>
										</div>
										<div>
											<ul class="full_gform_fields">
											<li class="gfield_option"><label class="gfield_label">&#8226; Cancellation or change policy (Before Payment)</label></li>
											<li class="gfield_option"><label class="gfield_label">Cancellations or changes to bookings must be made at least 48 hours prior to the check in date and time, or you will be charged the full amount of the booking.</label></li>
											<li class="gfield_option"><label class="gfield_label">&#8226; Refund Policy (After Payment)</label></li>
											<li class="gfield_option"><label class="gfield_label">If you wish to cancel your booking, two weeks' notice prior to the check in date must be given. After this point, no refunds will be given.</label></li>
											<li class="gfield_option"><label class="gfield_label">&#8226; Check-in instructions</label></li>
											<li class="gfield_option"><label class="gfield_label">You must present your accommodation confirmation letter as well as photo ID at check-in. Staff will record and retain a copy of this ID to protect against fraudulent bookings. Check-in is after 2:00PM; and you may not enter the room before then.</label></li>
											<li class="gfield_option"><label class="gfield_label">&#8226; Parking</label></li>
											<li class="gfield_option"><label class="gfield_label">Underground car parking is available free of charge</label></li>
											</ul>
																																	
											



										</div>
									</div>
									<div class="gform_page_footer">
										<input type="checkbox" name="agree" value=1 tabindex='22' /> I
										agree to the above terms and conditions and have read and agree to the <a target="_blank"
											href="http://www.ihbrisbane.com.au/Apps/bookingaccommodation/UNIRESORT LEGAL TERMS CONDITIONS 2015.pdf">Uniresort legal information</a><br/><br/>
											<a target="_blank"
											href="http://www.ihbrisbane.com.au/Apps/bookingaccommodation/Letter for 1st July.pdf">New Policy from July 2015</a><br/><br/>
									
										<input class="reset_button"
											type="button" value="Reset Form" onClick="this.form.reset()" />
										<input type="button" class="login_button" name="book"
											id="book" value="Submit" />&nbsp;&nbsp;&nbsp;
									</div>
								
							</div>

							

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>
	
							<script type="text/javascript">
//<![CDATA[
//You should create the validator only after the definition of the HTML form
  var frmvalidator  = new Validator("boform");
 
  frmvalidator.addValidation("f_name","req","Please enter your First Name");
  frmvalidator.addValidation("f_name","maxlen=255",	"Max length for First Name is 255");
  frmvalidator.addValidation("f_name","alpha_s","First Name-Alphabetic chars only");
 
  frmvalidator.addValidation("l_name","req","Please enter your Last Name");
  frmvalidator.addValidation("l_name","maxlen=100",	"Max length for Last Name is 100");
  frmvalidator.addValidation("l_name","alpha_s","Last Name-Alphabetic chars only");
  
  
   frmvalidator.addValidation("contact","req","Please enter your Contact");
  frmvalidator.addValidation("contact","minlen=8",	"Min length for Contact is 8");
  
  
    frmvalidator.addValidation("email","req","Please enter your Email");
    frmvalidator.addValidation("email","maxlen=255");
  frmvalidator.addValidation("email","email","Format of email is not valid");
  
 
  
    
   frmvalidator.addValidation("nationality","req", "Please select your Nationality");
   frmvalidator.addValidation("nationality","dontselect=none", "Please select your Nationality");
   
   
   frmvalidator.addValidation("dob","req", "Please enter your Date of Birth");
   frmvalidator.addValidation("dob","maxlen=10",	"Max length for Date of Birth is 10");
   
      frmvalidator.addValidation("st_addr_1","req", "Please enter your Stree Address");
   frmvalidator.addValidation("dob","maxlen=100",	"Max length for Stree Address is 100");
   
      frmvalidator.addValidation("city","req", "Please enter your City");
   frmvalidator.addValidation("city","maxlen=100",	"Max length for City is 100");
   
   frmvalidator.addValidation("state","req","Please enter your State");
   frmvalidator.addValidation("state","maxlen=100",	"Max length for State is 100");
   
 
   
   
  frmvalidator.addValidation("country","req", "Please select your Country");
   frmvalidator.addValidation("country","maxlen=100",	"Max length for Country is 100");
   frmvalidator.addValidation("country","dontselect=none", "Please select your Country");
   
   frmvalidator.addValidation("check_in","req", "Please enter Check in Date");
  frmvalidator.addValidation("room","selone");

  
  
  

   //frmvalidator.setAddnlValidationFunction(DoCustomValidation)

</script>
<script type="text/javascript" src="../js/book123.js"></script>
<?php

} else {
	$booking->setBooked ( false );
	
	?>
<div style="width: 1500px; height: 500px;">
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p class="m" style="text-align: center"><h1 style="text-align: center">Thank you for submitting your booking<br/><br/></h1></p><br/>
	<p class="m" style="text-align: center">We have sent you an email, please verify your email address by clicking in the link.</p><br/><br/><br/>
	
	<p class="m" style="text-align: center">
		<a href="display.php">->Back to previous page</a>
	</p>
</div>
<?php
}

include ('./_footer.php');
?>