<?php

class LoginAPI
{
    private $API_KEY;
    private $PASSWORD;
    private $HOST_NAME;

    public function __construct()
    {
        $this->API_KEY = env('SHOPIFY_LOGIN_API_KEY');
        $this->PASSWORD = env('SHOPIFY_LOGIN_PASSWORD');
        $this->HOST_NAME = env('SHOPIFY_LOGIN_HOST_NAME');
    }

    public function API()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET': //consulta
                $this->MetodoGet();
                break;
            case 'POST': //inserta
                $this->MetodoPost();
                break;
            case 'PUT': //actualiza

                break;
            case 'DELETE': //elimina

                break;
            default: //metodo NO soportado
                echo 'METODO NO SOPORTADO';
                break;
        }
    }


    function response($code = 200, $status = "", $message = "")
    {
        http_response_code($code);
        if (!empty($status) && !empty($message)) {
            $response = array("status" => $status, "message" => $message);
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    }



    // $file = fopen("texto_prueba.txt", "w");

    //  fwrite($file, "id_producto ".$objArr["id"]);
    //  fwrite($file, "title ".$objArr["title"]);
    //   fwrite($file, "product_type ".$objArr["product_type"]);
    //   fwrite($file, "img ".$objArr["image"]->src);
    //  fwrite($file, "handle ".$objArr["handle"]);

    //  fclose($file);

    function MetodoGet()
    {
        $obj = json_decode(file_get_contents('php://input'));
        $objArr = (array)$obj;
    }

    function MetodoPost()
    {
        $obj = json_decode(file_get_contents('php://input'));
        $objArr = (array)$obj;

        //    if($_GET['action']=='eliminarUsuario')
        //    {
        //         $usuarioId=$objArr["usuario_id"];
        //         borrarUsuario($usuarioId);

        //         echo json_encode(
        //             array(
        //                 "ok" => false,
        //                 "mensaje" => $usuarioId
        //             ));
        //          return;
        //    }
    }
} //end class
