<?php

namespace Models;

use Exception;
use Models\Interfaces\ModelFinder;
use PDO;

class Guest implements ModelFinder
{
    const TABLE_NAME = 'guest';
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findOne(int $id)
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id=:id");
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $room = $statement->fetch(PDO::FETCH_OBJ);
        return $room === false ? null : $room;
    }

    public function findByName(string $name)
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE full_name=:name");
        $statement->bindParam(':name', $name);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_OBJ);
    }

    public function findOrCreate(array $params)
    {
        $query     = "SELECT * FROM " . Guest::TABLE_NAME . " WHERE full_name = :name LIMIT 1";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':name', $params['guest_name']);
        $statement->execute();
        $guest = $statement->fetch(PDO::FETCH_OBJ);

        // if already exists in db get its id
        if ($guest) {
            return $guest->id;
        } else {
            // else if guest with this name not found then create him
            try {
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->beginTransaction();

                $query     = "INSERT INTO " . self::TABLE_NAME . " (`full_name`,`email`,`phone_number`,`created_at`) VALUES (:name,:email,:phone,UNIX_TIMESTAMP())";
                $statement = $this->connection->prepare($query);
                $statement->bindParam(':name', $params['guest_name']);
                $statement->bindParam(':email', $params['email']);
                $statement->bindParam(':phone', $params['phone_number']);
                $statement->execute();

                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollBack();
                echo "Failed to create guest: " . $e->getMessage();
                exit;
            }
            return $this->findByName($params['guest_name'])->id;
        }
    }

    public function findAll(): ?array
    {
        return [];
    }
}