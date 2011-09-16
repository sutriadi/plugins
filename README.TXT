Plugins Module for SLiMS
Version 3.15-2.1 build 14083116092011

Copyright (c) 1431 H / 2010 M
Indra Sutriadi Pipii (indra@sutriadi.web.id)

Official Website: http://sutriadi.web.id/slims-plugins
Daily Development: https://github.com/sutriadi/plugins

# Persyaratan System
- SLiMS 3 Stable 15 "Matoa" (download di http://slims.web.id/)

# Instalasi
- ekstrak paket plugins-15.x.x.tar.gz
  sehingga akan menghasilkan direktori ./plugins
- salin direktori plugins hasil ekstraksi ke ./admin/modules/
  sehingga hasil akhirnya adalah direktori ./admin/modules/plugins/
- import data sql/install.sql ke database
catatan: bila melakukan instalasi dari paket yang diambil dari github,
mohon untuk mengubah nama direktori hasil penguraian
dari ./sutriadi-plugins-xxx menjadi ./plugins

# Naikkan Instalasi
- uraikan paket plugins-15.x.x
  sehingga akan menghasilkan direktori ./plugins
- salin seluruh direktori plugin yang terpasang dari direktori
  ./admin/modules/plugins/ ke direktori include hasil ekstraksi
  di ./plugins/include/
- hapus direktori ./admin/modules/plugins
- salin direktori plugins hasil ekstraksi ke ./admin/modules/
  sehingga hasil akhirnya adalah direktori ./admin/modules/plugins/
- import data sql/upgrade-to-15.1.2.sql (bila dari versi 0.3),
  dan bila melakukan upgrade dari versi di bawah 0.3, supaya
  melakukan import data sql/upgrade-to-0.3.sql terlebih dahulu
catatan: bila melakukan upgrade dari paket yang diambil dari github,
mohon untuk mengubah nama direktori hasil penguraian
dari ./sutriadi-plugins-xxx menjadi ./plugins

# Hapus Instalasi
- hapus direktori plugins dari direktori ./admin/modules/
- import data sql/uninstall.sql ke database

# Hapus Data
Tidak semua data di database dihapus ketika instalasi dihapus.
Untuk menghapus secara permanen import data sql/remove.sql ke database

CHANGELOG
16.09.2011 (alpha)
- penambahan submodul block dan theme

03.09.2011
- restrukturisasi direktori
- penggunaan library untuk menyederhanakan dependensi
- penambahan submodul configure dan dataTables
- penambahan file SQL uninstalasi modul Plugins
- penyesuaian pengkodean di SLiMS 3 Stable 15

20.02.2011
- bug fixing
