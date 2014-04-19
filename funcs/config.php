<?php  if ( ! defined('PAGENAME')) exit('No direct script access allowed');

global $SESSION_PREFIX;   // session前缀
global $COOKIE_PREFIX;  // cookie前缀
global $PAGE_SIZE;   // 每页展示数据条数
global $database;   // 数据库配置
global $EMAIL_CONFIG;
global $COOKIE_EXPIRES_IN;  // cookie过期时间
global $DAYS_ACCT_EDIT; // 允许修改的账目天数
global $LOG_SAVE_PERUSER;  // 每个用户保留的日志数
global $LOG_SAVE_NOUSER;  // 游客所保留的日志数
global $LOG_CLEAN_INTERVAL;  // 日志清除的间隔数

$SESSION_PREFIX = 'ACCTOPEN_';
$COOKIE_PREFIX = 'ACCTOPEN_';
//$PAGE_SIZE = 12;
//$COOKIE_EXPIRES_IN = 86400*15;
//$DAYS_ACCT_EDIT = 2;
//$LOG_SAVE_PERUSER = 200;
//$LOG_SAVE_NOUSER = 500;
//$LOG_CLEAN_INTERVAL = 1000;

// 数据库配置（本地）
$database['host'] = 'localhost';
$database['port'] = '3306';
$database['dbname'] = 'acctopen';
$database['username'] = 'root';
$database['password'] = '123456';
