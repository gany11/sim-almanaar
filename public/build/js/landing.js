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

// ===================== TV DISPLAY =====================

/*
 * Hero mega carousel: menggabungkan slide gambar hero,
 * agenda, dan saldo dalam satu carousel yang sama
 * (lihat urutan slide di tv-display.php).
 *
 * Efek fade dipertahankan agar transisi antar jenis slide
 * (gambar / agenda / saldo) tetap halus.
 */
window.initTvHero = () => {
    const swiperEl = document.querySelector('.tv-hero-swiper');

    if (!swiperEl) {
        return;
    }

    const slideCount =
        swiperEl.querySelectorAll('.swiper-slide').length;

    new Swiper('.tv-hero-swiper', {
        loop: slideCount > 1,

        effect: 'fade',

        fadeEffect: {
            crossFade: true
        },

        autoplay: {
            delay: 8000,
            disableOnInteraction: false
        },

        speed: 800,

        pagination: {
            el: '.tv-hero-pagination',
            clickable: false
        }
    });
};


window.initTvAyat = () => {
    const swiperEl = document.querySelector('.tv-ayat-swiper');

    if (!swiperEl) {
        return;
    }

    const slideCount =
        swiperEl.querySelectorAll('.swiper-slide').length;

    new Swiper('.tv-ayat-swiper', {
        loop: slideCount > 1,

        autoplay: {
            delay: 9000,
            disableOnInteraction: false
        },

        speed: 700
    });
};


/*
 * Agenda mini-swiper (khusus tampilan portrait): setiap slide
 * "pasangan agenda" di hero utama punya satu mini-swiper sendiri
 * yang menampilkan 1 agenda per slide. Bisa ada lebih dari satu
 * instance sekaligus (satu per slide pasangan agenda), jadi di-
 * loop dengan querySelectorAll, bukan querySelector tunggal.
 *
 * `observer` + `observeParents`: elemen ini disembunyikan lewat
 * class Tailwind (`hidden portrait:block`) tergantung orientasi
 * layar. Tanpa observer, Swiper bisa salah hitung lebar slide
 * kalau container-nya masih `display:none` saat pertama kali
 * di-init (mis. render awal di landscape, lalu user memutar ke
 * portrait tanpa reload halaman).
 */
window.initTvAgendaMini = () => {
    const swiperEls = document.querySelectorAll('.tv-agenda-mini-swiper');

    swiperEls.forEach((el) => {

        const slideCount = el.querySelectorAll('.swiper-slide').length;

        new Swiper(el, {
            loop: slideCount > 1,

            autoplay: {
                delay: 5000,
                disableOnInteraction: false
            },

            speed: 600,

            observer: true,
            observeParents: true
        });

    });
};


/*
 * Donasi slider (khusus tampilan portrait): bergantian antara
 * slide QR dan slide info bank. Sama seperti mini-swiper agenda,
 * pakai observer/observeParents karena container-nya juga
 * ditoggle lewat class `hidden portrait:flex`.
 */
window.initTvDonasi = () => {
    const swiperEl = document.querySelector('.tv-donasi-swiper');

    if (!swiperEl) {
        return;
    }

    const slideCount =
        swiperEl.querySelectorAll('.swiper-slide').length;

    new Swiper(swiperEl, {
        loop: slideCount > 1,

        autoplay: {
            delay: 4000,
            disableOnInteraction: false
        },

        speed: 600,

        observer: true,
        observeParents: true,

        pagination: {
            el: '.tv-donasi-pagination',
            clickable: false
        }
    });
};

// ===================== INIT ON LOAD =====================
document.addEventListener("DOMContentLoaded", () => {
    // Jalankan init khusus landing
    window.initCalendar();
    window.initCarousel();

    // TV Display
    window.initTvHero();
    window.initTvAyat();
    window.initTvAgendaMini();
    window.initTvDonasi();

    // Jalankan reinit icons agar lucide terbaca
    window.reinitIcons();
});

// ===================== ALPINE =====================
document.addEventListener('alpine:initialized', () => {
    window.reinitIcons();
});

Alpine.start();