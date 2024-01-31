import _ from 'lodash';

import $ from "jquery";
import '~bootstrap';
import Chart from 'chart.js/auto';
import bst from 'bootstrap5-toggle';
import {Modal, Toast} from "bootstrap";
import dataTables from 'datatables.net-bs5';
import { TempusDominus } from '@eonasdan/tempus-dominus';
import jscolor from "@eastdesire/jscolor";

//import {Popover, Dropdown, Sidebar, Navigation} from "@coreui/coreui";


/*import 'jszip' ;
import 'pdfmake' ;
import 'datatables.net-autofill-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.js';
import 'datatables.net-buttons/js/buttons.html5.js';
import 'datatables.net-buttons/js/buttons.print.js';
import 'datatables.net-datetime';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-responsive-bs5';*/

try {
    window._ = _;
    window.$ = window.jQuery = $;
    window.Chart = Chart;
    window.dt = dataTables;
    window.Modal = Modal;
    window.Toast = Toast;
    window.TempusDominus = TempusDominus;
    window.bootstrapToggle = bst;
    window.jscolor = jscolor;
   /* window.jscolor.presets.default = {
        width: 200,
        height: 200,
        position: 'right',
        previewPosition: 'left',
        backgroundColor: '#f3f3f3',
        borderColor: '#bbbbbb',
        controlBorderColor: '#bbbbbb',
        palette: [
            '#000000', '#7d7d7d', '#870014', '#ec1c23', '#ff7e26',
            '#fef100', '#22b14b', '#00a1e7', '#3f47cc', '#a349a4',
            '#ffffff', '#c3c3c3', '#b87957', '#feaec9', '#ffc80d',
            '#eee3af', '#b5e61d', '#99d9ea', '#7092be', '#c8bfe7',
        ],
        paletteCols: 10,
        //hideOnPaletteClick: true,
    }*/
    /*window.Popover = Popover;
    window.Dropdown = Dropdown;
    window.Sidebar = Sidebar;
    window.Navigation = Navigation;*/

} catch (e) {
    console.log(e)
}

import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
