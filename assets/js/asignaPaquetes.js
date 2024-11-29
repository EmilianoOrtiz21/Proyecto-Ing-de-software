async function asignarPaquetes() {
    const datos = { accion: 'asignarPaquetes' };
    try {
        const response = await fetch('../../public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const data = await response.json(); // Parse JSON
        console.log(data);

        // Limpiar la tabla antes de agregar nuevos datos
        const tbody = document.getElementById('asignacionPaquetes').getElementsByTagName('tbody')[0];
        tbody.innerHTML = '';

        // Iterar sobre los resultados y a�adirlos a la tabla
        data.forEach(item => {
            const row = document.createElement('tr');

            const cellMatricula = document.createElement('td');
            cellMatricula.textContent = item.matricula;

            const cellCodigo = document.createElement('td');
            cellCodigo.textContent = item.codigo_unico;

            const cellFecha = document.createElement('td');
            cellFecha.textContent = item.fecha_entrega;

            const cellPunto = document.createElement('td');
            cellPunto.textContent = item.punto_entrega;

            row.appendChild(cellMatricula);
            row.appendChild(cellCodigo);
            row.appendChild(cellFecha);
            row.appendChild(cellPunto);

            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error al enviar los datos:', error);
    }
}
