# RRIS UI/UX Design System & AI Development Instructions

**Project:** RRIS (Research Repository Interface System) --- Research Management & Project Database System\
**Institution:** Mulungushi University (all schools and departments)\
**Framework:** Laravel\
**Purpose:** Canonical UI/UX instructions for AI-assisted development

------------------------------------------------------------------------

## 1. Purpose of This Document

This document defines the visual language, interaction principles,
component behavior, and implementation standards that AI coding agents
must follow when creating or modifying RRIS interfaces.

**Treat this document as a design contract, not a collection of optional
suggestions.**

The existing interface has a generic SaaS appearance and relies too
heavily on pale gradients, large rounded containers, dark navy controls,
and conventional dashboard/card patterns. Future development must move
toward a more distinctive institutional research platform: calm,
editorial, precise, academic, and grounded in the university's own
research across every discipline.

The goal is **not** to make RRIS look like a themed application for any
single discipline.

RRIS is a **research information system** serving every school. Its
identity should therefore be expressed through restrained visual cues,
archival materials, scientific structure, typography, terminology, and
a calm forest-and-ochre palette---not through discipline-specific
illustrations or decorative green gradients.

------------------------------------------------------------------------

# 2. Core Visual Direction

RRIS should feel like:

-   A modern university research portal.
-   A serious academic information system.
-   A curated digital research archive.
-   A professional institutional platform.
-   Calm, intelligent, trustworthy, and organized.
-   Equally at home with agriculture, education, health, business,
    engineering and social science research, without becoming visually
    themed or childish.
-   Designed for researchers, supervisors, students, administrators, and
    institutional decision-makers.

### Design keywords

**Editorial · Scientific · Natural · Institutional · Precise · Quiet ·
Modern · Human · Minimal**

### Avoid

The AI agent must actively avoid:

-   Generic "AI dashboard" aesthetics.
-   Excessive gradients.
-   Neon greens.
-   Large decorative blobs.
-   Glassmorphism as a default design language.
-   Excessive shadows.
-   Huge rounded cards.
-   Excessive pill-shaped UI.
-   Dark navy blocks used merely for contrast.
-   Overuse of icons.
-   Stock discipline-themed illustrations.
-   Cartoon subject graphics (crops, test tubes, mortarboards).
-   Leaf icons everywhere.
-   Visually noisy dashboards.
-   Excessive borders around every element.
-   Dense enterprise-software styling.
-   Unnecessary animations.
-   Template-like card grids that make the system look interchangeable
    with a CRM or admin panel.

------------------------------------------------------------------------

# 3. Brand Personality

RRIS's visual personality should communicate four things immediately:

### 3.1 Academic credibility

The interface must look appropriate for a university research
environment.

Use:

-   Strong typographic hierarchy.
-   Clear metadata.
-   Citation-like information structures.
-   Research categories.
-   Institutional labels.
-   Structured records.
-   Deliberate spacing.

Avoid making the interface look like a consumer marketplace.

### 3.2 Natural intelligence

Natural and archival materials should influence the visual language
subtly. The forest-and-ochre palette is inherited from the system's
origins and now stands for the university as a whole, not one school.

Good references include:

-   Archive shelves and stacked records.
-   Botanical forms.
-   Field patterns.
-   Topographic contours.
-   Scientific specimens.
-   Paper and archive materials.
-   Natural textures used extremely sparingly.
-   Muted botanical colors.

These references should influence the system's **design vocabulary**,
not become decoration.

### 3.3 Modern technology

RRIS should still feel like a contemporary digital product.

Use:

-   Responsive layouts.
-   Clean data visualization.
-   Modern form controls.
-   Strong search.
-   Clear filtering.
-   Progressive disclosure.
-   Thoughtful empty states.
-   Accessible interaction patterns.

### 3.4 Institutional trust

The user should feel that research records are authoritative and
carefully managed.

Use:

-   Clear ownership.
-   Status indicators.
-   Dates.
-   Researcher/supervisor metadata.
-   Verification states.
-   Access-control messaging.
-   Audit-friendly information architecture.

------------------------------------------------------------------------

# 4. Color System

Do not build the interface around a single bright green.

Use a restrained natural palette.

### Primary family

The primary brand color should be a **deep botanical / forest green**,
used selectively for:

-   Primary actions.
-   Active navigation.
-   Important links.
-   Selected states.
-   Key data highlights.

The green should feel mature and institutional rather than saturated.

### Supporting colors

Use a small neutral foundation:

-   Warm off-white / paper-like page background.
-   White or near-white surfaces.
-   Charcoal / deep neutral text.
-   Muted gray secondary text.
-   Soft sage or desaturated botanical green for subtle backgrounds.
-   Earth-inspired secondary accents where appropriate.

Potential semantic accents:

-   Amber for warnings.
-   Red for destructive/error states.
-   Blue for informational states.

Semantic colors must remain restrained and accessible.

### Important rule

**Do not use gradients as the primary visual identity.**

A hero section should not depend on a large green-to-white gradient to
communicate the brand.

If a gradient is used at all, it must be extremely subtle and
subordinate to the content.

------------------------------------------------------------------------

# 5. Typography

Typography is one of the main ways RRIS should distinguish itself
from generic SaaS products.

Use a clean, highly legible sans-serif system for the application
interface.

Typography should establish an editorial hierarchy:

-   Large page title.
-   Short supporting description.
-   Section heading.
-   Record title.
-   Metadata.
-   Body text.
-   Labels/captions.

Research titles should receive strong typographic emphasis because they
are the primary objects users are browsing.

### Rules

-   Do not use excessively heavy font weights everywhere.
-   Avoid all-caps text except for very small institutional labels when
    appropriate.
-   Keep body text comfortable to read.
-   Do not make metadata compete visually with research titles.
-   Use line-height generously enough for academic descriptions.
-   Avoid tiny gray text that becomes difficult to read.

The interface should look refined through **typography and spacing**,
not decoration.

------------------------------------------------------------------------

# 6. Layout Philosophy

RRIS should use a **content-first editorial layout**.

The page should not feel like a collection of floating cards.

Prefer:

``` text
Page
 ├── Context / breadcrumb
 ├── Page heading
 ├── Description / key information
 ├── Search and filtering
 ├── Main content
 │    ├── Research records
 │    ├── Tables
 │    ├── Detail panels
 │    └── Supporting metadata
 └── Secondary actions
```

### Container

Use a consistent maximum content width.

The content should have generous horizontal breathing room on desktop
while remaining efficient on smaller screens.

Do not make every section full-width simply because there is available
space.

### Spacing

Use a predictable spacing scale.

Whitespace should be intentional.

The interface should feel spacious without becoming wasteful.

------------------------------------------------------------------------

# 7. Navigation

The navigation should be simple and institutional.

Recommended primary areas:

-   Research Workspace
-   Research Repository
-   Projects
-   Students
-   Supervisors
-   Research Gaps
-   Similarity / Plagiarism
-   Reports
-   Administration

Only show navigation items relevant to the user's role.

### Navigation behavior

-   Active navigation must be obvious but understated.
-   Do not use oversized navigation buttons.
-   Avoid excessive pill navigation.
-   Keep icons secondary to labels.
-   Mobile navigation must remain easy to operate.
-   Use breadcrumbs where they materially improve orientation.

The navigation should communicate **where the user is**, not compete
with the page.

------------------------------------------------------------------------

# 8. Research Repository / Public Catalog

This is one of RRIS's most important surfaces.

The repository should resemble a **digital academic archive**, not an
e-commerce catalog.

### Research record presentation

Prioritize:

1.  Research title.
2.  Researcher/student.
3.  Department or research area.
4.  Year.
5.  Supervisor where appropriate.
6.  Research status.
7.  Short abstract/summary.
8.  Keywords.
9.  Access level.
10. Actions.

### Preferred visual pattern

Research records can use a hybrid of:

-   Editorial list rows.
-   Compact cards where appropriate.
-   Structured table views for administrators.
-   Detail pages for individual research.

Do not force every record into a large card.

A record should feel like a scholarly entry.

### Research title

Titles should be visually dominant.

Avoid truncating titles aggressively when the title is important for
identification.

Use line clamping only when necessary and provide a clear route to the
full title.

------------------------------------------------------------------------

# 9. Search

Search is a first-class feature.

The primary repository search should be visually prominent but not
oversized.

It should support searching by:

-   Research title.
-   Author/student.
-   Supervisor.
-   Keywords.
-   Abstract topics.
-   Research area.
-   Department.
-   Year.

### Search UX

-   Provide a clear search affordance.
-   Preserve the search query when navigating results.
-   Show result counts where useful.
-   Make filters easy to discover.
-   Do not hide essential filtering behind unnecessary dialogs.
-   Support keyboard interaction.
-   Show a clear empty state when no results are found.

The search interface should feel like a research discovery tool.

------------------------------------------------------------------------

# 10. Filters

Filters should be structured around how academic users actually explore
research.

Potential filters:

-   Research area.
-   Department.
-   Academic year.
-   Research status.
-   Supervisor.
-   Student.
-   Access level.
-   Project type.
-   Similarity status.

### Desktop

Filters may appear as:

-   A compact horizontal filter bar.
-   A left-side filter panel when the dataset is large.

### Mobile

Filters should collapse into a clear filter control and open in a
dedicated sheet/panel.

Do not create dozens of visible filter pills.

------------------------------------------------------------------------

# 11. Research Detail Page

A research detail page should feel like viewing an academic record.

Recommended structure:

``` text
Breadcrumb

Research title
Research status / access state

Student / researcher
Supervisor
Department
Academic year

Abstract

Research objectives

Research gap

Methodology summary

Keywords / research areas

Similarity / originality information
(visible according to role)

Documents / downloads
(access controlled)

Related research

Potential institutional research gaps
```

The title and core metadata should appear before secondary information.

Use visual separation through spacing and typography rather than
excessive bordered boxes.

------------------------------------------------------------------------

# 12. Dashboard Design

Dashboards should summarize meaningful research activity---not simply
fill the screen with statistics.

Examples of useful metrics:

-   Active research projects.
-   Completed projects.
-   Projects awaiting supervisor review.
-   Research submissions awaiting assessment.
-   Research gaps identified.
-   Similarity checks requiring attention.
-   Students under supervision.
-   Research by department/area.
-   Research activity over time.

### KPI cards

Use KPI cards sparingly.

A KPI card should answer a useful question.

Avoid:

``` text
[ 120 ]        [ 340 ]        [ 89 ]        [ 42 ]
Projects      Students       Research       Users
```

when the values provide no context.

Prefer contextual statistics such as:

``` text
Active Research
128 projects
+12 this academic year
```

The dashboard should prioritize **decision support** over decoration.

------------------------------------------------------------------------

# 13. Data Visualization

Charts should look academic and analytical.

Use:

-   Clean line charts.
-   Simple bar charts.
-   Carefully designed donut charts only where appropriate.
-   Trend indicators.
-   Distribution charts.
-   Research-area comparisons.

Avoid:

-   3D charts.
-   Excessive gradients.
-   Decorative charts.
-   Too many colors.
-   Chartjunk.

Every chart should answer a question.

Examples:

-   Which research areas are most active?
-   Which areas have limited research coverage?
-   How has research activity changed over time?
-   Which supervisors have pending reviews?
-   What topics show repeated research?

Charts must have meaningful empty/zero states.

------------------------------------------------------------------------

# 14. Institutional Visual Language

The institutional identity should be subtle and sophisticated.

### Good visual references

-   Deep forest green.
-   Sage.
-   Olive.
-   Warm earth neutrals.
-   Natural paper tones.
-   Fine botanical linework.
-   Topographic contour patterns.
-   Documentary photography of research in any discipline.
-   Archive, shelf and paper textures used sparingly.

### Bad visual references

-   Discipline-specific clip art (cartoon crops, stethoscopes, gears).
-   Large leaf illustrations.
-   Green gradients covering entire pages.
-   Excessive plant motifs.
-   Bright lime green.
-   "Eco startup" aesthetics.

RRIS should look like **a university's research archive**, not a
product themed around one field.

------------------------------------------------------------------------

# 15. Imagery

When imagery is appropriate, favor authentic academic imagery from
across the schools:

-   Field research.
-   Laboratory work.
-   Classrooms and teaching practice.
-   Clinical and community health work.
-   Engineering and computing labs.
-   Libraries and archives.
-   Students conducting fieldwork or interviews.
-   Research equipment.

Photography should be documentary/editorial rather than
stock-photo-like.

Do not add images simply to fill empty space.

------------------------------------------------------------------------

# 16. Cards, Borders, and Surfaces

Cards are allowed but must not dominate the entire interface.

### Use cards for:

-   Important summaries.
-   Distinct workflows.
-   Dashboard metrics.
-   Research highlights.
-   Actions requiring separation.

### Prefer open layouts for:

-   Research lists.
-   Metadata.
-   Tables.
-   Long-form academic information.
-   Detail pages.

### Borders

Use subtle borders only when they improve grouping or interaction.

Do not place a visible border around every small element.

### Shadows

Use shadows sparingly.

The default interface should be visually grounded through:

-   spacing,
-   hierarchy,
-   borders,
-   background contrast,

rather than floating shadows.

------------------------------------------------------------------------

# 17. Border Radius

Use a restrained radius system.

Avoid the current appearance where almost every element is heavily
rounded.

Recommended direction:

-   Small radius for inputs and controls.
-   Moderate radius for larger surfaces.
-   Minimal/no radius for editorial list structures where appropriate.

Pills should be reserved primarily for:

-   Status.
-   Category.
-   Small metadata tags.

Do not make ordinary buttons and containers look like pills.

------------------------------------------------------------------------

# 18. Buttons and Actions

Buttons should be clear and purposeful.

Primary action:

-   Strong visual emphasis.
-   One clear label.
-   Appropriate contrast.

Secondary action:

-   Lower visual weight.
-   Used for supporting actions.

Destructive action:

-   Clearly distinguished.
-   Never visually confused with ordinary actions.

Avoid having multiple competing primary buttons in the same area.

Use action labels that describe the operation:

-   Submit Research
-   Review Submission
-   Run Similarity Check
-   Assign Supervisor
-   View Research
-   Export Report

Avoid vague labels such as "Click Here", "Manage", or "Proceed" when a
more precise label is possible.

------------------------------------------------------------------------

# 19. Forms

Forms should feel calm and professional.

Requirements:

-   Clear labels.
-   Helpful descriptions.
-   Inline validation.
-   Accessible error messages.
-   Logical grouping.
-   Appropriate field widths.
-   Required/optional status.
-   Persistent user input after validation errors.

Long research forms should be divided into logical sections rather than
presented as one enormous form.

For example:

``` text
Research Information
Student & Supervisor
Research Classification
Abstract & Objectives
Methodology
Documents
Review & Submission
```

------------------------------------------------------------------------

# 20. Tables

Tables are important for administrative and research-management
workflows.

Requirements:

-   Clear column hierarchy.
-   Compact but readable rows.
-   Sticky headers where appropriate.
-   Search/filter controls.
-   Responsive behavior.
-   Meaningful empty states.
-   Clear row actions.
-   No unnecessary pagination when the dataset and UX permit a better
    alternative.

On mobile, tables should transform intelligently rather than becoming
unusably compressed.

Where appropriate, use:

-   horizontal scrolling,
-   priority columns,
-   stacked record layouts,
-   or responsive row details.

------------------------------------------------------------------------

# 21. Status System

Status should be understandable without relying solely on color.

Possible research statuses:

-   Draft
-   Submitted
-   Under Review
-   Revision Required
-   Approved
-   Active
-   Completed
-   Archived
-   Restricted

Use text plus subtle visual indicators.

Do not communicate status using color alone.

------------------------------------------------------------------------

# 22. Access-Control UX

Because RRIS manages academic research and potentially sensitive
documents, access control must be visible and understandable.

Public users may see:

-   Verified metadata.
-   Research title.
-   Author information where permitted.
-   Abstract/summary.
-   Research area.
-   Research gap.
-   Public status.

Restricted users may additionally access:

-   Full documents.
-   Datasets.
-   Internal review information.
-   Similarity reports.
-   Supervisor feedback.
-   Administrative records.

The UI must clearly explain why content is unavailable.

Avoid exposing sensitive data simply because a record exists in the
public catalog.

------------------------------------------------------------------------

# 23. Similarity & Plagiarism Interface

Similarity checking is a serious academic workflow.

Do not present it as a flashy AI feature.

The interface should feel analytical and evidence-based.

Display:

-   Check status.
-   Submission version.
-   Similarity percentage where applicable.
-   Comparison/reference information.
-   Flagged sections.
-   Review status.
-   Reviewer notes.
-   Date/time of check.

Avoid language that automatically declares a student "guilty" based
solely on a similarity score.

The system should distinguish:

**Similarity detection** from **academic plagiarism determination**.

------------------------------------------------------------------------

# 24. Student--Supervisor Workflow

Workflow screens should make responsibility and next actions obvious.

For each submission, show:

``` text
Current stage
Assigned supervisor
Submitted date
Days in current stage
Required action
Next step
Comments / feedback
Revision history
```

A workflow timeline can be useful.

Keep it visually simple.

Do not turn every workflow into a large graphic.

------------------------------------------------------------------------

# 25. Research Gap Discovery

Research gaps are an important differentiating feature of RRIS.

The UI should make patterns understandable.

Possible presentation:

``` text
Research Area

Current coverage
    ███████░░░

Repeated topics
    ...

Under-researched topics
    ...

Potential gaps
    ...

Related completed studies
    ...
```

The system should distinguish between:

-   observed data,
-   algorithmically detected patterns,
-   and human/institutional interpretation.

AI-generated recommendations must not be presented as unquestionable
facts.

------------------------------------------------------------------------

# 26. Empty States

Empty states are part of the product experience.

Do not show blank screens.

Examples:

**No research found**

> No research records match your current search and filters.

Actions:

-   Clear filters
-   Browse all research

For a new institution/workspace:

> Your research workspace is ready. Add your first project to begin
> building the institutional research record.

Keep empty-state illustrations subtle or omit them entirely.

------------------------------------------------------------------------

# 27. Loading States

Use skeleton loaders where content structure is predictable.

Avoid excessive spinner usage.

Skeletons should resemble the final layout.

Do not animate large decorative elements while content loads.

------------------------------------------------------------------------

# 28. Motion

Motion should be subtle and functional.

Use animation for:

-   Opening/closing panels.
-   Filtering.
-   Modal transitions.
-   Toast notifications.
-   State changes.
-   Loading transitions.

Avoid:

-   Constant floating animations.
-   Large page transitions.
-   Parallax effects.
-   Decorative bouncing.
-   Excessive hover animations.

Respect reduced-motion accessibility preferences.

------------------------------------------------------------------------

# 29. Accessibility

Accessibility is a core requirement, not a later enhancement.

AI agents must preserve:

-   Keyboard navigation.
-   Visible focus states.
-   Semantic HTML.
-   Accessible labels.
-   Sufficient contrast.
-   Screen-reader-friendly controls.
-   Meaningful form errors.
-   Non-color-only status indicators.
-   Appropriate touch targets.

Never sacrifice accessibility for visual minimalism.

------------------------------------------------------------------------

# 30. Responsive Design

RRIS must be mobile-first.

### Mobile

Prioritize:

-   Search.
-   Key metadata.
-   Primary actions.
-   Readability.
-   Simple navigation.
-   Touch-friendly controls.

### Tablet

Use available width to introduce more metadata and multi-column
structures.

### Desktop

Use:

-   Multi-column layouts where useful.
-   Persistent filters when justified.
-   Rich tables.
-   Research analytics.
-   Side panels.

Do not simply shrink the desktop UI for mobile.

The information hierarchy must change according to available space.

------------------------------------------------------------------------

# 31. Microcopy

The interface should use precise academic language.

Prefer:

> Research Repository

over:

> Research Hub

when referring specifically to the archive.

Prefer:

> Submit for Supervisor Review

over:

> Send

Prefer:

> Similarity Check

over:

> AI Check

Prefer:

> Restricted Document

over:

> Locked File

Microcopy should tell users what the system is doing and what they can
do next.

------------------------------------------------------------------------

# 32. AI-Driven Development Rules

**These rules are mandatory for AI coding agents.**

Before modifying the UI, the agent must:

1.  Inspect the existing implementation.
2.  Identify the relevant Blade/Livewire/Filament components.
3.  Reuse existing design tokens and components where appropriate.
4.  Avoid introducing a second competing design system.
5.  Check responsive behavior.
6.  Check empty, loading, error, and success states.
7.  Check accessibility.
8.  Preserve existing business logic unless the requested change
    explicitly requires it.
9.  Avoid unnecessary dependencies.
10. Avoid replacing working architecture simply to achieve a visual
    change.

### Design reasoning requirement

When implementing a new interface, the agent should ask:

-   What is the primary user task?
-   What information must receive the strongest visual hierarchy?
-   What is secondary?
-   What can be removed?
-   Does this look like an academic research platform?
-   Does it work equally well for research from every school?
-   Does the interface still look professional without the decorative
    styling?
-   Does the design remain usable on mobile?

------------------------------------------------------------------------

# 33. AI Agent Anti-Pattern Rules

The agent must **not** automatically:

-   Add a hero gradient because a page needs visual interest.
-   Wrap every section in a card.
-   Add icons to every label.
-   Add rounded corners to every component.
-   Create a dashboard-style KPI section unnecessarily.
-   Introduce bright green simply because the palette is green.
-   Add stock imagery without a functional reason.
-   Replace a simple list with cards merely for aesthetics.
-   Add animations without a UX purpose.
-   Add a new UI library without first checking existing project
    dependencies.
-   Rewrite an existing page completely when a targeted improvement is
    sufficient.
-   Create inconsistent spacing, colors, or typography.
-   Duplicate components that should be shared.
-   Use arbitrary one-off CSS values when an existing token/component
    can solve the problem.

------------------------------------------------------------------------

# 34. Component Reuse

The project should gradually develop a coherent design system.

Reusable components should be created for recurring patterns such as:

-   Research cards/list rows.
-   Metadata groups.
-   Status badges.
-   Search bars.
-   Filter controls.
-   Empty states.
-   Page headers.
-   Section headers.
-   Stat blocks.
-   Workflow timelines.
-   Document access indicators.
-   Alert banners.
-   Confirmation dialogs.

Before creating a new component, check whether an existing component can
be extended.

------------------------------------------------------------------------

# 35. Design Token Discipline

Colors, spacing, typography, radii, and shadows should be centralized
where the existing Laravel/frontend architecture permits.

Do not scatter arbitrary values throughout Blade/CSS/JavaScript.

For example, avoid repeatedly inventing:

``` text
#1F6B45
#236B49
#185D3A
#2C754D
```

for visually equivalent roles.

Instead establish a controlled semantic palette such as:

``` text
brand
brand-muted
surface
surface-muted
text
text-muted
border
success
warning
danger
info
```

The exact values should be defined once and reused.

------------------------------------------------------------------------

# 36. Visual Hierarchy Test

Every screen should pass this test:

### In three seconds, can the user identify:

1.  Where am I?
2.  What is this page about?
3.  What is the primary action?
4.  What information matters most?
5.  What should I do next?

If not, simplify the interface.

------------------------------------------------------------------------

# 37. Quality Gate Before Completing UI Work

Before considering a UI task complete, the AI agent must verify:

### Visual

-   [ ] No unnecessary gradients.
-   [ ] No excessive rounded containers.
-   [ ] No excessive shadows.
-   [ ] Typography has clear hierarchy.
-   [ ] Institutional identity is subtle and professional, and fits every school.
-   [ ] Page does not look like a generic SaaS template.
-   [ ] Spacing is consistent.
-   [ ] Visual hierarchy is obvious.

### UX

-   [ ] Primary task is clear.
-   [ ] Search/filter behavior is understandable.
-   [ ] Empty states exist.
-   [ ] Error states exist.
-   [ ] Loading states exist where needed.
-   [ ] Success feedback exists where needed.
-   [ ] Destructive actions are clearly distinguished.

### Responsive

-   [ ] Mobile layout is intentionally designed.
-   [ ] Tables remain usable.
-   [ ] Navigation works on mobile.
-   [ ] Forms remain usable.
-   [ ] No horizontal overflow except intentionally scrollable content.

### Accessibility

-   [ ] Keyboard navigation works.
-   [ ] Focus states are visible.
-   [ ] Contrast is sufficient.
-   [ ] Status is not communicated through color alone.
-   [ ] Controls have accessible labels.

### Engineering

-   [ ] Existing components were considered before creating new ones.
-   [ ] No unnecessary dependency was introduced.
-   [ ] Existing business logic was not accidentally changed.
-   [ ] Shared styles/tokens were reused.
-   [ ] The implementation follows the project's existing Laravel
    architecture.

------------------------------------------------------------------------

# 38. Reference Direction for the Current Repository Interface

The existing RRIS repository screen should be redesigned rather than
cosmetically adjusted.

In particular, reconsider:

-   The large green gradient hero.
-   The oversized rounded search container.
-   Dark navy search/filter controls that visually dominate the page.
-   Excessive rounded cards.
-   Large empty areas created by decorative containers.
-   Pill-heavy styling.
-   The generic three-column card presentation.
-   The visual similarity to a generic SaaS landing/dashboard template.

The replacement should feel more like:

**A university research archive + modern research workspace**

than:

**A green-themed SaaS dashboard.**

The research content should become the visual hero.

------------------------------------------------------------------------

# 39. Desired End State

When someone opens RRIS, the immediate impression should be:

> "This is a serious institutional research platform for the whole
> university's research."

Not:

> "This is a generic dashboard with a green theme."

The interface should be recognizable through its **composition,
typography, information architecture, restrained botanical palette,
editorial treatment of research, and subtle natural references**.

The design should remain credible if all decorative imagery is
removed.

That is an important test.

------------------------------------------------------------------------

# 40. Final Instruction to AI Agents

When making UI/UX changes to RRIS:

**Do not ask, "How can I make this screen look more impressive?"**

Ask:

**"How can I make this research workflow clearer, calmer, more
trustworthy, and more distinctive while preserving the RRIS visual
language?"**

Prioritize:

**Clarity → Hierarchy → Usability → Accessibility → Consistency →
Institutional character → Decoration**

Decoration is always the last priority.

A successful RRIS interface should feel **quietly distinctive**, not
loudly designed.
