<!-- ===================== COMPONENT: TABLE (DATATABLE) ===================== -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<div class="bg-white rounded-2xl shadow p-4">
    <table id="datatable" class="min-w-full text-sm">
        <thead>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <th class="px-4 py-2 text-left"><?= $col ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr class="border-t">
                    <?php foreach ($row as $cell): ?>
                        <td class="px-4 py-2"><?= $cell ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
</script>
