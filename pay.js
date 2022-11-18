////https://japapass.com/__debug/ja/pay/index?startdate=2021-01-23&enddate=2021-03-28

var num1="";
var num2="";
var num3="";
var num4="";
var getvars=getUrlVars();
var submitok=true;


          

/*------------------------------------------------------
* クレジットトークン化 callback b2c
-----------------------------------------------------*/
var tokenCallbackFunc = function(result){

	//error
	var errTxt = '';
	var errTxtSuffix = [
		jsmsg['errtxtsuffix0erro'],
		jsmsg['errtxtsuffix1erro']
	];

	if(!result || !result.tokenObject.token[0]) {
		errTxt = jsmsg['confirmcarderro'] + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) < 150) {
		errTxt = '［'+jscommonmsg['erro']+'（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) >= 150) {
		errTxt = '［'+jscommonmsg['erro']+'（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[1];
	}

	//show error
	if(errTxt) {
		$('#erromsg').html(errTxt);
		return false;
	}

	//success
	if(!errTxt) {
		$('input[name="creditCardToken"]').val(result.tokenObject.token[0]);
		 var dirval=Request['dir'];
		 var lang=Request['lang'];
		 var url=baseurl+"&dir="+dirval+"&lang="+lang;
		 $('form').attr('action', url);
		 $('form').submit();
	}

};

function notokenCallbackFunc(){
		 var url="./confirm";
		 $('form').attr('action', url);
		 $('form').submit();

}

$(function(){
          	
	//クレジットカード番号フォーマット     
	$('#cardNum').focusout(function(){
			var fullnum;
			var tmp=$("#cardNum").val().trim();
			var last4="****";
			if(tmp){
				
				 fullnum=tmp.replace(/[^0-9]/ig,"");

				
				if(fullnum.length==16) {
					
					 num1=fullnum.substring(0,4);
					 num2=fullnum.substring(4,8);
					 num3=fullnum.substring(8,12);
					 num4=fullnum.substring(12,16);
					$("#cardNum").val(num1+"-"+num2+"-"+num3+"-"+num4);
					last4=num4;

				}
				if(fullnum.length==15) {
					 num1=fullnum.substring(0,4);
					 num2=fullnum.substring(4,10);
					 num3=fullnum.substring(10,15);
					$("#cardNum").val(num1+"-"+num2+"-"+num3);
					
					last4=num3;

				}
				if(fullnum.length==14) {
					 num1=fullnum.substring(0,4);
					 num2=fullnum.substring(4,10);
					 num3=fullnum.substring(10,14);
					$("#cardNum").val(num1+"-"+num2+"-"+num3);
					last4=num3;
				}
				sessionStorage.setItem("last4", last4); 

				
			}		


	});	

	$('input:radio').bind("click",function(){
		
		var objid=$(this).attr('id');
		var objname=$(this).attr('name');
		var objval=$(this).val();
		$("input[name='"+objname+"'][checked]").each(function(index,domEle){
		    $(this).attr("checked",false);
		});
		
		$("#"+objid).attr("checked",true);

	});
	
	
});

 

function chkinputs(){
		
		var delegatefist=$("#delegatefist").val();
		if(delegatefist==""){
			alert(jsi18n['pay']['entername']);
			return false;
		}
		var delegatelast=$("#delegatelast").val();
		if(delegatelast==""){
			alert(jsi18n['pay']['entername']);
			return false;
		}
		var sex= $("input[name='sex'][checked]").val();
		
		if(sex==undefined){
			alert(jsi18n['pay']['selectsex']);
			return false;
		}
		var age= $("#age").val();
		if(age==""){
			alert(jsi18n['pay']['enterage']);
			return false;
		}		
		//var coupontype=$("#coupontype").val();
		//if(coupontype==""){
		//	alert(jsi18n['pay']['selectcategory']);
		//	return false;
		//}
		
		var address=$("#address").val();
		if(address==""){
			alert(jsi18n['pay']['enteraddress']);
			return false;
		}
		
		if(mullang=="jp"){
			var delegatetel=$("#delegatetel").val();
			if(delegatetel==""){
				alert(jsi18n['pay']['entertelphone']);
				return false;
			}
		}else{
			var delegatebirthday=$("#delegatebirthday").val();
			if(delegatebirthday==""){
				alert(jsi18n['pay']['enterbirthday']);
				return false;
			}
		
		}

		var email=$("#email").val();
		if(email==""){
			alert(jsi18n['pay']['entermail']);
			return false;
		}
		
		
		var chkrtn=usertypechk();
		if(chkrtn=="nook"){
			return false;
		}
		
		


			
			
	  	if(isb2b!="1"){
			var cardObj = {
				cardno: num1+num2+num3+num4,
				expire: $('#cardDateYear').val()+$('#cardDateMonth').val(),
				securitycode: $('#ccSecurityCode').val(),
				holdername: '',
				tokennumber: 1
			};
			sessionStorage.setItem("cardDateMonth", $('#cardDateYear').val()+"/"+$('#cardDateMonth').val());

			console.log(cardObj);
			Multipayment.getToken(cardObj,tokenCallbackFunc);

	  	}else{
	  		notokenCallbackFunc();
	  	}//
}


function chkArrangeInputs(){
		
			var cardObj = {
				cardno: num1+num2+num3+num4,
				expire: $('#cardDateYear').val()+$('#cardDateMonth').val(),
				securitycode: $('#ccSecurityCode').val(),
				holdername: '',
				tokennumber: 1
			};
			sessionStorage.setItem("cardDateMonth", $('#cardDateYear').val()+"/"+$('#cardDateMonth').val());

			console.log(cardObj);
			Multipayment.getToken(cardObj,tokenCallbackArrangeFunc);

}


function chkappend(){
			
			if((num1=="")||(num2=="")||(num3=="")||(num4=="")){
				alert(jsi18n['cardpay']['cardnumerro']);
				return;
			}
			if($("#finalpointval").val()==0){
				alert(jsi18n['cardpay']['pointnumerro']);
				return;
			}
			if(($('#cardDateYear').val()=="")||($('#cardDateMonth').val()=="")){
				alert(jsi18n['cardpay']['dateofexpiryerro']);
				return;
			}
			if($('#ccSecurityCode').val()==""){
				alert(jsi18n['cardpay']['securitycodeerro']);
				return;
			}
			
			var cardObj = {
				cardno: num1+num2+num3+num4,
				expire: $('#cardDateYear').val()+$('#cardDateMonth').val(),
				securitycode: $('#ccSecurityCode').val(),
				holdername: '',
				tokennumber: 1
			};
			sessionStorage.setItem("cardDateMonth", $('#cardDateYear').val()+"/"+$('#cardDateMonth').val());

			console.log(cardObj);
			Multipayment.getToken(cardObj,tokenCallbackFuncAppend);
}


/*------------------------------------------------------
* クレジットトークン化 callback b2c
-----------------------------------------------------*/
var tokenCallbackFunc = function(result){

	//error
	var errTxt = '';
	var errTxtSuffix = [
		'入力内容に誤り等がないかどうか再度ご確認くださいませ。',
		'大変お手数ではございますが、エラーの内容と併せて弊社までお問い合わせ頂けますようお願い致します。'
	];
	
	if(result.tokenObject==undefined){
		errTxt = 'クレジットカード番号を入れてください。';

	}else if(!result || !result.tokenObject.token[0]) {
		errTxt = '［エラー（999）］クレジットカードの確認時にエラーが発生致しました。' + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) < 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) >= 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[1];
	}

	//show error
	if(errTxt) {
		//$('#tokenErrorBox').html(errTxt);
		alert(errTxt);
		return false;
	}

	//success
	if(!errTxt) {
		$('input[name="creditCardToken"]').val(result.tokenObject.token[0]);

		 var url="./confirm";
		 $('form').attr('action', url);
		 $('form').submit();
	}

};
var tokenCallbackArrangeFunc = function(result){

	//error
	var errTxt = '';
	var errTxtSuffix = [
		'入力内容に誤り等がないかどうか再度ご確認くださいませ。',
		'大変お手数ではございますが、エラーの内容と併せて弊社までお問い合わせ頂けますようお願い致します。'
	];
	
	if(result.tokenObject==undefined){
		errTxt = 'クレジットカード番号を入れてください。';

	}else if(!result || !result.tokenObject.token[0]) {
		errTxt = '［エラー（999）］クレジットカードの確認時にエラーが発生致しました。' + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) < 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) >= 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[1];
	}

	//show error
	if(errTxt) {
		//$('#tokenErrorBox').html(errTxt);
		alert(errTxt);
		return false;
	}

	//success
	if(!errTxt) {
		$('input[name="creditCardToken"]').val(result.tokenObject.token[0]);

		 var url="./arrangesystem_pay/confirm";
		 $('form').attr('action', url);
		 $('form').submit();
	}

};

/*------------------------------------------------------
* クレジットトークン化 callback b2c
-----------------------------------------------------*/
var tokenCallbackFuncAppend = function(result){

	//error
	var errTxt = '';
	var errTxtSuffix = [
		'入力内容に誤り等がないかどうか再度ご確認くださいませ。',
		'大変お手数ではございますが、エラーの内容と併せて弊社までお問い合わせ頂けますようお願い致します。'
	];

	if(!result || !result.tokenObject.token[0]) {
		errTxt = '［エラー（999）］クレジットカードの確認時にエラーが発生致しました。' + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) < 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[0];
	}
	else if(result.resultCode != '000' && Number(result.resultCode) >= 150) {
		errTxt = '［エラー（'+result.resultCode+'）］' + err_gmo_credit_token[result.resultCode] + errTxtSuffix[1];
	}

	//show error
	if(errTxt) {
		$('#tokenErrorBox').html(errTxt).show();
		return false;
	}

	//success
	if(!errTxt) {
	
	
        var  confirmAction = confirm(jsi18n['cardpay']['doyoupay']);
        if (confirmAction) {
        
			$('input[name="creditCardToken"]').val(result.tokenObject.token[0]);
			
			
			 var url=$("#payurl").val();
			 $('#pointappendfm').attr('action', url);
			 $('#pointappendfm').submit();        
        
        
        }
        	

	}

};



