Create table reports.retention_campaigns (
ret_date date NOT NULL,
forum enum('Normal', 'Facebook'),
loginSource enum('Web','Mobile'),
mobileType enum('None', 'iOS','Android')
campaign_id int unsigned NOT NULL DEFAULT 0);
