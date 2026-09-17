# AgriHub — Stakeholder Interfaces & Dashboard Specifications

This document outlines the detailed UI/UX layouts, wireframe schematics, permission boundaries, and data contracts for each user interface across **AgriHub** (MUSA AgriResearch).

---

## 1. Public & Ordinary Student Portal

### 1.1 Objective & Access Clearance
- **Persona:** General undergraduate/postgraduate student exploring topics or checking past research.
- **Access Clearance:** Public / Unauthenticated / Basic Student account.
- **Strict Guardrail:** No access to full-text downloads, methodologies, raw datasets, grading sheets, or plagiarism breakdown reports.

### 1.2 Layout & Key Components
```
+-----------------------------------------------------------------------------------+
|  AGRIHUB | School of Agriculture and Natural Resources        [Sign In / Register]|
+-----------------------------------------------------------------------------------+
|  [ Search Research Topics, Keywords, or Themes...                       ] [Search]|
|  Filters: [Department v] [Year v] [Research Category v] [Thematic Area v]         |
+-----------------------------------------------------------------------------------+
|  ACTIVE RESEARCH GAPS BANNER                                                      |
|  * Emerging: "Impact of Solar Driers on Post-Harvest Maize Losses"                |
|  * Saturated: "Conventional NPK Fertilizer Usage in Southern Province"           |
+-----------------------------------------------------------------------------------+
|  REPOSITORY RESULTS (Paginated List)                                              |
|                                                                                   |
|  [Evaluation of Drought-Tolerant Sorghum Varieties]               [Year: 2024]    |
|  Category: Crop Science | Ongoing Status: Completed                               |
|  Abstract: An analysis of drought-tolerant sorghum yields across drought-prone... |
|  Key Recommendations: Adoption of early-maturing cultivars; decentralized seed...|
|  Identified Research Gaps: Response to micro-dosing fertilizer under sandy soils |
|  [ View Metadata Summary ]   (No Download Button Displayed)                       |
+-----------------------------------------------------------------------------------+
```

### 1.3 Detailed Page Spec: Metadata Summary Modal
When an ordinary student clicks **View Metadata Summary**, display a modal or page containing strictly:
- Project Title
- Academic Year & Department / Category
- Author Name & Institutional Affiliation
- Structured Abstract (Background, Objectives, Summary of Findings)
- Concluding Recommendations & Identified Research Gaps
- *Restricted Notice:* "Full reports, methodologies, and datasets are restricted to authorized supervisors and administrative reviewers to preserve academic integrity."

---

## 2. Registered Student Researcher Portal

### 2.1 Objective & Access Clearance
- **Persona:** Student actively enrolled in research conducting an approved project.
- **Access Clearance:** Authenticated as `Student Researcher`.
- **Scope:** Manage own proposal, upload progress drafts/final manuscripts, review assigned supervisor feedback, and monitor progress stage.

### 2.2 Layout & Key Components
```
+-----------------------------------------------------------------------------------+
|  AGRIHUB | Student Researcher Workspace                    [Profile] [Sign Out]   |
+-----------------------------------------------------------------------------------+
|  Active Project: Optimization of Bio-Pesticides for Fall Armyworm Control        |
|  Supervisor: Dr. M. Phiri | Department: Plant Protection                          |
|                                                                                   |
|  PROGRESS PIPELINE:                                                               |
|  [✓ Proposal Submitted] -> [✓ Approved] -> [● Progress Draft 1] -> [ Final Report]|
+-----------------------------------------------------------------------------------+
|  SUBMISSION & MILESTONE MANAGER                                                   |
|                                                                                   |
|  Milestone: Interim Progress Report & Field Methodology                           |
|  * Upload File: [ Choose File (.pdf, .docx) ]                                     |
|  * Submission Notes: [ Brief summary of progress to date...                     ] |
|  [ Submit for Supervisor Review ]                                                 |
|                                                                                   |
|  Current Status: Under Review (Submitted 2 days ago)                              |
+-----------------------------------------------------------------------------------+
|  SUPERVISOR FEEDBACK & ACTION ITEMS                                               |
|  * (Sept 12, 2026 - Dr. M. Phiri): "Revise sample size determination in Ch. 3."  |
|  * [Download Marked-Up Copy] (Only visible for student's own file)                |
+-----------------------------------------------------------------------------------+
```

---

## 3. Academic Supervisor Portal (Filament Panel)

### 3.1 Objective & Access Clearance
- **Persona:** Faculty lecturer / Assigned Research Supervisor.
- **Access Clearance:** Authenticated as `Supervisor`.
- **Scope:** Review assigned supervisees, view plagiarism/similarity reports, comment/request changes, grade rubric milestones, and approve completions.

### 3.2 Layout & Key Components
```
+-----------------------------------------------------------------------------------+
|  FILAMENT | Supervisor Workspace                           [Dr. M. Phiri v]       |
+-----------------------------------------------------------------------------------+
|  METRIC CARDS:                                                                    |
|  [ Assigned Students: 8 ]  [ Pending Reviews: 3 ]  [ Approved Completed: 5 ]     |
+-----------------------------------------------------------------------------------+
|  STUDENT SUPERVISION QUEUE                                                        |
|  Filter by: [Milestone Stage v] [Similarity Alert Level v]                        |
|                                                                                   |
|  Student Name     | Project Title              | Stage       | Sim. Score | Action|
|  -----------------|----------------------------|-------------|------------|-------|
|  Jane Doe         | Bio-Pesticide Field Trials | Progress Rpt| 8% (Low)   | Review|
|  Kelvin Mwape     | Hydroponics in Lusaka Peri | Final Draft | 24% (Check)| Review|
+-----------------------------------------------------------------------------------+
|  REVIEW & EVALUATION DRAWER (When 'Review' is clicked)                            |
|                                                                                   |
|  Document: [Download Final_Report_Kelvin_Mwape.pdf]                              |
|  Automated Similarity Report:                                                     |
|  * Similarity Index: 24%                                                          |
|  * Matched Sources: 18% overlap with Project #2022-04 (Methods section)          |
|  [ View Detailed Similarity Breakdown & Heatmap ]                                 |
|                                                                                   |
|  Supervisory Actions:                                                             |
|  * Feedback / Required Corrections: [ Textarea...                              ]  |
|  * Decision: ( ) Request Revisions  ( ) Approve Milestone  ( ) Reject             |
|  * Electronic Grading Rubric: Score /100 [ 78 ]                                   |
|  [ Save & Dispatch Notification to Student ]                                      |
+-----------------------------------------------------------------------------------+
```

---

## 4. Executive & Administrative Panel (Dean & Research Director)

### 4.1 Objective & Access Clearance
- **Persona:** Dean of School, Director of Research, Head of Department, System Administrator.
- **Access Clearance:** Full Administrative Access (`dean`, `director_of_research`, `admin`).
- **Scope:** School-wide oversight, departmental research output, aggregated gap analysis, supervisor workload distribution, and audit log tracking.

### 4.2 Layout & Key Components
```
+-----------------------------------------------------------------------------------+
|  FILAMENT | Dean & Research Directorate Console              [Dean Office v]      |
+-----------------------------------------------------------------------------------+
|  SCHOOL-WIDE RESEARCH KPI METRICS                                                 |
|  [ Total Projects: 420 ] [ Ongoing: 84 ] [ Avg Sim Score: 11% ] [ Faculty: 26 ]   |
+-----------------------------------------------------------------------------------+
|  DEPARTMENTAL RESEARCH OUTPUT & CAPACITY                                          |
|  Department                 | Active Supervisions | Completed (2025/2026) | Avg Days |
|  ---------------------------|---------------------|----------------------|---------|
|  Crop Science               | 28                  | 45                   | 112     |
|  Agribusiness & Extension   | 31                  | 52                   | 98      |
|  Animal Science             | 25                  | 38                   | 120     |
+-----------------------------------------------------------------------------------+
|  RESEARCH GAP & THEMATIC TREND ANALYTICS                                          |
|  [ Bar Chart: Topic Density by Sub-Sector ] [ Pie Chart: Over-explored Areas ]   |
|                                                                                   |
|  Flagged Saturated Domains:                                                       |
|  - Smallholder Maize Yield Response (Extensive output: 34 projects in 4 yrs)      |
|                                                                                   |
|  Identified Strategic Research Opportunities / Gaps:                              |
|  - Climate adaptation in Indigenous Small Livestock                               |
|  - Digital Extension delivery frameworks in rural provinces                       |
|  [ Export Institutional Research Report (PDF/Excel) ]                             |
+-----------------------------------------------------------------------------------+
|  SYSTEM AUDIT LOGS                                                                |
|  - Sept 15: Dr. Phiri graded Project #104 (Kelvin Mwape) -> 78/100                 |
|  - Sept 14: System flagged 31% similarity on Project #109                         |
+-----------------------------------------------------------------------------------+
```