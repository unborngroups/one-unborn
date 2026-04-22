import axios from 'axios';

// Global axios instance for all HTTP calls
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Note: We intentionally do NOT initialize Laravel Echo or Pusher here.
// The internal chat widget is built to work over plain HTTP polling.
// If you later want realtime WebSocket updates, you can reintroduce
// Echo/Pusher setup and install the corresponding npm packages.
