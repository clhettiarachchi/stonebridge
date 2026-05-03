# Stonebridge Construction — Project Listing

**Author:** Chandika Hettiarachchi
**Theme slug:** `stonebridge`
**Version:** 1.0.0

---

## 1. Project overview

Stonebridge Construction is a mid-sized commercial and residential construction company based in Sydney, NSW. Est. 1998. Operates across greater Sydney and regional NSW.

This feature is a filterable, paginated project listing page built as part of the Stonebridge WordPress theme. Visitors can filter projects by type and status, with results loading via AJAX and the URL updating to reflect the current filter state.

---

## 2. Business requirements

### Users

**Prospective client** — wants to see past work relevant to their build type. Filters by project type. Cares about scale and quality.

**Subcontractor** — looks for active or tendering projects. Filters by status. Wants location and project value upfront.

### User stories

| # | Story |
|---|---|
| 1 | As a visitor, I can browse all projects in a paginated listing so I'm not overwhelmed at once. |
| 2 | As a prospective client, I can filter by project type so I only see work relevant to my build. |
| 3 | As a subcontractor, I can filter by status so I can find active or tendering projects quickly. |
| 4 | As any visitor, I can apply both filters at once to narrow results further. |
| 5 | As a visitor, I can copy the URL of a filtered result and share it — the recipient sees the same view. |
| 6 | As a visitor, if no projects match my filters I see a clear message rather than a blank page. |

### Filter behaviour

- Default state: all projects shown, both filters set to "All"
- On filter change: results update immediately, page resets to 1
- Pagination: 6 projects per page, numbered pages
- URL reflects current state at all times: `?type=commercial&status=completed&page=2`

### Out of scope

Individual project detail page, keyword search, sort order control, admin-facing project management UI.

---

## 3. Content model

### Custom post type — `sb_project`

| Field | Type | Notes |
|---|---|---|
| Title | Post title | Project name |
| Description | Post excerpt | Short summary shown on card |
| Hero image | Featured image | Displayed on card |
| Location | Post meta | Suburb, NSW |
| Year | Post meta | Completion or expected year |
| Value | Post meta | Project value in AUD |

### Taxonomy — `sb_project_type`

| Term | Description |
|---|---|
| Commercial | Office buildings, retail centres, warehouses, mixed-use |
| Residential | Custom homes, townhouses, apartment complexes |
| Infrastructure | Roads, bridges, stormwater, civil earthworks |
| Renovation & fitout | Commercial fitouts, heritage restorations, extensions |

### Taxonomy — `sb_project_status`

| Term | Colour |
|---|---|
| Completed | Green |
| In progress | Blue |
| Planning | Amber |
| Tendering | Purple |
| On hold | Red |

### Sample data (5 projects)

| Name | Type | Status | Location | Year | Value |
|---|---|---|---|---|---|
| Parramatta Square Tower C | Commercial | In progress | Parramatta | 2024 | $42M |
| Manly Cove Residences | Residential | Completed | Manly | 2023 | $8.5M |
| Old Hawkesbury Bridge Restoration | Infrastructure | On hold | Windsor | 2025 | $3.2M |
| Surry Hills Creative Hub | Renovation & fitout | Completed | Surry Hills | 2023 | $1.8M |
| Campbelltown Logistics Park | Commercial | Planning | Campbelltown | 2026 | $67M |

---

## 4. File & folder structure

```
stonebridge/
│
├── style.css                          # Theme header — name, version, author
├── functions.php                      # Enqueues, includes
├── index.php                          # Fallback template
├── header.php                         # wp_head(), site header markup
├── footer.php                         # wp_footer(), site footer markup
├── page.php                           # Default page template
├── page-projects.php                  # Projects listing page template
│
├── template-parts/
│   └── project-card.php               # Single project card partial
│
├── inc/
│   ├── cpt-projects.php               # CPT + taxonomy registration
│   └── ajax-projects.php              # AJAX handler — WP_Query + return partial
│
├── assets/
│   ├── scss/
│   │   └── projects.scss              # BEM source styles
│   ├── css/
│   │   └── projects.css               # Gulp output — do not edit directly
│   └── js/
│       └── projects.js                # Vanilla JS — fetch, URL sync, pagination
│
├── gulpfile.js
└── package.json
```

---

## 5. Technical approach

### CPT & taxonomies
Registered in `inc/cpt-projects.php`, included via `functions.php`. Uses `init` hook. Both taxonomies are hierarchical (like categories).

### AJAX pattern
- JS sends `fetch()` POST to `admin-ajax.php` with action, filters, page number, and nonce
- PHP handler in `inc/ajax-projects.php` verifies nonce, builds `WP_Query` with `tax_query`, loads `project-card.php` partial via `get_template_part()`, returns HTML
- JS injects the response HTML into the listing container

### WP_Query — tax_query structure
When both filters are active, `tax_query` uses `relation => AND`. When only one filter is active, a single `tax_query` clause is used. When neither filter is active, no `tax_query` is passed.

### URL sync
`URLSearchParams` builds the query string from current filter state. `history.pushState` updates the URL without a page reload. On page load, JS reads URL params and pre-selects filters, then fires an initial AJAX request if params are present.

### Security
- Nonce generated with `wp_create_nonce()`, passed to JS via `wp_localize_script()`
- Verified in the AJAX handler with `check_ajax_referer()`
- All output escaped with `esc_html()`, `esc_attr()`, `esc_url()`
- Post meta sanitised with `sanitize_text_field()` on save

### Enqueuing
CSS and JS enqueued only on the projects page using `is_page_template('page-projects.php')`.

---

## 6. Milestones

Each milestone ends in a testable, working state.

### Milestone 1 — Theme scaffold
- Create theme folder with `style.css` (valid WP theme header — name, author, version)
- Create blank `index.php` and `functions.php`
- Create `header.php` — minimal HTML document open, `wp_head()` call
- Create `footer.php` — `wp_footer()` call, HTML document close
- Create `page.php` — calls `get_header()`, `the_content()`, `get_footer()`
- Activate theme in LocalWP
- Verify: theme appears in WP Admin → Appearance → Themes, activates without errors, a blank page renders without PHP warnings

### Milestone 2 — CPT & taxonomies
- Create `inc/cpt-projects.php` — register `sb_project` CPT and both taxonomies
- Include file in `functions.php`
- Add 5 sample projects with all fields populated
- Verify: Projects menu appears in WP admin, sample data is visible

### Milestone 3 — Static project listing
- Create `page-projects.php` and assign it to a new "Projects" page
- Write a basic `WP_Query` loop
- Create `template-parts/project-card.php` — output title, type, status, location, year, value
- Verify: projects listing page shows all 5 cards with correct data

### Milestone 4 — Gulp + SCSS setup
- `npm init -y` and install `gulp`, `gulp-sass`, `sass`
- Write `gulpfile.js` with compile and watch tasks
- Write `projects.scss` with BEM structure — card layout, status badge colours, responsive grid
- Enqueue compiled CSS in `functions.php`
- Verify: styles apply correctly on the listing page

### Milestone 5 — Filter bar (static)
- Add two `<select>` dropdowns to `page-projects.php` — populated from taxonomy terms via `get_terms()`
- No JS yet — filters do nothing at this point
- Verify: dropdowns render with correct taxonomy terms

### Milestone 6 — AJAX handler (PHP)
- Create `inc/ajax-projects.php`
- Register `wp_ajax_nopriv_sb_filter_projects` and `wp_ajax_sb_filter_projects` actions
- Handler verifies nonce, builds `WP_Query` with `tax_query` and pagination args, returns card HTML
- Verify: test handler directly with a tool like Postman or browser fetch in console

### Milestone 7 — AJAX fetch (JS)
- Create `assets/js/projects.js`
- On filter change: collect filter values, send `fetch()` POST to `admin-ajax.php`, inject response into listing container
- Add loading state: disable filters, show overlay on grid during request
- Verify: changing a filter updates results without page reload

### Milestone 8 — URL sync & pagination
- On every fetch: build query string with `URLSearchParams`, update URL with `history.pushState`
- On page load: read URL params, pre-select filters, fire initial fetch if params present
- Add numbered pagination to AJAX response, handle page clicks via JS
- Verify: filtered URL is shareable and restores correct state on load; browser back button works

### Milestone 9 — Edge cases & review
- Empty state: show "No projects found" message when query returns zero results
- Out of stock guard: hide pagination when only one page exists
- Escaping audit: review all output in PHP partials
- Verify: run through Query Monitor — zero PHP notices or warnings

---

## 7. Gulp setup

### Install

```bash
cd stonebridge
npm init -y
npm install --save-dev gulp gulp-sass sass
```

### `gulpfile.js`

```js
const { src, dest, watch, series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));

function scss() {
  return src('assets/scss/projects.scss')
    .pipe(sass({ outputStyle: 'compressed' }).on('error', sass.logError))
    .pipe(dest('assets/css'));
}

function watchFiles() {
  watch('assets/scss/**/*.scss', scss);
}

exports.default = series(scss, watchFiles);
exports.build   = scss;
```

### Commands

| Command | What it does |
|---|---|
| `npx gulp` | Compile once, then watch for changes |
| `npx gulp build` | Compile once — run before every commit |

---

## 8. WordPress best practices checklist

- [ ] All output escaped — `esc_html()`, `esc_attr()`, `esc_url()`
- [ ] All post meta sanitised on save — `sanitize_text_field()`, `absint()`
- [ ] Nonce verified in AJAX handler — `check_ajax_referer()`
- [ ] No direct `$wpdb` queries — WP_Query and WP APIs only
- [ ] CSS enqueued only on projects page — `is_page_template()`
- [ ] JS passed data via `wp_localize_script()` — no inline PHP in JS files
- [ ] CPT and taxonomy slugs prefixed — `sb_` to avoid conflicts

---

## 9. Local dev setup

1. Create a new site in LocalWP
2. Place the `stonebridge` folder in `wp-content/themes/`
3. Activate the theme from WP Admin → Appearance → Themes
4. Install and activate WooCommerce is NOT needed — this is a standalone theme
5. Create a page titled "Projects" and assign the Projects page template
6. Add 5–10 sample projects via WP Admin → Projects with varied types and statuses
7. Install **Query Monitor** — use it to inspect queries and catch errors during development

---

## 10. Commit guidelines

Format: `type: short description` — lowercase, present tense, under 60 characters.

| Type | When to use |
|---|---|
| `feat` | New feature or milestone complete |
| `fix` | Bug fix |
| `style` | SCSS/CSS changes only |
| `refactor` | Code restructure, no behaviour change |
| `chore` | Config, deps, build tooling |

**Examples:**
```
feat: register sb_project CPT and taxonomies
feat: static project listing with card partial
feat: gulp scss compile and watch tasks
feat: static filter bar with taxonomy terms
feat: ajax handler with tax_query and nonce
feat: vanilla JS fetch and loading state
feat: url sync with history.pushState
feat: numbered ajax pagination
fix: hide pagination on single page results
style: status badge colours and card grid
chore: add gulp scss compile task
```

---

## 11. GitHub setup

- Repo name: `stonebridge`
- Visibility: Public
- `.gitignore` must exclude `node_modules/`
- Run `npx gulp build` before every commit
- README should include screenshots of the listing page (filtered and unfiltered) and the admin project entry

---

## 12. How to use this brief

Paste this entire file at the start of a new chat and say:

> "I'm building this project step by step. Let's start at Milestone 1. Explain what we're doing, then give me the code."