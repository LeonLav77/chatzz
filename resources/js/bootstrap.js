window._ = require('lodash');
window.Pusher = require('pusher-js');
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your tea ssds m to easily build robust real-time web applications.
 */

//  import Echo from "laravel-echo"

//  window.Pusher = require('pusher-js');

//  window.Echo = new Echo({
//      broadcaster: 'pusher',
//      key: 'be069965d415f82969e7',
//      wsHost: window.location.hostname,
//      wsPort: 6001,
//      disableStats: true,
//      forceTLS: false,
//      // enabledTransports: ['ws', 'wss']
//  });
import Echo from 'laravel-echo'

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '9dc4b22bdc5679cab721',
  cluster: 'eu',
  forceTLS: true
});
