<?php
namespace app\controllers;

use pkphp\base\front;
class pay extends front
{
    protected $creditCardToken;
	protected $cardDateMonth;
	protected $cardDateYear;
	protected $ccSecurityCode;
	protected $accessID;
	protected $accessPass;
	protected $orderID;	
	

    //
    public function index()
    {
		global $config;

		if($_GET['peoplecnt']==""){
			
			echo "選択したデータが失ったので、再選択してください";
			exit;
		
		}

		$couponid		=$_GET['couponid'];
		$usagetypecnt	=$_GET['usagetypecnt'];
		$mdacode		=$_GET['mdacode'];
		$affiliatecd	=$_GET['affiliatecd'];
		$ages			=$_GET['ages'];
		$peoplecnt		=$_GET['peoplecnt'];//総人数
		
		if($usagetypecnt==""){
		
			echo "人数を取れなかった、前の画面からもう一回やってみてください。";
			exit;
		}		

		$isb2b=$this->_b2bflg;
		if($isb2b==1){//b2b
			//ログインしているか判断が要る
			if(!$this->b2blogincheck()){
				$tmp="/btob?areaid=".$_GET['areaid'];
				$url=outpath($tmp,1);
				$this->jumpto($url);
	    		exit;
			}	
		}

		$calmonth=$_GET['caldate'];
		
		if($isb2b==1){//b2b
			$cooupontype=$this->db->getCopnpointPointcalByCoupon_idCalmonthB2b($couponid,$calmonth);
		}elseif($isb2b==0){//b2c
			$cooupontype=$this->db->getCopnpointPointcalByCoupon_idCalmonth($couponid,$calmonth);
		}
		
		
		
		
		for($i=0;$i<count($cooupontype);$i++){
			
		$b2bpath="";
		if($isb2b==1){//b2b
			$b2bpath="/b2b";
			$tmparr=$this->db->getB2bmoneycal($cooupontype[$i]['copnb2bmid'],$cooupontype[$i]['coupon_id'],$calmonth);
		}elseif($isb2b==0){//b2c
			$tmparr=$this->db->getB2cmoneycal($cooupontype[$i]['copnb2cmid'],$cooupontype[$i]['coupon_id'],$calmonth);
		}
					
			$cooupontype[$i]['calid_money']		=$tmparr[0]['calid'];//b2cmoneycalのID
			$cooupontype[$i]['f_key_money']		=$tmparr[0]['f_key'];
			$cooupontype[$i]['coupon_idmoney']	=$tmparr[0]['coupon_id'];
			$cooupontype[$i]['calname_money']	=$tmparr[0]['calname'];
			$cooupontype[$i]['calmonth_money']	=$tmparr[0]['calmonth'];
			$cooupontype[$i]['calval_money']	=$tmparr[0]['calval'];//金額
			$cooupontype[$i]['calcost_money']	=$tmparr[0]['calcost'];//原価
			$cooupontype[$i]['calsex_money']	=$tmparr[0]['calsex'];
			
			
		}
		$agearr=explode(',',$ages);
		

		$areas=$this->db->getBigAreas($this->mullang);
		
		
		$rtn=$this->getPassinfoAllSubdata($isb2b,$couponid,$_GET['areaid'],$calmonth,$_GET['period']);

	
	
		for($d=0;$d<count($rtn[0]['subdata']);$d++){
			$typearr[$rtn[0]['subdata'][$d]['copnmname_en']]= $rtn[0]['subdata'][$d]['copnmid']."-placeholder-". ($rtn[0]['subdata'][$d]['calval']*1+$rtn[0]['subdata'][$d]['calval']*$config['tax']) ."-". $rtn[0]['subdata'][$d]['point_calid']."-".$rtn[0]['subdata'][$d]['point_calval']."-". $rtn[0]['subdata'][$d]['copnmname_en'];

		}			

		$typejsonstr=json_encode($typearr);

		//クレジットカード処理開始
		$this->assign('gmoShopId',$this->yourShopId);	
		$this->assign('paymentid',$this->paymentid);	
		$this->assign('jstokenurl',$this->jstokenurl);	
		//クレジットカード処理終了
		$areaid=$_GET['areaid'];
		$aiagecases=array_keys($config['aichkage']);

		$this->assign('aichkage',$config['aichkage']);
		$this->assign('aiagecases',$aiagecases);
		
		$this->assign('typejsonstr',$typejsonstr);
		$this->assign('typestr',$stypestr);
		$this->assign('ages',$agearr);

		$this->assign('age_disabled',json_encode($config['age_disabled']));
		$this->assign('age_other',json_encode($config['age_other']));
		$this->assign('age_children',json_encode($config['age_children']));
		$this->assign('age_student',json_encode($config['age_student']));
		$this->assign('age_adult',json_encode($config['age_adult']));
		$this->assign('age_senior',json_encode($config['age_senior']));
				
		$this->assign('cnfadulttype',json_encode($config['adulttype']));
		$this->assign('cnfchildtype',json_encode($config['childtype']));
		if($isb2b==1){
			
			$copnb2bcmid="copnb2bmid";
		}else{
			$copnb2bcmid="copnb2cmid";
		}
		$this->assign('copnb2bcmid',$copnb2bcmid);
		$this->assign('isb2b',$isb2b);
		$this->assign('mullang',$this->mullang);

		$this->assign('childtypename',json_encode($config['childrentypename']));
		$this->assign('adulttypename',json_encode($config['adulttypename']));
		
		$this->assign('mullang',$this->mullang);
		$this->assign('b2bpath',$b2bpath);
		$this->assign('mdacode',$mdacode);
		$this->assign('affiliatecd',$affiliatecd);
		$this->assign('areaid',$areaid);
		$this->assign('cooupontype',$cooupontype);	
		$this->assign('areas',$areas);	
		$this->assign('peoplenum',$peoplecnt);	
		$this->assign('paylib',1);	
		
        $this->render();
    }
    public function confirm()
    {

	
		$issendmail=1;
		
		if(isset($_GET['issendmail'])){
	
			$issendmail=$_GET['issendmail'];
		}
	
		
		$isb2b=$this->_b2bflg;
		$mdacode=$_GET['mdacode'];
		$affiliatecd=$_GET['affiliatecd'];
		$usagetypecnt=$_GET['usagetypecnt'];
		$posttoken=$this->getToken(8);
		$areaid=$_GET['address'];
		$areaarr=$this->db->getAreasByAreaid($areaid);
		$couponid=$_GET['couponid'];
		$couponinfo=$this->db->getCouponsByCouponid($couponid); 
		$agearr=explode('-', $_GET['age']);
		$age=$agearr[0];
		$peoplecnt=$_GET['peoplenum'];
		
		for($i=0;$i<$usagetypecnt;$i++){
		
			$usagetypetmp				=$_GET['usagetype_'.$i];
			$usagetypearr				=explode('-',$usagetypetmp);
			if($isb2b==1){
				$queryrtn					=$this->db->getCopnb2bmoneyByCopnb2cmid($usagetypearr[0]);	
			}elseif($isb2b==0){
				$queryrtn					=$this->db->getCopnb2cmoneyByCopnb2cmid($usagetypearr[0]);
			}
			
			$queryrtn[0]['peoplenum']	=$usagetypearr[1];
			$queryrtn[0]['money']		=$usagetypearr[2];
			$usagetypes[]				=$queryrtn[0];
			
			
		}

		
		if($isb2b==1){
			$b2bpath="/b2b";	
		}elseif($isb2b==0){
			$b2bpath="";
		}

		
		$this->assign('age',$age);
		$this->assign('issendmail',$issendmail);
		
		$this->assign('mullang',$this->mullang);
		$this->assign('affiliatecd',$affiliatecd);	
		$this->assign('mdacode',$mdacode);	
		$this->assign('b2bpath',$b2bpath);	
		$this->assign('posttoken',$posttoken);	
		$this->assign('areaid',$areaid);	
		$this->assign('couponinfo',$couponinfo[0]);	
		$this->assign('areaarr',$areaarr);	
		$this->assign('usagetypecnt',$usagetypecnt);	
		$this->assign('peoplecnt',$peoplecnt);	
		$this->assign('usagetypes',$usagetypes);	
		
		
        $this->render();
    }
    // 取引登録(EntryTran)
    public function dopay()
    {	
    	global $config;
		
		$ismobile=$this->isMobile();
		
		$isb2b=$this->_b2bflg;
		if($isb2b<>1){//b2c
			if(!isset($_POST['creditCardToken'])){
				
				echo "カード情報が足りない。";
			}
			
		}	

		$iscardpayok			=0;
		$coupon_id				=$_POST['couponid'];
		$posttoken				=$_POST['posttoken'];
		$area_id				=$_POST['address'];
		$urlareaid				=$_POST['areaid'];
		$caldate				=$_POST['caldate'];
		$period					=$_POST['period'];
		$endcaldate				=getNDaysSqlDate($caldate,$period*1-1);

		$delegatebirthday		=$_POST['delegatebirthday'];
		$issendmail				=$_POST['issendmail'];
		if($isb2b==1){//b2c
			$finalmoney		=0;
		}elseif($isb2b==0){
			$finalmoney		=$_POST['finalmoney'];
		}
		$affiliatecd	=$_POST['affiliatecd'];
		$finalmoneylog	=$_POST['finalmoney'];
		$finalpointval	=$_POST['finalpointval'];
		$finalpointvallog	=$finalpointval;
		$delegatefist	=$_POST['delegatefist'];
		$delegatelast	=$_POST['delegatelast'];
		$urllang	=$_POST['urllang'];
		
		$delegatesex	=$_POST['sex'];
		$delegateage	=$_POST['age'];
		$delegatetel	=$_POST['delegatetel'];
		$delegatemail	=$_POST['email'];
		$couponcd		=$_POST['couponcd'];
		$coupontypestrs	=$_POST['coupontype'];
		//copnpoint_id-(copnb2cmoneyのid)-年齢区分名称-ポイントID-ポイント数-ポイント原価-金額ID-金額-金額原価-区分キー
		$coupontypearr = explode('-', $coupontypestrs);


		
		$copnpoint_id	=$coupontypearr[0];
		$usagetypecnt	=$_POST['usagetypecnt'];		
		$peoplecnt		=$_POST['peoplecnt'];		
		
		$finalpoint_id=$coupontypearr[3];	
		$finalmoney_id=$coupontypearr[6];

    	//if($config['isdebugrun']==0){
    		$this->oneaccess($posttoken);
    	//}
    	

		//-----最初処理-----
		$this->db->noauto_commit(); 
		$dbsqlerr=0;
		//===================	
				
		$this->creditCardToken=$_POST['creditCardToken'];
		$this->cardDateMonth=$_POST['cardDateMonth'];
		$this->cardDateYear=$_POST['cardDateYear'];
		$this->ccSecurityCode=$_POST['ccSecurityCode'];
		//代表者
		if($isb2b==1){//b2b
		
				if(!isset($_SESSION['sellercompanyid'])){
					echo "もう一回ログインしてください。";
					exit;
					
				}
				
				if(!isset($_SESSION['sellerid'])){
					echo "もう一回ログインしてください。";
					exit;
					
				}				

				$frmcompanyid=$_SESSION['sellercompanyid'];
				$frmsellerid=$_SESSION['sellerid'];

				$insertid=$this->db->addBuyingb2b($frmsellerid,$frmcompanyid,$urllang,$coupon_id,$copnpoint_id,$urlareaid,$area_id,$caldate,$period,$endcaldate,$usagetypecnt,$finalmoney_id,$finalmoney,$finalmoneylog,$finalpoint_id,$finalpointval,$finalpointvallog,$delegatefist,$delegatelast,$delegatesex,$delegateage,$delegatetel,$delegatebirthday,$delegatemail);
			  //$insertid=$this->db->addBuyingb2b($frmsellerid,$frmcompanyid,$coupon_id,$copnpoint_id,$urlareaid,$area_id,$caldate,$period,$usagetypecnt,$finalmoney_id,$finalmoney,$finalmoneylog,$finalpoint_id,$finalpointval,$finalpointvallog,$delegatefist,$delegatelast,$delegatesex,$delegateage,$delegatetel,$delegatebirthday,$_POST['email']);
				if($insertid<0){
					$dbsqlerr--;
					echo "addBuyingb2b erro";
				}
		
		}else{
	
				$insertid=$this->db->addBuying($affiliatecd,$urllang,$coupon_id,$copnpoint_id,$urlareaid,$area_id,$caldate,$period,$endcaldate,$usagetypecnt,$finalmoney_id,$finalmoney,$finalmoneylog,$finalpoint_id,$finalpointval,$finalpointvallog,$delegatefist,$delegatelast,$delegatesex,$delegateage,$delegatetel,$delegatebirthday,$delegatemail);
				if($insertid<0){
					$dbsqlerr--;
					echo "addBuying erro";
				}

		}		

		if($_POST['mdacode']<>""){
		
		
			if($isb2b==1){//b2b
				$medcdrtn=$this->mediacdLogb2b($insertid,$_POST['mdacode']);
				if($medcdrtn<0){
					$dbsqlerr--;
					echo "mediacdLogb2b erro";
				}
			}else{
				$medcdrtn=$this->mediacdLogb2c($insertid,$_POST['mdacode']);
				if($medcdrtn<0){
					$dbsqlerr--;
					echo "mediacdLogb2c erro";
				}
								
			}

		}
		//代表者insertBuyingusage
		$buser_id=$insertid;
		$inertusagertn=$this->insertBuyingusage($_POST,$peoplecnt,$buser_id,1,$isb2b,$insertid,$copnb2cmid,$copnb2cnum,$copnb2cprice,$pointcal_calid,$pointcalval);
		

		
		//代表者を除く
		for($i=0;$i<$peoplecnt-1;$i++){
			
			$buy_id= $insertid;
			$followerfirstname=$_POST['followerfirst_'.$i];
			$followerlastname=$_POST['followerlast_'.$i];;
			$followersex=$_POST['followersex_'.$i];
			$followerage=$_POST['followerage_'.$i];
			$followertypestr=$_POST['followertype_'.$i];
			$followertypearr = explode('-', $followertypestr);
			
			if($isb2b==1){//b2b
				$insertid2=$this->db->addBuyinguserb2b($buy_id,$followerfirstname,$followerlastname,$followersex,$followerage,$followertypearr[0],$followertypearr[6],$followertypearr[3]);
			}else{
				$insertid2=$this->db->addBuyinguser($buy_id,$followerfirstname,$followerlastname,$followersex,$followerage,$followertypearr[0],$followertypearr[6],$followertypearr[3]);
			
			}

			
			if($insertid2<0){
				$dbsqlerr--;
				echo "addBuyinguser/addBuyinguserb2b erro";
			}
		
		}
		
		//同行者insertBuyingusage(内部Loopがありますので、Loopから外す)
		$buser_id=$insertid2;
		$inertusagertn=$this->insertBuyingusage($_POST,$peoplecnt,$buser_id,0,$isb2b,$insertid,$copnb2cmid,$copnb2cnum,$copnb2cprice,$pointcal_calid,$pointcalval);

			
		if($inertusagertn<0){
			$dbsqlerr--;
			echo "insertBuyingusage/insertBuyingusageb2b erro";
		}
		
		
		if($dbsqlerr>=0){
	
			//連番作成
			$num=str_pad($insertid,8,"0",STR_PAD_LEFT); 
			$testflg="";
			if($config['isdebugrun']===1){
				$testflg=$config['testflg'];
			}
			$b2cflg="";
			if($isb2b==1){
				$b2cflg="B";
			}
			if($isb2b==0){
				$b2cflg="C";
			}
						
			if($urlareaid==1){
				$tmpid=$testflg."HL".$b2cflg.$num;//連番コード	
			}elseif($urlareaid==10){
				$tmpid=$testflg."OL".$b2cflg.$num;//連番コード	
			}
			
		
			$orderid=$tmpid;//オーダーID 
			$itemcode=$this->itemcode;//商品コード(クポンコード)----------------
			
			$amount=$finalmoney;//利用金額
			$tax=$config['tax'];//税送料------------------------------
			
			
			//仮値終了
			$this->orderID=$orderid;
	    	$this->assign('httpisok', "success");
				    	
			if($isb2b<>1){//b2cのみの、クレジットカードを通す

				// リクエストコネクションの設定
				$curl=curl_init();
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_URL, $this->payurl );//-------------------
				$param = [
				    'ShopID'       => $this->yourShopId,
				    'ShopPass'     => $this->yourShopPassword,
				    'OrderID'      => $orderid,
				    'JobCd'        => $config['JobCd'],
				    'ItemCode'     => $itemcode,
				    'Amount'       => $amount,
				    'Tax'          => $tax,
				    'TdFlag'       => $config['TdFlag'],
				    'TdTenantName' => $config['TdTenantName'],
				    'Tds2Type'     => $config['Tds2Type']
				];

				// リクエストボディの生成
				curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $param ) );

				// リクエスト送信
				$response = curl_exec( $curl );

				$curlinfo = curl_getinfo( $curl );
				curl_close( $curl );

				// レスポンスチェック
				if( $curlinfo[ 'http_code' ] != 200 ){
				    // エラー
					
				    $this->assign('httpisok', "failed");
				}
				// レスポンスのエラーチェック
				parse_str( $response, $data );
				//if( array_key_exists( 'ErrCode', $response ) ){
				if(strpos($response,'ErrCode') !== false){		
					
					echo "dopay is failed";
				    $this->assign('ErrCode', $data['ErrCode']);
				    $this->assign('ErrInfo', $data['ErrInfo']);				
				    // エラー
					$this->assign('ispay', "failed");
					$dbsqlerr--;	    
				}else{
					$resarr = explode('&', $response);
					$idarr = explode('=', $resarr[0]);
					$passarr = explode('=', $resarr[1]);

					$this->accessID=$idarr[1];
					$this->accessPass=$passarr[1];		

					$rtn=$this->nopay();
				
					if($rtn['status']=="failed"){
						$this->assign('ispay', "failed");
						$dbsqlerr--;
						
						
					}else{
						$iscardpayok=1;
					}
					

				}

			}	    	

				if($isb2b==1){//b2b
			
					$updatertn=$this->db->updateBuyingb2b($orderid,$this->accessID,$this->accessPass,$insertid);
					if($updatertn<0){
						$dbsqlerr--;
						echo "updateBuying erro";
					}
					$this->db->updateBuyingb2bMobileflg($ismobile,$insertid);				
				}elseif($isb2b==0){
					if($iscardpayok=="1"){
				
						$updatertn=$this->db->updateBuying($orderid,$this->accessID,$this->accessPass,$insertid);
						if($updatertn<0){
							$dbsqlerr--;
							echo "updateBuyingb2c erro";
						}					
					}else{
						echo "API通信失敗でした";
					}
					$this->db->updateBuyingMobileflg($ismobile,$insertid);	
				}

		

		}
		

		
		
		
		//-----最終処理-----
		if (($this->db->errno)||($dbsqlerr<0)||($rtn['status']=="failed")) {

		    $this->db->pg_rollback();
		    			
		} else {
		    $this->db->pg_commit();
		    $this->db->auto_commit(); 
    		$this->assign('orderid', $orderid);
    		if($this->mullang=="jp"){
    			$this->assign('passwordnum', $delegatetel);
    		}else{
    			$this->assign('passwordnum', $delegatebirthday);
    		}
	   		
	   		$this->assign('ispay', "success");
    		

    		/*-----メール送信処理開始-----*/
    		$areaid=$_GET['areaid'];
    		$mailtxt="";
    		if($areaid==1){
    			$areadir="hk";
    		}elseif($areaid==10){
    			$areadir="ok";
    		}
    		
			if($isb2b==1){//b2b
				
				$tempfile=APP_PATH.'/config/template/mail/'.$areadir.'/b2b_dopay_'.$this->mullang.'.php';
				$companyinfo=$this->db->getCompany($frmcompanyid);
			}else{
				$tempfile=APP_PATH.'/config/template/mail/'.$areadir.'/dopay_'.$this->mullang.'.php';
			}
    						
    		
    		
    		include $tempfile;


    		$mailtxtchg['rsvcode']			=$orderid;
    		$mailtxtchg['unamef']			=$delegatefist;
    		$mailtxtchg['unamel']			=$delegatelast;
    		$mailtxtchg['areaid']	=		$_POST['areaid'];
    		$mailtxtchg['passfreeurl']		=$passfreeurl;
    		
    		$mailtxtchg['loginpwd']			=$delegatebirthday;
    		if($this->mullang=="jp"){
    			$mailtxtchg['loginpwd']			=$delegatetel;
    		}
    		$mailtxtchg['nowdate']			=date("Y/m/d",time()); 
    		$mailtxtchg['productperiod']	=$_POST['period'];
    		$mailtxtchg['godate']			=datelinechg($_POST['caldate']);
    		$mailtxtchg['productname']		=$_POST['couponname_'.$this->mullang];
 			$mailtxtchg['usedateto']		=getNDaysDate($_POST['caldate'],$_POST['period']*1-1);
 			
 			
    		$agtcontact		=$companyinfo[0]['agtcontact_'.$this->mullang];			
		
 			
 			$mailtxtchg['agentcontact']			=$agtcontact;
 
     		//キャンセルポリシー
    		$cancelinfos=$this->db->getCancelByCoupon_id($_POST['couponid']);
    		$cancelstrs="";
    		for($c=0;$c<count($cancelinfos);$c++){
    			$cost=$cancelinfos[$c]['cancelpercent']."%";
    			if($cancelinfos[$c]['cancelmoney']<>""){
    				$cost=$cancelinfos[$c]['cancelmoney'];
    			}
    			$cancelstrs=$cancelstrs.$cancelinfos[$c]['cancelfrom'].$cancelinfos[$c]['cancelfromtitle_'.$this->mullang]."～".$cancelinfos[$c]['cancelto'].$cancelinfos[$c]['canceltotitle_'.$this->mullang]."　".$cost."\r\n";
    			
    		}
    		   		
    		//人数と金額・ポイントの設定
    		$pricepeaplenumber="";
    		$pointsstr="";
    		
    		$optioncnt=$_POST['usagetypecnt'];
    		$totalpeople=0;
    		for($o=0;$o<$optioncnt;$o++){
    			$tmpstr=$_POST['usagetype_'.$o];
    			if($tmpstr==0){
    				continue;
    			}
    			$tmpstrarr 					= explode('-', $tmpstr);
    			$copnb2cmid					=$tmpstrarr[0];
    			$peoplenum					=$tmpstrarr[1];
    			$peopleprice				=$tmpstrarr[2];
    			$peoplepoint				=$tmpstrarr[4];
    			$copnb2cmoneyrtn			=$this->db->getCopnb2cmoneyByCopnb2cmid($copnb2cmid);

    			$pricepeaplenumber=$pricepeaplenumber.$copnb2cmoneyrtn[0]['copnb2cmname_'.$this->mullang]."　".number_format($peopleprice).$config['priceunit_'.$this->mullang]."X".$peoplenum.$config['peopleunit'.$this->mullang]."\r\n";
    			$pointsstr=$pointsstr.$copnb2cmoneyrtn[0]['copnb2cmname_'.$this->mullang]."(".$peoplepoint.")"."X".$peoplenum."\r\n";//ポイントと人数のセット:
    			
    			$totalpeople=$totalpeople+$peoplenum*1;
    		}

    		$mailtxtchg['pricepeaplenumber']=$pricepeaplenumber;
    		$mailtxtchg['pointtxt']			=$pointsstr;
    		$mailtxtchg['totalpeople']		=$totalpeople;
    		$mailtxtchg['totalmoney']		=number_format($_POST['finalmoney']);
    		$mailtxtchg['cancelinfostr']	=$cancelstrs;
    		$mailtxtchg['totalpoint']		=$_POST['finalpointval'];
    	
    		$b2bpath="";
    		if($isb2b==1){//b2b
    			$b2bpath="/b2b";
    		}
    		$cidprm="";
    		if($isb2b==1){//b2b
    			$cidprm="&cid=".$frmcompanyid;
    		}
    		    		
    		
			$mypageurl=$this->_lang.$b2bpath."/mypage?areaid=".$_GET['areaid'].$cidprm;
			$mailtxtchg['passmypageurl']=$mypageurl;
    		
    		
			$resmailpstr="";
			
			foreach($mailptxtchg as $key => $val) {
				$mailpoint	=str_replace("#".$key."#",$val,$mailpoint);
			}
			
			$resmailstr = '';
			
			foreach($mailtxtchg as $key => $val) {
				$mailtxt		=str_replace("#".$key."#",$val,$mailtxt);
			}    		
			
			if(($_POST['finalpointval']=="")||($_POST['finalpointval']==0)){
    			$mailtxt		=str_replace("#pointtxt#","",$mailtxt);
    		}else{
    			
    			$mailtxt		=str_replace("#pointtxt#",$mailpoint,$mailtxt);
    		}
    		
			$mailto = $delegatemail;
			
    		$mailtitlechg['orderid']		=$orderid;
    		$mailtitlechg['productname']	=$_POST['couponname_'.$this->mullang];
    		
 			foreach($mailtitlechg as $key => $val) {
				$title		=str_replace("#".$key."#",$val,$title);
			}  
    					
			$message =$mailtxt;   
			
			$from	="ラボパス";	
			$bcc	=$config['bcc'];
			
			$cid=$_SESSION['sellercompanyid'];
			$companyinfo=$this->db->getCompany($cid);
			$companymail=$companyinfo[0]['companymail'];
						
			if($issendmail==1){
    			$sendmailrtn=	$this->sendmail($mailto,$title,$message,$from,$bcc);

    			$this->db->buyingmaillog($mailto,$bcc,$title,$sendmailrtn);
			}

			$sendmailrtn2=	$this->sendmail($companymail,$title,$message,$from,$bcc);
			//b2bのみ
			if(isset($_SESSION['sellercompanyid'])){
				$this->db->buyingmaillog($companymail,$bcc,$title,$sendmailrtn2);
			}
			
 
			   		
    		/*------メール送信完了--------*/

		}
		//=================== 
		if($isb2b=="1"){
			$b2bpath="/b2b";
		}

		if($isb2b=="0"){
			$b2bpath="";
		}
		$this->assign('mullang', $this->mullang);		
		$this->assign('b2bpath',$b2bpath);
		$this->assign('cid',$_SESSION['sellercompanyid']);		

		
		$this->render();		
    }    
    
    public function insertBuyingusage($postprm,$peoplecnt,$buser_id,$isdelegate,$isb2b,$insertid,$copnb2cmid,$copnb2cnum,$copnb2cprice,$pointcal_calid,$pointcalval){



		$rtn=0;
	//	//代表者情報を入れる
		if($isdelegate==1){

			$dtemparr = explode('-', $postprm['coupontype']);
			$d_copnpoint_id=$dtemparr[0];//ポイントID

	    
			$rtnchktype=$this->chkuseagetype($postprm,$postprm['coupontype']);
			$dataarr=$rtnchktype['data'];

			$fl_copnpoint_id	=$dataarr[0];//クポンID
			$copnb2cmid			=$dataarr[1];//金額ID
			$copnb2cnum			=0;//$dataarr[x];//人数//問題があるので、廃置する
			$pointcal_calid		=$dataarr[3];//ポイントID//追加部分
			$pointcalval		=$dataarr[4];//ポイント
			$pointcalcost		=$dataarr[5];//ポイント原価
			$moneycal_calid		=$dataarr[6];//金額ID
			$copnb2cprice		=$dataarr[7];//金額
			$copnb2ccost		=$dataarr[8];//金額原価
			$kubunnkey			=$dataarr[9];//年齢区分キー

			
												
			if($isb2b==1){//b2b
				$insertedid=$this->db->addBuyingusageb2b($insertid,$buser_id,$isdelegate,$fl_copnpoint_id,$copnb2cmid,$copnb2cnum,$moneycal_calid,$copnb2cprice,$copnb2ccost,$pointcal_calid,$pointcalval,$pointcalcost);
				if($insertedid<0){
					$rtn--;
					echo "addBuyingusageb2b/b2b erro";
					countinue;
				}
	    
			}elseif($isb2b==0){//b2c
		
					$insertedid=$this->db->addBuyingusageb2c($insertid,$buser_id,$isdelegate,$fl_copnpoint_id,$copnb2cmid,$copnb2cnum,$moneycal_calid,$copnb2cprice,$copnb2ccost,$pointcal_calid,$pointcalval,$pointcalcost);
					if($insertedid<0){
						$rtn--;
						echo "addBuyingusageb2c/b2b erro";
						countinue;
					}

						
			}
			
		}else{//同行者情報処理
							
			$typecnt4for=$peoplecnt*1-1;
			
			for($v=0;$v<$typecnt4for;$v++){//同行者だけLOOP
				$fl_copnpoint_id="";

				$followertypestr=$postprm['followertype_'.$v];
				
				$rtnchktype=$this->chkuseagetype($postprm,$followertypestr);
				
				$dataarr=$rtnchktype['data'];

				
				$fl_copnpoint_id	=$dataarr[0];//クポンID
				$copnb2cmid			=$dataarr[1];//金額ID
				$copnb2cnum			=0;//$dataarr[x];//人数//問題があるので、廃置する
				$pointcal_calid		=$dataarr[3];//ポイントID//追加部分
				$pointcalval		=$dataarr[4];//ポイント
				$pointcalcost		=$dataarr[5];//ポイント原価
				$moneycal_calid		=$dataarr[6];//金額ID
				$copnb2cprice		=$dataarr[7];//金額
				$copnb2ccost		=$dataarr[8];//金額原価//追加部分
				$kubunnkey			=$dataarr[9];//年齢区分キー
				
				
				
				
				$temparr = explode('-', $postprm['followertype_'.$v]);

				
				$fl_copnpoint_id=$temparr[0];

				if($fl_copnpoint_id==""){
					continue;
				}
				if($isb2b==1){//b2b

				
					$insertedid=$this->db->addBuyingusageb2b($insertid,$buser_id,$isdelegate,$fl_copnpoint_id,$copnb2cmid,$copnb2cnum,$moneycal_calid,$copnb2cprice,$copnb2ccost,$pointcal_calid,$pointcalval,$pointcalcost);
					if($insertedid<0){
						$rtn--;
						echo "addBuyingusageb2b/b2b erro";
						countinue;
					}

				}elseif($isb2b==0){//b2c
				 
					  //$insertedid=$this->db->addBuyingusageb2c($insertid,$buser_id,$isdelegate,$fl_copnpoint_id,$copnb2cmid,$copnb2cnum,$copnb2cprice,$pointcal_calid,$pointcalval);
						$insertedid=$this->db->addBuyingusageb2c($insertid,$buser_id,$isdelegate,$fl_copnpoint_id,$copnb2cmid,$copnb2cnum,$moneycal_calid,$copnb2cprice,$copnb2ccost,$pointcal_calid,$pointcalval,$pointcalcost);
						if($insertedid<0){
							$rtn--;
							echo "addBuyingusageb2c/b2b erro";
							countinue;
						}
	
				}
			}	
		
		}

				

		return $rtn;
				    
    }
    public function chkuseagetype($postprm,$selectedtypestr){
    	$rtn['status']=0;
    	$usagetypecnt=$postprm['usagetypecnt'];
		for($t=0;$t<$usagetypecnt;$t++){//区分毎で照会
	
			$tmp=$postprm['usagetype_'.$t];///////////////////////////これを使う場所はここだけそう、使うfieldは区分keyのみそうです。//////////////////////////////////////////////////////////////
			$usagetypearr = explode('-', $tmp);
			$rtnarr = explode('-', $selectedtypestr);
			
			$ptname			=$usagetypearr[5];//区分key→これ本当にkeyなのか？要確認

			$arr = explode($ptname, $selectedtypestr);
			$arrleng=count($arr);
		
			if($arrleng>=2){ 
				$rtn['status']=1;
				$rtn['data']=$rtnarr;
				return $rtn;
			}

			
		}		
		
		
		
    
    }
    
    
    public function payappend($id){
    	global $config;
    	
    	if(isset($_REQUEST['b_id'])){
    		$id=$_REQUEST['b_id'];
    	}    
		$this->creditCardToken=$_REQUEST['creditCardToken'];
		$this->cardDateMonth=$_REQUEST['cardDateMonth'];
		$this->cardDateYear=$_REQUEST['cardDateYear'];
		$this->ccSecurityCode=$_REQUEST['ccSecurityCode'];
		
		$finalpointval=$_REQUEST['finalpointval'];
		$finalmoney=$_REQUEST['finalmoney'];
		$b2bflg=$_REQUEST['b2bflg'];
		$debugdir=$config['debugdir'];


		//-----最初処理-----
		$this->db->noauto_commit(); 
		$dbsqlerr=0;
		//===================	
		
		$insertid=$this->db->buyappend($id,$finalmoney,$finalpointval,$b2bflg);
		if($insertid<0){
			$dbsqlerr--;
			echo "addBuying erro";
		}
		$areaid=$_GET['areaid'];

		if($dbsqlerr>=0){
	
			//連番作成
			$num=str_pad($insertid,8,"0",STR_PAD_LEFT); 
			
			$testflg="";
			if($config['isdebugrun']===1){
				$testflg=$config['testflg'];
			}
			$tmpid=$testflg."OA".$num;//連番コード
		
			$orderid=$tmpid;//オーダーID 
			$itemcode=$this->itemcode;//商品コード(クポンコード)----------------
			
			$amount=$finalmoney;//利用金額
			$tax=$config['tax'];//税送料------------------------------
			
			
			//仮値終了
			$this->orderID=$orderid;
	    	$this->assign('httpisok', "success");
			// リクエストコネクションの設定
			$curl=curl_init();
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_URL, $this->payurl );//-------------------
			$param = [
			    'ShopID'       => $this->yourShopId,
			    'ShopPass'     => $this->yourShopPassword,
			    'OrderID'      => $orderid,
			    'JobCd'        => $config['JobCd'],
			    'ItemCode'     => $itemcode,
			    'Amount'       => $amount,
			    'Tax'          => $tax,
			    'TdFlag'       => $config['TdFlag'],
			    'TdTenantName' => $config['TdTenantName'],
			    'Tds2Type'     => $config['Tds2Type']
			];
			// リクエストボディの生成
			curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $param ) );

			// リクエスト送信
			$response = curl_exec( $curl );

			$curlinfo = curl_getinfo( $curl );
			curl_close( $curl );

			if(strpos($response,'ErrCode') !== false){		
				
				echo "dopay is failed";
			    // エラー
				$this->assign('ispay', "failed");
				$dbsqlerr--;	    
			}
			$resarr = explode('&', $response);
			$idarr = explode('=', $resarr[0]);
			$passarr = explode('=', $resarr[1]);

			$accessid=$idarr[1];
			$accesspass=$passarr[1];	
			
			
			$rtnupdate=$this->db->updateBuyappend($this->orderID,$accessid,$accesspass,$insertid);
			if($rtnupdate<0){
				$dbsqlerr--;
				echo "updateBuyappend erro";
			}
			
			
			$rtnupdatebuymning=$this->db->updateBuying_FinalmoneyByBuyid($finalmoney,$id);
			if($rtnupdatebuymning<0){
				$dbsqlerr--;
				echo "updateBuyappend money erro";
			}	
			
			$rtnupdatebuying=$this->db->updateBuying_FinalpointvalByBuyid($finalpointval,$id);
			if($rtnupdatebuying<0){
				$dbsqlerr--;
				echo "updateBuyappend point erro";
			}			
			// レスポンスチェック
			if( $curlinfo[ 'http_code' ] != 200 ){
			    // エラー

			    $this->assign('httpisok', "failed");
			}
			// レスポンスのエラーチェック
			//parse_str( $response, $data );
			//if( array_key_exists( 'ErrCode', $response ) ){
			if(strpos($response,'ErrCode') !== false){		

				echo "payappend is failed";
				
			    // エラー
				$this->assign('ispay', "failed");
				$dbsqlerr--;	    
			}else{
				$resarr = explode('&', $response);
				$idarr = explode('=', $resarr[0]);
				$passarr = explode('=', $resarr[1]);

				$this->accessID=$idarr[1];
				$this->accessPass=$passarr[1];		
		
				$rtn=$this->nopay();
			    
				if($rtn['status']=="failed"){
					$this->assign('ispay', "failed");
					$dbsqlerr--;
					
					
				}
			}
		}		
		
		//-----最終処理-----
		if (($this->db->errno)||($dbsqlerr<0)||($rtn['status']=="failed")) {

		    $this->db->pg_rollback();
		    			
		} else {
		    $this->db->pg_commit();
		    $this->db->auto_commit(); 
    		$this->assign('orderid', $orderid);
	   		$this->assign('delegatetel', $delegatetel);
	   		$this->assign('ispay', "success");
	   
	   }					
		$this->jumpto($debugdir."/".$this->_lang."/mypage/main/".$id."?areaid=".$areaid);
    }    
    
    public function nopay()
    {
    	global $config;

		    	
    	$this->assign('httpisok', "success");    
		// リクエストコネクションの設定
		$curl=curl_init();
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_URL, $this->nopayurl);
		// 本人認証サービス使用,トークン使用時
		$param = [
		    'AccessID'        => $this->accessID,
		    'AccessPass'      => $this->accessPass,
		    'OrderID'         => $this->orderID,
		    'Method'          => $this->cardmethod,
		    //'PayTimes'        => '2',
		    'Token'           => $this->creditCardToken,
		    'TokenType'       => $config['TokenType'],
		    'RetUrl'          => 'https://example.com/xxxxx'
		];
		// リクエストボディの生成
		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $param ) );

		// リクエスト送信
		$response = curl_exec( $curl );
		$curlinfo = curl_getinfo( $curl );
		curl_close( $curl );
		
		// レスポンスチェック
		if( $curlinfo[ 'http_code' ] != 200 ){
		    // エラー
		    $this->assign('httpisok', "failed");
		}
	
		// レスポンスのエラーチェック
		
		parse_str( $response, $data );
		if( array_key_exists( 'ErrCode', $data ) ){
		    // エラー

				$resarr = explode('&', $response);
				$ErrCodearr = explode('=', $resarr[0]);
				$ErrInfoarr = explode('=', $resarr[1]);
				
		    $rtn['info']=$response;
		    $rtn['status']="failed";
		    $rtn['ErrCode']=$ErrCodearr['1'];
		    $rtn['ErrInfo']=$ErrInfoarr[1];
		    
		}else{

			$rtn['status']="success";
		}
		return $rtn;
        
    }
    
    //全額返金

    public function cancelpay($accid,$pass,$newmoney)
    {	
    	global $config;

		$orderid=$_GET['orderid'];
		$itemcode=$this->itemcode;//商品コード
		$amount=$newmoney;//利用金額（予約した金額と一致しないと行けない）
		$accessid=$accid;
		$accesspass=$pass;
		
		
		$this->orderID=$orderid;
    	$this->assign('httpisok', "success");
		
		$curl=curl_init();
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_URL, $this->cancelpayurl );
		$param = [
		    'ShopID'       => $this->yourShopId,
		    'ShopPass'     => $this->yourShopPassword,
		    'AccessID'     => $accessid,
		    'AccessPass'   => $accesspass,
		    'JobCd'        => $config['JobCXCd'],
		    'Amount'       => $amount
		];

		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $param ) );

		$response = curl_exec( $curl );

		$curlinfo = curl_getinfo( $curl );
		curl_close( $curl );

		if( $curlinfo[ 'http_code' ] != 200 ){

		    $this->assign('httpisok', "failed");
		}
		parse_str( $response, $data );
		if(strpos($response,'ErrCode') !== false){		

			$rtn['status']="erro";
			$rtn['data']=$data;
			
			return $rtn;		    
		}else{
			$rtn['status']="success";
			return $rtn;
		}
		$resarr = explode('&', $response);
		$idarr = explode('=', $resarr[0]);
		$passarr = explode('=', $resarr[1]);

		$this->accessID=$idarr[1];
		$this->accessPass=$passarr[1];		

    }        



    
    public function changepay($id,$pass,$newmoney)
    {	
    	global $config;

		$orderid=$_GET['orderid'];
		$itemcode=$this->itemcode;//商品コード
		$amount=$newmoney;//$_GET['amount'];//利用金額（予約した金額と一致しないと行けない）
		$accessid=$id;
		$accesspass=$pass;
		
		//仮値終了
		$this->orderID=$orderid;
    	$this->assign('httpisok', "success");
		// リクエストコネクションの設定
		$curl=curl_init();
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_URL, $this->chgpayurl );
		$param = [
		    'ShopID'       => $this->yourShopId,
		    'ShopPass'     => $this->yourShopPassword,
		    'AccessID'     => $accessid,
		    'AccessPass'   => $accesspass,
		    'Method'          => $this->cardmethod,
		    'JobCd'        => CAPTURE,
		    'Amount'       => $amount
		];

		// リクエストボディの生成
		curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $param ) );

		// リクエスト送信
		$response = curl_exec( $curl );

		$curlinfo = curl_getinfo( $curl );
		curl_close( $curl );

		// レスポンスチェック
		if( $curlinfo[ 'http_code' ] != 200 ){
		    // エラー

		    $this->assign('httpisok', "failed");
		}

		// レスポンスのエラーチェック
		parse_str( $response, $data );
		//if( array_key_exists( 'ErrCode', $response ) ){
		if(strpos($response,'ErrCode') !== false){		
		    // エラー
			
			$rtn['status']="erro";
			$rtn['data']=$data;
			
			return $rtn;	    
		}else{
			$rtn['status']="success";
			return $rtn;
		}
		$resarr = explode('&', $response);
		$idarr = explode('=', $resarr[0]);
		$passarr = explode('=', $resarr[1]);

		$this->accessID=$idarr[1];
		$this->accessPass=$passarr[1];		


    }    
    public function  cancelcomplete($id=0){
    	global $config;

		$areaid=GET['areaid'];

		$isb2b=$this->_b2bflg;
		
		if($isb2b==1){
			$b2bpath="/b2b";	
		}elseif($isb2b==0){
			$b2bpath="";
		}
		if($isb2b==1){
			if(!isset($_SESSION['isadmin'])){
			//	echo "b2bキャンセルできない";
			//	exit;
			}

		}
		
    	if($config['isdebugrun']==0){
    		$this->oneaccess("cancelcomplete".$id);
    	}
        if($id==0){
        	$msg="キャンセル用データが失う";
			$this->assign('msg',$msg);	
	        $this->render();
        	exit;
        }
		if($isb2b==1){//b2b場合
			$iscancelrtn=$this->db->getBuyingb2bIsCancelByBuyid($id);	
		}elseif($isb2b==0){
			$iscancelrtn=$this->db->getBuyingIsCancelByBuyid($id);
		}
		        
  
        
        if(count($iscancelrtn)>0){
        	echo "もうキャンセルしました";
        	exit;
        }
        
        //パスを使ってるか
        $getcancelreserves=$this->db->getNoCancelReserves($id,$isb2b);
        if(count($getcancelreserves)>0){
        	
        	echo "予約を全部キャンセルしてください。";
        	exit;
        
        }
        $getusedfrees=$this->db->getusedreservefree($id,$areaid,$isb2b);
        if(count($getusedfrees)>0){
        	
        	echo "予約を全部キャンセルしてください。";
        	exit;
        
        }

		if($isb2b==1){
			$buyinginfo=$this->db->getBuyingb2bByBuyid($id);;	
		}elseif($isb2b==0){
			$buyinginfo=$this->db->getBuyingByBuyid($id);
		}
		
		$urllang=$buyinginfo[0]['urllang'];

    	$coupon_id=$buyinginfo[0]['coupon_id'];
    	
    	$couponinfo=$this->db->getCoupon($coupon_id);
    	
    	
    	$cancelinfo=$this->db->getCancelByCoupon_id($coupon_id);

		//-----最初処理-----
		$this->db->noauto_commit(); 
		$dbsqlerr=0;
		//-------
		
		
		$now = time();
		$startdate=$buyinginfo[0]['caldate'];
		
		//echo "  [ ($startdate) $your_date  - $now   ] ";
		

		if($config['isdebugrun']===1){

			$now=strtotime($config['testdate']);
		}
	
		$your_date = strtotime($startdate);
		$datediff = $your_date- $now ;
		$difdate= ceil($datediff / (60 * 60 * 24));		    
			
    	$newmoney=$_GET['cacelmoney'];
    	//キャンセル日付チェック
     	$endday=getNDaysSqlDate($buyinginfo[0]['caldate'],$buyinginfo[0]['period']);    	
    	
		$datediffchk = strtotime($endday)- $now ;
		$difdatechk= ceil($datediffchk / (60 * 60 * 24));		    	
		if($datediffchk<0){
			echo "キャンセル期限切れ";exit;
		}

    	//キャンセル手数料チェック
		$ispercent=1;
		$calculation=0;//ディフォルト100%手数料
						
		for($i=0;$i<count($cancelinfo);$i++){
			$costval=$cancelinfo[$i]['cancelpercent'];
			
			if($cancelinfo[$i]['cancelmoney']<>""){
				$costval=$cancelinfo[$i]['cancelmoney'];
			
				
			}
			
			$fromdate= $cancelinfo[$i]['cancelfrom'];
			$todate=  $cancelinfo[$i]['cancelto'];
	
			if(($todate>=$difdate)&&($difdate>=$fromdate)){
				
				if($cancelinfo[$i]['cancelmoney']<>""){
					$ispercent=0;
				}else{
					$ispercent=1;
				}

				$calculation=$costval;
				continue;
				
			}    	
		}    
		//echo "---- $calculation -----";
			
		if($ispercent){
	
			$cacelmoney=processmoney($buyinginfo[0]['finalmoneylog']*($calculation/100));
			
		}else{
			$cacelmoney=($calculation);
		}    	
    	

    	if($newmoney<>$cacelmoney){
    		echo "キャンセル手数料が正しくない。";
    		exit;
    	}

    	
    	$accid= $buyinginfo[0]['accessid'];
    	$pass= $buyinginfo[0]['accesspass'];
    	//キャンセル
    	if($cacelmoney>0){
    	
		
    		$buyid=$buyinginfo[0]['buyid'];
    		

			if($isb2b==1){
				
				/* B2Bはキャンセルする時に、入金していないので、下記処理は要らない（入金後でのキャンセルパターンがないと考える）
				$updtrtn=$this->db->updateBuyingb2bFinalmoneyvalBybuyid($newmoney,$buyid);
				
				
				if($updtrtn<0){

					$dbsqlerr--;
				}
				*/
				
			}elseif($isb2b==0){
			
	    		$rtn=$this->changepay($accid,$pass,$newmoney);
				if($rtn['status']=="success"){
					$updtrtn=$this->db->updateBuyingFinalmoneyvalBybuyid($newmoney,$buyid);
					
					if($updtrtn<0){

						$dbsqlerr--;
					}
				}else{
					$dbsqlerr--;
				}	 	
				
				
			}
	        
    		if($updtrtn<0){
    			$dbsqlerr--;
    		}


    	}else{
    		//返品
    		
    		$finalmoney=$buyinginfo[0]['finalmoney'];
    		$rtn=$this->cancelpay($accid,$pass,$finalmoney);
			
    		//キャンセル後buyingの金額も0にする
		
    		$buyid=$buyinginfo[0]['buyid'];
			if($isb2b==1){
			
				$updtrtn=$this->db->updateBuyingb2bFinalmoneyvalBybuyid($finalmoney,$buyid);
				
				
			}elseif($isb2b==0){
			
	    		if($rtn['status']=="success"){
	    			$updtrtn=$this->db->updateBuyingFinalmoneyvalBybuyid($finalmoney,$buyid);
	    		}else{
					
					$dbsqlerr--;
					
	    		}    
				
			}
				    		
    		if($updtrtn<0){
    			$dbsqlerr--;
    		}		
    		    		
    	}
    	
		if($isb2b==1){
			$canbuyrtn=	$this->db->cancelBuyingb2b($id);
		}elseif($isb2b==0){
			$canbuyrtn=	$this->db->cancelBuying($id);
		}
				    	
		if($canbuyrtn<0){
			$dbsqlerr--;
		}	
    	
    	
		if($isb2b==1){
			$rtncanceldayfee=$this->db->updateBuyingb2bCanceldayCancelfee($id,$cacelmoney);
		}elseif($isb2b==0){
			$rtncanceldayfee=$this->db->updateBuyingCanceldayCancelfee($id,$cacelmoney);
		}
				    	
		if($rtncanceldayfee<0){
			$dbsqlerr--;
		}	    	

		//-----最終処理-----
		if (($this->db->errno)||($dbsqlerr<0)) {

		    $this->db->pg_rollback();
		   
		    
		    $this->assign('status',"faild");
		    $this->assign('data',$rtn['data']);
		    		
		} else {
			
		    $this->db->pg_commit();
		    $this->db->auto_commit(); 
		  
		    $this->assign('status',"success");
			$this->assign('id',$id);
			
			
			
    		/*-----メール送信処理開始-----*/
    		$mailtxt="";
    		$areaid=$_GET['areaid'];
    		$mailtxt="";
    		if($areaid==1){
    			$areadir="hk";
    		}elseif($areaid==10){
    			$areadir="ok";
    		}
    		
    		if($isb2b==1){
    			$tempfile=APP_PATH.'/config/template/mail/'.$areadir.'/b2b_cancelcomplete_'.$urllang.'.php';
    		}else{
    			$tempfile=APP_PATH.'/config/template/mail/'.$areadir.'/cancelcomplete_'.$urllang.'.php';
    		}
    		
    		
    		include $tempfile;
    		    		
    		
    		$mailtxtchg['rsvcode']			=$buyinginfo[0]['serialnum'];
    		$mailtxtchg['unamef']			=$buyinginfo[0]['delegatefist'];
    		$mailtxtchg['unamel']			=$buyinginfo[0]['delegatelast'];
    									
    		$mailtxtchg['loginpwd']			=$buyinginfo[0]['delegatebirthday'];
    		if($this->mullang=="jp"){
    			$mailtxtchg['loginpwd']		=$buyinginfo[0]['delegatetel'];
    		}
    		$delegatemail					=$buyinginfo[0]['delegatemail'];
    		$mailtxtchg['usedatefrom']		=str_replace("-","/",$buyinginfo[0]['caldate']);
    		$endday=getNDaysSqlDate($buyinginfo[0]['caldate'],$buyinginfo[0]['period']*1-1);    
    		$mailtxtchg['usedateto']		=str_replace("-","/",$endday);
    		$couponname=$couponinfo[0]['couponname_'.$urllang];
    		
    		$agtcontact=$buyinginfo[0]['agtcontact_'.$urllang];
    		
    		$rsvdate = date('Y/m/d', time());  
    		$mailtxtchg['rsvdate']		=$rsvdate;
    		
    		
    		$mailtxtchg['couponname_'.$this->mullang]			=$buyinginfo[0]['couponname_'.$urllang];
    		
    		$mailtxtchg['passname']		=$couponname;
    		
    		
    		$mailtxtchg['finalmoney']	=number_format($buyinginfo[0]['finalmoney']);
    		
    		$mailtxtchg['rsvcxlrefund']	=number_format($buyinginfo[0]['finalmoney']*1-$cacelmoney*1);
			$mailtxtchg['rsvcxlprice']	=number_format($cacelmoney);
    		
    		$mailtxtchg['agentcontact']		=$agtcontact;
    		
    		$b2bpath="";
    		if($isb2b==1){//b2b
    			$b2bpath="/b2b";
    		}			
			$mypageurl=$this->_lang.$b2bpath."/mypage?areaid=".$_GET['areaid'];
			$mailtxtchg['passmypageurl']=$mypageurl;
			foreach($mailtxtchg as $key => $val) {
				$mailtxt		=str_replace("#".$key."#",$val,$mailtxt);
			}    	
			$mailto = $delegatemail;
			
			//$title_ok = "[沖縄ラボPass] パス取消完了のお知らせ(予約番号：".$buyinginfo[0]['serialnum'].")";
			//$title_hk = "[沖縄ラボPass] パス取消完了のお知らせ(予約番号：".$buyinginfo[0]['serialnum'].")";
			
    		//if($areaid==1){
    		//	$title=$title_hk;
    		//}elseif($areaid==10){
    		//	$title=$title_ok;
    		//}

    		$mailtitlechg['serialnum']		=$buyinginfo[0]['serialnum'];
    		
 			foreach($mailtitlechg as $key => $val) {
				$title		=str_replace("#".$key."#",$val,$title);
			}  
			    					
			$message =$mailtxt;   
			
			$from	="ラボパス";	
			$bcc	=$config['bcc'];
			
    		$this->sendmail($mailto,$title,$message,$from,$bcc);
    		
    		
    		    					
		}
		//=================== 
		if(isset($_POST['cid'])){
			
			$cid=$_POST['cid'];
			$cidprm="&cid=".$cid;;
			$this->assign('cid',$cid);	
			$this->assign('cidprm',$cidprm);	
			
		}
		$jumpurl="";
		$needjumpto=0;
		if(isset($_GET['jumpto'])){
			$jumpurl=str_replace(",","&",$_GET['jumpto']);;;
			$needjumpto=1;
		}

		$this->assign('jumpto',$jumpurl);	
		$this->assign('needjumpto',$needjumpto);	
		
		$this->assign('b2bpath',$b2bpath);	
		$this->assign('id',$id);	
        $this->render();
    }
}