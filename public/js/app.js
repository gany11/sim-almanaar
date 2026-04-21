// ===================== IMPORT LIBRARY =====================
import Alpine from 'alpinejs'
import $ from 'jquery'
import DataTable from 'datatables.net-dt'
import 'datatables.net-responsive-dt'
import Swal from 'sweetalert2'
import * as lucide from 'lucide'
import Chart from 'chart.js/auto'
import select2 from 'select2'

// ===================== IMPORT CSS LOKAL =====================
// Vite akan membungkus file-file CSS ini ke dalam build final Anda
import 'datatables.net-dt/css/dataTables.dataTables.css'; 
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'select2/dist/css/select2.css';
import 'sweetalert2/dist/sweetalert2.min.css';

// ===================== GLOBAL EXPOSE =====================
window.$ = window.jQuery = $
window.Swal = Swal
window.Chart = Chart
window.DataTable = DataTable
window.lucide = lucide
window.Alpine = Alpine

// ===================== INIT FUNCTIONS =====================
window.reinitIcons = () => {
    lucide.createIcons({
        icons: lucide.icons 
    });
};

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
    // Jalankan Select2
    select2($);
    
    $('.select2-dynamic').select2({
        tags: true,
        width: '100%'
    });

    // Render icons
    window.reinitIcons();
});

// ===================== ALPINE =====================
document.addEventListener('alpine:initialized', () => {
    window.reinitIcons();
});

Alpine.start();