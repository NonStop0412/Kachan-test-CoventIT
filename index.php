<?php

include "vendor/autoload.php";
// Creating 2000 users with 1-3 phones with a balance from -50 to 150 uah

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

$db = \app\MysqlDatabase::getInst();

$operators = ['50', '67', '63', '68'];

// Creating users
for ($i = 0; $i < 2000; $i++) {
    $queryUser = 'INSERT INTO users (name, birth) VALUES (:name, :birth)';
    $db->execute($queryUser, ['name' => $faker->name(), 'birth' => $faker->dateTimeBetween('-50 years', 'now')->format('Y-m-d')]);
    $userId = $db->getLastInsertId();

    // Adding a phones number from 1 to 3
    for ($j = 0; $j < rand(1, 3); $j++) {
        $phoneNumber = '380' . $operators[rand(0, count($operators) - 1)] . rand(1111111, 9999999);

        $queryUserPhones = 'INSERT INTO users_phones (user_id, phone) VALUES (:user_id, :phone)';
        $db->execute($queryUserPhones, ['user_id' => $userId, 'phone' => $phoneNumber]);
        $phoneId = $db->getLastInsertId();

        // Creating transaction for get balance from -50 to 150 uah
        for ($y = 0; $y < rand(1, 3); $y++) {
            $amount = random_int(-16, 50 - 1) + (random_int(0, PHP_INT_MAX - 1) / PHP_INT_MAX);
            $queryPhonesBalance = 'INSERT INTO phone_transactions (amount, phone_id, currency) VALUES (:amount, :phone_id, :currency)';
            $db->execute($queryPhonesBalance, ['amount' => $amount, 'phone_id' => $phoneId, 'currency' => 'UAH']);
        }
    }
    echo $i . "\n";
}

echo 'Generation done!' . "\n";

// Calling User methods for test
$user = new \app\User($db);
$birth = new DateTime('-22');
$user->create('Test Test', $birth);
$userId = $db->getLastInsertId();
$user->addPhone($userId, '380681234567');
$user->depositPhone('380681234567', 25.00);
var_dump($user->getById($userId));
//$user->delete($userId);

echo 'Done!' . "\n";