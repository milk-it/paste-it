create table posts (
    id bigint auto_increment,
    parent_id bigint default 0,
    name varchar(30),
    expire int,
    created_on datetime,
    code text,
    highlight varchar(12),
    PRIMARY KEY(id)
);
