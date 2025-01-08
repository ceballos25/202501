<?php
    include '../include/header.php';
    include '../../config/config_bd.php';


    $conn = obtenerConexion();


// Verificar si se pudo obtener la conexión
if (!$conn) {
    die("Error al conectar con la base de datos.");
}

// Consulta SQL
$sql = "SELECT * FROM numeros ORDER BY numeros.id ASC";

// Ejecutar la consulta
$resultado = $conn->query($sql);


    include 'plantilla.php';
?>
<!-- termina plantilla -->
 
<style>

/* Estilo para el fondo oscuro detrás del modal */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5); /* Color semi-transparente para el fondo */
}

/* Estilo para el contenido del modal */
.modal-content {
    background-color: #ffffff; /* Color de fondo del modal */
    border-radius: 10px; /* Borde redondeado del modal */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave del modal */
}

/* Estilo para el encabezado del modal */
.modal-header {
     /* Color de fondo del encabezado del modal */
    color: #000; /* Color del texto del encabezado del modal */
 }    
/* Estilo para el fondo oscuro detrás del modal */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5); /* Color semi-transparente para el fondo */
}

/* Estilo para el contenido del modal */
.modal-content {
    background-color: #f8f9fa; /* Color de fondo del modal */
    border-radius: 10px; /* Borde redondeado del modal */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra suave del modal */
}

/* Estilo para el encabezado del modal */
.modal-header {
    /* Color de fondo del encabezado del modal */
    color: #ffffff; /* Color del texto del encabezado del modal */
    border-top-left-radius: 10px; /* Borde redondeado superior izquierdo del encabezado */
    border-top-right-radius: 10px; /* Borde redondeado superior derecho del encabezado */
    padding: 1rem; /* Espaciado interno del encabezado del modal */
}

/* Estilo para el título del modal */
.modal-title {
    font-size: 1.5rem; /* Tamaño de fuente del título del modal */    
    color: #000;
}

/* Estilo para el cuerpo del modal */
.modal-body {
    padding: 1rem; /* Espaciado interno del cuerpo del modal */
}

/* Estilo para los números generados en el modal */
#listaNumeros .row {
    margin-bottom: 10px; /* Espaciado entre las filas de números */
    display: flex; /* Usar flexbox para alinear los números en una fila */
    justify-content: space-between; /* Distribuir uniformemente los elementos en la fila */
}

#listaNumeros .col {
    background-color: #000; /* Color de fondo de las columnas de números */
    color: #ffffff; /* Color del texto de las columnas de números */
    border-radius: 20px; /* Borde redondeado de las columnas de números */
    padding: 3px; /* Espaciado interno de las columnas de números */
    text-align: center; /* Alineación del texto en las columnas de números */
    flex: 1; /* Hacer que las columnas ocupen el mismo espacio */
    margin-right: 5px; /* Margen derecho entre las columnas */
}

#listaNumeros .col:last-child {
    margin-right: 0; /* Eliminar el margen derecho del último elemento en la fila */
}
  
</style>
<!-- inicia contenido -->
<div id="layoutSidenav_content">
    <main>
        <h1 class="mt-4 mb-4 mx-4 d-flex justify-content-center">Números Disponibles</h1>
        <div class="container-fluid px-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Agregar número</button>
                            <!-- Botón para abrir el modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#numerosModal">
                            <i class="bi bi-zoom-in"></i>
                            </button>
                </div>
                    <div class="card-body">
                    <table id="numerosDisponibles" class="table-striped">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultado->num_rows > 0) {
                                // Iterar sobre los resultados usando foreach
                                foreach ($resultado as $fila) { ?>
                                    <tr>
                                        <td><?php echo $fila["numero"]; ?></td>
                                        <td>
                                        <button type="button" onclick="eliminarNumero(<?php echo $fila['id']; ?>, '<?php echo $fila['numero']; ?>')" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan='4'>No se encontraron resultados.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal agregar números premiados -->


            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ingresar número premiado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="../functions/agregar-premiado.php">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Número:</label>
                        <input type="number" requiered class="form-control" id="numero_premiado" name="numero_premiado" required>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
                </div>
                </form>
            </div>
            </div>
            
<!-- Modal para mostrar los números -->
<div class="modal fade" id="numerosModal" tabindex="-1" aria-labelledby="numerosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="numerosModalLabel">Números disponibles</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Aquí se mostrarán los números generados -->
        <ul id="listaNumeros" class="list-group"></ul>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Función para generar y mostrar los números al abrir el modal
    $('#numerosModal').on('show.bs.modal', function(event) {
      // Limpiar el contenido del modal
      $('#listaNumeros').empty();

      // Obtener los números de la tabla
      var numeros = obtenerNumerosDeTabla();

      // Dividir los números en grupos de 10 para mostrar en filas
      var grupos = [];
      for (var i = 0; i < numeros.length; i += 8) {
        grupos.push(numeros.slice(i, i + 8));
      }

      // Generar HTML para mostrar los números en filas y columnas
      grupos.forEach(function(grupo) {
        var filaHtml = '<div class="row">';
        grupo.forEach(function(numero) {
          filaHtml += `<div class="col">${numero}</div>`;
        });
        filaHtml += '</div>';
        $('#listaNumeros').append(filaHtml);
      });
    });

    // Función para obtener los números de la tabla
    function obtenerNumerosDeTabla() {
      var numeros = [];

      // Iterar sobre las filas de la tabla
      $('#numerosDisponibles tbody tr').each(function() {
        var numero = $(this).find('td:first').text().trim();
        if (numero !== '') {
          numeros.push(numero);
        }
      });

      return numeros;
    }
  });
</script>

    </main>



         
<!-- termina contenido -->

<!-- inicia footer -->
<?php
include '../include/footer.php';
?>
<!-- termina footer -->