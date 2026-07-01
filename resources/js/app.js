// Global interactivity layer: make every server round-trip visible.
// - the control the user activated is dimmed, locked and spinner'd while busy
// - a thin progress bar runs along the top of the page during any request
document.addEventListener('livewire:init', () => {
    const bar = document.getElementById('global-progress');
    let inflight = 0;

    const startBar = () => {
        inflight++;
        if (!bar) return;
        bar.style.opacity = '1';
        bar.style.transform = 'scaleX(0.75)';
    };

    const stopBar = () => {
        inflight = Math.max(0, inflight - 1);
        if (!bar || inflight > 0) return;
        bar.style.transform = 'scaleX(1)';
        setTimeout(() => {
            if (inflight === 0) {
                bar.style.opacity = '0';
                bar.style.transform = 'scaleX(0)';
            }
        }, 250);
    };

    // Remember which control the user activated so we can flag it as busy.
    // Only track controls that actually hit the server (a wire:click element,
    // or a submit button inside a wire:submit form) — not plain buttons.
    let pending = null;
    document.addEventListener(
        'click',
        (e) => {
            const el = e.target.closest('[wire\\:click], button[type="submit"], button:not([type])');
            if (!el) return;
            if (!el.hasAttribute('wire:click') && !el.closest('form[wire\\:submit]')) return;
            pending = el;
        },
        true,
    );
    document.addEventListener(
        'submit',
        (e) => {
            if (!e.target.matches('form[wire\\:submit]')) return;
            const btn = e.target.querySelector('button[type="submit"], button:not([type])');
            if (btn) pending = btn;
        },
        true,
    );

    // Tie the busy visuals to the actual network commit lifecycle.
    Livewire.hook('commit', ({ succeed, fail }) => {
        const el = pending;
        pending = null;
        startBar();
        if (el) {
            el.classList.add('is-busy');
            el.setAttribute('disabled', 'disabled');
        }
        const done = () => {
            stopBar();
            if (el) {
                el.classList.remove('is-busy');
                el.removeAttribute('disabled');
            }
        };
        succeed(done);
        fail(done);
    });

    // SPA navigation has its own bar; make sure ours never gets stuck.
    document.addEventListener('livewire:navigated', () => {
        inflight = 0;
        if (bar) {
            bar.style.opacity = '0';
            bar.style.transform = 'scaleX(0)';
        }
    });
});
