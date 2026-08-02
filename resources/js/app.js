/*
Template Name: Dusty - Responsive Bootstrap 5 Admin Dashboard
Author: Zoyothemes
Version: 1.0.0
Website: https://zoyothemes.com/
File: Main Js File
*/

import $ from 'jquery'
window.jQuery = window.$ = $

import bootstrap from 'bootstrap/dist/js/bootstrap.min';
window.bootstrap = bootstrap;

import 'simplebar'
import Waves from 'node-waves'
import feather from 'feather-icons'
import 'iconify-icon'
window.feather = feather

import select2 from 'select2'
select2(window.$)

import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts


// Auto-format ribuan (thousand separator) untuk input rupiah / angka.
// Pasang class "input-ribuan" pada <input type="text"> yang tampil ke user.
// Jika perlu nilai mentah (tanpa titik) dikirim ke server, tambahkan
// atribut data-target="#idInputHidden" yang menunjuk ke <input type="hidden">.
function formatRibuan(el) {
  // Hanya digit dan satu koma (pemisah desimal ala Indonesia) yang diizinkan.
  let value = el.value.replace(/[^\d,]/g, '');
  const firstComma = value.indexOf(',');
  if (firstComma !== -1) {
    value = value.slice(0, firstComma + 1) + value.slice(firstComma + 1).replace(/,/g, '');
  }

  const [intPart, decPart] = value.split(',');
  const intFormatted = intPart ? parseInt(intPart, 10).toLocaleString('id-ID') : '';
  el.value = decPart !== undefined ? intFormatted + ',' + decPart.slice(0, 2) : intFormatted;

  const raw = intPart ? intPart + (decPart !== undefined ? '.' + decPart.slice(0, 2) : '') : '';
  const targetSel = el.dataset.target;
  if (targetSel) {
    const hidden = document.querySelector(targetSel);
    if (hidden) hidden.value = raw;
  }

  el.dispatchEvent(new CustomEvent('ribuan:change', { bubbles: true, detail: { raw } }));
}
window.formatRibuan = formatRibuan;

// Isi input .input-ribuan secara terprogram (mis. saat populate modal edit)
// dari nilai mentah (number atau string dengan titik desimal).
window.setRibuanValue = function (el, rawValue) {
  if (!el) return;
  const str = (rawValue === null || rawValue === undefined || rawValue === '') ? '' : String(rawValue);
  el.value = str.replace('.', ',');
  formatRibuan(el);
};

// Capture phase: format & sync hidden target BEFORE any page-specific
// 'input' listener attached directly to the element runs (those fire during
// the bubble/"at target" phase, which comes after capture).
document.addEventListener('input', function (e) {
  if (e.target.matches && e.target.matches('.input-ribuan')) {
    formatRibuan(e.target);
  }
}, true);

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.input-ribuan').forEach(formatRibuan);
});

class App {
  // All Components
  initComponents() {
    // Waves Effect
    Waves.init();

    // Feather Icons
    feather.replace();

    // Popovers
    var popoverTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });

    // Tooltips
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Toasts
    var toastElList = [].slice.call(document.querySelectorAll(".toast"));
    var toastList = toastElList.map(function (toastEl) {
      return new bootstrap.Toast(toastEl);
    });

    // Toasts Placement
    var toastPlacement = document.getElementById("toastPlacement");
    if (toastPlacement) {
      document
        .getElementById("selectToastPlacement")
        .addEventListener("change", function () {
          if (!toastPlacement.dataset.originalClass) {
            toastPlacement.dataset.originalClass = toastPlacement.className;
          }
          toastPlacement.className =
            toastPlacement.dataset.originalClass + " " + this.value;
        });
    }

    // liveAlert
    var alertPlaceholder = document.getElementById("liveAlertPlaceholder");
    var alertTrigger = document.getElementById("liveAlertBtn");

    function alert(message, type) {
      var wrapper = document.createElement("div");
      wrapper.innerHTML =
        '<div class="alert alert-' +
        type +
        ' alert-dismissible" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

      alertPlaceholder.append(wrapper);
    }

    if (alertTrigger) {
      alertTrigger.addEventListener("click", function () {
        alert("Nice, you triggered this alert message!", "primary");
      });
    }
  }

  //  Full Screen Controls
  initControls = function () {
    //  Full Screen Controls
    $('[data-toggle="fullscreen"]').on("click", function (e) {
      e.preventDefault();
      $("body").toggleClass("fullscreen-enable");
      if (
        !document.fullscreenElement &&
        /* alternative standard method */ !document.mozFullScreenElement &&
        !document.webkitFullscreenElement
      ) {
        // current working methods
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(
            Element.ALLOW_KEYBOARD_INPUT
          );
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    });
    document.addEventListener("fullscreenchange", exitHandler);
    document.addEventListener("webkitfullscreenchange", exitHandler);
    document.addEventListener("mozfullscreenchange", exitHandler);

    function exitHandler() {
      if (
        !document.webkitIsFullScreen &&
        !document.mozFullScreen &&
        !document.msFullscreenElement
      ) {
        $("body").removeClass("fullscreen-enable");
      }
    }
  };

  // Menu Toggle Button
  initMenu() {
    var self = this;
    var body = document.body;

    // Menu Toggle Button ( Placed in Topbar) (Left menu collapse)
    var menuToggleBtn = document.querySelector(".button-toggle-menu");
    if (menuToggleBtn) {
      menuToggleBtn.addEventListener("click", function () {
        if (body.getAttribute("data-sidebar") == "default") {
          body.setAttribute("data-sidebar", "hidden");
        } else {
          body.setAttribute("data-sidebar", "default");
        }
      });
    }

    const updateOnWindowResize = () => {
      if (window.innerWidth < 1040) {
        body.setAttribute("data-sidebar", "hidden");
      } else {
        body.setAttribute("data-sidebar", "default");
      }
    };

    updateOnWindowResize();
    window.addEventListener("resize", updateOnWindowResize);

    // Sidebar - main menu
    if ($("#side-menu").length) {
      var navCollapse = $("#side-menu li .collapse");

      // open one menu at a time only
      navCollapse.on({
        "show.bs.collapse": function (event) {
          var parent = $(event.target).parents(".collapse.show");
          // $("#side-menu .collapse.show").not(parent).collapse("hide");
        },
      });

      // activate the menu in left side bar (Vertical Menu) based on url
      $("#side-menu a").each(function () {
        var pageUrl = window.location.href.split(/[?#]/)[0];
        if (this.href == pageUrl) {
          $(this).addClass("active");
          $(this).parent().addClass("menuitem-active");
          $(this).parent().parent().parent().addClass("show");
          $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .addClass("menuitem-active");

          var firstLevelParent = $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent();
          if (firstLevelParent.attr("id") !== "sidebar-menu")
            firstLevelParent.addClass("show");

          $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .addClass("menuitem-active");

          var secondLevelParent = $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent();
          if (secondLevelParent.attr("id") !== "wrapper")
            secondLevelParent.addClass("show");

          var upperLevelParent = $(this)
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .parent();
          if (!upperLevelParent.is("body"))
            upperLevelParent.addClass("menuitem-active");
        }
      });
    }
  }

  init() {
    this.initComponents();
    this.initMenu();
    this.initControls();
  }
}

new App().init();
