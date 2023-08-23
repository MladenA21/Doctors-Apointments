<?php


class Database
{
    private $pdo;
    private $sql;

    public function __construct($db_name, $db_host = "localhost", $db_user = "root", $db_password = "")
    {
        try {
            $this->pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name . ";", $db_user, $db_password);
        } catch (PDOException $e) {
            echo "error " . $e->getMessage();
        }
    }

    public function insert($into, array $data)
    {
        try {
            $questionMarks = [];
            for ($i = 0; $i < count($data); $i++) {
                array_push($questionMarks, "?");
            }

            $sql = "INSERT INTO $into (" . implode(",", array_keys($data)) . ") VALUES (" . implode(",", $questionMarks) . ")";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
        } catch (PDOException $e) {
            echo "error " . $e->getMessage();
        }
    }

    public function select($from, $fields = [], $condition = "")
    {
        $sqlF = count($fields) > 0 ? implode(",", $fields) : "*";
        $this->sql = "SELECT $sqlF FROM $from $condition";

        return $this;
    }

    public function orderBy($field, $orientation = "DESC") {
        $this->sql = $this->sql . " ORDER BY " . $field . " ". $orientation;
        return $this;
    }

    public function get() {
        return $this->pdo->query($this->sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function edit($id, $from,array $data)
    {
        try {
            $sql = "UPDATE $from SET " . implode("=?,", array_keys($data)) . "=? WHERE id=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([...array_values($data), $id]);
        } catch (PDOException $e) {
            echo "error " . $e->getMessage();
        }
    }

    public function destroy($id, $from)
    {
        $sql = "DELETE FROM $from WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}
