<!-- Usage: include this file and pass $headers (array) and $rows (array of arrays) -->
<table class="min-w-full bg-white border mb-6">
  <thead>
    <tr>
      <?php foreach ($headers as $header): ?>
        <th class="py-2 px-4 border-b text-left bg-gray-50 font-semibold"> <?= htmlspecialchars($header) ?> </th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $row): ?>
      <tr>
        <?php foreach ($row as $cell): ?>
          <td class="py-2 px-4 border-b"> <?= htmlspecialchars($cell) ?> </td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
