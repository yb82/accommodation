
<?php
// include html header and display php-login message/error
include ('./_header.php');

?>
<link rel="stylesheet" id="Demopage-css" href="../datatables/css/demo_table.css" type="text/css" >

  
<script type="text/javascript" src="../jquery/jquery-1.9.1.js"></script>

<script type="text/javascript" src="../datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="../datatables/js/custom.field.js"></script>

<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>
</section>             
<section id="page_template" class="display_inner_content_body_wrapper">
<div class="inner_content_body">

	

	<div class="gform_wrapper display">
    <?php
$cancel = new Cancel ();
if ($cancel->isLogin ()) {
	$result = $cancel->createDataSet ();
	
	$title = $cancel->createTitle ();

	$role = $cancel->getRole();
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
<img  src="../images/cancel_title.png" width="150">

</div>
<div class="display_left_menu">
<!--  <a href="book.php"><img src="images/reservation-button.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;-->
<?php

if ($role=='admin') {
		
	echo '<a href="javascript:void(0)" id="remove"><img src="../images/remove.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a href="javascript:void(0)" id="move"><img src="../images/move.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
}

echo "<p align='right'><a href='display.php'>->Go to Booking List</a></p>";
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
		
        "bScrollCollapse" : true,
        "bPaginate" : true, 
        "bSort" : true,
      
	});	
	$('#remove').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('cancelupdate.php',{remove:data[0]});
		
	} );
	$('#move').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('cancelupdate.php',{cancelmove:data[0]});
		
	} );
	
		
	
	$("#example tbody").click(function(event) {
		$(oTable.fnSettings().aoData).each(function (){
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
	});
	
	


} );	

</script>

</div>
</div>
</div>
</div>
</section>
