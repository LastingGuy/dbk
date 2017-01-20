#学校
CREATE TABLE `dbk_school` (
  `school_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '学校id',
  `school_name` varchar(50) DEFAULT NULL COMMENT '学校名',
  `school_city` varchar(30) DEFAULT NULL COMMENT '学校所在城市',
  `online` int(11) DEFAULT '1' COMMENT '控制学校是否上线',
  `offline_msg` varchar(100) DEFAULT '此学校已下线',
  `display` int(11) DEFAULT '1' COMMENT '控制学校是否显示',
  `msg` varchar(100) DEFAULT NULL COMMENT '学校通知',
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

#管理员
CREATE TABLE `dbk_admin` (
  `admin_id` varchar(20) NOT NULL COMMENT '管理员id',
  `admin_passwd` varchar(20) NOT NULL COMMENT '管理员密码',
  `admin_school` int(11) unsigned DEFAULT NULL COMMENT '学校id',
  `admin_type` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`admin_id`),
  KEY `fk_dbk_admin` (`admin_school`),
  CONSTRAINT `fk_dbk_admin` FOREIGN KEY (`admin_school`) REFERENCES `dbk_school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#寝室
CREATE TABLE `dbk_dormitory` (
  `dormitory_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '寝室id',
  `school_id` int(11) unsigned DEFAULT NULL COMMENT '寝室所在学校',
  `dormitory_address` varchar(20) DEFAULT NULL COMMENT '寝室地址',
  `online` int(11) DEFAULT '1' COMMENT '控制是否上线',
  `offline_msg` varchar(100) DEFAULT '此寝室楼已经消失在历史中',
  `order` int(11) DEFAULT DEFAULT '0' COMMENT '控制显示顺序',
  PRIMARY KEY (`dormitory_id`),
  KEY `fk_dbk_dormitory` (`school_id`),
  CONSTRAINT `fk_dbk_dormitory` FOREIGN KEY (`school_id`) REFERENCES `dbk_school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#快递公司
CREATE TABLE `dbk_express_company` (
  `express_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(11) unsigned NOT NULL,
  `express_company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '快递公司名字',
  `online` int(11) DEFAULT '1' COMMENT '控制是否上线',
  `order` int(11) DEFAULT '1' COMMENT '控制显示顺序',
  PRIMARY KEY (`express_id`),
  KEY `fk_school_id_express_company` (`school_id`),
  CONSTRAINT `fk_school_id_express_company` FOREIGN KEY (`school_id`) REFERENCES `dbk_school` (`school_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#价格
CREATE TABLE `dbk_fee` (
  `school_id` int(11) unsigned NOT NULL COMMENT '学校id',
  `price` float DEFAULT NULL COMMENT '价格',
  `size` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(100) DEFAULT NULL COMMENT '描述',
  `addition` varchar(100) DEFAULT NULL,
  `online` int(11) DEFAULT '1' COMMENT '控制是否上线',
  PRIMARY KEY (`school_id`,`size`),
  CONSTRAINT `fk_dbk_school_fee_school_id` FOREIGN KEY (`school_id`) REFERENCES `dbk_school` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#用户表
CREATE TABLE `dbk_user` (
  `userid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '用户在微信上的id',
  `phone` varchar(15) DEFAULT NULL COMMENT '用户预留手机',
  `name` varchar(20) DEFAULT NULL COMMENT '用户姓名',
  `nickname` varchar(100) DEFAULT NULL COMMENT '用户昵称',
  `headimgurl` varchar(300) DEFAULT NULL COMMENT '用户头像url',
  `register_time` datetime DEFAULT NULL COMMENT '注册时间',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#默认地址
CREATE TABLE `dbk_defaultinfo` (
  `userid` int(11) NOT NULL,
  `default_name` varchar(20) NOT NULL,
  `default_city` varchar(50) NOT NULL,
  `default_school` varchar(50) NOT NULL,
  `default_dormitory` varchar(50) NOT NULL,
  `default_phone` varchar(11) NOT NULL,
  PRIMARY KEY (`userid`),
  CONSTRAINT `fk_dbk_openid_defaultinfo` FOREIGN KEY (`userid`) REFERENCES `dbk_user` (`userid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table dbk_pickup
(
  pickup_id int not null auto_increment comment '订单主键',
  pickup_no varchar(20) not null comment '订单号',
  userid varchar(50) not null comment '用户id',
  receiver_name varchar(10) comment '收件人姓名',
  receiver_phone varchar(15) comment '收件人手机号码',
  dormitory_id int unsigned comment '寝室id',
  express_type varchar(50) not null comment '快递类型',
  express_company varchar(20) not null comment '快递公司',
  express_sms varchar(300) not null comment '快递短信',
  express_code varchar(50) not null comment '取件码/货架号/手机号',
  remarks varchar(100) default null comment '备注',
	`time`  datetime not null comment '下单时间',
	temp1 varchar(45)  comment '暂时字段',
	express_status tinyint not null comment ' 0：等待接单  1：未支付  2：正在配送  3:已完成',
	constraint pk_dbk_pickup primary key(pickup_id),
  constraint fk_dbk_dormitory_pickup foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
  constraint fk_dbk_openid_pickup foreign key(userid) references dbk_user(userid)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table dbk_pickup_pay
(
  pickup_id int not null auto_increment comment '订单主键',
  total_fee int comment '总金额，单位分',
  pay_fee int comment '实际支付金额，单位分',
  coupon_id int DEFAULT NULL comment '使用代金券id',
  pay_status  tinyint DEFAULT 0 comment '订单状态 ：0未付款  1已付款 2退款中 3已退款',
  time_start datetime comment '交易起始时间',
  time_end datetime comment '交易结束时间',
  time_expire datetime comment '交易过期时间',
  transaction_id varchar(32) comment '微信支付订单号',
  refund_fee int comment '退款金额',
  refund_time datetime comment '退款起始时间',
  refund_id varchar(28) comment '微信退款单号',
  constraint pk_dbk_pickup_pay primary key(pickup_id),
  constraint fk_dbk_pickup_pay_pickupid foreign key(pickup_id) references dbk_pickup(pickup_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;



create view dbk_pickup_pay_view
as
  select
        dbk_pickup.pickup_id, dbk_pickup.pickup_no, dbk_pickup.userid,
        dbk_pickup.receiver_name, dbk_pickup.receiver_phone, dbk_pickup.dormitory_id,
        dbk_pickup.express_type, dbk_pickup.express_company, dbk_pickup.express_sms, dbk_pickup.express_code,
        dbk_pickup.remarks, dbk_pickup.price, dbk_pickup.time, dbk_pickup.pay_time, dbk_pickup.express_status,

        dbk_pickup_pay.total_fee, dbk_pickup_pay.pay_status, dbk_pickup_pay.time_start,
        dbk_pickup_pay.time_end, dbk_pickup_pay.time_expire, dbk_pickup_pay.transaction_id,
        dbk_pickup_pay.refund_fee, dbk_pickup_pay.refund_time, dbk_pickup_pay.refund_id
  from dbk_pickup, dbk_pickup_pay
  where dbk_pickup.pickup_id = dbk_pickup_pay.pickup_id
