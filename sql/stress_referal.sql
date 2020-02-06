/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50725
Source Host           : localhost:3306
Source Database       : l2pvp

Target Server Type    : MYSQL
Target Server Version : 50725
File Encoding         : 65001

Date: 2020-02-05 23:30:30
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `stress_referal`
-- ----------------------------
DROP TABLE IF EXISTS `stress_referal`;
CREATE TABLE `stress_referal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_referer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `charId` int(10) unsigned NOT NULL,
  `char_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `success` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of stress_referal
-- ----------------------------
