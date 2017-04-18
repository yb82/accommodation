
<?php
// include html header and display php-login message/error
include ('./_header.php');

?>
<link rel="stylesheet" id="Demopage-css" href="../datatables/css/demo_table.css" type="text/css" >
<link rel="stylesheet" id="Demopage-css" href="../libraries/TableTools/css/dataTables.tableTools.min.css" type="text/css" >  
  
<script type="text/javascript" src="../jquery/jquery-1.9.1.js"></script>

<script type="text/javascript" src="../datatables/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="../datatables/js/custom.field.js"></script>
<script type="text/javascript" src="../libraries/TableTools/js/dataTables.tableTools.min.js"></script>
<section class="inner_topbanner_wrapper sixteen columns page_template">
	<div class="intro_topbanner"></div>
</section>             
<section id="page_template" class="display_inner_content_body_wrapper">
<div class="inner_content_body">

	<div class="">

	<div class="gform_wrapper display">
    <?php
$complete = new Complete ();
if ($complete->isLogin ()) {
	$result = $complete->createDataSet ();
	
	$title = $complete->createTitle ();

	$role = $complete->getRole();
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
<img  src="../images/complete_title.png" width="150">

</div>
<div class="display_left_menu">

<?php
if($role !='driver'){
echo '<a href="javascript:void(0)" id="edit"><img src="../images/editdata-button1.gif"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
echo '<a href="javascript:void(0)" id="back"><img src="../images/move.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
}

if ($role=='uniresort') {
	
	echo '<a href="javascript:void(0)" id="fullrefund"><img src="../images/fully.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a href="javascript:void(0)" id="partialrefund"><img src="../images/partial.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;';
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
		"iDisplayLength": 50,
        "bScrollCollapse" : true,
        "bPaginate" : true, 
        "bSort" : true,
        "sDom": 'T<"clear">lfrtip',
        "tableTools": { "sSwfPath": "../libraries/TableTools/swf/copy_csv_xls_pdf.swf" }
      
	});	
	$('#fullrefund').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('completeupdate.php',{full:data[0]});
		
	} );
	$('#partialrefund').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('completeupdate.php',{partial:data[0]});
		
	} );

	
	 $('#edit').click( function() {
		var anSelected = fnGetSelected( oTable );
		var data = oTable.fnGetData(anSelected);
		
		postwith('completeupdate.php',{id:data[0]});
		
	} );
	 $('#back').click( function() {
			var anSelected = fnGetSelected( oTable );
			var data = oTable.fnGetData(anSelected);
			
			postwith('completeupdate.php',{back:data[0]});

		} );
		
	 $('#yes').click( function() {
			var anSelected = fnGetSelected( oTable );
			var data = oTable.fnGetData(anSelected);
			
			postwith('completeupdate.php',{yes:data[0]});

		} );
	 $('#no').click( function() {
			var anSelected = fnGetSelected( oTable );
			var data = oTable.fnGetData(anSelected);
			
			postwith('completeupdate.php',{no:data[0]});

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
