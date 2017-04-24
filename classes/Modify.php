<?php
//require_once 'classes/Booking.php';
class Modify {
	private $lang = array();
	private $db_connection = null;
	public $outputa = "";
	private $title = null;
	private $coutries = array ();
	private $rooms = array ();
	private $airports = array ();
	private $login = false;
	private $status ;
	private $id, $dob, $nation_id, $nation, $room_id, $room_type, $check_in, $weeks, $pickup, $arrival, $f_num, $airport_id, $airport, $pickup_price, $comment, $room_price, $user_id, $user_name, $user_agency, $user_contact, $user_email, $user_role, $user_first_name, $user_last_name;

	public function __construct() {
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		
		
		
		if (! empty ( $_SESSION ['user_id'] )) {
			$this->user_id = $_SESSION ['user_id'];
			$result = array ();
			$result = $this->get_user_data ( $this->user_id );
			// foreach ($result as $key => $value)
			$this->user_first_name = $result ['user_first_name'];
			$this->user_last_name = $result ['user_last_name'];
			$this->user_email = $result ['user_email'];
			$this->user_agency = $result ['user_agency_name'];
			$this->user_contact = $result ['user_contact_number'];
			$this->user_role = $result ['user_account_type'];
			$this->login = true;
		} else
			$this->login = false;
	
	}
	public function getRole(){
		return $this->user_role;
	}
	public function isLogin() {
		return $this->login;
	}
	public function getCountries() {
		return $this->coutries;
	}
	public function getAirports() {
		return $this->airports;
	}
	public function getRooms() {
		return $this->rooms;
	}
	public function getStatus(){
		return $this->status;
	}
	private function databaseConnection() {
		// connection already opened
		if (! is_null ( $this->db_connection )) {
			return true;
		} else {
			// create a database connection, using the constants from config/config.php
			try {
				$this->db_connection = new PDO ( 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS );
				return true;
				// If an error is catched, database connection failed
			} catch ( PDOException $e ) {
				$this->errors [] = $this->lang ['Database error'];
				return false;
			}
		}
	}
	
	
	private function get_user_data($id) {
		if ($this->databaseConnection ()) {
			
			$newquery = $this->db_connection->prepare ( 'SELECT user_first_name, user_last_name, user_email, user_contact_number, user_agency_name, user_account_type FROM users WHERE user_id = :user_id' );
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
	}
	
	private function checkGender($data){
		$tag="";
		if ($data>0) {
			$tag='<img src="../icon/men.gif" height="25" width="25">';
		} else $tag ='<img src="../icon/women.gif" height="25" width="25">';
		return $tag;			
				
	}
	private function checkBoolean($data){
		$tag='';
		
		if ($data>0) {
			$tag='<img src="../icon/gf.png" height="25" width="25">';
		} else $tag ='<img src="../icon/rf.png" height="25" width="25">';
		return $tag;				
	}
	private function checkFee($data){
		$tag='';
	
		if ($data>0) {
			$tag='<img src="../icon/money-ok.gif" height="25" width="25">';
		} else $tag ='<img src="../icon/money-no.gif" height="25" width="25">';
		return $tag;
	}
	private function checkStatus($data){
		// status of booking
		$tag="";
		if ($data=="Ready") {
			$tag='<img src="../icon/ready.gif" height="24" width="67"></img>';
		} else if ($data=="Confirmed") {
			$tag='<img src="../icon/confirm.gif" height="24" width="67"></img>';
		} else if ($data=="Modified") {
			$tag='<img src="../icon/modified.gif" height="24" width="67"></img>';
		}  else if ($data=="Canceled") {
			$tag='<img src="../icon/cancel.gif" height="24" width="67"></img>';
		} else 
			$tag ='<img src="../icon/new.gif" height="24" width="67"></img>';
		return $tag;
	}
	public function createDataSet() {
		$this->title = array ();
		$all = array ();
		$output = array ();
		$query = "";
		if ($this->databaseConnection ()) {
			if ($this->user_role == "admin") {
				$query = $this->db_connection->prepare ( 'SELECT id_booking,  booking_st_id, booking_st_first_name,booking_st_last_name,  booking_check_in ,
														booking_fee_added_offer,booking_ref_num,booking_st_gender,   booking_st_email,
														booking_st_contact_num, booking_st_dob , booking_nationality,  booking_room ,
														booking_weeks ,  booking_pickup ,booking_pickup_confirm,  booking_arrival_date ,  booking_arrival_time ,  booking_flight_num ,
														booking_airport  ,  booking_bed_code ,  booking_room_code ,  booking_front_door_code  ,
													    booking_pgate_code, booking_user_full_name,user_email,user_contact_number,user_agency_name,  booking_comment, booking_time_stamp FROM booking where booking_confirmed= "Modified" order by id_booking DESC ' );
				$query->execute ();
				$result = $query->fetchAll ();
					
				$i = 0;
				foreach ( $result as $row ) {
					// //////////////////// sprintf//////////////////////////////
					// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!create data set
					$j = 0;
					foreach ( $row as $key => $value ) {
						if ($key == $j) {
							$output [] = $value;
							$j ++;
						}
					}
					
					//$output[1]=$this->checkStatus($output[1]);
					
					//$output[5] = Booking::dbToDate($output[5]);
					//fee check
					$output[5]=$this->checkFee($output[5]);
						
					

					// status of booking
										
					
					
					// gender
					
					$output[7]=$this->checkGender($output[7]);
	
					if (!is_null($output[10])) {
						$output[10] = Booking::dbToDate($output[10]);
					}

					// pickup
					
					$output[14]=$this->checkBoolean($output[14]);
					$output[15]=$this->checkBoolean($output[15]);
					//if (!is_null($output[17])) {
						//$output[18] = Booking::dbToDate($output[18]);
					//}
					
					$datetime = new DateTime($output[29]);
					$output[30]= $datetime->format("d/m/Y h:i:s A");
				
						
					$all [$i ++] = $output;
					unset ( $output );
				}
				return json_encode($all);
				
			} 
		}
		
		
		// if (isset ( $query )) { $query->execute (); $result = $query->fetchAll (); $count = count ( $result ); $all = array (); $output = array (); foreach ( $result as $row ) { // //////////////////// sprintf////////////////////////////// // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!create data set
	}
	public function createTitle() {
		if ($this->user_role == "admin") {
			$this->title = '[{sTitle:"NO."},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "CHECK_IN DATE"},{sTitle: "FEE"},{sTitle: "REF NO."},{sTitle: "GENDER"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ADDRESS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"	 },{sTitle: "EMAIL"},{sTitle: "CONTACT"},{sTitle: "DATE OF BIRTH"},{sTitle: "NATIONALITY"},{sTitle: "ROOM TYPE"},{sTitle: "STAY WEEKS"},{sTitle: "PICK UP"},{sTitle: "PICK UP CONFIRM"},{sTitle: "ARRIVAL_DATE"},{sTitle: "ARRIVAL TIME"},{sTitle: "FLIGHT NO."},{sTitle: "AIRPORT"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "CREATOR"},{sTitle: "EMAIL"},{sTitle: "CONTACT"},{sTitle: "AGENCY"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;COMMENT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;Created Time&nbsp;&nbsp;&nbsp;&nbsp;"}]';
		} 
		
		if (isset ( $this->title )) {
			return $this->title;
		}
	}
}

?>