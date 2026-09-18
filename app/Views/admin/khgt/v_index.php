<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto pb-10">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-4 md:px-0">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kalender Hijriah Global Tunggal (KHGT)</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan sinkronisasi penanggalan Masehi dan Hijriah melalui API resmi.</p>
        </div>
        <?php if (in_array(session()->get('id_peran'), [1])): ?>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Tombol Sinkronisasi AJAX -->
                <button type="button" id="btn-sync"
                    class="flex items-center gap-2 px-5 py-2.5 bg-white border border-emerald-200 text-emerald-600 rounded-xl font-bold text-sm hover:bg-emerald-50 transition-all active:scale-95 shadow-sm shadow-emerald-50 cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-4 h-4" id="sync-icon"></i> 
                    <span id="sync-text">Sinkronisasi API</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <!-- Container FullCalendar KHGT -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 mx-4 md:mx-0">
        <div id="calendar-loading" class="hidden text-center py-4 text-blue-600 font-bold animate-pulse">
            Memperbarui Kalender KHGT...
        </div>
        <div id="khgt-calendar" class="min-h-[600px]"></div>
    </div>

    <!-- Modal Detail KHGT saat Event diklik -->
    <div x-data="{ 
            open: false, 
            masehi: '', 
            hijriah: '' 
        }" 
        @open-khgt.window="
            open = true; 
            masehi = $event.detail.masehi;
            hijriah = $event.detail.hijriah;
            setTimeout(() => { if(typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
        ">
        
        <div x-show="open" 
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition.opacity
            style="display: none;">
            
            <div class="bg-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl" @click.away="open = false">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase rounded-full">Detail Tanggal</span>
                    <button @click="open = false" class="text-gray-400 hover:text-red-500 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
        
                <div class="p-6 space-y-4 text-center">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Hijriah</p>
                        <h3 class="text-xl font-bold text-gray-900" x-text="hijriah"></h3>
                    </div>
        
                    <div class="pt-2 border-t border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Masehi</p>
                        <p class="text-sm font-semibold text-gray-700" x-text="masehi"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const $ = window.jQuery;

    // Handler Tombol Sinkronisasi AJAX
    $('#btn-sync').on('click', function() {
        const btn = $(this);
        const icon = $('#sync-icon');
        const text = $('#sync-text');

        // Ubah tombol ke mode loading
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
        icon.addClass('animate-spin');
        text.text('Menyinkronkan...');

        $.ajax({
            url: "<?= base_url('admin/khgt/sync') ?>",
            type: "GET", // Atau POST jika route diatur POST
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sinkronisasi Berhasil!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan saat sinkronisasi.', 'error');
                }
                refreshKhgtCalendar();
            },
            error: function(xhr) {
                let errMsg = 'Gagal terhubung ke server API.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errMsg, 'error');
                refreshKhgtCalendar();
            },
            complete: function() {
                // Kembalikan tombol ke kondisi normal
                btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed');
                icon.removeClass('animate-spin');
                text.text('Sinkronisasi API');
                if (window.reinitIcons) window.reinitIcons();
            }
        });
    });

    function refreshKhgtCalendar() {
        if (window.khgtCalendarInstance) {
            window.khgtCalendarInstance.refetchEvents();
        } else if (typeof FullCalendar !== 'undefined') {
            const calendarEl = document.getElementById('khgt-calendar');
            const cal = FullCalendar.Calendar.getCalendar(calendarEl);
            if (cal) cal.refetchEvents();
        }
    }
});
</script>
<?= $this->endSection() ?>