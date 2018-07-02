Create table Testimonials (
id int unsigned primary key auto_increment,
image varchar(200) NOT NULL,
name varchar(50) NOT NULL,
state char(2) NOT NULL,
description varchar(200),
testimonial varchar(300),
winDate date,
created timestamp default current_timestamp,
updated timestamp default current_timestamp on update current_timestamp);
