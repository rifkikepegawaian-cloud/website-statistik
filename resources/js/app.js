import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import * as XLSX from 'xlsx';

// expose globals expected by our modules / inline scripts
window.Chart = Chart;
window.ChartDataLabels = ChartDataLabels;
window.XLSX = XLSX;

// import our app modules so they're bundled
import './charts';
import './upload_preview_nolib';
