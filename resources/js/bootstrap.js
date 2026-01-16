import _ from 'lodash';
import axios from 'axios';

window._ = _;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * The Laravel Echo setup is commented out because it doesn't work out of the box
 * as you will need to setup a WebSocket service to be able to use it. Once that's
 * handled, you may uncomment the following lines to start receiving WebSockets.
 */

// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
//
// window.Pusher = Pusher;
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     forceTLS: import.meta.env.VITE_PUSHER_APP_USE_SSL === 'true',
//     disableStats: true,
//     wsHost: import.meta.env.VITE_PUSHER_APP_HOST,
//     wsPort: import.meta.env.VITE_PUSHER_APP_PORT || null,
// });
