ALTER TABLE kizzang.PlayPeriod CHANGE COLUMN id id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;
Alter table GameCount add foreign key (playPeriodId) references PlayPeriod(id) on delete cascade on update cascade;

CREATE TABLE archive.Players (
  id int(10) unsigned NOT NULL,
  first_name varchar(100) DEFAULT NULL,
  last_name varchar(100) DEFAULT NULL,
  email varchar(200) DEFAULT NULL,
  paypal_email varchar(200) DEFAULT NULL,
  dob date DEFAULT NULL,
  city varchar(100) DEFAULT NULL,
  state varchar(2) DEFAULT NULL,
  zipcode int(11) DEFAULT NULL,
  phone bigint(20) DEFAULT NULL,
  is_suspended tinyint(4) DEFAULT NULL,
  is_deleted tinyint(4) DEFAULT NULL,
  ip_address bigint(20) DEFAULT NULL,
  created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  updated timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;