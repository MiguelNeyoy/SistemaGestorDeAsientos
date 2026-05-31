class SeatingMapController {
  constructor(options = {}) {
    this.iframeId = options.iframeId || 'asientosIframe';
    this.zoomMin = options.zoomMin || 0.5;
    this.zoomMax = options.zoomMax || 2;
    this.zoomStep = options.zoomStep || 0.25;
    this.zoomLevel = 1;
    this.isDragging = false;
    this.startX = 0;
    this.startY = 0;
    this.scrollLeft = 0;
    this.scrollTop = 0;
    this.isIframe = window.self !== window.top;
    this._initialized = false;
  }

  init() {
    if (this._initialized) return;
    this.initDrag();
    this._initialized = true;
  }

  getIframe() {
    return document.getElementById(this.iframeId);
  }

  getZoomElement() {
    const iframe = this.getIframe();
    if (!iframe) return null;

    try {
      const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
      return iframeDoc.querySelector('.contenedor-scroll') || iframeDoc.body;
    } catch (e) {
      console.log('No se puede acceder al contenido:', e);
      return null;
    }
  }

  updateZoom() {
    const container = this.getZoomElement();
    if (container) {
      container.style.transform = 'scale(' + this.zoomLevel + ')';
      container.style.transformOrigin = 'top center';
      container.style.width = '200%';
      container.style.height = '200%';
    }

    const zoomDisplay = document.getElementById('zoomLevel');
    if (zoomDisplay) {
      zoomDisplay.textContent = Math.round(this.zoomLevel * 100) + '%';
    }

    this._onZoomChange?.(this.zoomLevel);
  }

  zoomIn() {
    if (this.zoomLevel < this.zoomMax) {
      this.zoomLevel += this.zoomStep;
      this.updateZoom();
    }
  }

  zoomOut() {
    if (this.zoomLevel > this.zoomMin) {
      this.zoomLevel -= this.zoomStep;
      this.updateZoom();
    }
  }

  setZoomLevel(level) {
    if (level >= this.zoomMin && level <= this.zoomMax) {
      this.zoomLevel = level;
      this.updateZoom();
    }
  }

  resetZoom() {
    this.zoomLevel = 1;
    this.updateZoom();
  }

  getZoomLevel() {
    return this.zoomLevel;
  }

  setEvento(evento) {
    const iframe = this.getIframe();
    if (iframe) {
      iframe.src = '../asientos.php?evento=' + evento;
      this.resetZoom();
    }
  }

  initDrag() {
    if (this.isIframe) {
      this.initIframeDrag();
    } else {
      this.initContainerDrag();
    }
  }

  initContainerDrag() {
    const container = this.getIframe()?.parentElement;
    if (!container) return;

    container.style.cursor = 'grab';

    container.addEventListener('mousedown', (e) => {
      if (e.target.tagName === 'BUTTON' || e.target.tagName === 'SELECT') return;
      this.isDragging = true;
      container.style.cursor = 'grabbing';
      this.startX = e.clientX;
      this.startY = e.clientY;
      this.scrollLeft = container.scrollLeft;
      this.scrollTop = container.scrollTop;
      e.preventDefault();
    });

    document.addEventListener('mouseup', () => {
      if (this.isDragging) {
        this.isDragging = false;
        container.style.cursor = 'grab';
      }
    });

    document.addEventListener('mousemove', (e) => {
      if (!this.isDragging) return;

      const deltaX = e.clientX - this.startX;
      const deltaY = e.clientY - this.startY;

      container.scrollLeft = this.scrollLeft - deltaX;
      container.scrollTop = this.scrollTop - deltaY;
    });
  }

  initIframeDrag() {
    const iframe = this.getIframe();
    if (!iframe) return;

    try {
      const win = iframe.contentWindow;
      const doc = win.document;
      const body = doc.body;

      body.style.cursor = 'grab';

      let isDragging = false;
      let startX, startY;
      let scrollLeft, scrollTop;

      body.addEventListener('mousedown', (e) => {
        isDragging = true;
        body.style.cursor = 'grabbing';
        startX = e.clientX;
        startY = e.clientY;
        scrollLeft = body.scrollLeft;
        scrollTop = body.scrollTop;
        return false;
      });

      win.addEventListener('mouseup', () => {
        if (isDragging) {
          isDragging = false;
          body.style.cursor = 'grab';
        }
      });

      win.addEventListener('mousemove', (e) => {
        if (!isDragging) return;

        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;

        body.scrollLeft = scrollLeft - deltaX;
        body.scrollTop = scrollTop - deltaY;
      });
    } catch (e) {
      console.log('No se puede acceder al iframe:', e);
    }
  }

  onZoomChange(callback) {
    this._onZoomChange = callback;
  }

  destroy() {
    this._initialized = false;
    this.zoomLevel = 1;
  }
}

window.SeatingMapController = SeatingMapController;