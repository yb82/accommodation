<?php
const MINWEEKS = 4;
const MAXWEEKS = 8;
class DisplayStudent{
	private $id,$fee_added,$lock,$ref, $confirmed,	$st_id,	$f_name,	$l_name,	$gender,	$email,	$contact,	$dob ,	$nation,	$room_type,	$check_in ,	$weeks ,	$pickup ,	$arrival_date ,	$arrival_time ,	$f_num ,	$airport,	$comment ,	$bed_code,	$room_code,	$front_door_code,	$pgate_code, $booing_created_time, $user_id, $user_name, $user_agency, $user_contact, $user_email, $user_role, $user_first_name, $user_last_name;
	public $error;
	private $status;
	protected $db_connection = null;


	public function __construct() {
		
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		$this->error.="hello2";
		$this->error.=$_SESSION ['user_id'] ;
		if (! empty ( $_SESSION ['user_id'] )) {
			$this->error.="<br/>hello user ID".$this->user_id = $_SESSION ['user_id'];
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
		$this->findLastRecord();
	}
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
	private function findLastRecord(){
		//protected function getPreviouseInfo($id) {
		if ($this->databaseConnection ()) {
			/*
			 * id_booking , booking_fee_added_offer, booking_ref_num , booking_confirmed, booking_st_id , booking_st_first_name , booking_st_last_name , booking_st_gender , booking_st_addr , booking_st_email , booking_st_contact_num , booking_st_dob , booking_nationality , booking_room VARCHAR(20) , booking_check_in , booking_weeks , booking_pickup , booking_arrival_date , booking_arrival_time , booking_flight_num , booking_airport , booking_comment , booking_bed_code , booking_room_code , booking_front_door_code , booking_pgate_code
			 */
			
			$query = $this->db_connection->prepare ( 'SELECT id_booking,booking_fee_added_offer,booking_lock, booking_ref_num,booking_confirmed,booking_st_id, booking_st_first_name,booking_st_last_name, booking_st_gender, 
							booking_st_email, booking_st_contact_num, booking_st_dob, booking_room,booking_check_in,booking_weeks,booking_pickup, 
							booking_arrival_date,booking_arrival_time,booking_flight_num,booking_airport,booking_comment,
							booking_bed_code,booking_room_code,booking_front_door_code,booking_pgate_code, booking_time_stamp, booking_nationality FROM booking WHERE booking_user_id = :cond order by id_booking DESC LIMIT 1' );
			
			
			
			
			$query->bindValue ( ":cond", $this->user_id , PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
				
			if (count ( $results ) > 0) {
				foreach ( $results as $row ) {
					$this->error.= $this->id = $row [0];
					$this->error.=$this->fee_added = $row [1];
					$this->error.=$this->lock =$row [2];
					$this->error.=$this->ref = $row [3];
					$this->error.=$this->confirmed = $row [4] ;
					$this->error.=$this->st_id = $row [5];
					$this->error.=$this->f_name = $row [6];
					$this->error.=$this->l_name = $row [7];
					$this->error.=$this->gender = $row [8];
					
					$this->error.=$this->email = $row [9];
					$this->error.=$this->contact = $row [10];
					$this->error.=$this->dob = Booking::dbToDate($row [11]);
					$this->error.=$this->room_type = $row [12];
					$this->error.=$this->check_in = Booking::dbToDate($row [13]);
					$this->error.=$this->weeks = $row [14];
					$this->error.=$this->pickup = $row [15];
					$this->error.=$this->arrival_date =Booking::dbToDate( $row [16]);
					$this->error.=$this->arrival_time = $row [17];
					$this->error.=$this->f_num = $row [18];
					$this->error.=$this->airport = $row [19];
					$this->error.=$this->comment = $row [20];
					$this->error.=$this->bed_code = $row [21];
					$this->error.=$this->room_code = $row [22];
					$this->error.=$this->front_door_code = $row [23];
					$this->error.=$this->pgate_code = $row [24];
					
					$this->error.=$this->booing_created_time = Booking::dateToDB(substr($row[25], 0, 10));
					$this->error.=$this->nation = $row[26];
					$this->error .="hello 0<br/>";
	
				}
			}
		}
		
		
	
	}
	private function makeTag($value, $flag) {
		$disable = "";
		if (! $flag) {
			$disable = 'readonly';
		}
		if (! empty ( $value )) {
			return "\"" . $value . "\"" . " " . $disable;
		} else
			return "\"\" " . " " . $disable;
	}
	public function getID(){
		return $this->id;		
	}

	public function getNationality(){
		return $this->makeTag($this->nation,0);
	}
	
	public function getReferenceNumber(){
		if ($this->ref == null || $this->ref == 0)
			return '""' ;
		return $this->ref;
	}
	public function getFeeAdded(){
		return $this->fee_added;
	}
	public function getBookingStatus(){
		return $this->makeTag($this->confirmed,0);
	}
	
	public function getStudentID(){
		return  $this->makeTag($this->st_id,1);	
	}
	
	public function getStudentFName(){
		return  $this->makeTag($this->f_name,1);
		
	}
	public function getStudentLName(){
		return  $this->makeTag($this->l_name,1);
		
	}
	public function getGender(){
		
		return $this->gender;
	}

	public function getEmail(){
		return  $this->makeTag($this->email,0);
	}
	public function getContact(){
		return  $this->makeTag( $this->contact,1);
	}
	public function getDOB(){
		return $this->dob;
	}
	
	public function getRoomType(){
		$query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= "'.$this->booing_created_time.'"  ORDER BY ABS( DATEDIFF(  "'.$this->booing_created_time.'",  `price_expirydate` ) ) LIMIT 4' );
		$query->execute ();
		$results = $query->fetchAll ();
		$rooms = "<select name='room' >";
		
		
		
		
		if (count ( $results ) > 0) {
		
			foreach ( $results as $result ) {
				if (isset ( $this->room_type ) && $result [0] == $this->room_type) {
		
					$rooms .= sprintf ( '<option value="%1$s" selected="selected">%2$s</option>' . "\n", $result [0], $result [0] );
				} elseif (!$this->lock){
					//$this->airports .= sprintf ( '<input type="radio" value= "%1$s" name="pickup" id="pickup'.$i.'"/> %2$s </input><br/>', $result [0], $result [0] );
				}
				else
					$rooms .= sprintf ( '<option value="%1$s">%2$s</option>' . "\n", $result [0], $result [0] );
				
			}
		
		}
		$rooms .="</select>";
		
		
		
		return $rooms;
	}
	public function getCheckInDate(){
		$output ="";
		if($this->lock){
			$output='<input type="text" name="check_in" id="checkin1" value="'.$this->check_in.'" />';
		}
		else 
			$output= '<input type="text" name="check_in"  value="'.$this->check_in.'" readonly/>';
		return $output;
		
		
	}
	public function getWeeks(){
		$weeks = "<select name ='stay_weeks'>";
		for ($i =  MINWEEKS; $i<= MAXWEEKS ;$i++ ){
			if($i == $this->weeks){
				$weeks .= sprintf ( '<option value="%1$s" selected="selected">%2$s</option>' . "\n", $i, $i );
			} elseif (!$this->lock){
				
			} else
				$weeks .= sprintf ( '<option value="%1$s">%2$s</option>' . "\n", $i, $i );
							
		}
		return $weeks.="</select>";
	}
	
	public function getArrivalDate() {
		$output ="";
		if($this->lock){
			$output='<input type="text" name="arrival_date"	id="arrivalDate" value="'.$this->arrival_date.'" />';
		}
		else 
			$output='<input type="text" name="arrival_date"	 value="'.$this->arrival_date.'" readonly/>';
		return $output;	
	}
	public function getArrivalTime(){
		return $this->makeTag($this->arrival_time,$this->lock);	
	} 
	public function getFlightNo(){
		return $this->makeTag($this->f_num,$this->lock);	
	}
	public function getAirport(){
		$this->error .="hello 0<br/>";
		$query = $this->db_connection->prepare ( 'SELECT airport_name,airport_pickup_price  FROM airport WHERE price_expirydate >= "'.$this->booing_created_time.'"  ORDER BY ABS( DATEDIFF(  "'.$this->booing_created_time.'",  `price_expirydate` ) ), airport_name LIMIT 3' );
			
		$query->execute ();
		$i=1;
		$airports = "<select name ='pickup'>";
		$results = $query->fetchAll ();
		if ($this->pickup != 1) {
			$airports .= sprintf ( '<option value= "0"  selected="selected"/> No pickup desired</input><br/>' );
		} elseif (!$this->lock) {
			//$this->airports = sprintf ( '<input type="radio" value= 0 id="pickup0" name="pickup" /> No Pickup Desired</input><br/>' );
		} else {
			$airports .= sprintf ( '<option value= "0" /> No Pickup desired</input><br/>' );
		}
		
		if (count ( $results ) > 0) {
			
			foreach ( $results as $result ) {
				if (isset ( $this->airport ) && $result [0] == $this->airport) {
					
					$airports .= sprintf ( '<option value= "%1$s" selected="selected" /> %2$s </input><br/>', $result [0], $result [0] );
				} elseif (!$this->lock){
					//$this->airports .= sprintf ( '<input type="radio" value= "%1$s" name="pickup" id="pickup'.$i.'"/> %2$s </input><br/>', $result [0], $result [0] );
				}
				else{
					$airports .= sprintf ( '<option  value= "%1$s" /> %2$s </input><br/>', $result [0], $result [0] );
					
				}
				$i++;
				
				
				
			}
		
		}
				
		return 	$airports."</select>";

		
		
		
		
		
	}
	public function getComment(){
		return $this->makeTag($this->comment,1);	
	}
	public function getBedCode() {
		return $this->makeTag($this->bed_code,0);
	}	
	public function getRoomCode(){
		return $this->makeTag($this->room_code,0);
	}
	public function getFrontDoorCode(){
		return $this->makeTag($this->front_door_code,0);
	}
	public function getPGateCode(){
		return $this->makeTag($this->pgate_code,0);
	}
	public function getStudentGender() {
		$option1 = "";
		$option2 = "";
		$disable = "";
		$output = "<select name='gender'>";
		if (0) {
			if ($this->gender == 1) {
				$output .= "<option value='1' selected >Male</option></select>";
			} else {
				$output .= "<option value='0' selected>Female</option></select>";
			}
		} else {
			if ($this->gender == 1) {
				$option1 = "selected";
			} else {
				$option2 = "selected";
			}
			
			$output .= "<option value='1' " . $option1 . " >Male</option><option value='0' " . $option2 . ">Female</option></select>";
		}
		return $output;
	}
	
	
}