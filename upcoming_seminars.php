<?
include("FX.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Seminar-o-matic</title>
<script src="js/jquery.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){


//Click event for seminar's checkbox
 	$('.seminarSelect').click(function(){
		if($(this).attr("checked")) {
			$(this).parent().addClass("selected");
		} else {
			$(this).parent().removeClass("selected");
		}
	
	});

//"Select All" Event Handler
	$('#selectAll').click(function(){
		//When "Select All" is clicked, do this
		if(! $('.seminarSelect').parent(".selected").length){
			$('.seminarSelect').each(function() {

				//add class("selected")

				$(this).parent().addClass("selected");
				$(this).attr("checked", true);
			})
		}else{
			$('.seminarSelect').each(function() {

				//remove class ("selected")
				
				$(this).parent().removeClass("selected");
				$(this).attr("checked", false);
			})
		}//End "Select All"
	});
   
});
</script>
	<style>
		/************ Doesn't Get Copied ************/
		center{
			background-color: #558ac8;
		}
		form{
			width: 640px;
			background-color: #f5f5f5;
		}
		.container{
			width: 100%;
		}
		#selectAll{
			position: fixed;
			margin-top: 10%;
			margin-left: -105px;
			padding: 10px;
			font-family: Helvetica, sans-serif;
			font-size: 19px;
			width: 130px;
			background-color: #e88b46;
			color: #000000;
			text-decoration: none;
			transition: all 0.5s ease;
		}
		#selectAll:hover{
			background-color: #b71849;
			margin-left: 0;
		}

		/************ General Wrap ************/

		.body_wrap{
			margin: 0 auto;
			width:640px;
		}

		/************ End General Wrap ************/


		/************ Exterior Nav Bar ************/
		.logo{
			margin: 0 auto;
		}
		.logo img{
			width: 130px;
		}
		.ext_nav{
			margin: 0 auto;
		}
		.nav_tables{
			margin:0 auto;
			padding:0;
			display:inline-block;
			width: 280px;
		}
		.nav_tables td{
			margin:0 auto;
			width:260px;
			display:inline;
		}

		/************ End Exterior Nav Bar ************/

		/************ Header Img and Internal Nav Bar ************/

		.header img{
			width:640px;
		}

		.internal_nav{
			margin:0 auto;
			padding: 0 20px;
			display:inline-block;
		}

		/************ End Header Img and Internal Nav Bar ************/

		/************ Introduction ************/

		.text_wrap td{
			width: 500px;
		}
		.text_wrap div{
			margin: 0 auto;
			width: 500px;
		}

		/************ Introduction ************/

		/************ Seminar Schedule ************/

		.two_col{
			margin:0 auto;
			padding: 0;
			display:inline-block;
		}
		.seminars_text{
			width: 100%;
		}
		.seminars_align{
			text-align: left;
		}
		/************ End Seminar Schedule ************/

		/************ Stories ************/

		.stories_body{
			margin:0 auto;
			width:640px;
			border-radius: 0 0 8px 8px;
			-webkit-border-radius: 0 0 8px 8px;
			-moz-border-radius: 0 0 8px 8px;
		}
		.strong_pad{
			height:0%;
		}
		.sound_stories{
			width: 90%;
		}
		.stories_cont{
			width:48%;
		}
		.stories_text{
			height: 200px;
			width: 60%;
		}
		.feet{
			height:20px;
		}

		/************ End Stories ************/

		/************ Footer ************/

		.footer{
			width: 400px;
		}

		/************ End Footer ************/

		/************ Processed Last ************/

		.seminars_width{
			width: 500px;
		}

		@media screen and (max-width: 960px){
			.ext_nav{
				margin: 0 auto;
				width: 600px;
			}
			.seminars_width{
				width: 80%;
			}
		}
		@media screen and (max-width: 768px){

		}
		@media screen and (max-width: 600px){

			/************ General ************/

			.body_wrap{
				width:100%;
			}
			.cat_header td{
				text-align:center;
			}

			/************ End General ************/

			/************ Stories ************/

			.stories_body{
				width:100%;
				border-radius: 0;
				-webkit-border-radius: 0;
				-moz-border-radius: 0;
			}
			.strong_pad{
				height:0%;
			}
			.stories_text{
				width: 60%;
			}
			.stories_resp{
				margin:20px 0;
				width: 100%;
			}
			.stories_head{
				height: 40px;
			}
			.stories_resp tr td{

				width: 90%;
			}
			.stories_resp tr td table{
				width: 80%;
			}
			.feet{
				height: 0;
			}

			/************ End Stories ************/

			/************ Exterior Nav Bar ************/

			.ext_nav{
				width: 80%;
			}
			.ext{
				padding-top:16px;
			}
			.nav_left{
				text-align: left;
			}
			.nav_right{
				text-align: right;
			}
			.nav_tables{
				width: 100%;
				display: table;
			}
			.nav_tables td{
				width: 100%;
				text-align:center;
				display:block;
			}

			/************ End Exterior Nav Bar ************/

			/************ Header Img and Internal Nav Bar ************/

			.header img{
				width: 100%;
			}
			.internal_nav{
				display: table;
				border: 1px solid #000000;
				width: 100%;
			}
			.internal_nav td{
				max-width: 100%;
				height:50px;
				padding: 0 20px;
			}

			/************ End Header Img and Internal Nav Bar ************/

			/************ Introduction ************/

			.text_wrap{
				margin: 0 auto;
				width: 90%;
			}
			.text_wrap td{
				width: 90%;
				text-align:center;
			}
			.text_wrap div{
				margin: 0 auto;
				width: 90%;
			}

			/************ End Introduction ************/

			/************ Seminars ************/

			.two_col{
				padding:0 20px;
			}
			.two_col td{
				width: 100%;
				height: 1.5em;
			}
			.two_col span{
				font-size:1.25em;
				line-height:1.5em;
			}
			.seminars_text{
				padding: 20px;
				width: 100%;
			}
			.seminars_align{
				text-align: center;
			}
			.seminars_pad{
				height:0px;
			}
			.seminars_width{
				width: 100%;
			}
			/************ End Seminars ************/
			/************ General (for later processing) ************/

			.cat_header span{
				font-size: 1.6em;
				line-height:1.6em;
				text-align:center;
			}
			.cat_weight{
				font-weight: 600;
			}
			.division{
				font-size: 1.6em;
				line-height:1.6em;
				text-align:center;
			}
			.division_bar{
				height: 3.5em;
			}
			.body_type{
				font-size: 1.4em;
				line-height: 1.6em;
			}
			.tagline{
				font-size: 1.25em;
				line-height: 1.4em;
			}
			.description{
				font-size: 1.25em;
				line-height: 1.6em;
			}
			.product{
				width: 90%;
			}
			.social_icon{
				padding: 4px;
				width: 45px;
			}
			.logo{
				width: 150px;
			}
			/************ End General (for later processing) ************/
		}

		/*@media screen and (max-width:375px){
			.body{
				width:375px;
			}
			.body_wrap{
				width: 375px;
			}
			.stories_body{
				width:375px;
			}
			.header img{
				width: 375px;
			}
			.intro_wrap{
				width: 280px;
			}
			.footer{
				width:300px;
			}
		}*/
	</style>
<!--
<style>
body {
	font-size:11px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	text-align:center;
}
.listo {
	width:1000px;
	text-align:left;
	margin:0 auto;
	border-collapse:collapse;
	border:1px solid #CACACA;
}
.listo td {
	padding:5px;
}
.selected {background-color:#FFFFFF;}
.dark td {background-color:#EEF0F7;}
.light td {background-color:#FFFFFF;}

</style>-->
</head>

<body style="margin:0 auto;">
	<div class="container">
<!--To view as HTML, <a href="http://www.meyersound.com/mail/seminars.html">click here</a>-->
<?php if(!$_REQUEST['_do']) : ?>       
        <a href="javascript: ;" id="selectAll">Select All &nbsp;&nbsp;&nbsp;&nbsp;&larr;</a>
<?php endif ?>   
<div>
<center>

	<!--<table cellspacing="0" border="0" id="page" style="background-color: #f4f8fc; width: 508px;" cellpadding="0" width="100%">
		<tr>
			<td id="topnav" style="background: #3a4e7f; color: #FFFFFF; vertical-align: top;"><p class="left" style="margin: 5px 0px; float: left;"><a href="http://www.meyersound.com/index.php" style="font-size: 10px; text-transform: uppercase; color: #FFFFFF; text-decoration: none;">Home</a> | <a href="http://meyersound.com/events/seminars/calendar.php" style="font-size: 10px; text-transform: uppercase; color: #FFFFFF; text-decoration: none;">Seminar Dates</a> | <a href="http://meyersound.com/education/" style="font-size: 10px; text-transform: uppercase; color: #FFFFFF; text-decoration: none;">Education Program Info</a> | <a href="mailto:education@meyersound.com" style="font-size: 10px; text-transform: uppercase; color: #FFFFFF; text-decoration: none;">Email Us</a></p>

				<p class="right" style="margin: 5px 0px; padding-top: 2px; float: right;"><a href="mailto:?subject=Sound%20Source:%20Meyer%20Sound%20Education%20Newsletter&body=A%20friend%20thought%20you%20may%20like%20this:%20http://meyersound.com/mail/seminars.html" style="font-size: 10px; text-transform: uppercase; color: #FFFFFF; text-decoration: none;">email this to a friend</a></p>
			</td>
		</tr>
		<tr>
			<td style="vertical-align: top;">
				<table cellspacing="0" border="0" id="inside" style="background: url('http://www.meyersound.com/mail/seminar_images/table-bg.gif') right bottom no-repeat; padding: 11px 0px 0px 0px;" cellpadding="0" width="100%">
					<tr>
						<td class="w497" style="padding: 0px 11px; vertical-align: top;"><a href="http://www.meyersound.com/index.php" style="font-size: 10px; color: #3a4f80; text-decoration: none;"><img src="http://www.meyersound.com/mail/seminar_images/billboard.jpg" alt="Sound Source | Audio Essentials" style="border: none;" /></a></td>
					</tr>
					<tr>
					    <td class="w497" style="padding: 0px 11px; vertical-align: top;">
							<p class="right" style="margin: 5px 0px; float: right;"><span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Upcoming Seminars</span> | <span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Product Updates</span> | <span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Featured Sound Stories</span></p>						</td>
					</tr>
					<tr>
						<td style="vertical-align: top;">
							<p class="left" style="margin: 0px 0px; padding-left: 35px; float: left;"><img src="http://www.meyersound.com/mail/seminar_images/hr.gif" alt="" style="border: none;" /></p>
							<p class="right" style="margin: 0px 0px; float: right;"><a href="http://twitter.com/MeyerSound" style="font-size: 10px; color: #3a4f80; text-decoration: none;"><img src="http://www.meyersound.com/mail/seminar_images/meyer-twitter.jpg" alt="Follow us on Twitter" style="border: none;" /></a></p>						</td>
					</tr>
				</table>-->
	<table align="center" cellspacing="0" border="0" width="100%" style="margin:0 auto;padding:0;background-color:#eeeded;width:100%;" cellpadding="0">
		<tr style="text-align:center;">
			<td style="text-align:center;width:100%" align="center">
				<table cellspacing="0" cellpadding="0" border="0" width="600" style="background-color:#eeeded;" class="ext_nav">
					<tr>
						<td colspan="2" height="20" style="line-height:21px;">
							<div></div>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="/*background-color:green;*/">
							<table cellpadding="0" cellspacing="0" border="0" width="300" class="nav_tables">
								<tr>
									<td colspan="1" style="/*background-color:green;*/" class="nav_left">
										<a href="http://www.meyersound.com"><img style="box-shadow:1px;" width="130" class="logo" src="http://www.meyersound.com/email/email_redesign/sound_source/img/ms_logo_drkGrey.png" alt="Meyer Sound Logo" /></a>
									</td>
								</tr>
							</table>
							<table cellpadding="0" cellspacing="0" border="0" width="300" class="nav_tables ext">
								<tr>
									<td colspan="1" style="vertical-align:bottom;/*background-color:red;*/" class="nav_right">
										<a href="http://www.meyersound.com/events/seminars/" target="_blank" style="font-family:Arial, sans-serif;font-weight:normal;font-size:16px;color:#222222;text-decoration:none;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;">
											<span class="body_type">Education</span>
										</a>
										&nbsp;&nbsp;&nbsp;&nbsp;
										<a href="http://www.meyersound.com/events/seminars/courses.php" target="_blank" style="font-family:Arial, sans-serif;font-weight:normal;font-size:16px;color:#222222;text-decoration:none;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;">
											<span class="body_type">Seminars</span>
										</a>
										&nbsp;&nbsp;&nbsp;&nbsp;
										<a href="mailto:gavinc@meyersound.com" style="font-family:Arial, sans-serif;font-weight:normal;font-size:16px;color:#222222;text-decoration:none;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;">
											<span class="body_type">Contact Us</span>
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20" style="line-height:21px;">
							<div></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>     
			</td>
		</tr>
<!-- 



Seminars start here
Seminars start here
Seminars start here
Seminars start here
Seminars start here


-->
<table cellspacing="0" cellpadding="0" border="0" align="center" width="640" style="margin:0 auto;background-color:#222222;" class="body_wrap">   
    <tr>
    	<td colspan="3" height="60" align="center"
style="font-size:24px;" class="division_bar">
<span style="font-family:Arial, sans-serif;font-weight:normal;color:#eeeded
;text-align:center;-webkit-font-smoothing: antialiased;-moz-osx-font-
smoothing: grayscale;" class="division">upcoming seminars</span>
		</td>
	</tr>
</table>
<table cellspacing="0" cellpadding="0" border="0" width="640" align="center" style="margin:0 auto;padding:0px;background-color:#f5f5f5;text-align:left;" class="stories_body">
	<tr>
		<td colspan="3">
			<table id="seminars" cellspacing="0" cellpadding="0" border="0" width="500" align="center" style="margin:0 auto;padding:0px;background-image:URL('sources/region_NA.jpg');background-color:#f5f5f5;background-repeat:no-repeat;" class="sound_stories seminars_width">
				<tr>
					<td colspan="3" align="center" height="50" style="line-height:21px;">
						<div></div>
					</td>
				</tr>         
<?
	if($_REQUEST['_do'] == 'process') {
		$args = $_REQUEST['selectedSeminars'];
		$webArgs = $_REQUEST['selectedWebinars'];
		echo findSeminars($args, $webArgs);
	} else {
		echo findSeminars();
	}	
?>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="40" style="line-height:21px;">
						<div></div>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="40" style="line-height:21px;">

						<div></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- 



Seminars end here
Seminars end here
Seminars end here
Seminars end here
Seminars end here



-->
</table>

</center>
</body>
</html>


<?
function findSeminars($args = false, $webArgs = false) {
	$sixWeeksFromToday = time() + (60 * 60 * 24 * 7 * 6);
	$dateRange = date('n/j/Y').'...'.date('n/j/Y', $sixWeeksFromToday);
	
	$fm = new FX();
	$fm->SetDBData('meyerSeminar', 'xsl_calendar');
	if($args) {
		foreach($args as $arg) {
			$fm->AddDBParam('KEY', $arg);
		}
		$fm->AddDBParam('-lop', 'or');
	} else {
		$fm->AddDBParam('isOpen', 1);
		$fm->AddDBParam('seminarTopic::isWebinar', 'yes', 'neq');
	}
//	$fm->AddDBParam('startDate', $dateRange);
	
	
	$fm->AddSortParam('venue::territory', 'regions');
	$fm->AddSortParam('startDate', 'ascend');
	$queryResult = $fm->FMFind();  
	if(FX::isError($queryResult)) $cache_this->errorCatch();
	$calendar = $queryResult['data'];
	
	
	// get Webinars
	
	$fm->SetDBData('meyerSeminar', 'xsl_calendar');
	if($webArgs) {
		foreach($webArgs as $arg) {
			$webinar_keys = $_REQUEST['ids_for_'.$arg];
			foreach($webinar_keys as $key) {
				$fm->AddDBParam('KEY', $key);
			}
		}
		$fm->AddDBParam('-lop', 'or');
	} else {
		$fm->AddDBParam('isOpen', 1);
		$fm->AddDBParam('seminarTopic::isWebinar', 'yes', 'eq');
	}
//	$fm->AddDBParam('startDate', $dateRange);
	
	$fm->AddSortParam('startDate', 'ascend');
	$fm->AddSortParam('startTime', 'ascend');
	$queryResult = $fm->FMFind();  
	if(FX::isError($queryResult)) $cache_this->errorCatch();
	if($queryResult['foundCount'] > 0) $webinars = $queryResult['data'];
	
	
	$region = '';
	$lastRegion = '';
	
	if(!$args) {
		$output = '<form action="upcoming_seminars.php" method="post">
								<input type="hidden" name="_do" value="process" />';
	}
	
	
	// seminars loop
	
	foreach($calendar as $recID => $data) {
		// 2 column design.  This $col keeps track of which column we are on, so we can know when to open/close table rows
		$col = (($col == 1)?2:1);
	
		$region = $data['venue::territory'][0];
		
		// split things up by region
		if(!$region || $region != $lastRegion) {
			// different case for the first row
			if($lastRegion) {
				// if row was unfinished, finish it
				if($col == 2) {
					$output .= '<td>&nbsp;</td></tr>';
					$col = 1;
				}
				// close previous section, make divider
				$output .= '
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="40" style="line-height:21px;" class="seminars_pad">
						<div></div>
					</td>
				</tr>';
			}
			
			// open new seminar block (REPLACED)
			$output .= '
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="0" border="0" class="text_wrap">
								<tr>
									<td colspan="1" style="font-size:20px;line-height:28px;" class="cat_header">
										<span style="font-family:Arial,sans-serif;font-weight:bold;color:#558ac8;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing: grayscale;">'.$region.'</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center" height="20" style="line-height:21px;">
							<div style="border-top:1px solid #558ac8;" id="seminars_underline"></div>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center" height="20" style="line-height:21px;">
							<div></div>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">';
		}
		
		
		//open a new row
		if($col == 1) {
			$output .= '<table cellpadding="0" cellspacing="0" border="0" width="210" align="left" class="stories_cont">
							<tr>
								<td colspan="2" align="center">';
		}
		
		// the meat of the seminars
		$id = $data['KEY'][0];
		$regLink = (($data['Cvent_link'][0])?$data['Cvent_link'][0]:'http://www.meyersound.com/seminars/registration.php?id='.$id);
		$language = '('.str_replace(array('(',')'),'',$data['web_language'][0]).')';
		
		$output .= '<table cellpadding="0" cellspacing="0" border="0" class="seminars_text">';
		if(!$args) {
			$output .= '<input type="checkbox" name="selectedSeminars[]" class="seminarSelect" value="'.$id.'">';
		}
		$output .= '
								<tr>
									<td colspan="1" valign="top" style="font-size:18px;line-height:25px;" class="seminars_align">
										<a href="'.$regLink.'" style="font-family:Arial,sans-serif;font-weight:normal;color:#5d6e83;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;text-decoration:none;" class="tagline">'.$data['venue::friendlyLocation'][0].'</a> 
									</td>
								</tr>
								<tr>
									<td colspan="1" valign="top" style="font-size:14px;line-height:21px;" class="seminars_align">
										<span style="font-family:Arial, sans-serif;font-weight:normal;color:#0f4dbc;text-decoration:none;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;" class="description">'.$data['friendlyDate'][0].'</span>
									</td>
								</tr>
								<tr>
									<td colspan="1" valign="top" style="font-size:14px;line-height:21px;" class="seminars_align">
										<span style="font-family:Arial, sans-serif;font-weight:normal;color:#51667c;text-decoration:none;-webkit-font-smoothing: antialiased;-moz-osx-font-smoothing: grayscale;" class="description">'.$data['seminarTopic::friendlyName'][0].' '.(($region != 'North America')?$language:'').'</span>
									</td>
							</tr>
						</table>';
									
		if($col == 2) {
			$output .= '	</td>
						</tr>
					</table>';
		}
		
		$lastRegion = $region;
		
	}	
	
	// close last region:
	$output .= '
						</table>
					</td>
				</tr>';
	// open new divider if there are webinars	to display
	if($webinars) {
		$output .= '
				<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">
							<p class="left" style="margin: 2px 0px; float: left;"><img src="http://www.meyersound.com/mail/seminar_images/short-hr.gif" alt="" style="border: none;" /></p>
						</td>
				</tr>
				<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">

							<p class="left" style="margin: 5px 0px; float: left;"><span class="mh1 blue normal" style="font-size: 14px; font-weight: normal; color: #3a4e7f;">Webinars</span></p>						</td>
					</tr>
					<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">
							<table cellspacing="0" border="0" cellpadding="0" width="100%">';
	
		$col = 0;
		// webinars loop
		// first loop through and group webinars by the same date, and format so multiple webinars on one day gets one entry
		// webinars are put into an array, keyed by dateKey (seminar date and topic ID)
		foreach($webinars as $recID => $data) {
			
			// the meat of the seminars
			$id = $data['KEY'][0];
			$regLink = (($data['Cvent_link'][0])?$data['Cvent_link'][0]:'http://www.meyersound.com/seminars/registration.php?id='.$id);
			$language = '('.str_replace(array('(',')'),'',$data['web_language'][0]).')';
			
			$dateKey = str_replace(' ','_',str_replace(',','',$data['friendlyDate'][0]).'-'.$data['seminarTopic::KEY'][0]);
			
			$web[$dateKey]['courseName'] = str_replace('Webinar - ','',$data['seminarTopic::friendlyName'][0]).' '.(($region != 'North America')?$language:'');
			$web[$dateKey]['date'] = $data['friendlyDate'][0];
			//$web[$data['friendlyDate'][0]]['topicKey'] = $data['seminarTopic::KEY'][0];
			
			$web[$dateKey]['time'][$id] = '<a href="'.$regLink.'" style="font-size: 11px; color: #555656; text-decoration: none;">Register for '.formatFMTime($data['startTime'][0]).' PST</a>';
			
		}
		
		foreach($web as $dateKey => $data) {
			// 2 column design.  This $col keeps track of which column we are on, so we can know when to open/close table rows
			$col = (($col == 1)?2:1);
			
			//open a new row
			if($col == 1) {
				$output .= '<tr>';
			}
			
			// the meat of the seminars
			$id = $data['KEY'][0];
			$regLink = (($data['Cvent_link'][0])?$data['Cvent_link'][0]:'http://www.meyersound.com/seminars/registration.php?id='.$id);
			$language = '('.str_replace(array('(',')'),'',$data['web_language'][0]).')';
			
			$output .= '
								<td style="vertical-align: top;">';
			if(!$args) {
				// each webinar has 1 or more distinct course IDs for different times
				// make one checkbox for this course (by date+key, replacing commas and spaces w/ underscores
				//$dateKey = str_replace(' ','_',str_replace(',','',$data['date']).'-'.$data['topicKey']);
				$output .= '<input type="checkbox" name="selectedWebinars[]" class="seminarSelect" value="'.$dateKey.'">';
				foreach($data['time'] as $key => $time) {
					$output .= '<input type="hidden" name="ids_for_'.$dateKey.'[]" value="'.$key.'">';
				}
			}
			$output .= '
									<ul class="seminar" style="margin: 0px; list-style: none; line-height: normal; padding: 0px 0px 20px 0px; width: 224px;">
										<li class="sloc" style="font-weight: bold; font-size: 12px; color: #555656;">
											'.str_replace('(1 hour)', '', $data['courseName']).'
											
										</li>
										<li class="sdate" style="font-size: 11px; color: #3a4e7f;">'.$data['date'].'</li>';
			foreach($data['time'] as $key => $time) {
				$output .= '<li class="sinfo" style="font-size: 11px; width: 150px;">'.$time.'</li>';
			}
			$output .= '
									</ul> 
								</td>';
										
			if($col == 2) {
				$output .= '</tr>';
			}
			
			
		}	
		// close last row
		$output .= '		</table>
						</td>
					</tr>';
	} // end if webinars	
	if(!$args) {
		$output .= '
						<tr>
					<td><input type="submit" value="submit">
				</form>
			</td>
		</tr>';
	}
	
	return $output;
}

////////////////////////////////////////////////////
//////// formats FMTime HH:MM:SS to timestamp /////
//////////////////////////////////////////////////
function formatFMTime($fmTime) {
	$time = explode(":",$fmTime);
	$time = mktime($time[0],$time[1]);
	$time = date("ga",$time);
	return $time;
}
?>