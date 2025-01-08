<?php
session_start();

include 'includes/header.php';
include 'config/config_bd.php';

$conn = obtenerConexion();

$sql = "SELECT ROUND((COUNT(numero) / 10000) * 100, 2) AS porcentaje FROM numeros_vendidos;";
$result = $conn->query($sql); {
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $porcentaje = $row["porcentaje"];
    $porcentajeReal = number_format($porcentaje, 2) . '%'; // Formatear con 2 decimales
  } else {
    // $porcentaje = 78.49;
    $porcentajeReal = number_format($porcentaje, 2) . '%';
  }
}

$conn->close();
?>
<span class="ir-arriba"></span>
<!-- banner -->



<!-- banner principal -->
<section class="banner_main mb-5 contenido">
  <div id="banner1" class="animate__animated animate__zoomInDown">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="container">
          <h2 class="pb-1 text-center text-bg titulo-pequeno">
            <span class="text-decoration yellow">Arranca el año con toda</span>
            <br>
            <span class="yellow"> <span class="color-mt"> Te traemos una hermosa <span class="color-premio">Pulsar NS-200</span> ✨</span> </span>

          </h2>
          <div class="row direction">

            <div class="col-md-6 img-fluid moto position-relative">
              <div class="contenedor__img-sorteo">
                <div class="text-center fecha-juego">
                  <span class="animate__animated animate__bounce badge rounded-pill bg-success">¡Juega este 31 de enero 🗓️
                    por la de Medellín! </span>
                </div>
                <div class="moto-rifada">
                  <swiper-container class="mySwiper" pagination="true" pagination-clickable="true" navigation="true" space-between="30" centered-slides="true" autoplay-delay="3500" autoplay-disable-on-interaction="false">
                    <swiper-slide><img src="images/moto-enero.jpg" alt="rifa-moto"></swiper-slide>

                  </swiper-container>
                </div>

              </div>
              <!--barra porcentaje-->
              <div class="mt-5">
                <p class="mb-1 text-center fs-6 number-vendidos">Números vendidos:</p>
                <div class="progress" style="height: 26px; position: relative;width:488px">
                  <div id="progress-bar" class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $porcentaje; ?>%;">
                  </div>
                  <span class="progress-bar-text texto-barra"><?php echo $porcentajeReal; ?></span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="text-bg">
                <h1 class="pb-1 text-center titulo-grande">
                  <span class="text-decoration yellow">Arranca el año con toda</span>
                  <br>
                  <span class="yellow"> <span class="color-mt"> Te traemos una hermosa <span class="color-premio">Pulsar NS-200</span> ✨</span> </span>
                </h1>

                <!--personas comprando en este momento-->
                <style>
                  #counter {
                    margin-top: 5px;
                    font-size: medium;
                    font-weight: bold;
                    color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    text-align: center;

                  }
                </style>

                <div class="combo-mas-vendido">
                  <div class="card1" data-aos="zoom-in">
                    <div class="cintilla-promocion"><img src="images/mas-vendido.png" alt="el mas vendido"></div>
                    <div class="promocion">
                      <h2>x5</h2>
                    </div>
                    <div class="precio">
                      <h2>$20.000</h2>
                      <!--<span class="valor-promo">$90.000</span>-->
                    </div>
                    <div class="cta-buy">
                      <button onclick="modal_x5()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<section>

  <!-- contenedor de numeros premiados -->
  <section class="mt-2 mb-5">
    <div data-aos="zoom-in" style="margin:15px 0px">
      <div class="text-bg text-center">
        <h3 class="h2">
          <span class="yellow my-5 mx-1">🎉 ¡10 Números Ganadores y Grandes Premios!</span>
        </h3>
      </div>

      <div class="text-center my-2 mb-3 inmediato">
        <div class="badge badge-success text-wrap" style="font-size: 15px; padding: 0.7rem;">
          Si te sale alguno de estos números, el pago será <strong>INMEDIATO.</strong>
        </div>
      </div>

      <div class="container numbers-premiados">
        <div class="row">
          <!-- Tarjeta para números de 200 mil -->
          <div class="col-12 col-md-4 my-1 mx-0">
            <div class="card text-center border border-primary">
              <div class="card-body">
                <h3 class="card-title premio-200">$200.000 🤑</h3>
                <div class="d-flex flex-wrap justify-content-center">
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">1234</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">1515</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">1905</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">0108</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">1122</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">9999</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">7007</span>
                  <span class="bg-primary m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">6666</span>
                </div>
                <p class="espaciado-card2">&nbsp;</p>

              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 my-1 mx-0">
            <div class="card text-center border border-danger">
              <div class="card-body">
                <h3 class="card-title premio-500">$500.000 🤑</h3>
                <div class="d-flex flex-wrap justify-content-center">
                  <span class="bg-danger m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">4268</span>
                  <span class="bg-danger m-1" style="color: #000; padding: 5px; border-radius: 8px; font-weight: bold; border: 2px solid #000; border-style: dotted;">8015</span>
                </div>
                <!--<p class="espaciado-card2">&#160;</p>-->
                <!--<p class="espaciado-card2">&nbsp;</p>-->
              </div>
            </div>
          </div>

          <!-- Tarjeta para numeros de 500 -->
          <div class="col-12 col-md-4 my-1">
            <div class="card text-center border">
              <div class="card-body mono">
                <h3 class="card-title premio-10000">$1.000.000 🤑</h3>
                <p class="card-title premio-10000">Para quien más entradas tenga</p>
                <!--<p class="espaciado-card2">&#160;</p>-->
                <!--  <p class="espaciado-card2">&#160;</p>-->
              </div>
            </div>
          </div>





          <!--<div class="text-center my-2 mb-3 inmediato">-->
          <!--  <div class="badge badge-success text-wrap" style="font-size: 15px; padding: 0.7rem;">-->
          <!--    Si te sale alguno de estos números, el pago será <strong>INMEDIATO.</strong>-->
          <!--  </div>-->
          <!--</div>-->
          <!-- Ya cayó-->
          <!-- <div class="col-12 col-md-12 my-1">-->
          <!--  <div class="text-bg text-center">-->
          <!--    <h3 class="h2">-->
          <!--      <span class="yellow my-5 mx-2">📢 ¡Aviso importante vuelve y juega! 📢</span>-->
          <!--    </h3>-->
          <!--  </div>-->
          <!--  <div class="card text-center border border-success mt-3">-->
          <!--    <div class="card-body">-->
          <!--      <div class="d-flex flex-wrap justify-content-center">-->
          <!--        El pasado 22 de noviembre se realizó el sorteo, pero no se vendió el número ganador (0852) y tampoco el número invertido (2580).🎊🎊 <br> <br> ¡Buena noticia! 🙌 Puedes seguir comprando para participar en el sorteo de este viernes 6 de diciembre. ¡Cada número es una nueva oportunidad de ganar! 🍀-->
          <!--      </div>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div> -->
        </div>


  </section>


  <!--section ganadores estaban aqui-->


  <!-- main container cards -->
  <div>
    <section data-aos="zoom-in" style="margin:20px 0px">
      <div class="text-bg text-center">
        <h2 class="h3">
          <span class="yellow my-2 mx-1">¡Paquete de Oportunidades!</span>
        </h2>
      </div>
    </section>

    <!-- Añadimos la cuenta regresiva al final del contenido de tu página, justo antes de cerrar el body -->
    <!--<div class="countdown">-->
    <!--    <span>Faltan:</span>-->
    <!--    <div id="timer"></div>-->
    <!--</div>-->


    <!--CUENTA REGRESIVA-->
    <!--    <script>-->
    <!--    function updateCountdown() {-->
    <!--        const eventDate = new Date("December 6, 2024 23:00:00").getTime();-->
    <!--        const now = new Date().getTime();-->
    <!--        const timeLeft = eventDate - now;-->

    <!--        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));-->
    <!--        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));-->
    <!--        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));-->
    <!--        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);-->

    <!--        document.getElementById("timer").innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;-->

    <!--        if (timeLeft < 0) {-->
    <!--            document.getElementById("timer").innerHTML = "¡El evento ha comenzado!";-->
    <!--        }-->
    <!--    }-->

    <!--    setInterval(updateCountdown, 1000);-->
    <!--</script>-->

    <!-- Agregamos el CSS que lo posicionará al pie de la página -->
    <style>
      .countdown {
        position: fixed;
        bottom: 0;
        /* Fija la cuenta regresiva en la parte inferior */
        left: 50%;
        transform: translateX(-50%);
        /* Centra la cuenta regresiva en la pantalla */
        background: #ffbc42;
        padding: 0px 15px;
        color: #000;
        font-size: 1.2em;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        /* Asegura que la cuenta regresiva esté sobre otros elementos */
        width: auto;
      }

      .countdown span {
        font-size: 1em;
        margin: 5px;
        FONT-WEIGHT: BOLD;
        FONT-STYLE: italic;
      }

      @media (max-width: 768px) {
        .countdown {
          font-size: 1em;
          padding: 0px 16px;
        }
      }
    </style>
    <div class="contenedor-cards">
      <!-- Cambios nuevo paquete -->



      <!-- <div class="card1" data-aos="zoom-in">
        <div class="cintilla-promocion"><img src="images/popular.png" class="popular" alt="Popular"></div>
        <div class="promocion">
          <h2>6x</h2>
        </div>

        <div class="precio">
          <h2>$30.000</h2>
          <span class="valor-promo">$48.000</span>
        </div>
        <div class="cta-buy">
          <button onclick="modal_x6()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>

        </div>
      </div> -->
    </div>

    <div class="espaciado">
      <span class="espaciado"></span>
    </div>

    <div class="contenedor-cards">

      <div class="card1" data-aos="zoom-in">
        <div class="promocion">
          <h2>5x</h2>
        </div>

        <div class="precio">
          <h2>$20.000</h2>
          <!--<span class="valor-promo">$27.000</span>-->
        </div>

        <div class="cta-buy">
          <button onclick="modal_x5()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>
        </div>
      </div>

      <div class="card1" data-aos="zoom-in">
        <div class="promocion">
          <h2>7x</h2>
        </div>

        <div class="precio">
          <h2>$28.000</h2>
          <!--<span class="valor-promo">$45.000</span>-->
        </div>
        <div class="cta-buy">
          <button onclick="modal_x7()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>

        </div>
      </div>

      <div class="card1" data-aos="zoom-in">
        <div class="cintilla-promocion"><img src="images/mas-vendido.png" alt="el mas vendido"></div>
        <div class="promocion">
          <h2>10x</h2>
        </div>

        <div class="precio">
          <h2>$35.000</h2>
          <!--<span class="valor-promo">$90.000</span>-->
        </div>
        <div class="cta-buy">
          <button onclick="modal_x10()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>

        </div>

      </div>



    </div>


    <div class="espaciado">
      <span class="espaciado"></span>
    </div>


    <div class="contenedor-cards">

      <div class="card1" data-aos="zoom-in">
        <div class="promocion">
          <h2>20x</h2>
        </div>

        <div class="precio">
          <h2>$70.000</h2>
          <span class="valor-promo">$70.000</span>
        </div>
        <div class="cta-buy">
          <button onclick="modal_x20()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>

        </div>
      </div>


      <div class="card1" data-aos="zoom-in">
        <img src="images/premium.png" width="150" style="margin-top: -45px; margin-right:10px" alt="paquete-premium">
        <div class="promocion">
          <h2 class="cincuenta">50x</h2>
        </div>

        <div class="precio">
          <h2>$150.000</h2>
          <span class="valor-promo">$175.000</span>

        </div>
        <div class="cta-buy">
          <button onclick="modal_x50()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>

        </div>
      </div>


      <div class="card1" data-aos="zoom-in">
        <div class="m-2">
          <p>Puedes digitar la Cantidad:</p>
          <input type="number" required min="3" leng="830" placeholder="Aquí:" id="input_manual" class="pb-4 input-manual" oninput="actualizarTotalManual()">
        </div>

        <div class="precio escoge">
          <h2 id="totalManual">$0</h2>

        </div>
        <div class="cta-buy">
          <button onclick="modal_xotro()">COMPRAR <span class="img-logo"><img src="images/haga-clic-aqui.png" alt="comprar ahora"></span></button>
        </div>
      </div>

    </div>
  </div>
  <!-- end container cards -->

  <div class="splide">
    <div class="splide__track">
      <ul class="splide__list">
        <!-- Cada <li> representa una diapositiva con título y contenido -->
        <li class="splide__slide">
          <div class="yellow">¡Ganador dinámica de <strong>Octubre</strong> FZ 3.0 🏍</div>
          <img src="images/ganadorfz.png" alt="Ganadora">
        </li>
        <li class="splide__slide">
          <div class="yellow">¡Ganadora Crypton FI! 🏍</div>
          <img src="images/ganadora.png" alt="Ganador MT">
        </li>
        <li class="splide__slide">
          <div class="yellow">¡Ganador dinámica de <strong>Julio</strong> Yamaha MT15! 🏍</div>
          <img src="images/ganadormt.png" alt="Ganador MT">
        </li>
        <li class="splide__slide">
          <div class="yellow">¡Ganador dinámica de <strong>Agosto</strong> NMAX 2025 🏍</div>
          <img src="images/ganadornmax.png" alt="Ganador MT">
        </li>
      </ul>
    </div>
  </div>

  <!--Nueva posicion ganadores-->
  <!-- <div id="slider" class="text-center py-2 mx-3 mt-2" data-aos="zoom-in">
    <h3 class="h3 mt-4">
      <span id="title-text" class="yellow my-4">¡Ganador dinámica de <strong>Julio</stganarong> Yamaha MT15! 🏍</span>
    </h3>
    <div class="container p-3">
      <img id="image" src="images/ganador1.png" alt="ganador mt" class="img-fluid rounded">
    </div>
  </div>

  <script>
    const titles = [
      "¡Ganador dinámica de <strong>Octubre</strong> FZ 3.0 🏍",
      "¡Ganadora Crypton FI! 🏍",
      "¡Ganador dinámica de <strong>Julio</strong> Yamaha MT15! 🏍",
      "¡Ganador dinámica de <strong>Agosto</strong> NMAX 2025 🏍"
    ];

    const images = [
      "images/ganadorfz.png",
      "images/ganadora.png",
      "images/ganadormt.png",
      "images/ganadornmax.png"

    ];

    let currentIndex = 0;

    function updateSlide() {
      currentIndex = (currentIndex + 1) % titles.length;
      document.getElementById('title-text').innerHTML = titles[currentIndex];
      document.getElementById('image').src = images[currentIndex];
    }

    // Cambia de diapositiva cada 3 segundos
    setInterval(updateSlide, 8000);
  </script> -->

  <!-- modal de calificación -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm sin-bg">
      <div class="modal-content sin-bg ">
        <div class="modal-header sin-bg">
          <h3 class="modal-title" id="exampleModalLabel">Califica nuestro Sitio</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body sin-bg">
          <form method="POST" action="functions/encuesta.php" id="formulario-encuesta">
            <div class="form-group sin-bg">
              <label class="col-form-label" for="rating-input">Calificación:</label>
              <input type="number" id="rating-input" value="1" class="d-none">
              <div id="star-rating" class="d-flex justify-content-center">

                <i class="fas fa-star selected" data-rating="1"></i>
                <i class="far fa-star" data-rating="2"></i>
                <i class="far fa-star" data-rating="3"></i>
                <i class="far fa-star" data-rating="4"></i>
                <i class="far fa-star" data-rating="5"></i>

              </div>

            </div>
            <div class="form-group">
              <label class="col-form-label" for="observaciones">Observaciones: (opcional)</label>
              <textarea required class="form-control" id="observaciones" name="observaciones"></textarea>
            </div>
            <div class="modal-footer sin-bg">
              <button class="btn btn-secondary btn-enviar-encuesta btn-loading" type="submit">
                Calificar
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- end de calificación -->

  <!-- Modal Todo Vendido -->
  <!-- <div class="modal fade" id="modalTodoVendido" tabindex="-1" aria-labelledby="modalTodoVendido" aria-hidden="true">
    <div class="modal-dialog modal-sm sin-bg">
      <div class="modal-content sin-bg ">
        <div class="modal-header sin-bg">
          <h3 class="modal-title" id="modalTodoVendido">Información importante 📢</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body sin-bg">
            <div><span class="text-dark" style="text-align:justify!important"><strong>Estimado Cliente, <br></strong>Informamos que todas las boletas han sido vendidas. Agradecemos tu participación y te invitamos a regresar el próximo <strong>lunes 12 de agosto</strong> para nuestra siguiente dinámica.</span></div>
        </div>
      </div>
    </div>
  </div> -->
  <!-- end de calificación -->

  <!-- end de calificación -->
  <!-- start Modals-Moto -->
  <div class="modal fade " id="modalRifa" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-main">
          <!-- <h2 class="modal-title title-rifa-modal" id="exampleModalLabel">Sorteo MT15 0 KM</h2> -->
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="functions/openpay/pagar.php" id="formulario" class="formulario" autocompleate="off">
            <div class="container">
              <div class="row">
                <div class="col">
                  <p class="mb-2">Tu información:</p>
                  <div class="form-group">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fa-solid fa-mobile-retro"></i>
                        </span>
                      </div>
                      <input type="number" class="form-control campo-vacio" min="10" aria-label="Celular" aria-describedby="Celular" placeholder="Celular:" required name="celular" id="celular">
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </span>
                      </div>
                      <input type="nombre" class="form-control campo-vacio text-capitalize" placeholder="Nombre:" aria-label="Nombre" aria-describedby="Nombre" required name="nombre" id="nombre">
                      <div class="invalid-feedback">
                        Debes especificar al menos un nombre y un apellido.
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fa fa-solid fa-envelope"></i>
                        </span>
                      </div>
                      <input type="email" class="form-control campo-vacio" placeholder="Correo:" aria-label="Correo" aria-describedby="correo" required name="correo" id="correo">
                      <input type="hidden" name="correoReal" id="correoReal">
                    </div>
                  </div>

                  <div class="form-group" id="departamento">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="departamento">
                          <i class="fa-solid fa-list"></i>
                        </span>
                      </div>

                      <select class="custom-select campo-vacio" id="usp-custom-departamento-de-residencia" name="departamento" required>
                        <option value="">Departamento</option>
                        <option value="Antioquia">ANTIOQUIA</option>
                        <option value="Amazonas">AMAZONAS</option>
                        <option value="Arauca">ARAUCA</option>
                        <option value="Atlántico">ATLÁNTICO</option>
                        <option value="Bolívar">BOLÍVAR</option>
                        <option value="Boyacá">BOYACÁ</option>
                        <option value="Caldas">CALDAS</option>
                        <option value="Caquetá">CAQUETÁ</option>
                        <option value="Casanare">CASANARE</option>
                        <option value="Cauca">CAUCA</option>
                        <option value="Cesar">CESAR</option>
                        <option value="Chocó">CHOCÓ</option>
                        <option value="Córdoba">CÓRDOBA</option>
                        <option value="Cundinamarca">CUNDINAMARCA</option>
                        <option value="Guainía">GUAINÍA</option>
                        <option value="Guaviare">GUAVIARE</option>
                        <option value="Huila">HUILA</option>
                        <option value="La Guajira">LA GUAJIRA</option>
                        <option value="Magdalena">MAGDALENA</option>
                        <option value="Meta">META</option>
                        <option value="Nariño">NARIÑO</option>
                        <option value="Norte de Santander">NORTE DE SANTANDER</option>
                        <option value="Putumayo">PUTUMAYO</option>
                        <option value="Quindío">QUINDÍO</option>
                        <option value="Risaralda">RISARALDA</option>
                        <option value="San Andrés y Providencia">SAN ANFRÉS Y PROVIDENCIA</option>
                        <option value="Santander">SANTANDER</option>
                        <option value="Sucre">SUCRE</option>
                        <option value="Tolima">TOLIMA</option>
                        <option value="Valle del Cauca">VALL DEL CAUCA</option>
                        <option value="Vaupés">VAUPÉS</option>
                        <option value="Vichada">VICHADA</option>
                      </select>
                    </div>
                  </div>

                  <div class="input-group mb-3" id="ciudad">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="">
                        <i class="fa-solid fa-list"></i>
                      </span>
                    </div>
                    <select class="custom-select campo-vacio" id="usp-custom-municipio-ciudad" name="ciudad" required id="ciudad">
                      <option value="" disabled selected>Seleccionar..</option>
                    </select>
                  </div>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="">
                        <i class="fa-solid fa-list"></i>
                      </span>
                    </div>
                    <select class="custom-select campo-vacio" id="opciones_boletas" name="opciones_boletas">
                      <option value="Cantidad de Oportunidades" required>Oportunidades</option>
                      <!-- <option value="2">2x = $12.000</option> -->
                      <option value="5">5x = $20.000</option>
                      <option value="7">7x = $28.500</option>
                      <!-- <option value="6">6x = $30.000</option> -->
                      <option value="10">10x = $35.000</option>
                      <option value="20">20x = $70.000</option>
                      <option value="50">50x = $175.000</option>
                      <!--<option value="100">100x = $600.000</option>-->
                      <option value="Otro">Otro</option>
                    </select>
                  </div>
                  <div class="input-otro">
                    <div class="input-group mb-3 ">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="cantidad">
                          <i class="fa-solid fa-hashtag"></i>
                        </span>
                      </div>
                      <input type="number" class="form-control" placeholder="Especifica la cantidad:" aria-label="Celular" aria-describedby="Numero" id="otroInput" name="otroInput">
                    </div>
                  </div>
                  <div class="mb-3" id="content_metodo">
                    <span class="form-check-label">Método de Pago:</span>
                    <div class="btn-group-toggle mt-2" data-toggle="buttons">
                      <label class="btn btn-primary" for="pse">
                        <input type="radio" name="metodo_pago" id="pse" autocomplete="off" checked> PSE
                      </label>
                      <!--<label class="btn btn-success" for="tarjeta">-->
                      <!--  <input type="radio" name="metodo_pago" id="tarjeta" autocomplete="off"> Tarjeta Débito/Crédito <i class="fa-regular fa-credit-card"></i>-->
                      <!--</label>-->
                    </div>
                    <div class="invalid-feedback campo-incompleto" id="metodoPagoFeedback">Por favor, selecciona el método de Pago</div>
                  </div>


                  <div class="input-group mb-3 ">
                    <div class="form-check input-group-prepend">
                      <input class="form-check-input" type="checkbox" value="Acepto" id="habeasData" required checked>
                      <label class="form-check-label" for="habeasData">
                        Acepto la Política de Protección de Datos Personales. <a class="habeas" target="_blank" href="docs/politica de proteccion de datos personale.pdf">(Consultar)</a>
                      </label>
                    </div>
                    <div class="invalid-feedback campo-vacio" id="habeasDataFeedback">Por favor, acepta la política de protección de datos personales.</div>
                  </div>

                  <!-- Contenedor de totales -->
                  <div class="d-flex">
                    <p>Números a Jugar: <strong>
                        <span></span>
                      </strong>
                    </p>
                    <input type="text" class="ml-1 w-50 input-total-numeros" name="totalNumeros" id="totalNumeros" value="0" readonly>
                  </div>
                  <div class="d-flex">
                    <p>Total a Pagar: <strong>
                        <span></span>
                      </strong>
                    </p>
                    <input type="text" class="ml-1 w-50 input-total-numeros" name="totalPagar" id="totalPagar" value="0" readonly>
                  </div>

                </div>
              </div>
              <div class="modal-footer">
                <div class="d-flex justify-content-between">
                  <div class="ml-4">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-secondary btn-pay btn-loading" type="submit" id="btn-pay">
                      Pagar <i class="fas fa-shopping-cart"></i>
                      <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                  </div>
                </div>
              </div>

            </div>
        </div>
      </div>
      </form>
    </div>
  </div>
  <!-- end modal moto -->

  <!-- modal search numbers -->
  <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm sin-bg">
      <div class="modal-content sin-bg">
        <div class="modal-header sin-bg">
          <h3 class="modal-title" id="exampleModalLabel">Buscar números comprados</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body sin-bg">
          <form method="POST" action="#" id="formulario-consulta-numeros">
            <div class="form-group sin-bg">
              <label for="buscar-numeros-celular" class="col-form-label">Ingresa tu número de celular:</label>
              <div class="form-group">
                <input type="number" class="form-control consulta__numeros_comprados" id="buscar-numeros-celular" name="celular">
              </div>
            </div>
            <div class="invalid-feedback campo-incompleto" id="campoIncompleto">
              Ingresa un número de celular válido.
            </div>
            <div class="modal-footer sin-bg">
              <button class="btn-sm button-85 btn-loading btn-buscar-numeros-comprados" type="submit">
                Buscar <i class="fa-solid fa-magnifying-glass"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- end modal search numbers -->

  <!-- whatsapp estaba https://wa.link/4jagve -->
  <div class="btn-whatsapp">
    <a href="https://wa.link/r3lqjg" target="_blank">
      <svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" viewBox="0 0 39 39">
        <path fill="#00E676" d="M10.7 32.8l.6.3c2.5 1.5 5.3 2.2 8.1 2.2 8.8 0 16-7.2 16-16 0-4.2-1.7-8.3-4.7-11.3s-7-4.7-11.3-4.7c-8.8 0-16 7.2-15.9 16.1 0 3 .9 5.9 2.4 8.4l.4.6-1.6 5.9 6-1.5z">
        </path>
        <path fill="#FFF" d="M32.4 6.4C29 2.9 24.3 1 19.5 1 9.3 1 1.1 9.3 1.2 19.4c0 3.2.9 6.3 2.4 9.1L1 38l9.7-2.5c2.7 1.5 5.7 2.2 8.7 2.2 10.1 0 18.3-8.3 18.3-18.4 0-4.9-1.9-9.5-5.3-12.9zM19.5 34.6c-2.7 0-5.4-.7-7.7-2.1l-.6-.3-5.8 1.5L6.9 28l-.4-.6c-4.4-7.1-2.3-16.5 4.9-20.9s16.5-2.3 20.9 4.9 2.3 16.5-4.9 20.9c-2.3 1.5-5.1 2.3-7.9 2.3zm8.8-11.1l-1.1-.5s-1.6-.7-2.6-1.2c-.1 0-.2-.1-.3-.1-.3 0-.5.1-.7.2 0 0-.1.1-1.5 1.7-.1.2-.3.3-.5.3h-.1c-.1 0-.3-.1-.4-.2l-.5-.2c-1.1-.5-2.1-1.1-2.9-1.9-.2-.2-.5-.4-.7-.6-.7-.7-1.4-1.5-1.9-2.4l-.1-.2c-.1-.1-.1-.2-.2-.4 0-.2 0-.4.1-.5 0 0 .4-.5.7-.8.2-.2.3-.5.5-.7.2-.3.3-.7.2-1-.1-.5-1.3-3.2-1.6-3.8-.2-.3-.4-.4-.7-.5h-1.1c-.2 0-.4.1-.6.1l-.1.1c-.2.1-.4.3-.6.4-.2.2-.3.4-.5.6-.7.9-1.1 2-1.1 3.1 0 .8.2 1.6.5 2.3l.1.3c.9 1.9 2.1 3.6 3.7 5.1l.4.4c.3.3.6.5.8.8 2.1 1.8 4.5 3.1 7.2 3.8.3.1.7.1 1 .2h1c.5 0 1.1-.2 1.5-.4.3-.2.5-.2.7-.4l.2-.2c.2-.2.4-.3.6-.5s.4-.4.5-.6c.2-.4.3-.9.4-1.4v-.7s-.1-.1-.3-.2z">
        </path>
      </svg> </a>
  </div>

  <div class="btn-instagram">
    <a href="https://www.instagram.com/eldiadetusuertecol" target="_blank">
      <img class="instagram" src="data:image/jpeg;base64,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" alt="Boton Instagram">
    </a>
  </div>

  <div class="btn-facebook">
    <a href="https://www.facebook.com/profile.php?id=100092247236425" target="_blank">
      <img class="facebook" src="data:image/png;base64,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" alt="Boton Facebook">
    </a>
  </div>

  <div class="btn-encuesta" data-toggle="tooltip" data-placement="right" title="👈 Danos tu Opinión">
    <button type="button" class="btn encuesta" id="encuesta" data-toggle="modal" data-target="#exampleModal"><img src="images/satisfaccion.png" alt="Califi nuestro sitio web"></button>
  </div>

  <!-- end service section -->
  <!-- start include footer -->
  <?php include 'includes/footer.php' ?>
  <!-- end include footer -->
  <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            var myModal = new bootstrap.Modal(document.getElementById('modalTodoVendido'), {
                keyboard: false  // Opcional, controla si el modal se puede cerrar con la tecla "Esc"
            });
            myModal.show();
        });
    </script> -->
  </body>

  </html>