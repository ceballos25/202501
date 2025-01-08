/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});


//codigo para la consulta de los datos que ya existen
$(document).ready(function() {
    $('#celular').on('input', function() {
        var celular = $(this).val();
        $.ajax({
            type: 'POST',
            url: '../functions/consultar-cedula.php',
            data: { celular: celular },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#nombre').val(response.data.nombre).attr('readonly', true);
                    $('#correo').val(response.data.correo).attr('readonly', true);
                    
                    // Obtener el departamento devuelto por la consulta
                    var departamento = response.data.departamento;
                    var ciudad = response.data.ciudad;
                    
                    // Seleccionar la opción correspondiente al departamento en el select
                    $('#usp-custom-departamento-de-residencia').val(departamento).attr('readonly', true).css('pointer-events', 'none').trigger('change');                                                                                
                    // Llenar el campo de ciudad
                    $('#usp-custom-municipio-ciudad').val(ciudad).attr('readonly', true).css('pointer-events', 'none');
                    
                } else {
                     $('#nombre').val('').attr('readonly', false);
                     $('#correo').val('').attr('readonly', false);                    
                    $('#usp-custom-departamento-de-residencia').val('').attr('readonly', false).css('pointer-events', 'all');
                    $('#usp-custom-municipio-ciudad').val('').attr('readonly', false).css('pointer-events', 'all');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

// Function to cancel a sale
function devolucion(id, correo_cliente) {
    Swal.fire({
        title: "Anular Venta",
        text: "¿Está completamente seguro de anular la venta: " + id + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, anular",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar mensaje de "Por favor, espere"
            Swal.fire({
                title: "Anulando la venta...",
                text: "Por favor, espere...",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar solicitud AJAX para eliminar la venta
            $.ajax({
                url: '../functions/anular-venta.php',
                type: 'POST',
                data: { id: id, correo_cliente: correo_cliente},
                dataType: 'json',
                success: function(response) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"

                    if (response.success) {
                        Swal.fire({
                            title: "¡Perfecto!",
                            text: "La venta "+ id +" se anuló correctamente ",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        }).then(() => {
                            // Eliminar la fila de la venta en la tabla sin recargar la página
                            var row = document.querySelector(`button[onclick="devolucion(${id}, '${correo_cliente}')"]`).closest('tr');
                            if (row) {
                                row.parentNode.removeChild(row);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message || "Algo salió mal, contacte al desarrollador.",
                            icon: "error",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"
                    console.error(xhr.responseText); // Agrega esto para más detalles
                    Swal.fire({
                        title: "Error",
                        text: "Algo salió mal, contacte al desarrollador.",
                        icon: "error",
                        allowOutsideClick: false, //Esta opción evita que le den clic fuera
                        allowEscapeKey: false //evita cerrar con la tecla esc
                    });
                }
            });
        }
    });
}


// Function to delete number 
function eliminarNumero(id, numero) {
    Swal.fire({
        title: "Eliminar número",
        text: "¿Estás seguro de eliminar el número: " + numero + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar mensaje de "Por favor, espere"
            Swal.fire({
                title: "Eliminando...",
                text: "Por favor, espere...",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            // Enviar solicitud AJAX
            $.ajax({
                url: '../functions/eliminar-numero.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"

                    if (response.success) {
                        Swal.fire({
                            title: "Perfecto",
                            text: "El número "+ numero +" se eliminó correctamente ",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        }).then(() => {
                            // Eliminar la fila de la venta en la tabla sin recargar la página
                            var row = document.querySelector(`button[onclick="eliminarNumero(${id}, '${numero}')"]`).closest('tr');
                            if (row) {
                                row.parentNode.removeChild(row);
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message || "Algo salió mal, contacte al desarrollador.",
                            icon: "error",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"
                    console.error(xhr.responseText); // Agrega esto para más detalles
                    Swal.fire({
                        title: "Error",
                        text: "Algo salió mal, contacte al desarrollador.",
                        icon: "error",
                        allowOutsideClick: false, //Esta opción evita que le den clic fuera
                        allowEscapeKey: false //evita cerrar con la tecla esc
                    });
                }
            });
        }
    });
}

//funtion edit client
$(document).ready(function() {
    // Al hacer clic en el botón de editar
    $(document).on('click', '.btnEditCliente', function() {
        // Capturar los datos de la fila
        var id = $(this).closest("tr").find("td:eq(0)").text();
         var nombre = $(this).closest("tr").find("td:eq(1)").text();
         var celular = $(this).closest("tr").find("td:eq(2)").text();
         var correo = $(this).closest("tr").find("td:eq(3)").text();
        
        // Llenar el formulario dentro del modal con los datos capturados
        $("#modalEditCliente #id").val(id);
         $("#modalEditCliente #nombre").val(nombre);
         $("#modalEditCliente #celular").val(celular);
         $("#modalEditCliente #correo").val(correo);
     });
});


//enviar fomulario y mostrar carga
$(document).ready(function() {
    // Al hacer clic en el botón de enviar del formulario
    $("#modalEditCliente").on("submit", "#formEditarCliente", function(e) {
        e.preventDefault(); // Evitar el envío predeterminado del formulario

        var btnGuardar = $("#btnGuardar");
        var spinner = btnGuardar.find('.spinner-border');

        // Mostrar el spinner cuando se hace clic en el botón
        spinner.removeClass('d-none');
        btnGuardar.prop('disabled', true); // Deshabilitar el botón mientras se procesa

        // Serializar los datos del formulario
        var formData = $(this).serialize();

        // Enviar la solicitud AJAX
        $.ajax({
            url: $(this).attr("action"), // Obtener la URL del formulario
            type: $(this).attr("method"), // Obtener el método del formulario (POST en este caso)
            data: formData, // Los datos serializados del formulario
            dataType: "json", // Esperar una respuesta JSON del servidor
            success: function(response) {
                // Manejar la respuesta del servidor
                if (response.success) {
                    // Si la actualización fue exitosa, mostrar SweetAlert2 con el mensaje de éxito
                    location.reload();
                    Swal.fire({
                        title: "Actualizando...",
                        text: "Por favor, espere...",
                        icon: "info",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        willClose: () => {
                            $('#modalEditCliente').modal('hide');
                        }
                    });
                } else {
                    // Si hubo un error, mostrar SweetAlert2 con el mensaje de error
                    Swal.fire({
                        title: 'Algo salió mal',
                        text: response.message,
                        icon: 'error',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                }
            },
            error: function(xhr, status, error) {
                // Manejar errores de la solicitud AJAX
                console.error(xhr.responseText); // Mostrar el mensaje de error en la consola
                // Mostrar SweetAlert2 con un mensaje de error genérico
                Swal.fire({
                    title: 'Algo salió mal',
                    text: 'Hubo un error al procesar la solicitud. Por favor, inténtalo de nuevo más tarde.',
                    icon: 'error'
                });
            }
        });
    });
});


// Function remember sale
function recordatorio(id, correo_cliente) {
    Swal.fire({
        title: "Reenviar Confirmación Venta",
        text: "¿Está seguro de reenviar la confirmación al correo: " + correo_cliente + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, reenviar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar mensaje de "Por favor, espere"
            Swal.fire({
                title: "Reenviando confirmación...",
                text: "Por favor, espere...",
                icon: "info",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Enviar solicitud AJAX para eliminar la venta
            $.ajax({
                url: '../functions/remember-venta.php',
                type: 'POST',
                data: { id: id, correo_cliente: correo_cliente},
                dataType: 'json',
                success: function(response) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"

                    if (response.success) {
                        Swal.fire({
                            title: "¡Perfecto!",
                            text: "Se envió la confirmación al correo: "+ correo_cliente +" ",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        }).then(() => {
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message || "Algo salió mal, contacte al desarrollador.",
                            icon: "error",
                            allowOutsideClick: false, //Esta opción evita que le den clic fuera
                            allowEscapeKey: false //evita cerrar con la tecla esc
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close(); // Cerrar el mensaje de "Por favor, espere"
                    console.error(xhr.responseText); // Agrega esto para más detalles
                    Swal.fire({
                        title: "Error",
                        text: "Algo salió mal, contacte al desarrollador.",
                        icon: "error",
                        allowOutsideClick: false, //Esta opción evita que le den clic fuera
                        allowEscapeKey: false //evita cerrar con la tecla esc
                    });
                }
            });
        }
    });
}

function descargarEXCEL() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "exportar.php", true);
    xhr.responseType = "blob";
    xhr.onload = function() {
        if (xhr.status === 200) {
            var url = window.URL.createObjectURL(xhr.response);
            var a = document.createElement("a");
            a.href = url;
            a.download = "reporte_ventas.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    };
    xhr.send();
}

function descargarClientes() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "exportar-clientes.php", true);
    xhr.responseType = "blob";
    xhr.onload = function() {
        if (xhr.status === 200) {
            var url = window.URL.createObjectURL(xhr.response);
            var a = document.createElement("a");
            a.href = url;
            a.download = "reporte_clientes.xlsx";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    };
    xhr.send();
}