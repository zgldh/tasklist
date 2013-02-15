/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50528
Source Host           : localhost:3306
Source Database       : zgldhcom_tasklist

Target Server Type    : MYSQL
Target Server Version : 50528
File Encoding         : 65001

Date: 2012-12-28 23:27:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `weibo_link`
-- ----------------------------
DROP TABLE IF EXISTS `weibo_link`;
CREATE TABLE `weibo_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `access_token` varchar(255) DEFAULT NULL COMMENT '访问token',
  `remind_in` int(11) DEFAULT NULL,
  `expries_in` int(11) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL COMMENT '刷新token',
  `uid` bigint(20) DEFAULT NULL,
  `update_datetime` datetime NOT NULL COMMENT '更新时间戳',
  `user_data` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='oa_user\r\n用户表';

-- ----------------------------
-- Records of weibo_link
-- ----------------------------
