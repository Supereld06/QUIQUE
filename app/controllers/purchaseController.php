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
        VALIDAR CARRITO
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
        VALIDAR CAJA
        =============================================*/

        $caja_id = $this->limpiarCadena(
            $_POST['caja_id'] ?? ''
        );


        if ($caja_id == "") {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Caja requerida",
                "texto" => "Debes seleccionar la caja que utilizarás para la compra.",
                "icono" => "error"
            ]);

        }


        /*=============================================
        OBTENER CAJA
        =============================================*/

        $datos_caja = $this->ejecutarConsulta(
            "SELECT *
         FROM caja
         WHERE caja_id = '$caja_id'
         LIMIT 1"
        );


        if ($datos_caja->rowCount() <= 0) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Caja no encontrada",
                "texto" => "La caja seleccionada no existe.",
                "icono" => "error"
            ]);

        }


        $caja = $datos_caja->fetch();


        $caja_efectivo = (float) $caja['caja_efectivo'];


        /*=============================================
        CALCULAR TOTAL
        =============================================*/

        $total = 0;


        foreach (
            $_SESSION['datos_producto_compra']
            as $producto
        ) {

            $total +=
                (float) $producto['compra_detalle_total'];

        }


        $total = round($total, 2);


        /*=============================================
        MONTO PAGADO
        =============================================*/

        $compra_pagado = $this->limpiarCadena(
            $_POST['compra_pagado'] ?? ''
        );


        if ($compra_pagado == "") {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Monto requerido",
                "texto" => "Debes introducir el monto pagado.",
                "icono" => "error"
            ]);

        }


        $compra_pagado = (float) $compra_pagado;


        if ($compra_pagado <= 0) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Monto incorrecto",
                "texto" => "El monto pagado debe ser mayor que cero.",
                "icono" => "error"
            ]);

        }


        /*=============================================
        VALIDAR PAGO
        =============================================*/

        if ($compra_pagado < $total) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Monto insuficiente",
                "texto" =>
                    "El monto pagado (Bs. " .
                    number_format($compra_pagado, 2) .
                    ") no puede ser menor al total de la compra (Bs. " .
                    number_format($total, 2) .
                    ").",
                "icono" => "error"
            ]);

        }


        /*=============================================
        VALIDAR SALDO DE CAJA
        =============================================*/

        if ($caja_efectivo < $total) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Saldo insuficiente",
                "texto" =>
                    "La caja seleccionada tiene Bs. " .
                    number_format($caja_efectivo, 2) .
                    " y necesitas Bs. " .
                    number_format($compra_pagado, 2) .
                    ".",
                "icono" => "error"
            ]);

        }


        /*=============================================
        CALCULAR CAMBIO
        =============================================*/

        $compra_cambio =
            round($compra_pagado - $total, 2);


        /*=============================================
        GENERAR CÓDIGO
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


        $compra_codigo = "COMP-" .
            str_pad(
                $numero,
                6,
                "0",
                STR_PAD_LEFT
            );


        /*=============================================
        USUARIO
        =============================================*/

        $usuario_id =
            isset($_SESSION['usuario_id'])
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
                "texto" => "No se pudo registrar la compra.",
                "icono" => "error"
            ]);

        }


        /*=============================================
        GUARDAR DETALLES Y ACTUALIZAR PRODUCTOS
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
                        $producto[
                            'compra_detalle_cantidad'
                        ]
                ],

                [
                    "campo_nombre" =>
                        "compra_detalle_precio_compra",

                    "campo_marcador" =>
                        ":precio_compra",

                    "campo_valor" =>
                        $producto[
                            'compra_detalle_precio_compra'
                        ]
                ],

                [
                    "campo_nombre" =>
                        "compra_detalle_precio_venta",

                    "campo_marcador" =>
                        ":precio_venta",

                    "campo_valor" =>
                        $producto[
                            'compra_detalle_precio_venta'
                        ]
                ],

                [
                    "campo_nombre" =>
                        "compra_detalle_total",

                    "campo_marcador" =>
                        ":total",

                    "campo_valor" =>
                        $producto[
                            'compra_detalle_total'
                        ]
                ],

                [
                    "campo_nombre" =>
                        "compra_detalle_descripcion",

                    "campo_marcador" =>
                        ":descripcion",

                    "campo_valor" =>
                        $producto[
                            'compra_detalle_descripcion'
                        ]
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
                    "titulo" => "Error en el detalle",
                    "texto" =>
                        "No se pudo guardar uno de los productos.",
                    "icono" => "error"
                ]);

            }


            /*=============================================
            ACTUALIZAR PRODUCTO
            =============================================*/

            $producto_id =
                (int) $producto['producto_id'];

            $cantidad =
                (int) $producto['compra_detalle_cantidad'];

            $precio_compra =
                (float) $producto[
                    'compra_detalle_precio_compra'
                ];

            $precio_venta =
                (float) $producto[
                    'compra_detalle_precio_venta'
                ];


            $actualizar_producto =
                $this->ejecutarConsulta(

                    "UPDATE producto
                 SET
                    producto_stock_total =
                        producto_stock_total + $cantidad,

                    producto_precio_compra =
                        $precio_compra,

                    producto_precio_venta =
                        $precio_venta

                 WHERE producto_id =
                    $producto_id"

                );


        }


        /*=============================================
        DESCONTAR DINERO DE LA CAJA
        =============================================*/

        $nuevo_saldo = round(
            $caja_efectivo - $total,
            2
        );


        $actualizar_caja =
            $this->ejecutarConsulta(

                "UPDATE caja
             SET caja_efectivo = $nuevo_saldo
             WHERE caja_id = '$caja_id'"

            );


        if ($actualizar_caja->rowCount() <= 0) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Error de caja",
                "texto" =>
                    "La compra fue registrada, pero no se pudo actualizar el saldo de la caja.",
                "icono" => "error"
            ]);

        }


        /*=============================================
        LIMPIAR CARRITO
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
                "La compra " . $compra_codigo .
                " fue registrada correctamente. " .
                "Se descontaron Bs. " .
                number_format(
                    $compra_pagado,
                    2
                ) .
                " de la caja.",

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


    /*=============================================
ACTUALIZAR PRECIOS DEL PRODUCTO EN EL CARRITO
=============================================*/

    public function actualizarPreciosCompraControlador()
    {

        $producto_codigo = $this->limpiarCadena(
            $_POST['producto_codigo'] ?? ''
        );

        $precio_compra = $this->limpiarCadena(
            $_POST['precio_compra'] ?? ''
        );

        $precio_venta = $this->limpiarCadena(
            $_POST['precio_venta'] ?? ''
        );


        if (
            $producto_codigo == "" ||
            $precio_compra == "" ||
            $precio_venta == ""
        ) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Datos incompletos",
                "texto" => "Debes introducir el precio de compra y el precio de venta.",
                "icono" => "error"
            ]);

        }


        $precio_compra = (float) $precio_compra;
        $precio_venta = (float) $precio_venta;


        if ($precio_compra <= 0) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Precio incorrecto",
                "texto" => "El precio de compra debe ser mayor que cero.",
                "icono" => "error"
            ]);

        }


        if ($precio_venta <= 0) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Precio incorrecto",
                "texto" => "El precio de venta debe ser mayor que cero.",
                "icono" => "error"
            ]);

        }


        if (!isset($_SESSION['datos_producto_compra'][$producto_codigo])) {

            return json_encode([
                "tipo" => "simple",
                "titulo" => "Producto no encontrado",
                "texto" => "El producto no está agregado a la compra.",
                "icono" => "error"
            ]);

        }


        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_precio_compra'] = $precio_compra;


        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_precio_venta'] = $precio_venta;


        /*=============================================
        RECALCULAR TOTAL
        =============================================*/

        $cantidad =
            $_SESSION['datos_producto_compra'][$producto_codigo]
            ['compra_detalle_cantidad'];


        $_SESSION['datos_producto_compra'][$producto_codigo]
        ['compra_detalle_total'] =
            $cantidad * $precio_compra;


        return json_encode([
            "tipo" => "recargar",
            "titulo" => "Precios actualizados",
            "texto" => "Los precios fueron actualizados correctamente.",
            "icono" => "success"
        ]);

    }

    /*=============================================
LISTAR CAJAS
=============================================*/

    public function listarCajasCompraControlador()
    {
        $consulta = $this->ejecutarConsulta("
        SELECT 
            caja_id,
            caja_numero,
            caja_nombre,
            caja_efectivo
        FROM caja
        ORDER BY caja_numero ASC
    ");

        return $consulta->fetchAll();
    }
}