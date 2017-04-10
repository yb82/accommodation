/**
 * 
 */


$(function() {
	var checkemail = false;
	//('#').datepick({minDate: '-100y'});
	$('#popupDatepicker').datepick({yearRange: 'c+0:c-100',dateFormat: 'dd/mm/yyyy'});
	//$('#popupDatepicker').datepick({minDate: -1});
	$('#popupDate1').datepick({
		minDate: '01/06/2014'
		,dateFormat: 'dd/mm/yyyy'});
	
	$('#timepick').timepicker({'step':10});

	$('#check_email').click( function() {
		var email = $('#book_st_email').val();
		var returndata;
		if(email.length >0){

		$.post( "./book.php", { studentemail: email })
		  .done(function( returndata ) {
			  var s_Data= jQuery.parseJSON( returndata );

			  if(s_Data.length ==0 ){
				  alert( "Email Address is valid; Please continue with your booking" );
				  checkemail = true;
				  
			  } else{  	
				
				  $('#book_st_email').val("").change();
			  	  $('#book_st_email').focus();
			  	  checkemail = false;
				  alert( "Email address already exist!" );
				 
				  
			  }


					  
		  }); 
		} else alert( "Student email address is empty!!!!!!" );
		
		return false;
		
	} );
	$('input[type=radio][name=room]').change(function() {
        if (this.id == 'radioDoublebed') {
            	$('#doublebed').show();
            	$('#twinbed').hide();
        }
        else if (this.id == 'radioTwinbed') {
       			$('#doublebed').hide();
            	$('#twinbed').show();
        } 
       else //(this.id != 'radioTwinbed' && this.id != 'radioDoublebed' )
		{		$('#doublebed').hide();
            	$('#twinbed').hide();
        }
    });
	$(document).ready(function() {
	
		
		$("#image").addClass("image1");
		$('#arrivalDate').datepick('enable');
		$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'});  
		$("#arrivalDate").attr('readonly', false); 
		$("#timepick").attr('readonly', false);
		$("#f_num").attr('readonly', false);
	/*
		$("#radiobutton1").click(function() { $("#image").removeClass().addClass("image1"); });
		$("#radiobutton2").click(function() { $("#image").removeClass().addClass("image2");});
		$("#radiobutton3").click(function() { $("#image").removeClass().addClass("image3"); });
		$("#radiobutton4").click(function() { $("#image").removeClass().addClass("image4"); });*/
		/*$("#pickup0").click(function() { 
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
		$("#pickup3").click(function() {
			$('#arrivalDate').datepick('enable'); 
			$('#arrivalDate').datepick({minDate: 0,dateFormat: 'dd/mm/yyyy'}); 
			$("#arrivalDate").attr('readonly', false); 
			$("#timepick").attr('readonly', false);
			$("#f_num").attr('readonly', false);
		});*/
		$(".agentemail").change(function() {
			if ($("#agent_email").attr("checked")) {
	            $('#book_agent_email').hide();
	        }
	        else {
	        	$('#book_agent_email').show();
	        }
	        
			
			
		});
		$("#book").click(function(e) {
			var fullDate = new Date();
			var twoDigitMonth = fullDate.getMonth()+1+"";if(twoDigitMonth.length==1)  twoDigitMonth="0" +twoDigitMonth;
			var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1) twoDigitDate="0" +twoDigitDate;
			var currentDate = twoDigitDate + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
		    var stID;
		    var name;
		    var email;
			var agent_email;
		    var room;
		    var gender =$("#book_gender option:selected").text();
		    var contact=$("#book_contact").val();
		    var nation= $("select[name='nationality'] option:selected").text();
		    var pickup;
		    var airport;
		    var flight_number= $("#f_num").val();
		    var comment=$("#book_comment").val();
		    var arrival_time;
		    var currentForm;
		    var arrival;

		    currentForm = $(this).closest('form');
		      
		    
		    
		    
		    
			var dob = $('#popupDatepicker').val();
			if(!$('input[type=checkbox]:checked').length) {
			        alert("Please agree with the policy.");

			        //stop the form from submitting
			        return false;
			}
			    
			var matches = dob.match(/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/); 
			if(matches==null){
				$("#popupDatepicker").val(currentDate);
				alert("Date of Birth : Invalid Date Format!!! Must Be dd/mm/yyyy" );
				$("#popupDatepicker").focus();
				return false;	
			}
			var checkin= $('#popupDate1').val();
			matches = checkin.match(/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/);
			if(matches==null){
				$("#popupDate1").val(currentDate);
				alert("Check-in Date : Invalid Date format!!! Must Be dd/mm/yyyy");
				$("#popupDate1").focus();
				return false;	
			}
			
			
				 
			stID = $("#book_st_id").val();
		    name= $("#book_st_fname").val()+" "+$("#book_st_lname").val();
		    email= $("#book_st_email").val();
		    agent_email= $("#book_agent_email").val();
		    room= $("input[name='room']:checked").val();
		    pickup= $("input[name='pickup']:checked").val();

		         	         
		   
		    arrival = $('#arrivalDate').val();
		 	if(arrival.length == 0 ){
			} else if(arrival.match(/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/) == null && arrival != "None" ){
				$("#arrivalDate").val(currentDate);
				alert("Arrival Date : Invalid Date format!!! Must Be dd/mm/yyyy");
				$("#arrivalDate").focus();
				return false;
			}
		 	 arrival_time=$("#timepick").val();
	    	 if(pickup != "0") {
	    		 airport=pickup;
		    	 pickup="YES";
		        
	    	 } else {
	    		// pickup="NO";
		    	// airport="None";
		    	// arrival="None";
		    	// arrival_time="None";
		    	// flight_number="None";
		    	 
		     }
 
		    	 
    	
		    	 
		    	 
		         $("#confirm_st_id").text(stID);
		     	 $("#confirm_st_name").text(name);
		     	 $("#confirm_dob").text(dob);
		     	 $("#confirm_gender").text(gender);
		     	 $("#confirm_contact").text(contact);
		     	 $("#confirm_st_email").text(email);
		     	 $("#confirm_agent_email").text(agent_email);
		     	 $("#confirm_st_nation").text(nation);
		     	 
		     	 $("#confirm_room_type").text(room);
		         $("#confirm_check_in").text(checkin);
		         $("#confirm_pickup").text(pickup);
		         $("#confirm_airport").text(airport);
		         $("#confirm_arrival_date").text(arrival);
		         $("#confirm_arrival_time").text(arrival_time);
		         $("#confirm_f_num").text(flight_number);
		         $("#confirm_comment").text(comment);
		         $( "#dialog-confirm" ).dialog({
		   	      resizable: true,
		   	      width: 400,
		          height: 600,
		   	      modal: true,
		   	      buttons: {
		   	        "Confirm": function() {
		   	          $( this ).dialog( "close" );
		   	          if(checkemail){
		   	        	var email = $('#book_st_email').val();
		   	        	var returndata;
		   	        	if(email.length >0){

		   	        		$.post( "./book.php", { studentemail: email })
		   	        		.done(function( returndata ) {
		   	       			var s_Data= jQuery.parseJSON( returndata );

		   	        		if(s_Data.length ==0 ){
		   	        			$('#boform').submit();
		   	        					   	 				  
		   	        		} else{  	
		   	 				
		   	        			$('#book_st_email').val("").change();
		   	        			$('#book_st_email').focus();
		   	        			checkemail = false;
		   	        			alert( "Email address already exist!" );
		   	 				 		   	 				  
		   	 			  }


		   	 					  
		   	 		  }); 
		   	 		} else alert( "Student email address is empty!!!!!!" );
		   	        	  
		   	        	  
		   	        	  
		   	        	  
		   	        	  
		   	        	  
		   	        	 
		   	     	  }
		   	          else {
		   	        	$( "#check_email" ).focus();
		   	        	alert("Please press 'Check Email' button");
		   	        	
		   	          }
		   	     	    	
		   	     	},
		   	        Cancel: function() {
		   	          $( this ).dialog( "close" );
		   	          return false;
		   	        }
		   	      }
		   	  });
		    
		    
		});	
		
		
	});
	
	
	 
	
});
