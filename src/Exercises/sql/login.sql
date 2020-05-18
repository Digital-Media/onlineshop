-- DROP SCHEMA IF EXISTS login;
-- CREATE SCHEMA IF NOT EXISTS login DEFAULT CHARACTER SET utf8;
-- GRANT ALL PRIVILEGES ON login.* TO 'onlineshop'@'localhost';
USE login;
-- place your solutions here

CREATE TABLE login ( iduser BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , `product_name` VARCHAR(255) NOT NULL , `price` DECIMAL(10,2) NOT NULL , `description` TEXT NOT NULL , `date_added` DATE NOT NULL , PRIMARY KEY (`idproduct`)) ENGINE = InnoDB;




-- end solution

-- DROP SCHEMA login;

-- REVOKE ALL PRIVILEGES ON login.* FROM 'onlineshop'@'localhost';
