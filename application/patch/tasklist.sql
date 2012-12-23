/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : zgldhcom_tasklist

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2012-12-23 20:35:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `command`
-- ----------------------------
DROP TABLE IF EXISTS `command`;
CREATE TABLE `command` (
  `command_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL COMMENT '命令所属的任务',
  `type` varchar(255) NOT NULL COMMENT '命令类型',
  `parameters` longtext COMMENT '本命令的参数',
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`command_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of command
-- ----------------------------
INSERT INTO `command` VALUES ('11', '19', 'url-request', '{\"url\":\"http:\\/\\/www.sina.com\"}', '2012-12-23 18:48:26');
INSERT INTO `command` VALUES ('12', '20', 'send-email', '{\"recipients\":[\"zgldh@qq.com\"],\"content\":\"<p>\\r\\n\\t\\u795d\\u4f60\\u751f\\u65e5\\u5feb\\u4e50\\uff01\\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<img src=\\\"\\/uploads\\/2012-12-23\\/3b946fd21a522b1d3ec4d2f184cab2d7.gif\\\" alt=\\\"\\\" \\/>\\r\\n<\\/p>\"}', '2012-12-23 18:53:52');
INSERT INTO `command` VALUES ('14', '21', 'send-email', '{\"recipients\":[\"zgldh@hotmail.com\",\"zgldh123@gmail.com\",\"241826677@qq.com\"],\"content\":\"<p>\\r\\n\\t\\u54c8\\u54c8\\uff01\\u6ca1\\u60f3\\u5230\\u5427\\uff01\\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<img src=\\\"\\/uploads\\/2012-12-23\\/0397610659c40a261a4130f25eb9e8fe.gif\\\" alt=\\\"\\\" \\/>\\r\\n<\\/p>\"}', '2012-12-23 18:57:38');
INSERT INTO `command` VALUES ('16', '21', 'url-request', '{\"url\":\"http:\\/\\/www.baidu.com\"}', '2012-12-23 18:57:38');

-- ----------------------------
-- Table structure for `condition`
-- ----------------------------
DROP TABLE IF EXISTS `condition`;
CREATE TABLE `condition` (
  `condition_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL COMMENT '条件所属的任务',
  `type` varchar(255) NOT NULL COMMENT '条件类型',
  `parameters` longtext COMMENT '本条件的参数',
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of condition
-- ----------------------------
INSERT INTO `condition` VALUES ('19', '19', 'date-static', '{\"year\":\"\",\"month\":\"\",\"day\":\"\",\"hour\":19}', '2012-12-23 18:48:26');
INSERT INTO `condition` VALUES ('20', '20', 'date-static', '{\"year\":\"\",\"month\":12,\"day\":\"\",\"hour\":19}', '2012-12-23 18:53:52');
INSERT INTO `condition` VALUES ('21', '21', 'date-static', '{\"year\":\"\",\"month\":\"\",\"day\":23,\"hour\":19}', '2012-12-23 18:57:38');

-- ----------------------------
-- Table structure for `invitation`
-- ----------------------------
DROP TABLE IF EXISTS `invitation`;
CREATE TABLE `invitation` (
  `invitation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '邀请码id',
  `code` varchar(32) NOT NULL COMMENT '邀请码',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户id',
  `used` bit(1) NOT NULL DEFAULT b'0' COMMENT '该邀请码是否用过了: 0没用过,1用过了',
  `creator_id` bigint(20) unsigned NOT NULL COMMENT '生成邀请码的人的id',
  PRIMARY KEY (`invitation_id`),
  UNIQUE KEY `code` (`code`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of invitation
-- ----------------------------

-- ----------------------------
-- Table structure for `process_log`
-- ----------------------------
DROP TABLE IF EXISTS `process_log`;
CREATE TABLE `process_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `task_id` bigint(20) unsigned NOT NULL COMMENT '任务id',
  `log_content` text COMMENT '日志内容',
  `log_date` datetime NOT NULL COMMENT '记录时间戳',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of process_log
-- ----------------------------
INSERT INTO `process_log` VALUES ('1', '21', '全部命令执行成功。', '2012-12-23 19:51:34');
INSERT INTO `process_log` VALUES ('2', '20', '部分命令执行失败： 命令Id: 12; 命令类型: send-email', '2012-12-23 19:51:35');
INSERT INTO `process_log` VALUES ('3', '19', '全部命令执行成功。', '2012-12-23 19:51:35');
INSERT INTO `process_log` VALUES ('4', '21', '全部命令执行成功。', '2012-12-23 20:07:09');
INSERT INTO `process_log` VALUES ('5', '20', '部分命令执行失败： 命令Id: 12; 命令类型: send-email', '2012-12-23 20:07:10');
INSERT INTO `process_log` VALUES ('6', '19', '全部命令执行成功。', '2012-12-23 20:07:12');
INSERT INTO `process_log` VALUES ('7', '21', '全部命令执行成功。', '2012-12-23 20:12:05');
INSERT INTO `process_log` VALUES ('8', '20', '全部命令执行成功。', '2012-12-23 20:12:05');
INSERT INTO `process_log` VALUES ('9', '21', '全部命令执行成功。', '2012-12-23 20:12:40');
INSERT INTO `process_log` VALUES ('10', '20', '全部命令执行成功。', '2012-12-23 20:12:42');
INSERT INTO `process_log` VALUES ('11', '19', '全部命令执行成功。', '2012-12-23 20:12:45');
INSERT INTO `process_log` VALUES ('12', '21', '全部命令执行成功。', '2012-12-23 20:28:19');
INSERT INTO `process_log` VALUES ('13', '21', '全部命令执行成功。', '2012-12-23 20:29:20');

-- ----------------------------
-- Table structure for `report_email`
-- ----------------------------
DROP TABLE IF EXISTS `report_email`;
CREATE TABLE `report_email` (
  `report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '报告邮件Id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '要发给的用户id',
  `task_id` bigint(20) unsigned NOT NULL,
  `sections` longtext COMMENT '报告章节(serialized string)',
  `attachment` longtext COMMENT '附件(serialized string)',
  `gen_datetime` datetime NOT NULL COMMENT '生成时间',
  `sent_datetime` datetime DEFAULT NULL COMMENT '发送时间',
  `sent` tinyint(3) unsigned DEFAULT '0' COMMENT '0: not sent; 1: sent',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of report_email
-- ----------------------------

-- ----------------------------
-- Table structure for `task`
-- ----------------------------
DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `task_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '创建任务的用户id',
  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT '任务状态. pending, active, prevent',
  `name` varchar(255) DEFAULT NULL COMMENT '任务名字',
  `limit` int(11) NOT NULL DEFAULT '-1' COMMENT '总共执行多少次, -1为无限次',
  `times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已经执行了多少次',
  `create_date` datetime NOT NULL COMMENT '任务创建时间',
  `alter_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '任务修改时间',
  PRIMARY KEY (`task_id`),
  KEY `user_id` (`user_id`),
  KEY `create_date` (`create_date`),
  KEY `alter_date` (`alter_date`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of task
-- ----------------------------
INSERT INTO `task` VALUES ('19', '12', 'active', '定时访问sina', '0', '3', '2012-12-09 23:46:17', '2012-12-09 23:46:17');
INSERT INTO `task` VALUES ('20', '12', 'active', '测试邮件任务', '0', '4', '2012-12-09 23:47:49', '2012-12-09 23:47:49');
INSERT INTO `task` VALUES ('21', '12', 'active', '两个都有', '998', '6', '2012-12-09 23:54:24', '2012-12-09 23:54:24');

-- ----------------------------
-- Table structure for `timing_process`
-- ----------------------------
DROP TABLE IF EXISTS `timing_process`;
CREATE TABLE `timing_process` (
  `process_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '处理id',
  `task_id` bigint(20) unsigned NOT NULL COMMENT '任务id',
  `skip` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不跳过，1跳过',
  `executed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0没执行过,1执行过',
  `gen_time` datetime DEFAULT NULL COMMENT '生成时间',
  `exec_time` datetime DEFAULT NULL COMMENT '实际执行时间',
  `plan_time` datetime DEFAULT NULL COMMENT '计划执行时间',
  PRIMARY KEY (`process_id`),
  KEY `plan_time` (`plan_time`,`skip`),
  KEY `gen_time` (`gen_time`),
  KEY `task_id` (`task_id`,`plan_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of timing_process
-- ----------------------------
INSERT INTO `timing_process` VALUES ('128', '17', '0', '0', '2012-12-23 01:19:28', null, '2013-01-01 00:00:00');
INSERT INTO `timing_process` VALUES ('129', '19', '0', '0', '2012-12-23 18:48:26', '2012-12-23 20:12:43', '2012-12-23 20:00:00');
INSERT INTO `timing_process` VALUES ('130', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-24 19:00:00');
INSERT INTO `timing_process` VALUES ('131', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-25 19:00:00');
INSERT INTO `timing_process` VALUES ('132', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-26 19:00:00');
INSERT INTO `timing_process` VALUES ('133', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-27 19:00:00');
INSERT INTO `timing_process` VALUES ('134', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-28 19:00:00');
INSERT INTO `timing_process` VALUES ('135', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-29 19:00:00');
INSERT INTO `timing_process` VALUES ('136', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-30 19:00:00');
INSERT INTO `timing_process` VALUES ('137', '19', '0', '0', '2012-12-23 18:48:26', null, '2012-12-31 19:00:00');
INSERT INTO `timing_process` VALUES ('138', '19', '0', '0', '2012-12-23 18:48:26', null, '2013-01-01 19:00:00');
INSERT INTO `timing_process` VALUES ('139', '20', '0', '0', '2012-12-23 18:53:52', '2012-12-23 20:12:42', '2012-12-23 20:00:00');
INSERT INTO `timing_process` VALUES ('140', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-24 19:00:00');
INSERT INTO `timing_process` VALUES ('141', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-25 19:00:00');
INSERT INTO `timing_process` VALUES ('142', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-26 19:00:00');
INSERT INTO `timing_process` VALUES ('143', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-27 19:00:00');
INSERT INTO `timing_process` VALUES ('144', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-28 19:00:00');
INSERT INTO `timing_process` VALUES ('145', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-29 19:00:00');
INSERT INTO `timing_process` VALUES ('146', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-30 19:00:00');
INSERT INTO `timing_process` VALUES ('147', '20', '0', '0', '2012-12-23 18:53:52', null, '2012-12-31 19:00:00');
INSERT INTO `timing_process` VALUES ('148', '20', '0', '0', '2012-12-23 18:53:52', null, '2013-12-01 19:00:00');
INSERT INTO `timing_process` VALUES ('149', '21', '0', '0', '2012-12-23 18:57:38', '2012-12-23 20:29:17', '2012-12-23 20:00:00');
INSERT INTO `timing_process` VALUES ('150', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-01-23 19:00:00');
INSERT INTO `timing_process` VALUES ('151', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-02-23 19:00:00');
INSERT INTO `timing_process` VALUES ('152', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-03-23 19:00:00');
INSERT INTO `timing_process` VALUES ('153', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-04-23 19:00:00');
INSERT INTO `timing_process` VALUES ('154', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-05-23 19:00:00');
INSERT INTO `timing_process` VALUES ('155', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-06-23 19:00:00');
INSERT INTO `timing_process` VALUES ('156', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-07-23 19:00:00');
INSERT INTO `timing_process` VALUES ('157', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-08-23 19:00:00');
INSERT INTO `timing_process` VALUES ('158', '21', '0', '0', '2012-12-23 18:57:38', null, '2013-09-23 19:00:00');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '经过md5加密的密码',
  `email` varchar(255) NOT NULL,
  `reg_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册时间戳',
  `auto_login_token` varchar(255) DEFAULT NULL,
  `auto_login_expire` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='oa_user\r\n用户表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('8', 'test1', '746927de0a4cb6be835d299e761e12e4', 'test1@email.com', '2012-11-28 06:11:18', 'b40d5fcd915a443e8174a6de5e23028d1354873077', '2013-01-06 00:00:00');
INSERT INTO `user` VALUES ('9', 'test2', '746927de0a4cb6be835d299e761e12e4', 'test2@email.com', '2012-11-28 06:12:07', '39546989e37910d93a9c93a566e6eaf11354679578', '2013-01-04 00:00:00');
INSERT INTO `user` VALUES ('10', 'test3', '746927de0a4cb6be835d299e761e12e4', 'test3@email.com', '2012-11-28 06:12:16', null, null);
INSERT INTO `user` VALUES ('11', 'test4', '746927de0a4cb6be835d299e761e12e4', 'test4@email.com', '2012-11-28 06:12:25', null, null);
INSERT INTO `user` VALUES ('12', 'test', '746927de0a4cb6be835d299e761e12e4', 'zgldh@hotmail.com', '2012-11-28 06:12:32', '86dd1ef95b0141f63ae5e7e86b9a9f981356259665', '2013-01-22 00:00:00');
