(function() {
  'use strict';

  const controller = new SeatingMapController({
    iframeId: 'asientosIframe',
    zoomMin: 0.5,
    zoomMax: 2,
    zoomStep: 0.25
  });

  window.zoomLevel = controller.zoomLevel;
  window.zoomMin = controller.zoomMin;
  window.zoomMax = controller.zoomMax;

  window.showAsientosMap = function(evento) {
    const tableContainer = document.getElementById('table-container');
    const asientosContainer = document.getElementById('asientos-container');
    const asientosControls = document.getElementById('asientos-controls');
    const selectEvento = document.getElementById('selectEventoAsientos');

    if (!evento) evento = 'li';

    if (tableContainer) tableContainer.style.display = 'none';
    if (asientosControls) asientosControls.style.display = 'flex';
    if (asientosContainer) asientosContainer.style.display = 'block';

    controller.setEvento(evento);

    if (selectEvento) selectEvento.value = evento;
  };

  window.showTableAlumnos = function() {
    const tableContainer = document.getElementById('table-container');
    const asientosContainer = document.getElementById('asientos-container');
    const asientosControls = document.getElementById('asientos-controls');

    if (asientosContainer) asientosContainer.style.display = 'none';
    if (asientosControls) asientosControls.style.display = 'none';
    if (tableContainer) tableContainer.style.display = 'block';
  };

  window.zoomIn = function() {
    controller.zoomIn();
  };

  window.zoomOut = function() {
    controller.zoomOut();
  };

  function updateGlobalZoom() {
    window.zoomLevel = controller.getZoomLevel();
  }

  controller.onZoomChange(updateGlobalZoom);

  document.addEventListener('DOMContentLoaded', function() {
    controller.init();

    const selectEvento = document.getElementById('selectEventoAsientos');
    if (selectEvento) {
      selectEvento.addEventListener('change', function(e) {
        window.showAsientosMap(e.target.value);
      });
    }
  });
})();