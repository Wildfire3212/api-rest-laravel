<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Recoger los datos del usuario por post

        $json = $request->input('json',null);
        $params = json_decode($json, true);
        $params = array_map('trim',$params);

        //Validar datos

        if (!empty($params)) {
            $validate = \Validator::make($params, [
                'name'        => 'required|alpha',
                'email'       => 'required|email|unique:users',
                'password'    => 'required'
            ]);
    
            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Fallo en la validacion',
                    'errors' => $validate->errors()
                );
            }else{

                //Cifrar la contraseña
                
                $pwd = hash('sha256',$params['password']);

                $user = new User();
                $user->name = $params['name'];
                $user->email = $params['email'];
                $user->password = $pwd;

                //Guardar el usuario

                $user->save();

                $data = array(
                    'status' => 'error',
                    'code' => '200',
                    'message' => 'El usuario se ha creado con exito'
                );
            }  
        } else {
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Existen campos vacios',
                'errors' => 'Rellene todos los campos'
            );
            return response()->json($data, $data['code']);
        }
        
    return response()->json($data, $data['code']);
    }


    public function login(Request $request)
    {
        
        $jwtAuth = new \JwtAuth();

        //Recibir datos por POST

        $json = $request->input('json',null);
        $params = json_decode($json,true);

        if(!empty($params))
        {
                $validate = \Validator::make($params, [
                'email'       => 'required|email',
                'password'    => 'required'
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'El usuario no se ha podido identificar',
                    'errors' => $validate->errors()
                );
                return response()->json($data, $data['code']);
            }else{

                //Cifrar la contraseña
                
                $pwd = hash('sha256',$params['password']);

                $signup = $jwtAuth->signup($params['email'], $pwd);

                if (!empty($params->gettoken)) {
                    $signup = $jwtAuth->signup($params['email'], $pwd, true);
                }

            }
        }else{
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Existen campos vacios',
                'errors' => 'Rellene todos los campos'
            );
            return response()->json($data, $data['code']);
        }

       return response()->json($signup, 200);
    }

    

    public function update(Request $request)
    {
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //Recoger los datos
        $json = $request->input('json',null);
        $params = json_decode($json,true);


        if ($checkToken && !empty($params)) {

            
            //Sacar el usuario autentificado
            $user = $jwtAuth->checkToken($token, true);

            //Validar datos

            $validate = \Validator::make($params, [
                'email'       => 'required|email|unique:users'.$user->sub,
                'name'        => 'required|alpha'
            ]);

            //Quitar campos que no quiero actualizar

            unset($params['id']);
            unset($params['admin']);
            unset($params['created_at']);
            unset($params['admin']);
            unset($params['remember_token']);

            //actualizar usuario en DB

            $user_update = User::where('id', $user->sub)->update($params);

            //Devolver array con resultado

            $data = array(
                'status' => 'success',
                'code' => '200',
                'user' => $user_update,
                'changes' =>$params
            );

        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'user' => 'Usuario no autentificado'
            );
        }
        return response()->json($data, $data['code']);
    }
}
