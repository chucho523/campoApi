<?php
    require_once "clases/respuestas.class.php";
    require_once "clases/animales.class.php";

    $_respuestas = new respuestas;
    $_animales = new animales;


    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        if(isset($_GET["page"])){
            $pagina = $_GET["page"];
            $listaAnimales = $_animales->listaAnimales($pagina);
            header("Content-type: application/json");
            echo json_encode($listaAnimales);
            http_response_code(200);

        }else if(isset($_GET['id'])){
            $animalId = $_GET['id'];
            $datosAnimal = $_animales->obtenerAnimal($animalId);
            header("Content-type: application/json");
            echo json_encode($datosAnimal);
            http_response_code(200);
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //recibimos los datos enviados
        $postBody = file_get_contents("php://input");
        //enviamos los datos al manejador
        $datosArray = $_animales->post($postBody);

        //devolvemos una respuesta
        header('Content-type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);

    }else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        //recibimos los datos enviados
        $postBody = file_get_contents("php://input");
        //enviamos datos al manejador
        $datosArray = $_animales->put($postBody);
        //devolvemos una respuesta
        header('Content-type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);


    }else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        //recibimos los datos enviados
        $postBody = file_get_contents("php://input");
        //enviamos datos al manejador
        $datosArray = $_animales->delete($postBody);
        //devolvemos una respuesta
        header('Content-type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray);
    }else{
        header('Content-type: application/json');
        $datosArray = $_respuestas->error_405();
        echo json_encode($datosArray);
    }
?>