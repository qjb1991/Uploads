create table upload_log(
  `id` int unsigned primary key auto_increment comment 'ID',
  `token` varchar(255) not null default '' comment '用户TOKEN',
  `app_name` varchar(20) not null default '' comment '文件来源',
  `file_info` varchar(1000) not null default '' comment '文件信息',
  `uid` int unsigned not null default 0 comment '用户ID',
  `create_at` int unsigned not null default 0 comment '创建时间',
  `type` varchar(10) not null default '' comment '文件类型'
)engine=InnoDb charset=utf8 comment='文件上传日志';

create table file(
  `id` int unsigned primary key auto_increment comment 'ID',
  `type` varchar(20) not null default '' comment '文件类型',
  `row_name` varchar(255) not null default '' comment '文件名',
  `access_url` varchar(255) not null default '' comment '访问路径',
  `save_url` varchar(255) not null default '' comment '保存路径',
  `scope` varchar(20) not null default '' comment '项目来源',
  `uid` int unsigned not null default 0 comment '用户ID',
  `create_at` int unsigned not null default 0 comment '创建时间',
  `state` tinyint not null default 1 comment '状态',
  `md5` varchar(50) not null default '' comment 'md5',
  `update_at` int unsigned not null default 0 comment '更新时间',
  unique key `md5`(`md5`)
)engine=InnoDb charset=utf8 comment='上传的文件';