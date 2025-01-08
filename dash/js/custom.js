
$(document).ready(function() {
    $('#btn-submit').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: '../functions/contar-numeros-disponibles.php',
            type: 'POST',
            dataType: 'json',
            data: { totalNumeros: $('#oportunidades').val() },
            success: function(response) {
                if (response.success) {
                    $('#formulario').submit();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Algo salió mal',
                        confirmButtonColor: "#000",
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Algo salió mal',
                    text: 'Error al realizar la consulta. Por favor, intenta nuevamente.',
                    confirmButtonColor: "#000"
                });
            },
        });
    });
});
