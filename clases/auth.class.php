<?php
    require_once 'conexion/conexion.php';
    require_once 'respuestas.class.php';

    class auth extends conexion{

        public function login($json){ 
            $_respuestas = new respuestas;
            $datos = json_decode($json, true);
            if(!isset($datos['usuario']) || !isset($datos['password'])){
                //error con los campos
                return $_respuestas->error_400();
            }else{
                $usuario = $datos['usuario'];
                $password = $datos['password'];
                $password = parent::encriptar($password);
                $datos = $this->obtenerDatosUsuario($usuario);
                if($datos){
                    //verificar si la contraseña es correcta
                    if($password == $datos[0]['password']){
                        if($datos[0]['Estado'] == "activo"){
                            //crear token
                            $verificar = $this->insertarToken($datos[0]['UsuarioId']);
                            if($verificar){
                                //si se guardo
                                $result = $_respuestas->response;
                                $result["result"] = array(
                                    "token" => $verificar
                                );
                                return $result;
                            }else{
                                //error al guardar
                                return $_respuestas->error_500("Error interno, no hemos podido guardar");
                            }
                        }else{
                            //usuario inactivo
                            return $_respuestas->error_200("El usuario esta inactivo");
                        }
                    }else{
                        //la contraseña no es igual
                        return $_respuestas->error_200("la contraseña no coincide");
                    }
                }else{
                    //no existe el usuario
                    return $_respuestas->error_200("el usuario $usuario no existe");
                }
            }
        }

        private function obtenerDatosUsuario($correo){
            $query = "SELECT UsuarioId,password, Estado FROM usuarios WHERE usuario = '$correo'";
            $datos = parent::obtenerDatos($query);
            if(isset($datos[0]['UsuarioId'])){
                return $datos;
            }else{
                return 0;
            }
        }
        private function insertarToken($usuarioId){
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            $date = date("Y-m-d H:i");
            $estado = "activo";
            $query = "INSERT INTO usuarios_token (UsuarioId, Token, Estado, Fecha) values ('$usuarioId', '$token', '$estado', '$date')";
            $verificar = parent::nonQuery($query);
            if($verificar){
                return $token;
            }else{
                return 0;
            }
        }

    }

?>