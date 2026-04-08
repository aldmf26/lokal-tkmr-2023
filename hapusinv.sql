-- Hapus semua data hari ini untuk reset pengujian:
DELETE FROM tb_order WHERE tgl = CURDATE();
DELETE FROM tb_order2 WHERE tgl = CURDATE();
DELETE FROM tb_pembelian WHERE tanggal = CURDATE();
DELETE FROM tb_stok_produk WHERE tgl = CURDATE();
DELETE FROM tb_transaksi WHERE tgl_transaksi = CURDATE();
DELETE FROM pembayaran WHERE tgl = CURDATE();
DELETE FROM tb_invoice WHERE DATE(tgl_jam) = CURDATE();
