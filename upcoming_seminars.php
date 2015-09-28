<?
include("../FX/FX.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Seminar-o-matic</title>
<script src="../jquery/jquery.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
  $('.seminarSelect').click(function(){
		if($(this).attr("checked")) {
			$(this).parent().addClass("selected");
		} else {
			$(this).parent().removeClass("selected");
		}
	
	});
	
	$('#selectAll').click(function(){
		$('.seminarSelect').each(function() {
			$(this).parent().addClass("selected");
			$(this).attr("checked", true);
		})

//		addClass("selected")
	});
   
});
</script>
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

</style>
</head>

<body style="font-size: 80%; background: #3a4e7f; font-family: arial, tahoma, verdana; color: #555656;">
<!--To view as HTML, <a href="http://www.meyersound.com/mail/seminars.html">click here</a>-->

<center>
	<table cellspacing="0" border="0" id="page" style="background-color: #f4f8fc; width: 508px;" cellpadding="0" width="100%">
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
							<p class="right" style="margin: 5px 0px; float: right;"><span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Upcoming Seminars</span> |<!-- <span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Sound Solutions</span> |--> <span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Product Updates</span> | <span class="nolink" style="font-size: 10px; color: #3a4f80; text-decoration: none;">Featured Sound Stories</span></p>						</td>
					</tr>
					<tr>
						<td style="vertical-align: top;">
							<p class="left" style="margin: 0px 0px; padding-left: 35px; float: left;"><img src="http://www.meyersound.com/mail/seminar_images/hr.gif" alt="" style="border: none;" /></p>
							<p class="right" style="margin: 0px 0px; float: right;"><a href="http://twitter.com/MeyerSound" style="font-size: 10px; color: #3a4f80; text-decoration: none;"><img src="http://www.meyersound.com/mail/seminar_images/meyer-twitter.jpg" alt="Follow us on Twitter" style="border: none;" /></a></p>						</td>
					</tr>
				</table>
<?php if(!$_REQUEST['_do']) : ?>       
        <a href="javascript: ;" id="selectAll">Select All</a>
<?php endif ?>        
			</td>
		</tr>
<!-- 



Seminars start here
Seminars start here
Seminars start here
Seminars start here
Seminars start here


-->
    
    <tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">
							<p class="left" style="margin: 5px 0px; float: left;"><span class="mh2" style="font-size: 14px; font-weight: bold;">upcoming</span> <span class="mh2 blue" style="font-size: 14px; font-weight: bold; color: #3a4e7f;">seminars</span></p>						</td>
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
						</table>
					</td>
				</tr>
				<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">
							<p class="left" style="margin: 2px 0px; float: left;"><img src="http://www.meyersound.com/mail/seminar_images/short-hr.gif" alt="" style="border: none;" /></p>
						</td>
				</tr>';
			}
			
			// open new seminar block
			$output .= '
					
					<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">

							<p class="left" style="margin: 5px 0px; float: left;"><span class="mh1 blue normal" style="font-size: 14px; font-weight: normal; color: #3a4e7f;">'.$region.'</span></p>						</td>
					</tr>
					<tr>
						<td class="w448" style="font-size: 11px; line-height: 22px; padding: 0px 35px; vertical-align: top;">
							<table cellspacing="0" border="0" cellpadding="0" width="100%">';
		}
		
		
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
			$output .= '<input type="checkbox" name="selectedSeminars[]" class="seminarSelect" value="'.$id.'">';
		}
		$output .= '
								<ul class="seminar" style="margin: 0px; list-style: none; line-height: normal; padding: 0px 0px 20px 0px; width: 224px;">
									<li class="sloc" style="font-weight: bold; font-size: 12px; color: #555656;">
										<a href="'.$regLink.'" style="font-size: 12px; font-weight: bold; color: #555656; text-decoration: none;">'.$data['venue::friendlyLocation'][0].'</a> 
										<a href="'.$regLink.'" style="font-size: 12px; font-weight: bold; color: #555656; text-decoration: none;">
										<img src="http://www.meyersound.com/mail/seminar_images/arrow.gif" alt="arrow" style="border: none;" /></a>
									</li>
									<li class="sdate" style="font-size: 11px; color: #3a4e7f;">'.$data['friendlyDate'][0].'</li>
									<li class="sinfo" style="font-size: 11px; width: 150px;">'.$data['seminarTopic::friendlyName'][0].' '.(($region != 'North America')?$language:'').'</li>
								</ul> 
							</td>';
									
		if($col == 2) {
			$output .= '</tr>';
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
		$output .= '</table>
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