DROP SCHEMA  IF EXISTS graph_demos;
CREATE SCHEMA IF NOT EXISTS graph_demos DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE graph_demos;

-- simple recursion example from mariadb documentation

CREATE TABLE IF NOT EXISTS folks (
  id bigint unsigned NOT NULL,
  name varchar(100) NOT NULL,
  father bigint unsigned NULL,
  mother bigint unsigned NULL,
  PRIMARY KEY (id),
  KEY father_fk (father),
  KEY mother_fk (mother)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO folks (id, name, father, mother) VALUES
(100, 'Alex', 20, 30),
(20, 'Dad', 10, null),
(30, 'Mom', null, null),
(10, 'Grandpa Bill', null, null),
(98, 'Sister Amy', 20, 30);

-- demonstrates, that result set is ordered with id asc
select * from folks;

-- demonstrate cte
with recursive anchestors as (
  select * from folks
  where name = "Alex"
  union 
  select f.* 
  from folks as f , anchestors as a
  where f.id = a.father or f.id = a.mother
  )
  select * from anchestors;
  
-- complex graph example

CREATE TABLE vertices (
vertex_id bigint UNSIGNED NOT NULL,
alias varchar (255),
label varchar (255),
name varchar (255),
type varchar (255),
properties json,
PRIMARY KEY (vertex_id)
);

INSERT INTO vertices (vertex_id, alias, label, name, type) VALUES
(1, 'NAmerica', 'Location', 'North America', 'continent'),
(2, 'Europe', 'Location', 'Europe', 'continent'),
(3, 'USA', 'Location', 'United States', 'country'),
(4, 'UK', 'Location', 'United Kingdom', 'country'),
(5, 'England', 'Location', 'England', 'country'),
(6, 'Austria', 'Location', 'Österreich', 'country'),
(7, 'Idaho', 'Location', 'Idaho', 'state'),
(8, 'London', 'Location', 'London', 'city'),
(9, 'UpperAustria', 'Location', 'Oberösterreich', 'Bundesland'),
(10, 'Waldviertel', 'Location', 'Waldviertel', 'Viertel'),
(11, 'Grein', 'Location', 'Grein', 'city'),
(12, 'Andrea', 'Person', 'Andrea', 'person'),
(13, 'Bert', 'Person', 'Bert', 'person'),
(14, 'Christian', 'Person', 'Christian', 'person');

CREATE TABLE edges (
edge_id bigint  UNSIGNED NOT NULL,
tail_vertex integer REFERENCES vertices (vertex_id),
head_vertex integer REFERENCES vertices (vertex_id),
label varchar(255),
properties json,
PRIMARY KEY (edge_id),
KEY (tail_vertex),
KEY (head_vertex)
);

INSERT INTO edges (edge_id, tail_vertex, head_vertex, label) VALUES
(1, 3, 1, 'within'),
(2, 4, 2, 'within'),
(3, 5, 4, 'within'),
(4, 6, 2, 'within'),
(5, 7, 3, 'within'),
(6, 8, 5, 'within'),
(7, 9, 6, 'within'),
(8, 10, 9, 'within'),
(9, 11, 10, 'within'),
(10, 12, 7, 'born_in'),
(11, 12, 8, 'lives_in'),
(12, 13, 11, 'born_in'),
(13, 13, 8, 'lives_in'),
(14, 14, 8, 'born_in'),
(15, 12, 13, 'married'),
(16, 13, 12, 'married');

WITH RECURSIVE
in_usa(vertex_id) AS (
    SELECT vertex_id FROM vertices WHERE name = 'United States'
  UNION
    SELECT edges.tail_vertex
    FROM edges JOIN in_usa
    ON edges.head_vertex = in_usa.vertex_id
    WHERE edges.label = 'within'
),
in_europe(vertex_id) AS (
    SELECT vertex_id FROM vertices WHERE name = 'Europe'
  UNION
    SELECT edges.tail_vertex
    FROM edges JOIN in_europe
    ON edges.head_vertex = in_europe.vertex_id
    WHERE edges.label = 'within'
),
born_in_usa(vertex_id) AS (
    SELECT edges.tail_vertex
    FROM edges JOIN in_usa
    ON edges.head_vertex = in_usa.vertex_id
    WHERE edges.label = 'born_in'
),
lives_in_europe(vertex_id) AS (
    SELECT edges.tail_vertex
    FROM edges
    JOIN in_europe ON edges.head_vertex = in_europe.vertex_id
    WHERE edges.label = 'lives_in'
)
SELECT vertices.name
FROM vertices
JOIN born_in_usa ON vertices.vertex_id = born_in_usa.vertex_id
JOIN lives_in_europe ON vertices.vertex_id = lives_in_europe.vertex_id;
