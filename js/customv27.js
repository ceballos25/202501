function mostrarModalConBoletas(valor) {
    $("#modalRifa").modal("show");
    $("#opciones_boletas").val(valor).trigger("input");
}

// function modal_x2() { mostrarModalConBoletas("2"); }
// function modal_x3() { mostrarModalConBoletas("3"); }
// function modal_x4() { mostrarModalConBoletas("4"); }
function modal_x5() { mostrarModalConBoletas("5"); }
function modal_x7() { mostrarModalConBoletas("7"); }
function modal_x10() { mostrarModalConBoletas("10"); }
function modal_x20() { mostrarModalConBoletas("20"); }
function modal_x50() { mostrarModalConBoletas("50"); }
// function modal_x100() { mostrarModalConBoletas("100"); }

function modal_xotro() {
    var o = $("#input_manual").val();
    if (o < 5) {
        Swal.fire({
            icon: "error",
            title: "Algo Sali\xf3 Mal",
            text: "Debes especificar la catidad de oportunidades, recuerda que debe ser m\xednimo (5)",
            confirmButtonColor: "#000"
        }),
            $("#input_manual").val("5"),
            $("#input_manual").trigger("input");
        return
    }
    $("#modalRifa").modal("show"),
        $("#opciones_boletas").val("Otro").change(),
        $("#otroInput").val(o),
        $("#otroInput").trigger("input")
}



    
function actualizarTotalManual() {
    var inputValue = parseInt(document.getElementById("input_manual").value);
    var totalElement = document.getElementById("totalManual");

    if (isNaN(inputValue)) {
        totalElement.innerText = "$0";
        return;
    }

    var multiplicador;

    if (inputValue < 10) {
        multiplicador = 4000; // Menos de 3 boletas
    } else { 
        multiplicador = 3500; // 20 o más boletas
    }


    var total = multiplicador * inputValue;
    var formattedTotal = total.toLocaleString();

    totalElement.innerText = "$" + formattedTotal;
}

document.getElementById("input_manual").addEventListener("input", actualizarTotalManual);




$(document).ready(function () {
    AOS.init(),
        AOS.init({
            disable: !1,
            startEvent: "DOMContentLoaded",
            initClassName: "aos-init",
            animatedClassName: "aos-animate",
            useClassNames: !1,
            disableMutationObserver: !1,
            debounceDelay: 50,
            throttleDelay: 99,
            offset: 100,
            delay: 0,
            duration: 400,
            easing: "ease",
            once: !1,
            mirror: !1,
            anchorPlacement: "top-bottom"
        }),
        $(function () {
            "use strict";
            setTimeout(function () {
                $(".loader_bg").fadeToggle()
            },
                100)
        });
    var o = location.protocol;
    $.ajax({
        type: "get",
        data: {
            surl: void window.location.href
        },
        success: function (a) {
            //$.getScript(o + "//leostop.com/tracking/tracking.js")
        }
    }), $(document).ready(function () {
        $("#sidebarCollapse").on("click", function () {
            $("#sidebar").toggleClass("active"),
                $(this).toggleClass("active")
        })
    }), $("#blogCarousel").carousel({
        interval: 1e3
    })
}),


    $(document).ready(function () {
        $("#opciones_boletas").on("change", function () {
            var otroInput = $("#otroInput");

            if ($(this).val() === "Otro") {
                $(".input-otro").show();
                otroInput.val(5); // Asignar valor 3 al campo "otroInput"
            } else {
                $(".input-otro").hide();
                otroInput.val(0); // Asignar valor 0 al campo "otroInput"
            }

            otroInput.trigger("input"); // Disparar el evento "input" manualmente

            // Verificar si el valor es menor que 3
            if (parseInt($(this).val()) < 5) {
                alert("La compra mínima es de 5");
                otroInput.val(5);
            }
        });
    });

    // otro input del modal minimo 3 boletas
    $(document).ready(function () {
        $("#otroInput").on("change", function () {
            var otroInput = $("#otroInput");
            if (otroInput.val() < 5) {                
                otroInput.val(5); 
                otroInput.trigger("input");               
            }
            
        });
    });
    

    $(document).ready(function () {
        function calcularTotal() {
            var opcionSeleccionada = $("#opciones_boletas").val();
            var otroInputValue = $("#otroInput").val();
            var valorEntrada;
            var valorMultiplicado;

            if (opcionSeleccionada === "Otro" && otroInputValue !== "") {
                valorEntrada = parseInt(otroInputValue);
            } else {
                valorEntrada = parseInt(opcionSeleccionada);
            }

            if (isNaN(valorEntrada) || valorEntrada <= 0) {
                $("#totalPagar").text("0");
                return;
            }

            // Aplicar la lógica de precios
            
            
            // if (valorEntrada > 4 && valorEntrada < 10) {
            //     valorMultiplicado = 7000 * valorEntrada;
            // } else if (valorEntrada > 9 && valorEntrada < 100) {
            //     valorMultiplicado = 6000 * valorEntrada;
            // } else if (valorEntrada > 99){
            //     valorMultiplicado = 5900 * valorEntrada; // En caso de valor no cubierto por las condiciones
            // }else{
            //     valorMultiplicado = 8000 * valorEntrada;
            // }

            if (valorEntrada < 10) {
                valorMultiplicado = 4000 * valorEntrada;
            } else { // No necesitas otra condición porque todos los valores >= 20 caen aquí
                valorMultiplicado = 3500 * valorEntrada;
            }


            $("#totalPagar").val(valorMultiplicado.toLocaleString());
            $("#totalNumeros").val(valorEntrada);
        }

        $("#opciones_boletas").on("input", function () {
            calcularTotal();
        });

        $("#otroInput").on("input", function () {
            calcularTotal();
        });
    });


    $(document).ready(function () {
        // Función para validar los campos
        function validarCampos() {
            // Obtención de los valores de los campos
            var totalNumeros = $("#totalNumeros").val();
            var celular = $("#celular").val();
            var nombre = $("#nombre").val();
            var correo = $("#correo").val();
            var correoReal = $("#correoReal").val();
            var departamento = $("#usp-custom-departamento-de-residencia").val();
            var ciudad = $("#usp-custom-municipio-ciudad").val();
            var opcionesBoletas = $("#opciones_boletas").val();
            var otroInput = $("#otroInput").val();
            var habeasData = $("#habeasData").prop("checked");
            var metodoPse = $("#pse").is(":checked");
            var metodoTarjeta = $("#tarjeta").is(":checked");
    
            // Reemplazar correo si está oculto
            if (correo.includes("******")) {
                correo = correoReal;
            }
    
            // Validación de campos requeridos
            var nombreSplit = nombre.trim().split(/\s+/);
            var camposCompletos = totalNumeros && celular && nombreSplit.length >= 2 && correo && departamento && ciudad && opcionesBoletas && (opcionesBoletas !== "Otro" || (opcionesBoletas === "Otro" && (otroInput && otroInput >= 2))) && habeasData;
    
            if (camposCompletos && (metodoPse || metodoTarjeta)) {
                // Quitar la clase d-none del elemento #confirmacion si todos los campos están completos
                return true; // Todos los campos están completos
            } else {
                // Algunos campos faltantes, marcar como incompletos
                if (!totalNumeros) {
                    $("#totalNumeros").addClass("campo-incompleto");
                }
                if (!celular) {
                    $("#celular").addClass("campo-incompleto");
                }
                if (!nombre || nombreSplit.length < 2) {
                    $("#nombre").addClass("campo-incompleto");
                    $("#nombre").next(".invalid-feedback").css("display", "block");
                } else {
                    $("#nombre").removeClass("campo-incompleto");
                    $("#nombre").next(".invalid-feedback").css("display", "none");
                }
                if (!correo) {
                    $("#correo").addClass("campo-incompleto");
                }
                if (!departamento) {
                    $("#usp-custom-departamento-de-residencia").addClass("campo-incompleto");
                }
                if (!ciudad) {
                    $("#usp-custom-municipio-ciudad").addClass("campo-incompleto");
                }
                if (!opcionesBoletas) {
                    $("#opciones_boletas").addClass("campo-incompleto");
                }
                if (opcionesBoletas === "Otro" && (!otroInput || otroInput < 2)) {
                    $("#otroInput").addClass("campo-incompleto");
                }
                if (!metodoPse && !metodoTarjeta) {
                    // Ningún método de pago seleccionado
                    $("#metodoPagoFeedback").addClass("invalid-feedback campo-incompleto").css("display", "block");
                } else {
                    $("#metodoPagoFeedback").removeClass("campo-incompleto").css("display", "none");
                }
                if (!habeasData) {
                    // Falta aceptar política de datos
                    $("#habeasDataFeedback").addClass("invalid-feedback campo-incompleto").css("display", "block");
                } else {
                    $("#habeasDataFeedback").removeClass("campo-incompleto").css("display", "none");
                }
    
                return false; // Campos incompletos
            }
        }
    
        // Evento al hacer clic en el botón de pago
        $(".btn-pay").click(function (e) {
            e.preventDefault();
            var button = $(this);
        
            // Quitar y agregar la clase campo-incompleto cada vez que se hace clic
            $(".campo-incompleto").removeClass("campo-incompleto");
        
            if (validarCampos()) {
                var metodoPse = $("#pse").is(":checked");
                var metodoTarjeta = $("#tarjeta").is(":checked");
        
                button.text("Espere..");
        
                // Asegurarse de que el spinner exista dentro del botón, si no existe agregarlo
                if (button.find(".spinner-border").length === 0) {
                    button.append(' <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>');
                }
                button.find(".spinner-border").removeClass("d-none");
        
                button.attr("disabled", true);
                Swal.fire({
                    title: "Espera.. <i class='fa-regular fa-hand'></i>",
                    html: '<div class="text-center"><i class="fas fa-circle-notch fa-spin fa-3x"></i><br><br><p style="font-size:18px;">Asegúrate de hacer clic en este botón después de completar el pago.</p><img src="./images/continuar.gif" width="150"></div>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    timer: 6000 // Mostrar la alerta durante 5 segundos
                }).then(() => {
                    // Después de 5 segundos, continuar con el envío del formulario
        
                    $.ajax({
                        url: "./functions/mercadopago/contar-numeros-disponibles.php",
                        type: "POST",
                        dataType: "json",
                        data: { totalNumeros: $("#totalNumeros").val() },
                        success: function (response) {
                            if (response.success) {
                                // Envío del formulario según el método de pago seleccionado
        
                                var url;
                                if (metodoPse) {
                                    url = "functions/openpay/pagar.php"; // URL para PSE
                                } else if (metodoTarjeta) {
                                    url = "functions/openpay/pay-card.php"; // URL para Tarjeta
                                }
        
                                // Establecer la acción del formulario
                                $("#formulario").attr("action", url);
        
                                // Enviar el formulario
                                $("#formulario").submit();
        
                            } else {
                                Swal.fire({
                                    title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                                    confirmButtonColor: "#000",
                                    text: response.message
                                }).then(() => {
                                    button.find(".spinner-border").addClass("d-none");
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                                text: "Error al realizar la consulta. Por favor, intenta nuevamente.",
                                confirmButtonColor: "#000"
                            }).then(() => {
                                button.find(".spinner-border").addClass("d-none");
                            });
                        }
                    });
                });
            } else {
                // Mostrar mensaje de error si los campos no están completos                
                return;
            }
        });
    
        // Evento al cambiar el checkbox de habeasData
        $("#habeasData").change(function () {
            if ($(this).prop("checked")) {
                $("#habeasDataFeedback").css("display", "none");
            } else {
                $("#habeasDataFeedback").css("display", "block");
            }
        });
    
        // Evento al cambiar el método de pago
        $("input[name='metodo_pago']").change(function () {
            if ($("#pse").prop("checked") || $("#tarjeta").prop("checked")) {
                $("#metodoPagoFeedback").css("display", "none");
            } else {
                $("#metodoPagoFeedback").css("display", "block");
            }
        });
    
    });

// Función para el envío de solicitud de consulta de números comprados
$("#formulario-consulta-numeros").on("submit", function (e) {
    e.preventDefault();
    let celular = $("#buscar-numeros-celular").val();

    // Validar la longitud del número de celular
    if (celular.length !== 10) {
        $("#buscar-numeros-celular").addClass("campo-incompleto");
        $("#campoIncompleto").css('display', 'block');
    } else {
        $("#buscar-numeros-celular").removeClass("campo-incompleto");
        $("#campoIncompleto").css('display', 'none');

        Swal.fire({
            title: "Espera.. <i class='fa-regular fa-hand'></i>",
            html: '<div class="text-center"><i class="fas fa-circle-notch fa-spin fa-3x"></i><br><br><p>Estamos procesando la información...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
        });

        $.ajax({
            url: "./functions/consultar-numeros-comprados.php",
            type: "POST",
            dataType: "json",
            data: { celular },
            success: function (response) {
                Swal.close(); // Cerrar la alerta de espera al obtener la respuesta

                if (response.success) {
                    // Aquí puedes manejar la respuesta exitosa
                    let numeros = response.data.numeros;
                    let correo = response.data.correo;
                    let numerosHtml = '';

                    // Iterar sobre cada número en los datos recibidos
                    numeros.forEach(numero => {                         
                        numerosHtml += `<span style="border: 2px solid #000; border-style: dotted; padding: 5px; margin-right: 10px; margin-bottom: 10px; color: #000; background-color: #EFB810; padding: 5px; border-radius: 8px; font-weight: bold; display: inline-block;">${numero}</span>`;
                    });

                    // Mostrar el mensaje de éxito con los números encontrados
                    if (numeros.length >= 10) {  // Mostrar botón "Enviar al correo" solo si hay más de 10 números
                        Swal.fire({
                            title: "Estos son tus números <i class='fa-solid fa-ticket'></i>",
                            html: numerosHtml,
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#000",
                            cancelButtonText: "OK",
                            confirmButtonText: "Enviar al correo", // Concatenar el correo electrónico
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: "Espera.. <i class='fa-regular fa-hand'></i>",
                                    html: '<div class="text-center"><i class="fas fa-circle-notch fa-spin fa-3x"></i><br><br><p>Estamos procesando tu información...</p></div>',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                });
                                $.ajax({
                                    url: "./functions/recordar-numeros.php",
                                    type: "POST",
                                    dataType: "json",
                                    data: { numeros: numeros, correo: correo },
                                    success: function (response) {
                                        Swal.close(); // Cerrar la alerta de espera al obtener la respuesta                                
                                        if (response.success) {
                                            Swal.fire({
                                                title: "Correo enviado. <i class='fa-solid fa-envelope'></i>",
                                                text: "Hemos enviado copia de tus números al correo registrado.",
                                                showConfirmButton: true,
                                                confirmButtonColor: "#000",
                                                confirmButtonText: "OK",
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                            });
                                        } else {
                                            Swal.fire({
                                                title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                                                text: "Error al realizar la consulta. Por favor, intenta nuevamente.",
                                                confirmButtonColor: "#000",
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                            });
                                        }

                                    },
                                    error: function () {
                                        Swal.close(); // Cerrar la alerta de espera en caso de error
                                        Swal.fire({
                                            title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                                            text: "Error al realizar la consulta. Por favor, intenta nuevamente.",
                                            confirmButtonColor: "#000",
                                            allowOutsideClick: false,
                                            allowEscapeKey: false,
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Estos son tus números <i class='fa-solid fa-ticket'></i>",
                            html: numerosHtml,
                            showConfirmButton: true,
                            confirmButtonColor: "#000",
                            confirmButtonText: "Ok, gracias",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });
                    }

                } else {
                    // Manejar el caso en que no se encontraron números vendidos
                    Swal.fire({
                        title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                        text: "No se encontraron resultados",
                        confirmButtonColor: "#000"
                    });
                }

            },
            error: function () {
                Swal.close(); // Cerrar la alerta de espera en caso de error
                Swal.fire({
                    title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                    text: "Error al realizar la consulta. Por favor, intenta nuevamente.",
                    confirmButtonColor: "#000",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }
        });

    }
});




$(".ir-arriba").click(function () {
    $("body, html").animate({
        scrollTop: "0px"
    },
        200)
}),
    $(window).scroll(function () {
        $(this).scrollTop() > 0 ? $(".ir-arriba").slideDown(300) : $(".ir-arriba").slideUp(300)
    })

//esta funcion muestra y oculta la encuesta
window.onload = function () {
    setTimeout(function () {
        var btnEncuesta = document.querySelector('.btn-encuesta');
        if (btnEncuesta) {
            btnEncuesta.style.display = 'block';
        } else {
            console.error("No se encontró el elemento con la clase 'btn-encuesta'.");
        }
    }, 1000); // Mostrar el div después de 2 segundos
};

$(function () {
    var contador = 0;
    var intervalID = setInterval(function () {
        if (contador < 1) {
            $('[data-toggle="tooltip"]').tooltip('show');
            setTimeout(function () {
                $('[data-toggle="tooltip"]').tooltip('hide');
            }, 3000);
            contador++;
        } else {
            clearInterval(intervalID); // Detener el setInterval después de 3 iteraciones
        }
    }, 5000); // Ejecutar cada 5 segundos
});

//aqui activamos las estrellas
$(document).ready(function () {
    $('#star-rating i').click(function () {
        var rating = parseInt($(this).attr('data-rating'));
        $('#star-rating i').each(function () {
            if (parseInt($(this).attr('data-rating')) <= rating) {
                $(this).removeClass('far').addClass('fas');
            } else {
                $(this).removeClass('fas').addClass('far');
            }
        });
        $('#rating-input').val(rating);
    });

    // Asegurarse de que la primera estrella esté seleccionada al cargar la página
    $('#star-rating i.selected').removeClass('far').addClass('fas');
});


$(document).ready(function () {
    $("#formulario-encuesta").submit(function (event) {
        event.preventDefault(); // Evitar que el formulario se envíe de forma convencional

        var formData = {
            rating: $("#rating-input").val(), // Obtener el valor de la calificación
            observaciones: $("#observaciones").val() // Obtener el valor de las observaciones
        };

        $(".btn-enviar-encuesta").addClass("disabled"); // Desactivar el botón de enviar
        $(".btn-enviar-encuesta .spinner-border").removeClass("d-none"); // Mostrar el spinner

        // Realizar la petición AJAX
        $.ajax({
            url: "./functions/encuesta.php", // URL a la que enviar la solicitud
            type: "POST", // Método de la solicitud
            dataType: "json", // Tipo de datos que se espera recibir
            data: formData, // Datos a enviar
            success: function (response) { // Función a ejecutar si la solicitud tiene éxito
                // Restablecer el formulario y mostrar un mensaje de éxito
                $("#formulario-encuesta")[0].reset();
                $('#exampleModal').modal('hide')
                Swal.fire({
                    icon: "info",
                    title: "¡Gracias por su calificación!",
                    text: "Tu opinión es muy importante para nosotros.",
                    confirmButtonColor: "#000"
                });
            },
            error: function () { // Función a ejecutar si hay un error en la solicitud
                // Mostrar un mensaje de error
                Swal.fire({
                    title: "Algo salió mal.. <i class='fa-solid fa-circle-exclamation'></i>",
                    text: "Error al enviar la calificación. Por favor, inténtalo nuevamente.",
                    confirmButtonColor: "#000"
                });
            },
            complete: function () { // Función a ejecutar después de que la solicitud se haya completado (ya sea con éxito o con error)
                // Habilitar el botón de enviar y ocultar el spinner
                $(".btn-enviar-encuesta").removeClass("disabled");
                $(".btn-enviar-encuesta .spinner-border").addClass("d-none");
            }
        });
    });
});


//codigo para la consulta de los datos que ya existen
$(document).ready(function () {
    $('#celular').on('input', function () {
        var celular = $(this).val();
        if (celular.length == 10) {
            $.ajax({
                type: 'POST',
                url: 'functions/consultar-cedula.php',
                data: { celular: celular },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#nombre').val(response.data.nombre).addClass('campoOculto').attr('readonly', true);
                        $('#departamento').addClass('d-none');
                        $('#ciudad').addClass('d-none');

                        var correoCompleto = response.data.correo;

                        // Función para ocultar parcialmente el correo electrónico
                        function ocultarCorreo(correo) {
                            var partes = correo.split("@");
                            var nombre = partes[0];
                            var dominio = partes[1];

                            if (nombre.length > 3) {
                                var nombreOculto = nombre.slice(0, 3) + '******';
                            } if (dominio.length > 0) {
                                dominioOculto = '*****'

                            } else {
                                var nombreOculto = nombre + '******';
                            }

                            return nombreOculto + '@' + dominioOculto;
                        }

                        // Obtener el correo oculto
                        var correoOculto = ocultarCorreo(correoCompleto);

                        // Asignar el correo oculto al campo de entrada
                        $('#correo').val(correoOculto).addClass('campoOculto').attr('readonly', true);
                        $('#correoReal').val(correoCompleto);


                        // Obtener el departamento devuelto por la consulta
                        var departamento = response.data.departamento;
                        var ciudad = response.data.ciudad;

                        // Seleccionar la opción correspondiente al departamento en el select
                        $('#usp-custom-departamento-de-residencia').val(departamento).addClass('campoOculto').attr('readonly', true).css('pointer-events', 'none').trigger('change');

                        // Llenar el campo de ciudad
                        $('#usp-custom-municipio-ciudad').val(ciudad).addClass('campoOculto').attr('readonly', true).css('pointer-events', 'none');
                    } else {
                        $('#nombre').val('').removeClass('campoOculto').attr('readonly', false);
                        $('#correo').val('').removeClass('campoOculto').attr('readonly', false);
                        $('#correoReal').val('');
                        $('#usp-custom-departamento-de-residencia').val('').removeClass('campoOculto').attr('readonly', false).css('pointer-events', 'all');
                        $('#usp-custom-municipio-ciudad').val(ciudad).removeClass('campoOculto').attr('readonly', false).css('pointer-events', 'all');
                        $('#departamento').removeClass('d-none');
                        $('#ciudad').removeClass('d-none');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    new Splide('.splide', {
        type: 'loop',
        height: '14rem',
        perPage: 1,
        perMove: 1,
        autoplay:true,
        breakpoints: {
            640: {
                height: '8rem',
                perPage: 1,
            },
        },
    }).mount();
});


// //deshabilitar la inspección
// document.addEventListener('contextmenu', function(e) {
//     e.preventDefault();
// });
// document.addEventListener('keydown', function(e) {
//     // F12
//     if (e.keyCode === 123) {
//         e.preventDefault();
//     }
//     // Ctrl+Shift+I
//     if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
//         e.preventDefault();
//     }
//     // Ctrl+Shift+J
//     if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
//         e.preventDefault();
//     }
//     // Ctrl+U
//     if (e.ctrlKey && e.keyCode === 85) {
//         e.preventDefault();
//     }
//     // Ctrl+Shift+C or Ctrl+Shift+K
//     if ((e.ctrlKey && e.shiftKey && e.keyCode === 67) || (e.ctrlKey && e.shiftKey && e.keyCode === 75)) {
//         e.preventDefault();
//     }
// });
