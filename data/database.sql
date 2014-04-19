-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 19, 2014 at 07:50 AM
-- Server version: 5.6.11
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `acctopen`
--
CREATE DATABASE IF NOT EXISTS `acctopen` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `acctopen`;

-- --------------------------------------------------------

--
-- Table structure for table `tb_account`
--

CREATE TABLE IF NOT EXISTS `tb_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payer` int(11) NOT NULL,
  `amount` float NOT NULL DEFAULT '0',
  `place` varchar(50) DEFAULT NULL,
  `time` date DEFAULT NULL,
  `time_apm` tinyint(4) DEFAULT NULL COMMENT '一天中具体时段：1:早上 2:上午 3:中午 4:下午 5:晚上',
  `content` varchar(100) DEFAULT NULL COMMENT '内容',
  `beneficiary` varchar(100) NOT NULL DEFAULT '{}' COMMENT '受益人两端大括号中间以逗号分隔，若前面有''#''则为用户ID，否则为用户姓名，如{#3,张三,#23,#4}',
  `note` varchar(50) DEFAULT NULL COMMENT '备注',
  `time_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `poster` int(11) NOT NULL COMMENT '发布者',
  PRIMARY KEY (`id`),
  KEY `fk_tb_account_tb_user1_idx` (`payer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_addressbook`
--

CREATE TABLE IF NOT EXISTS `tb_addressbook` (
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `addressbook` text,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户常用联系人列表';

-- --------------------------------------------------------

--
-- Table structure for table `tb_announcement`
--

CREATE TABLE IF NOT EXISTS `tb_announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `content` text,
  `time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_update` datetime NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `priority` tinyint(4) NOT NULL DEFAULT '3' COMMENT '优先级（1:非常重要 2:重要 3:普通 4:不重要 5:垃圾）',
  `public` bit(1) NOT NULL DEFAULT b'1' COMMENT '是否公开（1:公开 0:不公开）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_config`
--

CREATE TABLE IF NOT EXISTS `tb_config` (
  `name` varchar(24) NOT NULL COMMENT '配置名称',
  `value` varchar(40) NOT NULL COMMENT '配置值',
  `role` tinyint(4) NOT NULL DEFAULT '0' COMMENT '作用角色（-1:游客 0:全部 1:管理员 2:普通用户 3:受限用户）',
  `note` varchar(100) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`name`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_log`
--

CREATE TABLE IF NOT EXISTS `tb_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `ip` varchar(20) NOT NULL,
  `content` varchar(100) DEFAULT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT '2' COMMENT '优先级，1到5优先级依次提高',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tb_log_tb_user_idx` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1253 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_session`
--

CREATE TABLE IF NOT EXISTS `tb_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `key` varchar(32) NOT NULL COMMENT 'session值',
  `category` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类别(默认1:session 2:密码重置url)',
  `expires_in` int(11) NOT NULL DEFAULT '129600' COMMENT '有效时间（默认半个月）',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE IF NOT EXISTS `tb_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL,
  `salt` varchar(6) NOT NULL COMMENT 'pasword密钥',
  `email` varchar(32) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '3' COMMENT '角色：1:管理员 2:普通用户 3:游客',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
