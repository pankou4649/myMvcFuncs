
/*
strs=>id,id,id|判断基準,判断基準,判断基準
myValidates("id,name,mail|num,blank,mail");
*/
function myValidates(strs){
	var strvaliarr=strs.split("|");
	var strarr=strvaliarr[0].split(",");
	var valiarr=strvaliarr[1].split(",");
	var theval;
	
	for(var i=0;i<strarr.length;i++){
		var objs = document.getElementsByName(strarr[i]);
		if(objs==undefined){
			continue;
		}
		if(objs[0]==undefined){
			continue;
		}		
		if(objs.length>1){
			alert("唯一の名前じゃないと、判断できません");
		}else{
			theval=objs[0].value;
		
		}
		
		switch (valiarr[i]) {
			case 'num': 
				var tmp=isNumberChar(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "数字のみ";
					}
				break
			case 'mail': 
				var tmp =ValidateEmail(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "メールが正しくない";
					}
				break				 
			case 'blank': 
				var tmp=blankcheck(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
					
						return "内容がない";
					}
				break	
			case 'numalphabet': 
				var tmp=numAlphabet(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "数字とアルファベットのみ";
					}
				break
			case 'tel': 
				var tmp=telcheck(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "電話番号が正しくない";
					}
				break
			case 'lat': 
				var tmp=latcheck(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "電話番号が正しくない";
					}
				break	
			case 'lon': 
				var tmp=loncheck(theval);
					if(!tmp){
						$(":input[name='"+strarr[i]+"']")[0].style.borderColor  = "rgb(255,0,0)";
						return "電話番号が正しくない";
					}
				break						
			default:
				return "ok";
		}
			
	
	}

		

}

function latcheck(str){

	
	var latreg = /^(\-|\+)?([0-8]?\d{1}\.\d{0,6}|90\.0{0,6}|[0-8]?\d{1}|90)$/
	
	if(!latreg.test(str)){
		console.log("纬度整数0-90,小数0-6!");
		return false;
	}else{
		return true;
	}

}

function loncheck(str){

	
	var longrg = /^(\-|\+)?(((\d|[1-9]\d|1[0-7]\d|0{1,3})\.\d{0,6})|(\d|[1-9]\d|1[0-7]\d|0{1,3})|180\.0{0,6}|180)$/;

	if(!longrg.test(str)){
		
    console.log("经度整数0-180,小数0-6!");
		return false;
	}else{
		return true;
	}

}


function telcheck(str){

	var tel = str.replace(/[━.*‐.*―.*－.*\-.*ー.*\-]/gi,'');
	if (!tel.match(/^(0[5-9]0[0-9]{8}|0[1-9][1-9][0-9]{7})$/)) {
		return false;
	}else{
		return true;
	}

}
function blankcheck(str){
	if(str==null||str==""){
		return false;
	}
	return true;
}
function numAlphabet(str){
	if(str==null||str==""){
		return false;
	}
    var zg =  /^[0-9a-zA-Z]*$/;  
 
     if (!zg.test(str))  {  
        return false;  
     } else {  
        return true;  
     }  

}
//文字列中に数字のみかの判断
function isNumberChar(str) {
    var exp = /[^0-9()]/g;
    if (str.search(exp) != -1) {
        return false;
    }
    return true;
}
 
//Email判断
function ValidateEmail(Email) {
    if (Email==null || Email == "") {
        return false;
    }
    else {
        var r = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        if (r.test(Email)) {
            return true;
        } else {
            return false;
        }
    }
}


//<input type="text" onkeyup="OnlyAllowNumKeyup(this)" />
function OnlyAllowNumKeyup(o) {
    o.value = o.value.replace(/[^0-9.]/g, '');
}


//Request = GetRequest();
//var xxx = Request['xxx'];
function GetRequest() { 
	var url = location.search; //获取url中"?"符后的字串 
	var theRequest = new Object(); 
	if (url.indexOf("?") != -1) {
		var str = url.substr(1); 
		strs = str.split("&"); 
		for(var i = 0; i < strs.length; i ++) {
			theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]); 
		} 
	} 
	return theRequest; 
} 


/*------------------------------------------------------
* GETパラメータ取得
-----------------------------------------------------*/
//getlastdays(2009, 2, 0);
function getlastdays(year,month,day){
	var rtn= new Date(year,month,day).getDate();
	return rtn;
}
  //指定日から前・後ｎ日の日付
  /*
  date 日期：2018-09-27
day -1前一日，1後一日
  */
  
 function getNextDate(date,day) {  
 	if(!date)return;
 	var strdate=date.replace(/-/g, '/')+" 00:00:00";
 	var	oDate = new Date(strdate).valueOf();
 	var nDate=oDate+day* 24 * 3600 * 1000;
 	
  var dd = new Date(nDate);
  
  var y = dd.getFullYear();
  var m = dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1;
  var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  return y + "-" + m + "-" + d;
};

//getNextDate("2018-09-27",10)

 //今日から前ｎ日の日付
 function getBeforeDate(n){
    var n = n;
    var d = new Date();
    var year = d.getFullYear();
    var mon=d.getMonth()+1;
    var day=d.getDate();
    if(day <= n){
            if(mon>1) {
               mon=mon-1;
            }
           else {
             year = year-1;
             mon = 12;
             }
           }
          d.setDate(d.getDate()-n);
          year = d.getFullYear();
          mon=d.getMonth()+1;
          day=d.getDate();
     s = year+"-"+(mon<10?('0'+mon):mon)+"-"+(day<10?('0'+day):day);
     return s;
}
console.log(getBeforeDate(1));//昨天
console.log(getBeforeDate(7));//七日前
/**
 *指定月の日数を取得する
 * 
 */
function getDaysOfMonth(year,month){
    var date=new Date(year,month,0);
    var days=date.getDate();
    return days;
}

function gettodaydate(){

	var date = new Date();

	var nowMonth = date.getMonth() + 1;

	var strDate = date.getDate();

	var seperator = "-";
	if (nowMonth >= 1 && nowMonth <= 9) {
	   nowMonth = "0" + nowMonth;
	}
	if (strDate >= 0 && strDate <= 9) {
	   strDate = "0" + strDate;
	}

	var nowDate = date.getFullYear() + seperator + nowMonth + seperator + strDate;
	return nowDate;
}
function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
    }
    return vars;
}
function getNowDate(seperator){

	var date = new Date();
	var nowMonth = date.getMonth() + 1;
	var strDate = date.getDate();
	var seperator = seperator;

	if (nowMonth >= 1 && nowMonth <= 9) {
	   nowMonth = "0" + nowMonth;
	}

	if (strDate >= 0 && strDate <= 9) {
	   strDate = "0" + strDate;
	}

	var nowDate = date.getFullYear() + seperator + nowMonth + seperator + strDate;
	
	return nowDate;

}
function chkmail(address){

  var reg = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
  if (reg.test(address)) {
  
    return true;
    console.log("正しい");
  } else {
  
  	return false;
    console.log("間違っている");
  }
  
}
	
// 
function addnum(clsname){
	var maxperiod=4;
    var txt = document.getElementsByClassName(clsname)[0];
    var a = txt.value;
    if(a>=maxperiod){
    	return;
    }
    a++;
    txt.value = a;
}

// 
function subnum(clsname){
    var txt = document.getElementsByClassName(clsname)[0];
    var a = txt.value;
    if(a>1){
        a--;
        txt.value = a;
    }else{
        txt.value = 0;
    }
    
}

function transformToJson(formData){
    var obj={}
    for (var i in formData) {
        obj[formData[i].name]=formData[i]['value'];
    }
    return obj;
}
	