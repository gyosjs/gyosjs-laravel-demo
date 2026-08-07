import Gyos from 'gyosjs/auto';

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
