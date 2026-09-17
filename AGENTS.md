# AGENTS.md — AI-Driven Engineering Guidelines for AgriHub

> **Project Overview:**  
> **AgriHub** (MUSA AgriResearch) is a centralized Research Management and Project Database System developed for the School of Agriculture and Natural Resources (MUSAGRICA).  
> The system tracks completed and ongoing student projects, provides RBAC-governed discovery, assists in research gap analysis, conducts AI-powered similarity/plagiarism detection, and streamlines student–supervisor review workflows.

---

## 1. Core Principles & Philosophy

1. **Academic Integrity & Strict Data Boundaries First:**
   - Ordinary students must *only* view public metadata (Title, Abstract, Completion Year, Category, Gaps, Recommendations).
   - Students must **never** be exposed to full manuscript downloads, methodologies, raw datasets, supervisor assessment marksheets, or similarity breakdowns.
   - All access decisions must be enforced at the model/query/policy layer, not just hidden in the presentation layer.

2. **Simplicity Over Over-Engineering:**
   - Favor native Laravel features, Filament resources, and Livewire components over bespoke custom abstractions.
   - Strictly adhere to standard naming conventions and idiomatically clean PHP.

3. **Asynchronous Processing for Heavy Operations:**
   - Any document parsing (PDF text extraction), LLM embedding, research gap clustering, and similarity scoring must run via queued Laravel Jobs (`ShouldQueue`). Never block the HTTP request cycle.

4. **Zero-Assumption Code:**
   - Every database query returning project records must be scoped with tenant/role guards.
   - All file uploads must enforce strict MIME validation and secure, non-public storage with signed temporary download URLs.

---

## 2. Technology Stack & Environment

Agents must strictly produce code, migrations, and components conforming to this exact stack:

- **Runtime & Language:** PHP 8.3+ / Composer
- **Framework:** Laravel 11.x
- **Admin & Portals:** Filament v3 (Supervisors, HoDs, Deans, Research Directors, System Admins)
- **Frontend / Student Portal:** Laravel Livewire v3 + Tailwind CSS
- **Database:** PostgreSQL 16+ (leveraging `pgvector` or native relational full-text search where applicable)
- **Queue / Background Jobs:** Redis + Laravel Horizon / Database Queue
- **Testing:** Pest PHP
- **Code Quality:** Laravel Pint (standard Laravel code style rules)
- **Local Dev Tooling:** Laravel Herd / macOS native environment

---

## 3. Directory Layout & Architecture Standards

Agents must respect the following architectural structure:

```
app/
├── Enums/                     # ResearchStatus, UserRole, PlagiarismStatus, etc.
├── Filament/                  # Filament Admin & Supervisor Panels
│   ├── Resources/             # ProjectResource, EvaluationResource, etc.
│   └── Pages/                 # Dashboard, GapAnalyticsPage
├── Http/
│   ├── Controllers/           # Lightweight student controllers
│   ├── Requests/              # Form requests with strict validation
│   └── Middleware/            # Role and clearance middleware
├── Jobs/                      # Asynchronous processing (ExtractPdfTextJob, RunSimilarityCheckJob)
├── Livewire/                  # Public catalog, Student submission wizards
├── Models/                    # Eloquent models with strict casts and relationships
├── Policies/                  # Granular authorization (ProjectPolicy, AssessmentPolicy)
├── Services/                  # Business logic (PlagiarismDetectionService, ResearchGapService)
│   ├── Ai/                    # LLM client abstractions (e.g., Claude, OpenAI, embeddings)
│   └── Extraction/            # PDF and document text parsing
database/
├── migrations/                # Clean, indexed PostgreSQL migrations
├── seeders/                   # Role and initial demo seeders
tests/
├── Feature/                   # Pest feature tests testing RBAC and workflows
└── Unit/                      # Unit tests for parsing, similarity algorithms
```

---

## 4. Role-Based Access Control (RBAC) Specifications

Agents generating policies, Filament resources, or Livewire components must enforce these strict role boundaries:

| Entity / Action | Public / Unauth | Ordinary Student | Registered Student Researcher | Supervisor | Dean / Research Director / Admin |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Catalog Metadata (Title, Year, Abstract)** | View | View | View | View | View |
| **Research Gaps & Themes** | View | View | View | View | View |
| **Full Report / Methodology Download** | ❌ Denied | ❌ Denied | ❌ Denied (Self only) | Assigned Only | Full Access |
| **Raw Datasets** | ❌ Denied | ❌ Denied | ❌ Denied (Self only) | Assigned Only | Full Access |
| **Submit Proposal & Progress Logs** | ❌ Denied | ❌ Denied | Create / Update (Self) | ❌ Denied | Manage All |
| **Supervisor Reviews & Rubric Grades** | ❌ Denied | ❌ Denied | View Own Received | Review / Grade Assigned | Full Access |
| **Plagiarism & Similarity Reports** | ❌ Denied | ❌ Denied | ❌ Denied | Assigned Only | Full Access |
| **Institutional Analytics & Gap Reports** | ❌ Denied | ❌ Denied | Summary View | Department View | Full Access |

### Policy Implementation Rule:
Agents must enforce visibility via `ProjectPolicy`:
```php
public function viewFullContent(User $user, Project $project): bool
{
    if ($user->hasRole(['admin', 'dean', 'research_director'])) {
        return true;
    }

    if ($user->hasRole('supervisor') && $project->supervisor_id === $user->id) {
        return true;
    }

    return false; // Ordinary students, external users, or peers NEVER download full manuscripts
}
```

---

## 5. Coding Conventions & Code Style

When generating or modifying code, agents must adhere to the following:

### 5.1 PHP & Laravel Standards
- **Strict Typing:** Always declare `declare(strict_types=1);` at the top of PHP files.
- **Explicit Return Types:** All methods, closures, and actions must explicitly specify argument types and return types.
- **Enums for States:** Never use magic strings for workflow states. Use backed PHP Enums:
  ```php
  enum ProjectStatus: string {
      case DRAFT = 'draft';
      case PROPOSAL_SUBMITTED = 'proposal_submitted';
      case UNDER_SUPERVISION = 'under_supervision';
      case UNDER_REVIEW = 'under_review';
      case COMPLETED = 'completed';
      case REJECTED = 'rejected';
  }
  ```
- **Form Requests:** Never perform inline validation inside controllers. Move rules to dedicated `FormRequest` classes.
- **Pint Compliance:** Code must be formatted cleanly with no trailing whitespace, PSR-12/Laravel formatting standard.

### 5.2 Database & Eloquent Conventions
- Foreign keys must always use constrained cascades or null-on-delete:
  `$table->foreignIdFor(User::class, 'supervisor_id')->nullable()->constrained('users');`
- Always add database indexes on foreign keys, `status`, `completed_year`, and `category_id`.
- Protect mass assignment using `$fillable` or `$guarded = []` with proper validation.
- Store documents in private storage disks (`Storage::disk('secure_research')`), never in `public/`.

### 5.3 Livewire & Filament Rules
- Avoid querying the database directly in Blade views.
- Use Livewire v3 reactive properties and Alpine.js for lightweight UI transitions.
- In Filament resources, always implement `$table->filters()` for Year, Theme/Department, and Status.
- Guard Filament navigation tabs by checking user permissions in `canViewAny()`.

---

## 6. AI & Intelligent Module Implementation Patterns

AgriHub requires two distinct intelligent workflows:

### 6.1 Similarity & Plagiarism Pipeline
1. Upload trigger: When a student uploads a draft/final report (`.pdf`, `.docx`), an event `ProjectDocumentUploaded` is dispatched.
2. Job Execution: `ExtractDocumentTextJob` extracts raw text and strips headers/footers.
3. Similarity Calculation: `RunSimilarityAnalysisJob` calls the LLM/Embedding API:
   - Chunk document text into semantic paragraphs.
   - Compare chunks against existing project records in PostgreSQL (using vector embeddings or text similarity search).
   - Generate an evaluation summary noting conceptual overlaps, matching objectives, and potential paraphrasing.
   - Save the results into a `similarity_reports` table linked to the project.

### 6.2 Research Gap Analytics
- As projects are marked `completed`, an asynchronous job analyzes the abstract, recommendations, and methodologies.
- The system extracts "Future Research Directions" and clusters them under overarching research domains (e.g., Soil Fertility, Post-Harvest Loss, Crop Genetics).
- The system flags saturated vs. under-explored topics on the administrative and student dashboards.

---

## 7. Testing Requirements (Pest PHP)

Every feature implemented by an AI agent must be accompanied by Pest tests.

### Required Test Suites:
1. **Authorization Tests:**
   - Verify ordinary students cannot download restricted project files (`403 Forbidden`).
   - Verify supervisors can only review assigned projects.
   - Verify Deans/Directors can review all departmental outputs.
2. **Workflow Tests:**
   - Student submits proposal $\rightarrow$ status becomes `proposal_submitted`.
   - Supervisor approves $\rightarrow$ status updates to `under_supervision`.
3. **Queue / Job Tests:**
   - Test that `RunSimilarityAnalysisJob` correctly dispatches upon document upload.

Example Pest Test syntax to follow:
```php
test('ordinary students cannot access full research documents', function () {
    $student = User::factory()->create(['role' => UserRole::STUDENT]);
    $project = Project::factory()->completed()->create();

    $this->actingAs($student)
        ->get(route('projects.download', $project))
        ->assertForbidden();
});
```

---

## 8. Agent Instructions Checklist (Before Submitting Changes)

Before finalizing any task, verify:
- [ ] Is access to sensitive files and data guarded by Policies?
- [ ] Are migration files using PostgreSQL-compliant types and indexed appropriately?
- [ ] Are heavy computational tasks dispatched to queues?
- [ ] Has formatting passed Laravel Pint standards?
- [ ] Are tests written using Pest syntax for newly introduced features?
- [ ] Are all UI components (Filament/Livewire) clean, accessible, and reactive?