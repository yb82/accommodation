

<?php
$update = new Update ();
include ('./_header.php');

?>
<link type="text/css" href="../datepicker/jquery.datepick.css"
	rel="stylesheet" />
<script type="text/javascript" src="../datepicker/jquery.min.js"></script>
<script type="text/javascript" src="../datepicker/jquery.datepick.js"></script>
<script type="text/javascript" src="../timepicker/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css"
	href="../timepicker/jquery.timepicker.css" />
<script type="text/javascript">
$(function() {
	//('#').datepick({minDate: '-100y'});
	$('#popupDatepicker').datepick({yearRange: 'c+0:c-100',dateFormat: 'dd/mm/yyyy'});
	//$('#popupDatepicker').datepick({minDate: -1});
	$('#popupDate1').datepick({minDate: +2,dateFormat: 'dd/mm/yyyy'});
	$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'}); 
	$('#timepick').timepicker({'step':10,timeFormat: 'H:i'});
	
	$("#pickup0").click(function() { 
		$('#arrivalDate').datepick('disable');
		$("#arrivalDate").attr('readonly', true); 
		$("#timepick").attr('readonly', true);
		$("#f_num").attr('readonly', true);
	});
	$("#pickup1").click(function() {
		$('#arrivalDate').datepick('enable');
		$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'});  
		$("#arrivalDate").attr('readonly', false); 
		$("#timepick").attr('readonly', false);
		$("#f_num").attr('readonly', false);
	});
	$("#pickup2").click(function() {
		$('#arrivalDate').datepick('enable'); 
		$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'}); 
		$("#arrivalDate").attr('readonly', false); 
		$("#timepick").attr('readonly', false);
		$("#f_num").attr('readonly', false);
	});
	$("#boform").submit(function(e) {
		
	   
	   	 alert("Your form has been submitted! Please wait few seconds");
		return true;
	    
	});	
});




function DoCustomValidation()
{
  var frm = document.forms["boform"];
  
  for(var i=0; i<frm.pickup.length; i++) {
      if(frm.pickup[i].checked) {
  
	
	 	if(frm.pickup[i].value != "0"){
	 		if(frm.arrival_date.value.length <= 0){
	 		alert("Please enter Arrival Date");
	 		return false;	
	 		}
	 		
	 		if(frm.arriaval_time.value.length <= 0){
	 		alert("Please enter Arrival Time");
	 		return false;	
	 		}
	 		
	 		if(frm.f_num.value.length <= 0){
	 		alert("Please enter Flight Number");
	 		return false;	
	 		}
	 		

	 	}else{
	 	
	 	return true;
		}
	}
  }
}
	
</script>
<script type="text/javascript" language="JavaScript" src="../js/gen_validatorv4.js"  xml:space="preserve"></script>
<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>
</section>		
<section id="page_template" class="inner_content_body_wrapper">
	<div class="inner_content_body  container ">
	 <div class="inner_content_position offset-by-half">
		<div class="inner_content_position sixteen columns" id="post-101">
		<div class="offset-by-half">
		<div class="inner_content_position fifteen columns">
		
			<div id="gform_title" class="page_title" >
			 <img  src="../images/join_form_title.gif" > 
			</div>

<div class="inner_content_position offset-by-half" >
			
<div class="gform_wrapper">	

<form method="post" action="completeupdate.php" class="boform" id="boform" name="boform">

<!-- ALS Booking information area -->
	<div class="gform_group_wrapper">
	<div class="gfield  gsection">
		<img src="../images/join_arrow.gif" style="vertical-align:middle">&nbsp;<b>Booking Detail</b>
	</div>

	<ul class="full_gform_fields">

	<input type="hidden"  name='booking_id'  value=<?php echo $update->getBookingID();?>  readonly />
	
	<li class="gfield"> 
	<label class="gfield_long_label"> 
	Booking Reference Number
	</label><div class="gfield_long_input"><input type="text" name='ref_num' value=<?php echo $update->getRefNum();?> />
	</div>
	</li>
	<li class="gfield"> 
	<label class="gfield_long_label"> 
	Booking Status
	</label><div class="gfield_long_input"><?php echo $update->getStatus();  ?></div>		
				
	</li>
	<li  class="gfield"> 
	<label class="gfield_long_label"> 
	Bed Number</label><div class="gfield_long_input"><input type="text" name='bed_code' value=<?php echo $update->getBedCode(); ?> />
	</div>
	</li>
	<li class="gfield">
	<label class="gfield_long_label"> 
	Room Entry Code
	</label><div class="gfield_long_input"><input type="text" name='room_code' value=<?php echo $update->getRoomCode(); ?> />
	</div>
	</li>
	
	<li class="gfield">
	<label class="gfield_long_label"> 
	 Front Door Code
	</label><div class="gfield_long_input"><input type="text" name='front_code' value=<?php echo $update->getForntDoorCode(); ?> />
	</div></li>
	<li class="gfield">
			<label class="gfield_long_label"> Pedestrian Gate Code
			</label>
			<div class="gfield_long_input"><input type="text" name='gate_code' value=<?php echo $update->getPGateCode(); ?> />
			</div>
		</li>
	
	</ul>
	</div>
		
	
	
	
	<!-- student information area -->
	<div class="gform_group_wrapper">
	<div class="gfield  gsection">
		<img src="../images/join_arrow.gif" style="vertical-align:middle">&nbsp;<b>Student information </b>
	</div>
	
	
	<div class = "gform_group_pane_right ">
		<ul class="gform_fields">

<li class="gfield">
	<label class="gfield_long_label"> All fee added </label>
	<div class="gfield_long_input"><?php echo $update->getFeeAdded();?></div>
	</li>
		<li class="gfield"> 
			<label class="gfield_label"> 
			Last name
			</label><input type="text" name='l_name' value=<?php echo $update->getStudentLastName(); ?> />
			
				
		</li>
		<li></li>
		<li class="gfield">
			<label class="gfield_label"> Contact
			</label>
			<input type="text" name="contact" value=<?php echo $update->getStudentContact() ?> />
			
		</li>
		<li  class="gfield"><label class="gfield_label">&nbsp;&nbsp;&nbsp;</label>
		<li class="gfield">
			<label class="gfield_label"> Date of birth
			</label>
			<input type="text"   name="dob" id="popupDatepicker"  value=<?php echo $update->getDOB() ?> required /> 
			
		</li>
	
		</ul> 

	</div>	
	<div class = "gform_group_pane_left">

	<ul class="gform_fields">

	<li  class="gfield">
	<label class="gfield_label"> 
	Student ID</label><input type="text" name='st_id' value=<?php echo $update->getStudentID(); ?> />
	</li>
	<li class="gfield gfield_contains_" > 
	<label class="gfield_label"> 
	First name
	</label><input type="text" name='f_name' value=<?php echo $update->getStudentFirstName();?> />
	
	</li>
	<li  class="gfield"> 
	<label class="gfield_label"> Gender </label>
	<?php echo $update->getStudentGender();?>
	</li>
	<li class="gfield">
	<label class="gfield_label"> Email address
	</label>
	<input type="text" name="email" value=<?php echo $update->getStudentEmail(); ?> /> 
	
	</li>
	<li class="gfield">
	<label class="gfield_label"> nationality</label>
	 <?php  echo $update->getNationality()?>
	</li>
	</ul>
	</div>
	</div>

<!-- Address information area -->
	<div class="gform_group_wrapper">
	<div class="gfield  gsection">
	<img src="../images/join_arrow.gif" style="vertical-align:middle">&nbsp;<b>Address information </b>
	</div>
	<ul class="full_gform_fields">

	<li  class="gfield"> 
	<label class="gfield_label"> 
	Address 
	</label><input type="text" name="address" value=<?php echo $update->getStudentAddr();?> />
	
	</li>
	<!--
	<li  class="gfield"> 
	<label class="gfield_label"> 
	Street Address2
	</label><input type="text"  name="st_addr_2">
	
	</li>
	<li  class="gfield"> 
	<label class="gfield_label"> 
	Suburb/City
	</label><input type="text"  name="city" /> 
	
	</li>
	<li  class="gfield"> 
	<label class="gfield_label"> 
	State/Province
	</label><input type="text" placeholder="State/Province" name="state" /> 
	
	</li>
	<li  class="gfield"> 
	<label class="gfield_label"> 
	Postcode
	</label><input type="text" placeholder="Postcode" name="postcode"  /> 
	
	</li>
	<li  class="gfield"> 
	<label class="gfield_label"> Country </label>
	<select name='country' >
		<option value="none">Select Country</option>
		<?php $booking = new Booking(); echo $booking->printCountry()?>
	</select>
	</li>
	-->
	</ul>
	</div>
		

	<!-- Room Information -->
	<div class="gform_group_wrapper">
		<div class="gfield gsection">
		<img src="../images/join_arrow.gif" style="vertical-align:middle">&nbsp;<b>Room Type </b>	
		</div>
		<div class="checkin date">
		<ul class="full_gform_fields">
		<li class="gfield">
		<label class="gfield_label"> Check in date</label>
		<input type="text" name="check_in" id="popupDate1" value=<?php echo $update->getCheckin(); ?> />
		
		</li>
		<li class="gfield_option">
				
				<?php echo $update->getRooms();?>
				
		</li>
		<li class="gfield">
			<label class="gfield_label"> Weeks</label>
			<input type="text" name="stay_weeks" value=<?php echo $update->getWeeks(); ?> />
			
		</li>
		</ul>
		</div>
	</div>

<!-- Airport Pick Up -->
	<div class="gform_group_wrapper">
	
		<div class="gfield gsection">
		 <img src="../images/join_arrow.gif" style="vertical-align:middle">&nbsp;<b>Airport Pick Up</b> 	
		</div>
		<div>	
		<ul class="full_gform_fields">
			<li class="gfield_option">
			<?php echo $update->getAirports(); ?>
			</li>
			<li  class="gfield">
			<label class="gfield_label"> Arrival date
				</label>
			<input type="text" name="arrival_date" id="arrivalDate" value=<?php echo $update->getArrivalDate();?> />
			
			</li>
			<li class="gfield">
			<label class="gfield_label"> Arrival time
			</label>
			<input type="text" name="arrival_time" id="timepick" value=<?php echo $update->getArrivalTime();?> />
			
			</li>
			<li class="gfield">
			<label class="gfield_label"> Flight number
			</label>
			<input type="text" name="f_num" id="f_num" value=<?php echo $update->getFlightNumber()?> />
			
			</li>
			<li class="gfield">
			<label class="gfield_label"> Enter comment
			</label>
			<input type="text" name="comment" value=<?php echo $update->getComment();?> />
			
			</li>

		</ul>
		</div>
	</div>


	<div class="gform_page_footer">
	
	<a href='complete.php'>Back to Previous Page</a><input type="submit" class="login_button" name="u_submit"  value="Submit">
	</div>

</div>	

</form>

<script language="JavaScript" type="text/javascript" xml:space="preserve">
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
  frmvalidator.addValidation("contact","minlen=8",	"Min length for Contact is at least 8");
  //frmvalidator.addValidation("contact","numeric","Only number is allowed for Contact");
  
    frmvalidator.addValidation("email","req","Please enter your Email");
    frmvalidator.addValidation("email","maxlen=50");
  frmvalidator.addValidation("email","email","Format of email is invalid");

    
   frmvalidator.addValidation("nationality","req", "Please select your Nationality");
   frmvalidator.addValidation("nationality","dontselect=none", "Please select your Nationality");
   
   
   frmvalidator.addValidation("dob","req", "Please enter your Date of Birth");
   frmvalidator.addValidation("dob","maxlen=10",	"Max length for Date of Birth is 10");
   
    /*  frmvalidator.addValidation("st_addr_1","req", "Please enter your Stree Address");
   frmvalidator.addValidation("dob","maxlen=100",	"Max length for Stree Address is 100");
   
      frmvalidator.addValidation("city","req", "Please enter your City");
   frmvalidator.addValidation("city","maxlen=100",	"Max length for City is 100");
   
   frmvalidator.addValidation("state","req","Please enter your State");
   frmvalidator.addValidation("state","maxlen=100",	"Max length for State is 100");
   
   frmvalidator.addValidation("postcode","req", "Please enter your Postcode");
   frmvalidator.addValidation("postcode","maxlen=100",	"Max length for Postcode is 100");
   
   
      frmvalidator.addValidation("country","req", "Please select your Country");
   frmvalidator.addValidation("country","maxlen=100",	"Max length for Country is 100");
   frmvalidator.addValidation("country","dontselect=none", "Please select your Country");
   
   */
   
   frmvalidator.addValidation("check_in","req", "Please enter Check in Date");
   frmvalidator.addValidation("room","selone");

   frmvalidator.setAddnlValidationFunction(DoCustomValidation)

//]]></script>
		</div>	
		</div>
		</div>
		</div>
	</div>
	</div>	
	</div>
	
<?php
include ('./_footer.php');
?>
