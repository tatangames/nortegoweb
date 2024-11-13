<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat en Tiempo Real</title>
    <!-- Incluye la app.js para inicializar Laravel Echo y Pusher -->

</head>
<body>
<div id="chat">
    <h1>Chat en Tiempo Real</h1>

    <!-- Área de mensajes recibidos -->
    <div id="messages" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;">
        <!-- Los mensajes en tiempo real aparecerán aquí -->
    </div>

    <!-- Formulario para enviar un nuevo mensaje -->
    <form id="messageForm">
        <input type="text" id="messageInput" placeholder="Escribe un mensaje..." style="width: 80%;" required>
        <button type="submit">Enviar</button>
    </form>
</div>

@vite('resources/js/app.js')
</body>
</html>
