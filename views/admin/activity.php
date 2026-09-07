<?php
/**
 * Islem gunlugu.  DOCS.md 9.11
 *
 * @var array $rows
 * @var int   $total
 * @var int   $page
 * @var int   $pages
 * @var array $actions
 * @var array $users
 * @var array $filters
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<form class="filters" method="get" action="<?= Security::e(admin_url('islem-gunlugu')) ?>">
  <div class="field">
    <label for="islem">İşlem</label>
    <select id="islem" name="islem">
      <option value="">Tümü</option>
      <?php foreach ($actions as $action): ?>
        <option value="<?= Security::e($action) ?>" <?= $filters['islem'] === $action ? 'selected' : '' ?>>
          <?= Security::e($action) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="kullanici">Kullanıcı</label>
    <select id="kullanici" name="kullanici">
      <option value="0">Tümü</option>
      <?php foreach ($users as $user): ?>
        <option value="<?= (int) $user['id'] ?>" <?= (int) $filters['kullanici'] === (int) $user['id'] ? 'selected' : '' ?>>
          <?= Security::e($user['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button class="btn btn--ghost btn--sm" type="submit">Filtrele</button>
</form>

<section class="panel">
  <p class="muted"><?= Security::e((string) $total) ?> kayıt</p>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">Zaman</th>
        <th scope="col">Kullanıcı</th>
        <th scope="col">İşlem</th>
        <th scope="col">Kayıt</th>
        <th scope="col">Ayrıntı</th>
        <th scope="col">IP</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="muted">Kayıt yok.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td><?= Security::e(format_date($row['created_at'], true)) ?></td>
          <td><?= Security::e($row['user_name'] ?? 'sistem') ?></td>
          <td><code><?= Security::e($row['action']) ?></code></td>
          <td>
            <?= Security::e($row['entity'] ?? '—') ?>
            <?= $row['entity_id'] !== null ? '#' . Security::e((string) $row['entity_id']) : '' ?>
          </td>
          <td class="wrap-anywhere"><?= Security::e(str_limit((string) ($row['detail'] ?? ''), 120)) ?></td>
          <td><?= Security::e($row['ip'] ?? '—') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
    <nav class="pager" aria-label="Sayfalar">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="pager__item is-current" aria-current="page"><?= (int) $i ?></span>
        <?php else: ?>
          <a class="pager__item" href="<?= Security::e(admin_url('islem-gunlugu') . '?sayfa=' . $i) ?>"><?= (int) $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>
</section>
