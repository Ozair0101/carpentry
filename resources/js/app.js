// Reusable "print this table" engine used by the <x-print-button> component.
// Clones a table out of the live page, drops the action column and anything
// marked .no-print, then prints it inside a hidden iframe with clean, A4-ready
// styling — independent of the on-screen (Tailwind) layout.
window.printSection = function (selector, opts = {}) {
    const source = document.querySelector(selector);
    const table = source && (source.matches('table') ? source : source.querySelector('table'));
    if (!table) {
        console.warn('printSection: no table found for selector', selector);
        return;
    }

    const clone = table.cloneNode(true);

    // Remove anything explicitly excluded from print (badges' extra markup, etc.).
    clone.querySelectorAll('.no-print').forEach((el) => el.remove());

    // Drop the "action" column entirely, detected by its header label so no
    // per-table markup is required. Match highest index first when removing.
    const actionLabels = ['عملیات', 'اقدامات', 'actions', 'action'];
    const headRow = clone.querySelector('thead tr');
    if (headRow) {
        const heads = Array.from(headRow.children);
        const dropIdx = heads
            .map((th, i) => (actionLabels.includes(th.textContent.trim().toLowerCase()) ? i : -1))
            .filter((i) => i >= 0)
            .sort((a, b) => b - a);

        dropIdx.forEach((idx) => {
            clone.querySelectorAll('tr').forEach((row) => {
                // Only touch rows that share the header's column count, so the
                // empty-state row (a single colspan cell) is left untouched.
                if (row.children.length === heads.length) row.children[idx].remove();
            });
        });
    }

    const esc = (s) => String(s ?? '').replace(/[&<>]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]));
    const company = esc(opts.company);
    const title = esc(opts.title);
    const date = esc(opts.date);

    const styles = `
        @page { size: A4; margin: 15mm 14mm 18mm; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: Tahoma, 'Segoe UI', 'IRANSans', Arial, sans-serif;
            color: #1c1917; font-size: 12px; line-height: 1.5;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        /* Header band */
        .doc-head {
            display: flex; align-items: center; justify-content: space-between;
            gap: 20px; padding-bottom: 14px; margin-bottom: 22px;
            border-bottom: 3px solid #d97706;
        }
        .doc-head .brand { display: flex; align-items: center; gap: 10px; }
        .doc-head .mark {
            width: 34px; height: 34px; border-radius: 9px; background: #d97706;
            color: #fff; font-size: 18px; display: flex; align-items: center; justify-content: center;
        }
        .doc-head .company { font-size: 19px; font-weight: 700; letter-spacing: -0.01em; }
        .doc-head .title { font-size: 12.5px; color: #78716c; margin-top: 2px; }
        .doc-head .meta { text-align: left; font-size: 11px; color: #78716c; white-space: nowrap; }
        .doc-head .meta .label { color: #a8a29e; }

        /* Report table — clean horizontal-rule style, no vertical grid */
        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        thead { display: table-header-group; } /* repeat header on every page */
        thead th {
            background: #f5f5f4; color: #44403c; font-weight: 700;
            font-size: 10.5px; text-align: right; padding: 9px 12px;
            border-bottom: 2px solid #a8a29e; white-space: nowrap;
        }
        tbody td {
            padding: 9px 12px; text-align: right; vertical-align: top;
            border-bottom: 1px solid #e7e5e4; color: #292524;
        }
        tbody tr:nth-child(even) td { background: #fafaf9; }
        tbody tr:last-child td { border-bottom: 2px solid #d6d3d1; }
        tbody td:first-child { font-weight: 600; color: #1c1917; }
        tr { page-break-inside: avoid; }
        a { color: inherit; text-decoration: none; }
        /* Secondary lines inside a cell (phone/email, company under name) */
        td .text-xs, td p { font-size: 10px; color: #78716c; font-weight: 400; margin: 2px 0 0; }

        /* Footer */
        .doc-foot {
            margin-top: 24px; padding-top: 10px; border-top: 1px solid #e7e5e4;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 10px; color: #a8a29e;
        }
    `;

    const html = `<!doctype html><html dir="rtl" lang="fa"><head><meta charset="utf-8">
        <title>${title || company}</title><style>${styles}</style></head><body>
        <div class="doc-head">
            <div class="brand">
                <div class="mark">🪚</div>
                <div>${company ? `<div class="company">${company}</div>` : ''}${title ? `<div class="title">${title}</div>` : ''}</div>
            </div>
            ${date ? `<div class="meta"><span class="label">تاریخ چاپ:</span> ${date}</div>` : ''}
        </div>
        ${clone.outerHTML}
        <div class="doc-foot"><span>${title || ''}</span><span>${company || ''}</span></div>
    </body></html>`;

    const frame = document.createElement('iframe');
    frame.setAttribute('aria-hidden', 'true');
    frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
    document.body.appendChild(frame);

    const doc = frame.contentWindow.document;
    doc.open();
    doc.write(html);
    doc.close();

    // Give the iframe a tick to lay out before invoking the print dialog.
    frame.contentWindow.focus();
    setTimeout(() => {
        frame.contentWindow.print();
        setTimeout(() => frame.remove(), 1000);
    }, 200);
};

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
