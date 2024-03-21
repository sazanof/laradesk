import Echo from 'laravel-echo'
import store from './store/index.js'

import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: [ 'ws', 'wss' ]
})
/**
 * https://stackoverflow.com/questions/62153997/binding-callbacks-on-laravel-echo-with-laravel-websockets
 */
window.Echo.connector.pusher.connection.bind('connecting', (payload) => {

    /**
     * All dependencies have been loaded and Channels is trying to connect.
     * The connection will also enter this state when it is trying to reconnect after a connection failure.
     */

    store.state.ws.connecting = true
    store.state.ws.connected = false
})

window.Echo.connector.pusher.connection.bind('connected', (payload) => {

    /**
     * The connection to Channels is open and authenticated with your app.
     */

    store.state.ws.connecting = false
    store.state.ws.connected = true
    store.state.ws.id = payload.socket_id
    //console.log(payload)
})

window.Echo.connector.pusher.connection.bind('unavailable', (payload) => {

    /**
     *  The connection is temporarily unavailable. In most cases this means that there is no internet connection.
     *  It could also mean that Channels is down, or some intermediary is blocking the connection. In this state,
     *  pusher-js will automatically retry the connection every 15 seconds.
     */

    console.log('unavailable', payload)
})

window.Echo.connector.pusher.connection.bind('failed', (payload) => {

    /**
     * Channels is not supported by the browser.
     * This implies that WebSockets are not natively available and an HTTP-based transport could not be found.
     */

    console.log('failed', payload)

})

window.Echo.connector.pusher.connection.bind('disconnected', (payload) => {

    /**
     * The Channels connection was previously connected and has now intentionally been closed
     */

    console.log('disconnected', payload)

})

window.Echo.connector.pusher.connection.bind('message', (payload) => {

    /**
     * Ping received from server
     */

    console.log('message', payload)
})
