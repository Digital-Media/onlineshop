GRANT ALL PRIVILEGES ON login.* TO 'onlineshop'@'localhost';
DROP SCHEMA IF EXISTS login;
CREATE SCHEMA IF NOT EXISTS `login` DEFAULT CHARACTER SET utf8;
USE login;
-- place your solutions here



-- end solution

DROP SCHEMA login;

REVOKE ALL PRIVILEGES ON login.* FROM 'onlineshop'@'localhost';
