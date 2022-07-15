<?php

namespace Models;

use PDO;
use Models\Interfaces\ModelFinder;

class Room implements ModelFinder
{
    const TABLE_NAME = 'room';

    public int $id;
    public string $number;
    public int $created_at;

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


    public function findAll(): ?array
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME);
        $statement->execute();
        $comment = $statement->fetchAll(PDO::FETCH_OBJ);
        return $comment === false ? null : $comment;
    }

    public function findByNumber(string $number = "")
    {
        $statement = $this->connection->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE number=:number");
        $statement->bindParam(':number', $number);
        $statement->execute();
        $room = $statement->fetch(PDO::FETCH_OBJ);
        return $room === false ? null : $room;
    }

    public function is_free(int $id): bool
    {
        $statement = $this->connection->prepare("SELECT id FROM " . Reservation::TABLE_NAME . " WHERE room_id = :id AND UNIX_TIMESTAMP() between arriving_date AND leaving_date  LIMIT 1");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount() === 0;
    }

}