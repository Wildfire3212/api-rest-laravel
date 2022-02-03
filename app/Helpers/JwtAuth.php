<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\DatabaseManager;
use App\Models\User;

class JwtAuth
{

    public $key = '!!mejor_no_saber_que_hace_esto!--80470897';

    public function signup($email, $password, $getToken = null){
        //Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password,
        ])->first();
        //Comprobar si son correctas(objeto)
        $signup = false;
        if (is_object($user)) {
            $signup=true;
        }
        //Generar el token
        if ($signup) {
            $token = array (
                'sub'   => $user->id,
                'email' => $user->email,
                'name'  => $user->name,
                'iat'   => time(),
                'exp'   => time() + (2*24*60*60)
            );
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

            //Devolver los datos decodificados o el token
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else{
            $data= array(
                'status'    => 'error',
                'message'   => 'Login incorrecto'
            );
        }
        
        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
       $auth = false;

       try {
           $jwt = str_replace('"', '', $jwt);
           $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));
       } catch (\UnexpectedValueException $e) {
           $auth = false;
       } catch(\DomainException $e){
            $auth = false;
       }
       if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
           $auth = true;
       }else{
           $auth=false;
       }

       if ($getIdentity) {
           return $decoded;
       }

       return $auth;
    }
    
}
