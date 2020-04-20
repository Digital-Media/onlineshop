SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `onlineshop`
--
DROP SCHEMA  IF EXISTS examples;
CREATE SCHEMA IF NOT EXISTS `examples` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `examples`;

-- ----------------------------------------------------------
-- Table structure for table `pentest`
--

create table pentest (
    idpentest bigint unsigned not null auto_increment,
    email varchar(100) not null,
    password char(128) not null,
    active char(128) default null,
    role char(5) not null DEFAULT 'user',
    pt_varchar1 varchar(255) null,
    pt_varchar2 varchar(255) null,
    pt_int int null,
    pt_decimal decimal(10,2) null,
    primary key (idpentest)
) engine=InnoDB default charset=utf8 auto_increment=1;

-- ------ 1 : 1 relationship enforced by UNIQUE INDEX ---------------

create table product (
    pid bigint unsigned not null auto_increment,
    pname varchar (255) not null,
    ppreis decimal (10,2) not null,
    did bigint unsigned not null unique,
    primary key (pid),
    key (did)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table description (
    did bigint unsigned not null auto_increment,
    description text not null,
    primary key (did)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table product
    add constraint desc_fk
        foreign key (did)
            references description (did);

-- -------- 1 : 1 recursive relationship --------------

create table person (
    pid bigint unsigned not null auto_increment,
    married_to_id bigint unsigned null unique,
    primary key (pid),
    key (married_to_id)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table person
    add constraint pers_fk
        foreign key (married_to_id)
            references person (pid);

-- ---- 1 : M recursive relationship --------------------------------

create table employee1 (
    pid bigint unsigned not null auto_increment,
    is_superior_of_id bigint unsigned null,
    primary key (pid),
    key (is_superior_of_id)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table employee1
    add constraint emp_fk
        foreign key (is_superior_of_id)
            references employee1 (pid);

-- ----- N : M recursive relationship ----------------------------

create table employee2 (
    pid bigint unsigned not null auto_increment,
    primary key (pid)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table teammembers (
    teamleader bigint unsigned not null,
    teammember bigint unsigned not null,
    key (teamleader),
    key (teammember)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table teammembers
    add constraint emp_fk1
        foreign key (teamleader)
            references employee2 (pid);

alter table teammembers
    add constraint emp_fk2
        foreign key (teammember)
            references employee2 (pid);

alter table teammembers
    add constraint
        primary key (teamleader, teammember);

-- ------- fan trap 0NF ---------------------

create table staff_division_branch (
    idstaff bigint unsigned not null,
    iddivision bigint unsigned not null,
    idbranch bigint unsigned not null,
    primary key (idstaff, iddivision, idbranch)
) engine=InnoDB default charset=utf8 auto_increment=1;

insert into staff_division_branch
(idstaff, iddivision, idbranch)
VALUES
(1,1,1),
(2,1,2),
(3,2,3),
(4,2,3),
(5,3,4);


SELECT *
FROM staff_division_branch;

-- ------- 3NF with fan trap, not preserving dependencies, no loss of data --------------------

create table staff1 (
    idstaff bigint unsigned not null auto_increment,
    division_iddivision bigint unsigned not null,
    primary key (idstaff),
    key (division_iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table division1 (
    iddivision bigint unsigned not null auto_increment,
    primary key (iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table branch1 (
    idbranch bigint unsigned not null auto_increment,
    division_iddivision bigint unsigned not null,
    primary key (idbranch),
    key (division_iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table staff1
    add constraint div_fk1
        foreign key (division_iddivision)
            references division1 (iddivision);

alter table branch1
    add constraint div_fk2
        foreign key (division_iddivision)
            references division1 (iddivision);

insert into division1
(iddivision)
VALUES
(1),
(2),
(3);

insert into staff1
(idstaff, division_iddivision)
VALUES
(1,1),
(2,1),
(3,2),
(4,2),
(5,3);

insert into branch1
(idbranch, division_iddivision)
VALUES
(1,1),
(2,1),
(3,2),
(4,3);

SELECT s.idstaff,
       d.iddivision,
       b.idbranch
FROM   staff1 s, division1 d, branch1 b
WHERE  s.division_iddivision = d.iddivision
  AND    b.division_iddivision = d.iddivision;

-- ------ 3NF without fan trap, preserving dependencies, no loss of data -------------
-- ------ FKs can be null, to demonstrate chasm trap -------------------------------

create table division2 (
    iddivision bigint unsigned not null auto_increment,
    primary key (iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table branch2 (
    idbranch bigint unsigned not null auto_increment,
    division_iddivision bigint unsigned null,
    primary key (idbranch),
    key (division_iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table staff2 (
    idstaff bigint unsigned not null auto_increment,
    branch_idbranch bigint unsigned null,
    primary key (idstaff),
    key (branch_idbranch)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table staff2
    add constraint br_fk1
        foreign key (branch_idbranch)
            references branch2 (idbranch);

alter table branch2
    add constraint div_fk3
        foreign key (division_iddivision)
            references division2 (iddivision);

insert into division2
(iddivision)
VALUES
(1),
(2),
(3);

insert into branch2
(idbranch, division_iddivision)
VALUES
(1,1),
(2,1),
(3,2),
(4,3);

insert into staff2
(idstaff, branch_idbranch)
VALUES
(1,1),
(2,2),
(3,3),
(4,3),
(5,4);

SELECT s.idstaff,
       d.iddivision,
       b.idbranch
FROM   staff2 s,
       division2 d,
       branch2 b
WHERE
        s.branch_idbranch
        = b.idbranch
  AND
        b.division_iddivision
        = d.iddivision;

-- ------ fan trap 3NF with additional FK to shortcut staff and division --------

create table division3 (
    iddivision bigint unsigned not null auto_increment,
    primary key (iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table branch3 (
    idbranch bigint unsigned not null auto_increment,
    division_iddivision bigint unsigned not null,
    primary key (idbranch),
    key (division_iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

create table staff3 (
    idstaff bigint unsigned not null auto_increment,
    branch_idbranch bigint unsigned not null,
    division_iddivision bigint unsigned not null,
    primary key (idstaff),
    key (branch_idbranch),
    key (division_iddivision)
) engine=InnoDB default charset=utf8 auto_increment=1;

alter table staff3
    add constraint br_fk2
        foreign key (branch_idbranch)
            references branch3 (idbranch),
    add constraint div_fk4
        foreign key (division_iddivision)
            references division3 (iddivision);

alter table branch3
    add constraint div_fk5
        foreign key (division_iddivision)
            references division3 (iddivision);

insert into division3
(iddivision)
VALUES
(1),
(2),
(3);

insert into branch3
(idbranch, division_iddivision)
VALUES
(1,1),
(2,1),
(3,2),
(4,3);

insert into staff3
(idstaff, branch_idbranch, division_iddivision)
VALUES
(1,1,1),
(2,2,1),
(3,3,2),
(4,3,2),
(5,4,3);

SELECT s.idstaff,
       d.iddivision,
       b.idbranch
FROM   staff3 s,
       division3 d,
       branch3 b
WHERE
        s.branch_idbranch
        = b.idbranch
  AND
        b.division_iddivision
        = d.iddivision;

-- ------ to avoid chasm trap FKs are set to NOT NULL -------

alter table branch2 modify column division_iddivision bigint unsigned not null;

alter table staff2 modify column branch_idbranch bigint unsigned not null;
