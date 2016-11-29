<html>

<body>
<script>


setTimeout(funcClose, 5 * 1000);
alert("start");

function funcClose() {
  alert("close window");
  setTimeout(window.close, 500);

}

</script>




<?PHP
/*
//= ********** Changes and Updates **********
//= Changes
//= 20160821Su : Folder Creation name includes Day of Week appended
//= 20160821Su : Added Finished ALL Task Success for Log MSG
//= 20161128M  : Submitted to Github...

*/


$userAgentPC = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
$userAgentMAC = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36 OPR/38.0.2220.31';

$userAgentPCLatest = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:49.0) Gecko/20100101 Firefox/49.0';
$userAgentMACLatest = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:49.0) Gecko/20100101 Firefox/49.0';





/// Override for Retrieving in ALL Days including Weekend days.
$bolAccessOverride = true;
if (isset($_REQUEST['override'])) {
  $bolAccessOverride = true;
  echo "override set<br />";
}



$fileWebList = "weblist.dat";
$fileError = "logerror.dat";
$fileLog = "log.dat";

$fileWebList2 = "weblist2.dat";

date_default_timezone_set("America/New_York");

$date_today = date( "Ymd" );
//$dow = date( "w", $timestamp );
$dow = date( "w" );

echo "Start <br />";

if ($bolAccessOverride || ($dow != 0 && $dow !=6) ) {
     
    $strLogMSG = "Weekday: $dow"." ACCESS ATTEMPTED";

    echo "Weekday: $dow"." ACCESS ATTEMPTED";

    file_put_contents($fileLog, $date_today.": ".$strLogMSG."\n", FILE_APPEND );
    
} else if ($dow == 0 || $dow >= 6) {

    echo "Weekend Day, Not Accessed and No Override Specified (?override=1) <br />";

    $strLogMSG = "Weekend Day: $dow"." NOT ACCESSED\n";

    file_put_contents($fileLog, $date_today.": ".$strLogMSG."\n", FILE_APPEND );

    die();
} 

///// create folder
$strDirName = $date_today."_".$dow;

$bolDir = mkdir($strDirName, 0777);

if (!$bolDir) {

    $strErrorMSG = "ERROR: Error Creating Directory: $date_today\n";
    file_put_contents($fileError, $date_today.": ".$strErrorMSG, FILE_APPEND);

    //die();
    

    $strDirName = "$strDirName-";
    $bolDir = mkdir($strDirName, 0777);


    if (!$bolDir) {
      $strDirName = "$strDirName-";
      $bolDir = mkdir($strDirName, 0777);
    }

    

}

$lines = file($fileWebList);
$intCnt = 1;
//echo $fileWebList2;

foreach($lines as $line_num => $line) {
    print ( "Processing $line_num /".sizeof($lines) );
    $arrayWL = explode(";;", $line);

    ///$strHTML = file_get_contents($arrayWL[0]);

    try { 

      $strHTML = curl_get_contents($arrayWL[0]);
      //echo $arrayWL[0];
      //echo $strHTML."<br /><br /><br />";

    } catch (Exception $e) {

      $strErrorMSG = "ERROR: Error Retrieving $arrayML[0] : $date_today\n";

      file_put_contents($fileError, $date_today.": ".$strErrorMSG, FILE_APPEND);

    }


    if ($strHTML != "") {


      try {

        $strFile = $arrayWL[1];

        file_put_contents($strDirName.'/'.$strFile, $strHTML, FILE_APPEND | LOCK_EX);

      } catch (Exception $ex) {
        
        $strErrorMSG = "ERROR: Saving File $arrayML[0] : $date_today\n";

        file_put_contents($fileError, $date_today.": ".$strErrorMSG, FILE_APPEND);

      }




      /*
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        
        file_put_contents(dirname(__FILE__)."/".$bolDir."/".$strFile, $strHTML, FILE_APPEND | LOCK_EX);

      } else {

        file_put_contents(dirname(__FILE__)."/".$bolDir."/".$strFile, $strHTML, FILE_APPEND | LOCK_EX);

      } */

    }
}

echo "Finished All Task<br />";

$strLogMSGSuccess = "Finished All Task... Retrieved and Saved: Success"; 
file_put_contents($fileLog, $date_today.": ".$strLogMSGSuccess."\n", FILE_APPEND );

function curl_get_contents($url)
{
    global $userAgentMAC, $userAgentMACLatest, $userAgentPC, $userAgentPCLatest;
    global $strDirName;
    $userAgent0 = "";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  if (strpos($strDirName, "--") !== false ) {
    $userAgent0 = $userAgentMACLatest;
  } else {
    $userAgent0 = $userAgentPCLatest;
  }

  curl_setopt($ch, CURLOPT_USERAGENT, $userAgent0); //$userAgentMAC);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//$html = curl_exec($ch);
  $data = curl_exec($ch);
  curl_close($ch); 
  return $data;
}


//http://stackoverflow.com/questions/4372710/php-curl-https
function cur_get_web_page_options($url)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}


function file_get_html($url, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
{
    $dom = new simple_html_dom;
  $args = func_get_args();
  $dom->load(call_user_func_array('curl_get_contents', $args), true);
  return $dom;
    //$dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);

}


?>





</body>

</html>
