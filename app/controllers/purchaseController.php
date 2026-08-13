<?php

namespace app\controllers;

use app\models\mainModel;

class purchaseController extends mainModel
{


    /*=============================================
    BUSCAR PRODUCTO
    =============================================*/

    public function buscarCodigoCompraControlador()
    {

        $buscar_codigo = $this->limpiarCadena($_POST['buscar_codigo']);


        if ($buscar_codigo == "") {

            return '
                <article class="message is-warning mt-4 mb-4">

                    <div class="message-header">
                        <p>¡Ocurrió un error inesperado!</p>
                    </div>

                    <div class="message-body has-text-centered">

                        <i class="fas fa-exclamation-triangle fa-2x"></i>

                        <br><br>

                        Debes introducir el nombre,
                        marca o modelo del producto.

                    </div>

                </article>
            ';

        }


        $datos_productos = $this->ejecutarConsulta(
            "SELECT *
            FROM producto
            WHERE producto_nombre LIKE '%$buscar_codigo%'
            OR producto_marca LIKE '%$buscar_codigo%'
            OR producto_modelo LIKE '%$buscar_codigo%'
            ORDER BY producto_nombre ASC"
        );


        if ($datos_productos->rowCount() >= 1) {

            $datos_productos = $datos_productos->fetchAll();

            $tabla = '
                <div class="table-container">

                    <table class="table is-striped is-hoverable is-fullwidth">

                        <tbody>
            ';


            foreach ($datos_productos as $rows) {

                $tabla .= '

                    <tr>

                        <td>
                            <i class="fas fa-box fa-fw"></i>
                            &nbsp;
                            ' . $rows['producto_nombre'] . '
                        </td>

                        <td>
                            ' . $rows['producto_codigo'] . '
                        </td>

                        <td class="has-text-centered">

                            <button
                                type="button"
                                class="button is-link is-rounded is-small"
                                onclick="agregar_codigo_compra(\'' . $rows['producto_codigo'] . '\')">

                                <i class="fas fa-plus-circle"></i>

                            </button>

                        </td>

                    </tr>

                ';

            }


            $tabla .= '

                        </tbody>

                    </table>

                </div>
            ';


            return $tabla;

        }


        return '

            <article class="message is-warning mt-4 mb-4">

                <div class="message-header">

                    <p>¡Ocurrió un error inesperado!</p>

                </div>

                <div class="message-body has-text-centered">

                    <i class="fas fa-exclamation-triangle fa-2x"></i>

                    <br><br>

                    No hemos encontrado ningún producto
                    que coincida con
                    <strong>“' . $buscar_codigo . '”</strong>

                </div>

            </article>

        ';

    }



    /*=============================================
    AGREGAR PRODUCTO AL CARRITO
    =============================================*/

    public function agregarProductoCarritoControlador()
    {

        $producto_codigo = $this->limpiarCadena($_POST['producto_codigo']);


        if ($producto_codigo == "") {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Código requerido",

                "texto" => "Debes introducir el código del producto",

                "icono" => "error"

            ]);

        }


        $datos_producto = $this->ejecutarConsulta(
            "SELECT *
            FROM producto
            WHERE producto_codigo='$producto_codigo'"
        );


        if ($datos_producto->rowCount() <= 0) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Producto no encontrado",

                "texto" => "No existe ningún producto con el código " . $producto_codigo,

                "icono" => "error"

            ]);

        }


        $producto = $datos_producto->fetch();


        if (!isset($_SESSION['datos_producto_compra'])) {

            $_SESSION['datos_producto_compra'] = [];

        }


        /*
        SI EL PRODUCTO YA EXISTE
        */

        if (isset($_SESSION['datos_producto_compra'][$producto_codigo])) {

            $_SESSION['datos_producto_compra'][$producto_codigo]
            ['compra_detalle_cantidad']++;

        } else {

            /*
            PRODUCTO NUEVO
            */

            $_SESSION['datos_producto_compra'][$producto_codigo] = [

                "producto_id" =>
                    $producto['producto_id'],

                "producto_codigo" =>
                    $producto['producto_codigo'],

                "compra_detalle_cantidad" =>
                    1,

                "compra_detalle_precio_compra" =>
                    $producto['producto_precio_compra'],

                "compra_detalle_precio_venta" =>
                    $producto['producto_precio_venta'],

                "compra_detalle_total" =>
                    $producto['producto_precio_compra'],

                "compra_detalle_descripcion" =>
                    $producto['producto_nombre']

            ];

        }


        /*
        RECALCULAR TOTAL DEL PRODUCTO
        */

        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_total'] =

            $_SESSION['datos_producto_compra'][$producto_codigo]
            ['compra_detalle_cantidad']

            *

            $_SESSION['datos_producto_compra'][$producto_codigo]
            ['compra_detalle_precio_compra'];


        return json_encode([

            "tipo" => "recargar",

            "titulo" => "Producto agregado",

            "texto" => "El producto fue agregado correctamente a la compra",

            "icono" => "success"

        ]);

    }



    /*=============================================
    ACTUALIZAR CANTIDAD
    =============================================*/

    public function actualizarProductoCarritoControlador()
    {

        $producto_codigo = $this->limpiarCadena(
            $_POST['producto_codigo']
        );

        $cantidad = $this->limpiarCadena(
            $_POST['producto_cantidad']
        );


        if ($producto_codigo == "" || $cantidad == "") {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Datos incompletos",

                "texto" => "No se recibieron todos los datos",

                "icono" => "error"

            ]);

        }


        $cantidad = (int) $cantidad;


        if ($cantidad <= 0) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Cantidad incorrecta",

                "texto" => "La cantidad debe ser mayor que cero",

                "icono" => "error"

            ]);

        }


        if (
            !isset(
            $_SESSION['datos_producto_compra'][$producto_codigo]
        )
        ) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Producto no encontrado",

                "texto" => "El producto no está agregado a la compra",

                "icono" => "error"

            ]);

        }


        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_cantidad'] = $cantidad;


        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_total'] =

            $cantidad *

            $_SESSION['datos_producto_compra'][$producto_codigo]
            ['compra_detalle_precio_compra'];


        return json_encode([

            "tipo" => "recargar",

            "titulo" => "Cantidad actualizada",

            "texto" => "La cantidad fue actualizada correctamente",

            "icono" => "success"

        ]);

    }



    /*=============================================
    REMOVER PRODUCTO
    =============================================*/

    public function removerProductoCarritoControlador()
    {

        $producto_codigo = $this->limpiarCadena(
            $_POST['producto_codigo']
        );


        if (
            isset(
            $_SESSION['datos_producto_compra'][$producto_codigo]
        )
        ) {

            unset(
                $_SESSION['datos_producto_compra'][$producto_codigo]
            );

        }


        return json_encode([

            "tipo" => "recargar",

            "titulo" => "Producto eliminado",

            "texto" => "El producto fue eliminado de la compra",

            "icono" => "success"

        ]);

    }



    /*=============================================
   REGISTRAR COMPRA
   =============================================*/

    public function registrarCompraControlador()
    {

        /*=============================================
        VERIFICAR CARRITO
        =============================================*/

        if (
            !isset($_SESSION['datos_producto_compra'])
            ||
            count($_SESSION['datos_producto_compra']) <= 0
        ) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Compra vacía",

                "texto" => "Debes agregar al menos un producto.",

                "icono" => "error"

            ]);

        }


        /*=============================================
        CALCULAR TOTAL
        =============================================*/

        $total = 0;

        foreach (
            $_SESSION['datos_producto_compra']
            as $producto
        ) {

            $total += (float) $producto['compra_detalle_total'];

        }


        /*=============================================
        MONTO PAGADO
        =============================================*/

        if (!isset($_POST['compra_pagado'])) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Monto requerido",

                "texto" => "Debes introducir el monto pagado.",

                "icono" => "error"

            ]);

        }


        $compra_pagado = $this->limpiarCadena(
            $_POST['compra_pagado']
        );

        $compra_pagado = (float) $compra_pagado;


        /*=============================================
        VALIDAR MONTO
        =============================================*/

        if ($compra_pagado < $total) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Monto insuficiente",

                "texto" => "El monto pagado no puede ser menor al total de la compra.",

                "icono" => "error"

            ]);

        }


        $compra_cambio = $compra_pagado - $total;


        /*=============================================
        GENERAR CÓDIGO DE COMPRA
        =============================================*/

        $ultima_compra = $this->ejecutarConsulta(
            "SELECT compra_codigo
        FROM compra
        ORDER BY compra_id DESC
        LIMIT 1"
        );


        if ($ultima_compra->rowCount() > 0) {

            $ultimo = $ultima_compra->fetch();

            $numero = (int) preg_replace(
                '/[^0-9]/',
                '',
                $ultimo['compra_codigo']
            );

            $numero++;

        } else {

            $numero = 1;

        }


        $compra_codigo = "COMP-" . str_pad(
            $numero,
            6,
            "0",
            STR_PAD_LEFT
        );


        /*=============================================
        USUARIO
        =============================================*/

        $usuario_id = isset($_SESSION['usuario_id'])
            ? $_SESSION['usuario_id']
            : 1;


        /*=============================================
        GUARDAR CABECERA
        =============================================*/

        $datos_compra = [

            [

                "campo_nombre" => "compra_codigo",

                "campo_marcador" => ":codigo",

                "campo_valor" => $compra_codigo

            ],

            [

                "campo_nombre" => "compra_fecha",

                "campo_marcador" => ":fecha",

                "campo_valor" => date("Y-m-d")

            ],

            [

                "campo_nombre" => "compra_hora",

                "campo_marcador" => ":hora",

                "campo_valor" => date("H:i:s")

            ],

            [

                "campo_nombre" => "compra_total",

                "campo_marcador" => ":total",

                "campo_valor" => $total

            ],

            [

                "campo_nombre" => "compra_pagado",

                "campo_marcador" => ":pagado",

                "campo_valor" => $compra_pagado

            ],

            [

                "campo_nombre" => "compra_cambio",

                "campo_marcador" => ":cambio",

                "campo_valor" => $compra_cambio

            ],

            [

                "campo_nombre" => "usuario_id",

                "campo_marcador" => ":usuario",

                "campo_valor" => $usuario_id

            ]

        ];


        $guardar_compra = $this->guardarDatos(
            "compra",
            $datos_compra
        );


        if ($guardar_compra->rowCount() != 1) {

            return json_encode([

                "tipo" => "simple",

                "titulo" => "Error al registrar",

                "texto" => "No se pudo registrar la cabecera de la compra.",

                "icono" => "error"

            ]);

        }


        /*=============================================
        GUARDAR DETALLES
        =============================================*/

        foreach (
            $_SESSION['datos_producto_compra']
            as $producto
        ) {

            $datos_detalle = [

                [

                    "campo_nombre" =>
                        "compra_detalle_cantidad",

                    "campo_marcador" =>
                        ":cantidad",

                    "campo_valor" =>
                        $producto['compra_detalle_cantidad']

                ],

                [

                    "campo_nombre" =>
                        "compra_detalle_precio_compra",

                    "campo_marcador" =>
                        ":precio_compra",

                    "campo_valor" =>
                        $producto['compra_detalle_precio_compra']

                ],

                [

                    "campo_nombre" =>
                        "compra_detalle_precio_venta",

                    "campo_marcador" =>
                        ":precio_venta",

                    "campo_valor" =>
                        $producto['compra_detalle_precio_venta']

                ],

                [

                    "campo_nombre" =>
                        "compra_detalle_total",

                    "campo_marcador" =>
                        ":total",

                    "campo_valor" =>
                        $producto['compra_detalle_total']

                ],

                [

                    "campo_nombre" =>
                        "compra_detalle_descripcion",

                    "campo_marcador" =>
                        ":descripcion",

                    "campo_valor" =>
                        $producto['compra_detalle_descripcion']

                ],

                [

                    "campo_nombre" =>
                        "compra_codigo",

                    "campo_marcador" =>
                        ":codigo",

                    "campo_valor" =>
                        $compra_codigo

                ],

                [

                    "campo_nombre" =>
                        "producto_id",

                    "campo_marcador" =>
                        ":producto",

                    "campo_valor" =>
                        $producto['producto_id']

                ]

            ];


            $guardar_detalle = $this->guardarDatos(
                "compra_detalle",
                $datos_detalle
            );


            if ($guardar_detalle->rowCount() != 1) {

                return json_encode([

                    "tipo" => "simple",

                    "titulo" => "Error en detalle",

                    "texto" =>
                        "No se pudo guardar el producto "
                        . $producto['producto_codigo'],

                    "icono" => "error"

                ]);

            }


            /*=============================================
            AUMENTAR STOCK
            =============================================*/

            $producto_id = (int) $producto['producto_id'];

            $cantidad = (int) $producto['compra_detalle_cantidad'];


            $actualizar_stock = $this->ejecutarConsulta(

                "UPDATE producto

             SET producto_stock =
                 producto_stock + $cantidad

             WHERE producto_id = $producto_id"

            );


            if ($actualizar_stock->rowCount() != 1) {

                return json_encode([

                    "tipo" => "simple",

                    "titulo" => "Error de stock",

                    "texto" =>
                        "No se pudo actualizar el stock del producto "
                        . $producto['producto_codigo'],

                    "icono" => "error"

                ]);

            }

        }


        /*=============================================
        LIMPIAR SESIÓN
        =============================================*/

        $_SESSION['datos_producto_compra'] = [];

        $_SESSION['compra_total'] = 0;


        /*=============================================
        RESPUESTA
        =============================================*/

        return json_encode([

            "tipo" => "recargar",

            "titulo" => "Compra registrada",

            "texto" =>
                "La compra " .
                $compra_codigo .
                " fue registrada correctamente.",

            "icono" => "success"

        ]);

    }

    /*=============================================
LIMPIAR CARRITO DE COMPRA
=============================================*/

    public function limpiarCompraCarritoControlador()
    {

        $_SESSION['datos_producto_compra'] = [];

        $_SESSION['compra_total'] = 0;

        return json_encode([

            "tipo" => "recargar",

            "titulo" => "Compra limpiada",

            "texto" => "Todos los productos fueron eliminados de la compra.",

            "icono" => "success"

        ]);
    }

}