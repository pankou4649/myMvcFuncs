function GetRequests() {
   const url = location.search; //
   let theRequest = new Object();
   if (url.indexOf("?") != -1) {
      let str = url.substr(1);
      strs = str.split("&");
      for(let i = 0; i < strs.length; i ++) {
         theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
      }
   }
   return theRequest;
}
function set(key,value){
  //var curtime = new Date().getTime();//
  var curtime = new Date().format("yyyy-MM-dd hh:mm:ss");
  var curtimearr=curtime.split(' ');
  var endday=getNextDate(curtimearr[0],30);
  var endtime=endday+" "+curtimearr[1];
  localStorage.setItem(key,JSON.stringify({val:value,endtime:endtime}));//
}
function get(key)//exp
{
  var val = localStorage.getItem(key);//
  var dataobj = JSON.parse(val);//
	if(dataobj!=null){
	  var datedata=dataobj.endtime;
	  var timestemp=new Date(datedata);
	  if(timestemp.getTime()  -new Date().getTime()  >= 0)//有効期間内
	  {	
	    $("#mdacode").val(dataobj.val);
	  }
	}
  

}
Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}

//getNextDate("2018-09-27",10)
function getNextDate(date,day) {  
  var dd = new Date(date);
  dd.setDate(dd.getDate() + day);
  var y = dd.getFullYear();
  var m = dd.getMonth() + 1 < 10 ? "0" + (dd.getMonth() + 1) : dd.getMonth() + 1;
  var d = dd.getDate() < 10 ? "0" + dd.getDate() : dd.getDate();
  return y + "-" + m + "-" + d;
};

 function formatPrice(n) {
        var t = parseInt(n), i, r;
        for (t = t.toString().replace(/^(\d*)$/, "$1."), t = (t + "00").replace(/(\d*\.\d\d)\d*/, "$1"), t = t.replace(".", ","), i = /(\d)(\d{3},)/; i.test(t); )
            t = t.replace(i, "$1,$2");
        return t = t.replace(/,(\d\d)$/, ".$1"), r = t.split("."), r[1] == "00" && (t = r[0]), t
    }
        
function getdayofweek(daystr){
	
	if(daystr==undefined||daystr==""){
		return "";
	}
	
	var prefontstr="<span>";
	var endfontstr="</span>";
	
	var dayarr=daystr.split('-');
	var year = dayarr[0], month = dayarr[1]*1-1, date = dayarr[2];// month=8表示9月,因为月份要加1
	var dt = new Date(year, month, date);     
	var weekDay = ["日", "月", "火", "水", "木", "金", "土"];
	var indexday=dt.getDay();
	console.log(daystr);
	console.log(weekDay[indexday]);
	
	if(indexday==0){
		prefontstr="<span style='color:red'>";
		endfontstr="</span>";
	}
	if(indexday==6){
		prefontstr="<span style='color:blue'>";
		endfontstr="</span>";
	}
	return prefontstr+weekDay[indexday]+endfontstr;
	
}
$( function() {

	var requests= GetRequests();
	
	if(requests['mdacode']!=undefined){
		
		console.log(requests['mdacode']);
		if(requests['mdacode']!=""){
			set('labopass_mdacode',requests['mdacode']);
		}
		
	}
});

$.fn.parseForm=function(){
    var serializeObj={};
    var array=this.serializeArray();
    var str=this.serialize();
    $(array).each(function(){
        if(serializeObj[this.name]){
            if($.isArray(serializeObj[this.name])){
                serializeObj[this.name].push(this.value);
            }else{
                serializeObj[this.name]=[serializeObj[this.name],this.value];
            }
        }else{
            serializeObj[this.name]=this.value;
        }
    });
    return serializeObj;
};
