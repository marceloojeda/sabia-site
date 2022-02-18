select id, name, email from users where is_active = true and name like '%Larissa%';
select id, ticket_number, seller, buyer, created_at from sales where user_id = 25;


insert into sales (is_ecommerce, payment_method, amount, amount_paid, buyer, buyer_email, buyer_phone, ticket_number, 
payment_status, payment_date, billet_file) 
values (true, 'Pix', 12, 12, 'Thais', 'thais_martinss@hotmail.com', '34993210531', null, 'Pago', '2022-02-14 21:02', null);


insert into sales (is_ecommerce, payment_method, amount, amount_paid, buyer, buyer_email, buyer_phone, ticket_number, payment_status, payment_date, billet_file) values (true, 'Pix', 12, 12, 'Jane Meire Fatureto', 'janefatureto@uol.com.br', '34998040505', null, 'Pago', '2022-02-14 12:59', null);



mysqldump -u root –p udv_sabia > /var/www/data/bkp_1702.sql

mysqldump -u root -p udv_sabia > /var/www/data/17-fev.sql

mysql -u root -pMarcelo.2020 -h localhost udv_sabia