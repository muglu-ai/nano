<?php 

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

ob_start();
	
	require "include_in_all.php";

	/******
	checking session for event and year
	******/
	
	/*
	if( ($_SESSION['SELECTED_EVENT'] == "") || ($_SESSION['SELECTED_YEAR'] == "") ){
		echo "<script language='javascript'>alert('Please Select Event.');</script>";
		echo "<script language='javascript'>window.location = ('welcome.php');</script>";
		exit;
	}
	*/
	/******
	primary variable declaration to event and year
	******/
	
	$welcomeSummary = "";
	$selectedEvents = $_SESSION['SELECTED_EVENT'] ?? "";
	$selectedYear = $_SESSION['SELECTED_YEAR'] ?? "";
	
	
	
	/******
	checking is POST is happen or not
	******/	
	
	if($_POST){
		
		/******
		accepting $_POST values
		******/
		if( (isset($_POST['selectEvent']) && $_POST['selectEvent'] != "") || (isset($_POST['event_year']) && $_POST['event_year'] != "") ){
			if(isset($_POST['selectEvent']) && !is_array($_POST['selectEvent'])){
				$selectedEvents = trim($_POST['selectEvent']);
			}
			if(isset($_POST['event_year']) && !is_array($_POST['event_year'])){
				$selectedYear = trim($_POST['event_year']);
			}
		}
		
		
		
	}
	else{	
		
		
		/******
		accepting $_GET values
		******/
		if( (isset($_GET['selectEvent']) && $_GET['selectEvent'] != "") || (isset($_GET['event_year']) && $_GET['event_year'] != "") ){
	
		if(isset($_GET['selectEvent']) && !is_array($_GET['selectEvent'])){
			$selectedEvents = trim($_GET['selectEvent']);
		}
		if(isset($_GET['event_year']) && !is_array($_GET['event_year'])){
			$selectedYear = trim($_GET['event_year']);		
		}
		
		}
		
		
		
	}
	
		$hd_temp = @$_GET['hd'];
		$lk_temp = @$_GET['lk'];
		$page = @$_GET['page'];
		$ret = @$_GET['rt'];
		$user_id = @$_GET['user_id'];
		
		$delegate_type = @$_POST['deleType'];
		if($delegate_type == ""){
		
			$delegate_type = @$_GET['dele_type'];
			if($delegate_type == ""){
			
				$delegate_type = "All";
			}
		}
		
		/******
			accepting Comman values required for preparing  $query 
			* hd_temp = main catagory registrations
			* lk_temp = sub catagory in registrations current page display normal registrations
			* page = require for paging and fetching data from from last display count $s {calculated below} to next 30 records
			
		******/
		
	/******
		checking event and year value
	******/
	if( ($selectedEvents == "") && ($selectedYear == "")){
			
		$selectedEvents = $_SESSION['SELECTED_EVENT'];
		$selectedYear = $_SESSION['SELECTED_YEAR'];	
	}
	
	
	/******
		Start Validating Access
	******/
	
	if( ($qr_access_limit_ans['access_level'] <= 6) ){
		
		if(($qr_access_limit_ans['event_access_level'] != $selectedEvents)){	
			echo "<script language='javascript'>alert('You Dnt have Access for this operation or Event Data, please contact admin.');</script>";
			echo "<script language='javascript'>window.location = ('welcome.php');</script>";
			exit;
		}
	}
	/******
		End Validating Access
	******/
	
	
	if(	($selectedEvents!= "") || ($selectedYear!= "") ){
	/******
		paging logic
		* $page give last displayed page number
		* calculating no of pages to dispayed on current page
		* $limitQuery = give record number up to which we have to display records
		* $s = last displayed record number from  where we have to start displaying records
	******/
	
	if (!($page)){
		$page= "1";
	}
	$p1 = $page;
	$p2 = $p1 + 1;
	$p3 = $p2 + 1;
	$p4 = $p3 + 1;
	$p5 = $p4 + 1;
	$limitQuery = $page * 30; 
	$s = $limitQuery - 30;
	
	
	/******
		checking event name and opening requred database connection.
	******/
	
	
	//---------------------------------------------------------------------------------------------------------------
	$tempTotalEventCount = $totalEventCount;
	
	while($tempTotalEventCount>0){
	
	
	if($selectedEvents == $eventNameArray[$tempTotalEventCount]){
			
		$dbInterlinks->connecttodb();  
// $$eventdbConnectionObjectName[$tempTotalEventCount]->connecttodb();//connecting to  database		
		
		/******
			preparing query		
		******/
		$db_all_exhibitor_tbl = $eventDbTablePrefixArrayAssoc[$selectedEvents]."_".$selectedYear."_exhibitors_dir_details_tbl_phase_2";
		 $db_all_exhibitor_user_tbl = $eventDbTablePrefixArrayAssoc[$selectedEvents]."_".$selectedYear."_exhibitors_dir_user_details_tbl_phase_2";
		

			$query = "select b.exhibitor_name, b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.booth_space,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id)) order by b.srno,b.exhibitor_name;";
			$query1 = "select b.exhibitor_name,b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.booth_space,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id))  order by b.srno,b.exhibitor_name;";
	if($selectedEvents == 'Bangalore IT' && $selectedYear == '2018') {
		$query = "select b.exhibitor_name,b.assoc_nm,b.booth_space, b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id)) order by b.srno,b.exhibitor_name;";
		$query1 = "select b.exhibitor_name,b.assoc_nm,b.booth_space,b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id))  order by b.srno,b.exhibitor_name; ";
	
	}

	if($selectedEvents == 'Bangalore INDIA NANO' && $selectedYear == '2018') {
		$query = "select b.exhibitor_name,b.booth_space, b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id)) order by b.srno,b.exhibitor_name;";
		$query1 = "select b.exhibitor_name,b.booth_space,b.cp_title,b.cp_fname,b.cp_mname,b.cp_lname,b.cp_desig,b.cntry_code_phone,b.area_code_phone,b.phone,b.cntry_code_fax,b.area_code_fax,b.fax,b.cntry_code_mob,b.mob,b.email,b.website,b.address_line_1,b.address_line_2,b.city,b.state,b.country,b.zip,b.reg_date,b.reg_time,b.booth_area,b.booth_area_unit,b.fascia_name,b.total_exbhitors,a.title,a.fname,a.mname,a.lname,a.email,a.mob,a.desig,a.dept from $db_all_exhibitor_tbl as b, $db_all_exhibitor_user_tbl as a where ((a.exhibitor_id=b.exhibitor_id))  order by b.srno,b.exhibitor_name; ";
	
	}
	//echo "<br />a :".$query;
		
		$qr_chk_table_status = mysqli_num_rows(mysqli_query($dbInterlinks->lnk,"SHOW TABLES LIKE '$db_all_exhibitor_tbl'"));
		$db_reg_tbl_name_chk_status = mysqli_num_rows(mysqli_query($dbInterlinks->lnk,"SHOW TABLES LIKE '$db_all_exhibitor_user_tbl'"));
					
		if( ($qr_chk_table_status >= 1) && ($db_reg_tbl_name_chk_status >= 1) ){		
	
		
		/******
			* Executing $query1
			* finding number of rows
		******/
		$numresults1=mysqli_query($dbInterlinks->lnk,$query1);
		$numrows1=mysqli_num_rows($numresults1);
		
		
		/******
			Paging mechanism
			* Calculating page intervals after every 30 records
			* preparing $next and $previous page
		******/
		
		$tmp = intval($numrows1/30);
		$tmp1 = $numrows1%30; 
		if($tmp1 == "0")
		{
			$limit = $tmp; 
		}
		else
		{
			$limit = $tmp + 1; 
		}
		$nextPage= $page +1;
		if($nextPage >= $limit)
		{
			$nextPage = $limit;
		}
		$prevPage = $page-1;
		if($prevPage <= 0)
		{
			$prevPage =1;
		}
		
		/******
			* Executing $query
			* finding number of rows
		******/
		$numresults=mysqli_query($dbInterlinks->lnk,$query);
		$numrows=mysqli_num_rows($numresults);
		
		$result = mysqli_query($dbInterlinks->lnk,$query) or die("SQL Query failed ...");//fetching main data
		
		
		//echo $result."<br />";
		
		
		}//table checking
		
	$dbInterlinks->disconnecttodb();  
// 	  $dbInterlinks->disconnecttodb();    //$$eventdbConnectionObjectName[$tempTotalEventCount]->disconnecttodb(); //disconnecting from  database   
		break;
	}
	$tempTotalEventCount--;
	
	}//while end
//----------------------------------------------------------------------------------------------------------------------------------------------------------	
	
	if($tempTotalEventCount==0){
		
		echo "<script language='javascript'>alert('Please Select Event.');</script>";
		echo "<script language='javascript'>window.location = ('welcome.php');</script>";
		exit;
	}

 }
	

$csv_output ="\n\n";
$header="Partnering powered by InterlinX. All Exhibitors User Details";

$file = 'All_Exhibitors_Details'.date("Y-m-d_h:m:s");

//$file = 'registration_data'.date("d-m-Y h:m:s");



$csv_output = $header."\n\n"."Complete Exhibiotrs User details "."\n\n\n";

// There are 24 columns in a file.

/*$csv_output  .= "SR.No\tRegistration Date\tRegistration Time\tSender ID\tSender Name\tSender Organisation\tSender Designation\tSender Organisation Profile\tSender Email\tReceiver ID\tReceiver Name\tReceiver Organisation\tReceiver Designation\tReceiver Organisation Profile\tReceiver Email\tRegistration Type\tMeeting Date\tMeeting Time Start\tMeeting Time End\tMessege ID\tMessage\tStatus\tTable Number";*/

if($selectedEvents == 'Women Of Worth') {
	$csv_output  .= "SR.No\tExhibitors name\tAssociation name\tContact Person Name\tContact Person Designation \tContact Number\tFax Number\tMobile Number\tEmail Id\tWebsite\tAddress\tRegistration Date\tRegistration Time\tBooth Area\tBooth Area Unit\tFacia Name\tTotal Exhibitors";
} else if($selectedEvents == 'Bangalore IT' && $selectedYear == '2018') {
	$csv_output  .= "SR.No\tExhibitors name\tAssociation name\tContact Person Name\tContact Person Designation \tContact Number\tFax Number\tMobile Number\tEmail Id\tWebsite\tAddress\tRegistration Date\tRegistration Time\tBooth Space Type\tBooth Area\tBooth Area Unit\tFacia Name\tTotal Exhibitors";
} else if($selectedEvents == 'Bangalore INDIA NANO' && $selectedYear == '2018') {
	$csv_output  .= "SR.No\tExhibitors name\tContact Person Name\tContact Person Designation \tContact Number\tFax Number\tMobile Number\tEmail Id\tWebsite\tAddress\tRegistration Date\tRegistration Time\tBooth Space Type\tBooth Space Type\tBooth Area\tBooth Area Unit\tFacia Name\tTotal Exhibitors";
} else {
$csv_output  .= "SR.No\tExhibitors name\tName\tDesignation\tContact Number\tMobile Number\tEmail Id\tAddress\tRegistration Date\tRegistration Time\tBooth Area\tBooth Area Unit\tBooth Space\tTotal Exhibitors";
}
	$csv_output .= "\n\n";
	$i_cnt = 1;
	$dbInterlinks->connecttodb(); 	
	while ($row = mysqli_fetch_array($result))
	{	//print_r($row);exit;
			$csv_output .= $i_cnt;
			$csv_output .="\t";	
			$csv_output .=$row['exhibitor_name'];
			$csv_output .="\t";
			if($selectedEvents == 'Women Of Worth' || ($selectedEvents == 'Bangalore IT' && $selectedYear == '2018')) {
				$csv_output .=$row['assoc_nm'];
				$csv_output .="\t";
			}
			$csv_output .=$row['title']." ".$row['fname']." ".$row['lname'];
			$csv_output .="\t";
			$csv_output .=$row['desig'];
			$csv_output .="\t";
			$csv_output .=$row['cntry_code_phone']." ".$row['area_code_phone']." ".$row['phone'];
			$csv_output .="\t";
			$cellno =str_replace('+91-','',$row['mob']);
			$csv_output .=str_replace('+','',$cellno);
			$csv_output .="\t";
			$csv_output .=strtolower($row['email']);
			$csv_output .="\t";
			

			$addr1 = preg_replace( "/\r|\n/", "", $row['address_line_1']);
			$addr1 = htmlspecialchars($addr1);
			$addr1 = strip_tags($addr1);

			$addr2 = preg_replace( "/\r|\n/", "", $row['address_line_2']);
			$addr2 = htmlspecialchars($addr2);
			$addr2 = strip_tags($addr2);

			$csv_output .=$addr1." ".$addr2." ".$row['city']." ".$row['state']." ".$row['country']." ".$row['zip'];
			$csv_output .="\t";
			$csv_output .=$row['reg_date'];
			$csv_output .="\t";
			$csv_output .=$row['reg_time'];
			$csv_output .="\t";
			if(($selectedEvents == 'Bangalore IT' || $selectedEvents == 'Bangalore INDIA NANO') && $selectedYear == '2018') {				
				$csv_output .=$row['booth_space'];
				$csv_output .="\t";
			}
			$csv_output .=$row['booth_area'];
			$csv_output .="\t";
			$csv_output .=$row['booth_area_unit'];
			$csv_output .="\t";
			$csv_output .=$row['booth_space'];
			$csv_output .="\t";
			$csv_output .=$row['total_exbhitors'];
			$i_cnt = $i_cnt + 1 ;
			$csv_output .="\n";
	
	}
	$dbInterlinks->disconnecttodb();  
	
// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"".$file.".xls\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output the CSV/Excel content
echo $csv_output;
exit;

?>