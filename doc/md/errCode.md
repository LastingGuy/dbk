# 错误码
[TOC]
##默认
* 失败：0 FAIL
* 成功：1 OK
* 未登录 2 NOTSIGN
* 参数错误 3 WrongParams

##PHP
###登录操作(action:login)

msg|code|success|body|addition|
---| --- | ---|---|---
登录成功 | 11 |true|null
登录失败 |  10 |false|null
密码错误 |12 |false|null
无此用户| 13 |false|null|登录状态


###获得默认地址操作（action:getUserDefaultInfo）
接口: `interface\getUserDefaultInfo`

msg | code | success | body|addition
--- | ----- | -----|---|---
获得默认地址成功 | 1 | true|默认信息array
获得默认地址失败|0|false|null
用户无默认地址 | 21 | true|null
默认学校已被隐藏 | 22 |true|默认信息array
默认寝室楼已下线|23|true|默认信息array
用户未登录|13|false|null|登陆状态

###获得学校寝室和快递点信息（action:getSchoolInfo）
接口：`interface\getSchoolInfo`
msg|code|success|body|addition
---|---|---|---|---|---
获得学校信息成功|1|true|{dors:{},express:{},typesOfExpress{}}|
学校已下线|31|true|同上

###新建代取订单(action:newPickupOrder)
####接口：`home\order\newPickupOrder`
####method: `POST`
####参数：
```
	
	{
		school: 浙江大学城市学院
		dormitory: 问源楼
		receiver: 张三
		phone: 13112341234
		size:	size1
		company: 圆通
		sms:	快递短信	
		code: 1234
		remarks: 无
	}
```
	
####return
||code|success|msg|body|addition
---|---|---|---|---|---|---
未登录|2|false|NOTSIGN||
参数错误|3|false|WrongParams|description|
成功|1|true|OK|orderNo



##JAVA
###登录操作(action:login)
####接口：
`express\login`

####return
||code|success|msg|body|addition|
---| --- | ---|---|---|---
登录成功 | 11 |true|Success To Log in!|null
登录失败 |  10 |false|Fail To Log in!|null
密码错误 |12 |false|Password is Wrong!|null
无此用户| 13 |false|No such account!|null|

####提交 
method:`get`
data：`userid`和`passwd`  

####sample:
```
登录成功
{"action":"Login","success":true,"code":1,"msg":"OK","body":{}}

登录失败
{"action":"Login","success":false,"code":13,"msg":"No such account!","body":{}}

```


###获得获取学校及寝室楼，以及是否完成拨打的数据（action:getSchoolAndDormitory）
####接口：
`express\school`

####return
||code|success|msg|body|addition
---|---|---|---|---|---
未登录|2|false|did not log in!|null
无寝室信息|0|false|no dormitory information|null
成功|1|true|ok|寝室信息

####sample：
```
{
"action":"getSchoolAndDormitory",
"success":true,
"code":1,
"msg":"OK",
"body":
{"schoolId":1,
"schoolName":"浙江大学城市学院",
"dormitories":[{"dormitoryId":1,"schoolId":1,"dormitoryAddress":"问源楼"},{"dormitoryId":2,"schoolId":1,"dormitoryAddress":"尚雅楼"},{"dormitoryId":3,"schoolId":1,"dormitoryAddress":"求真楼"}]}
}

```


###获得订单列表(action:getOrders)
####接口
`express\order`
####提交
method: `get`
data:	`dormitoryid`

####return
||code|success|msg|body|addition
---|---|---|---|---|---|
未登录|2|false|未登录|null|
OK|1|true|OK|订单列表|


####sample
```

{"action":"getOrders","success":true,"code":1,"msg":"OK","body":[{"phone":"181XXXX0688","orderID":13881,"name":"胡XX","dialTime":null},{"phone":"137XXXX8844","orderID":13882,"name":"汪XX","dialTime":null}]}

```
###呼叫用户(action:dial)
####接口：
`express\dial`

####return
#####返回类型
||code|success|msg|body|addition|
---|---|---|---|---|---
未登录|2|false|did not log in|null
请求成功|1|true|ok|拨打状态
请求失败|0|false|Fail|阿里大于返回信息[^aldy]

[^aldy]: 参考 [阿里大于](https://api.alidayu.com/docs/api.htm?spm=a3142.7395905.4.5.j9Y30L&apiId=25445)
#####拨打状态

||code|success|msg|body|addition
---|---|---|---|---|---|
拨打成功|41|true|dial successfully!|null
已经呼叫过此用户|42|false|this person has already been dialed!|null

####提交
method:`get`
data:`pickupid` 需要呼叫的订单号数组


####sample
```
请求失败时返回
{"action":"Dial","success":false,"code":0,"msg":"Fail","body":"{\"error_response\":{\"code\":29,\"msg\":\"Invalid app Key\",\"sub_code\":\"isv.appkey-not-exists\",\"request_id\":\"eq6s5eww21mo\"}}"}


请求成功是返回
（如：http://localhost:8080/dial?pickupid=13881&pickupid=13882）
{"action":"Dial","success":true,"code":1,"msg":"OK","body":[{"statusMsg":"Already dialed","orderID":13881,"success":false,"body":null,"statusCode":42},{"statusMsg":"success to dial","orderID":13882,"success":true,"body":null,"statusCode":41}]}


```










