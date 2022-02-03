<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;


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
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Fallo en la validación',
                    'errors' => $validate->errors()
                );
            }else {
                $cliente = new Cliente();
                $cliente->nombre = $params['nombre'];
                $cliente->email = $params['email'];
                $cliente->direccion = $params['direccion'];
                $cliente->cel = $params['cel'];
                if ($request->filled('tel')) {
                    $cliente->tel = $params['tel'];
                }
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
    public function update(Request $request, $id)
    {
        //Recoger los datos por Post

        $json = $request->input('json',null);
        $params = json_decode($json, true);

        //Validarlos

        if (!empty($params)) {
                unset($params['id']);
                unset($params['created_at']);
                $cliente = Cliente::where('id',$id)->update($params);
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'message' => 'Cliente actualizado con éxito'
                ]);
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
