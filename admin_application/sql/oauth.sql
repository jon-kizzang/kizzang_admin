CREATE DATABASE  IF NOT EXISTS OAuth;
USE OAuth;
DROP TABLE IF EXISTS quickbooks_config;
CREATE TABLE quickbooks_config (
  quickbooks_config_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  qb_username varchar(40) NOT NULL,
  module varchar(40) NOT NULL,
  cfgkey varchar(40) NOT NULL,
  cfgval varchar(40) NOT NULL,
  cfgtype varchar(40) NOT NULL,
  cfgopts text NOT NULL,
  write_datetime datetime NOT NULL,
  mod_datetime datetime NOT NULL,
  PRIMARY KEY (quickbooks_config_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS quickbooks_log;
CREATE TABLE quickbooks_log (
  quickbooks_log_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  quickbooks_ticket_id int(10) unsigned DEFAULT NULL,
  batch int(10) unsigned NOT NULL,
  msg text NOT NULL,
  log_datetime datetime NOT NULL,
  PRIMARY KEY (quickbooks_log_id),
  KEY quickbooks_ticket_id (quickbooks_ticket_id),
  KEY batch (batch)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS quickbooks_oauth;
CREATE TABLE quickbooks_oauth (
  quickbooks_oauth_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  app_username varchar(255) NOT NULL,
  app_tenant varchar(255) NOT NULL,
  oauth_request_token varchar(255) DEFAULT NULL,
  oauth_request_token_secret varchar(255) DEFAULT NULL,
  oauth_access_token varchar(255) DEFAULT NULL,
  oauth_access_token_secret varchar(255) DEFAULT NULL,
  qb_realm varchar(32) DEFAULT NULL,
  qb_flavor varchar(12) DEFAULT NULL,
  qb_user varchar(64) DEFAULT NULL,
  request_datetime datetime NOT NULL,
  access_datetime datetime DEFAULT NULL,
  touch_datetime datetime DEFAULT NULL,
  PRIMARY KEY (quickbooks_oauth_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO quickbooks_oauth VALUES (2,'DO_NOT_CHANGE_ME','12345','qyprdTnq8cb8buPv4JMAtBDGkeT9TlpT9sWBHn950DaMSNZ7','eQfM0SliF4cN3U9qUtmm4ekIOk760e4VOxAvey8Y','8FSadfEi+09VBb4hrjdsi3k2BJgX1RWu8p2gSvAnMkBJ/Q8ENwLWTziMqVfKGw0hnKapJvj3kslOvOS2bfmwCh18dyPP/MpQXRfw+R+hyChquSa0cn7kGmEGU7TTjPz7c1y0WkZw9IUXjPw0JlhNscewpuJ4YaSuQv4ZPtyTo3b9lQJxBMg/XCm3v12qbQ==','R885C+G1VEHmAZNDwWtaNIz9zoNtRJW7hcLz0gEx13SUoNB75yJ3SYl6pYomlLxXG/0WbIrldnqKgTebykaB0cwrKfhiMlnL5Rp3ahpWLJ5hgoS5MPZp0xInq81/m5Fu1+QvYdfbQWQb5xsV/gMBYRJZHC8aU2Bukj1PRmalqkgQjYKXB7k=','1315132800','QBO',NULL,'2015-10-09 16:46:47','2015-10-09 16:47:10','2016-08-22 16:00:02');
DROP TABLE IF EXISTS quickbooks_queue;
CREATE TABLE quickbooks_queue (
  quickbooks_queue_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  quickbooks_ticket_id int(10) unsigned DEFAULT NULL,
  qb_username varchar(40) NOT NULL,
  qb_action varchar(32) NOT NULL,
  ident varchar(40) NOT NULL,
  extra text,
  qbxml text,
  priority int(10) unsigned DEFAULT '0',
  qb_status char(1) NOT NULL,
  msg text,
  enqueue_datetime datetime NOT NULL,
  dequeue_datetime datetime DEFAULT NULL,
  PRIMARY KEY (quickbooks_queue_id),
  KEY quickbooks_ticket_id (quickbooks_ticket_id),
  KEY priority (priority),
  KEY qb_username (qb_username,qb_action,ident,qb_status),
  KEY qb_status (qb_status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS quickbooks_recur;
CREATE TABLE quickbooks_recur (
  quickbooks_recur_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  qb_username varchar(40) NOT NULL,
  qb_action varchar(32) NOT NULL,
  ident varchar(40) NOT NULL,
  extra text,
  qbxml text,
  priority int(10) unsigned DEFAULT '0',
  run_every int(10) unsigned NOT NULL,
  recur_lasttime int(10) unsigned NOT NULL,
  enqueue_datetime datetime NOT NULL,
  PRIMARY KEY (quickbooks_recur_id),
  KEY qb_username (qb_username,qb_action,ident),
  KEY priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS quickbooks_ticket;
CREATE TABLE quickbooks_ticket (
  quickbooks_ticket_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  qb_username varchar(40) NOT NULL,
  ticket char(36) NOT NULL,
  processed int(10) unsigned DEFAULT '0',
  lasterror_num varchar(32) DEFAULT NULL,
  lasterror_msg varchar(255) DEFAULT NULL,
  ipaddr char(15) NOT NULL,
  write_datetime datetime NOT NULL,
  touch_datetime datetime NOT NULL,
  PRIMARY KEY (quickbooks_ticket_id),
  KEY ticket (ticket)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
DROP TABLE IF EXISTS quickbooks_user;
CREATE TABLE quickbooks_user (
  qb_username varchar(40) NOT NULL,
  qb_password varchar(255) NOT NULL,
  qb_company_file varchar(255) DEFAULT NULL,
  qbwc_wait_before_next_update int(10) unsigned DEFAULT '0',
  qbwc_min_run_every_n_seconds int(10) unsigned DEFAULT '0',
  status char(1) NOT NULL,
  write_datetime datetime NOT NULL,
  touch_datetime datetime NOT NULL,
  PRIMARY KEY (qb_username)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;