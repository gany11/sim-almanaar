// ===================== IMPORT LIBRARY =====================
import Alpine from 'alpinejs'
import $ from 'jquery'
import DataTable from 'datatables.net-dt'
import 'datatables.net-responsive-dt'
import Swal from 'sweetalert2'
import * as lucide from 'lucide'
import Chart from 'chart.js/auto'
import select2 from 'select2'
import tinymce from 'tinymce'
import flatpickr from "flatpickr";


// ===================== IMPORT CSS LOKAL =====================
import 'datatables.net-dt/css/dataTables.dataTables.css'; 
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'select2/dist/css/select2.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import "flatpickr/dist/flatpickr.css";
import { Indonesian } from "flatpickr/dist/l10n/id.js";

// ===================== GLOBAL EXPOSE =====================
window.$ = window.jQuery = $
window.Swal = Swal
window.Chart = Chart
window.DataTable = DataTable
window.lucide = lucide
window.Alpine = Alpine
window.tinymce = tinymce
window.flatpickr = flatpickr;
window.flatpickr_id = Indonesian;
flatpickr.localize(Indonesian);


// ===================== INIT FUNCTIONS =====================
window.reinitIcons = () => {
    lucide.createIcons({
        icons: lucide.icons 
    });
};

window.initEditor = () => {
    if (window.tinymce) {
        tinymce.remove('.editor'); 
        
        tinymce.init({
            selector: '.editor',
            license_key: 'gpl',
            base_url: '/assets/vendor/tinymce', 
            suffix: '.min',
            height: 400,
            menubar: false,
            plugins: 'lists link image table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | code',
            content_style: 'body { font-family:Inter,Arial,sans-serif; font-size:14px }',
            skin: 'oxide',
            content_css: 'default',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }
}

window.confirmLogout = function() {
    Swal.fire({
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            window.location.href = '/admin/logout'
        }
    })
}

// ===================== INIT ON LOAD =====================
document.addEventListener("DOMContentLoaded", () => {
    select2($);

    $('.select2-dynamic').select2({
        tags: true,
        width: '100%'
    });

    window.initEditor();

    window.reinitIcons();
});

// ===================== ALPINE =====================
document.addEventListener('alpine:initialized', () => {
    window.reinitIcons();
});

Alpine.start();