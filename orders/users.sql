create table users
(
    id           int unsigned auto_increment
        primary key,
    user_name    varchar(255) charset utf8 not null,
    email        varchar(255)              not null,
    count_orders int unsigned              not null
);

