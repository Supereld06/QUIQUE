<?php

use app\controllers\purchaseController;

$insCompra = new purchaseController();

/*=============================================
OBTENER CAJAS
=============================================*/

$cajas = $insCompra->listarCajasCompraControlador();


/*=============================================
CALCULAR TOTAL DE LA COMPRA
=============================================*/

$total_compra = 0;

if (
    isset($_SESSION['datos_producto_compra'])
    &&
    count($_SESSION['datos_producto_compra']) > 0
) {

    foreach (
        $_SESSION['datos_producto_compra']
        as $producto
    ) {

        $total_compra +=
            (float) $producto['compra_detalle_total'];

    }

}

$total_compra = round($total_compra, 2);

?>

<div class="container is-fluid mb-6">

    <h1 class="title">
        Compras
    </h1>

    <h2 class="subtitle">
        <i class="fas fa-shopping-cart fa-fw"></i>
        &nbsp; Nueva compra
    </h2>

</div>


<div class="container pb-6 pt-6">

    <form id="formRegistrarCompra" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST"
        autocomplete="off">

        <!--=============================================
        MODULO
        =============================================-->

        <input type="hidden" name="modulo_compra" value="registrar_compra">


        <!--=============================================
        CAJA
        =============================================-->

        <div class="columns">

            <div class="column is-6">

                <div class="field">

                    <label class="label">

                        <i class="fas fa-cash-register"></i>
                        Caja

                    </label>

                    <div class="control">

                        <div class="select is-fullwidth">

                            <select name="caja_id" id="caja_id" required>

                                <option value="">
                                    -- Seleccione una caja --
                                </option>

                                <?php

                                if (
                                    isset($cajas)
                                    &&
                                    count($cajas) > 0
                                ) {

                                    foreach ($cajas as $caja) {

                                        echo '

                                        <option
                                            value="' . $caja['caja_id'] . '"
                                        >
                                            Caja ' .
                                            $caja['caja_numero'] .
                                            ' - ' .
                                            $caja['caja_nombre'] .
                                            ' | Bs. ' .
                                            number_format(
                                                (float) $caja['caja_efectivo'],
                                                2
                                            ) .
                                            '</option>

                                        ';

                                    }

                                }

                                ?>

                            </select>

                        </div>

                    </div>

                    <?php

                    if (
                        !isset($cajas)
                        ||
                        count($cajas) <= 0
                    ) {

                        echo '

                        <p class="help is-danger">

                            <i class="fas fa-exclamation-triangle"></i>

                            No existen cajas registradas.

                        </p>

                        ';

                    }

                    ?>

                </div>

            </div>

        </div>


        <!--=============================================
        BUSCAR PRODUCTO
        =============================================-->

        <div class="columns">

            <div class="column is-8">

                <div class="field">

                    <label class="label">

                        <i class="fas fa-search"></i>
                        Buscar producto

                    </label>

                    <div class="field has-addons">

                        <div class="control is-expanded">

                            <input type="text" class="input" id="buscar_codigo"
                                placeholder="Código, nombre, marca o modelo">

                        </div>

                        <div class="control">

                            <button type="button" class="button is-info" id="btnBuscarProducto">

                                <i class="fas fa-search"></i>

                                &nbsp;

                                Buscar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!--=============================================
        RESULTADOS DE BÚSQUEDA
        =============================================-->

        <div id="resultado_busqueda" class="mb-5">
        </div>


        <!--=============================================
        CÓDIGO DE PRODUCTO
        =============================================-->

        <div class="columns">

            <div class="column is-6">

                <div class="field">

                    <label class="label">

                        <i class="fas fa-barcode"></i>
                        Código de producto

                    </label>

                    <div class="field has-addons">

                        <div class="control is-expanded">

                            <input type="text" class="input" id="producto_codigo"
                                placeholder="Escanee o introduzca el código">

                        </div>

                        <div class="control">

                            <button type="button" class="button is-info" id="btnAgregarProducto">

                                <i class="fas fa-plus"></i>

                                &nbsp;

                                Agregar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!--=============================================
        TABLA DE PRODUCTOS
        =============================================-->

        <div class="card">

            <header class="card-header">

                <p class="card-header-title">

                    <i class="fas fa-list"></i>

                    &nbsp;

                    Productos de la compra

                </p>

            </header>

            <div class="card-content">

                <div class="table-container">

                    <table class="table is-striped is-hoverable is-fullwidth">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Producto</th>

                                <th>Cantidad</th>

                                <th>Precio compra</th>

                                <th>Precio venta</th>

                                <th>Total</th>

                                <th>Eliminar</th>

                            </tr>

                        </thead>

                        <tbody id="lista_productos_compra">

                            <?php

                            if (
                                isset($_SESSION['datos_producto_compra'])
                                &&
                                count($_SESSION['datos_producto_compra']) > 0
                            ) {

                                $contador = 1;

                                foreach (
                                    $_SESSION['datos_producto_compra']
                                    as $producto
                                ) {

                                    $codigo = htmlspecialchars(
                                        $producto['producto_codigo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    $descripcion = htmlspecialchars(
                                        $producto['compra_detalle_descripcion'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    $cantidad =
                                        (int) 
                                        $producto['compra_detalle_cantidad'];

                                    $precio_compra =
                                        (float) 
                                        $producto['compra_detalle_precio_compra'];

                                    $precio_venta =
                                        (float) 
                                        $producto['compra_detalle_precio_venta'];

                                    $detalle_total =
                                        (float) 
                                        $producto['compra_detalle_total'];

                                    ?>

                                    <tr>

                                        <td>
                                            <?php echo $contador; ?>
                                        </td>

                                        <td>

                                            <strong>

                                                <?php
                                                echo $descripcion;
                                                ?>

                                            </strong>

                                            <br>

                                            <small>

                                                Código:
                                                <?php echo $codigo; ?>

                                            </small>

                                        </td>

                                        <td>

                                            <input type="number" class="input" min="1" value="<?php echo $cantidad; ?>"
                                                onchange="actualizarCantidadCompra(
                                                    '<?php echo $codigo; ?>',
                                                    this.value
                                                )">

                                        </td>

                                        <td>

                                            <input type="number" class="input" min="0.01" step="0.01"
                                                value="<?php echo number_format($precio_compra, 2, '.', ''); ?>" onchange="actualizarPreciosCompra(
                                                    '<?php echo $codigo; ?>'
                                                )" id="precio_compra_<?php echo $codigo; ?>">

                                        </td>

                                        <td>

                                            <input type="number" class="input" min="0.01" step="0.01"
                                                value="<?php echo number_format($precio_venta, 2, '.', ''); ?>" onchange="actualizarPreciosCompra(
                                                    '<?php echo $codigo; ?>'
                                                )" id="precio_venta_<?php echo $codigo; ?>">

                                        </td>

                                        <td>

                                            <strong>

                                                Bs.
                                                <?php
                                                echo number_format(
                                                    $detalle_total,
                                                    2
                                                );
                                                ?>

                                            </strong>

                                        </td>

                                        <td>

                                            <button type="button" class="button is-danger is-small is-rounded" onclick="eliminarProductoCompra(
                                                    '<?php echo $codigo; ?>'
                                                )">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </td>

                                    </tr>

                                    <?php

                                    $contador++;

                                }

                            } else {

                                ?>

                                <tr>

                                    <td colspan="7" class="has-text-centered">

                                        <i class="fas fa-shopping-cart fa-2x"></i>

                                        <br>
                                        <br>

                                        No hay productos agregados
                                        a la compra.

                                    </td>

                                </tr>

                                <?php

                            }

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <hr>


        <!--=============================================
        TOTAL
        =============================================-->

        <div class="columns">

            <div class="column is-4 is-offset-8">

                <div class="notification is-info">

                    <p class="title is-5">

                        Total compra

                    </p>

                    <p class="title is-2" id="total_compra">

                        Bs.
                        <?php
                        echo number_format(
                            $total_compra,
                            2
                        );
                        ?>

                    </p>

                </div>

            </div>

        </div>


        <!--=============================================
        PAGO
        =============================================-->

        <div class="columns">

            <div class="column is-4">

                <div class="field">

                    <label class="label">

                        <i class="fas fa-money-bill-wave"></i>

                        Monto pagado

                    </label>

                    <div class="control">

                        <input type="number" step="0.01" min="0" class="input" name="compra_pagado" id="compra_pagado"
                            value="<?php echo number_format($total_compra, 2, '.', ''); ?>" required>

                    </div>

                </div>

            </div>


            <div class="column is-4">

                <div class="field">

                    <label class="label">

                        <i class="fas fa-exchange-alt"></i>

                        Cambio

                    </label>

                    <div class="control">

                        <input type="text" class="input" id="compra_cambio" value="0.00" readonly>

                    </div>

                </div>

            </div>

        </div>


        <!--=============================================
        BOTONES
        =============================================-->

        <div class="has-text-centered mt-5">

            <button type="button" class="button is-link is-light is-rounded" id="btnLimpiarCompra">

                <i class="fas fa-broom"></i>

                &nbsp;

                Limpiar

            </button>


            <button type="submit" class="button is-info is-rounded" id="btnRegistrarCompra">
                <i class="far fa-save"></i>
                &nbsp;
                Registrar compra
            </button>

        </div>


    </form>

</div>


<script>

    /*=============================================
    IMPORTANTE:
    TODO EL JAVASCRIPT QUEDA DENTRO DE UNA FUNCIÓN
    PARA NO INTERFERIR CON EL MENÚ PRINCIPAL
    =============================================*/

    (function () {

        "use strict";


        /*=============================================
        URL AJAX
        =============================================*/

        const urlCompraAjax =
            "<?php echo APP_URL; ?>app/ajax/compraAjax.php";


        /*=============================================
        BUSCAR PRODUCTO
        =============================================*/

        const btnBuscar =
            document.getElementById("btnBuscarProducto");


        if (btnBuscar) {

            btnBuscar.addEventListener(
                "click",
                function () {

                    const campo =
                        document.getElementById("buscar_codigo");

                    const resultado =
                        document.getElementById("resultado_busqueda");

                    const buscar =
                        campo.value.trim();


                    if (buscar === "") {

                        Swal.fire({
                            icon: "warning",
                            title: "Campo vacío",
                            text:
                                "Introduzca el código, nombre, marca o modelo."
                        });

                        return;

                    }


                    const datos =
                        new FormData();

                    datos.append(
                        "modulo_compra",
                        "buscar_codigo"
                    );

                    datos.append(
                        "buscar_codigo",
                        buscar
                    );


                    resultado.innerHTML =
                        '<div class="has-text-centered p-5">' +
                        '<i class="fas fa-spinner fa-spin fa-2x"></i>' +
                        '<br><br>Buscando producto...' +
                        '</div>';


                    fetch(
                        urlCompraAjax,
                        {
                            method: "POST",
                            body: datos
                        }
                    )
                        .then(function (respuesta) {

                            return respuesta.text();

                        })
                        .then(function (respuesta) {

                            resultado.innerHTML =
                                respuesta;

                        })
                        .catch(function (error) {

                            console.error(error);

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text:
                                    "No se pudo realizar la búsqueda."
                            });

                        });

                }
            );

        }


        /*=============================================
        AGREGAR PRODUCTO
        =============================================*/

        const btnAgregar =
            document.getElementById(
                "btnAgregarProducto"
            );


        if (btnAgregar) {

            btnAgregar.addEventListener(
                "click",
                function () {

                    const campoCodigo =
                        document.getElementById(
                            "producto_codigo"
                        );

                    const codigo =
                        campoCodigo.value.trim();


                    if (codigo === "") {

                        Swal.fire({
                            icon: "warning",
                            title: "Código vacío",
                            text:
                                "Introduzca o escanee el código del producto."
                        });

                        return;

                    }


                    const datos =
                        new FormData();

                    datos.append(
                        "modulo_compra",
                        "agregar_producto"
                    );

                    datos.append(
                        "producto_codigo",
                        codigo
                    );


                    fetch(
                        urlCompraAjax,
                        {
                            method: "POST",
                            body: datos
                        }
                    )
                        .then(function (respuesta) {

                            return respuesta.json();

                        })
                        .then(function (respuesta) {

                            alertas_ajax(respuesta);

                        })
                        .catch(function (error) {

                            console.error(error);

                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text:
                                    "No se pudo agregar el producto."
                            });

                        });

                }
            );

        }


        /*=============================================
        AGREGAR DESDE RESULTADOS
        =============================================*/

        window.agregar_codigo_compra =
            function (codigo) {

                const campo =
                    document.getElementById(
                        "producto_codigo"
                    );

                const boton =
                    document.getElementById(
                        "btnAgregarProducto"
                    );


                if (campo && boton) {

                    campo.value =
                        codigo;

                    boton.click();

                }

            };


        /*=============================================
        ACTUALIZAR CANTIDAD
        =============================================*/

        window.actualizarCantidadCompra =
            function (
                codigo,
                cantidad
            ) {

                const datos =
                    new FormData();


                datos.append(
                    "modulo_compra",
                    "actualizar_producto"
                );

                datos.append(
                    "producto_codigo",
                    codigo
                );

                datos.append(
                    "producto_cantidad",
                    cantidad
                );


                fetch(
                    urlCompraAjax,
                    {
                        method: "POST",
                        body: datos
                    }
                )
                    .then(function (respuesta) {

                        return respuesta.json();

                    })
                    .then(function (respuesta) {

                        alertas_ajax(respuesta);

                    })
                    .catch(function (error) {

                        console.error(error);

                    });

            };


        /*=============================================
        ACTUALIZAR PRECIOS
        =============================================*/

        window.actualizarPreciosCompra =
            function (codigo) {

                const campoCompra =
                    document.getElementById(
                        "precio_compra_" + codigo
                    );

                const campoVenta =
                    document.getElementById(
                        "precio_venta_" + codigo
                    );


                if (!campoCompra || !campoVenta) {

                    return;

                }


                const precioCompra =
                    campoCompra.value;

                const precioVenta =
                    campoVenta.value;


                const datos =
                    new FormData();


                datos.append(
                    "modulo_compra",
                    "actualizar_precios"
                );

                datos.append(
                    "producto_codigo",
                    codigo
                );

                datos.append(
                    "precio_compra",
                    precioCompra
                );

                datos.append(
                    "precio_venta",
                    precioVenta
                );


                fetch(
                    urlCompraAjax,
                    {
                        method: "POST",
                        body: datos
                    }
                )
                    .then(function (respuesta) {

                        return respuesta.json();

                    })
                    .then(function (respuesta) {

                        alertas_ajax(respuesta);

                    })
                    .catch(function (error) {

                        console.error(error);

                    });

            };


        /*=============================================
        ELIMINAR PRODUCTO
        =============================================*/

        window.eliminarProductoCompra =
            function (codigo) {

                Swal.fire({

                    title:
                        "¿Eliminar producto?",

                    text:
                        "El producto será eliminado de la compra.",

                    icon:
                        "warning",

                    showCancelButton:
                        true,

                    confirmButtonText:
                        "Sí, eliminar",

                    cancelButtonText:
                        "Cancelar"

                }).then(function (result) {

                    if (!result.isConfirmed) {

                        return;

                    }


                    const datos =
                        new FormData();


                    datos.append(
                        "modulo_compra",
                        "remover_producto"
                    );

                    datos.append(
                        "producto_codigo",
                        codigo
                    );


                    fetch(
                        urlCompraAjax,
                        {
                            method: "POST",
                            body: datos
                        }
                    )
                        .then(function (respuesta) {

                            return respuesta.json();

                        })
                        .then(function (respuesta) {

                            alertas_ajax(respuesta);

                        })
                        .catch(function (error) {

                            console.error(error);

                        });

                });

            };


        /*=============================================
        CALCULAR CAMBIO
        =============================================*/

        function calcularCambio() {

            const campoPagado =
                document.getElementById(
                    "compra_pagado"
                );

            const campoCambio =
                document.getElementById(
                    "compra_cambio"
                );


            if (!campoPagado || !campoCambio) {

                return;

            }


            const total =
                <?php echo json_encode($total_compra); ?>;


            const pagado =
                parseFloat(
                    campoPagado.value
                ) || 0;


            let cambio =
                pagado - total;


            if (cambio < 0) {

                cambio = 0;

            }


            campoCambio.value =
                cambio.toFixed(2);

        }


        const campoPagado =
            document.getElementById(
                "compra_pagado"
            );


        if (campoPagado) {

            campoPagado.addEventListener(
                "input",
                calcularCambio
            );

            calcularCambio();

        }


        /*=============================================
        LIMPIAR COMPRA
        =============================================*/

        const btnLimpiar =
            document.getElementById(
                "btnLimpiarCompra"
            );


        if (btnLimpiar) {

            btnLimpiar.addEventListener(
                "click",
                function () {

                    Swal.fire({

                        title:
                            "¿Limpiar compra?",

                        text:
                            "Se eliminarán todos los productos agregados.",

                        icon:
                            "warning",

                        showCancelButton:
                            true,

                        confirmButtonText:
                            "Sí, limpiar",

                        cancelButtonText:
                            "Cancelar"

                    }).then(function (result) {

                        if (!result.isConfirmed) {

                            return;

                        }


                        const datos =
                            new FormData();


                        datos.append(
                            "modulo_compra",
                            "limpiar_compra"
                        );


                        fetch(
                            urlCompraAjax,
                            {
                                method: "POST",
                                body: datos
                            }
                        )
                            .then(function (respuesta) {

                                return respuesta.json();

                            })
                            .then(function (respuesta) {

                                alertas_ajax(respuesta);

                            })
                            .catch(function (error) {

                                console.error(error);

                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text:
                                        "No se pudo limpiar la compra."
                                });

                            });

                    });

                }

            );

        }

        /*=============================================
REGISTRAR COMPRA
=============================================*/

        const formRegistrarCompra =
            document.getElementById("formRegistrarCompra");


        if (formRegistrarCompra) {

            formRegistrarCompra.addEventListener(
                "submit",
                function (e) {

                    e.preventDefault();


                    /*=============================================
                    OBTENER DATOS
                    =============================================*/

                    const caja =
                        document.getElementById("caja_id");

                    const compraPagado =
                        document.getElementById("compra_pagado");


                    /*=============================================
                    VALIDAR CAJA
                    =============================================*/

                    if (!caja || caja.value === "") {

                        Swal.fire({

                            icon: "warning",

                            title: "Caja requerida",

                            text:
                                "Debes seleccionar la caja que utilizarás para registrar la compra."

                        });

                        return;

                    }


                    /*=============================================
                    VALIDAR MONTO
                    =============================================*/

                    if (
                        !compraPagado ||
                        compraPagado.value === "" ||
                        parseFloat(compraPagado.value) <= 0
                    ) {

                        Swal.fire({

                            icon: "warning",

                            title: "Monto requerido",

                            text:
                                "Debes introducir un monto pagado válido."

                        });

                        return;

                    }


                    /*=============================================
                    CONFIRMAR REGISTRO
                    =============================================*/

                    Swal.fire({

                        title:
                            "¿Registrar compra?",

                        text:
                            "Se registrará la compra y se actualizará el stock.",

                        icon:
                            "question",

                        showCancelButton:
                            true,

                        confirmButtonText:
                            "Sí, registrar",

                        cancelButtonText:
                            "Cancelar",

                        reverseButtons:
                            true

                    }).then(function (result) {

                        if (!result.isConfirmed) {

                            return;

                        }


                        /*=============================================
                        DESHABILITAR BOTÓN
                        =============================================*/

                        const boton =
                            document.getElementById(
                                "btnRegistrarCompra"
                            );


                        if (boton) {

                            boton.disabled = true;

                            boton.innerHTML =
                                '<i class="fas fa-spinner fa-spin"></i>' +
                                '&nbsp; Registrando...';

                        }


                        /*=============================================
                        CREAR FORM DATA
                        =============================================*/

                        const datos =
                            new FormData(
                                formRegistrarCompra
                            );


                        /*=============================================
                        ASEGURAR MÓDULO
                        =============================================*/

                        datos.set(
                            "modulo_compra",
                            "registrar_compra"
                        );


                        /*=============================================
                        ENVIAR AJAX
                        =============================================*/

                        fetch(
                            urlCompraAjax,
                            {
                                method: "POST",
                                body: datos
                            }
                        )

                            .then(function (respuesta) {

                                return respuesta.text();

                            })

                            .then(function (respuesta) {

                                console.log(
                                    "Respuesta registrar compra:",
                                    respuesta
                                );


                                /*=============================================
                                CONVERTIR RESPUESTA JSON
                                =============================================*/

                                let respuestaJSON;


                                try {

                                    respuestaJSON =
                                        JSON.parse(respuesta);

                                } catch (error) {

                                    console.error(
                                        "Respuesta no válida:",
                                        respuesta
                                    );


                                    Swal.fire({

                                        icon:
                                            "error",

                                        title:
                                            "Error del servidor",

                                        html:
                                            "El servidor no devolvió una respuesta válida.<br><br>" +
                                            "<small>" +
                                            respuesta +
                                            "</small>"

                                    });


                                    if (boton) {

                                        boton.disabled = false;

                                        boton.innerHTML =
                                            '<i class="far fa-save"></i>' +
                                            '&nbsp; Registrar compra';

                                    }


                                    return;

                                }


                                /*=============================================
                                MOSTRAR RESPUESTA
                                =============================================*/

                                if (
                                    respuestaJSON.tipo === "recargar"
                                ) {

                                    Swal.fire({

                                        icon:
                                            respuestaJSON.icono ||
                                            "success",

                                        title:
                                            respuestaJSON.titulo ||
                                            "Compra registrada",

                                        text:
                                            respuestaJSON.texto ||
                                            "La compra fue registrada correctamente.",

                                        confirmButtonText:
                                            "Aceptar"

                                    }).then(function () {

                                        /*
                                        RECARGAMOS LA PÁGINA.
            
                                        El controlador ya vació:
            
                                        $_SESSION['datos_producto_compra']
            
                                        por lo tanto el carrito aparecerá vacío.
                                        */

                                        window.location.reload();

                                    });


                                    return;

                                }


                                /*=============================================
                                RESPUESTA CON ERROR
                                =============================================*/

                                Swal.fire({

                                    icon:
                                        respuestaJSON.icono ||
                                        "error",

                                    title:
                                        respuestaJSON.titulo ||
                                        "Error",

                                    text:
                                        respuestaJSON.texto ||
                                        "No se pudo registrar la compra."

                                });


                                /*=============================================
                                VOLVER A ACTIVAR BOTÓN
                                =============================================*/

                                if (boton) {

                                    boton.disabled = false;

                                    boton.innerHTML =
                                        '<i class="far fa-save"></i>' +
                                        '&nbsp; Registrar compra';

                                }

                            })

                            .catch(function (error) {

                                console.error(
                                    "Error AJAX:",
                                    error
                                );


                                Swal.fire({

                                    icon:
                                        "error",

                                    title:
                                        "Error de conexión",

                                    text:
                                        "No se pudo comunicar con el servidor."

                                });


                                if (boton) {

                                    boton.disabled = false;

                                    boton.innerHTML =
                                        '<i class="far fa-save"></i>' +
                                        '&nbsp; Registrar compra';

                                }

                            });

                    });

                }

            );

        }

    })();




</script>