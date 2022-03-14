<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

/**
 * @OA\Info(title="API Clientes", version="1")
 * @OA\Server(url="https://localhost:80")
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     securityScheme="Authorization"
 * )
 */



class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        $this->middleware('api.auth');
    }

    /**
    * @OA\Get(
    *     path="/cliente",
    *     summary="Mostrar todos los clientes",
    *     security={ {"Authorization": {}} },
    *     @OA\Response(
    *         response=200,
    *         description="Mostrar todos los clientes."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */
    public function index()
    {
        $clientes = Cliente::all();

        return response()->json([
            'code' => '200',
            'status' => 'success',
            'clientes' => $clientes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
    * @OA\Post(
    *     path="/cliente",
    *     summary="Añadir un nuevo cliente",
    *     security={ {"Authorization": {}} },
    * @OA\RequestBody(
    *     required=true,
    *     @OA\MediaType(
    *       mediaType="application/x-www-form-urlencoded",
    *       @OA\Schema(
    *         type= "object",
    *         @OA\Property(
    *           property="json",
    *           type="object",
    *           @OA\Property(
    *               property="nombre",
    *               type="string"
    *           ),
    *           @OA\Property(
    *               property="email",
    *               type="string",
    *               format="email"
    *           ),
    *           @OA\Property(
    *               property="direccion",
    *               type="string"
    *           ),
    *           @OA\Property(
    *               property="cel",
    *               type="integer"
    *           ),
    *           @OA\Property(
    *               property="tel",
    *               type="integer"
    *           ),
    *         )
    *       )
    *     )
    *   ),
    *     @OA\Response(
    *         response=200,
    *         description="Se añadió el cliente."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */
    public function store(Request $request)
    {
        //Recoger los datos por Post



        $json = $request->input('json',null);
        $params = json_decode($json, true);

        //Validarlos

        if (!empty($params)) {
            $validate = \Validator::make($params, [
                'nombre'        => 'required',
                'email'         => 'required|email|unique:cliente',
                'direccion'     => 'required',
                'cel'           => 'required'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Fallo en la validación',
                    'errors' => $validate->errors()
                ]);
            }else {
                $cliente = new Cliente();
                $cliente->nombre = $params['nombre'];
                $cliente->email = $params['email'];
                $cliente->direccion = $params['direccion'];
                $cliente->cel = $params['cel'];
                if ($request->filled('tel')) {
                    $cliente->tel = $params['tel'];
                }
                $cliente->timestamps = false;
                $cliente->save();
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'message' => 'Cliente creado con éxito'
                ]);
            }
        }else{
            return response()->json([
                'code' => '400',
                'status' => 'error',
                'message' => 'La categoria está vacía',
            ]);
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
    * @OA\get(
    *     path="/cliente/{id}",
    *     summary="Obtener registros de un cliente específico y sus pedidos asociados",
    *     security={ {"Authorization": {}} },
    *     @OA\Parameter(
    *         description="parametro de id en url",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(type="string"),
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Se obtuvo el cliente con éxito."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */
    public function show($id)
    {
        if (!Cliente::find($id)) {
            return response()->json([
                'code' => '404',
                'status' => 'error',
                'message' => 'El cliente no existe',
            ]);
        }else{

            $cliente = Cliente::find($id)->load('pedidos');

            if (is_object($cliente)) {
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'cliente' => $cliente,
                ]);
            }else{
                return response()->json([
                    'code' => '404',
                    'status' => 'error',
                    'message' => 'El cliente no existe',
                ]);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
    * @OA\Put(
    *     path="/cliente/{id}",
    *     summary="Actualizar Cliente existente",
    *     security={ {"Authorization": {}} },
    *     @OA\Parameter(
    *         description="parametro de id en url",
    *         in="path",
    *         name="id",
    *         required=true,
    *         @OA\Schema(type="string"),
    *     ),
    * @OA\RequestBody(
    *     required=true,
    *     @OA\MediaType(
    *       mediaType="application/x-www-form-urlencoded",
    *       @OA\Schema(
    *         type= "object",
    *         @OA\Property(
    *           property="json",
    *           type="object",
    *           @OA\Property(
    *               property="nombre",
    *               type="string"
    *           ),
    *           @OA\Property(
    *               property="email",
    *               type="string",
    *               format="email"
    *           ),
    *           @OA\Property(
    *               property="direccion",
    *               type="string"
    *           ),
    *           @OA\Property(
    *               property="cel",
    *               type="integer"
    *           ),
    *           @OA\Property(
    *               property="tel",
    *               type="integer"
    *           ),
    *         )
    *       )
    *     )
    *   ),
    *     @OA\Response(
    *         response=200,
    *         description="Se actualizó el cliente."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="Ha ocurrido un error."
    *     )
    * )
    */

    public function update(Request $request, $id)
    {
        //Recoger los datos por Post

        $json = $request->input('json',null);
        $params = json_decode($json, true);

        //Validarlos

        if (!empty($params)) {
                $validate = \Validator::make($params, [
                    'email'         => 'email|unique:cliente',
                ]);
                if ($validate->fails()) {
                    return response()->json([
                        'code' => '400',
                        'status' => 'error',
                        'message' => 'El email ya esta siendo utilizado o no es válido',
                    ]);
                }else{
                $cliente=Cliente::where('id',$id)->update($params);
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'message' => 'Cliente actualizado con éxito'
                ]);
            }
        }else{
            return response()->json([
                'code' => '400',
                'status' => 'error',
                'message' => 'La categoria está vacía',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
