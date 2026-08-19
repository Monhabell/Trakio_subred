(function ($) {
  "use strict";

  // Función reutilizable para ajustar el sidebar según el ancho de pantalla
  function checkWidth() {
    var windowWidth = $(window).width();

    if (windowWidth >= 768) {
      // En pantallas grandes: quitamos el toggle para que se vea normal
      $("body").removeClass("sidebar-toggled");
      $("#accordionSidebar").removeClass("toggled");
      $("#main-container").addClass("max-container").removeClass("min-container");
    } else {
      // En pantallas pequeñas: forzamos el colapso inicial
      $("body").addClass("sidebar-toggled");
      $("#accordionSidebar").addClass("toggled");
      $('.sidebar .collapse').collapse('hide');
    }
  }

  // EJECUTAR AL CARGAR LA PÁGINA
  $(window).on('load', function () {
    checkWidth();
  });

  // EJECUTAR AL CAMBIAR EL TAMAÑO DE VENTANA
  $(window).resize(function () {
    checkWidth();
  });

  // COMPORTAMIENTO HOVER (Abrir/Cerrar al pasar el ratón)
  // Solo funciona si la pantalla es grande
  $("#sidebarToggle, .nav-item-menu, .sidebar").on('mouseover', function (e) {
    if ($(window).width() > 768) {
      $("#accordionSidebar").removeClass("toggled");
    }
  });

  $("#sidebarToggle, .nav-item-menu, .sidebar").on('mouseout', function (e) {
    if ($(window).width() > 768) {
      $("#accordionSidebar").addClass("toggled");
    }
  });

  // TOGGLE POR CLICK (Botón superior)
  $("#sidebarToggleTop").on('click', function (e) {
    $("body").toggleClass("sidebar-toggled");
    $("#accordionSidebar").toggleClass("toggled");
    $("#main-container").toggleClass("min-container max-container");
  });

  // --- El resto de tus funciones permanecen igual ---

  // Prevenir scroll en sidebar fija
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Botón Scroll to top
  $(document).on('scroll', function () {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  $(document).on('click', 'a.scroll-to-top', function (e) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    e.preventDefault();
  });

  // Logout
  $("#btn-logout").on('click', function (event) {
    event.preventDefault();
    $("#form-logout").submit();
  });

})(jQuery);