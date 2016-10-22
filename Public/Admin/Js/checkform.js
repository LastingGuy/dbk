
function checkForm(formName)
{
   try
   {
 var aa = document.forms[formName].elements;
 var obj = null;
 var jumpFromFor = false;
 for (i=0;i<aa.length;i++)
 {
  jumpFromFor = true;  //如果中途跳出，jumpFromFor的值将被保持为true,表示验证未通过
  if(aa[i].getAttribute("checkStr")!=""&&aa[i].getAttribute("checkStr")!=null)
  {
   obj = aa[i];

   if(obj.value.length==0)
   {
    if(obj.getAttribute("canEmpty")!="Y")
    {
     alert("请填写"+obj.getAttribute("checkStr")+"!");
     break;
    }
   }

   if(obj.getAttribute("equal")!=null && obj.getAttribute("equal").length>0)
   {	
    var obj2 = document.forms[formName].elements[obj.getAttribute("equal")];
    if(obj2 != null)
    {
     if(obj.value != obj2.value)
     {
      alert("两次填写密码不一致")
      break;
     }
    }
   }
   
   //后来加上开始
   if(obj.name=="username"||obj.name=="password"||obj.name=="y_password")
   {
    if(!(/^[\w]{4,20}$/.test(obj.value)))
    {
     alert("" + obj.getAttribute("checkStr")+"格式不正确!");
     break;
    }
   }
   //后来加上结束
   if(obj.getAttribute("checkType")=="email")
   {
    if(!checkEmail(obj))
    {
     alert("" + obj.getAttribute("checkStr")+"格式不正确!");
     break;
    }
   }
   
   if(obj.getAttribute("checkType")=="idcard")
   {
    if(!checkIDCard(obj))
    {
     alert("" + obj.getAttribute("checkStr")+"格式不正确!");
     break;
    }
   }
   
   if(/^string/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkString(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);
     break;
    }
   }

   if(/^float/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkFloat(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);
     break;
    }
   }

   if(/^integer/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkInteger(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);;
     break;
    }
   }
   if(/^number/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkNumber(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);;
     break;
    }
   }
   if(/^date/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkDate(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);;
     break;
    }
   }
   
   if(/^time/.test(obj.getAttribute("checkType")))
   {
    tempArr = checkTime(obj);
    if(!tempArr[0])
    {
     alert(tempArr[1]);;
     break;
    }
   }
   
  }//最先的if结束
  jumpFromFor = false; //循环正常结束，未从循环中跳出,验证结果：全部满足要求   
 }//循环结束
 if(jumpFromFor)
 {
  obj.focus();
  obj.select();
  return false;
 }
 return true;
   }
   catch(err)
   {
 return false;
   }
}

function checkEmail(obj)
{
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return true;
 return(/^([\.\w-]){1,}@([\w-]){1,}(\.([\w]){2,4}){1,}$/.test(obj.value));
}

function checkIDCard(obj)
{
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return true;
 if(obj.value.length==15)
  return(/^([0-9]){15,15}$/.test(obj.value));
 if(obj.value.length==18)
  return(/^([0-9]){17,17}([0-9xX]){1,1}$/.test(obj.value));
 return false;
}

function checkString(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 var length = obj.value.length;
 
 var arr = obj.getAttribute("checkType").split(",");
 var smallLength = parseInt(arr[1]);
 var bigLength= parseInt(arr[2]);
 
 if(length<smallLength)
 {
  tempArr[0]=false;
  tempArr[1]=""+ obj.getAttribute("checkStr")+"不能小于"+smallLength+"位,请重新填写";
  return tempArr;
 }
 if(length > bigLength)
 {
  tempArr[0]=false;
  tempArr[1]=""+obj.getAttribute("checkStr")+"不能大于"+bigLength+"位,请重新填写";
  return tempArr;
 }
 return tempArr;
}

function checkFloat(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 if(!(/^([-]){0,1}([0-9]){1,}([.]){0,1}([0-9]){0,}$/.test(obj.value))) 
 {
  tempArr[0]=false;
  tempArr[1]="Not real,Please input " + obj.checkStr+" again!";
  return tempArr;
 }
 var floatvalue = parseFloat(obj.value);
 var arr = obj.getAttribute("checkType").split(",");
 var smallFloat = parseFloat(arr[1]);
 var bigFloat = parseFloat(arr[2]);
 if(floatvalue<smallFloat)
 {
  tempArr[0]=false;
  tempArr[1]="Not less than "+smallFloat+",Please write your " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 if(floatvalue > bigFloat)
 {
  tempArr[0]=false;
  tempArr[1]="Not greater than "+bigFloat+",Please write your " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 return tempArr;
}

function checkInteger(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 if(!(/^([-]){0,1}([0-9]){1,}$/.test(obj.value)))
 {
  tempArr[0]=false;
  tempArr[1]="Not integer,Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 var integervalue = parseInt(obj.value);
 var arr = obj.getAttribute("checkType").split(",");
 var smallInteger = parseInt(arr[1]);
 var bigInteger = parseInt(arr[2]);
 if(integervalue<smallInteger)
 {
  tempArr[0]=false;
  tempArr[1]="Not less than "+smallInteger+",Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 if(integervalue > bigInteger)
 {
  tempArr[0]=false;
  tempArr[1]="Not greater than "+bigInteger+",Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 return tempArr;
}
//wangx 加入校验数字类型长度有限制。

function checkNumber(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 if(!(/^([-]){0,1}([0-9]){1,}$/.test(obj.value)))
 {
  tempArr[0]=false;
  tempArr[1]="Not numbers,Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 var integervalue = obj.value.length
 var arr = obj.getAttribute("checkType").split(",");
 var smallInteger = parseInt(arr[1]);
 var bigInteger = parseInt(arr[2]);
 if(integervalue<smallInteger)
 {
  tempArr[0]=false;
  tempArr[1]="Not less than "+smallInteger+" units,Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 if(integervalue > bigInteger)
 {
  tempArr[0]=false;
  tempArr[1]="Not greater than "+bigInteger+" units,Please input " + obj.getAttribute("checkStr")+" again!";
  return tempArr;
 }
 return tempArr;
}

function checkDate(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 if(!(/^([0-9]){4,4}-([0-9]){1,2}-([0-9]){1,2}$/.test(obj.value))) 
 {
  tempArr[0] = false;
  tempArr[1] = "不是合法的日期，请按\"YYYY-MM-DD\"的格式输入『"+obj.getAttribute("checkStr")+"』";
  return tempArr;
 }
 var arr = obj.value.match(/\d+/g);
 year = Number(arr[0]);
 month = Number(arr[1]);
 day = Number(arr[2]);
 var monthDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
 if(year%400==0||(year%4==0&&year%100!=0)) monthDay[1] = 29;
 if(year<0 || month<0 || month>12 || day>31 ||day>monthDay[month-1])
 {
  tempArr[0] = false;
  tempArr[1] = "您输入了一个不存在的日期，请重新输入『"+obj.getAttribute("checkStr")+"』";
  return tempArr;
 }
 arr = obj.getAttribute("checkType").split(",");
 if(arr[1].length>0)
 {
  var arr2 = arr[1].match(/\d+/g);
  var smallYear = Number(arr2[0]);
  var smallMonth = Number(arr2[1]);
  var smallDay = Number(arr2[2]);
  if(smallYear>year || (smallYear==year&&smallMonth>month) || (smallYear==year&&smallMonth==month&&smallDay>day))
  {
   tempArr[0] = false;
   tempArr[1] = "日期不能小于"+arr[1]+"，请重新输入『"+obj.getAttribute("checkStr")+"』";
   return tempArr;
  }
 }
 
 if(arr[2].length>0)
 {
  arr2 = arr[2].match(/\d+/g);
  var bigYear = Number(arr2[0]);
  var bigMonth = Number(arr2[1]);
  var bigDay = Number(arr2[2]);
  if(bigYear<year || (bigYear==year&&bigMonth<month) || (bigYear==year&&bigMonth==month&&bigDay<day))
  {
   tempArr[0] = false;
   tempArr[1] = "日期不能大于"+arr[2]+"，请重新输入『"+obj.getAttribute("checkStr")+"』";
   return tempArr;
  }
 }
 return tempArr;
}


function checkTime(obj)
{
 var tempArr = new Array(true,"");
 if(obj.getAttribute("canEmpty")=="Y" && obj.value.length==0) return tempArr;
 if(!(/^([0-9]){1,2}:([0-9]){1,2}$/.test(obj.value))) 
 {
  tempArr[0] = false;
  tempArr[1] = "不是合法的时间，请按\"hh:mm\"的格式输入『"+obj.getAttribute("checkStr")+"』";
  return tempArr;
 }
 var arr = obj.value.match(/\d+/g);
 hour = Number(arr[0]);
 minute = Number(arr[1]);
 if(hour<0 || hour>=24 || minute <0 || minute>=60)
 {
  tempArr[0] = false;
  tempArr[1] = "您输入了一个不存在的时间，请重新输入『"+obj.getAttribute("checkStr")+"』";
  return tempArr;
 }
 arr = obj.checkType.split(",");
 if(arr[1].length>0)
 {
  var arr2 = arr[1].match(/\d+/g);
  var smallHour = Number(arr2[0]);
  var smallMinute = Number(arr2[1]);
  if(smallHour>hour || (smallHour==hour&&smallMinute>minute))
  {
   tempArr[0] = false;
   tempArr[1] = "时间不能小于"+arr[1]+"，请重新输入『"+obj.getAttribute("checkStr")+"』";
   return tempArr;
  }
 }
 
 if(arr[2].length>0)
 { 
  arr2 = arr[2].match(/\d+/g);
  var bigHour = Number(arr2[0]);
  var bigMinute = Number(arr2[1]);
  if(bigHour<hour || (bigHour==hour&&bigMinute<minute))
  {
   tempArr[0] = false;
   tempArr[1] = "时间不能大于"+arr[2]+"，请重新输入『"+obj.getAttribute("checkStr")+"』";
   return tempArr;
  }
 }
 return tempArr;
}

