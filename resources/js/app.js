import './bootstrap';


/*window.Echo.channel('location-updates') // Canal público
    .listen('LocationUpdated', (event) => {
        console.log('Evento recibido:', event);
        console.log('-----------');

    });
*/


// Escuchar los usuarios conectados al canal de presencia
window.Echo.join('presence.chat')
    .here(users => {
        console.log("Usuarios conectados:", users);
    })
    .joining(user => {
        console.log(`${user.name} (${user.role}) se ha unido`);
    })
    .leaving(user => {
        console.log(`${user.name} (${user.role}) se ha desconectado`);
    });

// Escuchar mensajes del canal
window.Echo.channel('presence.chat')
    .listen('ChatMessage', (e) => {
        console.log("Nuevo mensaje:", e.message);
    });
