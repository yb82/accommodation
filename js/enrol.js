/**
 * 
 */
	var course;
	var optionVal;
	var start_date;
	var weeks;
	var end_date;
	
	
	var i =0;
	
	function isEmail(emailAddress){
		 var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
		 return pattern.test(emailAddress);
	}
	function alpha_s(str){
		if(str){
			var pattern = new RegExp(/^[a-zA-Z\s]*$/);
			return pattern.test(str);
		}
		return false;
	}
	
	function checkRange(str, min, max){
		var re = false;
		if(str){
			if(str.length>=min && str.length <= max  ){
				re =true;
			}
		}
		return re;
	}
	function checkLength(str){
		var re= true;
		if(!str){
			re= false;
		}
		return re;
	}
	function checkDate(str){
		if(str){
			var pattern = new RegExp(/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/);
			return pattern.test(str);
		}
		return false;
	}
	function checkEmail(str){
		if(str){
			var pattern = new RegExp(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i);
			return pattern.test(str);
		}
		return false;
	}
	$('#enrol_submit').click(function(){
	
		
		var str = $( "#f_name" ).val();

		
		if(!alpha_s(str)){
			alert("First name is invalid, please enter your first name again");
			$("#f_name" ).focus();
			return false;
		}
		
		str =  $( "#l_name" ).val();
	
		
		if(!alpha_s(str)){
			alert("Last name is invalid, please enter your last name again");
			$("#l_name" ).focus();
			return false;
		}
		
		str = $("input[name='gender']:checked").val();
		
		if(!checkLength(str)){
			alert("Please choose your gender");
			$("#male" ).focus();
			return false;
		} else if(str=="female"){
			str=$("input[name='title']:checked").val();
			if(!checkLength(str)){
				alert("Please choose your title");
				$('#female_title').show("slow");
				$("#male" ).focus();
				return false;
			}
		}
		
		str=  $( "#dobdatepicker" ).val();
		
		if(!checkDate(str)){
			alert("Your date of birth is invalid.\nPlease check the format of your date of birth\nDD/MM/YYYY.");
			$("#dobdatepicker" ).focus();
			return false;
		}
		
		str= $("#nationality").val();
		if(!checkLength(str)){
			alert("Please choose your nationality.");
			$("#nationality").focus();
			return false;
		}
		str= $("#address").val();
		if(!checkLength(str)){
			alert("Please enter your current address.");
			$("#address").focus();
			return false;
		}
		
		str= $("#contact").val();
		if(!checkRange(str,10,100)){
			alert("Your current contact number is invalid.\nPlease enter your current contact number.");
			$("#contact").focus();
			return false;
		}
		
		str=$("#email").val();
		if(!checkEmail(str)){
			alert("Your email address is invalid.\nPlease enter your email.");
			$("#email").focus();
			return false;
		}
		
		str = $("#visa_type").val();
		if(!checkLength(str)){
			alert("Please choose your visa.");
			$("#visa_type").focus();
			return false;
		}
		
		if(str=="Student"){
			var str1 = $("input[name='CoE']:checked").val();
			
			if(!checkLength(str1)){
				$("#CoE").show("slow");
				alert("Please choose CoE request.");
				$("#CoE_yes").focus();
				return false;
			}
			if(str1 && str1=="Yes"){
				var str2 = $('#dibp').val();
				if(!checkLength(str2)){
					$('#diac_office').show("slow");
					alert("Please enter your DIBP office");
					$('#dibp').focus();
					return false;
				}				
				
			} 
		}
		if(str=="Other"){
			var othervisa= $("#other_visa_type").val();
			if(!checkLength(othervisa)){
				$("#specify_visa").show("slow");
				alert("Please enter your visa type.");
				$("#other_visa_type").focus();
				return false;				
			}
			
		}
		
		



		var oshc = $("input[name='OSHC']:checked").val();

		if(!checkLength(oshc)){
			alert("Please choose OSHC request");
			$("#OSHC_yes").focus();
			return false;
			
		}
		
		if(oshc && oshc=="Yes"){
			var type=  $("input[name='OSHC_type']:checked").val();
			if(!checkLength(type)){
				alert("Please chooose one of OSHC types");
				$("#OSHC_single").focus();
				return false;						
			}
		}

		var accommodation =  $("input[name='accommodation']:checked").val();
	
		if(accommodation && accommodation =="Yes"){
			var accommodation_type =$("input[name='accommodation_type']:checked").val();
			if(!checkLength(accommodation_type)){
				alert("Please select one type of accommodation.");
				$("#homestay").focus();
				return false;
			}
		}
		var accommodation_type =  $("input[name='accommodation_type']:checked").val();
		var roomtype= $("input[name='accommodation_roomtype']:checked").val();
		if(!roomtype && accommodation_type =="IH Brisbane-ALS Accommodation" && accommodation !="No" ){
			alert("Please select room type.");
			$("#uni_roomtype").focus();
			return false;
		}
		
		var windowtype= $("input[name='accommodation_windowtype']:checked").val();
		if(!windowtype && accommodation_type =="IH Brisbane-ALS Accommodation" && accommodation !="No"){
			alert("Please select window type.");
			$("#uni_windowtype").focus();
			return false;
		}
		
	
		if(!$("#agree").is(':checked')){
			alert("Please agree to IH Brisbane-ALS Terms and Condition");
			$("#agree").focus();
			return false;
		}

		
		
		var courseDetail={};
		
		var output= new Array();
		$('#courses tbody tr td').each(function(){
			
		      if(i==0){
		    	  courseDetail.course_name = $(this).text();
		    	  i++;
		      }else if(i==1){
		    	  courseDetail.start_date= $(this).text();
		    	  i++;
		      }else if(i==2){
		    	  courseDetail.weeks = $(this).text();
		    	  i ++;
		    	  output.push(courseDetail);
			      courseDetail={};
		      }else if(i==3){
		    	  i = 0;
		      }		      
		});
		
		
		
		if(output.length >0){
			var json_text = JSON.stringify(output, null, 2);
			var row="<input type='hidden' name='all_course' value='" +json_text+"'>";
			$("#uniresort_detail").append(row);
			
			var fullDate = new Date();
			var twoDigitMonth = fullDate.getMonth()+1+"";if(twoDigitMonth.length==1)  twoDigitMonth="0" +twoDigitMonth;
			var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1) twoDigitDate="0" +twoDigitDate;
			var currentDate = twoDigitDate + "/" + twoDigitMonth + "/" + fullDate.getFullYear();
		    
			
			
		    var name=$("#f_name").val()+" "+$("#l_name").val();
		    var dob = $('#dobdatepicker').val();
		    var nation= $("select[name='nationality'] option:selected").text();
		    var passport = $('#passport').val();
		    var loe= $("select[name='LOE'] option:selected").text();
		    
			var address =$('#address').val();
			var contact=$("#contact").val();
			var email = $("#email").val();
			
		    var visa = $("#visa_type").val();
		    var coe = $("input[name='CoE']:checked").val();
			var dibp = $("#dibp").val();
			var current_enrol =  $("input[name='another_institution']:checked").val();
			var ohsc = $("input[name='OSHC']:checked").val();
			var ohsc_type =$("input[name='OSHC_type']:checked").val();
		    var pathway =$("input[name='pathway']:checked").val();
			var pathway_p=$("select[name='pathway_p'] option:selected").text();
			var accommodation =  $("input[name='accommodation']:checked").val();
			var accommodation_type = $("input[name='accommodation_type']:checked").val();
			
			
			currentForm = $(this).closest('form');
		    	    
		    
			
			if(!$('input[type=checkbox]:checked').length) {
			        alert("Please agree with the policy.");

			        //stop the form from submitting
			        return false;
			}
			    
			var matches = dob.match(/^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/); 
			if(matches==null){
				$("#dobdatepicker").val(currentDate);
				alert("Date of Birth : Invalid Date Format!!! Must Be dd/mm/yyyy" );
				$("#dobdatepicker").focus();
				return false;	
			}
						 
		         $("#confirm_st_name").text(name);
		     	 $("#confirm_dob").text(dob);
				 $("#confirm_contact").text(contact);
				 $("#confirm_nation").text(nation);
				 $("#confirm_passport").text(passport);
		     	 $("#confirm_current").text(loe);
				 $("#confirm_address").text(address);
		         $("#confirm_email").text(email);
				
				if(visa=="Student"){
					if(coe){
						visa += "    COE: "+coe;
					} 
					if(!dibp && coe!="No"){
						visa += "     DIBP office: "+dibp;
					}
				} 
				$("#confirm_visa").text(visa);
				 
				if(current_enrol){
					$("#confirm_enrolment").text(current_enrol);
				} else	$("#confirm_enrolment").text("No");
		        
				 if(!ohsc || ohsc =="No"){
					ohsc="No";
				 } else{
					if(oshc_type){
						ohsc += "  "+ohsc_type;
					}
				 }
		         $("#confirm_oshc").text(ohsc);
				 
				 if(!pathway|| pathway=="No"){
					pathway = "No";
				 }	else{
					if(pathway_p){
						pathway += " School: "+pathway_p;
					}
				 }
				 		 
		         $("#confirm_pathway").text(pathway);
				 
				 if(!accommodation || accommodation=="No"){
					accommodation="No";
				 } else{
					if(accommodation_type){
						accommodation += "  "+accommodation_type;
					}
				 }
				 
				 $("#confirm_accommodation").text(accommodation);
				 
				 
				 
		         $( "#dialog-confirm" ).dialog({
		   	      resizable: true,
		   	      width: 600,
		          height: 680,
		   	      modal: true,
		   	      buttons: {
		   	        "Confirm": function() {
		   	          $( this ).dialog( "close" );
		   	          $('#boform').submit();	
		   	        },
		   	        Cancel: function() {
		   	          $( this ).dialog( "close" );
		   	          return false;
		   	        }
		   	      }
		   	  });
		    
		    
		} else{
			alert("Please add at least one course.\nPlease press \"ADD BUTTON\"");
			$("#addcourse").focus();
			return false;
		}
		
			
			
			
			
			
		
		
		
	});
	

	$('#dobdatepicker').datepick({yearRange: 'c+0:c-100',dateFormat: 'dd/mm/yyyy'});
	$( "#start_date1" ).datepicker({ dateFormat: 'dd/mm/yy',beforeShowDay: null,changeMonth: true,
      changeYear: true});
	
	$( "#start_date" ).datepicker({ dateFormat: 'dd/mm/yy', changeMonth: true,  changeYear: true ,beforeShowDay: function(date){ return [date.getDay() == 1,""]}});
	$( "#cal_homestay" ).datepicker({ dateFormat: 'dd/mm/yy',beforeShowDay: null,changeMonth: true,    changeYear: true});
	$( "#cal_accommodation" ).datepicker({ dateFormat: 'dd/mm/yy',beforeShowDay: null,changeMonth: true,    changeYear: true});
		$(document).ready(function() {
	
		$("#male").click(function() { 
			$('#female_title').hide("slow");			
		});
		
		$("#title").click(function() { 
			$('#female_title').show("slow");			
		});
		$("#visa_type").on('change', function() {
			$(" #specify_visa , #CoE, #diac_office").hide("slow");
			var optionVal = $("#visa_type").val();
			switch(optionVal){
			case "Student":
			$("#CoE").show("slow");
			break;
			case "Other":
			$("#specify_visa").show("slow");
			break;
			}
		});
		$("#course_type").on('change', function() {
			$(" #other_type_course_div").hide("slow");
			$("#start_date,#start_date1").hide("fast");
			var optionVal = $("#course_type").val();
			switch(optionVal){
			case "Private tuition":
			$("#start_date1").show("fast");
			break;
			case "Other":
			$("#start_date1").show("fast");
			$("#other_type_course_div").show("slow");
			break;
			default:
			$("#start_date").show("fast");
			break;
			}
		});
		
		
		
		$("#CoE_yes").click(function() { 
			$('#diac_office').show("slow");			
		});		
		$("#CoE_no").click(function() { 
			$('#diac_office').hide("slow");			
		});		
		
		$("#OSHC_yes").click(function() { 
			$('#oshc_type').show("slow");			
		});		
		$("#OSHC_no").click(function() { 
			$('#oshc_type').hide("slow");			
		});	
		
		$("#pathway_yes").click(function() { 
			$('#pathway').show("slow");			
		});	
		
		$("#pathway_no").click(function() { 
			$('#pathway').hide("slow");			
			$('#pathway_p').val("");
		});	
		$("#accommodation_yes").click(function() { 
			$('#accommodation').show("slow");
			if($("#homestay").is(':checked')){
				$('#homestay_detail').show("slow");			
			} else if($("#uniresort").is(':checked')){
				$('#uniresort_detail').show("slow");			
			}
		});	
		$("#accommodation_no").click(function() { 
			$('#accommodation').hide("slow");			
			$("#homestay_detail").hide("slow");			
			$("#uniresort_detail").hide("slow");						
		});
		$("#homestay").click(function() { 
			$('#homestay_detail').show("slow");			
			$('#uniresort_detail').hide("slow");			
		});
		$("#uniresort").click(function() { 
			$('#homestay_detail').hide("slow");			
			$('#uniresort_detail').show("slow");			
		});			
		$("#removecourse").click(function() { 
			
			 if (rowCount<2){
				alert("There is no course!");
				$('#courses_area').hide("slow");	
				
				return false;
			}
			else{
				$('#courses tbody tr:last').remove();
				$('#confirm_courses tbody tr:last').remove();
				var rowCount = $('#courses tr').length;
				if (rowCount<2){
					$('#courses_area').hide("slow");
					
					return false;
				}
					
				
			}
		});	
		
		
		$("#addcourse").click(function(){
			var rowCount = $('#courses tr').length;
			 if (rowCount>4){
				alert("Only a maximum of 4 courses can be added" +
						"!!!!!!!!!!");
				return false;
			 }
			$("#course_type").prop("selectedIndex",0);
			$("#other_course_type").val("");
			$("#start_date").val("");
			$("#start_date1").val("");
			$("#weeks").val("");
			$( "#course_dialog" ).dialog({
	   	      resizable: true,
	   	      width: 600,
	          height: 400,
	   	      modal: true,
	   	      buttons: {
	   	        "ADD": function() {
					$( this ).dialog( "close" );
	   	        	var course= $('#course_type').val();
					var optionVal = $("#course_type").val();
					var start_date;
					if(optionVal=="Private tuition" || optionVal=="Other"){
						start_date=$('#start_date1').val();
						if(optionVal=="Other"){
							course +=" "+$("#other_course_type").val();
						}
					} else if ( optionVal=="none"){
						alert("You have not selected any course");
						return true;
					} 
					else {start_date=$('#start_date').val();}
					if (start_date.length==0){
						alert("You have not selected start date");
						return true;
					}
	   	        	var weeks= $('#weeks').val()
					if(weeks.length==0){
						alert("Weeks field is empty");
						return true;
					}
					$("#course_confirm_name").text(course);
					$("#course_confirm_startdate").text(start_date);
					$("#course_confirm_weeks").text(weeks);
					$( this ).dialog( "close" );
					
						$( "#course_confirm" ).dialog({
		   	    	   resizable: true,
			   	      width: 500,
			          height: 350,
			   	      modal: true,
			   	      buttons: {
			   	        "Yes": function() {
			   	        
			   	        
			   	        	
			   	        	var splitdate = start_date.split("/");
			   	        	var standard_date = splitdate[1]+"/"+splitdate[0]+"/"+splitdate[2];
			   	        	var date1 = new Date(standard_date);
			   	    	    date1.setDate(date1.getDate()+weeks*7-3);
			   	     	
			   	     	
			   	    	    end_date= $.datepicker.formatDate('dd/mm/yy', date1);
			   	              $( "#courses tbody" ).append( "<tr>" +
			   	          "<td>" + course + "</td>" +
			   	          "<td>" + start_date + "</td>" +
			   	          "<td>" + weeks + "</td>" +
			   	          "<td>" + end_date +"</td>"+
			   	        "</tr>" );
							  $( "#confirm_courses tbody" ).append( "<tr>" +
			   	          "<td>" + course + "</td>" +
			   	          "<td>" + start_date + "</td>" +
			   	          "<td>" + weeks + "</td>" +
			   	          "<td>" + end_date +"</td>"+
			   	        "</tr>" );
							  
			   		          $("#courses_area").show("slow");
			   			
			   			  
						  $( this ).dialog( "close" );
			   	        },
			   	        "No": function() {
				   	          $( this ).dialog( "close" );
				   	          return false;
				   	    }
			   	        
		   	       }
		   	       });
			   		
		   	       
				  
	   	          
	   	        },
	   	        Cancel: function() {
	   	          $( this ).dialog( "close" );
	   	          return false;
	   	        }
	   	      }
	   	  });
		});
	
	
	
		
		
	});

	