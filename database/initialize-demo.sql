CREATE DATABASE banks_demo CHARACTER SET 'UTF8' COLLATE 'utf8_general_ci';

CREATE TABLE banks_demo.users(
	id VARCHAR(100) PRIMARY KEY,
	first_name VARCHAR(100) NOT NULL,
	last_name VARCHAR(100) NOT NULL,
	password_hashed VARCHAR(150) NOT NULL
)ENGINE=InnoDB;

CREATE TABLE banks_demo.owners(
	id TINYINT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	
	user_id VARCHAR(100) NOT NULL,
	INDEX fk_user_id(user_id),
	FOREIGN KEY (user_id) REFERENCES banks_demo.users(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	first_name VARCHAR(100) NOT NULL,
	last_name VARCHAR(100) NOT NULL
)ENGINE=InnoDB;

CREATE TABLE banks_demo.banks(
	id TINYINT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL
)ENGINE=InnoDB;

CREATE TABLE banks_demo.accounts(
	id TINYINT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	
	owner_id TINYINT(3) UNSIGNED NOT NULL,
	INDEX fk_owner_id(owner_id),
	FOREIGN KEY (owner_id) REFERENCES banks_demo.owners(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	bank_id TINYINT(3) UNSIGNED NOT NULL,
	INDEX fk_bank_id(bank_id),
	FOREIGN KEY (bank_id) REFERENCES banks_demo.banks(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	name VARCHAR(100) NOT NULL,
	amount_initial FLOAT(10, 2) NOT NULL DEFAULT 0,
	amount_current FLOAT(10, 2) NOT NULL DEFAULT 0,
	
	date_initial DATE NOT NULL
)ENGINE=InnoDB;

CREATE TABLE banks_demo.accounts_statistics(
	id SMALLINT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	
	account_id TINYINT(3) UNSIGNED NOT NULL,
	INDEX fk_account_id(account_id),
	FOREIGN KEY (account_id) REFERENCES banks_demo.accounts(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	`type` ENUM('DAY', 'MONTH', 'YEAR') NOT NULL,
	`date` DATE NOT NULL,
	
	UNIQUE idx_account_type_date(account_id, `type`, `date`), 
	
	amount FLOAT(10, 2) NOT NULL DEFAULT 0
)ENGINE=InnoDB;

CREATE TABLE banks_demo.operations(
	id SMALLINT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	
	account_id TINYINT(3) UNSIGNED NOT NULL,
	INDEX fk_account_id(account_id),
	FOREIGN KEY (account_id) REFERENCES banks_demo.accounts(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	`type` ENUM('CREDIT', 'DEBIT') NOT NULL,
	name VARCHAR(100) NOT NULL,
	`amount` FLOAT(10, 2) NOT NULL DEFAULT 0,
	
	`date` DATE NOT NULL,
	INDEX idx_date(`date`),
	
	computed TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
)ENGINE=InnoDB;

CREATE TABLE banks_demo.budgets(
	`id` SMALLINT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`year` SMALLINT(4) UNSIGNED,
	
	owner_id TINYINT(3) UNSIGNED NOT NULL,
	INDEX fk_owner_id(owner_id),
	FOREIGN KEY (owner_id) REFERENCES banks_demo.owners(id) ON DELETE CASCADE ON UPDATE CASCADE,
	
	`type` ENUM('CREDIT', 'DEBIT') NOT NULL,
	`frequency` ENUM('DAILY', 'MONTHLY', 'YEARLY') NOT NULL,
	`value` FLOAT(10, 2) NOT NULL DEFAULT 0,
	`name` VARCHAR(100) NOT NULL
)ENGINE=InnoDB;