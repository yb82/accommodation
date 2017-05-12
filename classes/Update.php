<?php
/*
 *		Author : Young Bo KIM
 *		Date   : 18 /08 /2014
 * 		Version: 1.0 
 *		Updated: 20/11/2015 
 *		Update Detail: calculate the price with booking date.
	
 */




// PDF Libriry to create updated confirmation letter.



require_once ('../libraries/pdflib/tcpdf.php');
/* 
 *  User defined PDF class.
 *  A background image(IH Brisbane Logo) will be placed in every single pages.
 * 
 */

class MYPDF extends TCPDF {
	// Page header
	public function Header() {
		// get the current page break margin
		$bMargin = $this->getBreakMargin ();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak ( false, 0 );
		// set bacground image
		$img_file = '../images/background.jpg';
		$this->Image ( $img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0 );
		// restore auto-page-break status
		$this->SetAutoPageBreak ( $auto_page_break, $bMargin );
		// set the starting point for the page content
		$this->setPageMark ();
	}
}

/*
 * 	Class Name  	: Update
 * 	Description		: this class has a number of methods which update a booking records for each user.
 * 	Prerequisite 	: a record must be selected
 * 	 
 */

define("SECURITYDEPOSIT", 200);
define("PLACEMENTFEE",150);

class Update {
	protected  $role, $db_connection;
	protected $id, $fee_added, $ref, $confirmed, $st_id, $f_name, $l_name, $gender, $email, $contact, $dob, $nation, $room_type, $room_price, $room_after_price, $check_in, $weeks, $pickup, $pickup_price, $arrival_date, $arrival_time, $f_num, $airport, $comment, $user_id, $user_full_name, $user_agency, $user_contact, $user_email;
	protected $new_id, $new_fee_added, $new_ref, $new_confirmed, $new_st_id, $new_f_name, $new_l_name, $new_gender, $new_email, $new_contact, $new_dob, $new_nation, $new_room_type, $new_check_in, $new_weeks, $new_pickup, $new_arrival_date, $new_arrival_time, $new_f_num, $new_airport, $new_pickup_price, $new_comment, $new_room_price;
	protected $bed_code, $room_code, $front_door_code, $pgate_code;
	protected $new_bed_code, $new_room_code, $new_front_door_code, $new_pgate_code;
	protected $countries, $rooms, $airports, $status;
	protected $f_st_id, $f_fee_added, $f_ref, $f_confirmed, $f_f_name, $f_l_name, $f_gender,  $f_email, $f_contact, $f_dob, $f_nation, $f_room_type, $f_check_in, $f_weeks, $f_pickup, $f_arrival_date, $f_arrival_time, $f_f_num, $f_airport, $f_comment;
	protected $f_bed_code, $f_room_code, $f_front_door_code, $f_pgate_code;
	protected $is_updated = false;
	protected $reason_check_in, $reason_room_type, $reason_confirmed;
	protected $reason_pickup, $reason_arrival_datetime, $reason_airport, $reason_weeks;
	protected $confimationLetter, $uniModifiedConfirmLetter, $PickupModifiedConfirmLetter, $cancelLetter;
	protected $mail;
	protected $filename;
	protected $current_check_in, $new_check_in_;
	protected $pickup_confirm;
	protected $is_update_confirm ;
	protected $is_removed;
	protected $pickupfee;
	public $error;
	protected $booing_created_time;
	protected $lock;
	
	protected $agent_email,$new_agent_email;

	// !!!!!!!!!!! this will check current booking status and once it updates, will update time stamp.
	protected $updateStatus = false;
/* 
 *  @Constructor 
 *  
 * 	Description: In user view pages, jQuery send post value and data to this update page.
 * 	In order to role based system, each user has different uer role, even an administrator cannot access a number of fields.
 *    
 *   
 */
	public function __construct() {
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		$this->initEmail ();
		
		
		// Check current loged in user's information.
		
		if (! empty ( $_SESSION ['user_id'] )) {
			$this->user_id = $_SESSION ['user_id'];
			$result = array ();
			$result = $this->get_user_data ( $this->user_id );
			$this->role = $result ['user_account_type'];
		}
		
		
	
		/* Check Buttons
		 * 
		 * There are a number of buttons on dispaly panels in different page.
		 * jQuery send post value with id number from view.
		 *  
		 */
		
		
		/* Displayview
		 * Complete button
		 * 
		 * Once a student checked in the accommodation, user can send the student to the completed booking list.
		 * 
		 */
		if (isset ( $_POST ['complete'])){
			
			$this->completeBooking($_POST['complete']);
			$this->is_update_confirm= true;
			
		}
		
		/* CancelView
		 * Remove button
		 * 
		 * remove a record which canceled.
		 * prerequisite: the record has been canceled.
		 */
		if (isset ( $_POST ['remove'])){
				
			$this->removeBooking($_POST['remove']);
			$this->is_removed= true;
				
		}
		
		/* CancelView 
		 * CancelMove Button
		 * This button will send a record to booking list, the record will be set as new record.
		 * prerequisite: the record has been canceled.
		 */
		
		if (isset ( $_POST ['cancelmove'])){
		
			$this->cancelmoveBooking($_POST['cancelmove']);
			$this->is_removed= true;
		}
		
		
		
		
		/* CompleteView
		 * Back Button
		 * This button will send a record to booking list, the record will be set as confirmed status.
		 * prerequisite: the record has been completed.
		 * 
		 */
		if (isset($_POST['back'])) {
			$this->backToList($_POST['back']);
			$this->is_update_confirm= true;;
		
		}
		
		/*
		 * CompleteView
		* Full return Button
		* This button will set a record as full refund status, which student can refund bond fee form IH Brisbane.
		* it does not use anymore.
		*
		*/
		if (isset($_POST['full'])) {
			$this->bondRefund($_POST['full'],"YES");
			$this->is_update_confirm= true;			
		}

		/*
		 * CompleteView
		* Partial return Button
		* This button will set a record as partial refund status, which student can refund bond fee form IH Brisbane.
		* it does not use anymore.
		*
		*/
		if (isset($_POST['partial'])) {
			$this->bondRefund($_POST['partial'],"NO");
			$this->is_update_confirm= true;;
		}
		
		/*
		 * CompleteView
		 * Partial return Button
		 * This button will set a record as partial refund status, which student can refund bond fee form IH Brisbane.
		 * it does not use anymore.
		 *
		 */
		if (isset($_POST['lock'])) {
			$this->lockRecord($_POST['lock']);
			$this->is_update_confirm= true;;
		}
		/*
		 * CompleteView
		 * Partial return Button
		 * This button will set a record as partial refund status, which student can refund bond fee form IH Brisbane.
		 * it does not use anymore.
		 *
		 */
		if (isset($_POST['unlock'])) {
			$this->unlockRecord($_POST['unlock']);
			$this->is_update_confirm= true;;
		}
		
		
		/*
		 * A user press update button 
		 */
		if (isset ( $_POST ['u_submit'] ) || isset ($_POST['student_changes'])) {
			if(isset($_POST ['booking_id'])){
				$this->new_id = $_POST ['booking_id'];
			
				if($this->checkCurrentLockStatus($_POST ['booking_id']) == 1)
				{
					$this->receiveData ();
					$this->getPreviouseInfo ( $this->new_id );
					$this->updateData ();
					$this->is_updated = true;
				}
			}
		} elseif (isset ( $_POST ['pickup_confirm'] )) {
			
			$this->new_id = $_POST ['pickup_confirm'];
			$this->changePickUpConfirm ( $this->new_id );
			$this->is_update_confirm = true;
		} elseif (isset ( $_POST ['id'] )) {
			
			// id=> record ID~~!!!!
			
			$this->new_id = $_POST ['id'];
			$f_weeks = false;
			if ($this->role == "admin") {
				$this->f_st_id = $this->f_fee_added = $this->f_confirmed = $this->f_st_id = $this->f_f_name = $this->f_l_name = $this->f_email = $this->f_contact = $this->f_dob = $this->f_nation = $this->f_room_type = $this->f_check_in = $this->f_pickup = $this->f_arrival_date = $this->f_arrival_time = $this->f_f_num = $this->f_airport = $this->f_comment = $this->f_bed_code = $this->f_room_code = $this->f_front_door_code = $this->f_pgate_code = $this->f_gender =$this->f_weeks= true;
				$this->f_ref = false;
			} elseif ($this->role == "agent" || $this->role == "student") {
				$this->f_f_name = $this->f_l_name = $this->f_gender  = $this->f_email = $this->f_contact = $this->f_dob = $this->f_nation = $this->f_room_type = $this->f_check_in = $this->f_pickup = $this->f_arrival_date = $this->f_arrival_time = $this->f_f_num = $this->f_airport = $this->f_comment = true;
				$this->f_st_id = $this->f_confirmed = $this->f_st_id = $this->f_fee_added = $this->f_ref = $this->f_bed_code = $this->f_room_code = $this->f_front_door_code = $this->f_pgate_code = false;
			} elseif ($this->role == "uniresort") {
				$this->f_st_id = $this->f_fee_added = $this->f_st_id = $this->f_f_name = $this->f_l_name = $this->f_gender  = $this->f_email = $this->f_contact = $this->f_dob = $this->f_nation = $this->f_pickup = $this->f_arrival_date = $this->f_arrival_time = $this->f_f_num = $this->f_airport =  false;
				$this->f_bed_code = $this->f_room_code = $this->f_front_door_code = $this->f_pgate_code = $this->f_room_type = $this->f_check_in = $this->f_ref = $this->f_comment = $this->f_confirmed = true;
			}
			
			$this->getPreviouseInfo ( $this->new_id );
			$this->generateOptions ();
		} 
	}
	
	// check a record romved
	
	public function is_removed(){
		return $this->is_removed;
	}
	
	/*
	 *  Send a canceled record to Booking List
	 *  prerequisite: the record has been canceled.
	 */
	private function cancelmoveBooking($id){
		if ($this->databaseConnection () ) {
		
			$newquery = $this->db_connection->prepare ( 'update booking set booking_confirmed=:status, booking_bond_refund=:bond WHERE id_booking = :id' );
			$newquery->bindValue(':status', null,PDO::PARAM_STR);	
			$newquery->bindValue(':bond', "-",PDO::PARAM_STR);
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
		}
	}
	
	/*
	 *  Remove a canceld record from database.
	 *  prerequisite: the record has been canceled.
	 */
	private function removeBooking($id){
		if ($this->databaseConnection ()&&$this->checkCurrentStatus($id) =='Canceled' ) {
				
			$newquery = $this->db_connection->prepare ( 'DELETE FROM booking where id_booking = :id' );
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
				
		}
		
	}
	
	/*
	 * Change a reocrd's bond status. 
	 * There are two types of bond status
	 * partial and full (1: full , 0 : partial, "-": default value)
	 * prerequisite: the record has been completed.
	 */
	
	private function bondRefund($id,$tag){
		if ($this->databaseConnection ()){
	
			$newquery = $this->db_connection->prepare ( 'update booking set booking_bond_refund=:status WHERE id_booking = :id' );
			$newquery->bindValue(':status', $tag,PDO::PARAM_STR);
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
		}
	}
	
	/*
	 *  Send a completed record to booking list with confirmed status
	 *  prerequisite: the record has been completed.
	 */
	private function backToList($id){
		if ($this->databaseConnection () ) {
				
			$newquery = $this->db_connection->prepare ( 'update booking set booking_confirmed=:status, booking_bond_refund=:bond WHERE id_booking = :id' );
			$newquery->bindValue(':status', "Confirmed",PDO::PARAM_STR);
			$newquery->bindValue(':bond', "-",PDO::PARAM_STR);
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
		}
	}
	
	/*
	 *  Send a record to completed list.
	 *  * prerequisite: the record has been confirmed.
	 */
	
	private function completeBooking($id) {
		if ($this->databaseConnection ()&&$this->checkCurrentStatus($id) =='Confirmed' ) {
			
			$newquery = $this->db_connection->prepare ( 'update booking set booking_confirmed=:complete WHERE id_booking = :id' );
			$newquery->bindValue(':complete', "Completed",PDO::PARAM_INT);			
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
			
		}
	}
	
	private function lockRecord($id) {
		if ($this->databaseConnection () ) {
				
			$newquery = $this->db_connection->prepare ( 'update booking set booking_lock=:lock WHERE id_booking = :id' );
			$newquery->bindValue(':lock', 0 , PDO::PARAM_INT);
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
				
		}
	}
	private function unlockRecord($id) {
		if ($this->databaseConnection ()) {
	
			$newquery = $this->db_connection->prepare ( 'update booking set booking_lock=:lock WHERE id_booking = :id' );
			$newquery->bindValue(':lock', 1 , PDO::PARAM_INT);
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
	
		}
	}
	
	
	
	/* Qurey method,
	 * this method get a value from database 
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
	
	
	/*
	 *  Check current only status.
	 *  Test purpose, not using anymore
	 * 
	 */
	private function checkCurrentStatus($id) {
		$return_value = null;
		
		if ($this->databaseConnection ()) {
				
			$query = $this->db_connection->prepare ( 'SELECT booking_confirmed FROM booking WHERE id_booking = :id' );
			
			$query->bindValue ( ':id', $id, PDO::PARAM_INT );
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
	
	public function checkCurrentLockStatus($id) {
		$return_value = null;
	
		if ($this->databaseConnection ()) {
	
			$query = $this->db_connection->prepare ( 'SELECT booking_lock FROM booking WHERE id_booking = :id' );
				
			$query->bindValue ( ':id', $id, PDO::PARAM_INT );
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
	
	
	
	/*
	 * get method for update.
	 */
		
	public function isUpdateConfirm() {
		return $this->is_update_confirm;
	}
	
	/*
	 *  Only driver users can change the pickup status from 0 to 1 but not vice versa
	 */
	
	private function changePickUpConfirm($id) {
		$current;
		if ($this->databaseConnection ()) {
			
			$newquery = $this->db_connection->prepare ( 'SELECT booking_pickup_confirm from booking WHERE id_booking = :id' );
			$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
			$newquery->execute ();
			$newresult = $newquery->fetchAll ();
			if (count ( $newresult ) > 0) {
				/*
				 * foreach ($newresult as $newresult){ foreach ($newresult as $key => $value) $return_value[]=$col; }
				 */
				
				foreach ( $newresult as $result ) {
					foreach ( $result as $key => $value ) {
						$current = $value;
					}
				}
			}
			if ($current == 0) {
				$current = 1;
				$newquery = $this->db_connection->prepare ( 'UPDATE booking set booking_pickup_confirm =:value WHERE id_booking = :id' );
				$newquery->bindValue ( ':value', $current, PDO::PARAM_INT );
				$newquery->bindValue ( ':id', $id, PDO::PARAM_INT );
				
				$newquery->execute ();
			}
		}
	}
	
	/*
	 *  Initialize email settings
	 *  
	 *  
	 */
	private function initEmail() {
		$this->mail = new PHPMailer ();
		
		// please look into the config/config.php for much more info on how to use this!
		// use SMTP or use mail()
		if (EMAIL_USE_SMTP) {
			// Set mailer to use SMTP
			$this->mail->IsSMTP ();
			// useful for debugging, shows full SMTP errors
			// $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
			// Enable SMTP authentication
			$this->mail->SMTPAuth = EMAIL_SMTP_AUTH;
			// Enable encryption, usually SSL/TLS
			if (defined ( EMAIL_SMTP_ENCRYPTION )) {
				$this->mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
			}
			// Specify host server
			$this->mail->Host = EMAIL_SMTP_HOST;
			$this->mail->Username = EMAIL_SMTP_USERNAME;
			$this->mail->Password = EMAIL_SMTP_PASSWORD;
			$this->mail->Port = EMAIL_SMTP_PORT;
		} else {
			$this->mail->IsMail ();
		}
		
		$this->mail->From = EMAIL_VERIFICATION_FROM;
		$this->mail->FromName = EMAIL_VERIFICATION_FROM_NAME;
	}
	/*
	 *  Receive data from updata form and store data in local variables.
	 *  This data will be used to compare with previous data for making a decision to set new status.
	 *    
	 */
	
	private function receiveData() {

		if ($_POST ['status'] == "0") {
			$this->new_confirmed = null;
		} else {
			$this->new_confirmed = $_POST ['status'];
		}
		
		$this->new_ref = $_POST ['ref_num'];
		$this->new_id = $_POST ['booking_id'];
		$this->new_bed_code = $_POST ['bed_code'];
		$this->new_room_code = $_POST ['room_code'];
		$this->new_front_door_code = $_POST ['front_code'];
		$this->new_pgate_code = $_POST ['gate_code'];
		$this->new_fee_added = $_POST ['fee_added'];
		$this->new_st_id = $_POST ['st_id'];
		$this->new_f_name = $_POST ['f_name'];
		$this->new_l_name = $_POST ['l_name'];
		$this->new_gender = $_POST ['gender'];
		$this->new_email = $_POST ['email'];
		if(isset($_POST['agent_email'])){
			$this->new_agent_email = $_POST['agent_email'];
		} else $this->new_agent_email = $this->agent_email;
		
		if(is_null($this->new_agent_email)|| $this->new_agent_email == "" ){
			$this->new_agent_email = $this->agent_email;
		} 
		
		
		$this->new_contact = $_POST ['contact'];
		$this->new_nation = $_POST ['nationality'];
		
		$this->new_dob = Booking::dateToDB ( $_POST ['dob'] );
		$this->new_room_type = $_POST ['room'];
		$this->new_check_in = Booking::dateToDB ( $_POST ['check_in'] );
		$this->new_weeks = $_POST ['stay_weeks'];
		$this->new_arrival_date = Booking::dateToDB ( $_POST ['arrival_date'] );
		$airport = $_POST ['pickup'];
		if ($airport == "0") {
			$this->new_pickup = $_POST ['pickup'];
			$this->new_airport = null;
			$this->new_arrival_date = null;
			$this->new_arrival_time = null;
			$this->new_f_num = null;
		} else {
			// if pick up has been chosen the other information are compulsary
			$this->new_pickup = 1;
			$this->new_airport = $_POST ['pickup'];
			$this->new_arrival_date = Booking::dateToDB ( $_POST ['arrival_date'] );
			$this->new_arrival_time = $_POST ['arrival_time'];
			$this->new_f_num = $_POST ['f_num'];
		}
		$this->new_comment = $_POST ['comment'];
	}
	
	/* 
	 * Generate an email based on entered user data.
	 * This method shows the stored data.
	 * 
	 */
	
	
	public function allUniDetails() {
		$output = "";
		
		
		$gender = "FEMALE";
		if ($this->new_gender) {
			$gender = "MALE";
		} 
				
		$pickup = "NO";
		if ($this->new_pickup == 1) {
			$pickup = "YES";
		}
		$arrival = "NONE";
		if (!empty($this->arrival_date)){
			$arrival =Booking::dbToDate( $this->new_arrival_date )." ".date(" h:i A",strtotime($this->new_arrival_time));
		}
		
		$output='<table cellpadding="0" cellspacing="0" style="border: 5px ;solid #FFFFFcolor:#202020 ">
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">BOOKING NO.</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_ref.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">CURRENT STATUS</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_confirmed.'</td>
				</tr>
				
				</table>
				<br/>
				<br/>
				<table cellpadding="0" cellspacing="0" style="border: 5px ;solid #FFFFFcolor:#202020 ">
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">STUDENT ID</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_st_id.'</td>
				</tr>				
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">STUDENT NAME</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_f_name.' '.$this->new_l_name.'</td>
				</tr>				
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">GENDER</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$gender.'</td>
				</tr>
				
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">EMAIL</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->new_email.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">CONTACT NO.</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->new_contact.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">DATE OF BIRTH</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.Booking::dbToDate($this->new_dob).'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">NATIONALITY</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->new_nation.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">ROOM TYPE</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->new_room_type.'</td>
				</tr>	
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">CHECK-IN DATE</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'. (empty($this->new_check_in) ? 'NONE' : Booking::dbToDate ( $this->new_check_in )).'</td>
				</tr>	
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">STAY WEEKS</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$this->new_weeks.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">PICKUP</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$pickup.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">ARRIVAL DATE & TIME</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.$arrival.'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">FLIGHT TIME</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->new_f_num) ? 'NONE' : $this->new_f_num).'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">AIRPORT</td>
				<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->new_airport) ? 'NONE' : $this->new_airport).'</td>
				</tr>
				<tr>
				<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">COMMENT</td>
				<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">'.(empty($this->new_comment) ? 'NONE' : $this->new_comment).'</td>
				</tr>
				
		</table><br/><br/><br/>
		If you requiry any changes, please click on URL: http://www.ihbrisbane.com.au/Apps/bookingaccommodation/<br/>  
		<img src="cid:footer"/>';
		return $output;
	}
	
	/*
	 *  Send codes to students and agents to access to accommodation  
	 *  Bedcode separate into block, unit, room and bed code and display on Bed No.
	 *  
	 */
	private function KeyDetails() {
	
		$block = $this->new_bed_code[0];
		$unit = $this->new_bed_code[1].$this->new_bed_code[2];
		$roomNo =$this->new_bed_code[3];
		$bed = $this->new_bed_code[4];
		
		
		$output='<table cellpadding="0" cellspacing="0" style="border: 5px ;solid #FFFFFcolor:#202020 ">
	
		<tr>
	<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">BED NO.</td>
	<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">Block:'.$block.' Unit:'.$unit.
	' Room No.:'.$roomNo.' Bed:'.$bed.' </td>
	</tr>
	<tr>
	<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">ROOM ENTRY CODE</td>
	<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_room_code.'</td>
	</tr>
	<tr>
	<td style="width:200pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">FRONT DOOR CODE</td>
	<td style="width:310pt;background-color: #DEF4FF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_front_door_code.'</td>
	</tr>
	<tr>
	<td style="width:200pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020; ">PEDESTRIAN GATE CODE</td>
	<td style="width:310pt;background-color: #FFFFFF;padding-left:10px;font-size:11.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020 ;">'.$this->new_pgate_code.'</td>
	</tr>
			
		</table><br/><br/><br/>
		If you requiry any changes, please click on URL: http://www.ihbrisbane.com.au/Apps/bookingaccommodation/<br/>  
		<img src="cid:footer"/>';
		
		return $output;
	}
	
	
	/*
	 *  Update
	 *  
	 *  Compare current data and new data and change new status or send email to students and agents.
	 *  
	 */
	
	private function updateData() {
		
		
		
		/*
		 * Sending Entry code to student and agent 
		 * 
		 * 1: current code is empty and new code has been set.
		 * 2: current code is different to new code
		 * 
		 * Problem: if the field is empty, it sometimes send "0" string to database. need to figure out whether it is "0" or not.
		 * Tested, Okay
		 */
		
		
		$bedFlag = $frontGateFlag=$pGateFlag=$roomCodeFlag=false;
		
		if ((is_null ( $this->bed_code ) || empty ( $this->bed_code ) || $this->bed_code=="0")&& (!is_null ( $this->new_bed_code) && !empty($this->new_bed_code) && $this->new_bed_code!="0") ){
			$bedFlag = true;
		}elseif ((!is_null($this->bed_code)&&!empty($this->bed_code)&& $this->bed_code!="0") &&  (!is_null($this->new_bed_code)&&!empty($this->new_bed_code)&& $this->new_bed_code!="0") && $this->bed_code != $this->new_bed_code){
			$bedFlag = true;
		}
		
		
		if ((is_null ( $this->front_door_code ) || empty ( $this->front_door_code ) || $this->front_door_code=="0")&& (!is_null ( $this->new_front_door_code) && !empty($this->new_front_door_code) && $this->new_front_door_code!="0") ){
			$frontGateFlag = true;
		}elseif ((!is_null($this->front_door_code)&&!empty($this->front_door_code)&& $this->front_door_code!="0") &&  (!is_null($this->new_front_door_code)&&!empty($this->new_front_door_code)&& $this->new_front_door_code!="0") && $this->front_door_code != $this->new_front_door_code){
			$frontGateFlag = true;
		}
		

		if ((is_null ( $this->pgate_code ) || empty ( $this->pgate_code) || $this->pgate_code=="0")&& (!is_null ( $this->new_pgate_code) && !empty($this->new_pgate_code) && $this->new_pgate_code!="0") ){
			$pGateFlag = true;
		}elseif ((!is_null($this->front_door_code)&&!empty($this->front_door_code)&& $this->front_door_code!="0") &&  (!is_null($this->new_front_door_code)&&!empty($this->new_front_door_code)&& $this->new_front_door_code!="0") && $this->new_front_door_code != $this->new_front_door_code){
			$pGateFlag = true;
		}
		
		if ((is_null ( $this->room_code ) || empty ( $this->room_code) || $this->room_code=="0")&& (!is_null ( $this->new_room_code) && !empty($this->new_room_code) && $this->new_room_code!="0") ){
			$pGateFlag = true;
		}elseif ((!is_null($this->room_code)&&!empty($this->room_code)&& $this->room_code != "0") &&  (!is_null($this->new_room_code)&&!empty($this->new_room_code)&& $this->new_room_code!="0") && $this->room_code != $this->new_room_code){
			$roomCodeFlag = true;
		}
		
		// one of them has been changed, the codes will email to the student, agent and accommodtion email.
				
		if($bedFlag || $roomCodeFlag|| $frontGateFlag||$pGateFlag){
			$this->sendCodeEmail();
		}
		
		
		
		/* if ((empty($this->bed_code) && isset($this->new_bed_code)) ||($this->bed_code != $this->new_bed_code)) {
			$bedFlag=true;
		} elseif ((empty($this->room_code)&&isset($this->new_room_code))||($this->room_code != $this->new_room_code)){
			$roomCodeFlag=true;
		} elseif ((empty($this->front_door_code)&&isset($this->new_front_door_code))||($this->front_door_code != $this->new_front_door_code)){
			$frontGateFlag= true;
		} elseif ((empty($this->pgate_code)&&isset($this->new_pgate_code))||($this->pgate_code != $this->new_pgate_code)){
			$pGateFlag = true;
		}
		
		if($bedFlag || $roomCodeFlag|| $frontGateFlag||$pGateFlag){
			$this->sendCodeEmail();
		}
		 */
		
		
		
		
		
		
		/*
		 *  during updating records, empty fields are filled with "0" on a number of browsers, 
		 *  Before changing status of the booking, this condition will ckeck all values.
		 *  if any values of the text fields are entered with "0", this condition will put null instead of "0"
		 *  
		 *  
		 *  *  Reference number and student ID are the key values to confirm a booking records.
		 *  *  Check Reference number and student ID everytime update value and if "0", change to "null" value.
		 *   
		 */
		
			
		if ($this->new_ref == "0" || empty ( $this->new_ref ) || ! isset ( $this->new_ref )) {
			$this->new_ref = null;
		}
		if (! isset ( $this->new_st_id ) || empty ( $this->new_st_id ) || $this->new_st_id == "0") {
			$this->new_st_id = null;
		}
		$this->reason_check_in = $this->reason_room_type = $this->reason_confirmed = false;
		$this->reason_pickup = $this->reason_arrival_datetime = $this->reason_airport = false;
		$this->confimationLetter = false;
		$this->uniModifiedConfirmLetter = false;
		$this->PickupModifiedConfirmLetter = false;
		$this->cancelLetter = false;
		
		
		// change user entered date to database form
		$this->current_check_in = new DateTime ( Booking::dateToDB ( $this->check_in ) );
		$this->new_check_in_ = new DateTime ( Booking::dateToDB ( $this->new_check_in ) );
		
		$new_arrival_date = "";
		
		
		
		/*
		 * Update Condition
		 * In order to figure out the status, these conditions will compare student ID and reference number; 
		 * morever, previous status will be a key to determine the next status.
		 * 
		 * 1. Cancel : In any circumstances, if a user who has permission chooses the cancel status from select object,
		 * 			   the booking will be changed into cancel status. 
		 * 
		 * 2. New : Current status is "new"( null ), and the student id has been set, the booking will be ready.
		 * 			Even though it is new status, if there are student id and reference number, the booking will be confirmed immediately
		 * 			( Admin and uniresort have different permission to change data in update view, so it will not happen at the same time)    
		 * 			
		 * 
		 * 3. Ready : If Uniresort enters reference number, this will treate as confirm status.
		 * 			  However, if a admin user deletes student number, the record will be set as new.
		 *  
		 * 4. Confirm : if a student changes check in data or room type, the booking status will be changed into modified status, then the uniresort needs to confirm data and room type.
		 * 				After the Uniresort confirms it, this method will regenerate modified confirmation letter and send to the student and agent.
		 * 
		 * 5. Modify: only uniresort sees this status and changes the record. if uniresort users open and save it without any changes, this will be assumed as confirmed.
		 * 
		 *  			  
		 * 				 
		 */
		
		// case1 cancel!!!!
		if ($this->new_confirmed == "Canceled") {
			$this->reason_confirmed = false;
			$this->cancelLetter = true;
		} 		// case 2 new!!!!! empty( confirm) or null
		elseif (is_null ( $this->confirmed ) || empty ( $this->confirmed )) {
			if (! is_null ( $this->new_st_id ) && ! is_null ( $this->new_ref ) && $this->role != "student") {
				$this->new_confirmed = "Confirmed";
				$this->reason_confirmed = true;
				$this->updateStatus = true;
			} elseif (! is_null ( $this->new_st_id ) && $this->role != "student" ) {
				
				$this->new_confirmed = "Ready";
				$this->updateStatus = true;
				// $this->reason_confirmed = true;
			} else
				$this->new_confirmed = null;
		}		

		// case 3 ready!!!
		elseif ($this->confirmed == "Ready") {
			
			if (! is_null ( $this->new_st_id ) && isset ( $this->new_ref ) && ! is_null ( $this->new_ref ) && $this->new_ref != "0" && ! empty ( $this->new_ref )) {
				$this->new_confirmed = "Confirmed";
				$this->updateStatus = true;
				$this->reason_confirmed = true;
				// $this->confimationLetter = true;
			} elseif (! is_null ( $this->new_st_id ) ) {
				$this->new_confirmed = "Ready";
				
			} else
				$this->new_confirmed = null;
		}		// case 4 Confirmed~!!
		elseif ($this->confirmed == "Confirmed") {
			
			if ($this->current_check_in != $this->new_check_in_) {
				$this->new_confirmed = "Modified";
				$this->reason_check_in = true;
				// $this->reason_confirmed = true;
			}
			if ($this->room_type != $this->new_room_type) {
				$this->new_confirmed = "Modified";
				$this->reason_room_type = true;
				// $this->reason_confirmed = true;
			}
			if($this->weeks != $this-> new_weeks){
				$this->new_confirmed = "Modified";
				$this->reason_weeks = true;
			}
			
			if ($this->new_ref == "0" || empty ( $this->new_ref ) || ! isset ( $this->new_ref ) || is_null ( $this->new_ref )) {
				$this->new_confirmed = "Ready";
			}
		} 		// case 5 Modified~!!!
		elseif ($this->confirmed == "Modified" && $this->role == "uniresort") {
			$flagOption_check_in = false;
			$flagOption_room_type = false;
			$flagOption_weeks = false;
			if ($this->current_check_in != $this->new_check_in_) {
				$this->reason_check_in = true;
				$flagOption_check_in = true;
			}
			if ($this->room_type != $this->new_room_type) {
				$this->reason_room_type = true;
				$flagOption_room_type = true;
			}
			if ($this->weeks != $this->new_weeks) {
				$this->reason_weeks = true;
				$flagOption_weeks = true;
			}
			if ($this->new_confirmed == "Confirmed" && (! $flagOption_check_in && ! $flagOption_room_type && !$flagOption_weeks)) {
				$this->reason_confirmed = true;
				$this->uniModifiedConfirmLetter = true;
				$this->new_confirmed = "Confirmed";
			} else
				$this->new_confirmed = "Modified";
		}
		
		
		
		/*
		 *  Change Pickup information
		 *  
		 *  
		 *  Even though a booking confirmed, A student and an agent can change their pick up schedule whenever they want to.
		 *  Once user changes pickup information, this will regenerate and send to student a confirm letter which is only edited pickup part.
		*/
				
		if ($this->new_confirmed == "Confirmed") {
			
			if (is_null ( $this->new_airport ) && is_null ( $this->airport )) {
				$this->reason_airport = false;
				$this->PickupModifiedConfirmLetter = false;
			} elseif (! is_null ( $this->new_airport ) xor ! is_null ( $this->airport )) {
				$this->reason_airport = true;
				$this->PickupModifiedConfirmLetter = true;
			} elseif ($this->new_airport != $this->airport) {
				$this->reason_airport = true;
				$this->PickupModifiedConfirmLetter = true;
			} else {
				$this->reason_airport = false;
				$this->PickupModifiedConfirmLetter = false;
			}
			
			if (is_null ( $this->new_arrival_date ) && is_null ( $this->arrival_date )) {
				$this->reason_arrival_datetime = false;
			} elseif (! is_null ( $this->new_arrival_date ) xor ! is_null ( $this->arrival_date )) {
				$this->reason_arrival_datetime = true;
				$this->PickupModifiedConfirmLetter = true;
			} elseif ($this->new_arrival_date != $this->arrival_date) {
				$this->reason_arrival_datetime = true;
				$this->PickupModifiedConfirmLetter = true;
			}
			if (is_null ( $this->new_arrival_time ) && is_null ( $this->arrival_time )) {
				$this->reason_arrival_datetime = false;
			} elseif (! is_null ( $this->new_arrival_time ) xor ! is_null ( $this->arrival_time )) {
				$this->reason_arrival_datetime = true;
				$this->PickupModifiedConfirmLetter = true;
			} elseif (date ( "h:i A", strtotime ( $this->new_arrival_time ) ) != date ( "h:i A", strtotime ( $this->arrival_time ) )) {
				$this->reason_arrival_datetime = true;
				$this->PickupModifiedConfirmLetter = true;
			}
			if ($this->pickup != $this->new_pickup) {
				$this->PickupModifiedConfirmLetter = true;
				$this->reason_pickup = true;
			}
		}
		/*
		 * // if both arrival dates are nothing if (is_null ( $this->new_arrival_date ) && is_null ( $this->arrival_date )) { $this->reason_arrival_datetime = false; } elseif (! is_null ( $this->new_arrival_date ) xor ! is_null ( $this->arrival_date )) { $this->reason_arrival_datetime = true; $this->PickupModifiedConfirmLetter = true; } elseif (strcmp ( $this->new_arrival_date, $this->arrival_date ) != true) { $this->reason_arrival_datetime = true; $this->PickupModifiedConfirmLetter = true; } if (is_null ( $this->new_arrival_time ) && is_null ( $this->arrival_time )) { $this->reason_arrival_datetime = false; } elseif (! is_null ( $this->new_arrival_time ) xor ! is_null ( $this->arrival_time )) { $this->reason_arrival_datetime = true; $this->PickupModifiedConfirmLetter = true; } elseif (strcmp ( $this->new_arrival_time, $this->arrival_time ) != true) { $this->reason_arrival_datetime = true; $this->PickupModifiedConfirmLetter = true; } if (is_null ( $this->new_airport ) && is_null ( $this->airport )) { $this->reason_airport = false; } elseif (! is_null ( $this->new_airport ) xor ! is_null ( $this->airport )) { $this->reason_airport = true; $this->PickupModifiedConfirmLetter = true; } elseif (strcmp ( $this->new_airport, $this->airport ) != true) { $this->reason_airport = true; $this->PickupModifiedConfirmLetter = true; } $this->reason_pickup = ! strcmp ( $this->pickup, $this->new_pickup );
		 */
		if ($this->reason_check_in == true || $this->reason_confirmed == true || $this->reason_room_type == true || $this->reason_pickup == true || $this->reason_arrival_datetime == true || $this->reason_airport == true||$this->cancelLetter==true) {
			$this->sendEmail ();
		}
		
		if ($this->databaseConnection ()) {
			$newquery ="";
			
				$newquery = $this->db_connection->prepare ( 'Update booking set booking_fee_added_offer = :fee_added, booking_ref_num =:ref,	booking_confirmed= :confirmed,	booking_st_id =:st_id,	booking_st_first_name = :f_name , booking_st_last_name = :l_name ,					booking_st_gender = :gender , booking_st_email = :email ,	booking_st_contact_num = :contact ,
									booking_st_dob  = :dob,
									booking_nationality = :nation ,
									booking_room = :room ,
									booking_check_in = :check_in ,
									booking_weeks  = :weeks ,
									booking_pickup = :pickup ,
									booking_arrival_date = :date ,
									booking_arrival_time = :time ,
									booking_flight_num = :f_num ,
									booking_airport = :airport ,
									booking_comment = :comment,
									booking_bed_code = :bed_code,
									booking_room_code = :room_code,
									booking_front_door_code = :front_code ,
									booking_pgate_code =:gate_code,
									booking_updated_time = now(),
									user_email =:agent_email
											 WHERE id_booking = :id' );
			
			
				
			
			$newquery->bindValue ( ':id', $this->new_id, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':fee_added', $this->new_fee_added, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':ref', $this->new_ref, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':confirmed', $this->new_confirmed, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':st_id', $this->new_st_id, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':f_name', $this->new_f_name, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':l_name', $this->new_l_name, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':email', $this->new_email, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':agent_email', $this->new_agent_email, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':gender', $this->new_gender, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':contact', $this->new_contact, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':dob', $this->new_dob, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':nation', $this->new_nation, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':room', $this->new_room_type, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':check_in', $this->new_check_in, PDO::PARAM_STR );
			
			$newquery->bindValue ( ':weeks', $this->new_weeks, PDO::PARAM_INT );
			
			$newquery->bindValue ( ':pickup', $this->new_pickup, PDO::PARAM_INT );
			
			if (! isset ( $this->new_arrival_date )) {
				$this->new_arrival_date = null;
			}
			
			$newquery->bindValue ( ':date', $this->new_arrival_date, PDO::PARAM_STR );
			if (! isset ( $this->new_arrival_time )) {
				$this->new_arrival_time = null;
			}
			
			$newquery->bindValue ( ':time', $this->new_arrival_time, PDO::PARAM_STR );
			
			if (! isset ( $this->new_f_num )) {
				$this->new_f_num = null;
			}
			
			$newquery->bindValue ( ':f_num', $this->new_f_num, PDO::PARAM_STR );
			
			if (! isset ( $this->new_airport )) {
				$this->new_airport = null;
			}
			
			$newquery->bindValue ( ':airport', $this->new_airport, PDO::PARAM_STR );
			
			if (! isset ( $this->new_comment )) {
				$this->new_comment = null;
			}
			
			$newquery->bindValue ( ':comment', $this->new_comment, PDO::PARAM_STR );
			
			if (! isset ( $this->new_bed_code )) {
				$this->new_bed_code = null;
			}
			if (! isset ( $this->new_room_code )) {
				$this->new_room_code = null;
			}
			if (! isset ( $this->new_front_door_code )) {
				$this->new_front_door_code = null;
			}
			if (! isset ( $this->new_pgate_code )) {
				$this->new_pgate_code = null;
			}
			
			$newquery->bindValue ( ':bed_code', $this->new_bed_code, PDO::PARAM_STR );
			$newquery->bindValue ( ':room_code', $this->new_room_code, PDO::PARAM_STR );
			$newquery->bindValue ( ':front_code', $this->new_front_door_code, PDO::PARAM_STR );
			$newquery->bindValue ( ':gate_code', $this->new_pgate_code, PDO::PARAM_STR );
			
			$newquery->execute ();
		}
	}
	/*
	 *  Send Email to Student and agent 
	 *  
	 */
	
	private function sendEmail() {
		 //$this->mail->AddAddress ( "enrol@ihbrisbane.com.au" );
		 
		$this->mail->AddAddress($this->new_email); 
		
		//$this->user_email = $this->dbquery_select('user_email', 'booking', 'id_booking', $this->new_id);
		
		if ($this->databaseConnection ()) {
			
			$query = $this->db_connection->prepare ( 'SELECT  `user_email` FROM booking where id_booking = :id' );
			$query->bindValue ( ":id", $this->new_id, PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
				foreach ( $results as $row ) {
					$this->user_email = $row [0];
				}
			}
		}
		
		if (isset($this->new_agent_email)/* && !is_null($this->user_email )*/) {
			$this->mail->AddCC ( $this->new_agent_email );
		}
			
		
		
		//$this->mail->AddBCC ( "support@ihbrisbane.com.au");
		$this->mail->AddBCC("japan@ihbrisbane.com.au");
		$this->mail->AddBCC("vitor@ihbrisbane.com.au");
		$this->mail->AddBCC("james@ihbrisbane.com.au");
		$this->mail->AddBCC("marketing@ihbrisbane.com.au");
		$this->mail->AddBCC("middleeast@ihbrisbane.com.au");
		$this->mail->AddCC("uniresort@uniresort.com.au"); 
	 
		$ref = "  REF.NO. :";
		if (! is_null ( $this->ref )) {
			$ref .=  $this->ref;
		} elseif (! is_null ( $this->new_ref )) {
			$ref .=  $this->new_ref;
		}
		$student_id=" STUDENT NO: ";
		if(!is_null($this->st_id)){
			$student_id= $this->st_id;
		} elseif (! is_null ( $this->new_st_id )) {
			$student_id .=  $this->new_st_id;
		} else {
			$student_id ="NONE";
		}
		
		
		
		
		$this->mail->Subject = EMAIL_UPDATE_TITLE . " " . $ref.$student_id." ".$this->new_f_name." ".$this->new_l_name;
		
		$output = '<lable style="font-size:15.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020;">';
		
		if ($this->reason_check_in) {
			$fromDate = (empty ( $this->check_in ) ? 'NONE' : Booking::dbToDate ( $this->check_in ));
			$toDate = (empty ( $this->new_check_in ) ? 'NONE' : Booking::dbToDate ( $this->new_check_in ));
			$output .= "\nCheck-in Date has been changed from " . $fromDate . " to " . $toDate . "\n";
		}
		
		if ($this->reason_weeks) {
			$fromDate = (empty ( $this->weeks ) ? 'NONE' : $this->weeks);
			$toDate = (empty ( $this->new_weeks ) ? 'NONE' : $this->new_weeks);
			$output .= "\nWeeks has been changed from " . $fromDate . " to " . $toDate . "\n";
		}
		
		
		if ($this->reason_room_type) {
			$fromRoom = (empty ( $this->room_type ) ? 'NONE' : $this->room_type);
			$toRoom = (empty ( $this->new_room_type ) ? 'NONE' : $this->new_room_type);
			$output .= "\nRoom type has been changed" . " from " . $fromRoom . " to " . $toRoom;
		}
		if ($this->reason_airport) {
			$fromAirport = (empty ( $this->airport ) ? 'NONE' : $this->airport);
			$toAirport = (empty ( $this->new_airport ) ? 'NONE' : $this->new_airport);
			$output .= "\nAirport has been changed" . " from " . $fromAirport . " to " . $toAirport;
		}
		if ($this->reason_arrival_datetime) {
			
			$fromArrival = (empty ( $this->arrival_date ) ? 'NONE' : Booking::dbToDate ( $this->arrival_date ) . " " . date ( "h:i A", strtotime ( $this->arrival_time ) ));
			$toArrival = (empty ( $this->new_arrival_date ) ? 'NONE' : Booking::dbToDate ( $this->new_arrival_date ) . " " . date ( "h:i A", strtotime ( $this->new_arrival_time ) ));
			$output .= "\nArrival date and time have been changed" . " from " . $fromArrival . " to " . $toArrival;
		}
		if ($this->reason_confirmed || $this->PickupModifiedConfirmLetter) {
			$output .= EMAIL_UPDATE_CONFIRMED ;
			$this->createConfirmLetter ();
			$this->mail->AddAttachment ( $this->filename );
			;
		}
		if ($this->cancelLetter) {
			$output .= EMAIL_CANCEL ;
		}
		// the link to your register.php, please set this value in config/email_verification.php
		$this->mail->Body = $output."</lable><br/><br/>". $this->allUniDetails();;
		$this->mail->AddEmbeddedImage("../images/footer.jpg", "footer");
		
		
						
		
		$this->mail->IsHTML(true);
		if (! $this->mail->Send ()) {
			
			// $this->out.= $this->lang ['Verification mail not sent'] . $this->mail->ErrorInfo;
			return false;
		} else {
			
			return true;
		}   
	}
	
	/*
	 *  When uniresort staff changes any entry codes on updateview,
	 *  This will generate a code email and sent to student, agent and accommodation@ihbrisbane.com
	 */
	private function sendCodeEmail() {
		//$this->mail->AddAddress ( "enrol@ihbrisbane.com.au" );
			
		$this->mail->AddAddress($this->new_email);
	
	
	
		if ($this->databaseConnection ()) {
				
			$query = $this->db_connection->prepare ( 'SELECT  `user_email` FROM booking where id_booking = :id' );
			$query->bindValue ( ":id", $this->new_id, PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
				
			if (count ( $results ) > 0) {
				foreach ( $results as $row ) {
					$this->user_email = $row [0];
				}
			}
		}
	
		if (isset($this->user_email)/* && !is_null($this->user_email )*/) {
			$this->mail->AddCC ( $this->user_email );
		}
			
	
	
		//$this->mail->AddBCC ( "support@ihbrisbane.com.au");
		/* $this->mail->AddBCC("japan@ihbrisbane.com.au");
		
		
		$this->mail->AddBCC("brian@ihbrisbane.com.au");
		$this->mail->AddBCC("james@ihbrisbane.com.au");
		$this->mail->AddBCC("marketing@ihbrisbane.com.au");
		$this->mail->AddBCC("middleeast@ihbrisbane.com.au");
		$this->mail->AddBCC("uniresort@uniresort.com.au"); */
		/*$this->mail->AddBCC("accommodation@ihbrisbane.com.au");
		$this->mail->AddBCC("japan@ihbrisbane.com.au");
		$this->mail->AddBCC("vitor@ihbrisbane.com.au");
		$this->mail->AddBCC("marketing@ihbrisbane.com.au");
		$this->mail->AddBCC("james@ihbrisbane.com.au");*/
		
		$ref = "  REF.NO. :";
		if (! is_null ( $this->ref )) {
			$ref .=  $this->ref;
		} elseif (! is_null ( $this->new_ref )) {
			$ref .=  $this->new_ref;
		}
		$student_id=" STUDENT NO: ";
		if(!is_null($this->st_id)){
			$student_id= $this->st_id;
		} elseif (! is_null ( $this->new_st_id )) {
			$student_id .=  $this->new_st_id;
		} else {
			$student_id ="NONE";
		}
	
	
	
	
		$this->mail->Subject = EMAIL_UPDATE_TITLE . " " . $ref.$student_id." ".$this->new_f_name." ".$this->new_l_name." Entry Code Details";
	
		$output = '<lable style="font-size:15.0pt;font-family:Verdana, Geneva, sans-serif;color:#202020;">Dear '.$this->new_f_name.' '.$this->new_l_name.',<br/>
				Please find below the key codes for your accommodation.<br/>';
	
		
		$this->mail->Body = $output."</lable><br/><br/>". $this->KeyDetails();;
		$this->mail->AddEmbeddedImage("../images/footer.jpg", "footer");
	
	
	
	
		$this->mail->IsHTML(true);
		if (! $this->mail->Send ()) {
				
			// $this->out.= $this->lang ['Verification mail not sent'] . $this->mail->ErrorInfo;
			return false;
		} else {
				
			return true;
		}
	}
	
	/*
	 *  Get Methods
	 *  In order to display stored information on UpdateView to be edited by user, 
	 *  all values will be covered with html tags and checked before displaying.
	 */
	
	public function isUpdated() {
		return $this->is_updated;
	}
	public function getFID() {
		return $this->f_st_id;
	}
	public function getRole() {
		return $this->role;
	}
	public function getBookingID() {
		return $this->id;
	}
	public function getStudentID() {
		return $this->makeTag ( $this->st_id, $this->f_st_id );
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
	public function getFeeAdded() {
		$option1 = "";
		$option2 = "";
		$disable = "";
		$output = "<select name='fee_added'>";
		if (! $this->f_fee_added) {
			if ($this->fee_added == 1) {
				$output .= "<option value='1' selected >Yes</option></select>";
			} else {
				$output .= "<option value='0' selected>No</option></select>";
			}
		} else {
			if ($this->fee_added == 1) {
				$option1 = "selected";
			} else {
				$option2 = "selected";
			}
			
			$output .= "<option value='1' " . $option1 . " >Yes</option><option value='0' " . $option2 . ">No</option></select>";
		}
		return $output;
	}
	public function getRefNum() {
		return $this->makeTag ( $this->ref, $this->f_ref );
	}
	public function getStatus() {
		if ($this->role == "uniresort") {
			return '<select name="status"> <option value="Confirmed" >Confirm</option><option value="Canceled">Cancel</option>' . "</select>";
		} else
			return $this->status;
	}
	public function getStudentFirstName() {
		// return $this->makeTag($this->f_name, $this->f_f_name);
		return $this->makeTag ( $this->f_name, $this->f_f_name );
	}
	public function getStudentLastName() {
		return $this->makeTag ( $this->l_name, $this->f_l_name );
	}
	public function getStudentGender() {
		$option1 = "";
		$option2 = "";
		$disable = "";
		$output = "<select name='gender'>";
		if (! $this->f_gender) {
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
	
	public function getStudentEmail() {
		return $this->makeTag ( $this->email, $this->f_email );
	}
	public function getUserEmail(){
		return $this->makeTag ($this->agent_email,$this->f_email);
	}
	public function getStudentContact() {
		return $this->makeTag ( $this->contact, $this->f_contact );
	}
	public function getDOB() {
		return $this->makeTag ( Booking::dbToDate ( $this->dob ), $this->f_dob );
	}
	public function getNationality() {
		return $this->countries;
	}
	public function getRooms() {
		return $this->rooms;
	}
	public function getCheckin() {
		return $this->makeTag ( Booking::dbToDate ( $this->check_in ), $this->f_check_in );
	}
	public function getWeeks() {
		return $this->makeTag ( $this->weeks, $this->f_weeks );
	}
	public function getAirports() {
		return $this->airports;
	}
	public function getArrivalDate() {
		if (! empty ( $this->arrival_date )) {
			return $this->makeTag ( Booking::dbToDate ( $this->arrival_date ), $this->f_arrival_date );
		} else
			return "\"\"";
	}
	public function getArrivalTime() {
		return $this->makeTag ( $this->arrival_time, $this->f_arrival_time );
	}
	public function getFlightNumber() {
		return $this->makeTag ( $this->f_num, $this->f_f_num );
	}
	public function getComment() {
		return $this->makeTag ( $this->comment, $this->f_comment );
	}
	public function getBedCode() {
		return $this->makeTag ( $this->bed_code, $this->f_bed_code );
	}
	public function getRoomCode() {
		return $this->makeTag ( $this->room_code, $this->f_room_code );
	}
	public function getForntDoorCode() {
		/*
		 * $disable = ""; if (! $this->f_front_door_code) { $disable = $this->disabled; }
		 */
		return $this->makeTag ( $this->front_door_code, $this->f_front_door_code );
	}
	public function getPGateCode() {
		return $this->makeTag ( $this->pgate_code, $this->f_pgate_code );
	}
	
	/*
	 *  To compare current data and new data, data will be collected from database and stored in local variables.
	 */
	
	protected function getPreviouseInfo($id) {
		if ($this->databaseConnection ()) {
			/*
			 * id_booking , booking_fee_added_offer, booking_ref_num , booking_confirmed, booking_st_id , booking_st_first_name , booking_st_last_name , booking_st_gender , booking_st_addr , booking_st_email , booking_st_contact_num , booking_st_dob , booking_nationality , booking_room VARCHAR(20) , booking_check_in , booking_weeks , booking_pickup , booking_arrival_date , booking_arrival_time , booking_flight_num , booking_airport , booking_comment , booking_bed_code , booking_room_code , booking_front_door_code , booking_pgate_code
			 */
			$query = $this->db_connection->prepare ( 'SELECT  * FROM booking where id_booking = :id' );
			$query->bindValue ( ":id", $id, PDO::PARAM_INT );
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
				foreach ( $results as $row ) {
					$this->id = $row [0];
					$this->fee_added = $row [1];
					$this->lock =$row [2];
					$this->ref = $row [3];
					$this->confirmed = $row [4];
					$this->st_id = $row [5];
					$this->f_name = $row [6];
					$this->l_name = $row [7];
					$this->gender = $row [8];
					
					$this->email = $row [9];
					$this->contact = $row [10];
					$this->dob = $row [11];
					$this->nation = $row [12];
					$this->room_type = $row [13];
					$this->check_in = $row [14];
					$this->weeks = $row [15];
					$this->pickup = $row [16];
					$this->arrival_date = $row [18];
					$this->arrival_time = $row [19];
					$this->f_num = $row [20];
					$this->airport = $row [21];
					$this->comment = $row [22];
					$this->bed_code = $row [23];
					$this->room_code = $row [24];
					$this->front_door_code = $row [25];
					$this->pgate_code = $row [26];
					$this->agent_email = $row[29];
					 //TODO:
					$this->booing_created_time = Booking::dateToDB(substr($row[32], 0, 10));
					 
				}
			}
		}
	}
	
	/*
	 *  displaying current status based on database
	 *  
	 */
	private function generateOptions() {
		if ($this->databaseConnection ()) {
			
			$query = $this->db_connection->prepare ( 'SELECT * FROM nationality' );
			
			$query->execute ();
			$results = $query->fetchAll ();
			
			$this->countries = "<select name='nationality'>";
			
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					if ($result [0] == $this->nation) {
						$this->countries .= sprintf ( "\t" . '<option value="%1$s" selected="selected">%2$s</option>' . "\n", $result [0], $result [0] );
					} elseif ($this->f_nation) {
						$this->countries .= sprintf ( "\t" . '<option value="%1$s">%2$s</option>' . "\n", $result [0], $result [0] );
					}
				}
			}
			$this->countries .= "</select>";
			
			$query = $this->db_connection->prepare ( 'SELECT * FROM status' );
			
			$query->execute ();
			$results = $query->fetchAll ();
			/*
			 * if (! $this->f_confirmed) { $disable = $this->disabled; }
			 */
			// $disable = "";
			$this->status = "<select name='status' >";
			if (! isset ( $this->confirmed )) {
				$this->status .= sprintf ( "\t" . '<option value="0" selected="selected" >New</option>' . "\n" );
			}
			
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					if ($result [0] == $this->confirmed) {
						$this->status .= sprintf ( "\t" . '<option value="%1$s" selected="selected">%2$s</option>' . "\n", $result [0], $result [0] );
						;
					} elseif ($this->f_confirmed) {
						$this->status .= sprintf ( "\t" . '<option value="%1$s">%2$s</option>' . "\n", $result [0], $result [0] );
					}
				}
			}
			$this->status .= "</select>";
			
			
			/*
			 *  Room price might be changed in the future!!
			 * 
			 * 
			 *  29/11/2015 Don't worry mate, now I am fixing it...
			 */
			// $query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= CURDATE() ORDER BY ABS( DATEDIFF(  CURDATE(),  `price_expirydate` )   ) , room_type asc LIMIT 6 ' );
			//$query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= CURDATE() ORDER BY ABS( DATEDIFF(  CURDATE(),  `price_expirydate` )   ) , room_type asc LIMIT 10 ' );
			$query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= "'.$this->booing_created_time.'"  ORDER BY ABS( DATEDIFF(  "'.$this->booing_created_time.'",  `price_expirydate` ) ) LIMIT 10' );
			$query->execute ();
			$results = $query->fetchAll ();
			$this->rooms = "";
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					if ($result [0] == $this->room_type) {
						$this->rooms .= sprintf ( '<input type="radio" value= "%1$s" name="room" checked="true"> %2$s </option><br/>', $result [0], $result [0] );
						$this->room_price = $result [1];
						$this->room_after_price = $result [2];
					} elseif ($this->f_room_type) {
						$this->rooms .= sprintf ( '<input type="radio" value= "%1$s" name="room" > %2$s </option><br/>', $result [0], $result [0] );
					} else
						$this->rooms .= sprintf ( '<input type="radio" value= "%1$s" name="room" disabled> %2$s </option><br/>', $result [0], $result [0] );
				}
			}
			$query = $this->db_connection->prepare ( 'SELECT airport_name,airport_pickup_price  FROM airport WHERE price_expirydate >= "'.$this->booing_created_time.'"  ORDER BY ABS( DATEDIFF(  "'.$this->booing_created_time.'",  `price_expirydate` ) ), airport_name LIMIT 3' );
			
			$query->execute ();
			$i=1;
			$results = $query->fetchAll ();
			if (! $this->pickup) {
				$this->airports = sprintf ( '<input type="radio" value= 0 id="pickup0" name="pickup" checked="true"/>No</input><br/>' );
			} elseif ($this->f_airport) {
				$this->airports = sprintf ( '<input type="radio" value= 0 id="pickup0" name="pickup" />No</input><br/>' );
			} else {
				$this->airports = sprintf ( '<input type="radio" value= 0 id="pickup0" name="pickup" disabled="true"/>No</input><br/>' );
			}
			if (count ( $results ) > 0) {
				
				foreach ( $results as $result ) {
					if (isset ( $this->airport ) && $result [0] == $this->airport) {
						
						$this->airports .= sprintf ( '<input type="radio" value= "%1$s" name="pickup" id="pickup'.$i.'" checked="true" /> %2$s </input><br/>', $result [0], $result [0] );
					} elseif ($this->f_airport)
						$this->airports .= sprintf ( '<input type="radio" value= "%1$s" name="pickup" id="pickup'.$i.'"/> %2$s </input><br/>', $result [0], $result [0] );
					else
						$this->airports .= sprintf ( '<input type="radio" value= "%1$s" name="pickup" id="pickup'.$i.'" disabled="true" /> %2$s </input><br/>', $result [0], $result [0] );
					$i++;
				}
				
			}
		}
	}
	/*
	 * Check current user data return.
	 * 
	 */
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
	
	/*
	 *  Create pdf confirm letter.
	 *  TCPDF -> it does not work complicated html tag.
	 *  but simple one does work perfectly.
	 *  
	 *  
	 *  
	 */
	private function createConfirmLetter() {
		$titleTag = "";
		$timeTag = "";
		$pickup = "";
		$airport = "";
		$weeks4 = "";
		$afterweeks = "";
		$this->pickupfee = "";
		$pickupfee="";
		$sum = "";
		$rentfee = "";
		
		
		$securityDeposit = "";
		$placement = "";
		$roomDescription = '';
		$priceTable = "";
		$window = "";
		if ($this->databaseConnection ()) {
			$query = $this->db_connection->prepare ( 'SELECT price FROM accommodation_fees WHERE price_expirydate >= "'.$this->booing_created_time.'" AND fee_name =  "placement fee" ORDER BY ABS( DATEDIFF( " '.$this->booing_created_time.' ",  `price_expirydate` ) ) LIMIT 1' );
			
			$query->execute ();
			$results = $query->fetchAll ();
				
			if (count ( $results ) > 0) {
					
				foreach ( $results as $result ) {
					$placement = $result[0];
						
				}
				if (strpos($this->new_room_type,'COUPLE') !== false) {
					$placement *=2;
				}
				if (strpos($this->new_room_type,'PEOPLE') !== false) {
					$placement *=2;
				}
				

			}
			$query = $this->db_connection->prepare ( 'SELECT price FROM accommodation_fees WHERE price_expirydate >= "'.$this->booing_created_time.'" AND fee_name =  "deposit" ORDER BY ABS( DATEDIFF( " '.$this->booing_created_time.'" ,  `price_expirydate` ) ) LIMIT 1' );
				
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
					
				foreach ( $results as $result ) {
					$securityDeposit = $result[0];
			
				}
				if (strpos($this->new_room_type,'COUPLE') !== false) {
					$securityDeposit *=2;
				}
				if (strpos($this->new_room_type,'PEOPLE') !== false) {
					$securityDeposit *=2;
				} 
			}
			
			
			$query = $this->db_connection->prepare ( 'SELECT room_type, room_price, room_after_price  FROM room WHERE price_expirydate >= "'.$this->booing_created_time.'"  ORDER BY ABS( DATEDIFF(  "'.$this->booing_created_time.'",  `price_expirydate` ) ) LIMIT 10' );
			
			$query->execute ();
			$results = $query->fetchAll ();
			
			if (count ( $results ) > 0) {
				
				foreach ( $results as $row ) {
					if ($row [0] == $this->new_room_type) {
						$weeks4 = $row [1];
						$afterweeks = $row [2];
					}
					$priceTable .= "<tr><td class=\"roomprice\">" . $row [0] . "</td><td class=\"roomprice\">\$" . $row [1] . " per week</td><td class=\"roomprice\">\$" . $row [2] . " per week</td></tr>";
				}
			}
			if ($this->new_pickup == "1") {
				
				$newquery = $this->db_connection->prepare ( 'SELECT * FROM airport WHERE airport_name = :airport' );
				$newquery->bindValue ( ":airport", $this->new_airport, PDO::PARAM_STR );
				$newquery->execute ();
				$results = $newquery->fetchAll ();
				
				if (count ( $results ) > 0) {
					
					foreach ( $results as $row ) {
						
						$this->pickupfee = $row [1];
						if (strpos($this->new_room_type,'COUPLE') !== false) {
							$this->pickupfee *=2;
						}
						if (strpos($this->new_room_type,'PEOPLE') !== false) {
							$this->pickupfee *=2;
						}

						$pickup = "YES  (Please see page 3)";
					}
				}
			} else {
				$pickup = "NO";
			}
		}
		
		if ($this->new_weeks <= 4) {
			$rentfee = $weeks4 * $this->new_weeks;
		} else
			$rentfee = ($this->new_weeks - 4) * $afterweeks + 4 * $weeks4;
		
		$date = $this->new_check_in;
		$days = $this->new_weeks * 7;
		$checkout = date ( 'd/m/Y', strtotime ( $date . ' + ' . $days . ' days' ) );
		
		if ($this->new_pickup == "0") {
			$sum = $rentfee + $securityDeposit + $placement;
			$pickupfee = "NONE";
		} else {
		
			
			$sum = $rentfee + $securityDeposit + $this->pickupfee + $placement;
			
			$pickupfee = "\$" . $this->pickupfee;
		}
		
		if ($this->uniModifiedConfirmLetter) {
			$titleTag = "(Modified) ";
		}
		
		if (strpos($this->new_room_type,'SINGLE') !== false) {
			$roomDescription = 'a double bed';
		} else {
			$roomDescription = 'two single beds';
		}
		$pdf = new MYPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		
		// set document information
		$pdf->SetCreator ( 'IHBrisbane ALS' );
		$pdf->SetAuthor ( 'IHBrisbane ALS' );
		$pdf->SetTitle ( 'Confirmation Letter' );
		
		$pdf->SetAutoPageBreak ( true, 0 );
		
		$pdf->setImageScale ( PDF_IMAGE_SCALE_RATIO );
		
		$pdf->AddPage ();
		$html = "
				<style>
				h1{
					text-align:center;
					font-size:18.0pt;
					line-height:115%;
					font-family:'Calibri','sans-serif';
				}
				table.detail {
     	   			border-collapse:collapse;
					border:none;
					
					align: center;
					border-spacing:5px 3px;

    			}
				td.col1{
					width:240pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				
				}
				td.col2{
					width:270pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
					
				
				}	
				td.col11{
					width:110pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				
				}
				td.col12{
					width:290pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
					
				
				}	
				td.col13{
					width:106pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
					
				
				}	
				
				td.col3{
					width:255pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				}
				td.col4{
					width:255pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					
					font-weight:bold;
					padding: 5px;
					
				}
				td.col5{
					width:390pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				}
				td.description{
					width:390pt;
					
				    background-color:#888888;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
					font-weight:bold;
				}
				td.amount{
					width:120pt;
					
				    background-color:#888888;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					text-align: center;
					padding: 5px;
					font-weight:bold;
				}
				td.col6{
					width:120pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					text-align: right;
					font-weight:bold;
					padding: 5px;
				}
				td.total{
					width:120pt;
					
				    background-color: #888888;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					text-align: right;
					font-weight:bold;
					padding: 5px;
				}
				td.col7{
					width:510pt;
					border:none;
					border-bottom:solid white 3.0pt;
					padding-left:10px;
					font-family:'Calibri','sans-serif';
					font-color=#FFFFFF;
					background-color:#888888;
					padding: 5px;									
				}
				td.col8{
					width:510pt;
					border:solid white 3.0pt;
					border-top:none;
					background-color:#D9D9D9;
					padding-left:10px;
					font-weight:bold;
					font-size:11.0pt;
					padding: 5px;
				}
				td.roomtype{
					width:140pt;
					border:solid white 3.0pt;
					border-top:none;
					background-color:#888888;
					padding-left:10px;
					font-weight:bold;
					font-size:11.0pt;
					padding: 5px;
				}
				td.roomtype1{
					width:140pt;
					background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					
					
					padding: 5px;
				}
				td.roomdesc{
					width:370pt;
					border:solid white 3.0pt;
					border-top:none;
					background-color:#888888;
					padding-left:10px;
					font-weight:bold;
					font-size:11.0pt;
					padding: 5px;
				}
				td.roomdesc1{
					width:370pt;
					background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					
					
					padding: 5px;
				}
				span.title{
					font-size:11.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					font-weight:bold;
				}
				span.contact{
					font-size:10.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					
				}
				p.inst{
					font-size:9.0pt;
					font-family:\"Calibri\",\"sans-serif\;
					margin-left:20px;				
				}
				td.allroom{
					background-color:#888888;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				}
				td.roomprice{
					background-color: #D9D9D9;
					padding-left:10px;
					font-size:10.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
				}
				</style>
		
				<br/><br/><br/>
		<h1 >Student Accommodation Confirmation Letter " . $titleTag . "</h1><br/>
		<table class=\"detail\" border=1 cellspacing=0 cellpadding=0>
				<tr>
				<td class=\"col1\">BOOKING NUMBER</td>
				<td class=\"col2\">" . $this->new_ref . "</td>
				</tr>
				<tr>
				<td class=\"col1\">STUDENT NAME</td>
				<td class=\"col2\">" . $this->new_f_name . " " . $this->new_l_name . "</td>
				</tr>
				<tr>
				<td class=\"col1\">IH BRISBANE - ALS Student ID</td>
				<td class=\"col2\">" . $this->new_st_id . "</td>
				</tr>
				<tr>
				<td class=\"col1\">CHECK-IN DATE (dd/mm/yyyy)</td>
				<td class=\"col2\">" . Booking::dbToDate ( $this->new_check_in ) . " (02:00 PM Until 5:00 PM)</td>
				</tr>
				<tr>
				<td class=\"col1\">CHECK OUT DATE (dd/mm/yyyy)</td>
				<td class=\"col2\">No later than 10 AM on " . $checkout . "</td>
				</tr>
				<tr>
				<td class=\"col1\">DURATION OF THE STAY</td>
				<td class=\"col2\">" . $this->new_weeks . " Weeks</td>
				</tr>
				<tr>
				<td class=\"col1\">ACCOMMODATION TYPE</td>
				<td class=\"col2\">" . $this->new_room_type . "</td>
				</tr>
				<tr>
				<td class=\"col1\">ACCOMMODATION ADDRESS</td>
				<td class=\"col2\">80 Tryon Street, Upper Mt. Gravatt, QLD 4122</td>
				</tr>
				<tr>
				<td class=\"col1\">ACCOMMODATION CONTACT NO.</td>
				<td class=\"col2\">+61 7 3457 5588</td>
				</tr>
				<tr>
				<td class=\"col11\">Emergency Contacts</td>
				<td class=\"col12\">Aaron Rae (Student Accommodation Manager)</td>
				<td class=\"col13\">+61 412 112 314</td>		
				</tr>
				<tr>
				<td class=\"col11\"></td>
				<td class=\"col12\">James Kim (IH Brisbane - ALS Marketing Manager)</td>
				<td class=\"col13\">+61 401 249 191 </td>
				</tr>				
		</table>
						<br/><br/>
		<table class=\"detail\"  border=1 cellspacing=0 cellpadding=0>
				<tr>
				<td class=\"col3\">AIRPORT PICKUP</td>
				<td class=\"col4\">" . $pickup . "</td>
						
				</tr>				
		</table>
						<br/><br/>
		<table class=\"detail\"  border=1 cellspacing=0 cellpadding=0>
				<tr>
				<td class=\"description\">FEE DESCRIPTION</td>
				<td class=\"amount\">AMOUNT</td>
				</tr>				
				<tr>
				<td class=\"col5\">ACCOMMODATION FEE(" . $this->new_weeks . " WEEKS)</td>
				<td class=\"col6\">\$" . $rentfee . ".00</td>
				</tr>				
				<tr>
				<td class=\"col5\">BOND DEPOSIT (Refundable)</td>
				<td class=\"col6\">\$" . $securityDeposit . ".00</td>
				</tr>				
				<tr>
				<td class=\"col5\">AIRPORT PICKUP FEE SURCHARGE</td>
				<td class=\"col6\">" . $pickupfee . "</td>
				</tr>				
				<tr>
				<td class=\"col5\">PLACEMENT FEE</td>
				<td class=\"col6\">\$" . $placement . ".00</td>
				</tr>				
				<tr>
				<td class=\"description\">TOTAL</td>
				<td class=\"total\">\$" . $sum . ".00</td>
				</tr>				
																	
		</table>
						
						<br/><br/>
		<table class=\"detail\" border=1 cellspacing=0 cellpadding=0>
						<tr>
					<td class=\"roomtype\">ROOM TYPE</td>
					<td class=\"roomdesc\">ROOM DESCRIPTIONS</td>
					</tr>
					<tr>
					<td class=\"roomtype1\">" . $this->new_room_type . "<br>ACCOMMODATION</td>
					<td class=\"roomdesc1\">This room has " . $roomDescription . " and contains ensuite toilet and bathroom. Free internet (wired) connectors, single study desk, bed side table, built in wardrobe, air conditioned, code secured door.</td>
					</tr>							
					</table>
		
						
		<p><span class=\"title\" >IH Brisbane - ALS Student Accommodation additional information</span></p>
		<p class=\"inst\"><i>Only the first 4 weeks stay can be paid up front; additional weeks must be paid directly to the staff <u>at the accommodation reception</u>. (Minimum stay is 4 weeks).</i></p><br/>
		<p class=\"inst\"><i>If you wish to continue your stay in the student accommodation, please inform the accommodation staff at least 2 weeks in advance. If not, you may not be able to continue your stay</i></p><br/><br/>
		<p class=\"inst\"><i>The room type is chosen based on the availability, and we are not able to guarantee room types. Some rooms do not have windows.</i></p><br/>
		
		<p><span class=\"title\">Variation fee detail</span></p><br>
		<table class=\"detail\" border=1 cellspacing=0 cellpadding=0>
		<tr>
				<td class=\"allroom\">ROOM TYPE</td>
				<td class=\"allroom\">FIRST 4WEEKS</td>			
				<td class=\"allroom\">FROM 5th WEEK</td>
		</tr>
		" . $priceTable . "
		</table>";
		
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		

		$pdf->AddPage ();
		$term = "
				<style>
				li{
					font-size:8.0pt;
					font-family:\"Arial\",\"sans-serif\";
				}
				p.bold{
					font-size:10.0pt;
					font-family:\"Calibri\",\"sans-serif\";
					font-weight:bold;
					
				
				}
				p.bigbold{
					font-size:14.0pt;
					font-family:\"Calibri\",\"sans-serif\";
					font-weight:bold;
					
				
				}
				span{
					font-size:8.0pt;
					font-family:\"Calibri\",\"sans-serif\;
					
				}
				span.title{
					font-size:11.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					font-weight:bold;
				}
				span.contact{
					font-size:10.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					
				}
				</style>
				<br/><br/>
				<p class=\"bigbold\">IH Brisbane - ALS Accommodation important information</p>
				<p class=\"bold\">Changes to your booking:</p>
				<span>We understand that sometimes circumstances change, and you may want to change the dates of this booking or update other information (such as flight information). <b>These must be updated a minimum of 48 hours in advance</b>. To do so, please </span>
				<ol >
						<li>Log in the IH Brisbane - ALS accommodation booking page <a href='www.ihbrisbane.com.au/Apps/bookingaccommodation/'>www.ihbrisbane.com.au/Apps/bookingaccommodation/</a></li>
					<li>Update the details you need to change (eg. Check-in date, Airport pick up, Flight information)</li>
					<li>We will email you to confirm the new dates of your booking within 5 business days</li>
				</ol>
				<span>If you are unable to use this system please send an email to <font color='blue'><u>enrol@ihbrisbane.com.au</u></font></span>
				
				<p class=\"bold\">Check in Monday to Friday 2 p.m. to 5 p.m.:</p>
				<span>- You must present this accommodation confirmation letter as well as photo ID at check-in. Staff will record and retain a copy of the ID to protect against fraudulent bookings.</span><br/>
				<span>- <u>Check-in time is after 2pm</u>, you may not enter the room before check-in time.</span>
				
				<p class=\"bold\">Check in after 5 p.m., Saturday, Sunday and Public Holidays:</p>
				<span>- <u>You will receive an entry key code to the e-mail</u> which you registered on our online booking system. (If you have requested our airport pick up service, the driver will also tell you the entry key code)</span><br/>
				<span>- You can enter your room directly by using the entry key code.</span><br/>
				<span>- You must check in at the accommodation reception with this accommodation confirmation letter as well as photo ID, on the next working day (Reception Opening hours Monday to Friday 9am-5pm)</span>

				<p class=\"bold\">Check out Monday to Friday:</p>
				<span>- <u>Check out is 10am</u>.</span><br/>
				<span>- Please clean your room and return your card key to the staff of accommodation reception.</span>
				
				<p class=\"bold\">Check out Saturday, Sunday and Public holidays:</p>
				<span>- <u>Check out is 10am</u>.</span><br/>
				<span>- Please leave your card key with your name, student number and room number on the desk of your room.</span>
				
				<p class=\"bold\">Bond refund:</p>
				<span>- <b>Submit the Bond Refund Form at IH Brisbane - ALS reception</b>.</span><br/>
				<span>- If you need to go back to your country right after you check out, please talk to our school staff at least 1 week before your check out day.</span>
				
				<p class=\"bold\">Term and conditions</p>
				<span>- The accommodation placement fee is non-refundable.</span> <br/>
				<span>- Any changes/cancellations must be made <u>at least 2 working days</u> prior to the check in date and time or you will be charged the full amount of the booking.</span><br/>
				<span>- Please refer to our website at <a href='www.ihbrisbane.com.au'>www.ihbrisbane.com.au</a> for all terms and conditions</span><br/>
				


		";
		$pdf->writeHTML ( $term, true, false, true, false, '' );
		
		
		if ($this->new_pickup) {
			$pdf->AddPage ();
			$this->pickupLetter ( $pdf );
		}
		
		// ---------------------------------------------------------
		
		// Close and output PDF document
		
		// $this->filename = grayconfirmletter/REF-" . $this->new_ref . " " . $this->new_f_name . " " . $this->new_l_name . " CONFIRMATION LETTER " . $now . ".pdf";
		$this->firstDayToSchool ( $pdf );
		$pdf->lastPage ();
		
		$this->filename = "../confirmletter/Student ID " . $this->new_st_id . " " . $this->new_f_name . " " . $this->new_l_name . " " . uniqid () . ".pdf";
		$pdf->Output ( $this->filename, 'F' );
	}
	private function firstDayToSchool($pdf) {
		$pdf->AddPage ();
		$html = "
				<style>
				h1{
					text-align:center;
					font-size:18.0pt;					
					font-family:\"Calibri\",\"sans-serif\";
				}
				
				
				img {
				    display: block;
				    margin-left: auto;
				    margin-right: auto 
				}
				span{
					font-size:10.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					
				}
				</style>
				<br/><br/><br/>
				<h1>How to get to IH Brisbane - ALS on your first day</h1>
				<span>Walk from the student accommodation to the Garden City bus station, and Catch the bus 111 Eight Mile Plains to the City King George Square station, at 7:58am on platform number 1.</span>
				<p align=\"center\"><img src=\"../images/school1.jpg\" width=\"640\" height=\"480\" /></p>  			
				<span>Your arrival destination will be at King George Square station, at 8:20am on platform number 1F. Walk 159 meters from the King George Square station to the school located on 126 Adelaide Street, Brisbane.</span>
				<p align=\"center\"><img src=\"../images/school2.jpg\" width=\"640\" height=\"100\" /></p>
				<p align=\"center\"><img src=\"../images/logo.jpg\" width=\"640\" height=\"70\" /></p> 
				";
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$pdf->lastPage ();
	}
	/*
	 * $pdf->writeHTML ( $html, true, false, true, false, '' ); if ($this->new_airport == "Domestic") { $pdf->Image("images/domestic.jpg",20,50,168,90,'JPG'); $airport = "Domestic"; $place = "Meeting Place"; } elseif ($this->new_airport == "International") { $pdf->Image('images/international.jpg',20,50,168,90,'JPG'); $airport = "International"; $place = "Information Centre"; }
	 */
	private function pickupLetter($pdf) {
		$img = "";
		$place1 = "";
		$place2 = "";
		$airport = "";
		$fee=$this->pickupfee;
		$date = Booking::dbToDate ( $this->new_arrival_date );
		$time = date ( "h:i A", strtotime ( $this->new_arrival_time ) );
		$titleTag = ($this->PickupModifiedConfirmLetter ? '(Modified)' : '');
		$airport= $this->new_airport ;
		/*
		if ($this->new_airport == "Brisbane Domestic") {
			// $pdf->Image("images/domestic.jpg",20,50,168,90,'JPG');
			$img = '<p align="center"><img src="../images/domestic.jpg" width="550" height="230" /></p> ';
			$place1 = "Meeting Point";
			$place2 = "Meeting Point";
			
			
		} elseif ($this->new_airport == "Brisbane International") {
			
			// $pdf->Image('images/international.jpg',50,50,100,90,'JPG');
			$img = '<p align="center"><img src="../images/international.jpg" width="400" height="230"  /></p>';
			$place1 = "Information Centre";
			$place2 = "Information Centre";
			
		} elseif ($this->new_airport == "Gold Coast International") {
			
			// $pdf->Image('images/international.jpg',50,50,100,90,'JPG');
			$img = '<p align="center"><img src="../images/goldcoast.jpg" width="400" height="230"  /></p>';
			$place1 = "Gold Coast Tourist Shuttle Countre";
			$place2 = "Meeting Point";
		}
		*/
		$html = "
				<style>
				h1{
					text-align:center;
					font-size:18.0pt;					
					font-family:\"Calibri\",\"sans-serif\";
				}
				
				table.detail {
     	   			border-collapse:collapse;
					border:none;					
					border-spacing:5px 3px;
					

    			}		
			
				td.col1{
					width:180pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
					border-bottom:solid white 3.0pt;
				
				}
				td.col2{
					width:330pt;
					
				    background-color: #D9D9D9;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
					border-bottom:solid white 3.0pt;
				
				}	
				li{
					font-size:11.0pt;
					font-family:\"Arial\",\"sans-serif\";
				}
				p.bold{
					font-size:13.0pt;
					font-family:\"Calibri\",\"sans-serif\";
					font-weight:bold;
					
				
				}
				p.small{
					font-size:12.0pt;
					font-family:\"Calibri\",\"sans-serif\";
					font-weight:bold;
					
				}
				p.normal{
					font-size:12.0pt;
					font-family:\"Calibri\",\"sans-serif\";
					text-align:center;
				}
				img {
				    display: block;
				    margin-left: auto;
				    margin-right: auto 
				}
				span{
					font-size:10.0pt;
					line-height:100%;
					font-family:\"Calibri\",\"sans-serif\;
					
				}
				td.allroom{
					background-color:#888888;
					padding-left:10px;
					font-size:11.0pt;
					font-family:'Calibri','sans-serif';
					padding: 5px;
				}
				td.roomprice{
					background-color: #D9D9D9;
					padding-left:10px;
					font-size:10.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
				}
				td.predict{
					background-color: #D9D9D9;
					padding-left:10px;
					font-size:10.0pt;
					font-family:'Calibri','sans-serif';
					font-weight:bold;
					padding: 5px;
				}
				</style>
				<br/><br/>
				<h1>Airport Reception Instructions " . $titleTag . "</h1><br/><br/>" . $img . "
									
				<p class=\"normal\">Driver will be holding an IH Brisbane - ALS sign with your name </p>
						
						
						<p class=\"bold\">Arrival Details</p>
						
				<table class=\"detail\" border=1 cellspacing=0 cellpadding=0>
				<tr>
				<td class=\"col1\">AIRPORT</td>
				<td class=\"col2\">" . $airport . "</td>
				</tr>
				<tr>
				<td class=\"col1\">ARRIVAL DATE (dd/mm/yyyy)</td>
				<td class=\"col2\">" . $date . "</td>
				</tr>
				<tr>
				<td class=\"col1\">ARRIVAL TIME</td>
				<td class=\"col2\">" . $time . "</td>
				</tr>
			
				<tr>
				<td class=\"col1\">FLIGHT NUMBER</td>
				<td class=\"col2\">" . $this->new_f_num . "</td>
				</tr>
				<tr>
				<td class=\"col1\">DRIVER'S CONTACT INFORMATION</td>
				<td class=\"col2\">Alan +61 448 169 242<br/>Emergency contact 1300 76 83 80 (Operated by SayStay)</td>
				</tr>
				
				
		</table>
			<p class=\"small\">Meeting Points</p>
			<span>Once you arrive, please meet the driver at the point shown below (please click the image for a larger picture)</span>
			<br/>
		<table class=\"detail\" border=1 cellspacing=0 cellpadding=0>
			<tr>
				<td class=\"allroom\">Brisbane International Airport</td>
				<td class=\"allroom\">Brisbane Domestic Airport</td>			
				<td class=\"allroom\">Gold Coast Airport</td>
			</tr>
			<tr>
						<td class=\"roomprice\">
						
						
						<a href=\"www.ihbrisbane.com.au/wp-content/uploads/2016/03/Bris-International.jpg\"><img src=\"../images/BNE.jpg\"  width=\"214\" height=\"80\" /></a>
						
						</td>
						<td class=\"roomprice\">
						
						
						<a href=\"www.ihbrisbane.com.au/wp-content/uploads/2016/03/Bris-domestic.jpg\"><img src=\"../images/BNEDomestic.jpg\" width=\"214\" height=\"80\" /></a>
						</td>
						<td class=\"roomprice\">
												
						<a href=\"www.ihbrisbane.com.au/wp-content/uploads/2016/03/Gold-coast.jpg\"><img src=\"../images/BNEDomestic.jpg\" width=\"214\" height=\"80\" /></a>
						</td>
			</tr>
			<tr>
						<td class=\"predict\">Predicted meeting time</td>
			</tr>
						
			<tr>
						<td class=\"roomprice\">45 mins after Landing</td>
						<td class=\"roomprice\">15 mins after Landing</td>
						<td class=\"roomprice\">45 mins after Landing</td>
			</tr>
			</table>	
			<br/>
			
			<span><b>If you cannot find your driver within 1 hour after your landing time, call to the driver. </b>If you don't have Australian coins to call from payphone, please ask at information centre to help.<span><br/>
			
			<p class=\"small\">Change of flight schedule</p>
			<b>Minimum of 48 hours in advance</b><br/><br/>
			<span>If you do not arrive at the time you have booked, the airport pick up fee will not be refunded for any reason.<br/> 
			To update your details <b>a minimum of 48 hours in advance</b>, please 				
			</span>						
			
				<ol >
					<li>Log in the IH Brisbane - ALS accommodation booking page<br/>
					<a href='www.ihbrisbane.com.au/Apps/bookingaccommodation/'>www.ihbrisbane.com.au/Apps/bookingaccommodation/</a></li><br/>
					<li>Update your flight information </li><br/>
					<li>We will email you to confirm the new details</li><br/>					
				</ol>
			<br/><br/>
			<span><b>Between 48 and 24 hours before scheduled arrival time</b><br/><br/>
			please email directly to<br/>
			<a href='enrol@ihbrisbane.com.au'>enrol@ihbrisbane.com.au</a> and <a href='transfer@saystay.com'>transfer@saystay.com</a>.<br/><br/>
			
			<b>Within 24 hours of your scheduled pick up time</b><br/><br/>
			We cannot accept changes made within 24 hours of your scheduled pick up time.<br/><br/>
			
						
			<b>If you need to arrange another airport pickup, full fees will be charged again.</b>
			</span> 
			
";
		
		$pdf->writeHTML ( $html, true, false, true, false, '' );
	}
}