
#学校
create table dbk_school(
	school_id int unsigned auto_increment comment '学校id',
	school_name varchar(50) comment '学校名',
    school_city varchar(30) comment '学校所在城市',
    constraint pk_dbk_school primary key(school_id)
);

#寝室
create table dbk_dormitory(
	dormitory_id int unsigned auto_increment comment '寝室id',
	school_id int unsigned comment '寝室所在学校',
    dormitory_address varchar(20) comment '寝室地址',
    constraint pk_dbk_dormitory primary key(dormitory_id),
    constraint fk_dbk_dormitory foreign key(school_id) references dbk_school(school_id)
    
);

#代取件表
create table dbk_pickup(
	mail_id int not null auto_increment,
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
    constraint pk_dbk_pickup primary key(mail_id),
    constraint fk_dbk_pickup foreign key(dormitory_id) references dbk_dormitory(dormitory_id)
);
