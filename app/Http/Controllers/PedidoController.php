<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
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
        $pedidos = Pedido::all()->load('cliente');

        return response()->json([
            'code'   => 200,
            'status' => 'success',
            'pedidos' => $pedidos,
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
                'id_remitente_fk'        => 'required|numeric|exists:cliente,id',
                'kg'        => 'required|numeric|',
                'nombre_destinatario'         => 'required',
                'direccion_destinatario'     => 'required'
            ]);

            if($validate->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Fallo en la validación',
                    'errors' => $validate->errors()
                );
            }else {
                $pedido = new Pedido();
                $pedido->id_remitente_fk = $params['id_remitente_fk'];
                $pedido->nombre_destinatario = $params['nombre_destinatario'];
                $pedido->direccion_destinatario = $params['kg'];
                $pedido->kg = $params['kg'];
                if ($request->filled('comentarios')) {
                    $pedido->comentarios = $params['comentarios'];
                }
                $pedido->timestamps = false;
                $pedido->save();
                return response()->json([
                    'code' => '200',
                    'status' => 'success',
                    'message' => 'Pedido creado con éxito'
                ]);
            }
        }else{
            return response()->json([
                'code' => '400',
                'status' => 'error',
                'message' => 'El pedido está vacío',
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
    public function show($id)
    {
        $pedido = Pedido::find($id)->load('cliente');

        if (is_object($pedido)) {
            return response()->json([
                'code' => '200',
                'status' => 'success',
                'pedido' => $pedido,
            ]);
        }else{
            return response()->json([
                'code' => '404',
                'status' => 'error',
                'message' => 'La categoria no existe',
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
        //
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
