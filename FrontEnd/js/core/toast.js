/**
 * Toast notification system using custom <app-toast> tag.
 */
class ToastSystem {
    constructor() {
        this.container = document.querySelector('app-toast');
        if (!this.container) {
            this.container = document.createElement('app-toast');
            document.body.appendChild(this.container);
        }
    }

    success(message) { this.show(message, 'success'); }
    error(message) { this.show(message, 'error'); }
    warning(message) { this.show(message, 'warning'); }
    info(message) { this.show(message, 'info'); }

    show(message, type = 'info') {
        const item = document.createElement('section');
        item.className = `toast-item toast-item--${type}`;
        
        item.innerHTML = `
            <div class="toast-item__content">${message}</div>
            <div class="toast-item__close">✕</div>
        `;

        this.container.appendChild(item);

        const closeBtn = item.querySelector('.toast-item__close');
        const close = () => {
            item.classList.add('toast-item--fading');
            item.addEventListener('animationend', () => item.remove(), { once: true });
        };

        closeBtn.onclick = close;

        // Auto remove after 5s
        setTimeout(close, 5000);
    }
}

export const toast = new ToastSystem();
