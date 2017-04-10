/**
 * 
 */
jQuery(function($) {
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
			$('#edit').click( function() {
				var anSelected = fnGetSelected( oTable );
				var data = oTable.fnGetData(anSelected);
				
				postwith('update.php',{id_edit:data[0]});
				
			} );
			$('#delete').click( function() {
				var anSelected = fnGetSelected( oTable );
				var data = oTable.fnGetData(anSelected);
				
				postwith('update.php',{id_delete:data[0]});
				
			} );
			$("#example tbody").click(function(event) {
				$(oTable.fnSettings().aoData).each(function (){
					$(this.nTr).removeClass('row_selected');
				});
				$(event.target.parentNode).addClass('row_selected');
			});
			
			


		} );	
		