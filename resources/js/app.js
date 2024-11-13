import './bootstrap';


window.Echo.channel('location-updates') // Canal público
    .listen('LocationUpdated', (event) => {
        console.log('Evento recibido:', event);
        console.log('-----------');

    });

