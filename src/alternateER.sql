/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `onlineshop`
--
DROP SCHEMA  IF EXISTS alternate_er;
CREATE SCHEMA IF NOT EXISTS alternate_er DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE alternate_er;

-- --------------------

CREATE TABLE payment1 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    card_number varchar(50) NULL,
    expiration_date varchar(50) NULL,
    bank_code varchar(50) NULL,
    account_number varchar(50) NULL,
    bank_name varchar(50) NULL,
    provider_name varchar(50) NULL,
    customer_number varchar(50) NULL,
    mobile_number varchar(50) NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--

CREATE TABLE credit_card2 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NULL,
    card_number varchar(50) NULL,
    expiration_date varchar(50) NULL,
    pin varchar(50) NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE bank_account2 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NULL,
    bank_code varchar(50) NULL,
    account_number varchar(50) NULL,
    bank_name varchar(50) NULL,
    collection_authorisation varchar(50) NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE mobile_payment2 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NULL,
    provider_name varchar(50) NULL,
    customer_number varchar(50) NULL,
    mobile_number varchar(50) NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--

CREATE TABLE payment3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE credit_card3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    card_number varchar(50) NULL,
    expiration_date varchar(50) NULL,
    pin varchar(50) NULL,
    PRIMARY KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE bank_account3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    bank_code varchar(50) NULL,
    account_number varchar(50) NULL,
    bank_name varchar(50) NULL,
    collection_authorisation varchar(50) NULL,
    PRIMARY KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE mobile_payment3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    owner_name varchar(50) NULL,
    provider_name varchar(50) NULL,
    customer_number varchar(50) NULL,
    mobile_number varchar(50) NULL,
    PRIMARY KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

alter table credit_card3
    ADD CONSTRAINT
        FOREIGN KEY (idpayment)
            REFERENCES payment3 (idpayment);

alter table bank_account3
    ADD CONSTRAINT
        FOREIGN KEY (idpayment)
            REFERENCES payment3 (idpayment);

alter table mobile_payment3
    ADD CONSTRAINT
        FOREIGN KEY (idpayment)
            REFERENCES payment3 (idpayment);

--




