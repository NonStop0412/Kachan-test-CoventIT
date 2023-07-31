-- Баланс по каждому пользователю (сумма денег по всем номерам и операторам каждого пользователя)
SELECT users.*, SUM(phone_transactions.amount) as balance, phone_transactions.currency FROM users
LEFT JOIN users_phones ON users_phones.user_id = users.id
LEFT JOIN phone_transactions ON phone_transactions.phone_id = users_phones.id
GROUP BY users.id, phone_transactions.currency;

-- количество номеров телефонов по операторам (список: код оператора, кол-во номеров этого оператора);
SELECT
    (SELECT COUNT(*) from users_phones WHERE phone LIKE "38063%") as '63',
    (SELECT COUNT(*) from users_phones WHERE phone LIKE "38050%") as '50',
    (SELECT COUNT(*) from users_phones WHERE phone LIKE "38067%") as '67',
    (SELECT COUNT(*) from users_phones WHERE phone LIKE "38068%") as '68';

-- количество телефонов у каждого пользователя (список: имя пользователя, кол-во номеров у пользователя);
SELECT users.name, (SELECT COUNT(*) FROM users_phones WHERE users_phones.user_id = users.id) as numbers_count FROM users;

-- вывести имена 10 пользователей с максимальным балансом на счету (максимальный баланс по одному номеру);
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SELECT users.name, SUM(phone_transactions.amount) as balance, phone_transactions.currency FROM users
LEFT JOIN users_phones ON users_phones.user_id = users.id
LEFT JOIN phone_transactions ON phone_transactions.phone_id = users_phones.id
GROUP BY users_phones.phone, phone_transactions.currency
ORDER BY balance DESC
LIMIT 10;