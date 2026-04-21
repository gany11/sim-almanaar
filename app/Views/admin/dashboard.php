<?= $this->extend('layout/admin/main') ?>

<?= $this->section('content') ?>

<h2 class="text-2xl font-bold mb-4">Dashboard</h2>

<!-- BUTTON -->
<div class="mb-4">
    <?= view('layout/admin/components/button', [
        'type' => 'link',
        'text' => 'Tambah User',
        'icon' => 'plus',
        'href' => base_url('users/create'),
        'variant' => 'primary'
    ]) ?>
</div>

<!-- TABLE -->
<?= view('layout/admin/components/table', [
    'columns' => ['Nama', 'Email', 'Aksi'],
    'rows' => [
        [
            'John Doe',
            'john@mail.com',
            '
            <button onclick="confirmDelete(\''.base_url('users/delete/1').'\')" 
                class="text-red-500">Hapus</button>
            '
        ]
    ]
]) ?>

<!-- CHART -->
<div class="bg-white p-4 rounded-2xl shadow mt-6">
    <canvas id="myChart"></canvas>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar'],
            datasets: [{
                label: 'Transaksi',
                data: [10, 20, 15],
            }]
        }
    });
});
</script>

<?= $this->endSection() ?>