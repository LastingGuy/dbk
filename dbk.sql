
#用户
create table dbk_weixin_user(
  openid varchar(100) comment '用户在微信上的id',
  nickname varchar(100) comment '用户昵称',
  headimgurl varchar(300) comment '用户头像url',
  register_time datetime comment '注册时间',
  constraint pk_dbk_user primary key(openid)
)default character set utf8;

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

#dbk_express_company 快递公司
create table dbk_express_company(
	school_id  int unsigned comment '学校id',
    express_company_name varchar(100) comment '快递公司名字',
    constraint pk_dbk_express_company primary key(school_id,express_company_name),    
    constraint fk_dbk_express_company_school foreign key school_id references dbk_school(school_id)
)default character set utf8;
 


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
    openid varchar(100) comment '用户id',
    receiver_name varchar(10) comment '收件人姓名',
    receiver_phone varchar(12) comment '收件人手机号码',
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
    constraint fk_dbk_openid_pickup foreign key(openid) references dbk_weixin_user(openid)
)  default character set utf8 ;

#代寄件
create table dbk_send(
	send_id int not null auto_increment comment '订单id',
	openid varchar(100) comment '用户id',
    sender_name varchar(10) comment '寄件人姓名',
    sender_phone varchar(12) comment '寄件人手机号码',
    dormitory_id int unsigned comment '寝室id',
    sender_goods varchar(300) not null comment '寄件物品',
    remarks varchar(300) comment '备注',
    `time` datetime  not null comment '下单时间',
    sender_status tinyint not null comment '寄件状态  0:未接单 1:已接单 2：正在寄件 3：完成寄件' ,
    constraint pk_dbk_send primary key(send_id),
    constraint  fk_dbk_dormitory_send foreign key(dormitory_id) references dbk_dormitory(dormitory_id),
    constraint fk_dbk_openid_send foreign key(openid) references dbk_weixin_user(openid)
)  default character set utf8;

#建立代收件视图
create view dbk_pickup_view
as
 select dbk_school.school_id, dbk_school.school_city, dbk_school.school_name,
		    dbk_dormitory.dormitory_address,
        dbk_pickup.dormitory_id, dbk_pickup.express_code, dbk_pickup.express_company,
        dbk_pickup.express_sms, dbk_pickup.express_status, dbk_pickup.express_type,
        dbk_pickup.pickup_id, dbk_pickup.price, dbk_pickup.receiver_name, dbk_pickup.receiver_phone,
        dbk_pickup.remarks, dbk_pickup.time, dbk_pickup.openid
 from dbk_school, dbk_dormitory, dbk_pickup
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_pickup.dormitory_id;

#建立代寄件视图
create view dbk_send_view
as
 select  dbk_school.school_id, dbk_school.school_city, dbk_school.school_name,
		    dbk_dormitory.dormitory_address,
        dbk_send.dormitory_id, dbk_send.sender_goods, dbk_send.sender_status,
        dbk_send.send_id, dbk_send.sender_name, dbk_send.sender_phone,
        dbk_send.remarks, dbk_send.time, dbk_send.openid, dbk_send.destination
 from dbk_school, dbk_dormitory, dbk_send
 where dbk_school.school_id = dbk_dormitory.school_id and dbk_dormitory.dormitory_id = dbk_send.dormitory_id;


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