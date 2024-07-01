import axios from 'axios';
window.axios = axios;

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
