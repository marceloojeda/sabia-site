+----+-----------------------------+-------------------+
| id | name                        | phone             |
+----+-----------------------------+-------------------+
| 14 | Vendedor 1 (Demonstracao)   | (34) 9936-3060_   |
| 15 | Vendedor 2 (Demonstracao)   | (34) 9936-3060__   |
| 16 | Vendedor 3 (Demonstracao)   | (34) 9936-3060___ |
| 17 | Vendedor 4 (Demonstracao 2) | (34) 9110-9816_   |
| 18 | Vendedor 5 (Demonstracao 2) | (34) 9110-9816__  |
| 19 | Vendedor 6 (Demonstracao 2) | (34) 9110-9816___ |
+----+-----------------------------+-------------------+

update users set phone = '(34) 9770-5717_' where id = 14;
update users set phone = '(34) 9324-6623_' where id = 15;
update users set phone = '(34) 9154-0484_' where id = 16;

update users set phone = '(34) 9245-7729_' where id = 17;
update users set phone = '(34) 9272-2169_' where id = 18;
update users set phone = '(34) 9151-2375_' where id = 19;