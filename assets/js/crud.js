        // Cargar los estados desde el servidor al cargar la página
        document.addEventListener('DOMContentLoaded', cargarEstados);

        // Función para cargar los estados en el select
        async function cargarEstados() {
            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ accion: 'cargarEstados' })
                });
                const data = await response.json();
                console.log(data);
                const estadoSelect = document.getElementById('estadoConductor');
                data.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.id;
                    option.text = estado.descripcion;
                    estadoSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar los estados:', error);
            }
        }

async function asignarPaquetes(){
    const datos = {accion: 'asignarPaquetes'};
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });
    } catch (error) {
        console.error('Error al enviar los datos:', error);
    }
}
        // Función para enviar los datos del formulario al servidor
async function enviarDatos(accion) {
    const estadoConductor = document.getElementById('estadoConductor').value;
    // Validar que el estado sea seleccionado
    if (estadoConductor === "") {
        alert("Por favor, selecciona un estado válido para el conductor.");
        return;
    }

    const datos = {
        nombreConductor: document.getElementById('nombreConductor').value,
        correoConductor: document.getElementById('correoConductor').value,
        telefonoConductor: document.getElementById('telefonoConductor').value,
        usuarioConductor: document.getElementById('usuarioConductor').value,
        contrasenaConductor: document.getElementById('contrasenaConductor').value,
        matriculaConductor: document.getElementById('matriculaConductor').value,
        estadoConductor: estadoConductor,
    };
    const solicitud = {
        datos:datos,
        accion:accion
    }
    try {
        const response = await fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(solicitud)
        });
        const resultado = await response.json();
        console.log(resultado);
        alert(resultado.mensaje || resultado.error);
    } catch (error) {
        console.error('Error al enviar los datos:', error);
    }
}
