const formularioConsultarEstado = document.getElementById('form_consultar_estado');
const formularioAsignarHorario = document.getElementById('form_asignar_horario');
const formularioListarPaquetes = document.getElementById('form_listar_paquetes');
const mensaje = document.getElementById('mensaje_error');
const datosPaquete = document.getElementById('datos_Paquete');
const tablaPaquetes = document.getElementById('tabla_paquetes').getElementsByTagName('tbody')[0];
const urlServidor = 'index.php';
const BUSCAR_PAQUETE = 'obtenerEstadoEntrega';
const ASIGNAR_HORARIO = 'asignarHorario';
const LISTAR_PAQUETES = 'listarPaquetes';

// Función para mostrar mensajes de error o éxito
function mostrarMensaje(mensajeTexto) {
    mensaje.textContent = mensajeTexto;
}

// Función para hacer la solicitud Fetch
function hacerFetch(url, datos) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(respuesta => respuesta.json())
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje('Ocurrió un error al comunicar con el servidor.');
    });
}

// Función para convertir hora en formato HH:MM a minutos
function convertirAMinutos(hora) {
    const [horas, minutos] = hora.split(':');
    return parseInt(horas) * 60 + parseInt(minutos);
}

// Manejador para el formulario de consultar estado
formularioConsultarEstado.addEventListener('submit', function(evento) {
    evento.preventDefault();
    const codigoPaquete = document.getElementById('codigo_paquete').value;
    const datos = {
        accion: BUSCAR_PAQUETE,
        codigo: codigoPaquete
    };

    hacerFetch(urlServidor, datos)
        .then(datos => {
            console.log(datos);
            if (datos.error) {
                mostrarMensaje(datos.error);  // Mostrar error si se recibe del backend
            } else {
                if (datos.estado_entrega) {
                    formularioAsignarHorario.style.display = datos.estado_entrega.toLowerCase().includes("pendiente") ? 'block' : 'none';
                }
                // Limpiar datos previos
                datosPaquete.innerHTML = '';
                const elementos = [
                    `Estado de Entrega: ${datos.estado_entrega}`,
                    `Código Único: ${datos.codigo_unico}`,
                    `Franja Horaria Mínima: ${datos.franja_horaria_min || 'No asignado'}`,
                    `Franja Horaria Máxima: ${datos.franja_horaria_max || 'No asignado'}`
                ];
                elementos.forEach(elemento => {
                    const li = document.createElement('li');
                    li.textContent = elemento;
                    datosPaquete.appendChild(li);
                });
            }
        });
});

// Manejador para el formulario de asignar horario
formularioAsignarHorario.addEventListener('submit', function(evento) {
    evento.preventDefault();
    const franjaHorariaMin = document.getElementById('franja_horaria_min').value;
    const franjaHorariaMax = document.getElementById('franja_horaria_max').value;

    // Verificación de horarios
    if (!franjaHorariaMin || !franjaHorariaMax) {
        mostrarMensaje('Por favor, seleccione ambos horarios.');
        return;
    }

    const minutosMin = convertirAMinutos(franjaHorariaMin);
    const minutosMax = convertirAMinutos(franjaHorariaMax);

    if (minutosMin >= minutosMax) {
        mostrarMensaje("El horario mínimo debe ser menor que el horario máximo.");
    } else {
        const horario = {
            accion: ASIGNAR_HORARIO,
            franja_horaria_min: franjaHorariaMin,
            franja_horaria_max: franjaHorariaMax
        };

        hacerFetch(urlServidor, horario)
            .then(datos => {
                if (datos.error) {
                    mostrarMensaje(datos.error);  // Mostrar error si se recibe del backend
                } else {
                    mostrarMensaje(datos.resultado || "Horario asignado con éxito.");
                }
            });
    }
});

// Manejador para el formulario de listar paquetes
formularioListarPaquetes.addEventListener('submit', function(evento) {
    evento.preventDefault();

    const datos = {
        accion: LISTAR_PAQUETES
    };

    hacerFetch(urlServidor, datos)
        .then(datos => {
            // Limpiamos la tabla antes de agregar nuevas filas
            tablaPaquetes.innerHTML = '';
            if (datos.error) {
                mostrarMensaje(datos.error);  // Mostrar error si no hay paquetes
            } else {
                // Agregar filas a la tabla
                datos.forEach(paquete => {
                    const fila = tablaPaquetes.insertRow();
                    fila.insertCell(0).textContent = paquete.codigo_unico;
                    fila.insertCell(1).textContent = paquete.nombre_destinatario;
                    fila.insertCell(2).textContent = paquete.franja_horaria_min || 'No asignado';
                    fila.insertCell(3).textContent = paquete.franja_horaria_max || 'No asignado';
                });
            }
        });
});
