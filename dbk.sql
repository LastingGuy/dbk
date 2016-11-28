
#用户
create table dbk_weixin_user(
  openid varchar(100) comment '用户在微信上的id',
  nickname varchar(100) comment '用户昵称',
  headimgurl varchar(300) comment '用户头像url',
  register_time datetime comment '注册时间',
  constraint pk_dbk_user primary key(openid)
)default character set utf8;

#学校
-- the old

-- create table dbk_school(
-- 	school_id int unsigned auto_increment comment '学校id',
-- 	school_name varchar(50) comment '学校名',
--     school_city varchar(30) comment '学校所在城市',
--     constraint pk_dbk_school primary key(school_id)
-- )default character set utf8;


-- the new
CREATE TABLE `dbk_school` (
  `school_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '学校id',
  `school_name` varchar(50) DEFAULT NULL COMMENT '学校名',
  `school_city` varchar(30) DEFAULT NULL COMMENT '学校所在城市',
  `small_price` float DEFAULT '0',
  `mid_price` float DEFAULT '0',
  `large_price` float DEFAULT '0',
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8


#寝室
create table dbk_dormitory(
	dormitory_id int unsigned auto_increment comment '寝室id',
	school_id int unsigned comment '寝室所在学校',
    dormitory_address varchar(20) comment '寝室地址',
    constraint pk_dbk_dormitory primary key(dormitory_id),
    constraint fk_dbk_dormitory foreign key(school_id) references dbk_school(school_id)
)  default character set utf8;

#dbk_express_company 快递公司
CREATE TABLE `dbk_express_company` (
  `express_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(10) unsigned NOT NULL,
  `express_company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '快递公司名字',
  PRIMARY KEY (`express_id`),
  CONSTRAINT `fk_school_id_express_company` FOREIGN KEY (`school_id`) REFERENCES `dbk_school` (`school_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8

#管理员
create table dbk_admin(
  admin_id varchar(20) not null comment '管理员id',
  admin_passwd varchar(20) not null comment '管理员密码',
  admin_school int unsigned comment '学校id',
  admin_type tinyint unsigned default 0 comment '管理员类型：0普通某校管理员  1超级管理员',
  constraint pk_dbk_admin primary key(admin_id),
  constraint fk_dbk_admin foreign key(admin_school) references dbk_school(school_id)
)default character set utf8;

#代取件表
create table dbk_pickup(
	pickup_id int not null auto_increment comment '订单id',
    openid varchar(100) comment '用户id',
    receiver_name varchar(10) comment '收件人姓名',
    receiver_phone varchar(15) comment '收件人手机号码',
    dormitory_id int unsigned comment '寝室id',
    express_type varchar(50) not null comment '快递类型',
    express_company varchar(20) not null comment '快递公司',
    express_sms varchar(300) not null comment '快递短信',
    express_code varchar(50) not null comment '取件码/货架号/手机号',
    remarks varchar(100) default null comment '备注',
    price int(11) not null comment '快递价格',
	 `time`  datetime not null comment '下单时间',
	  pay_time datetime not null comment '支付时间',
	  temp1 varchar(45)  comment '暂时字段',
	  express_status tinyint not null comment ' 0：等待接单  1：未支付  2：正在配送 3:已完成',
	  constraint pk_dbk_pickup primary key(pickup_id),
    constraint fk_dbk_dormitory_pickup foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
    constraint fk_dbk_openid_pickup foreign key(openid) references dbk_weixin_user(openid)
)  default character set utf8 ;

#代寄件
create table dbk_send(
	send_id int not null auto_increment comment '订单id',
	openid varchar(100) comment '用户id',
    sender_name varchar(10) comment '寄件人姓名',
    sender_phone varchar(15) comment '寄件人手机号码',
    dormitory_id int unsigned comment '寝室id',
    recv_name varchar(45) NOT NULL COMMENT '收件人姓名',
    recv_phone varchar(15) NOT NULL COMMENT '收件人手机',
    sender_goods varchar(300) not null comment '寄件物品',
    destination varchar(1000) NOT NULL COMMENT '目的地',
    remarks varchar(300) comment '备注',
    time datetime  not null comment '下单时间',
    sender_status tinyint not null comment '寄件状态  0:未接单 1:未支付 2：正在寄件 3：完成寄件' ,
    constraint pk_dbk_send primary key(send_id),
    constraint  fk_dbk_dormitory_send foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
    constraint fk_dbk_openid_send foreign key(openid) references dbk_weixin_user(openid)
)  default character set utf8;


#到件通知
create table dbk_dormitory_dialog
(
	dormitory_id int unsigned comment '寝室号',
    date date comment '拨打日期',
    time datetime comment '拨打时间',
    constraint pk_dormitory_dialog primary key(dormitory_id, date),
    constraint fk_dormitory_dialog_dormitoryid foreign key(dormitory_id) references dbk_dormitory(dormitory_id)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

#收件费用
create table dbk_school_fee
(
  school_id int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '学校id',
  price float comment '价格',
  size varchar(20) comment '',
  description varchar(100) comment'描述',
  addition varchar(100),
  constraint pk_dbk_school_fee PRIMARY KEY(school_id,size),
  CONSTRAINT fk_dbk_school_fee_school_id FOREIGN KEY(school_id) REFERENCES  dbk_school(school_id)
)ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;


#建立代收件视图
create view dbk_pickup_view
as
 select dbk_school.school_id, dbk_school.school_city, dbk_school.school_name,
		    dbk_dormitory.dormitory_address,
        dbk_pickup.dormitory_id, dbk_pickup.express_code, dbk_pickup.express_company,
        dbk_pickup.express_sms, dbk_pickup.express_status, dbk_pickup.express_type,
        dbk_pickup.pickup_id, dbk_pickup.price, dbk_pickup.receiver_name, dbk_pickup.receiver_phone,
        dbk_pickup.remarks, dbk_pickup.time, dbk_pickup.openid, dbk_pickup.pay_time
 from dbk_school, dbk_dormitory, dbk_pickup
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_pickup.dormitory_id;

#建立代寄件视图
create view dbk_send_view
as
 select  dbk_school.school_id, dbk_school.school_city, dbk_school.school_name,
		    dbk_dormitory.dormitory_address,
        dbk_send.dormitory_id, dbk_send.sender_goods, dbk_send.sender_status,
        dbk_send.send_id, dbk_send.sender_name, dbk_send.sender_phone,
        dbk_send.remarks, dbk_send.time, dbk_send.openid, dbk_send.destination,
        dbk_send.recv_name, dbk_send.recv_phone
 from dbk_school, dbk_dormitory, dbk_send
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_send.dormitory_id;

#微信支付下单
create  table dbk_weixin_pay
(
  trade_no varchar(32) comment '商户订单号',
  openid  varchar(100) comment '用户id',
  order_id int comment '代拿代寄订单号',
  nonce_str varchar(32) comment '随机字符串',
  sign varchar(32) comment '签名',
  prepay_id varchar(64) comment '预支付交易会话标识',
  pay_type tinyint comment '订单类型： 1代拿下单  2代寄下单 ',
  pay_status  tinyint DEFAULT 0 comment '订单状态 ：0未完成  1已完成',
  total_fee int comment '支付金额，单位分',
  time_start datetime comment '交易起始时间',
  time_end datetime comment '交易结束时间',
  time_expire datetime comment '交易过期时间',
  transaction_id varchar(32) comment '微信支付订单号',
  CONSTRAINT pk_dbk_weixin_pay PRIMARY  KEY(trade_no)
)ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

#微信支付退款
create table dbk_weixin_refund
(
  refund_no varchar(32) comment '商户退款单号',
  openid  varchar(100) comment '用户id',
  trade_no varchar(32) comment '商户订单号',
  nonce_str varchar(32) comment '随机字符串',
  sign varchar(32) comment '签名',
  order_id int comment '订单号',
  pay_type tinyint comment '订单类型： 1代拿下单  2代寄下单 ',
  pay_status  tinyint DEFAULT 0 comment '订单状态 ：0未完成  1已完成',
  total_fee int comment '订单金额',
  refund_fee int comment '退款金额',
  refund_time datetime comment '退款起始时间',
  refund_id varchar(28) comment '微信退款单号',
  CONSTRAINT pk_dbk_weixin_refund PRIMARY  KEY(refund_no)
)ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

#导入学校
insert into dbk_school(school_name,school_city) values('浙江大学城市学院','杭州市');
insert into dbk_school(school_name,school_city) values('树人大学','杭州市');
insert into dbk_school(school_name,school_city) values('浙江工业大学(朝晖校区)','杭州市');
insert into dbk_school(school_name,school_city) values('武汉科技大学城市学院','武汉市');
insert into dbk_school(school_name,school_city) values('浙江师范大学','金华市');


#导入寝室
##浙江大学城市学院
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'问源楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚雅楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'求真楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'思睿楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致远楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'弘毅楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'惟学楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'精诚楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'明德楼' from dbk_school where school_name='浙江大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'慕贤楼' from dbk_school where school_name='浙江大学城市学院';

##树人大学
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'清乐园1号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'清乐园2号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'清乐园3号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'清乐园4号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致勤楼-东楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致勤楼-西楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致和园1号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致和园2号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致和园3号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致和园4号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致和园5号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园2号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园4号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园5号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园6号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园7号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园8号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园10号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园11号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园12号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园15号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园17号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园18号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园22号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'树人园23号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致信楼1号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致信楼2号楼' from dbk_school where school_name='树人大学';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'致信楼3号楼' from dbk_school where school_name='树人大学';


##浙江工业大学(朝晖校区)
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园1号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园2号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园3号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园4号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园5号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园6号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园7号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园8号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园9号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园10号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园11号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园12号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'尚德园研究生楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦1号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦2号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦3号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦4号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦5号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦6号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'梦溪园梦7号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关1号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关2号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关3号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关4号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关5号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关6号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关7号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关8号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关9号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关10号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'东新关11号楼' from dbk_school where school_name='浙江工业大学(朝晖校区)';

##武汉科技大学城市学院
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北一楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北二楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北三楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北四楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北五楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北六楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北七楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北八楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北九楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'北十楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'南一楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'南二楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'南三楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'南四楼' from dbk_school where school_name='武汉科技大学城市学院';
insert into dbk_dormitory(school_id,dormitory_address) select school_id,'南五楼' from dbk_school where school_name='武汉科技大学城市学院';