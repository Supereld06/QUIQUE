<?php

use app\controllers\purchaseController;

$insCompra = new purchaseController();

?>

<div class="container is-fluid mb-6">

    <h1 class="title">Compras</h1>

    <h2 class="subtitle">
        <i class="fas fa-shopping-cart fa-fw"></i>
        &nbsp; Nueva compra
    </h2>

</div>


<div class="container pb-6 pt-6">

    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/compraAjax.php" method="POST"
        autocomplete="off">

        <input type="hidden" name="modulo_compra" value="registrar_compra">


        <!--=====================================
        BUSCAR PRODUCTO
        ======================================-->

        <div class="columns">

            <div class="column is-8">

                <div class="control">

                    <label class="label">
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

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!--=====================================
        RESULTADOS DE BÚSQUEDA
        ======================================-->

        <div id="resultado_busqueda">

        </div>


        <!--=====================================
        CÓDIGO DE PRODUCTO
        ======================================-->

        <div class="columns">

            <div class="column is-6">

                <div class="control">

                    <label class="label">
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
                                &nbsp; Agregar

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!--=====================================
        TABLA DE PRODUCTOS
        ======================================-->

        <div class="table-container">

            <table class="table is-striped is-hoverable is-fullwidth">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Producto</th>

                        <th>Cantidad</th>

                        <th>Precio compra</th>

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

                            echo '

                            <tr>

                                <td>
                                    ' . $contador . '
                                </td>

                                <td>

                                    <strong>
                                        ' . $producto['compra_detalle_descripcion'] . '
                                    </strong>

                                    <br>

                                    <small>
                                        Código:
                                        ' . $producto['producto_codigo'] . '
                                    </small>

                                </td>

                                <td>

                                    <input
                                        type="number"
                                        class="input"
                                        min="1"
                                        value="' . $producto['compra_detalle_cantidad'] . '"
                                        onchange="actualizarCantidadCompra(
                                            \'' . $producto['producto_codigo'] . '\',
                                            this.value
                                        )"
                                    >

                                </td>

                                <td>

                                    Bs.
                                    ' . number_format(
                                $producto['compra_detalle_precio_compra'],
                                2
                            ) . '

                                </td>

                                <td>

                                    <strong>

                                        Bs.
                                        ' . number_format(
                                $producto['compra_detalle_total'],
                                2
                            ) . '

                                    </strong>

                                </td>

                                <td>

                                    <button
                                        type="button"
                                        class="button is-danger is-small is-rounded"
                                        onclick="eliminarProductoCompra(
                                            \'' . $producto['producto_codigo'] . '\'
                                        )"
                                    >

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </td>

                            </tr>

                            ';

                            $contador++;
                        }
                    } else {

                        echo '

                        <tr>

                            <td
                                colspan="6"
                                class="has-text-centered"
                            >

                                <i class="fas fa-shopping-cart fa-2x"></i>

                                <br><br>

                                No hay productos agregados a la compra.

                            </td>

                        </tr>

                        ';
                    }

                    ?>

                </tbody>

            </table>

        </div>


        <hr>


        <!--=====================================
        TOTAL
        ======================================-->

        <?php

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
                    $producto['compra_detalle_total'];
            }
        }

        ?>


        <div class="columns">

            <div class="column is-4 is-offset-8">

                <div class="notification is-info">

                    <p class="title is-4">

                        Total compra

                    </p>

                    <p class="title is-2">

                        Bs.
                        <?php echo number_format(
                            $total_compra,
                            2
                        ); ?>

                    </p>

                </div>

            </div>

        </div>


        <!--=====================================
        PAGO
        ======================================-->

        <div class="columns">

            <div class="column is-4">

                <div class="control">

                    <label class="label">
                        Monto pagado
                    </label>

                    <input type="number" step="0.01" min="0" class="input" name="compra_pagado" id="compra_pagado"
                        value="0.00" required>

                </div>

            </div>


            <div class="column is-4">

                <div class="control">

                    <label class="label">
                        Cambio
                    </label>

                    <input type="text" class="input" id="compra_cambio" value="0.00" readonly>

                </div>

            </div>

        </div>


        <!--=====================================
        BOTONES
        ======================================-->

        <p class="has-text-centered">

            <button type="button" class="button is-link is-light is-rounded" id="btnLimpiarCompra">

                <i class="fas fa-paint-roller"></i>

                &nbsp;

                Limpiar

            </button>


            <button type="submit" class="button is-info is-rounded">

                <i class="far fa-save"></i>

                &nbsp;

                Registrar compra

            </button>

        </p>


    </form>

</div>


<script>
    /*=============================================
BUSCAR PRODUCTO
=============================================*/

    const btnBuscarProducto =
        document.getElementById("btnBuscarProducto");


    if (btnBuscarProducto) {

        btnBuscarProducto.addEventListener("click", function () {

            let buscar_codigo =
                document.getElementById("buscar_codigo").value.trim();


            if (buscar_codigo === "") {

                Swal.fire({

                    icon: "warning",

                    title: "Campo vacío",

                    text: "Introduzca el nombre, código, marca o modelo."

                });

                return;

            }


            let datos = new FormData();

            datos.append(
                "modulo_compra",
                "buscar_codigo"
            );

            datos.append(
                "buscar_codigo",
                buscar_codigo
            );


            fetch(
                "<?php echo APP_URL; ?>app/ajax/compraAjax.php", {

                method: "POST",

                body: datos

            }
            )

                .then(respuesta => respuesta.text())

                .then(respuesta => {

                    document.getElementById(
                        "resultado_busqueda"
                    ).innerHTML = respuesta;

                });

        });

    }


    /*=============================================
    AGREGAR PRODUCTO
    =============================================*/

    const btnAgregarProducto =
        document.getElementById("btnAgregarProducto");


    if (btnAgregarProducto) {

        btnAgregarProducto.addEventListener("click", function () {

            let codigo =
                document.getElementById(
                    "producto_codigo"
                ).value.trim();


            if (codigo === "") {

                Swal.fire({

                    icon: "warning",

                    title: "Código vacío",

                    text: "Introduzca o escanee el código del producto."

                });

                return;

            }


            let datos = new FormData();

            datos.append(
                "modulo_compra",
                "agregar_producto"
            );

            datos.append(
                "producto_codigo",
                codigo
            );


            fetch(
                "<?php echo APP_URL; ?>app/ajax/compraAjax.php", {

                method: "POST",

                body: datos

            }
            )

                .then(respuesta => respuesta.json())

                .then(respuesta => {

                    alertas_ajax(respuesta);

                });

        });

    }


    /*=============================================
    AGREGAR DESDE RESULTADOS
    =============================================*/

    function agregar_codigo_compra(codigo) {

        document.getElementById(
            "producto_codigo"
        ).value = codigo;


        document.getElementById(
            "btnAgregarProducto"
        ).click();

    }


    /*=============================================
    ACTUALIZAR CANTIDAD
    =============================================*/

    function actualizarCantidadCompra(
        codigo,
        cantidad
    ) {

        let datos = new FormData();

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
            "<?php echo APP_URL; ?>app/ajax/compraAjax.php", {

            method: "POST",

            body: datos

        }
        )

            .then(respuesta => respuesta.json())

            .then(respuesta => {

                alertas_ajax(respuesta);

            });

    }


    /*=============================================
    ELIMINAR PRODUCTO
    =============================================*/

    function eliminarProductoCompra(codigo) {

        Swal.fire({

            title: "¿Eliminar producto?",

            text: "El producto será eliminado de la compra.",

            icon: "warning",

            showCancelButton: true,

            confirmButtonText: "Sí, eliminar",

            cancelButtonText: "Cancelar"

        }).then((result) => {

            if (result.isConfirmed) {

                let datos = new FormData();

                datos.append(
                    "modulo_compra",
                    "remover_producto"
                );

                datos.append(
                    "producto_codigo",
                    codigo
                );


                fetch(
                    "<?php echo APP_URL; ?>app/ajax/compraAjax.php", {

                    method: "POST",

                    body: datos

                }
                )

                    .then(respuesta => respuesta.json())

                    .then(respuesta => {

                        alertas_ajax(respuesta);

                    });

            }

        });

    }


    /*=============================================
    CALCULAR CAMBIO
    =============================================*/

    const compraPagado =
        document.getElementById("compra_pagado");


    if (compraPagado) {

        compraPagado.addEventListener("input", function () {

            let total =
                <?php echo $total_compra; ?>;

            let pagado =
                parseFloat(this.value) || 0;


            let cambio =
                pagado - total;


            if (cambio < 0) {

                cambio = 0;

            }


            document.getElementById(
                "compra_cambio"
            ).value =
                cambio.toFixed(2);

        });

    }



    /*=============================================
LIMPIAR COMPRA
=============================================*/

    const btnLimpiarCompra = document.getElementById("btnLimpiarCompra");

    if (btnLimpiarCompra) {

        btnLimpiarCompra.addEventListener("click", function () {

            Swal.fire({
                title: "¿Limpiar compra?",
                text: "Se eliminarán todos los productos agregados.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, limpiar",
                cancelButtonText: "Cancelar"
            }).then((result) => {

                if (result.isConfirmed) {

                    let datos = new FormData();

                    datos.append(
                        "modulo_compra",
                        "limpiar_compra"
                    );

                    fetch(
                        "<?php echo APP_URL; ?>app/ajax/compraAjax.php",
                        {
                            method: "POST",
                            body: datos
                        }
                    )
                        .then(respuesta => respuesta.json())
                        .then(respuesta => {

                            alertas_ajax(respuesta);

                        });

                }

            });

        });

    }
</script>