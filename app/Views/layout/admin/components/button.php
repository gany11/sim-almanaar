<!-- ===================== COMPONENT: BUTTON (app/Views/components/button.php) ===================== -->
<?php
    $type = $type ?? 'button'; // button | submit | link
    $variant = $variant ?? 'primary';
    $icon = $icon ?? null;
    $text = $text ?? '';
    $href = $href ?? '#';

    $baseClass = "inline-flex items-center gap-2 px-4 py-2 rounded text-sm font-medium transition";

    $variants = [
        'primary' => 'bg-blue-500 hover:bg-blue-600 text-white',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white',
        'danger' => 'bg-red-500 hover:bg-red-600 text-white',
        'success' => 'bg-green-500 hover:bg-green-600 text-white'
    ];

    $class = $baseClass . ' ' . ($variants[$variant] ?? $variants['primary']);
?>

<?php if ($type == 'link'): ?>
    <a href="<?= $href ?>" class="<?= $class ?>">
        <?php if ($icon): ?> <i data-lucide="<?= $icon ?>"></i> <?php endif; ?>
        <?= $text ?>
    </a>
<?php else: ?>
    <button type="<?= $type ?>" class="<?= $class ?>">
        <?php if ($icon): ?> <i data-lucide="<?= $icon ?>"></i> <?php endif; ?>
        <?= $text ?>
    </button>
<?php endif; ?>