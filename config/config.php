<?php
    error_reporting(0);
    date_default_timezone_set('Asia/Kolkata');	
    $authenticate = true;  
    
    include("../../config/constants.php");
	/*
     * PROJECT DETAILS
    */   
    define("TITLE","Billing360");
    define("DOMAIN","http://".$_SERVER["SERVER_ADDR"]."/pure_eggs_soft/");
    define("DOMAIN_F","http://".$_SERVER["SERVER_ADDR"]."/pure_eggs_soft/");
    define("ROOT","/pure_eggs_soft/");
    define("ROOT_F","/pure_eggs_soft/");
    //define("INC","/metr_purchase_sale_soft/");
  
	//image upload and view path admin side
	define("BACKUP","upload//backup//");
	define("BKP_DAYS",29);
	define("LOGO_A","..//..//view//img//logo//");
	define("LOGO","view//img//logo//");
	
	define("CUSTOMER_UPING","..//..//view//upload//customer_excel//");
	define("CUSTOMER_VWING","view//upload//customer_excel//");
	define("PRO_IMG_UPING","..//..//view//upload//product_images//");
	define("PRO_IMG_VWING","view//upload//product_images//");
	define("INQ_PRO_IMG_UPING","..//..//view//upload//inq_pro_img//");
	define("INQ_PRO_IMG_VWING","view//upload//inq_pro_img//");
	
	define("SETTING_A","..//..//view//upload//quotation_pdf_file//");
	define("SETTING","view//upload//quotation_pdf_file//");
	define("QUO_A","..//..//view//upload//quotation_pdf_file//");
	define("QUO","view//upload//quotation_pdf_file//");
	define("invoice_A","..//..//view//upload//invoice_mail_file//");
	
	define("EMAILFILE_UPING","..//..//view//upload//email_attachment//");
	define("EMAILFILE_VWING","view//upload//email_attachment//");
	
	//Company Switch Login Password 
	define('LOGIN_SETTING',"1"); //1 without password : 0 For with Password
	 //define("COMPANY","metR Technology");
    define("C_URL","http://www.metrtechnology.com");
    define("DEVELOPER","");
    define("D_URL","http://www.metrtechnology.com");
    
    define("CITY","AHMEDABAD");
    define("COUNTRY","INDIA");
    define("CURRENCY","INR");
    define("C_SYMBOL","&#8377;");
    
    /*
     *	Database Credentials
     */
    define("SERVER","localhost");
    define("DB","pure_eggs_soft_db");//bizaccou_pure_eggs_db
    define("DB_USER","root");//
    define("DB_PASS","");//
	/*
    * Admin Details
    */
	
    define("ADMIN","Metr Technology");
    define("ADMIN_EMAIL","abhi.metr@gmail.com"); 
	
    /*Database Connectivity*/
    $dbcon = new mysqli(SERVER,DB_USER,DB_PASS,DB);
    if($dbcon->connect_errno > 0){
            die('Unable to connect to database server. [' . $dbcon->connect_error . ']');
    }
    if(isset($_SESSION['permission'])) {
            $permission = unserialize($_SESSION['permission']);
    }
    
    
    /*SPECIAL FUNCTIONS*/
    function rmv($str) {
        if($str == NULL || $str == '') {
            return "NO-DATA";
        }
        else {
            $str = $str;
            return strtoupper($str);
        }
    }	
	function filter_data($connection_link, $data) {
		$str = $connection_link -> real_escape_string($data);
		$str = ($str);
		return strtoupper($str);
	}
	function bulk_filter($connection_link,$array) {
		while ($ele = current($array)) {
			if(is_array($ele)) {
				$key = key($array);
				$value = bulk_filter($connection_link,$ele);
				$array[$key] = $value;
			}
			else {
				$key = key($array);
				$value = filter_data($connection_link,$ele);
				$array[$key] = $value;
			}
			next($array);
		}
		return $array;
	}	
	define("ENCRYPTION_KEY", "!@#$%^&*");
	/**
	 * Returns an encrypted & utf8-encoded
	 */
	 
	 $key=md5('india');

	//Encrypt Function
	function encrypt($string, $key)
	{
		$string=rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB)));
		return $string;
	}
	//Deccrypt Function
	function decrypt($string, $key)
	{
		$string=rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($string), MCRYPT_MODE_ECB));
		return $string;

	} 
	function check_internet_connection($sCheckHost = 'www.google.com') 
	{
		return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
	}
	function encryptt($pure_string) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, ENCRYPTION_KEY, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
		return $encrypted_string;
	}
	/**
	 * Returns decrypted original string
	 */
	function decryptt($encrypted_string) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, ENCRYPTION_KEY, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		return $decrypted_string;
	}	
	//Convert to Indian Currency Format
	//@Start
	function check_user($alias)
	{
		$qry='';
		if($_SESSION['user_type']!='2')
		{
			$qry="  and ".$alias.".user_id in ($_SESSION[user_id])";
		}
		return $qry;
	}
	function text_rnremove($text)
	{
		return str_replace("\r\n","<br/>",($text));
	}
	function text_divremove($text)
	{
		$text=str_replace("<div>","",($text));
		$text=str_replace("</div>","",($text));
		return $text; 
	}
	function text_rnrremove_disp($text)
	{
		return str_replace("<br/>","\n",($text));
	}
	function indian_number($n, $d = 0) {
		$n = number_format($n, $d, '.', '');
		$n = strrev($n);
		if ($d) $d++;
		$d += 3;
		if (strlen($n) > $d)
			$n = substr($n, 0, $d) . ','
			   . implode(',', str_split(substr($n, $d), 2));

		return strrev($n);
	}	
	//@End
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function convert_number_to_words($num = false) {

/*$no = round($x);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
  $w = $result . "Rupees Only ";
return ($w);
*/
 $num = str_replace(array(',', ' '), '' , trim($num));
    $fraction='';
    if(! $num) {
        return false;
    }
	
    $words = array();
	
		$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
		);
		$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
		$list3 = array('', 'thousand', 'million', 'billion', 'trillion');


    $x = $num - floor($num);

    //$r = fmod($x, 10);
	
	if($x > 0 )
	{
	
	    $x1 = $x*100;
		$x = round($x1);
		if ( $x < 20 ) {
            $tens = ($x ? ' ' . $list1[$x] . ' ' : '' );
		
             } else {
                $tens = (int)($x / 10);
				$tens = ' ' . $list2[$tens] . ' ';
				$singles = (int) ($x % 10);
				$singles = ' ' . $list1[$singles] . ' ';
				
            }

		$fraction=  $tens.'-'.$singles.'Paisa';
	
	}
    $num = (int) $num;
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] .' '. $list2[10] . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles =(int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
		
		
        $words[] = $hundreds. $tens .$singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
	
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
if($fraction == ''){
	$and = '';
}else{
	$and = 'and';
}
   return  implode(' ', $words)."Rupees".' ' .$and .$fraction." Only " ;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/*function convert_number_to_words($x) {

    $nwords = array("Zero", "One", "Two", "Three", "Four", "Five", "Six", "Seven","Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen","Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen","Nineteen", "Twenty", 30 => "Thirty", 40 => "Forty",50 => "Fifty", 60 => "Sixty", 70 => "Seventy", 80 => "Eighty",90 => "Ninety" );
	/*if(!is_numeric($x))
	{
		$w = '#';
	}else 
	
	if(fmod($x, 1) != 0)
	{
		$w = '#';
	}else{
		if($x < 0)
		{
			$w = 'minus ';
			$x = -$x;
		}
		
		else{
			$w = '';
		}
		if($x < 21)
		{
			$w .= $nwords[$x];
		}else if($x < 100)
		{
			$w .= $nwords[10 * floor($x/10)];
			$r = fmod($x, 10);
			if($r > 0 )
			{
				$w .= '-'. $nwords[$r];
				$r = fmod($x,1);
				if($r < 1 and $r > 0)//for paisa code
				{
					$r=$r*100;
					$w .= ' and '.$nwords[10 * floor($r/10)];$r = fmod($x, 10);
					if($r > 0)
					{
						$w .= '-'. $nwords[$r];
					}
					$w .=' Paisa';
				}
			}
		} else if($x < 1000)
		{
			$w .= $nwords[floor($x/100)] .' hundred';
			$r = fmod($x, 100);
			if($r > 0)
			{
				$w .= ' and '. convert_number_to_words($r);
			}
		} else if($x < 100000)
		{
			$w .= convert_number_to_words(floor($x/1000)) .' thousand';
			$r = fmod($x, 1000);
			if($r > 0)
			{
				$w .= ' ';
				if($r < 100)
				{
					$w .= 'and ';
				}
				$w .= convert_number_to_words($r);
			}
		} else {
			$w .= convert_number_to_words(floor($x/100000)) .' lakh';
			$r = fmod($x, 100000);
			if($r > 0)
			{
				$w .= ' ';
				if($r < 100)
				{
					$word .= 'and ';
				}
				$w .= convert_number_to_words($r);
			}
		}
	/*}
	  return ($w);
    //return ucwords($w);
}*/

function smart_resize_image($file,
                              $width              = 0, 
                              $height             = 0, 
                              $proportional       = false, 
                              $output             = 'file', 
                              $delete_original    = true, 
                              $use_linux_commands = false ) {
      
    if ( $height <= 0 && $width <= 0 ) return false;
    # Setting defaults and meta
    $info                         = getimagesize($file);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;
    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );
      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
    }
    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
      case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
      case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);
      if ($transparency >= 0) {
        $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
        $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
    
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }
    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
      case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
      default: return false;
    }
    return true;
}
function strip_word_html($text, $allowed_tags = '<a><ul><li><b><i><sup><sub><em><strong><u><br><br/><br /><p><h2><h3><h4><h5><h6><span><div><table><tr><td><th><thead><tbody><tfoot>')
{
	mb_regex_encoding('UTF-8');
    //replace MS special characters first
    $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
    $replace = array('\'', '\'', '"', '"', '-');
    $text = preg_replace($search, $replace, $text);
    //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
    //in some MS headers, some html entities are encoded and some aren't
    //$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    //try to strip out any C style comments first, since these, embedded in html comments, seem to
    //prevent strip_tags from removing html comments (MS Word introduced combination)
    if(mb_stripos($text, '/*') !== FALSE){
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
    }
    //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
    //'<1' becomes '< 1'(note: somewhat application specific)
    $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
    $text = strip_tags($text, $allowed_tags);
    //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
    $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
    //strip out inline css and simplify style tags
    $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
    $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
    $text = preg_replace($search, $replace, $text);
    
    $num_matches = preg_match_all("/\<!--/u", $text, $matches);
    if($num_matches){
        $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
    }
    $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
	 
return $text;
}

function convert_number_to_dollar_words($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    $fraction='';
    if(! $num) {
        return false;
    }
    $words = array();
	$list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
	'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
	$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
	$list3 = array('', 'thousand', 'million', 'billion', 'trillion');
	
	
    $x = $num - floor($num);
    //$r = fmod($x, 10);
	if($x > 0 )
	{
	    $x = round($x*100);
		if ( $x < 20 ) {
            $tens = ($x ? ' ' . $list1[$x] . ' ' : '' );
             } else {
                $tens = (int)($x / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($x % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }

		$fraction= '-'. $tens . $singles.' Cent';
		
	}
    $num = (int) $num;
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] .' '. $list2[10] . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
   
    return implode(' ', $words)." Dollars ".$fraction." Only ";
	
}

/*function convert_number_to_dollar_words($testNumber){
	
	$tempNum = explode( '.' , $testNumber );

	$convertedNumber = ( isset( $tempNum[0] ) ? convertNumber( $tempNum[0] ) : '' );

	//  Use the below line if you don't want 'and' in the number before decimal point
	$convertedNumber = str_replace( ' and ' ,' ' ,$convertedNumber );

	//  In the below line if you want you can replace ' and ' with ' , '
	$convertedNumber .= ( ( isset( $tempNum[0] ) and isset( $tempNum[1] ) )  ? ' Dollars and ' : ' Dollars' );

	$convertedNumber .= ( isset( $tempNum[1] ) ? convertNumber( $tempNum[1] ) .' cents' : '' );
	
	return $convertedNumber;
}

*/
function check_permission($pagename,$usetype,$permission,$dbcon)
{
	$query="SELECT perm.* FROM `tbl_permission` as perm left join tbl_menu as menu on perm.menu_id=menu.menu_id where perm.".$permission."_permission=1 and menu.status=0 and perm.status=0 and usertype_id=".$usetype." and menu.page_name ='".$pagename."'";
	$rs=$dbcon->query($query);
	if(mysqli_num_rows($rs)>0)
	{
		return true;
	}
	return false;
}

?>