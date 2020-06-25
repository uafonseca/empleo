require('./core');
core = {};
require('../css/app.css');
require('../../public/site/assets/css/bootstrap.min.css');
require('../../public/site/assets/css/fontawesome-all.min.css');
require('../../public/site/assets/css/themify-icons.css');
require('../../public/site/assets/css/et-line.css');
require('../../public/site/assets/css/bootstrap-select.min.css');
require('../../public/site/assets/css/flag.css');
require('../../public/site/assets/css/slick.css');
require('../../public/site/assets/css/owl.carousel.min.css');
require('../../public/site/assets/css/jquery.nstSlider.min.css');
require('../../public/site/assets/js/jquery.nstSlider.min');
import "datatables.net-bs/css/dataTables.bootstrap.min.css";
import 'toastr/build/toastr.css'
import 'toastr/build/toastr.min'
import 'bootstrap-fileinput/css/fileinput-rtl.css'
import 'bootstrap-fileinput/css/fileinput.css'

require('./select');

const $ = require('jquery');
global.$ = global.jQuery = $;
const toastr = require('toastr');
global.toastr = toastr;
require( 'datatables.net-bs4' );
import 'bootstrap';
import '@fortawesome/fontawesome-free';
import 'chart.js';
import 'bootstrap-select';
import 'select2'
import 'fontawesome-iconpicker';
import 'fontawesome-iconpicker/dist/css/fontawesome-iconpicker.css';
import 'symfony-collection';
import 'chart.js'
import 'jquery-confirm'
import 'jquery-confirm/css/jquery-confirm.css'
import 'bootstrap-fileinput'

require('./datatables');
require('./actions');
require('./dialogs');
require('./plugins');



