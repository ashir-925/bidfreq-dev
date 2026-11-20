# BidFreq — Statement of Work (SOW)

**AI-Assisted Repair Estimating & Project Management Platform**

| | |
|---|---|
| Client | BidFreq LLC (Sean Fuller, Managing Member) |
| Document version | 2.7 (consolidates the client's SOW v1.0, the client's "three priorities" email, Pricing Sheet v3, and all later scope decisions) |
| Backend | Laravel 13 (PHP 8.3+), MySQL |
| Frontend | Already prepared; backend is built and connected to it |
| Delivery | As fast as possible; build order in Section 9 |
| Marker | **[CONFIRM]** = needs client confirmation before that part is finalized |

---

## 1. Project Overview

BidFreq turns a customer's repair request (photos, description, measurements) into a structured estimate. An Admin verifies the estimate, the customer approves it, and the approved estimate becomes a project that the Admin runs through daily progress, additional work, completion and final invoice.

**Core rule: every price, rate and total comes from BidFreq's Standard Repair Time & Cost Library, calculated by Laravel. AI never creates, guesses or estimates a price. The only other source is a deliberate Admin entry: a rate for a quote-by-scope task, or a logged override with a reason.** AI reads the evidence and decides *which Library tasks are needed and how much of each*. Laravel code calculates the price from the Library.

## 2. Roles

| Role | Who | Access |
|---|---|---|
| **Super Admin** | The developer | Access to everything, including system settings and all Admin functions. Not a business user; not shown in business screens. Created by seeder/command only. No public registration. |
| **Admin** | BidFreq staff | Admin panel: Library, request inbox, estimate review, projects, users. |
| **Customer** | Property managers/owners (called "user" in discussions) | Customer panel: requests, estimates, decisions, project status. |

- Customers self-register. Admins are created by the Super Admin or an Admin.
- Each role has its own panel after login.
- **Estimators, subcontractors, crews and the Project Manager do not log in** (no portal or dashboard for now). They report to the Admin manually (for example by message or photo), and the Admin records everything in the system.
- **Term note:** the client's SOW calls the person who verifies AI estimates the "estimator". In this build that verification is done by the **Admin**. A **Project Estimator** (Section 5, Module 9) is a field contact assigned to a project by the Admin; the Admin alone can see the assignment.

## 3. Guiding Principles

| # | Principle |
|---|---|
| P1 | Every price is calculated by Laravel from the Library. AI produces no pricing figures (no price, rate, cost or total); it does produce quantities and a confidence score. The only other source of a price is an Admin entry: a quote-by-scope rate, or a deliberate override of a line rate (reason required, logged, original Library rate kept and shown). |
| P2 | AI may only select tasks that exist in the Library, by `task_id`. A task not found is flagged **Unmatched**; AI never substitutes a "similar" task. |
| P3 | If evidence is insufficient for a task or quantity, it is flagged for the Admin. The system never treats an assumption as confirmed damage. |
| P4 | Prices are calculated by Laravel code, never by the AI model. |
| P5 | Rates used in an estimate or Change Order are saved with it (snapshot). Library changes never alter existing estimates. |
| P6 | Every Admin edit, approval and Library change is logged (who, when, old value, new value). |
| P7 | AI output is always a draft. Nothing reaches a customer without Admin approval. |
| P8 | Invoice amounts come only from approved totals (original estimate plus approved Change Orders). |
| P9 | Customer submission is instant. AI work happens in the background; the customer never waits on it. |

## 4. End-to-End Workflow

```
1. Customer registers / logs in (own panel)
2. Customer submits a request: property address, ZIP, description, photos
   (min 2), home size, stories, damaged-area dimensions, priority, optional NTE
   → saved instantly; customer sees "request received / under review";
     confirmation email sent
3. Background queue picks up the request → AI analysis
   → 4Cs + Library tasks + quantities + assumptions/risks/confidence
4. Laravel validates the AI output against the Library and calculates
   all prices from the Library → estimate saved as DRAFT
   (customer sees nothing yet)
5. Admin opens the request inbox (sorted by customer priority), reviews the
   draft, fixes mistakes, resolves flags, approves
6. Estimate (PDF + dashboard) is sent to the customer
7. Customer: Approve | Reject | Request revision
      Revision → back to Admin → new estimate version → customer again
      Approve  → Project is created
8. Admin assigns an Estimator (field contact) and sets dates
9. Work runs. The Estimator reports daily progress and photos to the Admin
   manually; the Admin records daily reports + photos in the project
10. Extra work found → Admin creates a Change Order, priced from the Library,
    kept separate from the original estimate
11. Walkthrough → completion certificate → project completed
12. Final invoice → payments recorded → paid
```

## 5. Functional Modules

### Module 1 — Authentication & Users
Registration and login for customers, login for Admin and Super Admin, logout, password reset, email verification. Role-based access with policies on every module. The Super Admin passes all access checks. Admin user management (list, create, deactivate). Basic company information stored on the customer profile (company name, contact details). A separate multi-user Organization structure is not included. **[CONFIRM]**

### Module 2 — Pricing Library (first business module after authentication)
**Source data:** Pricing Sheet v3 (effective 2026-11-10): `bidfreq-pricing-data-v3.csv` and `.json` (identical data, 162 tasks, `BF-T-001` to `BF-T-162`) and the XLSX. The SOP PDF is an older version (v1, 78 tasks); where it conflicts with v3, v3 applies.

**Nature of the data:** a client-rate library built from rates Evergreen Residential actually paid BidFreq. The standard rate is a revenue rate per unit that **already includes materials and margin.** It is not a labor rate and not a retail price list.

**Database:** library tables created by migrations (tasks, library versions, change history, and reference labor rates).

**Import / Export (Admin panel):**
- **Import** from CSV or JSON (same fields). Validate the file, show a preview (new tasks, changed rates, unchanged, errors), then apply in a transaction. Tasks are matched by `task_id`. Each import creates a library version record with the file name, user and date.
- **Export** the current Library to CSV and JSON in the same format as the import, so an exported file can be re-imported.
- Admin screens: list, search, filter, view, edit a task, with change history for every edit.

**Fields used (from the CSV):** `task_id`, `task_name`, `unit`, `evergreen_low`, `evergreen_typical`, `evergreen_high`, `times_seen_scopes`, `bidfreq_standard_rate`, `standard_hours`, `hours_basis`, `labor_cost_market_avg`, `source`, `effective_date`, `version`, `last_updated` (15 fields in total). All 15 fields are imported and exported, so an exported file re-imports unchanged.

| Field | Use |
|---|---|
| `task_id` | Permanent key. AI returns it. Estimates store it. Never renumbered. |
| `unit` | The unit the quantity must use. |
| `bidfreq_standard_rate` | **The price per unit. The only Library value used to price a task** (Admin entries for quote-by-scope tasks and logged overrides are the only alternatives). |
| `evergreen_low/typical/high`, `times_seen_scopes` | Reference range and confidence shown to the Admin (a task seen once is provisional). |
| `standard_hours`, `labor_cost_market_avg` | **Reference only**, shown to the Admin. Provisional estimates, never used in price. |

**Special cases:**
- **Seven tasks are "Quote by scope"** (no fixed rate): `BF-T-070`, `BF-T-077`, `BF-T-078`, `BF-T-079`, `BF-T-095`, `BF-T-100`, `BF-T-123`. They are stored with `pricing_mode = quote_by_scope`; any "reference" figure in the rate text is stored as a reference rate, never as the price. The **Admin enters the rate** manually per estimate.
- **22 unit types**, some ambiguous ("each (as scoped)"). Imported as-is and shown to the Admin; not reinterpreted.
- **Always use each task's own imported unit.** For example, interior paint tasks (`BF-T-001`, `BF-T-002`, `BF-T-003`, `BF-T-004`, `BF-T-140`) are priced per sq ft of **floor area** (whole-home), not wall area; touch-up tasks (`BF-T-005`, `BF-T-006`) are per home (flat); Exterior Door Paint (`BF-T-018`) is per door side; Cabinet Painting (`BF-T-150`) has the unit `each` (the task name says "per box", but the `unit` field is `each`; the imported `unit` field always decides).
- **How to read the fields (verified from the v3 workbook):** `evergreen_typical` is the **median** of all observed line items for the task. `times_seen_scopes` counts **distinct purchase-order scopes**, not lines. `standard_hours` are provisional planning estimates, understood per listed unit (the cross-check divides the per-unit rate by an hourly rate). `labor_cost_market_avg` = `standard_hours` × $30.11 (average of four markets' burdened turn-tech rate: BLS OEWS May 2025 wages + 25% burden), before materials. These are planning figures only.
- **Ambiguous units (8 tasks):** `BF-T-027`, `BF-T-093`, `BF-T-094`, `BF-T-112`, `BF-T-117`, `BF-T-125`, `BF-T-126`, `BF-T-145` (for example "each (as scoped)", "per wet area", "per unit"). Imported as-is. Any line using one of them is automatically flagged "confirm unit and quantity" for the Admin, and the AI may select them only with stated evidence. **[CONFIRM]** their meaning.
- **Flat units** (`per home (flat)`, `per job (flat)`, `per load (flat)`, `per occurrence (flat)`, `per side (flat)`): quantity is normally 1. The AI may return more than 1 only when the evidence shows more (for example two loads); the Admin can edit. Price is always quantity × rate.
- **Wide-range tasks:** the Pricing Sheet guidance says tree trimming, ceiling repair, fence repair and flooring repair should be quoted by scope, but the CSV gives fixed rates for `BF-T-072` (Tree Trimming — General), `BF-T-074` (Ceiling — General Repair) and `BF-T-071` (Fence/Gate — Repair); only `BF-T-070` and `BF-T-077` are quote-by-scope. Default: use the CSV (fixed rate) and show the low/typical/high range prominently to the Admin. **[CONFIRM]** (Section 11, item 19).
- The Library has **no material prices, no overhead/profit settings and no ZIP-to-region mapping.** The Labor Rates by Market tab (4 markets: Augusta, Raleigh, Tampa, Atlanta) exists only in the XLSX and is reference only. **[CONFIRM]** whether a separate labor-rates file will be provided.

### Module 3 — Property & Request Intake
| Input | Rule |
|---|---|
| Property address, ZIP | Required |
| Description of the problem | Required |
| Photos | **Required, minimum 2**; more allowed. Cannot submit without them. |
| Total home square footage | Required (paint, cleaning, pressure wash and similar tasks need it). **[CONFIRM]** |
| Number of stories | Required. **[CONFIRM]** |
| Damaged-area dimensions (label + length/width/height or area, one row per area) | Customer enters. **[CONFIRM]** which are mandatory |
| **Priority** | Customer selects: Low, Normal (default), High, Urgent. **[CONFIRM]** the levels |
| Not-to-Exceed (NTE) amount | Optional on the form; Admin can add or edit. **[CONFIRM]** who provides it |
| Videos, voice notes, inspection reports, other documents | Optional |

Customer-entered dimensions are **inputs, not verified facts**; the Admin can correct them.

**Submission behavior:** the form saves and returns **immediately**, with no waiting for AI. The customer sees a confirmation page and receives a confirmation email. Suggested wording: *"Thank you! Your request {number} has been received and is now under review. We will email you as soon as your estimate is ready."* The customer never sees the AI draft.

Customer-facing request statuses: Submitted → Under Review → Estimate Ready → Approved / Revision Requested / Rejected → In Progress → Completed.

### Module 4 — AI Analysis (background queue)
A queued job starts automatically after submission.

**Input to AI:** photos, description, home size, stories, damaged-area dimensions, optional documents, and the **Library task list** (`task_id`, name, unit). The Library has 162 tasks, so the whole list is passed in the prompt; no vector database or fine-tuning is needed.

**Required structured output (JSON, validated):**
- 4Cs: Complaint, Cause, Correction, Complications.
- Work items: `task_id` (must exist), `quantity`, `unit` (must equal the Library unit), `quantity_basis` (`customer_measurement`, `counted_from_photo`, `calculated_from_customer_measurement`, `cannot_determine`), and `evidence` (which photos/inputs support it).
- Unmatched work (needed but not in the Library), described in plain words.
- Assumptions, risk flags, confidence score.
- Plain statement of uncertainty where evidence is not enough.

**AI must not output any price, rate, cost or total.** If it does, only those fields are discarded; the rest of a valid response is used. The raw response, including the discarded fields, is stored.

**Validation boundary (what is retried and what is flagged):**
- **Whole-response failure → retry:** provider error, timeout or rate limit; response is not valid JSON; response is missing a required top-level part (the four Cs, the items list, the unmatched list, assumptions, risk flags, confidence) or has wrong top-level types.
- **Item-level problem → flag for the Admin, no retry:** unknown `task_id`; unit different from the Library unit; invalid `quantity_basis`; quantity missing, zero or negative.
- **How an undeterminable item is represented:** `quantity_basis = cannot_determine` with `quantity = null`. It is shown to the Admin, excluded from pricing, and counted in the flags.
- Every AI request and response is stored.

**Failure handling and retry limit:**
- The maximum number of attempts is **configurable** (`AI_MAX_ATTEMPTS`, default **5**; it can be set to 4 or 5 or another value without changing code).
- An attempt counts as failed when the provider errors, times out or rate-limits, the response is not valid JSON, or the response fails schema validation (missing required parts).
- A failed attempt is **automatically re-sent** with the same request data and an increasing delay (for example 1, 2, 5 and 10 minutes). Rate-limit errors from free-tier providers are handled the same way.
- Every attempt is stored: attempt number, time, outcome and error.
- Statuses: `ai_pending` → `ai_retrying` → `draft` on success. When the limit is reached without success the request becomes `ai_failed`.
- On `ai_failed` the Admin is notified. The Admin can press **Retry AI** (starts a fresh set of attempts) or build the estimate manually from the Library. The customer keeps seeing "Under Review" and never sees the failure.
- Item-level problems (unknown `task_id`, wrong unit, undeterminable quantity) are **not** retried; they are flagged for the Admin. Only whole-response failures are retried.
- The job must be idempotent: a retry never creates a duplicate estimate.

### Module 5 — Pricing Engine
```
line_total     = quantity × bidfreq_standard_rate       (rounded to 2 decimals)
estimate_total = sum of line_totals
```
- Decimal-safe arithmetic only; never floats.
- Quote-by-scope tasks use the Admin-entered rate.
- **Admin override:** during review the Admin may replace the rate of any line. A written reason is required, the change is logged, the original Library rate stays stored and visible, and the line is marked "Admin override". The AI can never override a rate.
- Unmatched, invalid or undeterminable items are excluded from the total and visibly flagged until the Admin resolves them.
- The total is compared with the NTE; a clear over-limit warning is shown. The system never silently cuts scope or rates.
- Overhead and profit are **not added** on top (already inside the rate). **[CONFIRM]** whether they must be shown separately.
- Each line stores a snapshot: `task_id`, name, unit, quantity, rate, Library version, pricing date, source (`ai` or `manual`).

### Module 6 — Admin Request Inbox & Estimate Review
**Inbox:** all requests, sorted by **customer priority** (Urgent first), then by submission time. Filters by priority, status, property and date. Flags visible in the list (AI failed, flags to resolve, over NTE).

**Review screen:**
- **4Cs** as four clear fields, each linked to its supporting photos, documents and notes.
- All photos, documents and customer measurements.
- Each line: task, quantity, unit, quantity basis and evidence, rate, line total, Library reference range (low/typical/high), times seen, provisional marker.
- Reference-only data: standard hours and market labor cost.
- Total, NTE and over/under status, pricing source and date, Library version.
- Flags: Unmatched items, quote-by-scope items, undeterminable quantities, AI risk flags, confidence score.

**Admin actions:** edit 4Cs, add/remove Library items, change quantities, enter quote-by-scope rates, override a rate (written reason required, logged), add assumptions/exclusions/notes, request more information from the customer, approve, reject. An estimate is released only after Admin approval.

**Request more information:** the request moves to `needs_info`. The customer sees "More Information Needed" with the Admin's message and can add text, photos or files. When the customer responds, the request returns to the Admin (`under_review`). The Admin decides whether to re-run the AI (a fresh set of attempts) or edit the estimate manually.

**Admin rejects a request:** the request becomes `declined`. The customer sees "Declined" with the Admin's reason and may submit a new request.

### Module 7 — PDF, Email & Customer Dashboard
On approval the system generates a professional PDF (property info, Scope of Work, line items, quantities, prices, assumptions, exclusions, total), stores it on the customer's dashboard and emails it. The customer can view details, download the PDF, see status and history, and **approve, reject, or request revision** (with comments). Approval is recorded electronically with user and timestamp. **[CONFIRM]** branding and whether the customer sees line-item rates or only the scope and total.

### Module 8 — Revisions
A revision request returns the estimate to the Admin, who edits it and issues a **new version**. Previous versions are kept and never overwritten.

### Module 9 — Project Creation & Assignment
When the customer approves, a project is created automatically: property, approved estimate reference, approved scope carried from the estimate lines. The Admin then:
- Assigns a **Project Estimator** (a contact record: name, phone, email; no login). Only the Admin sees this.
- Optionally records crews/subcontractors as contact records with the work assigned.
- Records the required materials as a simple list (no pricing, since the Library has no material prices).
- Sets the start date and estimated completion date.

Project statuses: `scheduled`, `in_progress`, `completed`, `cancelled`.

**Initial payment (undecided, not built now):** the amount charged before the project starts has not been decided (it could be 1%, 50% or 100%). Project creation is **not** blocked by payment now. The design must allow this to be added later (see Module 11).

### Module 10 — Project Execution (Admin-operated)
- **Daily progress:** the Estimator reports progress and photos to the Admin manually. The Admin adds a daily report per day: date, who reported, work progress, notes, and **daily work photos** with captions.
- **Additional work (Change Orders):** when extra work or damage is found, the Admin creates a Change Order from the project or a daily report. Items are chosen from the **same Library** (`task_id` + quantity) and priced with the same engine, snapshot rules and Admin override rules as estimates; quote-by-scope tasks use an Admin-entered rate; items not in the Library are flagged, not priced. Change Orders are **kept separate from the original approved estimate**, which never changes. The project shows the original total, approved Change Order total and combined total. Statuses: draft, pending, approved, rejected. **[CONFIRM]** whether customer approval is required or the Admin decides.
- **Walkthrough and completion:** PM walkthrough record (date, notes, status), completion certification (reference, date, file), project marked completed.

### Module 11 — Final Invoice & Payment Status (end of project)
- Payment is handled **at the end**. After completion the Admin issues an invoice with the original approved scope and approved Change Orders as **separate lines**, total, due date and Admin-set status (draft, sent, cancelled). Payment status and overdue are derived (below).
- The Admin records payments received (amount, date, method, reference, status).
- **Balance rules:** net paid = sum of payments with status `completed` − sum of payments with status `refunded`. Each refund is its own record with its own positive amount, and partial refunds are allowed (never more than the net paid). Balance = invoice total − net paid. `pending` and `failed` payments are not counted.
- **Status display:** payment status is derived: `unpaid` (net paid is 0), `partially paid` (0 < net paid < total), `paid` (net paid ≥ total). **Overdue is a separate flag**, shown whenever the due date has passed and the balance is above 0, so a partially paid invoice can show both. The Admin sets only draft, sent and cancelled.
- Status tracking only; no online payment process now. **[CONFIRM]**
- **Designed for later:** several invoices and several payments per project, an `invoice_type` field (default `final`), and no hardcoded assumption that payment happens only at the end, so initial payments and a proper payment process can be added later without redesign.

### Module 12 — Audit & Feedback Data
Activity log of important actions. For every estimate, keep the AI's original output beside the Admin's final version, to support future accuracy improvement.

## 6. Statuses and What the Customer Sees

| Internal status | Customer sees |
|---|---|
| `ai_pending`, `ai_retrying`, `ai_failed`, `draft`, `under_review` | Under Review |
| `needs_info` | More Information Needed (with the Admin's message) |
| `declined` (Admin rejected the request) | Declined (with reason) |
| `approved` (released, current version) | Estimate Ready |
| `revision_requested` | Revision Requested (being reviewed) |
| `customer_rejected` | Rejected by you |
| `customer_approved` → project `scheduled` | Approved |
| project `in_progress` | In Progress |
| project `completed` | Completed |
| project `cancelled` | Cancelled |

**Versions:** when the Admin approves a revised estimate, the new version becomes the single **current** version and the customer is notified. Earlier versions are marked `superseded`, stay stored and visible in history, and cannot be decided on. The customer can approve, reject or request revision only on the current released version.

## 7. Notifications (email)
Customer: request received, estimate ready, revised estimate, request for more information, request declined, project completed. Admin: new request received (with priority), AI failed after the retry limit. This list is the working default (Section 11, item 18).

## 8. Technical Requirements

| Area | Requirement |
|---|---|
| Framework | Laravel 13, PHP 8.3+ |
| Database | MySQL, migrations, foreign keys, DECIMAL for money and quantities |
| Auth | Laravel authentication; roles Super Admin / Admin / Customer; policies on all modules |
| Queue | Laravel queues for AI analysis, PDF and email (database driver in development; Redis or similar in production) |
| AI | Provider-agnostic interface, strict JSON schema validation. Development: a free-tier vision model. Production: a paid provider (Section 9). |
| Files | Private storage; type and size validation; authorized access only |
| PDF / Email | Server-side PDF; transactional email |
| Math | Decimal-safe only |
| Testing | Automated tests for pricing, AI-output validation, AI retry limit, Library import, policies, estimate states, Change Order pricing |

## 9. Build Order (fastest path to a working system)

Build in this order. Each step must work end to end before the next starts.

1. **Authentication and panels:** Super Admin, Admin, Customer.
2. **Pricing Library:** tables, CSV/JSON import and export, Library screens.
3. **Request intake and pricing engine:** request form (photos, measurements, priority), instant submit with confirmation, pricing from the Library, NTE check.
4. **AI analysis through the queue**, with the retry limit (Module 4).
5. **Admin inbox and estimate review:** priority list, review screen, edit, approve, reject.
6. **Customer side:** PDF, email, dashboard, approve / reject / revision, estimate versions.
7. **Projects:** creation from approved estimate, Estimator assignment, dates.
8. **Execution:** daily reports with photos, Change Orders priced from the Library, walkthrough, completion.
9. **Final invoice and payment status.**
10. Audit data, testing and deployment.

**Delivery phases:** **Phase 1** = steps 1 and 2 (Foundation, Admin and Pricing Library: login, panels, view/search/update/import/export pricing). **Phase 2** = steps 3 and 4 (request form, photos, measurements, automatic pricing from the Library, NTE validation, AI analysis, 4Cs, tasks and quantities, draft estimates). Steps 5 to 10 follow. No AI API key is needed until step 4.

Steps 1 to 6 deliver the core promise (photos in, verified estimate out, customer decision). Steps 7 to 9 add project execution.

**Needed from the client:** answers to the open questions in Section 11 and sample property photos for AI testing.

**Note on AI:** development will use a free AI API. Free services have low limits, may log or use submitted data, and are not suitable for live use. **A paid AI service will be required for production**, and its usage fees are separate from the development cost.

## 10. Assumptions & Out of Scope

**Assumptions:** the Library is the single source of pricing, and its content is the client's responsibility (58 of 162 tasks were seen in only one scope; standard hours are provisional; labor actuals are not yet logged). Third-party service fees (AI, storage, email) are separate. Estimate accuracy depends on photo quality and customer measurements.

**Out of scope:** logins for estimators, subcontractors, crews or PM; mobile apps; multi-user company sharing; initial/advance payment and online payment process (planned later); automatic Library updates from third-party pricing sources; AI-generated pricing of any kind; anything not listed here.

## 11. Open Items and Defaults

The coding agent uses the **default** below until the client confirms. Each default is implemented as a setting or kept easy to change.

| # | Open item | Default until confirmed |
|---|---|---|
| 1 | Who provides the NTE | Optional on the customer form; the Admin can add or edit it (every change logged) |
| 2 | Overhead/profit shown separately | No; they are inside the Library rate |
| 3 | The 7 "Quote by scope" tasks | The Admin enters the rate per estimate |
| 4 | ZIP-based and location-specific pricing (no ZIP mapping in the data; only 4 reference labor markets) | One Library rate set for all; ZIP is stored but does not change prices |
| 5 | Customer-specific rate sets | None; all customers use the Evergreen-based Library |
| 6 | Mandatory intake measurements | Home sq ft and stories required; damaged-area dimensions optional (the AI flags `cannot_determine` where a task needs a size that is missing) |
| 7 | Who can change Library rates | Admin and Super Admin; every change versioned and logged |
| 8 | Customer sees line-item rates | Yes: line items, quantities, unit rates and totals (the client's SOW lists "line items, quantities, pricing" in the PDF) |
| 9 | Change Order approval | The Admin decides; customer approval not required in this release (status field allows adding it later) |
| 10 | Priority levels | Low, Normal (default), High, Urgent |
| 11 | Initial/advance payment | Deferred; must not block project creation |
| 12 | Currency and tax | USD; no tax calculation in this release |
| 13 | Material costs and overhead/profit in the review screen | Not shown as separate lines (not in the data). Shown: quantity, rate, line total, Library reference range, reference hours and market labor cost, NTE, source, date |
| 14 | Inspection notes / physical inspection | No physical inspection step; notes are entered by the Admin; the only on-site step is the completion walkthrough |
| 15 | Separate labor-rates CSV | Not required; reference labor rates load from the XLSX tab only if the client wants them |
| 16 | PDF branding | A simple BidFreq-branded layout; the client supplies logo and colors |
| 17 | Customer profile | Basic company information only |
| 18 | Email notifications | The list in Section 7 |
| 19 | Wide-range tasks (`BF-T-071`, `BF-T-072`, `BF-T-074`) | Fixed CSV rate, with the low/typical/high range shown to the Admin |
| 20 | Meaning of the 8 ambiguous-unit tasks | Flagged to the Admin on every use |

Items 1, 4, 5, 8, 9, 13, 16, 19 and 20 should be confirmed with the client.

## 12. Acceptance Criteria

1. Authentication works for Super Admin, Admin and Customer, each with its own panel and correct access.
2. Admin can import the Library from CSV/JSON (with preview) and export it back; every edit is versioned.
3. Customers can submit requests; requests without 2 photos or required measurements are blocked; submission is instant with a confirmation message and email.
4. Requests run through the background queue; AI returns validated 4Cs and Library-based tasks; price fields in AI output are discarded and the rest of a valid response is used.
5. Every price is calculated by Laravel as `quantity × Library rate`, or uses an Admin-entered rate (quote-by-scope tasks, or a logged override with reason). Each line is traceable to a Library version and shows the original Library rate when overridden.
6. Unmatched, wrong-unit and undeterminable items are flagged and never silently priced. AI failures are retried automatically up to the configured limit, then flagged for the Admin without blocking anything; the customer never sees them.
7. The Admin inbox lists requests by customer priority.
8. The Admin can review, edit, approve and reject; all changes are logged; estimates reach the customer only after approval, as PDF and in the dashboard.
9. Customer approve / reject / revision works with versioned estimates.
10. Approval creates a project; the Admin can assign an Estimator and record dates.
11. The Admin can record daily reports with photos and Change Orders priced from the Library, separate from the original estimate.
12. Walkthrough, completion, final invoice and payment status are recorded and linked to the same property and project.
13. Over-NTE estimates are clearly warned.

## Appendix A — Client Requirement Traceability

| Client requirement | Where covered | Note |
|---|---|---|
| Registration / login, user management | Module 1 | |
| Address, ZIP, description, mandatory photos (min 2), optional files | Module 3 | |
| AI scope, trade breakdown, quantities, assumptions, risks, confidence | Module 4 | AI "labor and material suggestions" are not priced by AI; Library only |
| ZIP-based localized pricing | Module 2 / Item 4 | Data gap: no ZIP mapping |
| Human verification workspace (edit scope, quantities, costs, notes; request more info) | Module 6 | Done by Admin |
| Professional PDF, email, dashboard | Module 7 | |
| Approve / reject / revision, history | Modules 7, 8 | |
| Approved Project | Module 9 | |
| Admin management | Modules 1, 2, 6 | |
| Historical / feedback data | Module 12 | |
| Email 1: 4Cs fields linked to photos and notes | Module 6 | Notes entered by Admin (Item 14) |
| Email 2: price breakdown (quantities, hours, labor rates, materials, overhead/profit, NTE, source, date) | Module 6 | Materials and overhead/profit not separate in data (Item 13) |
| Email 3: project execution (crews, materials, dates, daily photos, change orders, walkthrough, completion, invoice/payment status) | Modules 9, 10, 11 | |
