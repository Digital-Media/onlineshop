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
-- ------------------------
-- The FK user_iduser will not be implemented as alter table add constraint foreign key
-- But the column is listed to show, that there could be an relationship to a table user
-- --------------------

CREATE TABLE payment1 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
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
    owner_name varchar(50) NOT NULL,
    card_number varchar(50) NOT NULL,
    expiration_date varchar(50) NOT NULL,
    pin varchar(50) NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE bank_account2 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
    bank_code varchar(50) NOT NULL,
    account_number varchar(50) NOT NULL,
    bank_name varchar(50) NOT NULL,
    collection_authorisation varchar(50) NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE mobile_payment2 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
    provider_name varchar(50) NOT NULL,
    customer_number varchar(50) NOT NULL,
    mobile_number varchar(50) NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--

CREATE TABLE payment3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE credit_card3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    card_number varchar(50) NOT NULL,
    expiration_date varchar(50) NOT NULL,
    pin varchar(50) NOT NULL,
    PRIMARY KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE bank_account3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    bank_code varchar(50) NOT NULL,
    account_number varchar(50) NOT NULL,
    bank_name varchar(50) NOT NULL,
    collection_authorisation varchar(50) NOT NULL,
    PRIMARY KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE mobile_payment3 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    provider_name varchar(50) NOT NULL,
    customer_number varchar(50) NOT NULL,
    mobile_number varchar(50) NOT NULL,
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

CREATE TABLE payment4 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
    paymentcol1_key varchar(50) NOT NULL,
    paymentcol1_value varchar(50) NOT NULL,
    paymentcol2_key varchar(50) NULL,
    paymentcol2_value varchar(50) NULL,
    paymentcol3_key varchar(50) NULL,
    paymentcol3_value varchar(50) NULL,
    paymentcol4_key varchar(50) NULL,
    paymentcol4_value varchar(50) NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--

CREATE TABLE payment5 (
    idpayment bigint unsigned NOT NULL AUTO_INCREMENT,
    user_iduser bigint unsigned NOT NULL,
    owner_name varchar(50) NOT NULL,
    PRIMARY KEY (idpayment),
    KEY (user_iduser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE attribute_types (
    idattribute_types bigint unsigned NOT NULL AUTO_INCREMENT,
    attribute_name  varchar(255) NOT NULL,
    attribute_property  varchar(255) NOT NULL,
    attr_is_null bool NOT NULL,
    PRIMARY KEY (idattribute_types)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE attributes (
    idattributes bigint unsigned NOT NULL AUTO_INCREMENT,
    idattribute_types bigint unsigned NOT NULL,
    idpayment bigint unsigned NOT NULL,
    PRIMARY KEY (idattributes),
    KEY (idattribute_types),
    KEY (idpayment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE data_int (
    idattributes bigint unsigned NOT NULL,
    attribute_value bigint NOT NULL,
    PRIMARY KEY (idattributes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE data_varchar (
    idattributes bigint unsigned NOT NULL,
    attribute_value varchar(255) NOT NULL,
    PRIMARY KEY (idattributes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE data_decimal (
    idattributes bigint unsigned NOT NULL,
    attribute_value decimal(10,2) NOT NULL,
    PRIMARY KEY (idattributes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE data_date (
    idattributes bigint unsigned NOT NULL,
    attribute_value date NOT NULL,
    PRIMARY KEY (idattributes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE data_text (
    idattributes bigint unsigned NOT NULL,
    attribute_value text NOT NULL,
    PRIMARY KEY (idattributes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

alter table attributes
    ADD CONSTRAINT
        FOREIGN KEY (idattribute_types)
            REFERENCES attribute_types (idattribute_types),
    ADD CONSTRAINT
        FOREIGN KEY (idpayment)
            REFERENCES payment5 (idpayment);

alter table data_int
    ADD CONSTRAINT
        FOREIGN KEY (idattributes)
            REFERENCES attributes (idattributes);

alter table data_varchar
    ADD CONSTRAINT
        FOREIGN KEY (idattributes)
            REFERENCES attributes (idattributes);

alter table data_decimal
    ADD CONSTRAINT
        FOREIGN KEY (idattributes)
            REFERENCES attributes (idattributes);

alter table data_date
    ADD CONSTRAINT
        FOREIGN KEY (idattributes)
            REFERENCES attributes (idattributes);

alter table data_text
    ADD CONSTRAINT
        FOREIGN KEY (idattributes)
            REFERENCES attributes (idattributes);



/*
select p.products_image, pd.products_name, p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from products_description pd, products p left join manufacturers m on p.manufacturers_id = m.manufacturers_id left join specials s on p.products_id = s.products_id, products_to_categories p2c where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '1' and p2c.categories_id = '4' order by pd.products_name limit 0, 20

select pimage.value as products_image, pname.value as products_name, p.products_id, pmanuID.value manufacturers_id, pprice.value products_price, ptci.value products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, pprice.value) as final_price from products_entity p join eav_attribute as eavattr_tci on (p.entity_id = eavattr_tci.entity_id) join attribute as attr_tci on (eavattr_tci.attribute_id = attr_tci.attribute_id) join product_value_int as ptci on (eavattr_tci.eav_attribute_id = ptci.eav_attribute_id) and eavattr_tci.attribute_id = attr_tci.attribute_id and attr_tci.attribute_name = 'tax_class_id' join eav_attribute as eavattr_price on (p.entity_id = eavattr_price.entity_id) join attribute as attr_price on (eavattr_price.attribute_id = attr_price.attribute_id) join product_value_decimal as pprice on (eavattr_price.eav_attribute_id pprice.eav_attribute_id) and eavattr_price.attribute_id = attr_price.attribute_id and attr_price.attribute_name = 'price' join eav_attribute as eavattr_manuID on (p.entity_id = eavattr_manuID.entity_id) join attribute as attr_manuID on (eavattr_manuID.attribute_id = attr_manuID.attribute_id) join product_value_int as pmanuID on (eavattr_manuID.eav_attribute_id = pmanuID.eav_attribute_id) and eavattr_manuID.attribute_id = attr_manuID.attribute_id and attr_manuID.attribute_name = 'manufacturers_id' join eav_attribute as eavattr_status on (p.entity_id = eavattr_status.entity_id) join attribute as attr_status on (eavattr_status.attribute_id = attr_status.attribute_id) join product_value_int as pstatus on (eavattr_status.eav_attribute_id = pstatus.eav_attribute_id) and eavattr_status.attribute_id = attr_status.attribute_id and attr_status.attribute_name = 'status' join eav_attribute as eavattr_name on (p.entity_id = eavattr_name.entity_id) join attribute as attr_name on (eavattr_name.attribute_id = attr_name.attribute_id) join product_value_varchar as pname on (eavattr_name.eav_attribute_id = pname.eav_attribute_id) and eavattr_name.attribute_id = attr_name.attribute_id and attr_name.attribute_name = 'name'join eav_attribute as eavattr_image on (p.entity_id = eavattr_image.entity_id) join attribute as attr_image on (eavattr_image.attribute_id = attr_image.attribute_id) join product_value_varchar as pimage on (eavattr_image.eav_attribute_id = pimage.eav_attribute_id) and eavattr_image.attribute_id = attr_image.attribute_id and attr_image.attribute_name = 'image' left join manufacturers m on pmanuID.value = m.manufacturers_id left join specials s on p.products_id = s.products_id, products_to_categories p2c where pstatus.value = '1' and p.products_id = p2c.products_id and p2c.categories_id = '4' order by pname.value limit 0, 20
*/




