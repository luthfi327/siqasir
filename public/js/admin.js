/* ==========================================================================
   Bathiku Kasir — Admin Panel JS
   Interaksi ringan: sidebar mobile, jam live, auto-dismiss alert,
   konfirmasi aksi, baris tabel yang bisa diklik, format ribuan.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

  // ------------------------------------------------------------------
  // Sidebar toggle (mobile / tablet)
  // ------------------------------------------------------------------
  var sidebar      = document.getElementById('adminSidebar');
  var toggleBtn    = document.getElementById('sidebarToggle');
  var backdrop     = document.getElementById('sidebarBackdrop');

  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('is-open');
    if (backdrop) backdrop.classList.remove('is-open');
  }

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('is-open');
      if (backdrop) backdrop.classList.toggle('is-open');
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  // ------------------------------------------------------------------
  // Jam live di topbar (WIB)
  // ------------------------------------------------------------------
  var clockEl = document.getElementById('liveClock');

  if (clockEl) {
    var namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    function updateClock() {
      var now = new Date();
      var jam = String(now.getHours()).padStart(2, '0');
      var menit = String(now.getMinutes()).padStart(2, '0');
      var detik = String(now.getSeconds()).padStart(2, '0');
      clockEl.textContent = namaHari[now.getDay()] + ', ' + jam + ':' + menit + ':' + detik + ' WIB';
    }

    updateClock();
    setInterval(updateClock, 1000);
  }

  // ------------------------------------------------------------------
  // Auto-dismiss alert sukses setelah beberapa detik
  // ------------------------------------------------------------------
  document.querySelectorAll('.alert-auto-dismiss').forEach(function (el) {
    setTimeout(function () {
      if (window.bootstrap && window.bootstrap.Alert) {
        var instance = window.bootstrap.Alert.getOrCreateInstance(el);
        instance.close();
      } else {
        el.style.display = 'none';
      }
    }, 5000);
  });

  // ------------------------------------------------------------------
  // Konfirmasi sebelum submit form berbahaya (mis. nonaktifkan promo)
  // Tambahkan atribut data-confirm="Pesan konfirmasi..." pada <form>.
  // ------------------------------------------------------------------
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var message = form.getAttribute('data-confirm') || 'Apakah Anda yakin?';
      if (!window.confirm(message)) {
        e.preventDefault();
      }
    });
  });

  // ------------------------------------------------------------------
  // Baris tabel yang bisa diklik untuk navigasi (data-href)
  // ------------------------------------------------------------------
  document.querySelectorAll('tr.row-clickable[data-href]').forEach(function (row) {
    row.addEventListener('click', function (e) {
      // Jangan navigasi jika yang diklik adalah tombol/link di dalam baris
      if (e.target.closest('a, button')) return;
      window.location.href = row.getAttribute('data-href');
    });
  });

  // ------------------------------------------------------------------
  // Format ribuan otomatis pada input harga (tampilan saja, value asli
  // tetap dikirim lewat hidden input atau di-strip sebelum submit)
  // ------------------------------------------------------------------
  document.querySelectorAll('input[data-format-rupiah]').forEach(function (input) {
    input.addEventListener('input', function () {
      var raw = input.value.replace(/[^0-9]/g, '');
      input.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
    });

    var form = input.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        input.value = input.value.replace(/[^0-9]/g, '');
      });
    }
  });

  // ------------------------------------------------------------------
  // Filter pilihan toko (datalist/select) berdasarkan teks pencarian
  // Dipakai di form tambah promo: <input data-filter-target="#kodeOwnerSelect">
  // ------------------------------------------------------------------
  document.querySelectorAll('[data-filter-target]').forEach(function (searchInput) {
    var targetSelect = document.querySelector(searchInput.getAttribute('data-filter-target'));
    if (!targetSelect) return;

    var allOptions = Array.prototype.slice.call(targetSelect.options);

    searchInput.addEventListener('input', function () {
      var keyword = searchInput.value.toLowerCase();
      allOptions.forEach(function (opt) {
        if (opt.value === '') return; // selalu tampilkan opsi placeholder
        var match = opt.text.toLowerCase().indexOf(keyword) !== -1;
        opt.hidden = !match;
      });
    });
  });

});