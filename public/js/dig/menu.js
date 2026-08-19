$(document).ready(function () {
    "use strict";

    // Inicialización (Verifica si el elemento está oculto)
    $('#infopaqueteid').is(':hidden');

    // 1. Toggle manual del Sidebar (Botones)
    $("#sidebarToggle, #sidebarToggleTop").on('click', function (e) {
        $("body").toggleClass("sidebar-toggled");
        $(".sidebar").toggleClass("toggled");
        if ($(".sidebar").hasClass("toggled")) {
            $('.sidebar .collapse').collapse('hide');
        }
    });

    // 2. Control de Redimensionamiento (Resize)
    $(window).resize(function () {
        var windowWidth = $(window).width();

        // Si la pantalla es menor a 768px, cerramos los acordeones abiertos
        if (windowWidth < 768) {
            $('.sidebar .collapse').collapse('hide');
        }

        // Si la pantalla es móvil (< 480px), forzamos que se contraiga
        if (windowWidth < 480 && !$(".sidebar").hasClass("toggled")) {
            $("body").addClass("sidebar-toggled");
            $(".sidebar").addClass("toggled");
            $('.sidebar .collapse').collapse('hide');
        }

        // --- SOLUCIÓN: Si la pantalla vuelve a ser grande, eliminamos el estado "toggled" ---
        if (windowWidth >= 768) {
            $("body").removeClass("sidebar-toggled");
            $(".sidebar").removeClass("toggled");
            // Opcional: si quieres que los menús se vean desplegados al volver a grande, 
            // podrías quitar el .collapse('hide') aquí.
        }
    });

    // 3. Comportamiento del scroll en Sidebar fija (solo escritorio)
    $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function (e) {
        if ($(window).width() > 768) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        }
    });

    // 4. Aparición del botón "Scroll to top"
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

    // 6. Logout
    $("#btn-logout").on('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto
        $("#form-logout").submit();
    });

});