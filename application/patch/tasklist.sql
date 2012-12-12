/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : zgldhcom_tasklist

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2012-12-12 22:36:26
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
  `parameters` text COMMENT '本命令的参数',
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`command_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of command
-- ----------------------------
INSERT INTO `command` VALUES ('8', '16', 'url-request', '{\"url\":\"http:\\/\\/www.zgldh.com\"}', '2012-12-12 21:39:51');
INSERT INTO `command` VALUES ('9', '17', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"none\"}', '2012-12-12 22:34:18');
INSERT INTO `command` VALUES ('10', '18', 'url-request', '{\"url\":\"asdfasdf\"}', '2012-12-12 21:39:59');
INSERT INTO `command` VALUES ('11', '19', 'url-request', '{\"url\":\"http:\\/\\/www.sina.com\"}', '2012-12-12 21:40:01');
INSERT INTO `command` VALUES ('12', '20', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"++\"}', '2012-12-12 21:40:11');
INSERT INTO `command` VALUES ('14', '21', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"aaa\"}', '2012-12-12 21:40:07');
INSERT INTO `command` VALUES ('15', '16', 'send-email', '{\"recipients\":\"test@email.com;\",\"content\":\"\\u00a0\\u5e76\\u4e0d<br \\/>\"}', '2012-12-12 21:39:51');

-- ----------------------------
-- Table structure for `condition`
-- ----------------------------
DROP TABLE IF EXISTS `condition`;
CREATE TABLE `condition` (
  `condition_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL COMMENT '条件所属的任务',
  `type` varchar(255) NOT NULL COMMENT '条件类型',
  `parameters` text COMMENT '本条件的参数',
  `update_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of condition
-- ----------------------------
INSERT INTO `condition` VALUES ('16', '16', 'date-static', '{\"year\":\"2013\",\"month\":\"\",\"day\":\"\",\"hour\":\"0\"}', '2012-12-12 21:39:51');
INSERT INTO `condition` VALUES ('17', '17', 'date-static', '{\"year\":\"2013\",\"month\":\"2\",\"day\":\"\",\"hour\":\"0\"}', '2012-12-12 22:34:18');
INSERT INTO `condition` VALUES ('18', '18', 'date-static', '{\"year\":\"2014\",\"month\":\"\",\"day\":\"31\",\"hour\":\"0\"}', '2012-12-12 21:39:59');
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of process_log
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
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of task
-- ----------------------------
INSERT INTO `task` VALUES ('16', '12', 'active', 'Task 16', '0', '0', '2012-12-09 23:39:23', '2012-12-09 23:39:23');
INSERT INTO `task` VALUES ('17', '12', 'pause', 'Task 17', '0', '0', '2012-12-09 23:41:58', '2012-12-09 23:41:58');
INSERT INTO `task` VALUES ('18', '12', 'active', 'Task 18', '22', '0', '2012-12-09 23:45:30', '2012-12-09 23:45:30');
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
  `skip` bit(1) NOT NULL DEFAULT b'0' COMMENT '0不跳过，1跳过',
  `executed` bit(1) NOT NULL DEFAULT b'0' COMMENT '0没执行过,1执行过',
  `gen_time` datetime DEFAULT NULL COMMENT '生成时间',
  `exec_time` datetime DEFAULT NULL COMMENT '实际执行时间',
  `plan_time` datetime DEFAULT NULL COMMENT '计划执行时间',
  PRIMARY KEY (`process_id`),
  KEY `plan_time` (`plan_time`,`skip`),
  KEY `gen_time` (`gen_time`),
  KEY `task_id` (`task_id`,`plan_time`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Records of timing_process
-- ----------------------------
INSERT INTO `timing_process` VALUES ('95', '18', '', '', '2012-12-12 22:34:29', null, '2014-12-31 00:00:00');
INSERT INTO `timing_process` VALUES ('94', '18', '', '', '2012-12-12 22:34:29', null, '2014-10-31 00:00:00');
INSERT INTO `timing_process` VALUES ('93', '18', '', '', '2012-12-12 22:34:29', null, '2014-08-31 00:00:00');
INSERT INTO `timing_process` VALUES ('92', '18', '', '', '2012-12-12 22:34:29', null, '2014-07-31 00:00:00');
INSERT INTO `timing_process` VALUES ('91', '18', '', '', '2012-12-12 22:34:29', null, '2014-05-31 00:00:00');
INSERT INTO `timing_process` VALUES ('90', '18', '', '', '2012-12-12 22:34:29', null, '2014-03-31 00:00:00');
INSERT INTO `timing_process` VALUES ('89', '18', '', '', '2012-12-12 22:34:29', null, '2014-01-31 00:00:00');
INSERT INTO `timing_process` VALUES ('78', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-10 00:00:00');
INSERT INTO `timing_process` VALUES ('77', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-09 00:00:00');
INSERT INTO `timing_process` VALUES ('76', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-08 00:00:00');
INSERT INTO `timing_process` VALUES ('75', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-07 00:00:00');
INSERT INTO `timing_process` VALUES ('74', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-06 00:00:00');
INSERT INTO `timing_process` VALUES ('73', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-05 00:00:00');
INSERT INTO `timing_process` VALUES ('72', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-04 00:00:00');
INSERT INTO `timing_process` VALUES ('71', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-03 00:00:00');
INSERT INTO `timing_process` VALUES ('70', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-02 00:00:00');
INSERT INTO `timing_process` VALUES ('69', '16', '', '', '2012-12-12 22:34:23', null, '2013-01-01 00:00:00');

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
INSERT INTO `user` VALUES ('12', 'test', '746927de0a4cb6be835d299e761e12e4', 'test@email.com', '2012-11-28 06:12:32', '62a871395c9b55e1a7f7e7a8e34723321355319515', '2013-01-11 00:00:00');
