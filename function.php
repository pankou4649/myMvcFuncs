<?php
/*************************
$time1=date("Y-m-d H:i:s");                              
$time2="2017-5-1 12:30:00";                              
**************************/
function compadays($time1,$time2){

	if(strtotime($time1)-strtotime($time2)<0){                   //

	    return true;;                              //time1-time2<0，順番としてはtime1,time2

	}else{

	    return false;                              //順番としてはtime2,time1

	}


}
function getColor($num) {
    $hash = md5('color' . $num); // modify 'color' to get a different palette
    return array(
        hexdec(substr($hash, 0, 2)), // r
        hexdec(substr($hash, 2, 2)), // g
        hexdec(substr($hash, 4, 2))); //b
}
/**------------------------------------------------------------------------------------
 *
 *　共通処理
 *
 ------------------------------------------------------------------------------------*/
  function getWeekname($datestr){
  		global $langtxt;
 		
  		 $thedate=str_replace('-','',$datestr);
  
		$date = date('w', strtotime($thedate));

		//配列を使用し、要素順に(日:0〜土:6)を設定する
		$week =$langtxt['weekname'];

		//日本語で曜日を出力
		return $week[$date] ;
		

  
  }
  function datelinechg($datestr){
  	
  	return str_replace('-','/',$datestr)."(".getWeekname($datestr).")";;
  }
  
  function metafn($tgtFile,$key){
 
 	global $metai18n;
 	
 	$areaid=$_GET['areaid'];

 	
 	$tmplan= $metai18n[$areaid][$tgtFile][$key];
 	
	return $tmplan;	

 }
 
  function b2bmetafn($tgtFile,$key){
 
 	global $metai18n;
 	
 	$areaid=$_GET['areaid'];

 	
 	$tmplan= $metai18n[$areaid][$tgtFile][$key];
 	
	return $tmplan;	

 }
 
 
 function getNDaysDate($startday,$daynums){
 	
 	$date=date_create($startday);
	date_add($date,date_interval_create_from_date_string("$daynums days"));
	$finaldate=date_format($date,"Y/m/d");
	$fdate=date_format($date,"Ymd");
	return $finaldate."(".getWeekname($fdate).")";

 }
 function getNDaysSqlDate($startday,$daynums){
 	
 	$date=date_create($startday);
	date_add($date,date_interval_create_from_date_string("$daynums days"));
	$finaldate=date_format($date,"Y-m-d");

	return $finaldate;

 }
 function getNDaysBeforeSqlDate($startday,$daynums){
 	
 	$date=date_create($startday);
	date_sub($date,date_interval_create_from_date_string("$daynums days"));
	$finaldate=date_format($date,"Y-m-d");

	return $finaldate;

 } 
/*
$pathstr→/xxx/ooo左必ず/が要る
*/
function outpath($pathstr,$rtnonly=0){
	global $langtype;
	$path=DEBUGDIR."/".$langtype.$pathstr;
	if($rtnonly){
		return $path;
	}else{
		echo $path;
	}

}
function processmoney($money){
	
	return round($money);
}
//$day1=date("y-m-d h:i:s");
function getdatedif($day1,$day2){
	return (strtotime($day2)-strtotime($day1));



}
function outrealpath($pathstr,$rtnonly=0){
	global $langtype;
	$path=DEBUGDIR.$pathstr;
	if($rtnonly){
		return $path;
	}else{
		echo $path;
	}

}
function e($string) {
	echo $string;
}

/**-----------------------------------
 *　ランダム文字列生成
------------------------------------*/

function generateRandomString($size_of_random_string = 8) {
    // ランダム文字列に使用する文字
    $char_list_str = array_merge(
    	range('a', 'z'),
    	range('0', '9'),
    	range('A', 'Z'),
    	range('0', '9'),
    	range('a', 'z'),
    	range('A', 'Z'),
    	range('0', '9'),
    	range('A', 'Z'),
    	range('a', 'z')
	);

	for($i=0;$i<10;$i++) {
		$char_list_str = array_merge($char_list_str,$char_list_str);
	}


    if ($size_of_random_string < 1) {
        return false;
    }
    if ($size_of_random_string === 1) {
        return $char_list_str[array_rand($char_list_str, 1)];
    }

    $random_string = '';
    foreach (array_rand($char_list_str, $size_of_random_string) as $k) {
        $random_string .= $char_list_str[$k];
    }
    return $random_string;
}




/**-----------------------------------
 *　JS URL出力
 ------------------------------------*/

function js($file,$iscommon=false,$output=true) {

	$path = DEBUGDIR.$file;
	
	$path=str_replace("//","/",$path);

	if(!$output) {
	
		return $path;
	} else {
		echo $path;
	}
}

/**-----------------------------------
 *　CSS URL出力
 ------------------------------------*/

function css($file,$iscommon=false,$output=true) {
	$path = DEBUGDIR.$file;
	
	$path=str_replace("//","/",$path);

	if(!$output) {
	
		return $path;
	} else {
		echo $path;
	}
}

 
 function lang($key,$page="common"){
 	global $langtxt;

 	$tmplan= $langtxt[$page][$key];

 	$newtmplan =explode('”', $tmplan);
	$newtmplan2=implode('"',$newtmplan);

 	$newtmplan3 =explode('’', $newtmplan2);
	$finallan=implode("'",$newtmplan3);

 	
 echo $finallan;	

 
 }
 function rtnlang($key,$page="common"){
 	global $langtxt;

 	$tmplan= $langtxt[$page][$key];

 	$newtmplan =explode('”', $tmplan);
	$newtmplan2=implode('"',$newtmplan);

 	$newtmplan3 =explode('’', $newtmplan2);
	$finallan=implode("'",$newtmplan3);

 	
 return $finallan;	

 
 }
 function br2nl($text){
    return preg_replace('/<br\\s*?\/??>/i','',$text);
}
/**-----------------------------------
 *　CSS HTML出力
 ------------------------------------*/

function setCss($array) {

	for($i=0;$i < count($array);$i++) {
		echo '<link rel="stylesheet" href="'.$array[$i].'" media="screen">';
	}
}


/**-----------------------------------
 *　JS HTML出力
 ------------------------------------*/

function setJs($array) {

	for($i=0;$i < count($array);$i++) {

		echo '<script type="text/javascript" src="'.$array[$i].'"></script>';
		
	}
}




//----------------------------------------------------------------------------
//	処理概要：リクエスト送信
//	呼出形式：$ret = get_response($url,$req);
//	引　　数：
//			　$url	接続するURL
//			　$req	送信するリクエスト "xml=" .$xml ."&ini=" .$ini
//	戻 り 値：レスポンス
//----------------------------------------------------------------------------
function get_response($url,$req){

	$ch = curl_init();
	curl_setopt ($ch,CURLOPT_URL,$url);
	curl_setopt ($ch,CURLOPT_POST,TRUE);

	//postするデータ
	$post = $req;

	curl_setopt ($ch,CURLOPT_POSTFIELDS,$post);
	curl_setopt ($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt ($ch,CURLOPT_RETURNTRANSFER, TRUE);



	$response = curl_exec($ch);
	

	curl_close ($ch);

	return $response;
}

/*---------------------------------------------------------
本番の関数は問題がありますので、array2xmlの切り替え用
	$xml = array2xml($xml_ary);→$xml = array2xmlByPK($xml_ary);
*/
function array2xmlByPK($data, $encoding='utf-8', $root='pankou') {
    $xml    = '<?xml version="1.0" encoding="' . $encoding . '"?>';
    //$xml   .= '<' . $root . '>';
    $xml   .= data_to_xml($data);
    //$xml   .= '</' . $root . '>';

   
    return $xml;
}

function sendedxmllog($filename,$debugstr,$runflg){
	
	if($runflg==1){
		$logfile=ROOTDIR."/xml-log-".$filename.".txt";
		
		file_put_contents($logfile,$debugstr);

	
	}

}

function data_to_xml($data) {
    $xml = '';
    foreach ($data as $key => $val) {

    	
    	if((isset($val['_attr']))&& ( is_array($val)  )){

    		$xml    .=  "<$key ";
    		foreach($val['_attr'] as $k=>$v){
    			
    			if(($v=="")||($v==null)){
    				$xml    .=  " $k = \"\"";
    			}else{
    				$xml    .=  " $k = \"$v\"";
    			}
    			
    		}
    		
	    	if(isset($val['_val']) ){


	    		 $xml    .=  " >";
	    		 $xml    .=  $val['_val'];
	    		 $xml    .=  "</".$key.">";
	    	}else{
	    		 $xml    .=  " />";
	    	}
	    	
	    	
	    	$xml .= "\r\n";	  
	    	
	    	 
    	}else{

	        is_numeric($key) && $key = "item id=\"$key\"";
	        $xml    .=  "<$key>";
	        $xml    .=  ( is_array($val) || is_object($val)) ? data_to_xml($val) : $val;
	        list($key, ) = explode(' ', $key);
	       $xml    .=  "</$key>";
	       
	       	$xml .= "\r\n";	
    	}
		 
    }
    return $xml;
}


function xml2arrayByPK($contents, $get_attributes=1) {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }
    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create();
    xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
    xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
    xml_parse_into_struct( $parser, $contents, $xml_values );
    xml_parser_free( $parser );

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array;

    //Go through the tags.
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = '';
        if($get_attributes) {//The second argument of the function decides this.
            $result = array();
            if(isset($value)) $result['value'] = $value;

            //Set the attributes too.
            if(isset($attributes)) {
                foreach($attributes as $attr => $val) {
                    if($get_attributes == 1) $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    /**  :TODO: should we change the key name to '_attr'? Someone may use the tagname 'attr'. Same goes for 'value' too */
                }
            }
        } elseif(isset($value)) {
            $result = $value;
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;

            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                $current = &$current[$tag];

            } else { //There was another element with the same tag name
                if(isset($current[$tag][0])) {
                    array_push($current[$tag], $result);
                } else {
                    $current[$tag] = array($current[$tag],$result);
                }
                $last = count($current[$tag]) - 1;
                $current = &$current[$tag][$last];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;

            } else { //If taken, put all things inside a list(array)
                if((is_array($current[$tag]) and $get_attributes == 0)//If it is already an array...
                        or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                    array_push($current[$tag],$result); // ...push the new element into that array.
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }

    return($xml_array);
} 



	
?>
