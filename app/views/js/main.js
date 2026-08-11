
/*=====================================================
    MOSTRAR / OCULTAR MENU PRINCIPAL
=====================================================*/

let btn_menu = document.getElementById('btn-menu');

if (btn_menu) {

  btn_menu.addEventListener("click", function (e) {

    e.preventDefault();

    let navLateral = document.getElementById('navLateral');
    let pageContent = document.getElementById('pageContent');

    if (
      navLateral.classList.contains('navLateral-change') &&
      pageContent.classList.contains('pageContent-change')
    ) {

      navLateral.classList.remove('navLateral-change');
      pageContent.classList.remove('pageContent-change');

    } else {

      navLateral.classList.add('navLateral-change');
      pageContent.classList.add('pageContent-change');

    }

  });

}


/*=====================================================
    MOSTRAR / OCULTAR SUBMENUS
=====================================================*/

let btn_subMenu = document.querySelectorAll(".btn-subMenu");

btn_subMenu.forEach(subMenu => {

  subMenu.addEventListener("click", function (e) {

    e.preventDefault();

    if (this.classList.contains('btn-subMenu-show')) {

      this.classList.remove('btn-subMenu-show');

    } else {

      this.classList.add('btn-subMenu-show');

    }

  });

});


/*=====================================================
    MODALES GENERALES
=====================================================*/

document.addEventListener('DOMContentLoaded', () => {

  function openModal($el) {

    if ($el) {
      $el.classList.add('is-active');
    }

  }


  function closeModal($el) {

    if ($el) {
      $el.classList.remove('is-active');
    }

  }


  function closeAllModals() {

    document
      .querySelectorAll('.modal')
      .forEach(($modal) => {

        closeModal($modal);

      });

  }


  /*
   * Abrir modales mediante:
   *
   * class="js-modal-trigger"
   *
   * data-target="nombreModal"
   */

  document
    .querySelectorAll('.js-modal-trigger')
    .forEach(($trigger) => {

      const modal = $trigger.dataset.target;
      const $target = document.getElementById(modal);

      if ($target) {

        $trigger.addEventListener('click', () => {

          openModal($target);

        });

      }

    });


  /*
   * Cerrar modales
   */

  document
    .querySelectorAll(
      '.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button'
    )
    .forEach(($close) => {

      const $target = $close.closest('.modal');

      if ($target) {

        $close.addEventListener('click', () => {

          closeModal($target);

        });

      }

    });


  /*
   * Tecla ESC
   */

  document.addEventListener('keydown', (event) => {

    if (event.code === 'Escape') {

      closeAllModals();

    }

  });

});


/*=====================================================
    ESCÁNER DE CÓDIGO DE BARRAS
=====================================================*/

document.addEventListener("DOMContentLoaded", function () {

  const btnEscanear =
    document.getElementById("btnEscanear");
  console.log(
    "BOTON ESCANEAR:",
    btnEscanear
  );

  const modalScanner =
    document.getElementById("modalScanner");

  const cerrarScanner =
    document.getElementById("cerrarScanner");

  const productoCodigo =
    document.getElementById("producto_codigo");


  /*
   * Si no estamos en la página de productos,
   * no hacemos nada.
   */

  if (
    !btnEscanear ||
    !modalScanner ||
    !productoCodigo
  ) {

    return;

  }


  let scanner = null;

  let scannerActivo = false;


  /*=================================================
      ABRIR ESCÁNER
  =================================================*/

  btnEscanear.addEventListener("click", function () {

    console.log(
      "Botón de escáner presionado"
    );


    /*
     * Comprobar que la librería existe
     */

    if (typeof Html5Qrcode === "undefined") {

      alert(
        "No se pudo cargar el lector de códigos de barras."
      );

      console.error(
        "ERROR: Html5Qrcode no está disponible."
      );

      return;

    }


    /*
     * Abrir modal
     */

    modalScanner.classList.add("is-active");


    /*
     * Si ya está activo no crear otro
     */

    if (scannerActivo) {

      return;

    }


    /*
     * Crear lector
     */

    scanner =
      new Html5Qrcode("reader");


    /*
     * Iniciar cámara
     */

    scanner.start(

      {
        facingMode: "environment"
      },

      {
        fps: 10,

        qrbox: {
          width: 300,
          height: 150
        }

      },


      /*
       * CÓDIGO DETECTADO
       */

      function (codigo) {

        console.log(
          "Código detectado:",
          codigo
        );


        /*
         * Colocar código en el input
         */

        productoCodigo.value = codigo;


        /*
         * Detener cámara
         */

        scanner
          .stop()
          .then(function () {

            console.log(
              "Cámara detenida"
            );

            scanner.clear();

            scanner = null;

            scannerActivo = false;

            modalScanner.classList.remove(
              "is-active"
            );

          })
          .catch(function (error) {

            console.error(
              "Error al detener cámara:",
              error
            );

          });

      },


      /*
       * Errores de lectura.
       *
       * No mostramos nada porque mientras
       * busca un código se producen muchos.
       */

      function (errorMessage) {

        // No hacer nada.

      }

    )
      .then(function () {

        scannerActivo = true;

        console.log(
          "Cámara iniciada correctamente"
        );

      })
      .catch(function (error) {

        console.error(
          "Error al iniciar la cámara:",
          error
        );

        scannerActivo = false;

        scanner = null;

        modalScanner.classList.remove(
          "is-active"
        );


        alert(
          "No se pudo acceder a la cámara.\n\n" +
          "Verifica que hayas permitido el acceso " +
          "a la cámara en el navegador."
        );

      });

  });


  /*=================================================
      CERRAR ESCÁNER
  =================================================*/

  function cerrarCamara() {

    /*
     * Si existe una cámara activa,
     * detenerla antes de cerrar.
     */

    if (
      scanner &&
      scannerActivo
    ) {

      scanner
        .stop()
        .then(function () {

          console.log(
            "Cámara detenida"
          );

          scanner.clear();

          scanner = null;

          scannerActivo = false;

          modalScanner.classList.remove(
            "is-active"
          );

        })
        .catch(function (error) {

          console.error(
            "Error al detener la cámara:",
            error
          );

          scanner = null;

          scannerActivo = false;

          modalScanner.classList.remove(
            "is-active"
          );

        });

    } else {

      modalScanner.classList.remove(
        "is-active"
      );

    }

  }


  /*
   * Botón X
   */

  if (cerrarScanner) {

    cerrarScanner.addEventListener(
      "click",
      cerrarCamara
    );

  }


  /*
   * Fondo del modal
   */

  const fondoScanner =
    modalScanner.querySelector(
      ".modal-background"
    );

  if (fondoScanner) {

    fondoScanner.addEventListener(
      "click",
      cerrarCamara
    );

  }

});

