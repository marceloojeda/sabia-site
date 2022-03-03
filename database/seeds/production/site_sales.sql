select id, name, email from users where is_active = true and name like '%Larissa%';
select id, ticket_number, seller, buyer, created_at from sales where user_id = 25;


insert into sales (is_ecommerce, payment_method, amount, amount_paid, buyer, buyer_email, buyer_phone, ticket_number, 
payment_status, payment_date, billet_file) 
values (true, 'Pix', 12, 12, 'Thais', 'thais_martinss@hotmail.com', '34993210531', null, 'Pago', '2022-02-14 21:02', null);


insert into sales (is_ecommerce, payment_method, amount, amount_paid, buyer, buyer_email, buyer_phone, ticket_number, payment_status, payment_date, billet_file) values (true, 'Pix', 12, 12, 'Jane Meire Fatureto', 'janefatureto@uol.com.br', '34998040505', null, 'Pago', '2022-02-14 12:59', null);


# Dump da base
mysqldump -u root -p udv_sabia > /var/www/data/25-fev.sql

# Copia do servidor
sudo scp -i ojeda_ec2-sabia.pem ubuntu@52.91.174.190:/home/ubuntu/02-mar.sql /home/marcelo/Documentos/Ojeda

mysql -u root -pMarcelo.2020 -h localhost udv_sabia