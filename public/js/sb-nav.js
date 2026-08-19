(function ($) {
  "use strict"; // Start of use strict

  // 1. Toggle manual mediante botones
  $("#sidebarToggle, #sidebarToggleTop").on('click', function (e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    if ($(".sidebar").hasClass("toggled")) {
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // 2. Lógica de redimensionamiento (Resize) corregida
  $(window).resize(function () {
    var windowWidth = $(window).width();

    // Si la pantalla es pequeña (menor a 768px)
    if (windowWidth < 768) {
      $('.sidebar .collapse').collapse('hide');
    };

    // Si la pantalla es muy pequeña (móvil < 480px), forzamos el colapso
    if (windowWidth < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $('.sidebar .collapse').collapse('hide');
    }

    // SOLUCIÓN AL PROBLEMA: 
    // Si la pantalla vuelve a ser grande (Escritorio >= 768px), quitamos el toggle
    if (windowWidth >= 768) {
      $("body").removeClass("sidebar-toggled");
      $(".sidebar").removeClass("toggled");
    }
  });

  // 3. Prevenir scroll del contenido cuando el sidebar fijo tiene hover
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // 4. Botón de "Ir arriba" (Scroll to top)
  $(document).on('scroll', function () {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // 5. Scroll suave (Smooth scrolling)
  $(document).on('click', 'a.scroll-to-top', function (e) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    e.preventDefault();
  });

})(jQuery); // End of use strict