<?php

namespace app\ajax;

use app\controllers\purchaseController;


/*=============================================
INICIAR SESIÓN
=============================================*/

require_once "../views/inc/session_start.php";


/*=============================================
CARGAR MODELO
=============================================*/

require_once "../models/mainModel.php";


/*=============================================
CARGAR CONTROLADOR
=============================================*/

require_once "../controllers/purchaseController.php";


/*=============================================
INSTANCIA
=============================================*/

$insCompra = new purchaseController();


/*=============================================
ACCIONES
=============================================*/

if (isset($_POST['modulo_compra'])) {

    $modulo = $_POST['modulo_compra'];


    /*=============================================
    BUSCAR PRODUCTO
    =============================================*/

    if ($modulo == "buscar_codigo") {

        echo $insCompra->buscarCodigoCompraControlador();

        exit;
    }


    /*=============================================
    AGREGAR PRODUCTO
    =============================================*/

    if ($modulo == "agregar_producto") {

        echo $insCompra->agregarProductoCarritoControlador();

        exit;
    }


    /*=============================================
    ACTUALIZAR PRODUCTO
    =============================================*/

    if ($modulo == "actualizar_producto") {

        echo $insCompra->actualizarProductoCarritoControlador();

        exit;
    }

    /*=============================================
ACTUALIZAR PRECIOS
=============================================*/

    if ($modulo == "actualizar_precios") {

        echo $insCompra->actualizarPreciosCompraControlador();

        exit;
    }


    /*=============================================
    ELIMINAR PRODUCTO
    =============================================*/

    if ($modulo == "remover_producto") {

        echo $insCompra->removerProductoCarritoControlador();

        exit;
    }


    /*=============================================
    REGISTRAR COMPRA
    =============================================*/

    if ($modulo == "registrar_compra") {

        echo $insCompra->registrarCompraControlador();

        exit;
    }

    /*=============================================
LIMPIAR COMPRA
=============================================*/

    if ($modulo == "limpiar_compra") {

        echo $insCompra->limpiarCompraCarritoControlador();

        exit;
    }

}


/*=============================================
ERROR
=============================================*/

echo json_encode([
    "tipo" => "simple",
    "titulo" => "Error",
    "texto" => "No se recibió una acción válida.",
    "icono" => "error"
]);



exit;