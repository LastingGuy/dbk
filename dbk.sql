
#学校
create table dbk_school(
	school_id int unsigned auto_increment comment '学校id',
	school_name varchar(50) comment '学校名',
    school_city varchar(30) comment '学校所在城市',
    constraint pk_dbk_school primary key(school_id)
)default character set utf8;

#寝室
create table dbk_dormitory(
	dormitory_id int unsigned auto_increment comment '寝室id',
	school_id int unsigned comment '寝室所在学校',
    dormitory_address varchar(20) comment '寝室地址',
    constraint pk_dbk_dormitory primary key(dormitory_id),
    constraint fk_dbk_dormitory foreign key(school_id) references dbk_school(school_id)

)  default character set utf8;

#管理员
create table dbk_admin(
  admin_id varchar(20) not null comment '管理员id',
  admin_passwd varchar(20) not null comment '管理员密码',
  admin_school int unsigned comment '学校id',
  constraint pk_dbk_admin primary key(admin_id),
  constraint fk_dbk_admin foreign key(admin_school) references dbk_school(school_id)
)default character set utf8;


#代取件表
create table dbk_pickup(
	pickup_id int not null auto_increment comment '订单id',
    user_id int comment '用户id',
    receiver_name varchar(10) comment '收件人姓名',
    receiver_phone int(12) comment '收件人手机号码',
    dormitory_id int unsigned comment '寝室id',
    express_type varchar(50) not null comment '快递类型',
    express_company varchar(20) not null comment '快递公司',
    express_sms varchar(300) not null comment '快递短信',
    express_code varchar(50) not null comment '取件码/货架号/手机号',
    remarks varchar(100) default null comment '备注',
    price int(11) not null comment '快递价格',
	`time`  datetime not null comment '下单时间',
	express_status tinyint not null comment ' 0：等待接单  1：已接单  2：正在配送 3:已完成',
	constraint pk_dbk_pickup primary key(pickup_id),
    constraint fk_dbk_dormitory_pickup foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
)  default character set utf8 ;

#代寄件
create table dbk_send(
	send_id int not null auto_increment comment '订单id',
	user_id int comment '用户id',
    sender_name varchar(10) comment '寄件人姓名',
    sender_phone int(12) comment '寄件人手机号码',
    dormitory_id int unsigned comment '寝室id',
    sender_goods varchar(300) not null comment '寄件物品',
    remarks varchar(300) comment '备注',
    `time` datetime  not null comment '下单时间',
    sender_status tinyint not null comment '寄件状态  0:未接单 1:已接单 2：正在寄件 3：完成寄件' ,
    constraint pk_dbk_send primary key(send_id),
    constraint  fk_dbk_dormitory_send foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
)  default character set utf8;

#建立代收件视图
create view dbk_pickup_view
as
 select dbk_school.school_city, dbk_school.school_name,
		dbk_dormitory.dormitory_address,
        dbk_pickup.dormitory_id, dbk_pickup.express_code, dbk_pickup.express_company,
        dbk_pickup.express_sms, dbk_pickup.express_status, dbk_pickup.express_type,
        dbk_pickup.pickup_id, dbk_pickup.price, dbk_pickup.receiver_name, dbk_pickup.receiver_phone,
        dbk_pickup.remarks, dbk_pickup.school_id, dbk_pickup.time, dbk_pickup.user_id
 from dbk_school, dbk_dormitory, dbk_pickup
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_pickup.dormitory_id





#建立代寄件视图
create view dbk_send_view
as
 select dbk_school.school_city, dbk_school.school_name,
		dbk_dormitory.dormitory_address,
        dbk_send.dormitory_id, dbk_send.sender_goods, dbk_send.sender_status,
        dbk_send.send_id, dbk_send.sender_name, dbk_send.sender_phone,
        dbk_send.remarks, dbk_send.school_id, dbk_send.time, dbk_send.user_id
 from dbk_school, dbk_dormitory, dbk_send
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_send.dormitory_id;
