<?php

namespace app;

use DateTime;

// Class for working with users
class User
{
    private DatabaseInterface $db;

    /**
     * @param DatabaseInterface $db
     */
    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    // Get one User by id
    public function getById(int $id): array
    {
        $sql = "SELECT GROUP_CONCAT(users_phones.phone SEPARATOR ', ') as phones, users.id, users.birth, users.`name` 
                    FROM `users` LEFT JOIN users_phones ON users_phones.user_id = users.id
                    WHERE users.id = :id GROUP BY users_phones.user_id";

        $usersData = $this->db->selectOne($sql, [':id' => $id]);

        if (empty($usersData)) {
            return [];
        }

        $usersData['phones'] = explode(', ', $usersData['phones']);

        return $usersData;
    }

    // Get phone id by phone number for deposit
    private function getPhoneIdByPhoneNumber(string $phone): int|null
    {
        $sql = "SELECT id FROM users_phones WHERE phone = :phone";

        $phoneId = $this->db->selectOne($sql, [':phone' => $phone]);

        return $phoneId['id'] ?? null;
    }

    // Make a deposit for phone
    public function depositPhone(string $phone, float $amount, string $currency = 'UAH'): bool
    {
        $phoneId = $this->getPhoneIdByPhoneNumber($phone);

        // Validation to deposit by a max amount (100) and existing phone in db
        if ($amount <= 0 || $amount > 100 || $phoneId === null) {

            return false;
        }

        $sql = "INSERT INTO  phone_transactions (phone_id, amount, currency) VALUES (:phone_id, :amount, :currency)";

        return $this->db->execute($sql, [':amount' => $amount, ':phone_id' => $phoneId, ':currency' => $currency]);
    }

    // Creating a new User
    public function create(string $name, DateTime $dateOfBirth,): bool
    {
        $sql = "INSERT INTO users (name, birth) VALUES (:name, :birth)";

        return $this->db->execute($sql, [':name' => $name, ':birth' => $dateOfBirth->format('y-m-d')]);
    }

    // Adding a phone for User
    public function addPhone(int $userId, string $phone): bool
    {
        // Validation a phone number by supported operators and country code
        $phoneVerify = preg_grep('/380(50|63|67|68)\d{7}/', explode("\n", $phone));
        if (empty($phoneVerify)) {

            return false;
        }

        $sql = "INSERT INTO users_phones (user_id, phone) VALUES (:user_id, :phone)";

        return $this->db->execute($sql, [':user_id' => $userId, ':phone' => $phone]);
    }

    // Deleting a User and all his data (numbers, transactions)
    public function delete(int $userId): bool
    {
        $sql = "DELETE users.*, users_phones.*, phone_transactions.* 
                FROM users 
                LEFT JOIN users_phones ON users_phones.user_id = users.id
                LEFT JOIN phone_transactions ON phone_transactions.phone_id = users_phones.id 
                WHERE users.id = :user_id";

        return $this->db->execute($sql, [':user_id' => $userId]);
    }
}