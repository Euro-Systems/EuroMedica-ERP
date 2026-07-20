// Importa la biblioteca Axios para realizar peticiones HTTP (GET, POST, etc.)
import axios from 'axios';

// Asigna Axios al objeto global 'window' para que esté disponible en cualquier parte del proyecto sin necesidad de volver a importarlo
window.axios = axios;

// Configura un encabezado (header) por defecto para todas las peticiones que se realicen
// 'X-Requested-With' configurado como 'XMLHttpRequest' le indica al servidor (como Laravel) 
// que la petición fue hecha mediante AJAX/JavaScript y no por una recarga tradicional del navegador.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';