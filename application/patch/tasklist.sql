/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : zgldhcom_tasklist

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2012-12-23 02:35:45
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of command
-- ----------------------------
INSERT INTO `command` VALUES ('8', '16', 'url-request', '{\"url\":\"http:\\/\\/www.zgldh.com\"}', '2012-12-20 00:33:09');
INSERT INTO `command` VALUES ('9', '17', 'send-email', '{\"recipients\":[\"test@email.com\",\"zgldh@hotmail.com\"],\"content\":\"<p>\\r\\n\\tdfdsfdf\\r\\n<\\/p>\\r\\n<p>\\r\\n\\tdsds<img src=\\\"\\/uploads\\/2012-12-15\\/139a58a2044e93bf380487fda9bfefaf.gif\\\" alt=\\\"\\\" \\/><img src=\\\"\\/uploads\\/2012-12-15\\/9370ab9312b7b76ab7bb6419489c482a.jpg\\\" alt=\\\"\\\" \\/> \\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<br \\/>\\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<img src=\\\"\\/uploads\\/2012-12-15\\/64db23896f7899b019bab4ea7dd27052.png\\\" alt=\\\"\\\" \\/> \\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<br \\/>\\r\\n<\\/p>\\r\\n<p>\\r\\n\\t<img src=\\\"\\/uploads\\/2012-12-23\\/a96e186a1778421780d83ce77404ca43.jpg\\\" alt=\\\"\\\" \\/> \\r\\n<\\/p>\"}', '2012-12-23 01:19:28');
INSERT INTO `command` VALUES ('10', '18', 'url-request', '{\"url\":\"http:\\/\\/www.sina.com\"}', '2012-12-13 23:59:20');
INSERT INTO `command` VALUES ('11', '19', 'url-request', '{\"url\":\"http:\\/\\/www.sina.com\"}', '2012-12-12 21:40:01');
INSERT INTO `command` VALUES ('12', '20', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"++\"}', '2012-12-12 21:40:11');
INSERT INTO `command` VALUES ('14', '21', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"aaa\"}', '2012-12-12 21:40:07');
INSERT INTO `command` VALUES ('15', '16', 'send-email', '{\"recipients\":[\"test@email.com\"],\"content\":\"\\u00a0\\u5e76\\u4e0d<br \\/>\"}', '2012-12-20 00:33:10');

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
INSERT INTO `condition` VALUES ('16', '16', 'date-static', '{\"year\":2013,\"month\":\"\",\"day\":\"\",\"hour\":0}', '2012-12-20 00:33:09');
INSERT INTO `condition` VALUES ('17', '17', 'date-static', '{\"year\":2013,\"month\":1,\"day\":1,\"hour\":0}', '2012-12-23 01:19:28');
INSERT INTO `condition` VALUES ('18', '18', 'date-static', '{\"year\":2013,\"month\":1,\"day\":31,\"hour\":0}', '2012-12-13 23:59:20');
INSERT INTO `condition` VALUES ('19', '19', 'date-static', '{\"year\":\"\",\"month\":\"8\",\"day\":\"\",\"hour\":\"2\"}', '2012-12-12 21:40:01');
INSERT INTO `condition` VALUES ('20', '20', 'date-static', '{\"year\":\"\",\"month\":\"10\",\"day\":\"10\",\"hour\":\"0\"}', '2012-12-12 21:40:11');
INSERT INTO `condition` VALUES ('21', '21', 'date-static', '{\"year\":\"2015\",\"month\":\"1\",\"day\":\"3\",\"hour\":\"0\"}', '2012-12-12 21:40:07');

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of process_log
-- ----------------------------

-- ----------------------------
-- Table structure for `report_email`
-- ----------------------------
DROP TABLE IF EXISTS `report_email`;
CREATE TABLE `report_email` (
  `report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '报告邮件Id',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '要发给的用户id',
  `task_id` bigint(20) unsigned NOT NULL,
  `sections` longtext COMMENT '报告章节(serialized array)',
  `attachment` longtext COMMENT '附件(serialize array)',
  `gen_datetime` datetime NOT NULL COMMENT '生成时间',
  `sent_datetime` datetime DEFAULT NULL COMMENT '发送时间',
  `sent` tinyint(3) unsigned DEFAULT '0' COMMENT '0: not sent; 1: sent',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

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
INSERT INTO `task` VALUES ('16', '12', 'active', 'Task 16', '5', '3', '2012-12-09 23:39:23', '2012-12-09 23:39:23');
INSERT INTO `task` VALUES ('17', '12', 'active', 'Task 17', '0', '3', '2012-12-09 23:41:58', '2012-12-09 23:41:58');
INSERT INTO `task` VALUES ('18', '12', 'active', 'Task 18', '22', '3', '2012-12-09 23:45:30', '2012-12-09 23:45:30');
INSERT INTO `task` VALUES ('19', '12', 'pause', 'lol', '0', '0', '2012-12-09 23:46:17', '2012-12-09 23:46:17');
INSERT INTO `task` VALUES ('20', '12', 'pause', '<b>sdfds</b>', '0', '0', '2012-12-09 23:47:49', '2012-12-09 23:47:49');
INSERT INTO `task` VALUES ('21', '12', 'pause', 'Task 212', '998', '0', '2012-12-09 23:54:24', '2012-12-09 23:54:24');

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
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of timing_process
-- ----------------------------
INSERT INTO `timing_process` VALUES ('100', '16', '0', '0', '2012-12-12 22:40:23', '2012-12-23 01:29:58', '2012-12-23 02:00:00');
INSERT INTO `timing_process` VALUES ('109', '18', '0', '0', '2012-12-13 23:59:20', '2012-12-23 01:29:58', '2012-12-23 02:00:00');
INSERT INTO `timing_process` VALUES ('121', '17', '0', '0', '2012-12-15 17:31:48', '2012-12-23 01:29:56', '2012-12-23 02:00:00');
INSERT INTO `timing_process` VALUES ('128', '17', '0', '0', '2012-12-23 01:19:28', null, '2013-01-01 00:00:00');

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
INSERT INTO `user` VALUES ('12', 'test', '746927de0a4cb6be835d299e761e12e4', 'test@email.com', '2012-11-28 06:12:32', '51332bbae860a4f51d7792fa0fa3225d1356186235', '2013-01-21 00:00:00');
