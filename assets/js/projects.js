/**
 * Projects archive — filter, fetch, URL sync, pagination
 */

const grid    = document.querySelector('.projects-grid');
const wrapper = document.querySelector('.projects-listing');
const selType = document.querySelector('.projects-filters__type');
const selStatus = document.querySelector('.projects-filters__status');

let currentPage = 1;

// --- Fetch ---

async function fetchProjects() {
    setLoading(true);

    const params = new URLSearchParams({
        action: 'sb_filter_projects',
        nonce:  sbProjects.nonce,
        type:   selType.value,
        status: selStatus.value,
        page:   currentPage,
    });

    try {
        const res  = await fetch(sbProjects.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:   params.toString(),
        });
        const html = await res.text();

        renderResults(html);
        syncURL();
        renderPagination();
    } catch (err) {
        console.error('sb_filter_projects fetch error:', err);
    } finally {
        setLoading(false);
    }
}

// --- Render ---

function renderResults(html) {
    // Strip the injected script tag, capture total pages
    const parsed = new DOMParser().parseFromString(html, 'text/html');
    const script = parsed.querySelector('script');

    if (script) {
        eval(script.textContent); // sets window._sbTotalPages
        script.remove();
    }

    const empty = parsed.querySelector('.projects-empty');

    if (empty) {
        wrapper.querySelector('.projects-grid')?.remove();
        wrapper.querySelector('.projects-pagination')?.remove();

        if (!wrapper.querySelector('.projects-empty')) {
            wrapper.insertAdjacentHTML('beforeend', empty.outerHTML);
        }
        return;
    }

    // Clear empty state if present
    wrapper.querySelector('.projects-empty')?.remove();

    // Replace or create grid
    let gridEl = wrapper.querySelector('.projects-grid');
    if (!gridEl) {
        gridEl = document.createElement('div');
        gridEl.className = 'projects-grid';
        wrapper.prepend(gridEl);
    }
    gridEl.innerHTML = parsed.body.innerHTML.replace(/<script[\s\S]*?<\/script>/gi, '');
}

function renderPagination() {
    wrapper.querySelector('.projects-pagination')?.remove();

    const total = window._sbTotalPages ?? 1;
    if (total <= 1) return;

    const nav = document.createElement('nav');
    nav.className = 'projects-pagination';

    for (let i = 1; i <= total; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className   = 'projects-pagination__btn' + (i === currentPage ? ' is-active' : '');
        btn.addEventListener('click', () => {
            currentPage = i;
            fetchProjects();
        });
        nav.appendChild(btn);
    }

    wrapper.appendChild(nav);
}

// --- URL sync ---

function syncURL() {
    const params = new URLSearchParams();
    if (selType.value) params.set('type', selType.value);
    if (selStatus.value) params.set('status', selStatus.value);
    if (currentPage > 1) params.set('page', currentPage);

    const query = params.toString();
    history.pushState({}, '', query ? '?' + query : location.pathname);
}

function readURL() {
    const params = new URLSearchParams(location.search);
    if (params.get('type'))   selType.value = params.get('type');
    if (params.get('status')) selStatus.value = params.get('status');
    if (params.get('page'))   currentPage   = parseInt(params.get('page'));
}

// --- Loading state ---

function setLoading(state) {
    selType.disabled = state;
    selStatus.disabled = state;
    wrapper.classList.toggle('is-loading', state);
}

// --- Events ---

selType.addEventListener('change', () => { currentPage = 1; fetchProjects(); });
selStatus.addEventListener('change', () => { currentPage = 1; fetchProjects(); });

window.addEventListener('popstate', () => {
    readURL();
    fetchProjects();
});

// --- Init ---

readURL();
if (location.search) fetchProjects();