import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import jQuery


// Import jQuery UI datepicker after jQuery is available
// import 'jquery-ui/ui/widgets/datepicker';
// import 'jquery-ui/dist/themes/ui-lightness/jquery-ui.css';

import './form-validation.js';
