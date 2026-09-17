# AgriHub (MUSA AgriResearch)

AgriHub is a centralized Research Management and Project Database System designed for the School of Agriculture and Natural Resources (MUSAGRICA). The system tracks research projects, provides access-controlled catalogs, automates similarity and plagiarism detection, identifies institutional research gaps, and digitizes student–supervisor workflows.

---

## 1. System Architecture & Module Breakdown

AgriHub is designed as a modular monolith using **Laravel 11**, **Filament v3**, and **Livewire v3**.

### Module 1: Access-Controlled Research Repository & Discovery
* **Purpose:** Serves as the central digital archive for completed and ongoing research projects while strictly protecting intellectual property and academic integrity.
* **Functional Scope:**
  * Public/Student view: Displays titles, categories, completion year, abstracts, recommendations, identified gaps, and ongoing research areas.
  * Access restrictions: Automatically blocks ordinary students from viewing or downloading full manuscripts, raw experimental data, detailed methodology chapters, and supervisor evaluations.
* **Implementation Mechanism:**
  * **Frontend:** A reactive search, filter, and faceted exploration catalog built with **Livewire v3** and styled using Tailwind CSS.
  * **Authorization:** Strict Laravel **Policies** (`ProjectPolicy`) enforcing granular attribute and file gating.
  * **Storage:** Files are held in a private, non-public storage disk (`Storage::disk('secure_research')`). Document downloads are served strictly via signed, time-limited, role-checked streaming endpoints.

---

### Module 2: Research Proposal & Supervision Workflow
* **Purpose:** Manages the research lifecycle from proposal submission to final defense between students and academic supervisors.
* **Functional Scope:**
  * Registered students submit initial proposals, upload milestone progress reports, track approval stages, and view supervisor feedback.
  * Supervisors inspect submitted drafts, leave inline comments/revisions, accept or reject submissions, and submit digital evaluation rubrics.
* **Implementation Mechanism:**
  * **Student Portal:** Multi-step Livewire submission wizards with client-side and server-side file validation.
  * **Supervisor & Faculty Panel:** **Filament v3 Resource Pages** configured with approval workflow actions, notification dispatchers, and grading rubric forms.
  * **State Management:** Handled via typed PHP Enums (`ProjectStatus`: `Draft`, `ProposalSubmitted`, `UnderSupervision`, `UnderReview`, `Completed`, `Rejected`).

---

### Module 3: AI-Driven Plagiarism & Similarity Detection
* **Purpose:** Detects content overlap, unoriginal submissions, conceptual duplication, and paraphrasing across past and current institutional projects.
* **Functional Scope:**
  * Analyzes uploaded documents against the internal database of completed research.
  * Generates an internal similarity score and itemized similarity report highlighting matching methodology, objectives, and text sections.
  * Restricts access to similarity reports strictly to supervisors, research directors, and administrators.
* **Implementation Mechanism:**
  * **Text Extraction:** Dispatches `ExtractDocumentTextJob` upon file upload using tools like `smalot/pdfparser` or command-line wrappers to extract normalized plain text.
  * **Similarity Engine:** Queued `RunSimilarityAnalysisJob` generates semantic embeddings and compares text chunks against indexed PostgreSQL vector tables or similarity algorithms.
  * **Reporting:** Persists structured results to the `similarity_reports` table, viewable exclusively within the supervisor Filament panel.

---

### Module 4: Research Gap & Trend Analytics
* **Purpose:** Analyzes institutional research history to spotlight over-saturated topics, emerging agricultural challenges, and unaddressed research opportunities.
* **Functional Scope:**
  * Evaluates completed projects by theme (e.g., Soil Fertility, Crop Protection, Agribusiness, Post-Harvest Management).
  * Automatically flags saturated research areas and highlights suggested future study topics to guide prospective researchers.
* **Implementation Mechanism:**
  * **Analytics Pipeline:** Scheduled console jobs (`ProcessResearchGapsCommand`) aggregate categorized project abstracts and conclusion/recommendation sections.
  * **Admin/Dean Dashboard:** Visualized via **Filament Widgets** (Chart.js / ApexCharts) illustrating departmental output trends, topic distributions, and keyword heatmaps.

---

### Module 5: Administrative & Academic Governance
* **Purpose:** Provides oversight tools for the Dean, Director of Research, and System Administrators to track departmental performance and manage user roles.
* **Functional Scope:**
  * Global view of all research records, faculty workloads, evaluation archives, and academic performance metrics.
  * Granular Role-Based Access Control (RBAC) management and institutional audit logging.
* **Implementation Mechanism:**
  * **Panel Engine:** Dedicated Filament Admin Panel guarded by multi-auth and spatie/laravel-permission integration.
  * **Audit Trails:** Automatic event logging for document downloads, status transitions, and grade submissions.

---

## 2. Technical Prerequisites

Make sure the following local development tools are installed on your macOS machine:

1. **Laravel Herd:** Provides PHP 8.3+, Composer, and Nginx. ([Download Herd](https://herd.laravel.com))
2. **DBngin:** Lightweight local database manager for running PostgreSQL. ([Download DBngin](https://dbngin.com))
3. **TablePlus:** Modern GUI client for database inspection and query execution. ([Download TablePlus](https://tableplus.com))
4. **Node.js & NPM:** For compiling frontend assets.

---

## 3. Installation & Setup Guide

### Step 1: Database Setup with DBngin & TablePlus
1. Open **DBngin** and click **+ New Service**.
2. Select **PostgreSQL**, choose version **16** (or latest), set the port to `5432`, and click **Create**.
3. Click **Start** next to the PostgreSQL service.
4. Open **TablePlus**, create a new connection to **PostgreSQL**:
   * **Host:** `127.0.0.1`
   * **Port:** `5432`
   * **User:** `postgres`
   * **Password:** *(blank or whatever configured in DBngin)*
5. Click **Connect**, then create a new database named `agrihub_db`.

---

### Step 2: Clone & Configure the Project
Clone the repository into your Herd-managed directory (typically `~/Herd`):

```bash
cd ~/Herd
git clone <repository-url> agrihub
cd agrihub
```

Copy the environment configuration file:

```bash
cp .env.example .env
```

Open `.env` in your code editor and update the database settings to connect to DBngin:

```env
APP_NAME=AgriHub
APP_URL=http://agrihub.test

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=agrihub_db
DB_USERNAME=postgres
DB_PASSWORD=
```

---

### Step 3: Install Dependencies
Run Composer and NPM inside the project directory:

```bash
# Install PHP packages
composer install

# Install Frontend packages
npm install
```

Generate the Laravel application encryption key:

```bash
php artisan key:generate
```

---

### Step 4: Run Migrations & Seeders
Execute the database schema setup along with initial roles and demo users:

```bash
php artisan migrate --seed
```

---

### Step 5: Build Assets & Run Application
Compile frontend assets:

```bash
# For local hot-reload
npm run dev

# Or build production bundle
npm run build
```

Link the local storage disk:

```bash
php artisan storage:link
```

---

### Step 6: Start Queue Worker
Since heavy operations (PDF text parsing, similarity checks, gap processing) run in the background, start a local queue worker:

```bash
php artisan queue:work
```

---

### Step 7: Access the System
Open your browser and navigate to:
* **Student Catalog & Portal:** `http://agrihub.test`
* **Admin & Supervisor Panel:** `http://agrihub.test/admin`