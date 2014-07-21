CREATE TABLE IF NOT EXISTS simple_module_domain_entity (
	ID		int(11) unsigned NOT NULL AUTO_INCREMENT,
 	LID		char(2) COLLATE utf8_unicode_ci NOT NULL,
	TIMESTAMP_X	timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	ACTIVE		char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT "Y",
	SORT		int(11) NOT NULL DEFAULT '500',
	PRIMARY KEY	(ID)
);
DROP TRIGGER IF EXISTS simple_module_domain_entity_add_timestamp;
CREATE TRIGGER simple_module_domain_entity_add_timestamp BEFORE INSERT ON simple_module_domain_entity FOR EACH ROW SET NEW.TIMESTAMP_X = now();