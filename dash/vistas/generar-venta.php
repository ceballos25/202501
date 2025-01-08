<?php
include '../include/header.php';
include '../../config/config_bd.php';

// valido la sesion para que el formulario no se vuelva a enviar 2 veces
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$conn = obtenerConexion();

$conn->close();
?>


<!-- termina header -->

<!-- inicia plantilla -->
<?php

include 'plantilla.php';
?>
<!-- termina plantilla -->


<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <h1 class="mt-4 mb-4 mx-4">Generar una Venta</h1>
        <div class="container-fluid px-4">
            <form class="row g-3" method="POST" action="../functions/venta-manual.php" id="formulario">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="col-md-4">
                    <label for="inputAddress2" class="form-label">Celular:</label>
                    <input type="number" class="form-control" id="celular" name="celular" required>
                </div>
                <div class="col-md-4">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" class="form-control text-capitalice" id="nombre" name="nombre" required>
                </div>
                <div class="col-md-4">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                </div>
                <div class="col-md-4">
                    <label for="inputState" class="form-label">Departamento:</label>
                    <select class="custom-select form-control" id="usp-custom-departamento-de-residencia" name="departamento" required>
                        <option value="">Departamento</option>
                        <option value="Antioquia">Antioquia</option>
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
                <div class="col-md-4">
                    <label for="inputState" class="form-label">Ciudad:</label>
                    <select class="custom-select form-control" id="usp-custom-municipio-ciudad" name="ciudad" required>
                        <option value="" disabled selected>Seleccionar..</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="inputAddress2" class="form-label">Comprobante de pago:</label>
                    <input type="text" class="form-control" id="comprobante" name="comprobante" required>
                </div>

                <div class="col-md-4">
                    <label for="opciones_boletas" class="form-label">Oportunidades:</label>
                    <select class="custom-select form-control" id="opciones_boletas" name="opciones_boletas" required>
                        <option></option>
                        <!-- <option value="2">2x = $18.000</option>-->
                        <!--<option value="3">3x = $21.000</option>-->
                        <!--<option value="4">4x = $28.000</option>-->
                        <!-- <option value="4">4x = $24.000</option> -->
                        <option value="5">5x = $17.500</option>
                        <option value="7">7x = $24.500</option>
                         <option value="10">10x = $35.000</option>
                        <option value="20">20x = $60.000</option>
                        <option value="50">50x = $150.000</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="col-md-4 input-otro" style="display: none;">
                    <label for="otroInput" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" placeholder="Especifica la cantidad:" id="otroInput" name="otroInput">
                </div>


                <div class="col-md-4 mt-4">

                    <div class="m-1">
                        <span class="badge bg-dark">Total Números:</span>
                        <span id="total-numeros-container"></span>
                    </div>

                    <div class="m-1">
                        <span class="badge bg-dark">Total a Pagar:</span>
                        <span id="total-pagar-container"></span>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-center">
                    <button type="submit" id="btn-submit" class="btn btn-success">Generar</button>
                </div>


            </form>
        </div>
    </main>
    <!-- termina contenido -->

    <script>
        document.getElementById("formulario").addEventListener("submit", function(event) {
            // Deshabilitar el botón al enviar el formulario
            document.getElementById("btn-submit").disabled = true;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const selectOpciones = document.getElementById('opciones_boletas');
            const inputOtro = document.querySelector('.input-otro');

            // Agregar evento de cambio al select
            selectOpciones.addEventListener('change', function() {
                const selectedOption = this.value;

                // Mostrar u ocultar div input-otro según la selección
                if (selectedOption === 'Otro') {
                    inputOtro.style.display = 'block';
                } else {
                    inputOtro.style.display = 'none';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const opcionesBoletas = document.getElementById('opciones_boletas');
            const otroInput = document.getElementById('otroInput');
            const totalNumerosContainer = document.getElementById('total-numeros-container');
            const totalPagarContainer = document.getElementById('total-pagar-container');

            // Función para actualizar los totales
            function actualizarTotales() {
                let totalNumeros = 0;
                let totalPagar = 0;

                const seleccion = opcionesBoletas.value;

                if (seleccion === 'Otro' && otroInput.value.trim() !== '') {
                    totalNumeros = parseInt(otroInput.value.trim());
                } else if (seleccion !== '0') {
                    totalNumeros = parseInt(seleccion);
                }

                    // Calcular el valor a pagar según la cantidad de números actual cambio 07-11-2024
                    // if (totalNumeros < 5) {
                    //     totalPagar = totalNumeros * 9000; // Menos de 3 boletas a 8 mil
                    // } else if (totalNumeros >= 5 && totalNumeros < 10) {
                    //     totalPagar = totalNumeros * 8000; // De 3 a 4 boletas a 7 mil
                    // } else if (totalNumeros >= 10 && totalNumeros < 100) {
                    //     totalPagar = totalNumeros * 7000; // A partir de 5 boletas a 6 mil
                    // }else if(totalNumeros > 99){
                    //     totalPagar = totalNumeros * 6500; // A partir de 5 boletas a 6 mil
                    // }
                    
                    // Calcular el valor a pagar según la cantidad de números
                        if (totalNumeros < 20) {
                            totalPagar = totalNumeros * 3500;
                        } else { // Para todos los valores >= 20
                            totalPagar = totalNumeros * 3000;
                        }




                // Mostrar los totales en badges de Bootstrap
                totalNumerosContainer.innerHTML = `<span class="badge bg-primary">${totalNumeros}</span>`;
                totalPagarContainer.innerHTML = `<span class="badge bg-success">$${totalPagar.toLocaleString()}</span>`;
            }

            // Evento de cambio en el select
            opcionesBoletas.addEventListener('change', function() {
                if (this.value === 'Otro') {
                    otroInput.parentElement.style.display = 'block';
                } else {
                    otroInput.parentElement.style.display = 'none';
                }
                actualizarTotales();
            });

            // Evento de entrada en el input "Otro"
            otroInput.addEventListener('input', function() {
                actualizarTotales();
            });
        });

        // Enviar el formulario con los nuevos input
        document.addEventListener('DOMContentLoaded', function() {
            const btnGenerar = document.getElementById('btn-submit');
            const totalNumerosContainer = document.getElementById('total-numeros-container');
            const totalPagarContainer = document.getElementById('total-pagar-container');

            btnGenerar.addEventListener('click', function(event) {
                event.preventDefault(); // Evitar que el formulario se envíe automáticamente

                // Obtener los valores actuales de "Total de Números" y "Total a Pagar"
                const totalNumeros = totalNumerosContainer.textContent.trim();
                const totalPagar = totalPagarContainer.textContent.trim().replace('$', '').replace(/,/g, '');

                // Verificar que los campos requeridos estén completos
                const nombre = document.getElementById('nombre').value.trim();
                const celular = document.getElementById('celular').value.trim();
                const correo = document.getElementById('correo').value.trim();
                const departamento = document.getElementById('usp-custom-departamento-de-residencia').value.trim();
                const ciudad = document.getElementById('usp-custom-municipio-ciudad').value.trim();
                const comprobante = document.getElementById('comprobante').value.trim();
                const opcionesBoletas = document.getElementById('opciones_boletas').value.trim();

                if (!nombre || !celular || !correo || !departamento || !ciudad || !comprobante || !opcionesBoletas) {
                    // Mostrar mensaje de error o tomar otra acción si algún campo requerido está vacío
                    alert('❌ Por favor, complete todos los campos ❌.');
                    return; // Detener el envío del formulario si falta algún campo requerido
                }

                // Cambiar contenido del botón a indicador de carga
                btnGenerar.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Espere...
        `;

                // Deshabilitar el botón mientras se procesa el formulario
                btnGenerar.disabled = true;

                // Crear campos ocultos en el formulario y agregar los valores capturados
                const form = document.getElementById('formulario');

                const inputTotalNumeros = document.createElement('input');
                inputTotalNumeros.type = 'hidden';
                inputTotalNumeros.name = 'total_numeros';
                inputTotalNumeros.value = totalNumeros;

                const inputTotalPagar = document.createElement('input');
                inputTotalPagar.type = 'hidden';
                inputTotalPagar.name = 'total_pagar';
                inputTotalPagar.value = totalPagar;

                form.appendChild(inputTotalNumeros);
                form.appendChild(inputTotalPagar);

                // Enviar el formulario después de agregar los campos ocultos
                form.submit();
            });
        });
    </script>

    <!-- inicia footer -->
    <?php
    include '../include/footer.php';
    ?>
    <!-- termina footer -->