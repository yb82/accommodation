<?php
/*
 *		Author : Young Bo KIM
*		Date   : 01 /02 /2016
* 		Version: 1.1
*

* 	Class Name  	: Booking
* 	Description		: Bookview sends post data to only this class. 
* 					  This class handles all user input data in order to save and send all detail to a student and an agent. 
*
*/

class Booking {
	private $db_connection = null;
	private $id, $fee_added, $ref, $st_id, $f_name, $l_name, $gender,  $email, $contact, $dob, $nation, $room_type, $check_in, $weeks, $pickup, $arrival_date, $arrival_time, $f_num, $airport_id, $airport, $pickup_price, $comment, $room_price, $user_id, $user_full_name, $user_agency, $user_contact, $user_email,$agent_email;
	private $countries = "";
	private $rooms = "";
	private $airports = "";
	private $isbooked = false;
	private $placement_fee ="";
	private $lang = array();
	public $errors = array();
	private $uniqueID ;
	private $tempDateOfBirth;
	public $error;
	private $created_id;
	private $doublebedValue = "COUPLE DOUBLE BED(WINDOW) -2017-";
	private $twinBedValue = "TWIN (WINDOW, BOOKING FOR 2 PEOPLE) -2017-";
	private $partnersName ="";
/* 
 *  @Constructor 
 *  
 * 	Description: this constructor generate options to display on the booking screen.
 *    			 Moreover, it checks post data which comes from bookview.
 *       
 */
	
	
	public function __construct() {
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		if ($this->databaseConnection ()) {
			
			$query = $this->db_connection->prepare ( 'SELECT * FROM nationality' );
			
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					
					$this->countries .= sprintf ( "\t" . '<option value="%1$s">%2$s</option>' . "\n", $result [0], $result [0] );
					
				}
			}
			
			$query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= CURDATE() ORDER BY ABS( DATEDIFF(  CURDATE(),  `price_expirydate` )   ) , room_type asc LIMIT 6 ' );
		
			$query->execute ();
			$results = $query->fetchAll ();
			$i = 0;
			//$j = 0;
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					
					
						if($i == 4 ){
							$this->rooms .= sprintf ( '<input type="radio" id="radioDoublebed" value= "%2$s" name="room" > %3$s </option> <div id="doublebed"  style="display:none;inline">Write the name of the person you will share with <input name="partner" /></div><br/>',$i ,$result [0], $result [0]);

						}elseif($i==5){
							$this->rooms .= sprintf ( '<input type="radio" id="radioTwinbed" value= "%2$s" name="room"> %3$s </option><div id="twinbed"  style="display:none;inline">Write the name of the person you will share with <input name="sharemate" /></div><br/>',$i ,$result [0], $result [0]);;

						}
						else {$this->rooms .= sprintf ( '<input type="radio" id="radiobutton%1$s" value= "%2$s" name="room"> %3$s </option><br/>',$i ,$result [0], $result [0]);
						}
					
					$i++;
				
				}
			}
			
			$query = $this->db_connection->prepare ( 'SELECT price FROM accommodation_fees WHERE price_expirydate >= CURDATE() AND fee_name =  "placement fee" ORDER BY ABS( DATEDIFF(  CURDATE(),  `price_expirydate` ) ) LIMIT 1' );
				
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
			
				foreach ( $results as $result ) {
					$this->placement_fee = $result[0];
			
				}
			}
			
			
			$query = $this->db_connection->prepare ( 'SELECT airport_name,airport_pickup_price  FROM airport WHERE price_expirydate >= CURDATE()  ORDER BY ABS( DATEDIFF(  CURDATE(),  `price_expirydate` ) ), airport_name LIMIT 3' );
			
			$query->execute ();
			$results = $query->fetchAll ();
			$i=1;
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					$this->airports .= sprintf ( '<input type="radio" value="%1$s"  id="pickup'.$i.'" name="pickup" /> %2$s </input><br/>', $result [0], $result [0] );
					$i++;
				}
			}
		}
		
		if (isset ( $_POST ["f_name"] )) {
			
			if (! empty ( $_SESSION ['user_id'] )) {
				$this->user_id = $_SESSION ['user_id'];
				$user_data = array ();
				$user_data = $this->get_user_data ( $this->user_id );
				if (! empty ( $user_data )) {
					$this->user_full_name = $user_data ['user_first_name'] . " " . $user_data ['user_last_name'];
					$this->user_email = $user_data ['user_email'];
					$this->user_agency = $user_data ['user_agency_name'];
					$this->user_contact = $user_data ['user_contact_number'];
				}
			}
			$this->st_id = $_POST ['st_id'];
			$this->f_name = strtoupper ($_POST ['f_name']);
			$this->l_name = strtoupper ($_POST ['l_name']);
			$this->gender = $_POST ['gender'];
			$this->email = $_POST ['email'];
			
			//This time it's  compulsory!!!!
			//not compulsory anymore
			
			if (isset($_POST['agent_email'])) {
				$this->agent_email= $_POST['agent_email'];;
			} else $this->agent_email = null;
			
	$error .= "hello0";		
			$this->nation = $_POST ['nationality'];
			
			
			$this->contact = $_POST ['contact'];
			$this->tempDateOfBirth=$tmpdate = $_POST ['dob'];
			$this->dob = $this->dateToDB ( $tmpdate );
			$this->room_type = $_POST ['room'];
			if($this->room_type == $this->doublebedValue ){
				if( isset($_POST['partner'])){
					$this->partnersName = $_POST['partner'];
					$error .= "hello0<br/>";
				}
			}
			if( $this->room_type == $this->twinBedValue ){
				if( isset($_POST['sharemate'])){
					$this->partnersName = $_POST['sharemate'];
				}
			}
			 				

			if (isset( $_POST ['check_in'])) {
				$tmpdate = $_POST ['check_in'];
				$this->check_in = $this->dateToDB ( $tmpdate );
			} else $this->check_in = null;
			
			
			
			if (isset ( $_POST ['pickup'] ) && $_POST ['pickup'] != "0") {
				$this->pickup = 1;
				$this->airport = $_POST ['pickup'];
			} else {
				$this->pickup = 0;
				$this->airport = null;
			}
			if (isset ( $_POST ['arrival_date'] ) && $this->pickup == 1) {
				$tmp1 = $_POST ['arrival_date'];
				$this->arrival_date = $this->dateToDB ( $tmp1 );
			} else
				$this->arrival_date = null;
			
			if (isset ( $_POST ['arriaval_time'] ) && ! empty ( $this->arrival_date )) {
				
				$tmp = new DateTime ( $_POST ['arriaval_time'] );
				$this->arrival_time .= " " . $tmp->format ( "H:i:s" );
			}
			
			$this->f_num = $_POST ['f_num'];
			
			$this->comment = $_POST ['comment'];
			
			$this->weeks = 4;
			
			$this->insertData ();
		}
		
	}
	/*
	 *  Data connection method.
	 *  
	 */
	
	private function databaseConnection() {
		// connection already opened
		if ($this->db_connection != null) {
			return true;
		} else {
			// create a database connection, using the constants from config/config.php
			try {
				$this->db_connection = new PDO ( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS );
				return true;
				// If an error is catched, database connection failed
			} catch ( PDOException $e ) {
				//$this->errors [] = $this->lang ['Database error'];
				return false;
			}
		}
	}
	
	/*
	 *  value nesting method with html tags.
	 */
	
	private function check_gender($val) {
		if (! is_null ( $val )) {
			if ($val == 1) {
				echo '<td><img src = ./icon/blueball.png></img></td>';
			} else {
				echo '<td><img src = ./icon/redball.png></img></td>';
			}
		}
	}
	private function check_bool($val) {
		if (! is_null ( $val )) {
			if ($val == 1) {
				echo '<td><img src = ./icon/blueflag.png></img></td>';
			} else {
				echo '<td><img src = ./icon/redflag.png></img></td>';
			}
		}
	}
	private function writetag($val) {
		echo '<td>' . $val . '</td>';
	}
	private function check_null($val) {
		if (is_null ( $val ) || ($val == 0)) {
			echo '<td><img src = ./icon/smallx.png></img></td>';
		} else {
			echo '<td>' . $val . '</td>';
		}
	}
	
	/* 
	 *  For testing purpose but changed into email.
	 *  This output data will be sent to both a student and agent as submission letter. 
	 */
	private function table_display_all() {
		
		
		
		$gender="";
		$pickup="";
		if ($this->gender==1) {
			$gender =  "MALE";
		}else $gender ="FEMALE";
		if (isset($this->pickup)&& $this->pickup == 1) {
			$pickup  ="YES" ;
		}else $pickup="NO";
		$arrival="";
		if (empty($this->arrival_date)){
			$arrival ="NONE";
		}else {
			$arrival =$this->dbToDate( $this->arrival_date )." ".date(" h:i A",strtotime($this->arrival_time)); 
		} 
		
		$output = '<html><body><table cellpadding="0" cellspacing="0" style="border: 5px ;solid #FFFFFcolor:#202020 ">
				
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">STUDENT ID</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->st_id.'</td>
				</tr>				
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">STUDENT NAME</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->f_name.' '.$this->l_name.'</td>
				</tr>				
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">GENDER</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$gender.'</td>
				</tr>
				
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">EMAIL</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->email.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">CONTACT NO.</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->contact.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">DATE OF BIRTH</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->dbToDate($this->dob).'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">NATIONALITY</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->nation.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">ROOM TYPE</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->room_type.'</td>
				</tr>	
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">CHECK-IN DATE</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'. (empty($this->check_in) ? 'NONE' : Booking::dbToDate ( $this->check_in )).'</td>
				</tr>	
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">STAY WEEKS</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->weeks.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">PICKUP</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$pickup.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">ARRIVAL DATE & TIME</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$arrival.'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">FLIGHT NUMBER</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->f_num) ? 'NONE' : $this->f_num).'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">AIRPORT</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->airport) ? 'NONE' : $this->airport).'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">COMMENT</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->comment) ? 'NONE' : $this->comment).'</td>
				</tr>
				<tr>
				<td style="width:150pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">Agent Email</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->agent_email) ? 'NONE' : $this->agent_email).'</td>
				</tr>
				
				
		</table><br/><br/><br/>
		
		If you would like to update any details or change your booking, please go to Manage your booking here: http://www.ihbrisbane.com.au/Apps/bookingaccommodation/<br/>  
		
		<img src="cid:footer"/><br/></body></html>
		';
		
		
		return $output;
	}
	/*
	 *  Only testing purpose these select methods.
	 *  
	 */
	private function dbquery_select($col, $table, $colname, $cond) {
		$return_value = null;
		if ($this->databaseConnection ()) {
			
			$query = $this->db_connection->prepare ( 'SELECT :col FROM :table WHERE :colname = :cond' );
			$query->bindValue ( ':col', $col, PDO::PARAM_STR );
			$query->bindValue ( ':table', $table, PDO::PARAM_STR );
			$query->bindValue ( ':colname', $colname, PDO::PARAM_STR );
			$query->bindValue ( ':cond', $cond, PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
			if (count ( $results ) > 0) {
				foreach ( $results as $result ) {
					if (! is_null ( $result )) {
						
						foreach ( $result as $output ) {
							$return_value = $output;
						}
					}
				}
			} else
				return null;
		}
		return $return_value;
	}
	private function dbquery_select2($col1, $col2, $table, $colname, $cond) {
		$return_value = array ();
		if ($this->databaseConnection ()) {
			
			$query = $this->db_connection->prepare ( 'SELECT :col1, :col2 FROM :table WHERE :colname = :cond' );
			$query->bindValue ( ':col', $col1, PDO::PARAM_STR );
			$query->bindValue ( ':col', $col2, PDO::PARAM_STR );
			$query->bindValue ( ':table', $table, PDO::PARAM_STR );
			$query->bindValue ( ':colname', $colname, PDO::PARAM_STR );
			$query->bindValue ( ':cond', $cond, PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
			if (count ( $results ) > 0) {
				foreach ( $results as $result ) {
					if (! is_null ( $result )) {
						
						foreach ( $result as $output ) {
							$return_value [] = $output;
						}
					}
				}
			} else
				return null;
		}
		return $return_value;
	}
	
	/*
	 *  check current login user data.
	 */
	private function get_user_data($id) {
		if ($this->databaseConnection ()) {
			
			$newquery = $this->db_connection->prepare ( 'SELECT user_first_name, user_last_name, user_email, user_contact_number, user_agency_name FROM users WHERE user_id = :user_id' );
			$newquery->bindValue ( ':user_id', $id, PDO::PARAM_INT );
			$newquery->execute ();
			$newresult = $newquery->fetchAll ();
			if (count ( $newresult ) > 0) {
				/*
				 * foreach ($newresult as $newresult){ foreach ($newresult as $key => $value) $return_value[]=$col; }
				 */
				
				foreach ( $newresult as $result )
					// foreach ($result as $key => $value)
					return $result;
			}
		}
		
		/*
		 * $query = $this->db_connection->prepare ( 'SELECT user_first_name, user_last_name, user_email, user_contact_number, user_agency_name, user_account_type FROM users WHERE user_id = :user_id' ); $query->bindValue ( ':user_id', $this->user_id, PDO::PARAM_INT ); $query->execute (); $results = $query->fetchAll (); if (count ( $results ) > 0) { foreach ( $results as $result ) { foreach ( $result as $output ) { $return_value [] = $output; } return $return_value; } } else return null;
		 */
	}
	
	/*
	 * Due to the difference between user input form and database date form,
	 * date has to be changed into database form, and vice versa
	 */
	
	public static function dateToDB($date) {
		$date = str_replace ( '/', '-', $date );
		$date = date ( 'Y-m-d', strtotime ( $date ) );
		return $date;
	}
	public static function dbToDate($date) {
		$date = date ( 'd-m-Y', strtotime ( $date ) );
		$date = str_replace ( '-', '/', $date );
		return $date;
	}
	public function isBooked() {
		return $this->isbooked;
	}
	public function setBooked($flag){
		$this->isbooked=$flag;
	}
	
	/*
	 * Insert all user input data into database.
	 * Even though jquery check all input data, unexpected data sometimes comes from view. 
	 */
	
	public function insertData() {
		if ($this->databaseConnection ()) {
			$query = $this->db_connection->prepare ( 'insert into booking (booking_st_id,  booking_st_first_name, booking_st_last_name, booking_st_gender,
										    booking_st_email,  booking_st_contact_num,  booking_st_dob,  booking_nationality,  booking_room,
										  booking_check_in,  booking_weeks,  booking_pickup,  booking_arrival_date,booking_arrival_time,  booking_flight_num,  booking_airport,
										   booking_user_id, booking_user_full_name,user_email,user_contact_number,user_agency_name, booking_comment, booking_time_stamp, booking_updated_time,booking_partner)
					              values (:st_id,:f_name,:l_name,:gender,
											:email,:contact,:dob,:nation,:room,
											:check_in,:weeks,:pick_up,:arrival_date,:arrival_time,:f_num,:airport,
											:user_id,:user_full_name,:user_email,:user_contact,:user_agency,:comment, now(),now(),:partner)' );
			if (! isset ( $this->st_id )|| empty($this->st_id )|| $this->st_id=="0" ) {
				$this->st_id = null;
			}
			$query->bindValue ( ':st_id', $this->st_id, PDO::PARAM_INT );
			$query->bindValue ( ':f_name', $this->f_name, PDO::PARAM_STR );
			$query->bindValue ( ':l_name', $this->l_name, PDO::PARAM_STR );
			$query->bindValue ( ':gender', $this->gender, PDO::PARAM_INT );
			
			$query->bindValue ( ':email', $this->email, PDO::PARAM_STR );
			$query->bindValue ( ':contact', $this->contact, PDO::PARAM_STR );
			$query->bindValue ( ':dob', $this->dob, PDO::PARAM_STR );
			$query->bindValue ( ':nation', $this->nation, PDO::PARAM_INT );
			$query->bindValue ( ':room', $this->room_type, PDO::PARAM_INT );
			


			$query->bindValue ( ':check_in', $this->check_in, PDO::PARAM_STR );
			$query->bindValue ( ':weeks', $this->weeks, PDO::PARAM_INT );
			$query->bindValue ( ':pick_up', $this->pickup, PDO::PARAM_INT );
			if (! isset ( $this->arrival_date )) {
				$this->arrival_date = null;
			}
			$query->bindValue ( ':arrival_date', $this->arrival_date, PDO::PARAM_STR );
			if (! isset ( $this->arrival_time )) {
				$this->arrival_time = null;
			}
			$query->bindValue ( ':arrival_time', $this->arrival_time, PDO::PARAM_STR );
			
			if (! isset ( $this->f_num )) {
				$this->f_num = null;
			}
			$query->bindValue ( ':f_num', $this->f_num, PDO::PARAM_STR );
			
			if (! isset ( $this->airport )) {
				$this->airport = null;
			}
			$query->bindValue ( ':airport', $this->airport, PDO::PARAM_INT );
			
			if (! isset ( $this->user_id )) {
				$this->user_id = null;
				
				$this->user_full_name = null;
				$this->user_contact = null;
				
				$this->user_agency = null;
			}
			if(isset($this->partnersName)){
				$query->bindValue ( ':partner', $this->partnersName, PDO::PARAM_STR );
			} else $query->bindValue ( ':partner', "", PDO::PARAM_STR );
			if(!is_null( $this->agent_email ) || strlen ($this->agent_email) !=0 ){
				$this->user_email = $this->agent_email;					
			}
			
			$this->createID();
			
			$user_data = array ();
			$user_data = $this->get_user_data ( $this->created_id );
			if (! empty ( $user_data )) {
				$this->user_full_name = $user_data ['user_first_name'] . " " . $user_data ['user_last_name'];
				//$this->user_email = $user_data ['user_email'];
				//$this->user_agency = $user_data ['user_agency_name'];
				//$this->user_contact = $user_data ['user_contact_number'];
			}
			
			$query->bindValue ( ':user_id', $this->created_id, PDO::PARAM_INT );
			$query->bindValue ( ':user_full_name', $this->user_full_name, PDO::PARAM_STR );
			if(!is_null( $this->agent_email ) || strlen ($this->agent_email) > 0 ){
				$query->bindValue ( ':user_email', $this->agent_email, PDO::PARAM_STR );
			}//else $query->bindValue ( ':user_email', $this->user_email, PDO::PARAM_STR );
			
			$query->bindValue ( ':user_contact', $this->user_contact, PDO::PARAM_STR );
			$query->bindValue ( ':user_agency', $this->user_agency, PDO::PARAM_STR );
			
			if (! isset ( $this->comment )) {
				$this->comment = null;
			}
			
			$query->bindValue ( ':comment', $this->comment, PDO::PARAM_STR );
			$success=$query->execute ();
			if ($success) {
				$this->sendEmail ( $this->email, $this->f_name . " " . $this->l_name );
				//$this->createID();
				$this->isbooked = true;
			} else $this->isbooked = false;
			
			
		}
	}
	private function createID(){
		$reg = new Registration();
		$takeFirstPart = explode(" ", $this->f_name);
		$user_name = $takeFirstPart[0].uniqid();
		$user_email = $this->email;
		$user_password = $user_password_repeat = $this->clean($this->tempDateOfBirth);
		$user_first_name = $this->f_name;
		$user_last_name = $this->l_name;
		$this->error.=$this->created_id=$reg->registerNewUser1($user_name, $user_email, $user_password, $user_password_repeat, $user_first_name, $user_last_name, $this->agent_email);
		
		//$this->error.= "hello3<br/>" ;
	}
	private function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
	
	//Get methods to print options on bookview.
	
	public function printCountry() {
		return $this->countries;
	}
	public function printRoomType() {
		return $this->rooms;
	}
	public function printAirport() {
		return $this->airports;
	}
	
	public function printPlacementFee(){
		return $this->placement_fee;
	}
	
	/*
	 * Send email to both a student and an agent with booking details.
	 */
	
	private function sendEmail($email, $fullname) {
		$mail = new PHPMailer ();
		
		// please look into the config/config.php for much more info on how to use this!
		// use SMTP or use mail()
		if (EMAIL_USE_SMTP) {
			// Set mailer to use SMTP
			$mail->IsSMTP ();
			// useful for debugging, shows full SMTP errors
			// $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
			// Enable SMTP authentication
			$mail->SMTPAuth = EMAIL_SMTP_AUTH;
			// Enable encryption, usually SSL/TLS
			if (defined ( EMAIL_SMTP_ENCRYPTION )) {
				$mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
			}
			// Specify host server
			$mail->Host = EMAIL_SMTP_HOST;
			$mail->Username = EMAIL_SMTP_USERNAME;
			$mail->Password = EMAIL_SMTP_PASSWORD;
			$mail->Port = EMAIL_SMTP_PORT;
		} else {
			$mail->IsMail ();
		}
		
		$mail->From = EMAIL_VERIFICATION_FROM;
		$mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
		
		
		$mail->AddAddress ( $email );  
		//if(!is_null($this->user_contact)){
		if(!is_null($this->agent_email)|| strlen($this->agent_email) >0 ){
			$mail->AddCC($this->agent_email);
		}
		//}
		
		$mail->AddBCC("support@ihbrisbane.com.au");
		

		
		$mail->AddBCC("vitor@ihbrisbane.com.au");
		$mail->AddBCC("japan@ihbrisbane.com.au");
		$mail->AddBCC("brian@ihbrisbane.com.au");
		$mail->AddBCC("enrol@ihbrisbane.com.au");
		$mail->AddBCC("james@ihbrisbane.com.au");
		$mail->AddBCC("marketing@ihbrisbane.com.au");
		$mail->AddBCC("middleeast@ihbrisbane.com.au");
		$mail->AddBCC("uniresort@uniresort.com.au");
		
		$mail->Subject = EMAIL_BOOKING_NEW_TITLE." Student Name: ".$fullname;
		$mail->AddEmbeddedImage("../images/footer.jpg", "footer");
		
		
		// the link to your register.php, please set this value in config/email_verification.php
		$mail->Body = '<lable style="font-size:15.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020;">'.$fullname . EMAIL_BOOKING_ADD_COMMENT."</lable><br/><br/>".$this->table_display_all();
		
		
		
		$mail->IsHTML(true);
		if (! $mail->Send ()) {
			$this->errors [] = $this->lang ['Verification mail not sent'] . $mail->ErrorInfo;
			return false;
		} else {
			return true;
		}  
	}
	
	public function get_user_email() {
		if ($this->databaseConnection ()) {
			if(isset($_POST['studentemail'])){
			$email 	= $_POST['studentemail'];
			$newquery = $this->db_connection->prepare ( 'SELECT user_email FROM `users` WHERE `user_email` = :email' );
			$newquery->bindValue ( ':email', $email, PDO::PARAM_STR );
			$newquery->execute ();
			$newresult = $newquery->fetchAll ();
				if (count ( $newresult ) > 0) {
				/*
				 * foreach ($newresult as $newresult){ foreach ($newresult as $key => $value) $return_value[]=$col; }
				 */
			
					foreach ( $newresult as $result )
					// foreach ($result as $key => $value)
							return json_encode ($result);
				}
			}
		}
	
		
		return "[]";
	}
	/*
	 * public function get_id() { return $this->id; } public function get_fee_added() { return $this->fee_added; } public function get_ref() { return $this->ref; } public function get_st_id() { return $this->st_id; } public function get_f_name() { return $this->f_name; } public function get_l_name() { return $this->l_name; } public function get_gender() { return $this->gender; } public function get_addr() { return $this->addr; } public function get_email() { return $this->email; } public function get_contact() { return $this->contact; } public function get_dob() { return $this->dob; } public function get_nation_id() { return $this->nation_id; } public function get_nationality() { return $this->nation; } public function get_room_id() { return $this->room_id; } public function get_room_type() { return $this->room_type; } public function get_room_price() { return $this->room_price; } public function get_check_in() { return $this->check_in; } public function get_weeks() { return $this->weeks; } public function get_pickup() { return $this->pickup; } public function get_f_num() { return $this->f_num; } public function get_airport_id() { return $this->airport_id; } public function get_airport() { return $this->airport; } public function get_pickup_price() { return $this->pickup_price; } public function get_comment() { return $this->comment; } public function get_user_id() { return $this->user_id; } // set funtions public function set_fee_add($val) { $this->fee_added = $val; } public function set_ref($val) { $this->ref = $val; } public function set_student_id($val) { $this->$st_id = $val; } public function set_f_name($val) { $this->f_name = $val; } public function set_l_name($val) { $this->l_name = $val; } public function set_gender($val) { $this->gender = $val; } public function set_addr($val) { $this->addr = $val; } public function set_email($val) { $this->email = $val; } public function set_contact($val) { $this->conatct = $val; } public function set_($val) { $this->$dob = $val; } public function set_nation_id($val) { $this->nation_id = $val; } public function set_room_id($val) { $this->room_id = $val; } public function set_room_type($val) { $this->$room_type = $val; } public function set_check_in($val) { $this->$check_in = $val; } public function set_weeks($val) { $this->weeks = $val; } public function set_pickup($val) { $this->pickup = $val; } public function set_f_num($val) { $this->f_num = $val; } public function set_airport_id($val) { $this->airport_id = $val; } public function set_airport($val) { $this->airport = $val; } public function set_pickup_price($val) { $this->pickup_price = $val; } public function set_comment($val) { $this->comment = $val; } public function set_user($val) { $this->user = $val; } public function set_room_price($val) { $this->room_price; } public function set_user_name($val) { $this->user_name = $val; } public function set_user_agency($val) { $this->user_agency = $val; } public function set_user_contact($val) { $this->user_contact = $val; }
	 */
}