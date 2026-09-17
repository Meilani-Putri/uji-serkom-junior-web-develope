<?php
/**
 * Partial kartu produk.
 * Variabel yang dipakai:
 *   $p         -> array data produk (harus berisi kategori_nama)
 *   $kembaliKe -> halaman tujuan setelah tombol keranjang ditekan (opsional)
 */
$kembaliKe = $kembaliKe ?? 'produk.php';
$stok = (int)($p['stok'] ?? 0);
?>
<article class="product-card">
  <a href="detail.php?id=<?= (int)$p['id'] ?>" class="product-card__media" aria-label="Lihat detail <?= bersihkan($p['nama']) ?>">
    <?php if (!empty($p['gambar'])): ?>
      <div class="product-swatch product-swatch--foto" style="background-image:url('/toko-rajut/assets/img/produk/<?= bersihkan($p['gambar']) ?>')"></div>
    <?php else: ?>
      <div class="product-swatch" style="background-color:<?= bersihkan($p['warna']) ?>">
        <span class="product-swatch__initial"><?= bersihkan(mb_substr($p['nama'], 0, 1)) ?></span>
      </div>
    <?php endif; ?>

    <?php if ($stok <= 0): ?>
      <span class="product-badge product-badge--habis">Stok habis</span>
    <?php elseif ($stok <= 3): ?>
      <span class="product-badge product-badge--terbatas">Sisa <?= $stok ?></span>
    <?php endif; ?>
  </a>

  <div class="product-card__body">
    <span class="product-card__kategori"><?= bersihkan($p['kategori_nama'] ?? '') ?></span>
    <h3 class="product-card__nama">
      <a href="detail.php?id=<?= (int)$p['id'] ?>"><?= bersihkan($p['nama']) ?></a>
    </h3>
    <div class="product-card__meta">
      <p class="product-card__harga"><?= rupiah((int)$p['harga']) ?></p>
      <?php if ($stok > 0): ?>
        <span class="product-card__stok"><?= ikon('centang') ?> Stok <?= $stok ?></span>
      <?php else: ?>
        <span class="product-card__stok product-card__stok--habis">Tidak tersedia</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="product-card__aksi">
    <a href="detail.php?id=<?= (int)$p['id'] ?>" class="product-card__link">Lihat detail</a>
    <?php if ($stok > 0): ?>
      <form method="post" action="tambah_keranjang.php" class="product-card__form">
        <input type="hidden" name="produk_id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="jumlah" value="1">
        <input type="hidden" name="kembali" value="<?= bersihkan($kembaliKe) ?>">
        <button type="submit" class="product-card__tambah" title="Tambah ke keranjang" aria-label="Tambah <?= bersihkan($p['nama']) ?> ke keranjang">
          <?= ikon('keranjang-plus') ?>
        </button>
      </form>
    <?php else: ?>
      <button type="button" class="product-card__tambah product-card__tambah--nonaktif" disabled title="Stok habis" aria-label="Stok habis">
        <?= ikon('keranjang') ?>
      </button>
    <?php endif; ?>
  </div>
</article>