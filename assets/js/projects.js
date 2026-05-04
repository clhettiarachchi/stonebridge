/**
 * Projects archive — filter, fetch, URL sync, pagination
 */

const wrapper   = document.querySelector('.projects-listing');
const selType   = document.querySelector('.projects-filters__type');
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
    } catch (err) {
        console.error('sb_filter_projects fetch error:', err);
    } finally {
        setLoading(false);
    }
}

// --- Render ---

function renderResults(html) {
    const parsed = new DOMParser().parseFromString(html, 'text/html');

    // Remove existing grid, pagination and empty state
    wrapper.querySelector('.projects-grid')?.remove();
    wrapper.querySelector('.projects-pagination')?.remove();
    wrapper.querySelector('.projects-empty')?.remove();

    // Inject full partial output
    wrapper.insertAdjacentHTML('beforeend', parsed.body.innerHTML);
}

// --- Pagination click delegation ---

wrapper.addEventListener('click', function (e) {

    const link = e.target.closest('.projects-pagination a');
    if (!link) return;

    e.preventDefault();
    const url   = new URL(link.href);
    currentPage = parseInt(url.searchParams.get('page') ?? 1);
    fetchProjects();
});

// --- URL sync ---

function syncURL() {
    const params = new URLSearchParams();
    if (selType.value)   params.set('type', selType.value);
    if (selStatus.value) params.set('status', selStatus.value);
    if (currentPage > 1) params.set('page', currentPage);

    const query = params.toString();
    const state = {
        type:   selType.value,
        status: selStatus.value,
        page:   currentPage,
    };

    history.pushState({}, '', query ? '?' + query : location.pathname);
}

function readURL() {
    const params = new URLSearchParams(location.search);
    if (params.get('type'))   selType.value   = params.get('type');
    if (params.get('status')) selStatus.value = params.get('status');
    if (params.get('page'))   currentPage     = parseInt(params.get('page'));
}

// --- Loading state ---

function setLoading(state) {
    selType.disabled   = state;
    selStatus.disabled = state;
    wrapper.classList.toggle('is-loading', state);
}

// --- Events ---

selType.addEventListener('change',  () => { currentPage = 1; fetchProjects(); });
selStatus.addEventListener('change', () => { currentPage = 1; fetchProjects(); });

window.addEventListener('popstate', (e) => {

    console.log('state: ', {
        type: e.state.type,
        status: e.state.status,
        page: e.state.page,
    })

     if (e.state) {
        selType.value   = e.state.type ?? '';
        selStatus.value = e.state.status ?? '';
        currentPage     = e.state.page ?? 1;
    } else {
        selType.value   = '';
        selStatus.value = '';
        currentPage     = 1;
    }
    fetchProjects();
});

// --- Init ---

readURL();

// Push initial state so back button can restore it
history.replaceState({
    type:   selType.value,
    status: selStatus.value,
    page:   currentPage,
}, '', location.href);

if (location.search) fetchProjects();