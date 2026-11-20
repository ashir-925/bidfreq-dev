# BidFreq handoff

Workspace: `D:/laragon/www/github/bidfreq-dev`. Branch: `main`. Source of truth: `BidFreq_SOW_v2.7.md` at the repository root. The estimate workflow and latest UI fixes include uncommitted and untracked files. This handoff used source inspection and read-only database queries.

## 1. Progress

| SOW §9 step | Status | Detail |
|---|---|---|
| 1. Authentication and panels | Done and verified | Role-based access, account management, verification, reset and Admin seeding have automated coverage. |
| 2. Pricing Library | Done and verified | Preview/apply, versioned edits, history, export and CSV/JSON round-trip tested. Local DB has 162 tasks and one import record. |
| 3. Request intake and pricing | Done and verified | Validated uploads, measurements, priority, confirmation, decimal pricing and NTE checks are covered. |
| 4. Queued AI analysis | Partly done | Queued analysis, retries, validation and manual fallback are tested with mocks. Live matching and quantity-basis issues remain. |
| 5. Admin inbox and review | Partly done | Inbox, edits, evidence, approval, rejection and follow-up are functional and tested. Exact reference parity is open. |
| 6. Customer delivery and decisions | Partly done | Released PDF, version history, approval, rejection and revision exist. Exact reference parity and live email delivery need verification. |
| 7. Projects and assignments | Not started | Project screens are templates. Customer approval records the decision and does not create a project. |
| 8. Execution | Not started | No daily reports, Change Orders, walkthrough or certification workflow. |
| 9. Invoice and payment status | Not started | No invoice, receipts, refunds, balance or overdue workflow. |
| 10. Audit, testing and deployment | Partly done | Library and estimate audit records and tests exist. Full project audit, production readiness and deployment remain. |

Overall: **about 55% of the full SOW**. Core estimating is substantially built. Project execution and billing are outstanding.

## 2. What exists

- **Tables:** `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `library_versions`, `library_tasks`, `library_task_changes`, `library_reference_labor_rates`, `library_import_previews`, `repair_requests`, `repair_request_files`, `repair_ai_attempts`, `repair_drafts`, `repair_draft_lines`, `repair_estimate_releases`, `repair_estimate_events`. Laravel also has `migrations`.
- **Auth routes/screens:** `/`, `/login`, `/register`, `/forgot-password`, `/reset-password`, `/verify-email`, verification resend and logout.
- **Admin:** `/admin/dashboard`, `/admin/users` for list/create/status, `/admin/estimates` and `/admin/request-inbox` for queue/review. Request actions cover save, release, retry AI, manual draft, NTE, disposition and protected attachments. Projects, Customers, Estimators, Reports, Notifications and Profile routes also render existing screens.
- **Customer:** `/user/dashboard`, `/user/new-estimate-request`, `/user/estimate-requests` and request details. Request actions cover submission, protected attachments, follow-up, decision and versioned PDFs. Projects, Project Detail, Notifications and Profile routes also render existing screens.
- **Library:** `/admin/library` with list/search, task detail/edit/history, upload/preview/apply and CSV/JSON export.
- **Accounts verified in the local DB:** Admin `admin@example.com`. No Super Admin row. `AdminSeeder` is idempotent and skips Super Admin when all four corresponding settings are blank.
- **Queue:** Laravel database queue for estimate jobs and mail notices. AI job timeout is 90 seconds. Database retry timeout defaults to 150 seconds. AI defaults to five attempts with backoff of 1, 2, 5 and 10 minutes. Uploads use private local storage.
- **AI:** Google Gemini, `gemini-3.1-flash-lite` (configured default in `config/estimate_requests.php`).

## 3. UI state

All four Blade pages differ from the reference. These are the known differences from source inspection and prior desktop captures. A fresh full responsive comparison was not run for this handoff.

| Reference and Blade | Known differences |
|---|---|
| `HTML/admin/estimates.html` → `admin/pages/estimates.blade.php` | Live counts, request UUIDs, server filters and pagination, updated captions, filter controls and status labels. Adds Priority, NTE and Submitted columns and operational flags. Reference Estimator/SLA columns, selection checkboxes, bulk actions, CSV export and row menus are absent. |
| `HTML/admin/estimate-review.html` → `admin/pages/estimates_review.blade.php` | Live request UUID, draft data and attachments. Line editor, Library picker, 4Cs, evidence controls, NTE, decision forms, totals and notes differ in placement or markup. Reference edit icons and edited-row treatments, draft PDF preview, Return to AI placement, SLA display, sample confidence breakdown and customer statistics are absent or different. Retry is available only in eligible states. Live Admin sidebar has extra operational links. |
| `HTML/estimate-requests.html` → `front/pages/estimate_request.blade.php` | Live status counts and request UUIDs, priority column, server search/filter/sort and pagination, updated header copy, and simple View actions. Reference selection checkboxes, bulk/PDF actions, CSV export, sortable-header arrows and row menus are absent. |
| `HTML/estimate-detail.html` → `front/pages/estimate_request_detail.blade.php` | Live request UUID and conditional release metadata instead of the sample estimate ID, verifier, sent and validity metadata. Scope uses released snapshot rows with a simpler table and breakdown. Assumptions/exclusions are conditional. Evidence uses the shared card with original file IDs and links. Reference risk flags, confidence widget, named evidence tiles/View all, and illustrated timeline are absent or different. Version/PDF history, follow-up form and decision dialogs use live state. |

Admin CSS: `public/admin_assets/css/styles.css`. Customer CSS: `public/front_assets/css/styles.css` and `public/front_assets/pages_css/{estimate_request,estimate_request_detail,new_estimate_request}.css`. Shared feedback: `public/front_assets/css/feedback.css`. These page styles are served directly from `public`.

## 4. Known problems

- **Admin review evidence fixed.** The old multi-select options repeated every upload under every C. Gemini did not supply per-C links then. Current Blade shows only linked chips, uses request-local numbers and supports adding/removing links. The anchor scrolls and the photo preview opens. Other requests' files are rejected. Existing drafts are not backfilled, non-image attachments download, and the customer detail still uses the old shared evidence display.
- **Exact UI parity is open.** The Admin review was compared at 1440 px and interactions passed. The four-page differences above remain.
- **Invalid quantity basis is open.** Three current draft lines carry the flag. In `GeminiEstimateProvider.php`, the schema types `quantity_basis` as a string. `DraftPricing::BASES` enforces the four SOW tokens. Inspect the saved attempt before calling it a validator bug. The reported line's raw token was not verified here.
- **Paint touch-up is open.** The Library contains `BF-T-006`, Interior Paint Touch-Up — Partial, per home (flat). No current draft line uses it. The current prompt sends the full catalog and requires exact matches. The wall-hole case has no dedicated regression.
- **Migrations:** all seven are applied in the current local DB, including `2026_11_20_000003_add_estimate_review_tables`. A fresh database still needs migrations.
- One local Library import record was verified. Tests import the actual CSV and verify the JSON round-trip. A separate live JSON import was not verified. Project creation and later workflows remain open.

## 5. Decisions and defaults

- §11 items 1–6: Optional customer NTE with logged Admin edits. Overhead/profit is included. Quote-by-scope rates are entered per estimate by Admin. One global rate set, with ZIP stored and no ZIP adjustment. No customer-specific rates. Home area and stories required, damaged-area dimensions optional, missing required quantities flagged.
- Items 7–12: Admin and Super Admin can version/edit the Library. Customers see released item rates. Future Change Order approval is Admin-only. Advance payment is deferred. Priority defaults to Normal among Low/Normal/High/Urgent. USD, with no tax calculation.
- Items 13–20: No separate materials/overhead lines. Reference ranges, hours, cost and pricing trace appear in review. No physical inspection step before the completion walkthrough. No separate labor CSV requirement. Simple BidFreq PDF branding and basic company profiles. Section 7 emails, with project completion email pending. Wide-range tasks use their fixed CSV rates and show reference ranges. The eight ambiguous units require confirmation.
- §11 asks the client to confirm items 1, 4, 5, 8, 9, 13, 16, 19 and 20. Implementation choices include UUID request references, private local files, request-local attachment numbering and editable per-C links. Development uses the free AI setup per the user. The SOW calls for a separate production AI decision.

## 6. How to run

1. Install PHP 8.3+, Composer, MySQL and Node. Run `composer install`, `npm ci` and `npm run build`. For a fresh install, run `Copy-Item .env.example .env`, fill the settings privately and run `php artisan key:generate`. Retain the existing `APP_KEY` when moving an existing environment.
2. Environment names: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`, optional `DB_QUEUE_CONNECTION` and `DB_QUEUE_RETRY_AFTER`, `MAIL_MAILER`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, and for SMTP `MAIL_SCHEME`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`. Staff: `ADMIN_FIRST_NAME`, `ADMIN_LAST_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `SUPER_ADMIN_FIRST_NAME`, `SUPER_ADMIN_LAST_NAME`, `SUPER_ADMIN_EMAIL`, `SUPER_ADMIN_PASSWORD`. AI: `GEMINI_API_KEY`, `GEMINI_MODEL`, `AI_MAX_ATTEMPTS`.
3. Run `php artisan migrate` and `php artisan db:seed --class=AdminSeeder`. The seeder requires valid staff details and distinct passwords of 16–72 bytes.
4. Preview with `php artisan bidfreq:library-import bidfreq-pricing-data-v3.csv --email=admin@example.com`. Add `--apply` to commit. Use the same command with `bidfreq-pricing-data-v3.json` for the JSON source.
5. Start the app with `php artisan serve`. In a separate terminal run `php artisan queue:work database --timeout=90`. Restart the worker after code changes. Use log mail locally unless intentionally testing delivery.
6. For tests, create a private `.env.testing` with its own valid `APP_KEY`, testing environment, SQLite in-memory database, empty `DB_URL` and no live AI credentials. Clear any cached app config locally before testing and confirm the test connection is isolated. Run `php artisan test --env=testing`. Keep Gemini HTTP faked.

## 7. Tests

Last run: **36 passed, 0 failed, 533 assertions** using `.env.testing`, SQLite in memory and mocked Gemini. Prior Chrome smoke checks passed for the Admin preview, evidence link controls, picker and scrolling. The handoff did not rerun tests.

Missing coverage includes live wall-hole matching, a recorded fixture for the invalid basis and `BF-T-006`, pixel comparisons across all four pages and screen sizes, production mail/worker setup, and end-to-end tests for steps 7–10.

## 8. Next steps

1. Reproduce the wall-hole case safely. Inspect the stored AI attempt, add a mocked regression, verify the SOW basis tokens and `BF-T-006` matching, and confirm the Library-only total.
2. Finish visual parity on the four estimate pages against the exact HTML references. Verify the Admin evidence flow on an existing draft and a new result. Finish customer delivery checks.
3. Start step 7 only after user approval. Continue through execution, invoicing, audit and deployment in SOW order.
4. Preserve the HTML references and approved layouts. Preserve Library-only rates, imported units, decimal math, historical snapshots, and the written Admin override rule. Keep evidence and drafts private. Never expose secrets or run tests against the development database.

## 9. Reminder before switching

- Review `git status --short`, then stage and commit the intended modified **and untracked** estimate files with this handoff. Push to the approved remote if the next account needs access.
- Copy `.env` and any local `.env.testing` securely outside Git. `.env.testing` is not explicitly ignored in the current `.gitignore`. Verify it is excluded before staging. Confirm the next agent can access the repo and the final SOW.
- If moving machines and preserving existing requests, privately transfer the development DB and matching `storage/app/private` files. Keep the same `APP_KEY` for the existing environment.
