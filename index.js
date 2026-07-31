// ============================================================================
// index.js
// Servidor Express que sirve una pagina de mantenimiento estatica y notifica
// eventos del servidor / acciones del usuario mediante Telegram.
//
// Variables de entorno requeridas (las unicas que se deben configurar):
//   - TELEGRAM_TOKEN    -> Token del bot de Telegram
//   - TELEGRAM_CHAT_ID  -> ID del chat/canal donde se enviaran los mensajes
//
// Compatible con Node.js 22+ (usa fetch nativo, sin dependencias extra).
// ============================================================================

'use strict';

const express = require('express');
const path = require('path');

// ---------------------------------------------------------------------------
// Configuracion basica
// ---------------------------------------------------------------------------
const app = express();
const PORT = process.env.PORT || 3000;

const TELEGRAM_TOKEN = process.env.TELEGRAM_TOKEN;
const TELEGRAM_CHAT_ID = process.env.TELEGRAM_CHAT_ID;

// ---------------------------------------------------------------------------
// Middlewares
// ---------------------------------------------------------------------------
// Permite recibir JSON en el body de las peticiones (ej: formulario de contacto)
app.use(express.json());

// Sirve archivos estaticos (index.html, imagenes, css/js embebidos, etc.)
app.use(express.static(path.join(__dirname)));

// ---------------------------------------------------------------------------
// Utilidad: envio de mensajes a Telegram
// ---------------------------------------------------------------------------
/**
 * Envia un mensaje de texto al chat configurado en TELEGRAM_CHAT_ID
 * usando el bot definido por TELEGRAM_TOKEN.
 *
 * Si las variables de entorno no estan configuradas, la funcion no falla:
 * simplemente registra una advertencia en consola y no envia nada.
 *
 * @param {string} message - Texto a enviar (soporta HTML basico de Telegram)
 * @returns {Promise<void>}
 */
async function sendTelegramMessage(message) {
  if (!TELEGRAM_TOKEN || !TELEGRAM_CHAT_ID) {
    console.warn(
      '[Telegram] Variables TELEGRAM_TOKEN / TELEGRAM_CHAT_ID no configuradas. ' +
      'Se omite el envio del mensaje.'
    );
    return;
  }

  const url = `https://api.telegram.org/bot${TELEGRAM_TOKEN}/sendMessage`;

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        chat_id: TELEGRAM_CHAT_ID,
        text: message,
        parse_mode: 'HTML'
      })
    });

    if (!response.ok) {
      const errorBody = await response.text();
      console.error(
        `[Telegram] Error al enviar mensaje. Status: ${response.status}. Detalle: ${errorBody}`
      );
      return;
    }

    console.log('[Telegram] Mensaje enviado correctamente.');
  } catch (error) {
    console.error('[Telegram] Error de red al enviar mensaje:', error.message);
  }
}

// ---------------------------------------------------------------------------
// Rutas
// ---------------------------------------------------------------------------

/**
 * Ruta principal: sirve la pagina de mantenimiento.
 * Notifica a Telegram cada vez que alguien la visita.
 */
app.get('/', async (req, res) => {
  // Obtener informacion del visitante
  const ip = req.ip || req.connection.remoteAddress;
  const userAgent = req.get('user-agent') || 'Desconocido';
  
  // Notificar a Telegram
  const telegramMessage =
    '👀 <b>Mirones entrando</b>\n\n' +
    `<b>IP:</b> ${escapeHtml(ip)}\n` +
    `<b>User Agent:</b> ${escapeHtml(userAgent)}`;
  
  await sendTelegramMessage(telegramMessage);
  
  res.sendFile(path.join(__dirname, 'index.html'));
});

/**
 * Ruta para health check (util en Render para verificar que el servicio
 * esta activo). No envia notificaciones a Telegram.
 */
app.get('/health', (req, res) => {
  res.status(200).json({ status: 'ok', uptime: process.uptime() });
});

/**
 * Endpoint de contacto: recibe una accion iniciada por el usuario
 * (por ejemplo, el envio de un formulario) y notifica a Telegram.
 * No se procesa ni almacena ningun dato de visitantes que no
 * hayan enviado explicitamente este formulario.
 */
app.post('/api/contact', async (req, res) => {
  try {
    const { name, email, message } = req.body || {};

    // Validacion basica de los datos recibidos
    if (!name || !email || !message) {
      return res.status(400).json({
        ok: false,
        error: 'Los campos "name", "email" y "message" son obligatorios.'
      });
    }

    const telegramMessage =
      '📩 <b>Nuevo mensaje de contacto</b>\n\n' +
      `<b>Nombre:</b> ${escapeHtml(name)}\n` +
      `<b>Email:</b> ${escapeHtml(email)}\n` +
      `<b>Mensaje:</b> ${escapeHtml(message)}`;

    await sendTelegramMessage(telegramMessage);

    return res.status(200).json({
      ok: true,
      message: 'Mensaje enviado correctamente.'
    });
  } catch (error) {
    console.error('[POST /api/contact] Error inesperado:', error.message);
    return res.status(500).json({
      ok: false,
      error: 'Ocurrio un error interno al procesar la solicitud.'
    });
  }
});

// ---------------------------------------------------------------------------
// Utilidad: escape basico de HTML para evitar inyeccion en mensajes Telegram
// ---------------------------------------------------------------------------
function escapeHtml(text) {
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

// ---------------------------------------------------------------------------
// Manejo de rutas no encontradas (404)
// ---------------------------------------------------------------------------
app.use((req, res) => {
  res.status(404).sendFile(path.join(__dirname, 'index.html'));
});

// ---------------------------------------------------------------------------
// Manejo global de errores
// ---------------------------------------------------------------------------
app.use((err, req, res, next) => {
  console.error('[Error global]', err.message);
  res.status(500).json({ ok: false, error: 'Error interno del servidor.' });
});

// ---------------------------------------------------------------------------
// Inicio del servidor
// ---------------------------------------------------------------------------
app.listen(PORT, async () => {
  console.log(`Servidor de mantenimiento escuchando en el puerto ${PORT}`);

  // Notifica por Telegram que el servidor se ha iniciado correctamente
  await sendTelegramMessage(
    `🚀 <b>Servidor iniciado</b>\nLa pagina de mantenimiento esta activa en el puerto ${PORT}.`
  );
});

// ---------------------------------------------------------------------------
// Manejo de cierre del proceso (SIGTERM/SIGINT) - notifica apagado
// ---------------------------------------------------------------------------
async function handleShutdown(signal) {
  console.log(`Senal recibida: ${signal}. Cerrando servidor...`);
  await sendTelegramMessage(`🛑 <b>Servidor detenido</b>\nSenal recibida: ${signal}.`);
  process.exit(0);
}

process.on('SIGTERM', () => handleShutdown('SIGTERM'));
process.on('SIGINT', () => handleShutdown('SIGINT'));
