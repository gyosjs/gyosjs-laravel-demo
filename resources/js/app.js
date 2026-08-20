import Gyos from 'gyosjs/csp/auto';
import 'gyosjs/styles.css';

Gyos.scope('ProductForm', () => ({
    step: 1,
    maxStep: 3,
    name: '',
    sku: '',
    category: '',
    description: '',
    price: 0,
    status: 'draft',
    stock: 0,
    next() {
        this.step = Math.min(this.maxStep, this.step + 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    previous() {
        this.step = Math.max(1, this.step - 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    goTo(step) {
        this.step = Math.max(1, Math.min(this.maxStep, step));
    },
    formatPrice() {
        return Number(this.price || 0).toFixed(2);
    },
}));

Gyos.scope('ConfirmAction', () => ({
    message: 'Continue?',
    confirm(event) {
        if (!window.confirm(this.message)) event.preventDefault();
    },
}));

Gyos.scope('Stocktake', () => ({
    expected: {},
    counts: {},
    get changedCount() {
        return Object.keys(this.expected).filter(id => this.isChanged(id)).length;
    },
    get totalVariance() {
        return Object.keys(this.expected).reduce((total, id) => total + this.variance(id), 0);
    },
    variance(id) {
        return Number(this.counts[id] ?? 0) - Number(this.expected[id] ?? 0);
    },
    isChanged(id) {
        return this.variance(id) !== 0;
    },
    hasError(id) {
        return Boolean(this.stocktakeForm?.errors?.()[`counts[${id}]`]);
    },
}));

Gyos.scope('Scratchpad', {
    open: false,
    note: '',
    startedAt: Date.now(),
    elapsed: '00:00',
    timer: null,
    onMount() {
        const tick = () => {
            const seconds = Math.floor((Date.now() - this.startedAt) / 1000);
            this.elapsed = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
        };
        tick();
        this.timer = window.setInterval(tick, 1000);
    },
    onUnmount() {
        if (this.timer) window.clearInterval(this.timer);
    },
});

window.Gyos = Gyos;
