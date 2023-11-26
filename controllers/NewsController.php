<?php

require_once './models/News.php';
 
class NewsController
{
    public function getNews($param, $data)
    {
        $queryParams = array();
        if (isset($_SERVER['QUERY_STRING'])) {
            $queryString = $_SERVER['QUERY_STRING'];
            parse_str($queryString, $queryParams);
        } 
        // var_dump($queryParams->length());
        try {
            $News = new News();

            $result = $News->get(
                $queryParams,
                ['news_id'],
                ['news_id' ,'created_at', 'updated_at',  'title', 'content']
            );
            
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            
           
            if($queryParams == []) { 
                http_response_code(200);
                echo json_encode(["message" => "News List fetched Successfully", "data" => $rows]);

            } else {
                if($result->rowCount() == 0) {
                    http_response_code(404);
                    echo json_encode(["message" => "No News found"]);
                } else {
                    http_response_code(200);
                    echo json_encode(["message" => "News fetched Successfully", "data" => $rows]);
                }
            }
        } catch (PDOException $e) {
            echo "Unknown error in NewsController::getNewss: " . $e->getMessage();
            die();
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Create News
    /////////////////////////////////////////////////////////////////////////////////////
    public function addNews($param, $data)
    {
        // Checking body data
         
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Update News
    /////////////////////////////////////////////////////////////////////////////////////
    public function updateNews($param, $data)
    {
        
    }

    /////////////////////////////////////////////////////////////////////////////////////
    // Delete News
    /////////////////////////////////////////////////////////////////////////////////////
    public function deleteNews($param, $data)
    {
        
    }

    
}