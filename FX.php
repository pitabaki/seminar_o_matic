<?php
#### FX.php #############################################################
#                                                                       #
#       By: Chris Hansen with Chris Adams, Gjermund Thorsen, and others #
#  Version: 4.2                                                         #
#     Date: 03 Nov 2005                                                 #
# Web Site: www.iviking.org                                             #
#  Details: FX is a free open-source PHP class for accessing FileMaker  #
#          data.  For complete details about this class, please visit   #
#          www.iviking.org.                                             #
#                                                                       #
#########################################################################

define("FX_VERSION", '4.2');                                            // Current version information for FX.php.  New constants as of version 4.0.
define("FX_VERSION_FULL", "FX.php version 4.2 (03 Nov 2005) by Chris Hansen, Chris Adams, Gjermund Thorsen, and others.");

require_once('FX_Error.php');                                           // This version of FX.php includes object based error handling.  See
                                                                        // FX_Error.php for more information.

require_once('FX_constants.php');                                       // The constants in this file are designed to be used with DoFXAction()

define("EMAIL_ERROR_MESSAGES", false);                                  // Set this to TRUE to enable emailing of specific error messages.
define("DISPLAY_ERROR_MESSAGES", false);                               // Set this to FALSE to hide system error messages from the user. 
																																			 // The alt message can be modified in FX_Error.php

function EmailError ($errorText)
{
		require_once("phpmailer/class.phpmailer.php");
		$mail=new PHPMailer();

    if (EMAIL_ERROR_MESSAGES) {
				$mail->IsSMTP('true');					// set mailer to use SMTP
				$mail->Host = "192.168.50.27";	// specify main and backup server
				$mail->SMTPAuth = false;				// turn on SMTP authentication
				$mail->Username = "";						// SMTP username
				$mail->Password = "";						// SMTP password
								
				$mail->From = "snowball@meyersound.com";
				$mail->FromName = "Error on Snowball";

				$mail->WordWrap = 70;						// set word wrap to 70 characters
				$mail->Subject = 'PHP Server Error';

				$text =  "The following error just occured:\r\nMessage: {$errorText}\r\n"; 
				$mail->AddAddress('ashwinib@meyersound.com');        

				$mail->IsHTML(false);  
				$mail->Body = $text; 

				$mail->Send();
    }
}

function EmailErrorHandler ($FXErrorObj)
{    
	//if error not written to log
	if(checkLog()==false) {
		//send email to admins
		EmailError($FXErrorObj->message);
	}
	//write it
	writeLog($FXErrorObj->message);
	
    if (DISPLAY_ERROR_MESSAGES) {
        echo($FXErrorObj->message);
    }
    return true;
}


function writeLog($message)
{
	$handle = @fopen("/webdir/FX/logs/errorlog.txt", "a+");
	if ($handle) {
        fwrite($handle, date('Ymd h:i:s A').' '.$message."\n");
	    fclose($handle); 
	}
}

function checkLog()
{
	$handle = @file_get_contents("/webdir/FX/logs/errorlog.txt");
	if ($handle) {
        $pos = strpos($handle, date('Ymd h'));          
		if ($pos === false){
			return false;
		} else return true;
	}
}


class FX
{
    // These are the basic database variables.
    var $dataServer = "192.168.50.42";
    var $dataServerType = 'FMPro7';
    var $dataPort = "80";
    var $dataPortSuffix;
    var $urlScheme;
    var $database = "";
    var $layout = ""; // the layout to be accessed for FM databases.  For SQL, the table to be accessed.
    var $responseLayout = "";
    var $groupSize;
    var $currentSkip = 0;
    var $defaultOperator = 'bw';
    var $dataParams = array();
    var $sortParams = array();

    // Variables to help with SQL queries
    var $primaryKeyField = '';
    var $modifyDateField = '';
    var $dataKeySeparator = '';
    var $fuzzyKeyLogic = false;
    var $genericKeys = false;
    var $selectColsSet = false;
    var $selectColumns = '';

    // These are the variables to be used for storing the retrieved data.
    var $fieldInfo = array();
    var $currentData = array();
    var $valueLists = array();
    var $totalRecordCount = -1;
    var $foundCount = -1;
    var $dateFormat = "";
    var $timeFormat = "";
    var $dataURL = "";
    var $dataURLParams = "";
    var $dataQuery = "";

    // Variables used to track how data is moved in and out of FileMaker.  Used when UTF-8 just doesn't cut it (as when working with Japanese characters.)
    // This and all related code were submitted by Masayuki Nii.
    // Note that if either of these variables are simply empty, UTF-8 is the default.
    var $charSet = '';                                                  // Determines how outgoing data is encoded.
    var $dataParamsEncoding = '';                                       // Determines how incoming data is encoded.


    // Flags and Error Tracking
    var $currentFlag = '';
    var $currentRecord = '';
    var $currentField = '';
    var $currentValueList = '';
    var $fieldCount = 0;
    var $columnCount = -1;                                                // columnCount is ++ed BEFORE looping
    var $fxError = 'No Action Taken';
    var $errorTracking = 0;
    var $useInnerArray = true;                                              // Do NOT change this variable directly.  Use FlattenInnerArray() or the appropriate param of action method.

    // These variables will be used if you need a password to access your data.
    var $DBUser = 'FX';
    var $DBPassword = '';                                                 // This can be left blank, or replaced with a default or dummy password.
    var $userPass = '';

    // These variables are related to sending data to FileMaker via a Post.
    var $defaultPostPolicy = true;
    var $isPostQuery;
    var $useCURL = true;

    // When returning your data via the 'object' return type, these variables will contain the database meta data
    var $lastLinkPrevious = '';
    var $lastLinkNext = '';
    var $lastFoundCount = -2;
    var $lastFields = array();
    var $lastURL = '';
    var $lastQuery = '';
    var $lastErrorCode = -2;
    var $lastValueLists = array();

    // Other variables
    var $invalidXMLChars = array("\x0B", "\x0C", "\x12");

    /*
        Translation arrays used with str_replace to handle special
        characters in UTF-8 data received from FileMaker. The two arrays
        should have matching numeric indexes such that $UTF8SpecialChars[0]
        contains the raw binary equivalent of $UTF8HTMLEntities[0].

        This would be a perfect use for strtr(), except that it only works
        with single-byte data. Instead, we use preg_replace, which means
        that we need to delimit our match strings

        Please note that in this latest release I've removed the need for
        the include files which contained long lists of characters. Gjermund
        was sure there was a better way and he was right. With the two six
        element arrays below, every unicode character is allowed for. Let
        me know how this works for you. A link to Gjermund's homepage can
        be found in the FX Links section of www.iViking.org.
     */
    var $UTF8SpecialChars = array(
        "|([\xC2-\xDF])([\x80-\xBF])|e",
        "|(\xE0)([\xA0-\xBF])([\x80-\xBF])|e",
        "|([\xE1-\xEF])([\x80-\xBF])([\x80-\xBF])|e",
        "|(\xF0)([\x90-\xBF])([\x80-\xBF])([\x80-\xBF])|e",
        "|([\xF1-\xF3])([\x80-\xBF])([\x80-\xBF])([\x80-\xBF])|e",
        "|(\xF4)([\x80-\x8F])([\x80-\xBF])([\x80-\xBF])|e"
    );

    var $UTF8HTMLEntities = array(
        "\$this->BuildExtendedChar('\\1','\\2')",
        "\$this->BuildExtendedChar('\\1','\\2','\\3')",
        "\$this->BuildExtendedChar('\\1','\\2','\\3')",
        "\$this->BuildExtendedChar('\\1','\\2','\\3','\\4')",
        "\$this->BuildExtendedChar('\\1','\\2','\\3','\\4')",
        "\$this->BuildExtendedChar('\\1','\\2','\\3','\\4')"
    );

    function BuildExtendedChar ($byteOne, $byteTwo="\x00", $byteThree="\x00", $byteFour="\x00")
    {
        if (ord($byteTwo) >= 128) {
            $tempChar = substr(decbin(ord($byteTwo)), -6);
            if (ord($byteThree) >= 128) {
                $tempChar .= substr(decbin(ord($byteThree)), -6);
                if (ord($byteFour) >= 128) {
                    $tempChar .= substr(decbin(ord($byteFour)), -6);
                    $tempChar = substr(decbin(ord($byteOne)), -3) . $tempChar;
                } else {
                    $tempChar = substr(decbin(ord($byteOne)), -4) . $tempChar;
                }
            } else {
                $tempChar = substr(decbin(ord($byteOne)), -5) . $tempChar;
            }
        } else $tempChar = $byteOne;
        $tempChar = '&#' . bindec($tempChar) . ';';
        return $tempChar;
    }

    function ClearAllParams ()
    {
        $this->userPass = "";
        $this->dataURL = "";
        $this->dataURLParams = "";
        $this->dataQuery = "";
        $this->dataParams = array();
        $this->sortParams = array();
        $this->fieldInfo = array();
        $this->valueLists = array();
        $this->fieldCount = 0;
        $this->currentSkip = 0;
        $this->currentData = array();
        $this->columnCount = -1;
        $this->currentRecord = "";
        $this->currentField = "";
        $this->currentFlag = "";
        $this->isPostQuery = $this->defaultPostPolicy;
        $this->primaryKeyField = '';
        $this->modifyDateField = '';
        $this->dataKeySeparator = '';
        $this->fuzzyKeyLogic = false;
        $this->genericKeys = false;
        $this->useInnerArray = true;
    }

    function ErrorHandler ($errorText)
    {
        $this->fxError = $errorText;
        $this->errorTracking = 3300;
        return $errorText;
    }

    function FX ($dataServer=false, $dataPort=false, $dataType='', $dataURLType='')
    {
        $this->dataServer = is_string($dataServer) ? $dataServer :  $this->dataServer;
        $this->dataPort = ($dataPort) ? $dataPort :  $this->dataPort;
        $this->dataPortSuffix = ":" . $this->dataPort;
        if (strlen($dataType) > 0) {
            $this->dataServerType = $dataType;
        }
        if (strlen($dataURLType) > 0 && $dataType == 'FMPro7' && strtolower($dataURLType) == 'https') {
            $this->urlScheme = 'https';
        } else {
            $this->urlScheme = 'http';
        }

        $this->ClearAllParams();
    }

    function CreateCurrentSort ()
    {
        $currentSort = "";

        foreach ($this->sortParams as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $$key2 = $value2;
            }
            if (strtolower($this->dataServerType) == 'fmpro7') {
                if ($sortOrder == "") {
                    $currentSort .= "&-sortfield.{$key1}=" . str_replace ("%3A%3A", "::", rawurlencode($field));
                }
                else {
                    $currentSort .= "&-sortfield.{$key1}=" . str_replace ("%3A%3A", "::", rawurlencode($field)) . "&-sortorder.{$key1}=" . $sortOrder;
                }
            } else {
                if ($sortOrder == "") {
                    $currentSort .= "&-sortfield=" . str_replace ("%3A%3A", "::", rawurlencode($field));
                }
                else {
                    $currentSort .= "&-sortfield=" . str_replace ("%3A%3A", "::", rawurlencode($field)) . "&-sortorder=" . $sortOrder;
                }
            }
        }
        return $currentSort;
    }

    function CreateCurrentSearch ()
    {
        $currentSearch = '';

        foreach ($this->dataParams as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $$key2 = $value2;
            }
            if ($op == "" && $this->defaultOperator == 'bw') {
                $currentSearch .= "&" . str_replace ("%3A%3A", "::", urlencode($name)) . "=" . urlencode($value);
            } else {
                if ($op == "") {
                    $op = $this->defaultOperator;
                }
                switch (strtolower($this->dataServerType)) {
                    case 'fmpro5/6':
                        $currentSearch .= "&-op=" . $op . "&" . str_replace("%3A%3A", "::", urlencode($name)) . "=" . urlencode($value);
                        break;
                    case 'fmpro7':
                        $tempFieldName = str_replace("%3A%3A", "::", urlencode($name));
                        $currentSearch .= "&" . $tempFieldName . ".op=" . $op . "&" . $tempFieldName . "=" . urlencode($value);
                        break;
                }
            }
        }
        return $currentSearch;
    }

    function AssembleCurrentSearch ($layRequest, $skipRequest, $currentSort, $currentSearch, $action, $FMV=6)
    {
        $tempSearch = '';

        $tempSearch = "-db=" . urlencode($this->database);               // add the name of the database...
        $tempSearch .= $layRequest;                                      // and any layout specified...
        if ($FMV < 7) {
            $tempSearch .= "&-format=-fmp_xml";                              // then set the FileMaker XML format to use...
        }
        $tempSearch .= "&-max=$this->groupSize$skipRequest";             // add the set size and skip size data...
        $tempSearch .= $currentSort . $currentSearch . "&" . $action;    // finally, add sorting, search parameters, and action data.
        return $tempSearch;
    }

    function StartElement($parser, $name, $attrs)                        // The functions to start XML parsing begin here
    {
        switch(strtolower($name)) {
             case "data":
                $this->currentFlag = "parseData";
                if (! $this->useInnerArray) {
                    $this->currentData[$this->currentRecord][$this->currentField] = "";
                } else {
                    $this->currentData[$this->currentRecord][$this->currentField][$this->currentFieldIndex] = "";
                }
                break;
            case "col":
                $this->currentFieldIndex = 0;
                ++$this->columnCount;
                $this->currentField = $this->fieldInfo[$this->columnCount]['name'];
                if ($this->useInnerArray) {
                    $this->currentData[$this->currentRecord][$this->currentField] = array();
                }
                break;
            case "row":
                foreach ($attrs as $key => $value) {
                    $key = strtolower($key);
                    $$key = $value;
                }
                if (substr_count($this->dataURL, '-dbnames') > 0 || substr_count($this->dataURL, '-layoutnames') > 0) {
                    $modid = count($this->currentData);
                }
                $this->currentRecord = $recordid . '.' . $modid;
                $this->currentData[$this->currentRecord] = array();
                break;
            case "field":
                if ($this->charSet  != '' && defined('MB_OVERLOAD_STRING')) {
                    foreach ($attrs as $key => $value) {
                        $key = strtolower($key);
                        $this->fieldInfo[$this->fieldCount][$key] = mb_convert_encoding($value, $this->charSet, 'UTF-8');
                    }
                } else {
                    foreach ($attrs as $key => $value) {
                        $key = strtolower($key);
                        $this->fieldInfo[$this->fieldCount][$key] = $value;
                    }
                }
                $this->fieldInfo[$this->fieldCount]['extra'] = ''; // for compatibility w/ SQL databases
                if (substr_count($this->dataURL, '-view') < 1) {
                    $this->fieldCount++;
                }
                break;
            case "style":
                foreach ($attrs as $key => $value) {
                    $key = strtolower($key);
                    $this->fieldInfo[$this->fieldCount][$key] = $value;
                }
                break;
            case "resultset":
                foreach ($attrs as $key => $value) {
                    switch(strtolower($key)) {
                        case "found":
                          $this->foundCount = (int)$value;
                          break;
                    }
                }
                break;
            case "errorcode":
                $this->currentFlag = "fmError";
                break;
            case "valuelist":
                foreach ($attrs as $key => $value) {
                    if (strtolower($key) == "name") {
                        $this->currentValueList = $value;
                    }
                }
                $this->valueLists[$this->currentValueList] = array();
                $this->currentFlag = "values";
                $this->currentValueListElement = -1;
                break;
            case "value":
                $this->currentValueListElement++;
                $this->valueLists[$this->currentValueList][$this->currentValueListElement] = "";
                break;
            case "database":
                foreach ($attrs as $key => $value) {
                    switch(strtolower($key)) {
                        case "dateformat":
                          $this->dateFormat = $value;
                          break;
                        case "records":
                          $this->totalRecordCount = $value;
                          break;
                        case "timeformat":
                          $this->timeFormat = $value;
                          break;
                    }
                }
                break;
            default:
                break;
        }
    }

    function ElementContents($parser, $data)
    {
        switch($this->currentFlag) {
            case "parseData":
                if ($this->dataParamsEncoding  != '' && defined('MB_OVERLOAD_STRING')) {
                    if (! $this->useInnerArray) {
                        $this->currentData[$this->currentRecord][$this->currentField] .= mb_convert_encoding($data, $this->charSet, 'UTF-8');
                    } else {
                        $this->currentData[$this->currentRecord][$this->currentField][$this->currentFieldIndex] .= mb_convert_encoding($data, $this->charSet, 'UTF-8');
                    }
                } else {
                    if (! $this->useInnerArray) {
                        $this->currentData[$this->currentRecord][$this->currentField] .= preg_replace($this->UTF8SpecialChars, $this->UTF8HTMLEntities, $data);
                    } else {
                        $this->currentData[$this->currentRecord][$this->currentField][$this->currentFieldIndex] .= preg_replace($this->UTF8SpecialChars, $this->UTF8HTMLEntities, $data);
                    }
                }
                break;
            case "fmError":
                $this->fxError = $data;
                break;
            case "values":
                $this->valueLists[$this->currentValueList][$this->currentValueListElement] .= preg_replace($this->UTF8SpecialChars, $this->UTF8HTMLEntities, $data);
                break;
        }
    }

    function EndElement($parser, $name)
    {
        switch(strtolower($name)) {
            case "data":
                $this->currentFieldIndex++;
                $this->currentFlag = "";
                break;
            case "col":
                break;
            case "row":
                $this->columnCount = -1;
                break;
            case "field":
                if (substr_count($this->dataURL, '-view') > 0) {
                    $this->fieldCount++;
                }
                break;
            case "errorcode":
            case "valuelist":
                $this->currentFlag = "";
                break;
        }
    }                                                                     // XML Parsing Functions End Here

    function RetrieveFMData ($action)
    {
        $data = '';
        if ($this->DBPassword != '') {                                      // Assemple the Password Data
            $this->userPass = $this->DBUser . ':' . $this->DBPassword . '@';
        }
        if ($this->layout != "") {                                          // Set up the layout portion of the query.
            $layRequest = "&-lay=" . urlencode($this->layout);
        }
        else {
            $layRequest = "";
        }
        if ($this->currentSkip > 0) {                                       // Set up the skip size portion of the query.
            $skipRequest = "&-skip=$this->currentSkip";
        } else {
            $skipRequest = "";
        }
        $currentSort = $this->CreateCurrentSort();
        $currentSearch = $this->CreateCurrentSearch();
        $this->dataURL = "http://{$this->userPass}{$this->dataServer}{$this->dataPortSuffix}/FMPro"; // First add the server info to the URL...
        $this->dataURLParams = $this->AssembleCurrentSearch($layRequest, $skipRequest, $currentSort, $currentSearch, $action);
        $this->dataURL .= '?' . $this->dataURLParams;

        if (defined("DEBUG") and DEBUG) {
            echo "<P>Using FileMaker URL: <a href=\"{$this->dataURL}\">{$this->dataURL}</a><P>\n";
        }

        if (defined("HAS_PHPCACHE") and defined("FX_USE_PHPCACHE") and strlen($this->dataURLParams) <= 510 and (substr_count($this->dataURLParams, '-find') > 0 || substr_count($this->dataURLParams, '-view') > 0 || substr_count($this->dataURLParams, '-dbnames') > 0 || substr_count($this->dataURLParams, '-layoutnames') > 0)) {
            $data = get_url_cached($this->dataURL);
            if (! $data) {
                return new FX_Error("Failed to retrieve cached URL in RetrieveFMData()");
            }
            $data = $data["Body"];
        } elseif ($this->isPostQuery) {
            if ($this->useCURL && defined("CURLOPT_TIMEVALUE")) {
                $curlHandle = curl_init(str_replace($this->dataURLParams, '', $this->dataURL));
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->dataURLParams);
                ob_start();
                if (! curl_exec($curlHandle)) {
                    return new FX_Error("cURL could not retrieve Post data in RetrieveFMData(). A bad URL is the most likely reason.");
                }
                curl_close($curlHandle);
                $data = trim(ob_get_contents());
                ob_end_clean();
                if (substr($data, -1) != '>') {
                    $data = substr($data, 0, -1);
                }
            } else {
                $dataDelimiter = "\r\n";
                $socketData = "POST /FMPro HTTP/1.0{$dataDelimiter}";
                if (strlen(trim($this->userPass)) > 1) {
                    $socketData .= "Authorization: Basic " . base64_encode($this->DBUser . ':' . $this->DBPassword) . $dataDelimiter;
                }
                $socketData .= "Host: {$this->dataServer}:{$this->dataPort}{$dataDelimiter}";
                $socketData .= "Pragma: no-cache{$dataDelimiter}";
                $socketData .= "Content-length: " . strlen($this->dataURLParams) . $dataDelimiter;
                $socketData .= "Content-type: application/x-www-form-urlencoded{$dataDelimiter}";
                // $socketData .= "Connection: close{$dataDelimiter}";
                $socketData .= $dataDelimiter . $this->dataURLParams;

                $fp = fsockopen ($this->dataServer, $this->dataPort, $this->errorTracking, $this->fxError, 30);
                if (! $fp) {
                    return new FX_Error( "Could not fsockopen the URL in retrieveFMData" );
                }
                fputs ($fp, $socketData);
                while (!feof($fp)) {
                    $data .= fgets($fp, 128);
                }
                fclose($fp);
                $pos = strpos($data, chr(13) . chr(10) . chr(13) . chr(10)); // the separation code
                $data = substr($data, $pos + 4) . "\r\n";
            }
        } else {
            $fp = fopen($this->dataURL, "r");
            if (! $fp) {
                return new FX_Error("Could not fopen URL in RetrieveFMData.");
            }
            while (!feof($fp)) {
                $data .= fread($fp, 4096);
            }
            fclose($fp);
        }
        $data = str_replace($this->invalidXMLChars, '', $data);
        return $data;
    }

    function RetrieveFM7Data ($action)
    {
        $data = '';
        if ($this->DBPassword != '' || $this->DBUser != 'FX') {             // Assemple the Password Data
            $this->userPass = $this->DBUser . ':' . $this->DBPassword . '@';
        }
        if ($this->layout != "") {                                          // Set up the layout portion of the query.
            $layRequest = "&-lay=" . urlencode($this->layout);
            if ($this->responseLayout != "") {
                $layRequest .= "&-lay.response=" . urlencode($this->responseLayout);
            }
        }
        else {
            $layRequest = "";
        }
        if ($this->currentSkip > 0) {                                       // Set up the skip size portion of the query.
            $skipRequest = "&-skip={$this->currentSkip}";
        } else {
            $skipRequest = "";
        }
        $currentSort = $this->CreateCurrentSort();
        $currentSearch = $this->CreateCurrentSearch();
        if ($action == '-view') {
            $FMFile = 'FMPXMLLAYOUT.xml';
        } else {
            $FMFile = 'FMPXMLRESULT.xml';
        }
        $this->dataURL = "{$this->urlScheme}://{$this->userPass}{$this->dataServer}{$this->dataPortSuffix}/fmi/xml/{$FMFile}"; // First add the server info to the URL...
        $this->dataURLParams = $this->AssembleCurrentSearch($layRequest, $skipRequest, $currentSort, $currentSearch, $action, 7);
        $this->dataURL .= '?' . $this->dataURLParams;

        if (defined("DEBUG") and DEBUG) {
            echo "<P>Using FileMaker URL: <a href=\"{$this->dataURL}\">{$this->dataURL}</a><P>\n";
        }

        if (defined("HAS_PHPCACHE") and defined("FX_USE_PHPCACHE") and strlen($this->dataURLParams) <= 510 and (substr_count($this->dataURLParams, '-find') > 0 || substr_count($this->dataURLParams, '-view') > 0 || substr_count($this->dataURLParams, '-dbnames') > 0 || substr_count($this->dataURLParams, '-layoutnames') > 0)) {
            $data = get_url_cached($this->dataURL);
            if (! $data) {
                return new FX_Error("Failed to retrieve cached URL in RetrieveFM7Data()");
            }
            $data = $data["Body"];
        } elseif ($this->isPostQuery) {
            if ($this->useCURL && defined("CURLOPT_TIMEVALUE")) {
             	$curlURL  = str_replace($this->dataURLParams, '', $this->dataURL);
             	$curlURL  = str_replace('?','%23', $curlURL);
            	$curlURL .= 'scriptfile=' . str_replace('/var/www/meyersound.com/', '', $_SERVER["SCRIPT_FILENAME"]);
                $curlHandle = curl_init($curlURL);
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $this->dataURLParams);
                ob_start();
                if (! curl_exec($curlHandle)) {
                    return new FX_Error("cURL could not retrieve Post data in RetrieveFM7Data(). A bad URL is the most likely reason.");
                }
                curl_close($curlHandle);
                $data = trim(ob_get_contents());
                ob_end_clean();
                if (substr($data, -1) != '>') {
                    $data = substr($data, 0, -1);
                }
            } else {
                $dataDelimiter = "\r\n";
                $logData = '#scriptfile=' . str_replace('/var/www/meyersound.com/', '', $_SERVER["SCRIPT_FILENAME"]);
                $socketData = "POST /fmi/xml/{$FMFile}?{$logData} HTTP/1.0{$dataDelimiter}";
                if (strlen(trim($this->userPass)) > 1) {
                    $socketData .= "Authorization: Basic " . base64_encode($this->DBUser . ':' . $this->DBPassword) . $dataDelimiter;
                }
                $socketData .= "Host: {$this->dataServer}:{$this->dataPort}{$dataDelimiter}";
                $socketData .= "Pragma: no-cache{$dataDelimiter}";
                $socketData .= "Content-length: " . strlen($this->dataURLParams) . $dataDelimiter;
                $socketData .= "Content-type: application/x-www-form-urlencoded{$dataDelimiter}";
                $socketData .= $dataDelimiter . $this->dataURLParams;

                $fp = fsockopen ($this->dataServer, $this->dataPort, $this->errorTracking, $this->fxError, 30);
                if (! $fp) {
                    return new FX_Error( "Could not fsockopen the URL in retrieveFM7Data" );
                }
                fputs ($fp, $socketData);
                while (!feof($fp)) {
                    $data .= fgets($fp, 128);
                }
                fclose($fp);
                $pos = strpos($data, chr(13) . chr(10) . chr(13) . chr(10)); // the separation code
                $data = substr($data, $pos + 4) . "\r\n";
            }
        } else {
            $fp = fopen($this->dataURL, "r");
            if (! $fp) {
                return new FX_Error("Could not fopen URL in RetrieveFM7Data.");
            }
            while (!feof($fp)) {
                $data .= fread($fp, 4096);
            }
            fclose($fp);
        }
        $data = str_replace($this->invalidXMLChars, '', $data);
        return $data;
    }

    function BuildSQLSorts ()
    {
        $currentOrderBy = '';

        if (count($this->sortParams) > 0) {
            $counter = 0;
            $currentOrderBy .= ' ORDER BY ';
            foreach ($this->sortParams as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $$key2 = $value2;
                }
                if ($counter > 0) {
                    $currentOrderBy .= ', ';
                }
                $currentOrderBy .= "'{$field}'";
                if (substr_count(strtolower($sortOrder), 'desc') > 0) {
                    $currentOrderBy .= ' DESC';
                }
                ++$counter;
            }
            return $currentOrderBy;
        }
    }

    function BuildSQLQuery ($action)
    {
        $currentLOP = 'AND';
        $logicalOperators = array();
        $LOPCount = 0;
        $currentQuery = '';
        $counter = 0;
        $whereClause = '';

        switch ($action) {
            case '-find':
                foreach ($this->dataParams as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $$key2 = $value2;
                    }
                    switch ($name) {
                        case '-lop':
                            $LOPCount = array_push($logicalOperators, $currentLOP);
                            $currentLOP = $value;
                            $currentSearch .= "(";
                            break;
                        case '-lop_end':
                            $currentLOP = array_pop($logicalOperators);
                            --$LOPCount;
                            $currentSearch .= ")";
                            break;
                        case '-recid':
                            if ($counter > 0) {
                                $currentSearch .= " {$currentLOP} ";
                            }
                            $currentSearch .= $this->primaryKeyField . " = '" . $value . "'";
                            ++$counter;
                            break;
                        case '-script':
                        case '-script.prefind':
                        case '-script.presort':
                            return new FX_Error("The '-script' parameter is not currently supported for SQL.");
                            break;
                        default:
                            if ($op == "") {
                                $op = $this->defaultOperator;
                            }
                            if ($counter > 0) {
                                $currentSearch .= " {$currentLOP} ";
                            }
                            switch ($op) {
                                case 'eq':
                                    $currentSearch .= $name . " = '" . $value . "'";
                                    break;
                                case 'neq':
                                    $currentSearch .= $name . " != '" . $value . "'";
                                    break;
                                case 'cn':
                                    $currentSearch .= $name . " LIKE '%" . $value . "%'";
                                    break;
                                case 'bw':
                                    $currentSearch .= $name . " LIKE '" . $value . "%'";
                                    break;
                                case 'ew':
                                    $currentSearch .= $name . " LIKE '%" . $value . "'";
                                    break;
                                case 'gt':
                                    $currentSearch .= $name . " > '" . $value . "'";
                                    break;
                                case 'gte':
                                    $currentSearch .= $name . " >= '" . $value . "'";
                                    break;
                                case 'lt':
                                    $currentSearch .= $name . " < '" . $value . "'";
                                    break;
                                case 'lte':
                                    $currentSearch .= $name . " <= '" . $value . "'";
                                    break;
                                default: // default is a 'begins with' search for historical reasons (default in FM)
                                    $currentSearch .= $name . " LIKE '" . $value . "%'";
                                    break;
                            }
                            ++$counter;
                            break;
                    }
                }
                while ($LOPCount > 0) {
                    --$LOPCount;
                    $currentSearch .= ")";
                }
                $whereClause = ' WHERE ' . $currentSearch; // set the $whereClause variable here, to distinguish this from a "finall" request
            case '-findall': //
                if ($this->selectColsSet) {
                    $currentQuery = "SELECT {$this->selectColumns} FROM {$this->layout}{$whereClause}" . $this->BuildSQLSorts();
                } else {
                    $currentQuery = "SELECT * FROM {$this->layout}{$whereClause}" . $this->BuildSQLSorts();
                }
                break;
            case '-delete':
                foreach ($this->dataParams as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $$key2 = $value2;
                    }
                    if ($name == '-recid') {
                        $currentQuery = "DELETE FROM {$this->layout} WHERE {$this->primaryKeyField} = '{$value}'";
                    }
                }
                break;
            case '-edit':
                $whereClause = '';
                $currentQuery = "UPDATE {$this->layout} SET ";
                foreach ($this->dataParams as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $$key2 = $value2;
                    }
                    if ($name == '-recid') {
                        $whereClause = " WHERE {$this->primaryKeyField} = '{$value}'";
                    } else {
                        if ($counter > 0) {
                            $currentQuery .= ", ";
                        }
                        $currentQuery .= "{$name} = '{$value}'";
                        ++$counter;
                    }
                }
                $currentQuery .= $whereClause;
                break;
            case '-new':
                $tempColList = '(';
                $tempValueList = '(';
                foreach ($this->dataParams as $key1 => $value1) {
                    foreach ($value1 as $key2 => $value2) {
                        $$key2 = $value2;
                    }
                    if ($name == '-recid') {
                        $currentQuery = "DELETE FROM {$this->layout} WHERE {$this->primaryKeyField} = '{$value}'";
                    }
                    if ($counter > 0) {
                        $tempColList .= ", ";
                        $tempValueList .= ", ";
                    }
                    $tempColList .= $name;
                    $tempValueList .= "'{$value}'";
                    ++$counter;
                }
                $tempColList .= ')';
                $tempValueList .= ')';
                $currentQuery = "INSERT INTO {$this->layout} {$tempColList} VALUES {$tempValueList}";
                break;
        }
        $currentQuery .= ';';
        return $currentQuery;
    }

    function RetrieveMySQLData ($action)
    {
        if (strlen(trim($this->dataServer)) < 1) {
            return new FX_Error('No MySQL server specified.');
        }
        if (strlen(trim($this->dataPort)) > 0) {
            $tempServer = $this->dataServer . ':' . $this->dataPort;
        } else {
            $tempServer = $this->dataServer;
        }
        $mysql_res = @mysql_connect($tempServer, $this->DBUser, $this->DBPassword); // although username and password are optional for this function, FX.php expects them to be set
        if ($mysql_res == false) {
            return new FX_Error('Unable to connect to MySQL server.');
        }
        if ($action != '-dbopen') {
            if (! mysql_select_db($this->database, $mysql_res)) {
                return new FX_Error('Unable to connect to specified MySQL database.');
            }
        }
        if (substr_count($action, '-db') == 0 && substr_count($action, 'names') == 0) {
            $theResult = mysql_query('SHOW COLUMNS FROM ' . $this->layout);
            if (! $theResult) {
                return new FX_Error('Unable to access MySQL column data: ' . mysql_error());
            }
            $counter = 0;
            $keyPrecedence = 0;
            while ($tempRow = mysql_fetch_assoc($theResult)) {
                $this->fieldInfo[$counter]['name'] = $tempRow['Field'];
                $this->fieldInfo[$counter]['type'] = $tempRow['Type'];
                $this->fieldInfo[$counter]['emptyok'] = $tempRow['Null'];
                $this->fieldInfo[$counter]['maxrepeat'] = 1;
                $this->fieldInfo[$counter]['extra'] = $tempRow['Key'] . ' ' . $tempRow['Extra'];
                if ($this->fuzzyKeyLogic) {
                    if (strlen(trim($this->primaryKeyField)) < 1 || $keyPrecedence < 3) {
                        if (substr_count($this->fieldInfo[$counter]['extra'], 'UNI ') > 0 && $keyPrecedence < 3) {
                            $this->primaryKeyField = $this->fieldInfo[$counter]['name'];
                            $keyPrecedence = 3;
                        } elseif (substr_count($this->fieldInfo[$counter]['extra'], 'auto_increment') > 0 && $keyPrecedence < 2) {
                            $this->primaryKeyField = $this->fieldInfo[$counter]['name'];
                            $keyPrecedence = 2;
                        } elseif (substr_count($this->fieldInfo[$counter]['extra'], 'PRI ') > 0 && $keyPrecedence < 1) {
                            $this->primaryKeyField = $this->fieldInfo[$counter]['name'];
                            $keyPrecedence = 1;
                        }
                    }
                }
                ++$counter;
            }
        }
        switch ($action) {
            case '-dbopen':
            case '-dbclose':
                return new FX_Error('Opening and closing MySQL databases not available.');
                break;
            case '-delete':
            case '-edit':
            case '-find':
            case '-findall':
            case '-new':
                $this->dataQuery = $this->BuildSQLQuery($action);
                if (FX::isError($this->dataQuery)) {
                    return $this->dataQuery;
                }
            case '-sqlquery': // note that there is no preceding break, as we don't want to build a query
                $theResult = mysql_query($this->dataQuery);
                if (! $theResult) {
                    return new FX_Error('Invalid query: ' . mysql_error());
                }
                if (substr_count($action, '-find') > 0 || substr_count($this->dataQuery, 'SELECT ') > 0) {
                    $this->foundCount = mysql_num_rows($theResult);
                } else {
                    $this->foundCount = mysql_affected_rows($theResult);
                }
                if ($action == '-dup' || $action == '-edit') {
                    // pull in data on relevant record
                }
                $currentKey = '';
                while ($tempRow = mysql_fetch_assoc($theResult)) {
                    foreach ($tempRow as $key => $value) {
                        if ($this->useInnerArray) {
                            $tempRow[$key] = array($value);
                        }
                        if ($key == $this->primaryKeyField) {
                            $currentKey = $value;
                        }
                    }
                    if ($this->genericKeys || $this->primaryKeyField == '') {
                        $this->currentData[] = $tempRow;
                    } else {
                        $this->currentData[$currentKey] = $tempRow;
                    }
                }
                break;
            case '-findany':
                break;
            case '-dup':
                break;
        }
        $this->fxError = 0;
        return true;
    }


    function ExecuteQuery ($action)
    {
        switch (strtolower($this->dataServerType)) {
            case 'fmpro5/6':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing FileMaker Pro data.<P>\n";
                }
                $data = $this->RetrieveFMData($action);
                if (FX::isError($data)) {
                    return $data;
                }

                $xml_parser = xml_parser_create("UTF-8");
                xml_set_object($xml_parser, $this);
                xml_set_element_handler($xml_parser, "StartElement", "EndElement");
                xml_set_character_data_handler($xml_parser, "ElementContents");
                $xmlParseResult = xml_parse($xml_parser, $data, true);
                if (! $xmlParseResult) {
                    $theMessage = sprintf("ExecuteQuery XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser));
                    xml_parser_free($xml_parser);
                    return new FX_Error($theMessage);
                }
                xml_parser_free($xml_parser);
                break;
            case 'fmpro7':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing FileMaker Pro 7 data.<P>\n";
                }
                $data = $this->RetrieveFM7Data($action);
                if (FX::isError($data)) {
                    return $data;
                }

                $xml_parser = xml_parser_create("UTF-8");
                xml_set_object($xml_parser, $this);
                xml_set_element_handler($xml_parser, "StartElement", "EndElement");
                xml_set_character_data_handler($xml_parser, "ElementContents");
                $xmlParseResult = xml_parse($xml_parser, $data, true);
                if (! $xmlParseResult) {
                    $theMessage = sprintf("ExecuteQuery XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser));
                    xml_parser_free($xml_parser);
                    return new FX_Error($theMessage);
                }
                xml_parser_free($xml_parser);
                break;
            case 'openbase':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing OpenBase data.<P>\n";
                }
                $openBaseResult = $this->RetrieveOpenBaseData($action);
                if (FX::isError($openBaseResult)) {
                    return $openBaseResult;
                }
                break;
            case 'mysql':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing MySQL data.<P>\n";
                }
                $mySQLResult = $this->RetrieveMySQLData($action);
                if (FX::isError($mySQLResult)) {
                    return $mySQLResult;
                }
                break;
            case 'postgres':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing PostgreSQL data.<P>\n";
                    if ($this->fuzzyKeyLogic) {
                        echo "<P>WARNING: Fuzzy key logic is not supported for PostgreSQL.<P>\n";
                    }
                }
                $postgreSQLResult = $this->RetrievePostgreSQLData($action);
                if (FX::isError($postgreSQLResult)) {
                    return $postgreSQLResult;
                }
                break;
            case 'odbc':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing data via ODBC.<P>\n";
                }
                $odbcResult = $this->RetrieveODBCData($action);
                if (FX::isError($odbcResult)) {
                    return $odbcResult;
                }
                break;
            case 'cafephp4pc':
                if (defined("DEBUG") and DEBUG) {
                    echo "<P>Accessing CAFEphp data.<P>\n";
                }
                $CAFEphpResult = $this->RetrieveCAFEphp4PCData($action);
                if (FX::isError($CAFEphpResult)) {
                    return $CAFEphpResult;
                }
                break;
        }
    }

    function BuildLinkQueryString ()
    {
        $tempQueryString = '';
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $paramSetCount = 0;
            $appendFlag = true;
            foreach ($_POST as $key => $value) {
                if ($appendFlag && strcasecmp($key, '-foundSetParams_begin') != 0 && strcasecmp($key, '-foundSetParams_end') != 0) {
                    $tempQueryString .= urlencode($key) . '=' . urlencode($value) . '&';
                } elseif (strcasecmp($key, '-foundSetParams_begin') == 0) {
                    $appendFlag = true;
                    if ($paramSetCount < 1) {
                        $tempQueryString = '';
                        ++$paramSetCount;
                    }
                } elseif (strcasecmp($key, '-foundSetParams_end') == 0) {
                    $appendFlag = false;
                }
            }
        } else {
            $beginTagLower = strtolower('-foundSetParams_begin');
            $endTagLower = strtolower('-foundSetParams_end');
            if (! isset($_SERVER['QUERY_STRING'])) {
                $_SERVER['QUERY_STRING'] = '';
            }
            $queryStringLower = strtolower($_SERVER['QUERY_STRING']);
            if (substr_count($queryStringLower, $beginTagLower) > 0 && substr_count($queryStringLower, $beginTagLower) == substr_count($queryStringLower, $endTagLower)) {
                $tempOffset = 0;
                for ($i = 0; $i < substr_count($queryStringLower, $beginTagLower); ++$i) {
                    $tempBeginFoundSetParams = strpos($queryStringLower, $beginTagLower, $tempOffset);
                    $tempEndFoundSetParams = strpos($queryStringLower, $endTagLower, $tempOffset) + (strlen($endTagLower) - 1);
                    $tempFoundSetParams = substr($_SERVER['QUERY_STRING'], $tempBeginFoundSetParams, ($tempEndFoundSetParams - $tempBeginFoundSetParams) + 1);
                    $tempQueryString .= preg_replace("/(?i)$beginTagLower=[^&]*&(.*)&$endTagLower/", "\$1", $tempFoundSetParams);
                    $tempOffset = $tempEndFoundSetParams;
                }
            } else {
                $tempQueryString = $_SERVER['QUERY_STRING'];
            }
            $tempQueryString = preg_replace("/skip=[\d]*[&]?/", "", $tempQueryString);
        }
        return $tempQueryString;
    }

    function AssembleDataSet ($returnData)
    {
        $dataSet = array();
        $FMNext = $this->currentSkip + $this->groupSize;
        $FMPrevious = $this->currentSkip - $this->groupSize;

        switch ($returnData) {
            case 'object':
                $dataSet = $this->currentData;

                if ($FMNext < $this->foundCount || $FMPrevious >= 0) {
                    $tempQueryString = $this->BuildLinkQueryString();
                } else {
                    $tempQueryString = '';
                }
                if ($FMNext >= $this->foundCount) {
                    $this->lastLinkNext = "";
                } else {
                    $this->lastLinkNext = $_SERVER['SCRIPT_NAME'] . "?skip=$FMNext&{$tempQueryString}";
                }
                if ($FMPrevious < 0) {
                    $this->lastLinkPrevious = "";
                } else {
                    $this->lastLinkPrevious = $_SERVER['SCRIPT_NAME'] . "?skip=$FMPrevious&{$tempQueryString}";
                }

                $this->lastFoundCount = $this->foundCount;
                $this->lastFields = $this->fieldInfo;
                $this->lastURL = $this->dataURL;
                $this->lastQuery = $this->dataQuery;
                $this->lastErrorCode = $this->fxError;
                $this->lastValueLists = $this->valueLists;
                break;
            case 'full':
                $dataSet['data'] = $this->currentData;
            case 'basic':
                if ($FMNext < $this->foundCount || $FMPrevious >= 0) {
                    $tempQueryString = $this->BuildLinkQueryString();
                } else {
                    $tempQueryString = '';
                }
                if ($FMNext >= $this->foundCount) {
                    $dataSet['linkNext'] = "";
                } else {
                    $dataSet['linkNext'] = $_SERVER['SCRIPT_NAME'] . "?skip=$FMNext&{$tempQueryString}";
                }

                if ($FMPrevious < 0) {
                    $dataSet['linkPrevious'] = "";
                } else {
                    $dataSet['linkPrevious'] = $_SERVER['SCRIPT_NAME'] . "?skip=$FMPrevious&{$tempQueryString}";
                }

                $dataSet['foundCount'] = $this->foundCount;
                $dataSet['fields'] = $this->fieldInfo;
                $dataSet['URL'] = $this->dataURL;
                $dataSet['query'] = $this->dataQuery;
                $dataSet['errorCode'] = $this->fxError;
                $dataSet['valueLists'] = $this->valueLists;
                break;
        }

        $this->ClearAllParams();
        return $dataSet;
    }

    function FMAction ($Action, $returnDataSet, $returnData, $useInnerArray)
    {
        $this->useInnerArray = $useInnerArray;
        $queryResult = $this->ExecuteQuery($Action);
        if (FX::isError($queryResult)){
            if (EMAIL_ERROR_MESSAGES) {
                EmailErrorHandler($queryResult);
            }
            return $queryResult;
        }
        if ($returnDataSet) {
            $dataSet = $this->AssembleDataSet($returnData);
            return $dataSet;
        } else {
            $this->ClearAllParams();
            return true;
        }
    }

    // The functions above (with the exception of the FX constructor) are intened to be called from other functions within FX.php (i.e. private functions).
    // The functions below are those which are intended for general use by developers (i.e. public functions).
    // Once I'm quite sure that most people are using PHP5, I'll release a version using the improved object model of PHP5.

    function isError($data) {
        return (bool)(is_object($data) &&
                      (strtolower(get_class($data)) == 'fx_error' ||
                      is_subclass_of($data, 'fx_error')));
    }

    function SetCharacterEncoding ($encoding) {         // This is the more general of the encoding functions (see notes below, and the functions documentation.)
        $this->charSet = $encoding;
        $this->dataParamsEncoding = $encoding;

        // When using a different type of encoding downstream than upstream, you must call this function -- SetCharacterEncoding() --
        // to set downstream encoding (the way data FROM the database is encoded) BEFORE calling SetDataParamsEncoding().
        // When this function is called alone, both instance valiables are set to the same value.
        // *IMPORTANT*: Using either this function or the next one is moot unless you have multi-byte support compliled into PHP (e.g. Complete PHP).
    }

    function SetDataParamsEncoding ($encoding) {        // SetDataParamsEncoding() is used to specify the encoding of parameters sent to the database (upstream encoding.)
        $this->dataParamsEncoding = $encoding;
    }

    /*function SetDBData_Old ($database, $layout="", $groupSize=50, $responseLayout="") // the layout parameter is equivalent to the table to be used in SQL queries
    {
        $this->database = ($database == 'news') ? 'news_pub' : $database;

        // switch dataserver based on database name
        $dbCheck = str_ireplace('.fp7','',$this->database);
        $webDatabaseArr = array('careers','DDR','meyerSeminar','ms_quickfly','news_pub','news_test','tradeshows','Requests','MS_milestones');
        if (in_array($dbCheck, $webDatabaseArr))
            $this->dataServer = '192.168.70.213';
        else if ($this->dataServerType == 'FMPro7')
            $this->dataServer = '192.168.50.42';

        $this->layout = $layout;
        $this->groupSize = $groupSize;
        $this->responseLayout = $responseLayout;
        $this->ClearAllParams();
    }*/

    function SetDBData ($database, $layout="", $groupSize=50, $responseLayout="") // the layout parameter is equivalent to the table to be used in SQL queries
    {
        $this->database = ($database == 'news') ? 'news_pub' : $database;

        // switch dataserver based on database name
        $dbCheck = str_ireplace(array('.fp7','.fp5'),'',$this->database);
        $webDatabaseArr = array('careers','DDR','meyerSeminar','ms_quickfly','news_pub','news_test','tradeshows','Requests','MS_milestones','mktg');
        if (in_array($dbCheck, $webDatabaseArr))
        {
            // this needs to reset
            $this->dataServer = '192.168.70.213';
            $this->dataServerType = 'FMPro7';
            $this->dataPort = "80";
            $this->dataPortSuffix = ":" . $this->dataPort;

            // special case user permissions for mktg
            if ($dbCheck == 'mktg')
                 $this->SetDBUserPass('webform','r3Quest1');
        }
        else if ($this->dataServerType == 'FMPro7')
        {
            $this->dataServer = '192.168.50.42';
        }

        $this->layout = $layout;
        $this->groupSize = $groupSize;
        $this->responseLayout = $responseLayout;
        $this->ClearAllParams();
    }

    function SetDBPassword ($DBPassword, $DBUser='FX') // Note that for historical reasons, password is the FIRST parameter for this function
    {
        if ($DBUser == '') {
            $DBUser = 'FX';
        }
        $this->DBPassword = $DBPassword;
        $this->DBUser = $DBUser;
    }

    function SetDBUserPass ($DBUser, $DBPassword='') // Same as above function, but paramters are in the opposite order
    {
        $this->SetDBPassword($DBPassword, $DBUser);
    }

    function SetDefaultOperator ($op)
    {
        $this->defaultOperator = $op;
        return true;
    }

    function AddDBParam ($name, $value, $op="")                          // Add a search parameter.  An operator is usually not necessary.
    {
        if ($this->dataParamsEncoding  != '' && defined('MB_OVERLOAD_STRING')) {
            $this->dataParams[]["name"] = mb_convert_encoding($name, $this->dataParamsEncoding, $this->charSet);
            end($this->dataParams);
            $this->dataParams[key($this->dataParams)]["value"] = mb_convert_encoding($value, $this->dataParamsEncoding, $this->charSet);
        } else {
            $this->dataParams[]["name"] = $name;
            end($this->dataParams);
            $this->dataParams[key($this->dataParams)]["value"] = $value;
        }
        $this->dataParams[key($this->dataParams)]["op"] = $op;
    }

    function AddDBParamArray ($paramsArray, $paramOperatorsArray=array())   // Add an array of search parameters.  An operator is usually not necessary.
    {
        foreach ($paramsArray as $key => $value) {
            if (isset($paramOperatorsArray[$key]) && strlen(trim($paramOperatorsArray[$key])) > 0) {
                $this->AddDBParam($key, $value, $paramOperatorsArray[$key]);
            } else {
                $this->AddDBParam($key, $value);
            }
        }
    }

    function SetPortalRow ($fieldsArray, $portalRowID=0, $relationshipName='')
    {
        foreach ($fieldsArray as $fieldName => $fieldValue) {
            if (strlen(trim($relationshipName)) > 0 && substr_count($fieldName, '::') < 1) {
                $this->AddDBParam("{$relationshipName}::{$fieldName}.{$portalRowID}", $fieldValue);
            } else {
                $this->AddDBParam("{$fieldName}.{$portalRowID}", $fieldValue);
            }
        }
    }

    function SetRecordID ($recordID)
    {
        if (! is_numeric($recordID) || (intval($recordID) != $recordID)) {
            if (defined("DEBUG") and DEBUG) {
                echo "<P>RecordIDs must be integers.  Value passed was &quot;{$recordID}&quot;.<P>\n";
            }
        }
        $this->AddDBParam('-recid', $recordID);
    }

    function SetModID ($modID)
    {
        if (! is_numeric($modID) || (intval($modID) != $modID)) {
            if (defined("DEBUG") and DEBUG) {
                echo "<P>ModIDs must be integers.  Value passed was &quot;{$modID}&quot;.<P>\n";
            }
        }
        $this->AddDBParam('-modid', $modID);
    }

    function SetLogicalOR ()
    {
        $this->AddDBParam('-lop', 'or');
    }

    // FileMaker 7 only
    function SetFMGlobal ($globalFieldName, $globalFieldValue)
    {
        $this->AddDBParam("{$globalFieldName}.global", $globalFieldValue);
    }

    function PerformFMScript ($scriptName)                              // This function is only meaningful when working with FileMaker data sources
    {
        $this->AddDBParam('-script', $scriptName);
    }

    function PerformFMScriptPrefind ($scriptName)                       // This function is only meaningful when working with FileMaker data sources
    {
        $this->AddDBParam('-script.prefind', $scriptName);
    }

    function PerformFMScriptPresort ($scriptName)                       // This function is only meaningful when working with FileMaker data sources
    {
        $this->AddDBParam('-script.presort', $scriptName);
    }

    function AddSortParam ($field, $sortOrder="", $performOrder=0)        // Add a sort parameter.  An operator is usually not necessary.
    {
        if ($performOrder > 0) {
            $this->sortParams[$performOrder]["field"] = $field;
            $this->sortParams[$performOrder]["sortOrder"] = $sortOrder;
        } else {
            if (count($this->sortParams) == 0) {
                $this->sortParams[1]["field"] = $field;
            } else {
                $this->sortParams[]["field"] = $field;
            }
            end($this->sortParams);
            $this->sortParams[key($this->sortParams)]["sortOrder"] = $sortOrder;
        }
    }

    function FMSkipRecords ($skipSize)
    {
        $this->currentSkip = $skipSize;
    }

    function FMPostQuery ($isPostQuery = true)
    {
        $this->isPostQuery = $isPostQuery;
    }

    function FMUseCURL ($useCURL = true)
    {
        $this->useCURL = $useCURL;
    }

    // By default, FX.php adds an extra layer to the returned array to allow for repeating fields and portals.
    // When these are not present, or when accessing SQL data, this may not be desirable.  FlattenInnerArray() removes this extra layer.
    function FlattenInnerArray ()
    {
        $this->useInnerArray = false;
    }

/* The actions that you can send to FileMaker start here */

    function FMDBOpen ()
    {
        $queryResult = $this->ExecuteQuery("-dbopen");
        if (FX::isError($queryResult)){
            return $queryResult;
        }
    }

    function FMDBClose ()
    {
        $queryResult = $this->ExecuteQuery("-dbclose");
        if (FX::isError($queryResult)){
            return $queryResult;
        }
    }

    function FMDelete ($returnDataSet = false, $returnData = 'basic', $useInnerArray = true)
    {
        return $this->FMAction("-delete", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMDup ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-dup", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMEdit ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
    	$this->isPostQuery = true;
    	$this->defaultPostPolicy = true;    	
        return $this->FMAction("-edit", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMFind ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-find", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMFindAll ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-findall", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMFindAny ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-findany", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMNew ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
    	$this->isPostQuery = true;
    	$this->defaultPostPolicy = true;
        return $this->FMAction("-new", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMView ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-view", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMDBNames ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-dbnames", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMLayoutNames ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-layoutnames", $returnDataSet, $returnData, $useInnerArray);
    }

    function FMScriptNames ($returnDataSet = true, $returnData = 'full', $useInnerArray = true)
    {
        return $this->FMAction("-scriptnames", $returnDataSet, $returnData, $useInnerArray);
    }

    // DoFXAction() is a general purpose action function designed to streamline FX.php code
    function DoFXAction ($currentAction, $returnDataSet = true, $useInnerArray = false, $returnType = 'object')
    {
        return $this->FMAction($currentAction, $returnDataSet, $returnType, $useInnerArray);
    }

/* The actions that you can send to FileMaker end here */
    // PerformSQLQuery() is akin to the FileMaker actions above with two differences:
    //  1) It is SQL specific
    //  2) The SQL query passed is the sole determinant of the query performed (AddDBParam, etc. will be ignored)
    function PerformSQLQuery ($SQLQuery, $returnDataSet = true, $useInnerArray = false, $returnData = 'object')
    {
        $this->dataQuery = $SQLQuery;
        return $this->FMAction("-sqlquery", $returnDataSet, $returnData, $useInnerArray);
    }

    // SetDataKey() is used for SQL queries as a way to provide parity with the RecordID/ModID combo provided by FileMaker Pro
    function SetDataKey ($keyField, $modifyField = '', $separator = '.')
    {
        $this->primaryKeyField = $keyField;
        $this->modifyDateField = $modifyField;
        $this->dataKeySeparator = $separator;
        return true;
    }

    // SetSelectColumns() allows users to specify which columns should be returned by an SQL SELECT statement
    function SetSelectColumns ($columnList)
    {
        $this->selectColsSet = true;
        $this->selectColumns = $columnList;
        return true;
    }

    // SQLFuzzyKeyLogicOn() can be used to have FX.php make it's best guess as to a viable key in an SQL DB
    function SQLFuzzyKeyLogicOn ($logicSwitch = false)
    {
        $this->fuzzyKeyLogic = $logicSwitch;
        return true;
    }

    // By default, FX.php uses records' keys as the indices for the returned array.  UseGenericKeys() is used to change this behavior.
    function UseGenericKeys ($genericKeys=true)
    {
        $this->genericKeys = $genericKeys;
        return true;
    }

}
?>