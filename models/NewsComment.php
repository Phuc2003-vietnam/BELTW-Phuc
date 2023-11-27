<?php

require_once './config/Database.php';

$connection = Database::getInstance()->getConnection();

class NewsComment
{
    public function get($queryParams, $allowedKeys = [], $select = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $queryParams = array_intersect_key($queryParams, array_flip($allowedKeys));
        }  

        $selectClause = empty($select) ? '*' : implode(', ', $select);
     
        $conditions = [];
        foreach ($queryParams as $key => $value) {
            $conditions[] = "$key = '$value'";
        }
        
        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $query = "SELECT $selectClause FROM news_comments $whereClause ORDER BY news_comments.updated_at DESC";

        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in news_comments::get: " . $e->getMessage();
            
        }
    }

    public function create($data, $allowedKeys = [])
    {
        global $connection;
        if (!empty($allowedKeys)) {
            $data = array_intersect_key($data, array_flip($allowedKeys));
        }

        $columns = implode(', ', array_keys($data));
        $values = "'" . implode("', '", $data) . "'";
        
        $query = "INSERT INTO news_comments ($columns) VALUES ($values)";
        
        try {

            $result = $connection->query($query);
            return $result;

        } catch (PDOException $e) {

            echo "Unknown error in news_comments::create: " . $e->getMessage();
        }
    }
}
