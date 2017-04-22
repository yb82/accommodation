<?php
//require_once 'classes/Booking.php';
class Displaying {
	private $lang = array();
	protected $db_connection = null;
	public $outputa = "";
	private $title = null;
	private $coutries = array ();
	private $rooms = array ();
	private $airports = array ();
	private $login = false;
	private $status ;
	//public $error ;
	//protected $id,$ref, $dob, $nation_id, $nation, $room_id, $room_type, $check_in, $weeks, $pickup, $arrival, $f_num, $airport_id, $airport, $pickup_price, $comment, $room_price, $user_id, $user_name, $user_agency, $user_contact, $user_email, $user_role, $user_first_name, $user_last_name,$lock;

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
		//$this->coutries = $this->selectCountryAll ();
		//$this->rooms = $this->selectRoomAll ();
		//$this->airports = $this->selectAirportAll ();
		//$this->status= $this->selectStatusAll();
	}
	 public function getRole(){
		return $this->user_role;
	}
	
	public function isLogin() {
		return $this->login;
	}
	/*
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
	} */
	protected function databaseConnection() {
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
	protected function get_user_data($id) {
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
	
	protected function checkGender($data){
		$tag="";
		if ($data==1) {
			$tag='<img src="../icon/men.gif" height="25" width="25">';
		} else $tag ='<img src="../icon/women.gif" height="25" width="25">';
		return $tag;			
				
	}
	protected function checkGenderToText($data){
		$tag="";
		if ($data==1) {
			$tag='MALE';
		} else $tag ='FEMALE';
		return $tag;
	
	}
	protected function checkBoolean($data){
		$tag='';
		
		if ($data>0) {
			$tag='<img src="../icon/gf.png" height="25" width="25">';
		} else $tag ='<img src="../icon/rf.png" height="25" width="25">';
		return $tag;				
	}
	protected function checkFee($data){
		$tag='';
	
		if ($data>0) {
			$tag='<img src="../icon/money-ok.gif" height="25" width="25">';
		} else $tag ='<img src="../icon/money-no.gif" height="25" width="25">';
		return $tag;
	}
	protected function checkStatus($data){
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
				$query = $this->db_connection->prepare ( 'SELECT id_booking,booking_confirmed, booking_updated_time ,booking_st_id, booking_st_first_name,booking_st_last_name,  booking_check_in ,
														booking_fee_added_offer,booking_lock ,booking_ref_num,booking_st_gender, booking_nationality,  booking_room , booking_partner,
														booking_weeks ,  booking_pickup ,booking_pickup_confirm,  booking_arrival_date ,  booking_arrival_time ,  booking_flight_num ,
														booking_airport  ,  booking_bed_code ,  booking_room_code ,  booking_front_door_code  ,
													    booking_pgate_code,   booking_st_email,
														booking_st_contact_num, booking_st_dob ,  booking_user_full_name,user_email,user_contact_number,user_agency_name,  booking_comment, booking_time_stamp FROM booking where booking_confirmed is null or booking_confirmed = "Ready" or booking_confirmed = "Confirmed"  order by id_booking DESC'  );
			
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
					//status
					$output[1]=$this->checkStatus($output[1]);
					
					//check in date
					//$output[5] = Booking::dbToDate($output[5]);
					
					//fee check
					$output[7]=$this->checkFee($output[7]);
					
					//Check lock status
					$output[8]=$this->checkBoolean($output[8]);

					
										
					
					
					// gender
					$output[10]=$this->checkGender($output[10]);
	
					// pickup
					$output[15]=$this->checkBoolean($output[15]);
					$output[16]=$this->checkBoolean($output[16]);

					//arrival date
					if (!is_null($output[17])) {
						//$output[18] = Booking::dbToDate($output[1]);
					}
					//arrival date
					if (!is_null($output[18])) {
						$date = new DateTime($output[18]);
						$output[18]= $date->format('H:i');
					}
					//date of birth
					if (!is_null($output[28])) {
						//$output[27] = Booking::dbToDate($output[25]);
					}

					
					
					
					
					$datetime = new DateTime($output[2]);
					$output[2]= $datetime->format("d/m/Y h:i:s A");
				

				
						
					$all [$i ++] = $output;
					unset ( $output );
				}
				return $all;
				
			} elseif ($this->user_role == "agent") {
				
				$query = $this->db_connection->prepare ( 'SELECT id_booking, booking_st_id, booking_st_first_name,booking_st_last_name,booking_check_in,
															 booking_room,booking_weeks,booking_pickup,booking_arrival_date,booking_arrival_time,
															booking_flight_num,booking_airport,booking_bed_code,booking_room_code,booking_front_door_code,
															booking_pgate_code, booking_time_stamp FROM booking WHERE booking_user_id = :cond order by id_booking DESC' );
				$query->bindValue ( ':cond', $this->user_id, PDO::PARAM_INT );
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
						
				
					$output[4] = Booking::dbToDate($output[4]);
					// pickup
					$output[7]=$this->checkBoolean($output[7]);
						
					//arrival date		
					if (!is_null($output[8])) {
						$output[8] = Booking::dbToDate($output[8]);
					}
					
					$datetime = new DateTime($output[16]);
					$output[16]= $datetime->format("d/m/Y h:i:s A");
					
						
				
										
				
					$all [$i ++] = $output;
					unset ( $output );
				}
				return $all;
				
				
			} elseif ($this->user_role == "uniresort") {
				$query = $this->db_connection->prepare ( 'SELECT id_booking, booking_ref_num,booking_st_id,booking_st_first_name, booking_st_last_name,booking_check_in,booking_confirmed,booking_fee_added_offer,
															booking_st_gender, booking_st_email, booking_nationality, 
															booking_bed_code,booking_room_code,booking_front_door_code,booking_pgate_code,booking_room, booking_partner, booking_weeks,
															booking_time_stamp  FROM booking WHERE booking_confirmed = "Ready" OR booking_confirmed = "Modified"  OR booking_confirmed = "Confirmed" order by id_booking DESC' );
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
				
		
					//$output[5] = Booking::dbToDate($output[5]);
										
					// status of booking
					
					$output[6]=$this->checkStatus($output[6]);
						
					
					$output[7]=$this->checkFee($output[7]);
					$output[8]=$this->checkGenderToText($output[8]);
					$datetime = new DateTime($output[18]);
					$output[18]= $datetime->format("d/m/Y h:i:s A");
					$all [$i ++] = $output;
					unset ( $output );
				}
				return $all;
			} elseif ($this->user_role == "driver") {
				$query = $this->db_connection->prepare ( 'SELECT id_booking, booking_st_id,booking_st_first_name,booking_st_last_name, booking_st_gender, booking_nationality,booking_pickup_confirm,
															booking_arrival_date,booking_arrival_time,booking_flight_num,booking_airport,booking_bed_code,booking_room_code,booking_front_door_code,booking_pgate_code,
															booking_time_stamp ,booking_comment FROM booking WHERE booking_pickup = 1 and booking_confirmed ="Confirmed" ORDER BY booking_arrival_date DESC' );
				$flag = true;
				$query->execute ();
				$result = $query->fetchAll ();
					
				$i = 0;
				foreach ( $result as $row ) {
					
					$j = 0;
					foreach ( $row as $key => $value ) {
						if ($key == $j && $key != 16) {
							$flag = true;
							$output [] = $value;
							$j ++;
						} else{
							if (strpos($value,'ALS')!==false) {
								$flag = false;
							}
						}
							
					}
					$output[6]=$this->checkBoolean($output[6]);
					$output[4]=$this->checkGender($output[4]);
							//arrival date		
					/* if (!is_null($output[6])) {
						$output[6] = Booking::dbToDate($output[6]);
					} */
					$datetime = new DateTime($output[15]);
					$output[15]= $datetime->format("d/m/Y h:i:s A");
					
						
					// pickup
					if ($flag) {
						$all [$i ++] = $output;
					}
					
				
					
					unset ( $output );
				}
				return $all;
			} /* elseif ($this->user_role == "student") {
				$query = $this->db_connection->prepare ( 'SELECT id_booking, booking_st_id, booking_st_first_name,booking_st_last_name,booking_check_in,
							booking_room,booking_weeks,
							booking_pickup,booking_arrival_date,booking_arrival_time,booking_airport,
							booking_bed_code,booking_room_code,booking_front_door_code,booking_pgate_code, booking_time_stamp FROM booking WHERE booking_user_id = :cond order by id_booking DESC' );
				
				$query->bindValue ( ':cond', $this->user_id, PDO::PARAM_INT );
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
				
					
					$output[4] = Booking::dbToDate($output[4]);
					$output[7]=$this->checkBoolean($output[7]);
					if (!is_null($output[8])) {
						$output[8] = Booking::dbToDate($output[8]);
					}
					$datetime = new DateTime($output[15]);
					$output[15]= $datetime->format("d/m/Y h:i:s A");
					
						
				
				
				
					$all [$i ++] = $output;
					unset ( $output );
				}
				return $all;
			} */
		}
		
	
			
			
		
		// if (isset ( $query )) { $query->execute (); $result = $query->fetchAll (); $count = count ( $result ); $all = array (); $output = array (); foreach ( $result as $row ) { // //////////////////// sprintf////////////////////////////// // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!create data set
	}
	public function createTitle() {
		if ($this->user_role == "admin") {
			$this->title = '[{sTitle:"NO."},{sTitle: "STATUS"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;Updated Time&nbsp;&nbsp;&nbsp;&nbsp;"},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "CHECK_IN DATE"},{sTitle: "FEE"},{sTitle: "LOCK"},{sTitle: "REF NO."},{sTitle: "GENDER"},{sTitle: "NATIONALITY"},{sTitle: "ROOM TYPE"},{sTitle: "PARTNER NAME"},{sTitle: "STAY WEEKS"},{sTitle: "PICK UP"},{sTitle: "Flight Schedule Confirmed"},{sTitle: "ARRIVAL_DATE"},{sTitle: "ARRIVAL TIME"},{sTitle: "FLIGHT NO."},{sTitle: "AIRPORT"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "STUDENT_EMAIL"},{sTitle: "CONTACT"},{sTitle: "&nbsp;&nbsp;&nbsp;DATE OF BIRTH&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"},{sTitle: "CREATOR"},{sTitle: "AGENT_EMAIL"},{sTitle: "CONTACT"},{sTitle: "AGENCY"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;COMMENT&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"},{sTitle: "&nbsp;&nbsp;&nbsp;&nbsp;Created Time&nbsp;&nbsp;&nbsp;&nbsp;"}]';
		} elseif ($this->user_role == "agent") {
			$this->title = '[{sTitle: "NO."},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "CHECK IN DATE"},{sTitle: "ROOM TYPE"},{sTitle: "STAY WEEKS"},{sTitle: "PICK UP"},{sTitle: "ARRIVAL DATE" },{sTitle: "ARRIVAL TIME"},{sTitle: "FLIGHT NO."},{sTitle: "AIRPORT"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "CREATED TIME"}]';
		} elseif ($this->user_role == "uniresort") {
			$this->title = '[{sTitle: "NO."},{sTitle: "REF NO."},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "CHECK IN DATE"},{sTitle: "STATUS"},{sTitle: "FEE"},{sTitle: "GENDER"},{sTitle: "STUDENT_EMAIL"},{sTitle: "NATIONALITY"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "ROOM TYPE"},{sTitle: "PARTNER NAME"},{sTitle: "STAY WEEKS"},{sTitle: "CREATED TIME"}]';
		} elseif ($this->user_role == "driver") {
			$this->title = '[{sTitle: "NO."},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "GENDER"},{sTitle: "NATIONALITY"},{sTitle: "Flight Schedule Confirmed"},{sTitle: "ARRIVAL DATE"},{sTitle: "ARRIVAL TIME"},{sTitle: "FLIGHT NO."},{sTitle: "AIRPORT"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "CREATED TIME"}]';
		} /* elseif ($this->user_role == "student") {
			$this->title = '[{sTitle: "NO."},{sTitle: "STUDENT NO."},{sTitle: "FIRST NAME"},{sTitle: "LAST NAME"},{sTitle: "CHECK IN DATE"},{sTitle: "ROOM TYPE"},{sTitle: "STAY WEEKS"},{sTitle: "PICK UP"},{sTitle: "ARRIVAL DATE"},{sTitle: "ARRIVAL TIME"},{sTitle: "AIRPORT"},{sTitle: "BED NO."},{sTitle: "ROOM CODE"},{sTitle: "FRONT DOOR CODE"},{sTitle: "GATE CODE"},{sTitle: "CREATED TIME"}]';
		} */
		
		if (isset ( $this->title )) {
			return $this->title;
		}
	}
	
}

?>