import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

Alpine.plugin(focus);
Alpine.plugin(collapse);

const csrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

Alpine.data('cartDrawer', () => ({
    show: false,
    loading: false,

    open(html) {
        this.show = true;
        if (html) {
            this.$refs.content.innerHTML = html;
        } else {
            this.refresh();
        }
    },

    close() {
        this.show = false;
    },

    async refresh() {
        this.loading = true;
        try {
            const res = await fetch('/cart/drawer');
            this.$refs.content.innerHTML = await res.text();
        } finally {
            this.loading = false;
        }
    },

    async handleClick(e) {
        const removeBtn = e.target.closest('[data-remove]');
        if (removeBtn) {
            e.preventDefault();
            const itemId = removeBtn.dataset.remove;
            const res = await fetch(`/cart/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
            });
            if (res.ok) {
                const data = await res.json();
                this.$refs.content.innerHTML = data.drawer_html;
                window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: { count: data.count } }));
            }
            return;
        }

        if (e.target.closest('[data-close-drawer]')) {
            e.preventDefault();
            const link = e.target.closest('a[data-close-drawer]');
            this.close();
            if (link && link.href) {
                window.location.href = link.href;
            }
        }
    },

    async handleChange(e) {
        const qtySelect = e.target.closest('[data-update-qty]');
        if (qtySelect) {
            const itemId = qtySelect.dataset.updateQty;
            const quantity = parseInt(qtySelect.value, 10);
            const res = await fetch(`/cart/${itemId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ quantity }),
            });
            if (res.ok) {
                const data = await res.json();
                this.$refs.content.innerHTML = data.drawer_html;
                window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: { count: data.count } }));
            }
        }
    },

    handleCartOpen(e) {
        this.open(e.detail?.html);
    },
}));

window.Alpine = Alpine;
Alpine.start();
