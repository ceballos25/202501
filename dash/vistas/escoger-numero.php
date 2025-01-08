<?php
include '../include/header.php';
include '../../config/config_bd.php';
// valido la sesion para que el formulario no se vuelva a enviar 2 veces


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
        <h1 class="mt-4 mb-4 mx-4">Generar una Venta (número específico)</h1>
        <div class="container-fluid px-4">
            <form class="row g-3" method="POST" action="../functions/venta.php" id="formulario">
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

                <div class="col-md-4" id="inputs-container">
                    <label for="inputAddress2" class="form-label">Número:</label>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control numeros" name="numeros[]" maxlength="4" oninput="validarNumero(this)" required>
                        <a class="btn btn-sm btn-success" id="add-input">+</a>
                    </div>
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
                const numerosInputs = document.querySelectorAll('input.numeros');

                // Bandera para verificar campos vacíos
                let camposIncompletos = false;

                // Iterar sobre los inputs con la clase 'numeros' para verificar si alguno está vacío
                numerosInputs.forEach(input => {
                    const numero = input.value.trim(); // Obtener el valor del input y limpiar espacios

                    // Verificar y corregir la longitud del número (no debe tener más de 4 caracteres)
                    if (numero.length > 4 || numero.length < 4) {
                        // Cortar el número para dejar solo los primeros 4 caracteres
                        alert("❌ El número NO debe ser mayor o menor a 4 dígitos. ❌");
                        exit();
                    };

                    if (numero == '1234' ||
                        numero == '1515' ||
                        numero == '1905' ||
                        numero == '0108' ||
                        numero == '1122' ||
                        numero == '9999' ||
                        numero == '7007' ||
                        numero == '6666' ||
                        numero == '4268' ||
                        numero == '8015' ) {
                        alert("❌ Acabas de ingresar un número premiado, verifica e intenta nuevamente. ❌");
                        exit();
                    }


                    // Verificar si el campo está vacío
                    if (numero === '') {
                        camposIncompletos = true; // Establecer la bandera como 'true'
                        return; // Salir del bucle forEach si se encuentra un campo vacío
                    }
                });

                // Verificar si algún campo requerido está vacío o si se encontró algún campo vacío en 'numerosInputs'
                if (!nombre || !celular || !correo || !departamento || !ciudad || !comprobante || camposIncompletos) {
                    // Mostrar mensaje de error si falta algún campo requerido o se encontró un campo vacío
                    alert('❌ Por favor, complete todos los campos, incluyendo los campos numéricos ❌.');
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


        //agregar numero especifico
        document.addEventListener('DOMContentLoaded', function() {
            const inputsContainer = document.getElementById('inputs-container');
            const totalNumerosContainer = document.getElementById('total-numeros-container');
            const totalPagarContainer = document.getElementById('total-pagar-container');

            // Función para contar el número de inputs
            function contarInputs() {
                const inputs = inputsContainer.querySelectorAll('input[type="number"]');
                return inputs.length;
            }


            // el que estaba cambios del 07/11/2024
            // // Función para calcular el total a pagar según la cantidad de inputs
            // function calcularTotalPagar(totalNumeros) {
            //     let totalPagar = 0;
            
            //     if (totalNumeros < 5) {
            //         totalPagar = totalNumeros * 9000; // Menos de 3 boletas a 8 mil
            //     } else if (totalNumeros >= 5 && totalNumeros < 10) {
            //         totalPagar = totalNumeros * 8000; // De 3 a 4 boletas a 7 mil
            //     } else if (totalNumeros >= 10 && totalNumeros < 100) {
            //         totalPagar = totalNumeros * 7000; // A partir de 5 boletas a 6 mil
            //     }else if (totalNumeros > 99){
            //         totalPagar = totalNumeros * 6500;
            //     }
            
            //     return totalPagar;
            // }
            
            
                        // Función para calcular el total a pagar según la cantidad de inputs
            function calcularTotalPagar(totalNumeros) {
                let totalPagar = 0;
            
                if (totalNumeros < 10) {
                    totalPagar = totalNumeros * 4000; // Menos de 3 boletas a 9000
                } else { 
                    totalPagar = totalNumeros * 3500; // 20 o más boletas a 6000
                }
                return totalPagar;

            }


            // Función para actualizar los elementos HTML con los resultados
            function actualizarResultados() {
                const totalNumeros = contarInputs();
                const totalPagar = calcularTotalPagar(totalNumeros);

                // Crear HTML con badges de Bootstrap para mostrar los resultados
                const numerosBadgeHTML = `<span class="badge bg-primary">${totalNumeros}</span>`;
                const pagarBadgeHTML = `<span class="badge bg-success">$${totalPagar.toLocaleString()}</span>`;

                // Asignar el HTML generado a los contenedores correspondientes
                totalNumerosContainer.innerHTML = numerosBadgeHTML;
                totalPagarContainer.innerHTML = pagarBadgeHTML;
            }

            // Evento de clic en el botón de agregar input
            const addButton = document.getElementById('add-input');
            addButton.addEventListener('click', function() {
                const newInputGroup = document.createElement('div');
                newInputGroup.classList.add('input-group', 'mb-3');

                const newInput = document.createElement('input');
                newInput.type = 'number';
                newInput.classList.add('form-control', 'numeros');
                newInput.name = 'numeros[]';
                newInput.required = true;
                newInput.maxLength = 4; // Longitud máxima permitida para el input (4 caracteres)
                newInput.setAttribute('oninput', 'validarNumero(this)');


                const removeButton = document.createElement('button');
                removeButton.classList.add('btn', 'btn-sm', 'btn-danger', 'remove-input');
                removeButton.textContent = '-';
                removeButton.addEventListener('click', function() {
                    inputsContainer.removeChild(newInputGroup);
                    actualizarResultados(); // Actualizar resultados al eliminar un input
                });

                newInputGroup.appendChild(newInput);
                newInputGroup.appendChild(removeButton);
                inputsContainer.appendChild(newInputGroup);

                actualizarResultados(); // Actualizar resultados al agregar un input
            });

            // Evento de clic en el botón de eliminar input (delegación de eventos)
            inputsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-input')) {
                    const inputGroup = e.target.closest('.input-group');
                    if (inputGroup) {
                        inputsContainer.removeChild(inputGroup);
                        actualizarResultados(); // Actualizar resultados al eliminar un input
                    }
                }
            });

            // Al cargar la página, inicialmente actualizar los resultados
            actualizarResultados();
        });

        function validarNumero(input) {
            const numero = input.value.trim();
            if (numero.length === 4) {
                // Realizar una petición AJAX para verificar si el número existe en la base de datos
                const url = '../functions/validar_numero.php'; // Ruta al archivo PHP que verificará el número
                const params = new URLSearchParams({
                    numero
                });

                fetch(`${url}?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.existe) {
                            //alert(`El número ${numero} ya está en uso.`);

                            Swal.fire({
                                title: '¡Algo salió mal!',
                                text: `El número ${numero} ya se vendió.`,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#000'
                            }).then((result) => {
                                if (result.isConfirmed) {}
                            });

                            input.value = ''; // Limpiar el campo si el número existe
                        }
                    })
                    .catch(error => console.error('Error al verificar el número:', error));
            }
        }
    </script>

    <!-- inicia footer -->
    <?php
    include '../include/footer.php';
    ?>
    <!-- termina footer -->