window.addEventListener('DOMContentLoaded', () => {
    const tablesConfig = {
        'respaldo': { perPage: 10 },
        'clientes': { perPage: 10 },
        'ventasAldetalle': { perPage: 10 },
        'calificaciones': { perPage: 10 },
        'numerosVendidos': { perPage: 10 },
        'numerosDisponibles': { perPage: 100 }
    };

    Object.keys(tablesConfig).forEach(id => {
        const tableElement = document.getElementById(id);
        if (tableElement) {
            new simpleDatatables.DataTable(tableElement, {
                perPage: tablesConfig[id].perPage,
                // Puedes agregar más configuraciones opcionales aquí
            });
        }
    });
});
