document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('mainNav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
  }

  var esAnonima = document.getElementById('es_anonima');
  var datosContacto = document.getElementById('datosContacto');
  if (esAnonima && datosContacto) {
    var toggleContacto = function () {
      datosContacto.style.display = esAnonima.checked ? 'none' : 'block';
    };
    esAnonima.addEventListener('change', toggleContacto);
    toggleContacto();
  }
});
