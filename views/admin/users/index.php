<?php
/**
 * Kullanici listesi.  DOCS.md 9.11
 *
 * @var array $users
 */

declare(strict_types=1);

use Arcates\Core\Security;
?>

<div class="panel__head">
  <p class="muted"><?= Security::e((string) count($users)) ?> kullanici</p>
  <a class="btn btn--primary btn--sm" href="<?= Security::e(admin_url('kullanicilar/yeni')) ?>">Yeni kullanici</a>
</div>

<section class="panel">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Ad</th>
        <th scope="col">E-posta</th>
        <th scope="col">Rol</th>
        <th scope="col">Durum</th>
        <th scope="col">Son giris</th>
        <th scope="col"><span class="visually-hidden">Islemler</span></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?= Security::e($user['name']) ?></td>
        <td><?= Security::e($user['email']) ?></td>
        <td><?= Security::e($user['role'] === 'admin' ? 'Yonetici' : 'Editor') ?></td>
        <td>
          <span class="tag tag--<?= (int) $user['status'] === 1 ? 'ok' : 'off' ?>">
            <?= (int) $user['status'] === 1 ? 'Etkin' : 'Kapali' ?>
          </span>
        </td>
        <td><?= Security::e(format_date($user['last_login_at'], true) ?: '—') ?></td>
        <td class="row-actions">
          <a class="btn btn--ghost btn--sm" href="<?= Security::e(admin_url('kullanicilar/' . (int) $user['id'])) ?>">Duzenle</a>
          <form method="post" action="<?= Security::e(admin_url('kullanicilar/' . (int) $user['id'] . '/sil')) ?>"
                data-confirm="<?= Security::e($user['name']) ?> kullanicisini silmek istiyor musunuz?">
            <?= csrf_field() ?>
            <button class="btn btn--danger btn--sm" type="submit">Sil</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</section>
