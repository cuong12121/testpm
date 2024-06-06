<?php
function printr($data){
	echo "<pre>";
	print_r($data);
	die;
}

function dd($data){
	echo "<pre>";
	print_r($data);
	die;

}

function check_empty_date($date){
	if(!empty($date) && @$date != "0000-00-00 00:00:00" && @$date != "0000-00-00" && @$date != ""){
		return date('d-m-Y',strtotime(@$date));
	}else{
		return '';
	}
}

function check_webp(){
	$user_agent = $_SERVER['HTTP_USER_AGENT']; 
	if (stripos( $user_agent, 'Chrome') !== false){
		return 1;
	}elseif (stripos( $user_agent, 'Safari') !== false || stripos( $user_agent, 'iPhone') !== false){
		return 0;
	}else {
		return 1;
	}
}

function clean_ascii_characters($string) {
    $string = str_replace(array('-', '–'), '-', $string);
    $string = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);  
    return $string;
}


function add_content_webp($description){
	if(!$description) return '';
	$arr_data = preg_split('/(<img[^>]+\>)/i', html_entity_decode($description), -1, PREG_SPLIT_DELIM_CAPTURE);
	foreach ($arr_data as $idata) {
		if (strpos($idata, '<img') !== false && strpos($idata, '.gif') === false) {
			preg_match( '@src="([^"]+)"@' , $idata, $match );
			$src = array_pop($match);
			$src = substr($src, 1);
			$arr_src = explode("/", $src);
			$imgname = $arr_src[count($arr_src)-1];
			$srcset = str_replace($imgname, 'compress/'.$imgname.'.webp', $src);
			if (check_webp()) {
				$idata_new = str_replace('<img','<img srcset='.$srcset, $idata);
			} else {
				$idata_new = str_replace('<img','<img srcset='.$srcset, $idata);
			}
			$description = str_replace($idata, $idata_new, $description);
		}
	}
	return $description;
}

function randomkey($str,$keyword = 0){
	$return = '';
	$strreturn = explode(" ",trim($str));
	$i=0;
	$listid = '';
	while($i<count($strreturn)){
		$id = rand(0,count($strreturn));
		if(strpos($listid,'[' . $id . ']')===false){
			if(isset($strreturn[$id])){
				$return .= $strreturn[$id] . ' ';
				$i++;
				if($keyword == 1 && ($i%2)==0 && $i<count($strreturn))  $return .= ',';
				$listid .= '[' . $id . ']';
			}
		}
	}
	return $return;
}
function addRelate($table,$feild_id,$field_relate,$record_id,$arrayRelate=array()){
	$db_delete = new db_execute("DELETE FROM " . $table . " WHERE " . $feild_id . "=" . $record_id);
	unset($db_delete);
	foreach($arrayRelate as $key=>$value){
		$db_relate_execute = new db_execute("INSERT INTO " . $table . "(" . $feild_id . "," . $field_relate . ") VALUES(" . $record_id . ", " . intval($value) . ")");
		unset($db_relate_execute);
	}
}
function array_language(){
	return array(1=>"vn"
		,2=>"en");
}
function formatNumber($value){
	return number_format($value,0,"",".");
}
function random(){
	$rand_value = "";
	$rand_value.=rand(1000,9999);
	$rand_value.=chr(rand(65,90));
	$rand_value.=rand(1000,9999);
	$rand_value.=chr(rand(97,122));
	$rand_value.=rand(1000,9999);
	$rand_value.=chr(rand(97,122));
	$rand_value.=rand(1000,9999);
	return $rand_value;
}
function str_encode($encodeStr="")
{
	$returnStr = "";
	if($encodeStr == '') return $encodeStr;
	if(!empty($encodeStr)) {
		$enc = base64_encode($encodeStr);
		$enc = str_replace('=','',$enc);
		$enc = str_rot13($enc);
		$returnStr = $enc;
	}
	return $returnStr;
}

function str_decode($encodedStr="",$type=0)
{
	$returnStr = "";
	$encodedStr = str_replace(" ","+",$encodedStr);
	if(!empty($encodedStr)) {
		$dec = str_rot13($encodedStr);
		$dec = base64_decode($dec);
		$returnStr = $dec;
	}
	return $returnStr;
}
function getURLR($mod_rewrite = 1,$serverName=0, $scriptName=0, $fileName=1, $queryString=1, $varDenied=''){
	if($mod_rewrite==1){
		return $_SERVER['REQUEST_URI'];
	}else{
		return getURL($serverName, $scriptName, $fileName, $queryString, $varDenied);
	}
}
//hàm get URL
function getURL($serverName=0, $scriptName=0, $fileName=1, $queryString=1, $varDenied=''){
	$url	 = '';
	$slash = '/';
	if($scriptName != 0)$slash	= "";
	if($serverName != 0){
		if(isset($_SERVER['SERVER_NAME'])){
			$url .= 'http://' . $_SERVER['SERVER_NAME'];
			if(isset($_SERVER['SERVER_PORT'])) $url .= ":" . $_SERVER['SERVER_PORT'];
			$url .= $slash;
		}
	}
	if($scriptName != 0){
		if(isset($_SERVER['SCRIPT_NAME']))	$url .= substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
	}
	if($fileName	!= 0){
		if(isset($_SERVER['SCRIPT_NAME']))	$url .= substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
	}
	if($queryString!= 0){
		$dau = 0;
		$url .= '?';
		reset($_GET);
		if($varDenied != ''){
			$arrVarDenied = explode('|', $varDenied);
			while(list($k, $v) = each($_GET)){
				if(array_search($k, $arrVarDenied) === false){
					$url .= (($dau==0) ? '' : '&') . $k . '=' . urlencode($v);
					$dau  = 1;
				}
			}
		}
		else{
			while(list($k, $v) = each($_GET)) $url .= '&' . $k . '=' . urlencode($v);
		}
	}
	$url = str_replace('"', '&quot;', strval($url));
	return $url;
}
//hàm t?o link khi c?n thi?t chuy?n sang mod rewrite
function createLink($type="detail",$url=array(),$path="",$con_extenstion='html',$rewrite = 1){
	global $lang_path;
	$menuReturn = '';
	$keyReplace = '_';
	//neu ko de mod rewrite
	if($rewrite == 0){
		$menuReturn = $path . $type . ".php?";
		foreach($url as $key=>$value){
			if($key == "module") $value = strtolower($value);
			if($key != "title"){
				$menuReturn .= $key . "=" . $value . "&";	
			}
		}
		$menuReturn = substr($menuReturn,0, strrpos($menuReturn, "&"));
		//tra ve url ko rewrite
		return $menuReturn;
	}
	$module = "d";
	switch(strtolower($url["module"])){
		case "news":
		case "km":
		$module = 'n';
		break;
		case "static":
		$module = 's';
		break;
		case "phukien":
		$module = 'p';
		break;
		
	}
	//tao luat cho mod rewrite
	switch($type){
		
		case "detail":
		if(strtolower($url["module"])=="product"){
			$menuReturn = "/" . (isset($url["supname"]) ? $url["supname"] . "/" : "") .  removeTitle($url["title"],$keyReplace) . '-c' . $url["iCat"] . 'd' . $url["iData"] . (isset($url["tab"]) ? 't' . $url["tab"]: '') . '.html';
		}else{
			$menuReturn = "/" .  removeTitle($url["title"],$keyReplace) . '-' . $module . $url["iCat"] . '-' . $url["iData"] . '.html';
		}
		break;
		case "type":
		$menuReturn = '/' .  removeTitle($url["title"],$keyReplace) . '/' . $url["iCat"] . '/';
		if(isset($url["iSup"])) $menuReturn = $lang_path .  removeTitle($url["title"],$keyReplace) . $keyReplace  . strtolower($url["module"]) . $keyReplace . $url["iCat"] . $keyReplace . 'hsx_' . $url["iSup"] . '.' . $con_extenstion;
		if(isset($url["iPri"])) $menuReturn = $lang_path .  removeTitle($url["title"],$keyReplace) . $keyReplace  . strtolower($url["module"]) . $keyReplace . $url["iCat"] . $keyReplace . 'gia_' . $url["iPri"] . '.' . $con_extenstion;
		break;
		case "sup":
		$menuReturn = "/" . removeTitle($url["title"],$keyReplace) . '/';
		break;
	}
	return $menuReturn;
}
function removethuoctinh($value){
	$value = str_replace("|","",$value);
	$value = str_replace(";","",$value);
	return $value;
}
function getKeyword($value){
	$value = str_replace("\'","'",$value);
	$value = str_replace("'","''",$value);
	$value = str_replace(" ","%",$value);
	return $value;
}
//hàm getvalue : 1 tên bi?n; 2 ki?u; 3 phuong th?c; 4 giá tr? m?c d?nh; 5 remove quote
//function getValue($value_name, $data_type = "int", $method = "GET", $default_value = 0, $advance = 0){
//	$value = $default_value;
//	switch($method){
//		case "GET": if(isset($_GET[$value_name])) $value = $_GET[$value_name]; break;
//		case "POST": if(isset($_POST[$value_name])) $value = $_POST[$value_name]; break;
//		case "COOKIE": if(isset($_COOKIE[$value_name])) $value = $_COOKIE[$value_name]; break;
//		case "SESSION": if(isset($_SESSION[$value_name])) $value = $_SESSION[$value_name]; break;
//		case "POSTGET":
//			if(isset($_POST[$value_name])){
//				 $value = $_POST[$value_name]; 
//			}elseif(isset($_GET[$value_name])){
//				$value = $_GET[$value_name];
//			}
//		break;
//		default: if(isset($_GET[$value_name])) $value = $_GET[$value_name]; break;
//	}
//	$valueArray	= array("int" => intval($value), "str" => trim(strval($value)), "flo" => floatval($value), "dbl" => doubleval($value), "arr" => $value);
//	foreach($valueArray as $key => $returnValue){
//		if($data_type == $key){
//			if($advance != 0){
//				switch($advance){
//					case 1:
//						$returnValue = str_replace('"', '&quot;', $returnValue);
//						$returnValue = str_replace("\'", "'", $returnValue);
//						$returnValue = str_replace("'", "''", $returnValue);
//						break;
//					case 2:
//						$returnValue = htmlspecialbo($returnValue);
//						break;
//				}
//			}
//			//Do s? quá l?n nên ph?i ki?m tra tru?c khi tr? v? giá tr?
//			if((strval($returnValue) == "INF") && ($data_type != "str")) return 0;
//			return $returnValue;
//			break;
//		}
//	}
//	return (intval($value));
//}



/*
*	type: msg, error, alert.
*/

function setRedirect($url,$msg='',$type='',$code = 301)
{
	
	if($msg)
	{
		switch ($type)
		{
			case'error':
			if(!isset($_SESSION['msg_error']))
				$msg_error = array();
			else
				$msg_error = $_SESSION['msg_error'];

			$msg_error[] = $msg;
			$_SESSION['msg_error'] = $msg_error;	
			break;
			case'alert':
			if(!isset($_SESSION['msg_alert']))
				$msg_alert = array();
			else
				$msg_alert = $_SESSION['msg_alert'];

			$msg_alert[] = $msg;
			$_SESSION['msg_alert'] = $msg_alert;	
			break;
			case'':
			default:

			if(!isset($_SESSION['msg_suc']))
				$msg_suc = array();
			else
				$msg_suc = $_SESSION['msg_suc'];

			$msg_suc[] = $msg;
			$_SESSION['msg_suc'] = $msg_suc;	
			break;
		}
		$_SESSION['have_redirect'] = 1;
	}
	
//	if (headers_sent()) {
//		echo "<script>document.location.href='$url';</script>\n";
//	} else {
		//@ob_end_clean(); // clear output buffer
	session_write_close();
	if($code == 404){
		header('HTTP/1.0 404 Not Found');
	}
	//	header( 'HTTP/1.1 301 Moved Permanently' );
	ob_start();
	header("Location:".$url, true, $code);
	ob_end_flush();
//	}
	exit();
}




//
function replaceMQ($text){
	$text	= str_replace("\'", "'", $text);
	$text	= str_replace("'", "''", $text);
	$text	= str_replace(" ", "-", $text);
	return $text;
}
// remove sign
// remove multi space 
// lowertocase

//function RemoveSign($str)
//{
//function removeTitle($string,$keyReplace){
//	$string	=	RemoveSign($string);
//	//neu muon de co dau
//	//$string 	=  trim(preg_replace("/[^A-Za-z0-9àá??ãâ?????a?????èé???ê?????ìí??iòó??õô?????o?????ùú??uu??????ý???dÀÁ??ÃÂ?????A?????ÈÉ???Ê?????ÌÍ??IÒÓ??ÕÔ?????O?????ÙÚ??UU??????Ý???]/i"," ",$string));
//	
//	$string 	=  trim(preg_replace("/[^A-Za-z0-9]/i"," ",$string)); // khong dau
//	$string 	=  str_replace(" ","-",$string);
//	$string	=	str_replace("--","-",$string);
//	$string	=	str_replace("--","-",$string);
//	$string	=	str_replace("--","-",$string);
//	$string	=	strtolower($string);
//	$string	=	str_replace($keyReplace,"-",$string);
//	return $string;
//}
//function generate_sort($type, $sort, $current_sort, $image_path){
//	if($type == "asc"){
//		$title = "Tang d?n";
//		if($sort != $current_sort) $image_sort = "sortasc.gif";
//		else $image_sort = "sortasc_selected.gif";
//	}
//	else{
//		$title = "Gi?m d?n";
//		if($sort != $current_sort) $image_sort = "sortdesc.gif";
//		else $image_sort = "sortdesc_selected.gif";
//	}
//	return '<a title="' . $title . '" href="' . getURL(0,0,1,1,"sort") . '&sort=' . $sort . '"><img border="0" vspace="2" src="' . $image_path . $image_sort . '" /></a>';
//}
function getKeyRef($query,$keyname="q"){
	$strreturn = '';
	preg_match("#" . $keyname . "=(.*)#si",$query,$match);
	if(isset($match[1])) $strreturn = preg_replace('#\&(.*)#si',"",$match[1]);
	return urldecode($strreturn);
}
function int_to_words($x)
{
	$nwords = array(    "không", "m?t", "hai", "ba", "b?n", "nam", "sáu", "b?y",
		"tám", "chín", "mu?i", "mu?i m?t", "mu?i hai", "mu?i ba",
		"mu?i b?n", "mu?i lam", "mu?i sáu", "mu?i b?y", "mu?i tám",
		"mu?i chín", "hai muoi", 30 => "ba muoi", 40 => "b?n muoi",
		50 => "nam muoi", 60 => "sáu muoi", 70 => "b?y muoi", 80 => "tám muoi",
		90 => "chín muoi" );
	if(!is_numeric($x))
	{
		$w = '#';
	}else if(fmod($x, 1) != 0)
	{
		$w = '#';
	}else{
		if($x < 0)
		{
			$w = 'minus ';
			$x = -$x;
		}else{
			$w = '';
		}
		if($x < 21)
		{
			$w .= $nwords[$x];
		}else if($x < 100)
		{
			$w .= $nwords[10 * floor($x/10)];
			$r = fmod($x, 10);
			if($r > 0)
			{
				$w .= ' '. $nwords[$r];
			}
		} else if($x < 1000)
		{
			$w .= $nwords[floor($x/100)] .' tram';
			$r = fmod($x, 100);
			if($r > 0)
			{
				$w .= '  '. int_to_words($r);
			}
		} else if($x < 1000000)
		{
			$w .= int_to_words(floor($x/1000)) .' ngàn';
			$r = fmod($x, 1000);
			if($r > 0)
			{
				$w .= ' ';
				if($r < 100)
				{
					$w .= ' ';
				}
				$w .= int_to_words($r);
			}
		} else {
			$w .= int_to_words(floor($x/1000000)) .' tri?u';
			$r = fmod($x, 1000000);
			if($r > 0)
			{
				$w .= ' ';
				if($r < 100)
				{
					$word .= ' ';
				}
				$w .= int_to_words($r);
			}
		}
	}
	return $w . '';
}

function fsdate($date='',$type = 0 )
{
 	// format 'D, d/m/Y, H:i '
	if($date)
	{
		$Day = date('D',strtotime($date));
		$str_date =  date('d/m/Y, H:i',strtotime($date));
	}
	else
	{
		$Day = date('D');
		$str_date =  date('d/m/Y, H:i');
	}
	$Day = strtoupper($Day);
	$str = "";
	//TUE WED THU FRI SAT SUN MON TUE WED THU FRI SAT SUN MON JAN FEB
	switch ($Day) {
		case 'MON':
		$str .= "Th&#7913; 2";	
		break;
		case 'TUE':
		$str .= "Th&#7913; 3";	
		break;
		case 'WED':
		$str .= "Th&#7913; 4";	
		break;
		case 'THU':
		$str .= "Th&#7913; 5";	
		break;
		case 'FRI':
		$str .= "Th&#7913; 6";	
		break;
		case 'SAT':
		$str .= "Th&#7913; 7";	
		break;
		case 'SUN':
		$str .= "Ch&#7911; nh&#7853;t ";	
		break;
	}

	if($type == 1){
		$str .= ", ng&#224;y ".$str_date;
	} else {
		$str .= ", ".$str_date;
		$str .= " GMT+7";
	}

	return $str;
}
function show_datetime($date='')
{
	 	// format 'D, d/m/Y, H:i '
	if($date)
	{
		$Day = date('D',strtotime($date));
		$str_date =  date('d/m/Y, H:i A',strtotime($date));
	}
	else
	{
		$Day = date('D');
		$str_date =  date('d/m/Y, H:i A');
	}
	$Day = strtoupper($Day);
	$str = "";
		//TUE WED THU FRI SAT SUN MON TUE WED THU FRI SAT SUN MON JAN FEB
	switch ($Day) {
		case 'MON':
		$str .= "Th&#7913; 2";	
		break;
		case 'TUE':
		$str .= "Th&#7913; 3";	
		break;
		case 'WED':
		$str .= "Th&#7913; 4";	
		break;
		case 'THU':
		$str .= "Th&#7913; 5";	
		break;
		case 'FRI':
		$str .= "Th&#7913; 6";	
		break;
		case 'SAT':
		$str .= "Th&#7913; 7";	
		break;
		case 'SUN':
		$str .= "Ch&#7911; nh&#7853;t ";	
		break;
	}
	$str .= ", ".$str_date;
	return $str;
}


/******* CUT STRING BY LENGTH ********/
function stringStandart($str)
{
	$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);

	$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	$str = preg_replace("/(đ)/", 'd', $str);

	$str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	$str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	$str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	$str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	$str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	$str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	$str = preg_replace("/(Đ)/", 'D', $str);
	$arr=array("&ldquo;","&rdquo;","&lsquo;","&rsquo;","&quot;","'");
	$str=str_replace($arr, "", $str);
	$arr=array(".","!","~","@","#","$","%","^","&","*","(",")","=","+","|","\\","/","?",",","'","\"");
	$str=str_replace($arr, "-", $str);

	$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
	$str = preg_replace('/\s\s+/', ' ', $str);
	$str = str_replace(' ','-',$str);
	$str = strtolower ( $str);
	return $str;
}

function count_words($str) {
	$words = 0;

	$str =  preg_replace("/ +/i", " ", $str);
	$array = explode(" ", $str);
	for($i=0;$i < count($array);$i++){

		if (preg_match("/[0-9A-Za-zÀ-ÖØ-öø-ÿ]/i", $array[$i]))

			$words++;
	}
	return $words;
}
	  //End function
function implodeWord($str,$noWord){

	$str = preg_replace("/ +/i", " ", $str);
	$array = explode(" ", $str);

	
	for($i=0;$i<$noWord;$i++){       
		if (preg_match("/[0-9A-Za-zÀ-ÖØ-öø-ÿ]/i", $array[$i])) $aryContent[] = $array[$i];

	}
	$strContent = implode(" ",$aryContent);
	return $strContent;
}
function getWord($noWord,$str){

	$noCountWord = count_words(strip_tags($str));
	if($noCountWord >= $noWord){
		$content = implodeWord(strip_tags($str),$noWord).'...';
	} else {
		$content = strip_tags($str);
	}

	$k = chr(92); 
	$content = str_replace($k,"",$content);

	return $content;
}
function cat_character_by_length($maxleng = 100,$str,$suppend = '...'){

	$str_leng = mb_strlen(strip_tags($str));
	if($str_leng > $maxleng){
		$content = substr($str,0,$maxleng).$suppend;
	} else {
		$content = strip_tags($str);
	}

	return $content;
}
function get_word_by_length($maxleng = 100,$str,$suppend = '...'){
	$str =  preg_replace("/ +/i", " ", $str);

	$i = $maxleng;
	if(mb_strlen($str) <= $maxleng)
		return $str;

	while(true){
		$character_current = @mb_substr($str,($i),1,'utf-8');

		if(empty($character_current) || $character_current == ' ' || $character_current == ',' || $character_current == '|')
			return mb_substr($str, 0,$i,'utf-8').$suppend;					
		$i --;
	}
	return 	$str.$suppend;
}
function format_money($price,$current = '₫',$text_if_rezo = '0')
{
	// echo $price;die;

	$price2 = (float)$price;
	if(!$price || !$price2)
		return $text_if_rezo;	
	// return number_format($price, 3, ',', '.').$current.'';


	$pos = strpos($price, '.');
	if ($pos == false) {
	    return number_format($price, 0, ',', '.').$current.'';
	}else{
		 return number_format($price,2, ',', '.').$current.'';
	}
}

function format_money2($price,$current = '₫',$text_if_rezo = '0')
{
	$price2 = (int)$price;
	if(!$price || !$price2)
		return $text_if_rezo;	
	return number_format($price, 0, ',', '.').$current.'';
}

function get_price_eps($price)
{
	if(!$price)
		$price =0;
	$rate_vnd_eps = 100000.0;
	$price = number_format($price/$rate_vnd_eps, 0, ',', '.');
	return $price;
}
function get_price_usd($price)
{
	$rate_vnd_usd = 1950000;
	return  number_format($price/$rate_vnd_usd, 0, ',', '.');
}

	  /*
	   * return price VND
	   */
	  function money_transform_to_vnd($price, $currency) {

	  	if(!$currency || strtoupper($currency) == 'VND') {
	  		return $price;
	  	}
	  	if($currency == "USD") {
	  		$rate_vnd_usd = 1950000;
	  		return  $price*$rate_vnd_usd;
	  	}
	  }
	  /*
	   * Lọc chỉ lấy số
	   */
	  function filter_int_from_string($str) {
	  	return filter_var($str, FILTER_SANITIZE_NUMBER_INT);		
	  }
	  /*
	   * Input: date 
	   * Output: 12h30 ngày 2-3-2011
	   */
	  function format_date($str_time){
	  	$time = strtotime($str_time);
	  	$hour = date('H',$time);
	  	$minute = date('i',$time);
	  	$date = date('d-m-Y',$time);
	  	return $hour.'h'.$minute.' ngày '.$date;
	  }
	  /******* end CUT STRING BY LENGTH ********/
	  
	  function calculator_price($price, $price_old,$is_promotion = '',$date_start = '',$date_end = '',$promotion_info='',$prices_by_regions = null) {
	  	if(isset($_COOKIE['city_id'])){
	  		$city_id = isset($_COOKIE['city_id'])?$_COOKIE['city_id']:'9999';	
	  		if($price){
	  			$price_region = isset($prices_by_regions[$city_id])?$prices_by_regions[$city_id] -> price:0;					
	  			$price = $price + $price_region;

	  		}
	  	}
	  	$result = array();
	  	if($is_promotion){
	  		if( $date_start <  date('Y-m-d H:i:s') && $date_end >  date('Y-m-d H:i:s') ){
	  			$result['price']  = $price;
	  			$result['price_old'] = $price_old;

	  		}else{
	  			$result['price'] = $price;
	  			$result['price_old'] =$price_old;
	  		}
	  	}else{
	  		$result['price']= $price;
	  		$result['price_old']  = $price_old;
	  	}

	  	return $result;
	  }
	  /******* end CUT STRING BY LENGTH ********/
	  function image_to_bytes($img_source,$method_resize='resized_not_crop',$new_width = 1, $new_height = 1,$quality=100) {
        // Begin capturing the byte stream
	  	ob_start();
	  	$img_source = str_replace(' ','%20', $img_source);

	  	list($old_width,$old_height) = getimagesize($img_source);
	  	if($method_resize == 'resized_not_crop'){
	  		if(!$new_width){
	  			$new_width  = $old_width * $new_height/ $old_height ;
	  		}
	  		if(!$new_height){
	  			$new_height = $old_height * $new_width /$old_width  ;
	  		}

	  		$file_ext =strtolower(substr($img_source, (strripos($img_source, '.')+1),strlen($img_source))); 

	  		$cropped_tmp = @imagecreatetruecolor($new_width,$new_height)
	  		or die("Cannot Initialize new GD image stream when cropped");

			 // transparent
	  		imagealphablending($cropped_tmp, false);
	  		imagesavealpha($cropped_tmp,true);
		        $transparent = imagecolorallocatealpha($cropped_tmp, 255, 255, 255, 127);//
		        imagefilledrectangle($cropped_tmp, 0, 0, $new_width, $new_height, $transparent);
	    	// end transparent
		        if($file_ext == "png"){
		        	$source = imagecreatefrompng($img_source);
		        }elseif($file_ext == "jpg" || $file_ext == "jpeg"){
		        	$source = imagecreatefromjpeg($img_source);
		        }elseif($file_ext == "gif"){
		        	$source = imagecreatefromgif($img_source);
		        } 

		        imagecopyresampled($cropped_tmp,$source,0,0,0,0,$new_width,$new_height, $old_width,$old_height);


		        if($file_ext == "png"){
		        	$img  =  imagepng($cropped_tmp,NULL,0); 
		        }elseif($file_ext == "jpg" || $file_ext == "jpeg"){
		        	$img  =  imagejpeg($cropped_tmp,NULL,90);
		        }elseif($file_ext == "gif"){
		        	$img  =  imagegif($cropped_tmp,NULL,0);
		        } 

		    }else if($method_resize == 'resize_image'){
		    	$crop_width = $new_width;
		    	$crop_height = $new_height ;
		    	if(!$crop_width && !$crop_height){
		    		$crop_width = $old_width;
		    		$crop_height = $old_height;
		    	} else if(!$crop_width){
		    		$crop_width = 	$crop_height * $old_width / $old_height;
		    	} else if(!$crop_height){
		    		$crop_height = 	$crop_width *  $old_height/$old_width;
		    	}
		    	if(($crop_width/$crop_height)>($old_width/$old_height))
		    	{
		    		$real_height = $crop_height;
		    		$real_width = $real_height*$old_width/$old_height;
		    	}
		    	else
		    	{
		    		$real_width = $crop_width;
		    		$real_height = (($real_width*$old_height)/$old_width);			
		    	}

		    	$file_ext =strtolower(substr($img_source, (strripos($img_source, '.')+1),strlen($img_source))); 

			// new
		    	$newpic = imagecreatetruecolor(round($real_width), round($real_height));

			// transparent
		    	imagealphablending($newpic, false);
		    	imagesavealpha($newpic,true);
	       	$transparent = imagecolorallocatealpha($newpic, 255, 255, 255, 127);//
	       	imagefilledrectangle($newpic, 0, 0, $real_width, $real_height, $transparent);


	       	if($file_ext == "png"){
	       		$source = imagecreatefrompng($img_source);
	       	}elseif($file_ext == "jpg" || $file_ext == "jpeg"){
	       		$source = imagecreatefromjpeg($img_source);
	       	}elseif($file_ext == "gif"){
	       		$source = imagecreatefromgif($img_source);
	       	} 

	       	if(!imagecopyresampled($newpic, $source, 0, 0, 0, 0, $real_width, $real_height, $old_width, $old_height))
	       	{
	       		return false;
	       	}
		  	// create frame 
	       	$final = imagecreatetruecolor($crop_width, $crop_height);
	        // transparent
	        imagealphablending($final, false);//
	        imagesavealpha($final,true);//
	        $transparent = imagecolorallocatealpha($final, 255, 255, 255, 127);//
	        imagefill($final, 0, 0, $transparent);
	    	// end transparent
	        
	        imagecopy($final, $newpic, (abs($crop_width - $real_width)/ 2), (abs($crop_height - $real_height) / 2), 0, 0, $real_width, $real_height);


	        if($file_ext == "png"){
	        	$img  =  imagepng($final,NULL,0); 
	        }elseif($file_ext == "jpg" || $file_ext == "jpeg"){
	        	$img  =  imagejpeg($final,NULL,100);
	        }elseif($file_ext == "gif"){
	        	$img  =  imagegif($final,NULL,0);
	        } 
		    // end transparent
	    }else if($method_resize == 'cut_image'){
	    	$crop_width = $new_width;
	    	$crop_height = $new_height ;

	    	if(($crop_width/$crop_height)>($old_width/$old_height))
	    	{
	    		$real_width = $crop_width;
	    		$real_height = (($real_width*$old_height)/$old_width);	
	    		$x_crop = 0;
	    		$y_crop = 0;
	    	}
	    	else
	    	{
	    		$real_height = $crop_height;
	    		$real_width = $real_height*$old_width/$old_height;
	    		$x_crop = ((abs($real_width - $crop_width))/2)*$old_height/$crop_height;
	    		$x_crop = round($x_crop);
	    		$y_crop = 	0;	
	    	}


	    	$file_ext =strtolower(substr($img_source, (strripos($img_source, '.')+1),strlen($img_source))); 

			// new
	    	$newpic = imagecreatetruecolor($crop_width,$crop_height);
			// transparent
	    	imagealphablending($newpic, false);
	    	imagesavealpha($newpic,true);
	       	$transparent = imagecolorallocatealpha($newpic, 255, 255, 255, 127);//
	       	imagefilledrectangle($newpic, 0, 0, $crop_width, $crop_height, $transparent);	

	       	if($file_ext == "png"){
	       		$source = imagecreatefrompng($img_source);
	       	}elseif($file_ext == "jpg" || $file_ext == "jpeg"){
	       		$source = imagecreatefromjpeg($img_source);
	       	}elseif($file_ext == "gif"){
	       		$source = imagecreatefromgif($img_source);
	       	} 

	       	if(!imagecopyresampled($newpic, $source,0,0, $x_crop, $y_crop, $real_width, $real_height, $old_width, $old_height))
	       	{
        		// Errors::setErrors("Not copy and resize part of an image with resampling when cropped");
	       	}


			// header('Content-Type: image/jpeg');

	       	if($file_ext == "png"){
	       		$img  =  imagepng($newpic,NULL,0); 
	       	}elseif($file_ext == "jpg" || $file_ext == "jpeg"){
	       		$img  =  imagejpeg($newpic,NULL,90);
	       	}elseif($file_ext == "gif"){
	       		$img  =  imagegif($newpic,NULL,0);
	       	} 




		    // end transparent
	       }

       //  // generate the byte stream
       //  imagejpeg($img, NULL, $quality);

       //  // and finally retrieve the byte stream
	       $rawImageBytes = ob_get_clean();
	       return "data:image/jpeg;base64," . base64_encode( $rawImageBytes );

	   }
  //ham ma hoa
	   function fSencode($encodeStr=""){
	   	$returnStr = "";
	   	if(!empty($encodeStr)) {
	   		$enc = base64_encode($encodeStr);
	   		$enc = str_replace('=','',$enc);
	   		$enc = str_rot13($enc);
	   		$returnStr = $enc;
	   	}

	   	return $returnStr;
	   }

	//ham giai mai
	   function fSdecode($encodedStr="",$type=0){
	   	$returnStr = "";
	   	$encodedStr = str_replace(" ","+",$encodedStr);
	   	if(!empty($encodedStr)) {
	   		$dec = str_rot13($encodedStr);
	   		$dec = base64_decode($dec);
	   		$returnStr = $dec;
	   	}
	   	switch($type){
	   		case 0:
	   		$returnStr = str_replace("\'","'",$returnStr);
	   		$returnStr = str_replace("'","''",$returnStr);
	   		return $returnStr;
	   		break;
	   		case 1:
	   		return intval($returnStr);
	   		break;
	   		case 3:
	   		return doubleval($returnStr);
	   		break;
	   	}
	   }
	   function objectToArray($d) {
	   	if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
	   		$d = get_object_vars($d);
	   	}

	   	if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	function time_elapsed_string($ptime){
		$etime = time() - $ptime;
		if ($etime < 1){
			return 'Vừa xong';
		}

		$a = array( 365 * 24 * 60 * 60  =>  'year',
			30 * 24 * 60 * 60  =>  'month',
			24 * 60 * 60  =>  'day',
			60 * 60  =>  'hour',
			60  =>  'minute',
			1  =>  'second'
		);
		$a_plural = array( 'year'   => 'năm',
			'month'  => 'tháng',
			'day'    => 'ngày',
			'hour'   => 'giờ',
			'minute' => 'phút',
			'second' => 'giây'
		);

		foreach ($a as $secs => $str){
			$d = $etime / $secs;
			if ($d >= 1){
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' trước';	        	
			}
		}
	}

	function set_image_webp($link,$repalce,$alt,$class,$lazy,$more,$webp = 1) {
		if($repalce) {
			$link = URL_ROOT.str_replace('/original/','/'.$repalce.'/', $link);
		} 
		if($alt){
			$alt = htmlspecialchars($alt);
		} 
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		$img = '<img ';
		if($class) {
			$img .= ' class="'.$class.'"';
		}
		if($alt) {
			$img .= ' alt="'.$alt.'"';
		}
		if($more) {
			$img .= $more;
		}
		if ($lazy) {
			$img .= ' data-src="'.$link.'"';
		} else {
			$img .= ' src ="'.$link.'"';
		}
		if (stripos( $user_agent, 'Chrome') !== false)
		{
			if($webp == 1) {
				$link_new = $link.'.webp';
				if ($lazy) {
					$img .= ' data-srcset="'.$link_new.'"';
				} else {
					$img .= ' srcset="'.$link_new.'"';
				}
			}
		}
		elseif (stripos( $user_agent, 'Safari') !== false || stripos( $user_agent, 'iPhone') !== false)
		{
		}
		else {
			if($webp == 1) {
				$link_new = $link.'.webp';
				if ($lazy) {
					$img .= ' data-srcset="'.$link_new.'"';
				} else {
					$img .= ' srcset="'.$link_new.'"';
				}
			}
			else {
			}	
		}
		$img .= '>';
		return $img;
	}

	function set_back_webp($link,$repalce,$alt,$class,$lazy,$more,$webp = 1) {
		if($repalce) {
			$link = URL_ROOT.str_replace('original',$repalce, $link);
		} 
		if($alt){
			$alt = htmlspecialchars($alt);
		} 
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		$img = '<div ';
		if($class) {
			$img .= ' class="'.$class.'"';
		}
		if($alt) {
			$img .= ' alt="'.$alt.'"';
		}
		if($more) {
			$img .= $more;
		}

		if (stripos( $user_agent, 'Chrome') !== false)
		{

			if($webp == 1) {
				$link_new = $link.'.webp';
				$img .= 'style="background-image: url('.$link_new.'); display: block;"';
			}
		}
		elseif (stripos( $user_agent, 'Safari') !== false || stripos( $user_agent, 'iPhone') !== false)
		{
			if ($lazy) {
				$img .= 'style="background-image: url('.$link.'); display: block;"';
			} else {
				//$img .= ' src ="'.$link.'"';
				$img .= 'style="background-image: url('.$link.'); display: block;"';
			}
		}
		else {
			if($webp == 1) {
				$link_new = $link.'.webp';		
				$img .= 'style="background-image: url('.$link_new.'); display: block;"';
			}
			else {
			}	
		}
		$img .= '>';
		return $img;

	}

	function does_url_exists($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code == 200) {
			$status = true;
		} else {
			$status = false;
		}
		curl_close($ch);
		return $status;
	}

	function fwrite_stream($file,$msg){
		$fn = $file;
		$fp = fopen($fn,"a") or die ("Error opening file in write mode!");		
		$content = $msg;
		fputs($fp,$content); 
		fclose($fp) or die ("Error closing file!"); 
	}

	function dt_edit_selectbox($title,$name,$value,$default = '',$arry_select = array(),$field_value = 'id', $field_label='name',$size = 1,$multi  = 0,$add_fisrt_option=0,$comment = '',$sub_item = '',$class='chosen-select',$class_col1='col-md-2',$class_col2='col-md-10'){
		$class = $class? $class:'';
		$html = '<div class="form-group"><div class="form_name">'.$title.'</div>
		<div class="value">
		';
		if(!$multi){
			$html_sized = $size > 1 ? "size=$size":"" ;
			$html .= '<select data-placeholder="'.$title.'" class="form-control " name="'.$name.'" id="'.$name.'" '.$html_sized.'>';
			$compare  = 0;
			if(isset($value))
				$compare = $value;
			else 
				$compare = $default;
			
			if($add_fisrt_option){
				$checked = "";
				if(!$compare)
					$checked = "selected=\"selected\"";
				$html .= '<option  value="0" '.$checked.'>--'.$title.'--</option>';
			}	
			$j = 0;
			if($arry_select && count($arry_select)){
				
				if(is_object(end($arry_select))){
					
					foreach ($arry_select as $select_item) {
						$checked = "";
						if(!$compare && !$j && !$add_fisrt_option){
							$checked = "selected=\"selected\"";
						} else {
							if($compare === ($select_item->$field_value))
								$checked = "selected=\"selected\"";
						}
						$html .= '<option value="'.$select_item->$field_value.'" '. $checked.'>'.$select_item -> $field_label.'</option>';	
						$j ++;
					}
				} else {
					foreach ($arry_select as $key => $name) {
						$checked = "";
						if(!$compare && !$j && !$add_fisrt_option){
							$checked = "selected=\"selected\"";
						} else {
							if($compare == $key)
								$checked = "selected=\"selected\"";
						}
						$html .= '<option value="'.$key.'" '. $checked.'>'.$name.'</option>';	
						$j ++;
					}
				}
			}
			$html .= '</select>';
		}else{
			// not working
			$html_sized = $size > 1 ? "size=$size":"" ;
			$html .= '<select data-placeholder="'.$title.'" class="form-control " name="'.$name.'[]" id="'.$name.'" '.$html_sized.' multiple="multiple">';
          //  if($add_fisrt_option){
//				$html .= '<option>--'.$title.'--</option>';
//			}
			$array_value  = isset($value)?explode(',',$value):array();
			
//			$compare  = 0;
//			if(@$value)
//				$compare = $value;
			$j = 0;
			if(count($arry_select)){
				if(is_object(end($arry_select))){
					foreach ($arry_select as $select_item) {
						$checked = "";
						if(in_array($select_item->$field_value,$array_value))
							$checked = "selected=\"selected\"";
						$html .= '<option value="'.$select_item->$field_value.'" '. $checked.'>'.$select_item -> $field_label.'</option>';	
						$j ++;
					}
				} else {
					foreach ($arry_select as $key => $name) {
						$checked = "";
						
						// if(in_array($name,$array_value))
						if(in_array($key,$array_value))	
							$checked = "selected=\"selected\"";
						$html .= '<option value="'.$key.'" '. $checked.'>'.$name.'</option>';	
						$j ++;
					}
				}
			}
			$html .= '</select>';
		}
		if($sub_item)
			$html .= $sub_item;
		if($comment)
			$html .= '<p class="help-block">'.$comment.'</p>';
		$html .= '</div></div>';
		echo $html;
	}
	/******* end CUT STRING BY LENGTH ********/

	function check_image($img_pc,$img_mb){
		$b = 1;
		if(!$img_pc && !$img_mb){
			$b = 0;
		}
		return $b;
	}
	function numInWords($num)
	{
		$nwords = array(
			0                   => 'không',
			1                   => 'một',
			2                   => 'hai',
			3                   => 'ba',
			4                   => 'bốn',
			5                   => 'năm',
			6                   => 'sáu',
			7                   => 'bảy',
			8                   => 'tám',
			9                   => 'chín',
			10                  => 'mười',
			11                  => 'mười một',
			12                  => 'mười hai',
			13                  => 'mười ba',
			14                  => 'mười bốn',
			15                  => 'mười lăm',
			16                  => 'mười sáu',
			17                  => 'mười bảy',
			18                  => 'mười tám',
			19                  => 'mười chín',
			20                  => 'hai mươi',
			30                  => 'ba mươi',
			40                  => 'bốn mươi',
			50                  => 'năm mươi',
			60                  => 'sáu mươi',
			70                  => 'bảy mươi',
			80                  => 'tám mươi',
			90                  => 'chín mươi',
			100                 => 'trăm',
			1000                => 'nghìn',
			1000000             => 'triệu',
			1000000000          => 'tỷ',
			1000000000000       => 'nghìn tỷ',
			1000000000000000    => 'ngàn triệu triệu',
			1000000000000000000 => 'tỷ tỷ',
		);
		$separate = ' ';
		$negative = ' âm ';
		$rltTen   = ' linh ';
		$decimal  = ' phẩy ';
		if (!is_numeric($num)) {
			$w = '#';
		} else if ($num < 0) {
			$w = $negative . numInWords(abs($num));
		} else {
			if (fmod($num, 1) != 0) {
				$numInstr    = strval($num);
				$numInstrArr = explode(".", $numInstr);
				$w           = numInWords(intval($numInstrArr[0])) . $decimal . numInWords(intval($numInstrArr[1]));
			} else {
				$w = '';
            if ($num < 21) // 0 to 20
            {
            	$w .= $nwords[$num];
            } else if ($num < 100) {
                // 21 to 99
            	$w .= $nwords[10 * floor($num / 10)];
            	$r = fmod($num, 10);
            	if ($r > 0) {
            		$w .= $separate . $nwords[$r];
            	}

            } else if ($num < 1000) {
                // 100 to 999
            	$w .= $nwords[floor($num / 100)] . $separate . $nwords[100];
            	$r = fmod($num, 100);
            	if ($r > 0) {
            		if ($r < 10) {
            			$w .= $rltTen . $separate . numInWords($r);
            		} else {
            			$w .= $separate . numInWords($r);
            		}
            	}
            } else {
            	$baseUnit     = pow(1000, floor(log($num, 1000)));
            	$numBaseUnits = (int) ($num / $baseUnit);
            	$r            = fmod($num, $baseUnit);
            	if ($r == 0) {
            		$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit];
            	} else {
            		if ($r < 100) {
            			if ($r >= 10) {
            				$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
            			}
            			else{
            				$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . numInWords($r);
            			}
            		} else {
            			$baseUnitInstr      = strval($baseUnit);
            			$rInstr             = strval($r);
            			$lenOfBaseUnitInstr = strlen($baseUnitInstr);
            			$lenOfRInstr        = strlen($rInstr);
            			if (($lenOfBaseUnitInstr - 1) != $lenOfRInstr) {
            				$numberOfZero = $lenOfBaseUnitInstr - $lenOfRInstr - 1;
            				if ($numberOfZero == 2) {
            					$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm linh ' . numInWords($r);
            				} else if ($numberOfZero == 1) {
            					$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . ' không trăm ' . numInWords($r);
            				} else {
            					$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
            				}
            			} else {
            				$w = numInWords($numBaseUnits) . $separate . $nwords[$baseUnit] . $separate . numInWords($r);
            			}
            		}
            	}
            }
        }
    }
    return $w;
}

function numberInVietnameseWords($num)
{
	return str_replace("mươi năm", "mươi lăm", str_replace("mươi một", "mươi mốt", numInWords($num)));
}

function numberWords($num)
{
	$rs    = numberInVietnameseWords($num);
	$rs[0] = strtoupper($rs[0]);
	return $rs . ' đồng';
}
?>