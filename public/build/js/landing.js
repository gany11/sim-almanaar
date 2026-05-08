// ===================== IMPORT LIBRARY =====================
import Alpine from 'alpinejs'
import $ from 'jquery'
// import DataTable from 'datatables.net-dt'
// import 'datatables.net-responsive-dt'
import Swal from 'sweetalert2'
import * as lucide from 'lucide'
import Chart from 'chart.js/auto'
// Library Tambahan Khusus Landing
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import Swiper from 'swiper/bundle'

// ===================== IMPORT CSS LOKAL =====================
// import 'datatables.net-dt/css/dataTables.dataTables.css'; 
// import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'swiper/css/bundle';

// ===================== GLOBAL EXPOSE =====================
window.$ = window.jQuery = $
window.Swal = Swal
window.Chart = Chart
// window.DataTable = DataTable
window.lucide = lucide
window.Alpine = Alpine

// ===================== INIT FUNCTIONS =====================
window.reinitIcons = () => {
    lucide.createIcons({
        icons: lucide.icons 
    });
};

window.initCalendar = () => {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            locale: 'id',
            eventSources: [{
                url: '/api/agenda',
                method: 'GET',
                fetchOptions: {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            }],
            eventClick: function(info) {
                const props = info.event.extendedProps;
                
                let timeStr = info.event.start.toLocaleDateString('id-ID', { dateStyle: 'full' });
                let clockStr = (props.ket_mulai || info.event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                
                if (info.event.end || props.ket_selesai) {
                    clockStr += ' s/d ' + (props.ket_selesai || info.event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                }

                let speakerHtml = 'Tidak ada pengisi.';
                if (props.pengisi && props.pengisi.length > 0) {
                    speakerHtml = props.pengisi.map(p => `<div><span class='text-blue-600'>${p.peran}:</span> ${p.nama}</div>`).join('');
                }

                window.dispatchEvent(new CustomEvent('open-agenda', {
                    detail: {
                        category: props.nama_kategori,
                        theme: props.tema || 'Kegiatan Rutin',
                        title: props.judul,
                        time: timeStr + ' (' + clockStr + ')',
                        loc: props.tempat || 'Masjid Al-Manaar',
                        speaker: speakerHtml,
                        desc: props.deskripsi || '<em class="text-gray-400">Tidak ada deskripsi tambahan.</em>'
                    }
                }));
            }
        });
        calendar.render();
    }
}

window.initCarousel = () => {
    const swiperEl = document.querySelector('.hero-swiper');
    if (swiperEl) {
        new Swiper('.hero-swiper', {
            loop: true,
            autoplay: { delay: 5000 },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }
}

// ===================== INIT ON LOAD =====================
document.addEventListener("DOMContentLoaded", () => {
    // Jalankan init khusus landing
    window.initCalendar();
    window.initCarousel();

    // Jalankan reinit icons agar lucide terbaca
    window.reinitIcons();
});

// ===================== ALPINE =====================
document.addEventListener('alpine:initialized', () => {
    window.reinitIcons();
});

Alpine.start();