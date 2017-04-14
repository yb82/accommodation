
<?php
// include html header and display php-login message/error
include ('./_header.php');

?>
<link rel="stylesheet" id="Demopage-css" href="../datatables/css/demo_table.css" type="text/css" >

<link rel="stylesheet" id="Demopage-css" href="../libraries/TableTools/css/dataTables.tableTools.css" type="text/css" >
<link rel="stylesheet" id="Demopage-css" href="../libraries/TableTools/css/dataTables.tableTools.min.css" type="text/css" >  



<script type="text/javascript" src="../jquery/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="../jquery/jquery-ui-1.10.3.custom.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>


<script type="text/javascript" src="../datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="../datatables/js/custom.field.js"></script>

<script type="text/javascript" src="../libraries/TableTools/js/dataTables.tableTools.js"></script>
<script type="text/javascript" src="../libraries/TableTools/js/dataTables.tableTools.min.js"></script>


<script type="text/javascript" charset="utf-8" src="../js/FixedHeader.js"></script>
<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>
</section>             
<section id="page_template" class="display_inner_content_body_wrapper">
<div class="inner_content_body">

	<div class="">

	<div class="gform_wrapper display">
    <?php
$display = new Displaying ();
if ($display->isLogin ()) {
	$result = json_encode ( $display->createDataSet () );
	
	$title = $display->createTitle ();
	//$countries = $display->getCountries ();
	//$status = $display->getStatus ();
	//$rooms = $display->getRooms ();
	//$airport = $display->getAirports ();
	$role = $display->getRole();
	//echo $phplogin_lang ['You are logged in as'] . $_SESSION ['user_name'] . "<br />";
	//echo '<a href="login.php?logout">' . $phplogin_lang ["Logout"] . '</a><br />';
	//echo '<a href="edit.php">' . $phplogin_lang ["Edit user data"] . '</a><br />';
	
	//echo '<a href="book.php">Make a reservation </a><br/>';
	//if ($role != "driver") {
	//	echo '<a href="javascript:void(0)" id="edit">Edit Data</a></br>';
	//}	else{ 
	//	echo '<a href="javascript:void(0)" id="confirm">Confirm pickup</a>';
	//}
	//echo '<div id="output"></div>';
}
 else
	print_r ( "please login" );
?>
<div class="display_right_menu">
<?php echo $phplogin_lang ['You are logged in as'] .'<b>'. $_SESSION ['user_name'] .'</b>&nbsp;&nbsp;&nbsp;&nbsp;'; 
 echo '<img src="../images/edit_user.gif">&nbsp;&nbsp;<a href="edit.php">' . $phplogin_lang ["Edit user data"] . '</a>&nbsp;&nbsp;&nbsp;&nbsp;';
     echo '<img src="../images/logout_icon02.gif">&nbsp;&nbsp;<a href="login.php?logout">' . $phplogin_lang ["Logout"] . '</a>';
	
	?>
</div>
<div id="gform_title" class="page_title" style="margin-left:30px">
<img  src="../images/display_title.gif" width="150">

</div>
<div class="display_left_menu">
<a href="book.php"><img src="../images/reservation-button.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;
<?php

if ($role == "driver") {
		echo '<a href="javascript:void(0)" id="confirm"><img src="../images/confirm-button.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		
				
	}else {
		echo '<a href="javascript:void(0)" id="edit"><img src="../images/editdata-button1.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		
	}
	
if ($role =='admin' || $role=='uniresort' || $role="driver") {
	echo '<a href="javascript:void(0)" id="complete"><img src="../images/complete.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
	if($role=='admin'){
		echo '<a href="javascript:void(0)" id="lock"><img src="../images/lock.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<a href="javascript:void(0)" id="unlock"><img src="../images/unlock.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
	
	}
	echo "<p align='right'><a href='complete.php'>->Go to Completed List</a></p>";
	echo "<p align='right'><a href='cancel.php'>->Go to Canceled List</a></p>";
} 
if($role=='admin'){

	echo "<p align='right'><a href='modify.php'>->Go to Modified List</a></p>";
}

	
	
?>

</div>
<div id="output"> </div>

<script type="text/javascript">
 
var aDataSet =  <?php echo $result?>;

$(document).ready(function() {
	$('#output').html( '<table cellpadding="0" cellspacing="0" border="0" class="display" id="example"></table>' );
	
	var oTable = $('#example').dataTable( {
		"aaData": aDataSet,
		"aoColumns": <?php echo $title;?>,
		"aaSorting": [[ 0, "desc" ]],
		"sPaginationType": "full_numbers",
		"iDisplayLength": 50,
        "bScrollCollapse" : true,
        "bPaginate" : true, 
        "bSort" : true,
        "sDom": 'T<"clear">lfrtip',
        "tableTools": { "sSwfPath": "../libraries/TableTools/swf/copy_csv_xls_pdf.swf" }
      
	});	
	$('#edit').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('update.php',{id:data[0]});
		
	} );
	$('#complete').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('update.php',{complete:data[0]});
		
		
	} );


	$('#lock').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('update.php',{lock:data[0]});
		
		
	} );
	$('#unlock').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('update.php',{unlock:data[0]});
		
		
	} );
	 $('#confirm').click( function() {
			var anSelected = fnGetSelected( oTable );
			var data = oTable.fnGetData(anSelected);
			
			postwith('update.php',{pickup_confirm:data[0]});
		
		} );
	
	
	$("#example tbody").click(function(event) {
		$(oTable.fnSettings().aoData).each(function (){
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
	});
	new FixedHeader( oTable );
	
	


} );	

</script>

</div>
</div>
</div>
</div>
</section>
