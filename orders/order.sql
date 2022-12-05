create table `order`
(
    order_id int unsigned auto_increment
        primary key,
    user_id  int unsigned              not null,
    date     date                      not null,
    address  varchar(255) charset utf8 not null
);

