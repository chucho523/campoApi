<?php
    require_once "conexion/conexion.php";
    require_once "respuestas.class.php";

    class animales extends conexion{
        private $table = "animales";
        private $animalId = "";
        private $caravana = "";
        private $nombre = "";
        private $ultimo_parto ="0000-00-00";
        private $ultimo_celo = "0000-00-00";
        private $token = "";
        //5eb6d936916bf8cec8ee7a05ffcbb14d

        public function listaAnimales($pagina = 1){
            $inicio = 0;
            $cantidad = 100;
            if($pagina > 1){
                $inicio = ($cantidad * ($pagina - 1)) +1;
                $cantidad = $cantidad * $pagina;
            }

            $query = "SELECT id, caravana, nombre, ultimo_parto, ultimo_celo FROM ". $this->table . " limit $inicio,$cantidad";
            $datos = parent::obtenerDatos($query);
            return ($datos);
        }

        public function obtenerAnimal($id){
            $query = "SELECT * FROM ". $this->table ." WHERE id = '$id'";
            return parent::obtenerDatos($query);
        }

        public function post($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json, true);

            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){
                    if(!isset($datos['caravana']) || !isset($datos['nombre']) || !isset($datos['ultimo_parto']) || !isset($datos['ultimo_celo'])){
                        return $_respuestas->error_400();
                    }else{
                        $this->caravana = $datos['caravana'];
                        $this->nombre = $datos['nombre'];
                        $this->ultimo_parto = $datos['ultimo_parto'];
                        $this->ultimo_celo = $datos['ultimo_celo'];
                        $resp = $this->insertarAnimal();
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta['result'] = array(
                                "id" => $resp
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }else{
                    return $_respuestas->error_401("El token que envio es invalido o a caducado");
                }
            }

          

        }

        private function insertarAnimal(){
            $query = "INSERT INTO ". $this->table . " (caravana, nombre, ultimo_parto, ultimo_celo) 
            VALUES 
            ('" . $this->caravana . "','" . $this->nombre . "','" . $this->ultimo_parto . "','" . $this->ultimo_celo ."')";
            $resp = parent::nonQueryId($query);
            if($resp){
                return $resp;
            }else{
                return 0;
            }
        }


        public function put($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json, true);


            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){
                    if(!isset($datos['id'])){
                        return $_respuestas->error_400();
                    }else{
                        $this->animalId = $datos['id'];
                        if(isset($datos['caravana'])){$this->caravana = $datos['caravana'];}
                        if(isset($datos['nombre'])){ $this->nombre = $datos['nombre'];}
                        if(isset($datos['ultimo_parto'])){ $this->ultimo_parto = $datos['ultimo_parto'];}
                        if(isset($datos['ultimo_celo'])){$this->ultimo_celo = $datos['ultimo_celo'];}
                        $resp = $this->modificarAnimal();
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta['result'] = array(
                                "id" => $this->animalId
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }else{
                    return $_respuestas->error_401("El token que envio es invalido o a caducado");
                }
            }


            

        }

        private function modificarAnimal(){
            $query = "UPDATE ". $this->table . " SET caravana= '" .$this->caravana ."',nombre= '" .$this->nombre. "', ultimo_parto= '" .$this->ultimo_parto. "', ultimo_celo= '" .$this->ultimo_celo. "' WHERE id = '".$this->animalId."'";
            $resp = parent::nonQuery($query);
            if($resp >= 1){
                return $resp;
            }else{
                return 0;
            }
        }

        public function delete($json){
            $_respuestas = new respuestas;
            $datos = json_decode($json, true);

            if(!isset($datos['token'])){
                return $_respuestas->error_401();
            }else{
                $this->token = $datos['token'];
                $arrayToken = $this->buscarToken();
                if($arrayToken){
                    if(!isset($datos['id'])){
                        return $_respuestas->error_400();
                    }else{
                        $this->animalId = $datos['id'];
                        $resp = $this->eliminarAnimal();
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta['result'] = array(
                                "id" => $this->animalId
                            );
                            return $respuesta;
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }else{
                    return $_respuestas->error_401("El token que envio es invalido o a caducado");
                }
            }

            
        }

        private function eliminarAnimal(){
            $query = "DELETE FROM ".$this->table. " WHERE id= '" .$this->animalId. "'";
            $resp = parent::nonQuery($query);
            if($resp >=1){
                return $resp;
            }else{
                return 0;
            }
        }

        private function buscarToken(){
            $query = "SELECT TokenId, UsuarioId, Estado FROM usuarios_token WHERE Token= '".$this->token. "' AND Estado= 'activo'";
            $resp = parent::obtenerDatos($query);
            if($resp){
                return $resp;
            }else{
                return 0;
            }
        }

        private function actualizarToken($tokenid){
            $date = date("Y-m-d H:i");
            $query = "UPDATE usuarios_token SET fecha= '$date' WHERE TokenId= '$tokenid'";
            $resp = parent::nonQuery($query);
            if($resp >= 1){
                return $resp;
            }else{
                return 0;
            }
            
        }
    }

?>