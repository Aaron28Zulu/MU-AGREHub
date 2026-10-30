<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed accounts are looked up by their handle, falling back to any user so the seeder runs on its own.
        $person = fn (string $handle): User => User::where('email', "{$handle}@rris.mu.ac.zm")->first() ?? User::firstOrFail();

        // Completed studies from every school, plus a few still moving through supervision.
        $projects = [
            [
                'title' => 'Assessing Biochar Amendments on Soil Microbial Biomass and Nitrogen Dynamics in Semi-Arid Sandy Soils',
                'abstract' => 'This study investigates the multi-season efficacy of locally pyrolyzed maize stover biochar in enhancing moisture retention, cation exchange capacity, and microbial respiration across smallholder agricultural soils in Southern Zambia. Results demonstrated a 34% increase in plant-available water and significant mitigation of nitrate leaching.',
                'category' => ResearchCategory::AGRICULTURE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('lukundo')->id,
                'supervisor_id' => $person('cmulenga')->id,
                'domain' => 'Soil Fertility & Nutrient Management',
                'gaps' => [
                    'Long-term field trials required to examine biochar aging effects on soil macro-aggregate stability.',
                    'Quantifying economics of farm-scale retort kilns for smallholder farmer cooperatives.',
                ],
            ],
            [
                'title' => 'Formulation of Low-Cost Black Soldier Fly Larvae Meal as a Replacement for Fishmeal in Broiler Diets',
                'abstract' => 'To combat escalating poultry feed expenses, this research formulated five isonitrogenous diets substituting traditional fishmeal with Hermetia illucens larval protein (0%, 25%, 50%, 75%, 100%). Growth performance, feed conversion ratios (FCR), and carcass characteristics were evaluated over a 42-day trial.',
                'category' => ResearchCategory::AGRICULTURE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('lukundo')->id,
                'supervisor_id' => $person('cmulenga')->id,
                'domain' => 'Animal Nutrition & Livestock Genetics',
                'gaps' => [
                    'Substrate optimization using localized brewery spent grains to enhance larval lipid profiles.',
                    'Sensory evaluation of broiler meat from full insect-meal diets among urban consumer demographics.',
                ],
            ],
            [
                'title' => 'Lead Concentrations in Garden Soils and Leafy Vegetables Near Former Mine Sites in Kabwe',
                'abstract' => 'Soil and leafy vegetable samples from 48 household gardens at increasing distances from the former lead-zinc mine were analysed by atomic absorption spectrometry. Lead levels in rape and pumpkin leaves exceeded WHO/FAO limits within two kilometres of the mine, and household interviews showed little awareness of washing or soil-replacement practices.',
                'category' => ResearchCategory::ENVIRONMENTAL_SCIENCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('lukundo')->id,
                'supervisor_id' => $person('cmulenga')->id,
                'domain' => 'Pollution & Environmental Health',
                'gaps' => [
                    'Blood lead levels of households consuming garden produce have not been linked to soil data.',
                    'Low-cost soil capping or raised-bed methods for contaminated gardens remain untested.',
                ],
            ],
            [
                'title' => 'Charcoal Production and Miombo Woodland Loss in Chibombo District, 2010 to 2024',
                'abstract' => 'Landsat imagery for five time points was classified to map woodland cover change, and 120 charcoal producers were interviewed on harvesting practices and income. Woodland cover fell by 31 percent, concentrated along roads to Lusaka and Kabwe, and charcoal provided over half of cash income for participating households.',
                'category' => ResearchCategory::ENVIRONMENTAL_SCIENCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('natasha')->id,
                'supervisor_id' => $person('cmulenga')->id,
                'domain' => 'Forestry & Land Use',
                'gaps' => [
                    'Regeneration rates of harvested miombo stands under community forest management are unknown.',
                    'Alternative livelihoods that could replace charcoal income have not been costed for the district.',
                ],
            ],
            [
                'title' => 'Non-Monetary Incentives and Employee Retention in Zambian Commercial Banks',
                'abstract' => 'A survey of 214 staff at six commercial banks in Lusaka and Kabwe examined how recognition, training opportunities and flexible working relate to intention to stay. Structural equation modelling showed career development had a stronger effect on retention than pay satisfaction among staff under 35.',
                'category' => ResearchCategory::BUSINESS_MANAGEMENT,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2023,
                'student_id' => $person('joseph')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Human Resource Management',
                'gaps' => [
                    'Whether retention effects hold in microfinance institutions and savings groups is untested.',
                    'Long-term turnover data are needed to confirm stated intentions to stay.',
                ],
            ],
            [
                'title' => 'Survival of Youth-Owned Micro-Enterprises After Business Start-Up Grants',
                'abstract' => 'Tracked 160 youth enterprises funded through a national empowerment grant in Central Province two to four years after disbursement. Forty-two percent were still trading; survival was higher among owners who received mentoring and kept written records, and lower in crowded retail sectors.',
                'category' => ResearchCategory::BUSINESS_MANAGEMENT,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('joseph')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Entrepreneurship & Small Business',
                'gaps' => [
                    'The cost-effectiveness of mentoring compared with larger grant sizes has not been evaluated.',
                ],
            ],
            [
                'title' => 'Adoption of IFRS for SMEs Among Small and Medium Businesses in Kabwe',
                'abstract' => 'Examined the extent to which registered small and medium businesses prepare financial statements under IFRS for SMEs. Interviews with owners and accountants of 75 firms showed low adoption, driven by cost, lack of trained staff and weak enforcement, although adopters reported easier access to bank credit.',
                'category' => ResearchCategory::ACCOUNTING_FINANCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('joseph')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Financial Reporting & Auditing',
                'gaps' => [
                    'The effect of simplified reporting templates on SME compliance has not been piloted.',
                    'Links between reporting quality and loan pricing need verification with lender data.',
                ],
            ],
            [
                'title' => 'Mobile Money Adoption and Record Keeping Among Market Traders in Kabwe',
                'abstract' => 'A survey of 300 traders at Kabwe\'s main markets assessed how mobile money use relates to business record keeping and savings. Traders using mobile money for most sales were twice as likely to keep daily records, but transaction charges discouraged use for small-value sales.',
                'category' => ResearchCategory::ACCOUNTING_FINANCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('joseph')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Finance, Markets & Enterprise',
                'gaps' => [
                    'Whether mobile transaction histories could support credit scoring for informal traders is unexplored.',
                ],
            ],
            [
                'title' => 'Credit Access and Fertilizer Adoption Among Women Smallholder Farmers',
                'abstract' => 'A household survey of 280 farmers examined how access to input credit and farmer group membership influence fertilizer adoption intensity, using a double-hurdle model to separate the adoption decision from application rates.',
                'category' => ResearchCategory::ECONOMICS,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2022,
                'student_id' => $person('peter')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Finance, Markets & Enterprise',
                'gaps' => [
                    'Effects of mobile-money credit products on input use are not yet studied locally.',
                ],
            ],
            [
                'title' => 'Exchange Rate Pass-Through to Consumer Food Prices in Zambia, 2012 to 2024',
                'abstract' => 'Estimated the speed and size of kwacha depreciation pass-through to the food component of the consumer price index using monthly data and an autoregressive distributed lag model. Pass-through was incomplete but faster after 2020, with imported cooking oil and sugar adjusting within three months.',
                'category' => ResearchCategory::ECONOMICS,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('peter')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => 'Macroeconomics & Prices',
                'gaps' => [
                    'Pass-through to rural markets, which are outside the urban CPI basket, has not been measured.',
                ],
            ],
            [
                'title' => 'Learner-Centred Pedagogy in Large Grade 9 Mathematics Classes',
                'abstract' => 'Classroom observations in 18 Kabwe secondary schools and interviews with 36 teachers explored how learner-centred methods are applied in classes of over 60 pupils. Group work was common but often teacher-led, and pupils in classes using structured peer explanation scored higher on a common algebra test.',
                'category' => ResearchCategory::EDUCATION,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('chipo')->id,
                'supervisor_id' => $person('nkabwe')->id,
                'domain' => 'Teaching & Learning Practice',
                'gaps' => [
                    'Teacher training models for structured peer learning in large classes need trialling.',
                    'Effects on girls\' participation in mathematics were not separately analysed.',
                ],
            ],
            [
                'title' => 'Inclusion of Learners with Hearing Impairment in Mainstream Primary Schools',
                'abstract' => 'Case study of six primary schools practising inclusive education in Central Province. Few teachers had sign language training and most relied on written notes, while specialist units provided better communication but limited interaction with hearing peers.',
                'category' => ResearchCategory::EDUCATION,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2023,
                'student_id' => $person('chipo')->id,
                'supervisor_id' => $person('nkabwe')->id,
                'domain' => 'Inclusive Education',
                'gaps' => [
                    'The availability and cost of Zambian Sign Language training for serving teachers is undocumented.',
                ],
            ],
            [
                'title' => 'Factors Influencing Late Antenatal Care Booking Among Pregnant Women at Kabwe Urban Clinics',
                'abstract' => 'A cross-sectional study of 384 pregnant women attending four urban clinics found that 61 percent booked their first antenatal visit after 16 weeks. Late booking was associated with unplanned pregnancy, distance to the clinic and the belief that early visits are unnecessary when a woman feels well.',
                'category' => ResearchCategory::HEALTH_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('mutinta')->id,
                'supervisor_id' => $person('smwansa')->id,
                'domain' => 'Maternal & Child Health',
                'gaps' => [
                    'Community health worker outreach to promote early booking has not been evaluated in Kabwe.',
                    'Partner involvement in the timing of first antenatal visits needs qualitative study.',
                ],
            ],
            [
                'title' => 'Adherence to Antiretroviral Therapy Among Adolescents in Central Province',
                'abstract' => 'Pharmacy refill records and interviews with 210 adolescents on antiretroviral therapy at three facilities showed 68 percent optimal adherence. Disclosure of HIV status to the adolescent, support groups and school-friendly clinic hours were associated with better adherence.',
                'category' => ResearchCategory::HEALTH_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('mutinta')->id,
                'supervisor_id' => $person('smwansa')->id,
                'domain' => 'Health Systems & Disease Control',
                'gaps' => [
                    'The effect of SMS reminders on adolescent adherence in rural facilities remains untested.',
                ],
            ],
            [
                'title' => 'A USSD-Based Results and Fees Enquiry System for Students Without Smartphones',
                'abstract' => 'Designed and tested a USSD service that lets students check examination results and fee balances on basic phones. Usability testing with 90 students showed task completion above 95 percent, and the service reduced queues at the accounts office during the pilot week.',
                'category' => ResearchCategory::COMPUTING,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('thandiwe')->id,
                'supervisor_id' => $person('bchanda')->id,
                'domain' => 'Software & Data Systems',
                'gaps' => [
                    'Security of USSD sessions against SIM-swap fraud needs a dedicated assessment.',
                    'Costs of USSD sessions for students have not been compared with SMS alternatives.',
                ],
            ],
            [
                'title' => 'Machine Learning Detection of Fraudulent Mobile Money Agent Transactions',
                'abstract' => 'Compared logistic regression, random forest and gradient boosting models on an anonymised dataset of 1.2 million agent transactions. Gradient boosting achieved the best recall on rare fraud cases, and transaction velocity features were the most informative.',
                'category' => ResearchCategory::COMPUTING,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('thandiwe')->id,
                'supervisor_id' => $person('bchanda')->id,
                'domain' => 'Software & Data Systems',
                'gaps' => [
                    'Model performance on transactions from rural agents, under-represented in the data, is unknown.',
                ],
            ],
            [
                'title' => 'Performance of Off-Grid Solar Systems at Rural Health Posts in Central Province',
                'abstract' => 'Monitored energy output, battery health and load profiles of solar systems at twelve rural health posts over a year. Half of the systems failed to supply night-time loads in the rainy season, mainly due to degraded batteries and added equipment beyond the original design.',
                'category' => ResearchCategory::ENGINEERING,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('mwila')->id,
                'supervisor_id' => $person('rmusonda')->id,
                'domain' => 'Energy & Infrastructure',
                'gaps' => [
                    'Maintenance models that keep health-post solar systems working beyond donor funding are needed.',
                ],
            ],
            [
                'title' => 'Compressive Strength of Concrete Using Crushed Waste Glass as Partial Fine Aggregate',
                'abstract' => 'Concrete cubes with 0 to 30 percent of river sand replaced by crushed waste glass were tested at 7, 28 and 56 days. Up to 20 percent replacement maintained the target strength of 25 MPa while reducing sand use, with workability improving at higher glass content.',
                'category' => ResearchCategory::ENGINEERING,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('mwila')->id,
                'supervisor_id' => $person('rmusonda')->id,
                'domain' => 'Construction Materials',
                'gaps' => [
                    'Long-term durability under alkali-silica reaction has not been tested for local glass sources.',
                ],
            ],
            [
                'title' => 'Physicochemical and Bacteriological Quality of Borehole Water in Peri-Urban Kabwe',
                'abstract' => 'Water from 40 boreholes was tested for pH, nitrate, lead and faecal coliforms in the wet and dry seasons. A third of samples exceeded the national limit for faecal coliforms in the wet season, most often where pit latrines were within 30 metres.',
                'category' => ResearchCategory::NATURAL_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2023,
                'student_id' => $person('mwila')->id,
                'supervisor_id' => $person('rmusonda')->id,
                'domain' => 'Water Quality & Public Health',
                'gaps' => [
                    'Safe siting distances between latrines and boreholes in Kabwe\'s dolomite geology need modelling.',
                ],
            ],
            [
                'title' => 'Statistical Modelling of Rainfall Variability and Season Onset Dates in Central Zambia',
                'abstract' => 'Analysed 40 years of daily rainfall records from four stations to model onset, cessation and dry-spell frequency. Onset dates have become more variable since 2000, and the probability of a dry spell longer than ten days in January has increased.',
                'category' => ResearchCategory::NATURAL_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('peter')->id,
                'supervisor_id' => $person('rmusonda')->id,
                'domain' => 'Water Resources & Climate Adaptation',
                'gaps' => [
                    'Downscaled climate projections for onset dates at district level are not yet available.',
                ],
            ],
            [
                'title' => 'Youth Unemployment and Participation in Informal Cross-Border Trade',
                'abstract' => 'Interviews with 95 young traders at the Kasumbalesa and Chirundu borders explored why youths enter cross-border trade and the risks they face. Trade offered better income than local alternatives, but harassment, unclear duty rules and loss of goods were common.',
                'category' => ResearchCategory::SOCIAL_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $person('natasha')->id,
                'supervisor_id' => $person('lsakala')->id,
                'domain' => 'Livelihoods & Social Change',
                'gaps' => [
                    'Experiences of young women traders, who were under-represented in the sample, need focused study.',
                ],
            ],
            [
                'title' => 'Social Media Use and Political Participation Among University Students',
                'abstract' => 'A survey of 420 students at three universities examined links between social media use and offline political participation. Following political pages was associated with higher voter registration, but sharing political content was not linked to attending meetings or campaigns.',
                'category' => ResearchCategory::SOCIAL_SCIENCES,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('natasha')->id,
                'supervisor_id' => $person('lsakala')->id,
                'domain' => 'Media & Society',
                'gaps' => [
                    'The influence of misinformation on students\' voting decisions has not been measured.',
                ],
            ],
            [
                'title' => 'Decentralisation and Service Delivery in Kabwe Municipal Council',
                'abstract' => 'Assessed how the national decentralisation policy has affected waste collection, water and market management in Kabwe. Document review and interviews with councillors and residents showed functions were devolved without matching funds, limiting improvements in service delivery.',
                'category' => ResearchCategory::PUBLIC_ADMINISTRATION,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2023,
                'student_id' => $person('natasha')->id,
                'supervisor_id' => $person('lsakala')->id,
                'domain' => 'Governance & Public Policy',
                'gaps' => [
                    'Comparative evidence from rural councils on fiscal decentralisation is lacking.',
                ],
            ],
            [
                'title' => 'Constituency Development Fund Utilisation and Community Participation in Project Selection',
                'abstract' => 'Reviewed CDF records for two constituencies and held focus groups in eight wards. Higher allocations since 2022 increased the number of projects, but community participation in project selection was uneven and wards with active development committees received more completed projects.',
                'category' => ResearchCategory::PUBLIC_ADMINISTRATION,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $person('peter')->id,
                'supervisor_id' => $person('lsakala')->id,
                'domain' => 'Governance & Public Policy',
                'gaps' => [
                    'Monitoring of CDF project quality after completion has not been studied.',
                ],
            ],
            [
                'title' => 'Offline-First Mobile App for Recording Research Site Data',
                'abstract' => 'Proposed development and evaluation of an Android app that lets students record site observations without network coverage and synchronise them to the repository when they reconnect.',
                'category' => ResearchCategory::COMPUTING,
                'status' => ProjectStatus::UNDER_SUPERVISION,
                'completion_year' => null,
                'student_id' => $person('kelvin')->id,
                'supervisor_id' => $person('bchanda')->id,
                'domain' => null,
                'gaps' => [],
            ],
            [
                'title' => 'Fuel Price Changes and Minibus Fare Setting in Kabwe',
                'abstract' => 'Examines how minibus operators adjust fares after fuel price reviews, using fare records from three routes and interviews with operators and passengers.',
                'category' => ResearchCategory::ECONOMICS,
                'status' => ProjectStatus::UNDER_REVIEW,
                'completion_year' => null,
                'student_id' => $person('peter')->id,
                'supervisor_id' => $person('jtembo')->id,
                'domain' => null,
                'gaps' => [],
            ],
            [
                'title' => 'Soil Carbon Stocks Under Agroforestry Versus Continuous Maize Cultivation',
                'abstract' => 'Proposed comparison of soil organic carbon to one metre depth under Faidherbia albida agroforestry and continuous maize, using paired plots across three districts.',
                'category' => ResearchCategory::AGRICULTURE,
                'status' => ProjectStatus::PROPOSAL_SUBMITTED,
                'completion_year' => null,
                'student_id' => $person('lukundo')->id,
                'supervisor_id' => $person('cmulenga')->id,
                'domain' => null,
                'gaps' => [],
            ],
            [
                'title' => 'Nurse Staffing Levels and Patient Waiting Times at Kabwe General Hospital',
                'abstract' => 'Proposed time-motion study of outpatient waiting times across shifts with different nurse-to-patient ratios.',
                'category' => ResearchCategory::HEALTH_SCIENCES,
                'status' => ProjectStatus::PROPOSAL_SUBMITTED,
                'completion_year' => null,
                'student_id' => $person('mutinta')->id,
                'supervisor_id' => $person('smwansa')->id,
                'domain' => null,
                'gaps' => [],
            ],
            [
                'title' => 'Mother Tongue Instruction and Early Grade Reading Outcomes',
                'abstract' => 'Assessing reading fluency of Grade 2 learners taught in a familiar local language compared with learners in schools where English dominates classroom talk.',
                'category' => ResearchCategory::EDUCATION,
                'status' => ProjectStatus::UNDER_SUPERVISION,
                'completion_year' => null,
                'student_id' => $person('chipo')->id,
                'supervisor_id' => $person('nkabwe')->id,
                'domain' => null,
                'gaps' => [],
            ],
        ];

        foreach ($projects as $item) {
            /** @var Project $project */
            $project = Project::updateOrCreate(
                ['title' => $item['title']],
                [
                    'abstract' => $item['abstract'],
                    'category' => $item['category'],
                    'status' => $item['status'],
                    'completion_year' => $item['completion_year'],
                    'student_id' => $item['student_id'],
                    'supervisor_id' => $item['supervisor_id'],
                ]
            );

            if ($item['domain'] === null) {
                continue;
            }

            // Link domain and gaps
            $domain = ResearchDomain::firstOrCreate(
                ['slug' => Str::slug($item['domain'])],
                ['name' => $item['domain']]
            );

            foreach ($item['gaps'] as $gapText) {
                $project->researchGaps()->firstOrCreate(
                    ['extracted_gap_text' => $gapText],
                    [
                        'domain_id' => $domain->id,
                        'is_emerging' => true,
                        'is_saturated' => false,
                    ]
                );
            }
        }
    }
}
