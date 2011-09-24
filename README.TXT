Plugins Module for SLiMS
Version 3.15-2.1 build 10120224092011

Copyright (c) 1431 H / 2010 M
Indra Sutriadi Pipii (indra@sutriadi.web.id)

Official Website: http://sutriadi.web.id/slims-plugins
Daily Development: https://github.com/sutriadi/plugins

# Persyaratan System
- SLiMS 3 Stable 15 "Matoa" (download di http://slims.web.id/)

# Instalasi
- ekstrak paket plugins-3.15-x.x.tar.gz
  sehingga akan menghasilkan direktori ./plugins
- salin direktori plugins hasil ekstraksi ke ./admin/modules/
  sehingga hasil akhirnya adalah direktori ./admin/modules/plugins/
- import data sql/install.sql ke database
catatan: bila melakukan instalasi dari paket yang diambil dari github,
mohon untuk mengubah nama direktori hasil penguraian
dari ./sutriadi-plugins-xxx menjadi ./plugins

# Naikkan Instalasi
- uraikan paket plugins-3.15-x.x
  sehingga akan menghasilkan direktori ./plugins
- salin seluruh direktori plugin yang terpasang dari direktori
  ./admin/modules/plugins/ ke direktori include hasil ekstraksi
  di ./plugins/include/
- hapus direktori ./admin/modules/plugins
- salin direktori plugins hasil ekstraksi ke ./admin/modules/
  sehingga hasil akhirnya adalah direktori ./admin/modules/plugins/
- import data sql/upgrade-to-3.15-2.1.sql (bila dari versi 15.1.2),
  dan bila melakukan upgrade dari versi di bawah 15.1.2, supaya
  melakukan import data upgrade secara berurutan
catatan: bila melakukan upgrade dari paket yang diambil dari github,
mohon untuk mengubah nama direktori hasil penguraian
dari ./sutriadi-plugins-xxx menjadi ./plugins

# Hapus Instalasi
- hapus direktori plugins dari direktori ./admin/modules/
- import data sql/uninstall.sql ke database

# Hapus Data
Tidak semua data di database dihapus ketika instalasi dihapus.
Untuk menghapus secara permanen import data sql/remove.sql ke database

SAY THANKS TO:
- Drupal Core Development Team http://drupal.org
- danielizer, author of
  http://wizardinternetsolutions.com/web-database-design/single-query-dynamic-multi-level-menu/

CHANGELOG
24.09.2011 (beta)
- penambahan submodul block, theme dan menu
- penambahan metode serial untuk penyimpanan variable
- penambahan file SQL remove data moduk Plugins
- (uji coba) penambahan kompatibilitas menu, block dan plugin

03.09.2011
- restrukturisasi direktori
- penggunaan library untuk menyederhanakan dependensi
- penambahan submodul configure dan dataTables
- penambahan file SQL uninstalasi modul Plugins
- penyesuaian pengkodean di SLiMS 3 Stable 15

20.02.2011
- bug fixing
