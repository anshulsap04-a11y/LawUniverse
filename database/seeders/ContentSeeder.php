<?php

namespace Database\Seeders;

use App\Models\AiVendor;
use App\Models\BareAct;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Coupon;
use App\Models\Examination;
use App\Models\ExaminationCategory;
use App\Models\ExaminationLabel;
use App\Models\Feature;
use App\Models\LandmarkJudgment;
use App\Models\LegalMaxim;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Package;
use App\Models\PreviousYearPaper;
use App\Models\Question;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $adminId = $admin?->id ?? 1;

        // 1. Settings
        $settings = [
            'site_name' => 'Law Universe',
            'tagline' => 'Complete Multi-Role Legal Education & Research Platform',
            'motto' => '।। यतो धर्मस्ततो जयः ।।',
            'contact_email' => 'support@lawuniverse.com',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'timezone' => 'Asia/Kolkata',
            'allow_registration' => '1',
            'require_judge_approval' => '1',
            'require_advocate_approval' => '1',
            'default_ai_engine' => 'OpenAI GPT-4o',
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        // 2. Resource Categories
        $categoriesData = [
            'Constitutional Law' => 'Provisions relating to the Constitution, fundamental rights, and executive powers.',
            'Criminal Law & Procedure' => 'Substantive offenses, penal statutes, criminal procedure, and forensic jurisprudence.',
            'Civil Law & Procedure' => 'Code of Civil Procedure, contracts, torts, property, and specific relief.',
            'Corporate & Commercial Law' => 'Companies Act, insolvency, competition law, and commercial arbitration.',
            'Family Law' => 'Personal laws, succession, matrimonial remedies, and guardianship.',
            'Intellectual Property' => 'Patents, copyrights, trademarks, and trade secrets.',
        ];

        $resCategories = [];
        foreach ($categoriesData as $name => $desc) {
            $resCategories[$name] = ResourceCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $desc]
            );
        }

        // Subcategories
        $subConst = ResourceCategory::updateOrCreate(
            ['slug' => 'fundamental-rights'],
            ['name' => 'Fundamental Rights', 'parent_id' => $resCategories['Constitutional Law']->id]
        );
        $subBns = ResourceCategory::updateOrCreate(
            ['slug' => 'bns-penal-statutes'],
            ['name' => 'BNS Penal Statutes', 'parent_id' => $resCategories['Criminal Law & Procedure']->id]
        );

        // 3. Bare Acts
        $bareActs = [
            [
                'act_name' => 'The Constitution of India, 1950',
                'act_number' => 'Constituent Assembly of India',
                'year' => 1950,
                'category_id' => $resCategories['Constitutional Law']->id,
                'subcategory_id' => $subConst->id,
                'jurisdiction' => 'Republic of India',
                'amendment_status' => 'Updated up to 106th Constitutional Amendment Act, 2023',
                'publication_date' => '1950-01-26',
                'description' => 'The supreme law of India, establishing the fundamental political code, structure, procedures, powers, and duties of government institutions and fundamental rights.',
                'content' => "PREAMBLE\nWE, THE PEOPLE OF INDIA, having solemnly resolved to constitute India into a SOVEREIGN SOCIALIST SECULAR DEMOCRATIC REPUBLIC and to secure to all its citizens:\nJUSTICE, social, economic and political;\nLIBERTY of thought, expression, belief, faith and worship;\nEQUALITY of status and of opportunity;\nand to promote among them all\nFRATERNITY assuring the dignity of the individual and the unity and integrity of the Nation...",
                'is_free' => true,
                'created_by' => $adminId,
            ],
            [
                'act_name' => 'The Bharatiya Nyaya Sanhita, 2023 (BNS)',
                'act_number' => 'Act No. 45 of 2023',
                'year' => 2023,
                'category_id' => $resCategories['Criminal Law & Procedure']->id,
                'subcategory_id' => $subBns->id,
                'jurisdiction' => 'Republic of India',
                'amendment_status' => 'Enacted & In Force (w.e.f. July 1, 2024)',
                'publication_date' => '2023-12-25',
                'description' => 'The official criminal code of the Republic of India replacing the Indian Penal Code, 1860.',
                'content' => "CHAPTER I - PRELIMINARY\nSection 1: Short title, commencement and application.\n(1) This Act may be called the Bharatiya Nyaya Sanhita, 2023.\n(2) It shall come into force on such date as the Central Government may, by notification in the Official Gazette, appoint.\n(3) Every person shall be liable to punishment under this Sanhita and not otherwise for every act or omission contrary to the provisions thereof, of which he shall be guilty within India...",
                'is_free' => true,
                'created_by' => $adminId,
            ],
            [
                'act_name' => 'The Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS)',
                'act_number' => 'Act No. 46 of 2023',
                'year' => 2023,
                'category_id' => $resCategories['Criminal Law & Procedure']->id,
                'subcategory_id' => $subBns->id,
                'jurisdiction' => 'Republic of India',
                'amendment_status' => 'In Force (w.e.f. July 1, 2024)',
                'publication_date' => '2023-12-25',
                'description' => 'Code governing criminal procedure, investigations, trial, and detention, replacing CrPC 1973.',
                'content' => "An Act to consolidate and amend the law relating to Criminal Procedure. Be it enacted by Parliament in the Seventy-fourth Year of the Republic of India...",
                'is_free' => true,
                'created_by' => $adminId,
            ],
            [
                'act_name' => 'The Bharatiya Sakshya Adhiniyam, 2023 (BSA)',
                'act_number' => 'Act No. 47 of 2023',
                'year' => 2023,
                'category_id' => $resCategories['Criminal Law & Procedure']->id,
                'subcategory_id' => $subBns->id,
                'jurisdiction' => 'Republic of India',
                'amendment_status' => 'In Force (w.e.f. July 1, 2024)',
                'publication_date' => '2023-12-25',
                'description' => 'The statutory law governing admissibility of evidence in Indian courts, replacing Indian Evidence Act, 1872.',
                'content' => "CHAPTER I - PRELIMINARY\nSection 1: Short title, application and commencement. (1) This Adhiniyam may be called the Bharatiya Sakshya Adhiniyam, 2023...",
                'is_free' => true,
                'created_by' => $adminId,
            ],
        ];

        foreach ($bareActs as $ba) {
            $act = BareAct::updateOrCreate(['act_name' => $ba['act_name']], $ba);

            // Also mirror as a central Resource
            Resource::updateOrCreate(
                ['slug' => Str::slug($ba['act_name'])],
                [
                    'title' => $ba['act_name'],
                    'resource_type' => 'bare_act',
                    'category_id' => $ba['category_id'],
                    'subcategory_id' => $ba['subcategory_id'],
                    'year' => $ba['year'],
                    'author' => 'Legislative Department, Ministry of Law and Justice',
                    'language' => 'English',
                    'price' => 0.00,
                    'is_free' => true,
                    'is_copyright' => false,
                    'status' => 'published',
                    'description' => $ba['description'],
                    'uploaded_by' => $adminId,
                ]
            );
        }

        // 4. Legal Maxims
        $maximsData = [
            [
                'title' => 'Ubi jus ibi remedium',
                'latin_maxim' => 'Ubi jus ibi remedium',
                'english_meaning' => 'Where there is a right, there is a remedy.',
                'hindi_meaning' => 'जहाँ अधिकार है, वहाँ उपचार भी है।',
                'description' => 'A fundamental common law principle that a court will provide an effective legal remedy for an infringement of a recognized legal right.',
                'legal_explanation' => 'First authoritatively applied in Ashby v. White (1703) where Holt C.J. held that if the plaintiff has a right, he must of necessity have a means to vindicate and maintain it.',
                'example' => 'Deprivation of lawful voting right creates an actionable tort even without pecuniary loss.',
                'reference' => 'Ashby v. White (1703) 2 Ld. Raym. 938',
                'category_id' => $resCategories['Civil Law & Procedure']->id,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Audi alteram partem',
                'latin_maxim' => 'Audi alteram partem',
                'english_meaning' => 'No person shall be condemned unheard / Hear the other side.',
                'hindi_meaning' => 'दूसरे पक्ष को भी सुनो (निष्पक्ष सुनवाई का नियम)।',
                'description' => 'The cardinal second pillar of natural justice ensuring fair hearing before an adverse administrative or judicial order.',
                'legal_explanation' => 'Mandates proper prior notice and reasonable opportunity to present defense before passing an order affecting personal rights or livelihood.',
                'example' => 'Termination of public employment or suspension of passport without show cause notice violates natural justice.',
                'reference' => 'Maneka Gandhi v. Union of India (1978) 1 SCC 248',
                'category_id' => $resCategories['Constitutional Law']->id,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Nemo debet esse judex in propria causa',
                'latin_maxim' => 'Nemo judex in causa sua',
                'english_meaning' => 'No one should be a judge in their own cause.',
                'hindi_meaning' => 'कोई भी अपने मामले में स्वयं न्यायाधीश नहीं हो सकता (पक्षपात के विरुद्ध नियम)।',
                'description' => 'Rule against judicial, pecuniary, personal, or subject-matter bias.',
                'legal_explanation' => 'Justice must not only be done, but manifestly and undoubtedly be seen to be done.',
                'example' => 'A selection committee member cannot participate when an immediate relative is an applicant.',
                'reference' => 'A.K. Kraipak v. Union of India (1969) 2 SCC 262',
                'category_id' => $resCategories['Constitutional Law']->id,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Ignorantia juris non excusat',
                'latin_maxim' => 'Ignorantia juris neminem excusat',
                'english_meaning' => 'Ignorance of the law excuses no one.',
                'hindi_meaning' => 'विधि की अनभिज्ञता कोई क्षमा नहीं है।',
                'description' => 'A person who commits an offense cannot escape penal liability by pleading they were unaware of the statute.',
                'legal_explanation' => 'Presumes every competent citizen knows the applicable laws of the territory.',
                'example' => 'Violating mandatory traffic signals cannot be defended on the ground of not knowing the rules.',
                'reference' => 'State of Maharashtra v. Mayer Hans George, AIR 1965 SC 722',
                'category_id' => $resCategories['Criminal Law & Procedure']->id,
                'created_by' => $adminId,
            ],
            [
                'title' => 'Actus non facit reum nisi mens sit rea',
                'latin_maxim' => 'Actus non facit reum nisi mens sit rea',
                'english_meaning' => 'An act does not make a person guilty unless the mind is also guilty.',
                'hindi_meaning' => 'केवल कार्य किसी को दोषी नहीं बनाता, जब तक मन में दुर्भावना न हो।',
                'description' => 'The cornerstone doctrine of criminal culpability requiring both physical act (actus reus) and culpable mental state (mens rea).',
                'legal_explanation' => 'Unless an express statute creates strict liability, criminal conviction requires showing intentional, reckless, or knowing state of mind.',
                'example' => 'Accidental taking of someone else’s identical coat in a restaurant without dishonest intent is not theft.',
                'reference' => 'Fowler v. Padget (1798) 7 TR 509',
                'category_id' => $resCategories['Criminal Law & Procedure']->id,
                'created_by' => $adminId,
            ],
        ];

        foreach ($maximsData as $m) {
            LegalMaxim::updateOrCreate(['latin_maxim' => $m['latin_maxim']], $m);
        }

        // 5. Landmark Judgments
        $judgmentsData = [
            [
                'case_name' => 'Kesavananda Bharati Sripadagalvaru v. State of Kerala',
                'case_number' => 'Writ Petition (Civil) No. 135 of 1970',
                'court' => 'Supreme Court of India',
                'bench' => '13-Judge Constitutional Bench (S.M. Sikri C.J. et al.)',
                'judgment_date' => '1973-04-24',
                'citation' => '(1973) 4 SCC 225 : AIR 1973 SC 1461',
                'legal_principle' => 'Basic Structure Doctrine: Parliament has vast powers to amend the Constitution under Article 368 but cannot alter or destroy its basic foundation and framework.',
                'case_summary' => 'In a historic 7:6 majority verdict, the Supreme Court overruled Golaknath and held that while Article 368 empowers Parliament to amend any provision of the Constitution including Fundamental Rights, that amending power is subject to the inherent limitation that the basic structure of the Constitution cannot be damaged or emasculated.',
                'full_judgment' => "JUDGMENT BY SIKRI, C.J.: The question of the scope of amending power under Article 368 of the Constitution arose primarily in the context of property rights and land reforms...\nHELD: The expression 'amendment of this Constitution' does not enable Parliament to alter the basic structure or framework of the Constitution.",
                'category_id' => $resCategories['Constitutional Law']->id,
                'year' => 1973,
                'is_free' => true,
                'created_by' => $adminId,
            ],
            [
                'case_name' => 'Maneka Gandhi v. Union of India',
                'case_number' => 'Writ Petition No. 231 of 1977',
                'court' => 'Supreme Court of India',
                'bench' => '7-Judge Bench (M.H. Beg C.J., P.N. Bhagwati et al.)',
                'judgment_date' => '1978-01-25',
                'citation' => '(1978) 1 SCC 248 : AIR 1978 SC 597',
                'legal_principle' => 'Article 21 and the "Golden Triangle" test: Procedure depriving life or liberty under Article 21 must be just, fair, and reasonable, and not arbitrary, fanciful, or oppressive.',
                'case_summary' => 'Impounding of petitioner’s passport under Section 10(3)(c) of Passports Act without giving reasons or opportunity of hearing was challenged. The Court synthesized Articles 14, 19, and 21, establishing that natural justice is an integral part of fair procedure.',
                'full_judgment' => "BHAGWATI, J.: The principle of reasonableness, which legally as well as philosophically, is an essential element of equality or non-arbitrariness pervades Article 14 like a brooding omnipresence...",
                'category_id' => $resCategories['Constitutional Law']->id,
                'year' => 1978,
                'is_free' => true,
                'created_by' => $adminId,
            ],
            [
                'case_name' => 'Justice K.S. Puttaswamy (Retd.) v. Union of India',
                'case_number' => 'Writ Petition (Civil) No. 494 of 2012',
                'court' => 'Supreme Court of India',
                'bench' => '9-Judge Constitutional Bench (J.S. Khehar C.J. et al.)',
                'judgment_date' => '2017-08-24',
                'citation' => '(2017) 10 SCC 1 : AIR 2017 SC 4161',
                'legal_principle' => 'Right to Privacy is a fundamental right emanating from the guarantee of life and personal liberty in Article 21 and Part III of the Constitution.',
                'case_summary' => 'Unanimously held that privacy is intrinsic to freedom, dignity, and autonomy of the individual, overruling MP Sharma (1954) and Kharak Singh (1962) to the extent they held privacy is not protected.',
                'full_judgment' => "CHANDRACHUD, J.: Privacy is the constitutional core of human dignity. It enables the preservation of personal intimacies, the sanctity of family life, marriage, procreation, the home, and sexual orientation...",
                'category_id' => $resCategories['Constitutional Law']->id,
                'year' => 2017,
                'is_free' => true,
                'created_by' => $adminId,
            ],
        ];

        foreach ($judgmentsData as $jd) {
            LandmarkJudgment::updateOrCreate(['case_name' => $jd['case_name']], $jd);
        }

        // 6. Examination Categories & Labels
        $examCatJudicial = ExaminationCategory::updateOrCreate(
            ['slug' => 'judicial-services'],
            ['name' => 'Judicial Services Examination', 'description' => 'Competitive examinations for Provincial Civil Services Judicial (PCS-J) across states.']
        );
        $examCatBar = ExaminationCategory::updateOrCreate(
            ['slug' => 'bar-council-aibe'],
            ['name' => 'Bar Council (AIBE)', 'description' => 'All India Bar Examination qualification for legal practice.']
        );

        $labelHjs = ExaminationLabel::updateOrCreate(
            ['name' => 'Haryana Judicial Services 2026'],
            ['label_type' => 'First Label', 'description' => 'Targeted mock series for HJS Preliminary Examination']
        );
        $labelLaw1 = ExaminationLabel::updateOrCreate(
            ['name' => 'Law Paper I - General Laws'],
            ['label_type' => 'Second Label', 'description' => 'Covers Constitution, Penal Code, Evidence, and Procedure']
        );

        // 7. Question Bank
        $questions = [
            [
                'question_code' => 'QS-CONST-001',
                'question_title' => 'Which Article of the Constitution of India guarantees the Right to Constitutional Remedies?',
                'question_description' => 'Referred to by Dr. B.R. Ambedkar as the "heart and soul of the Constitution".',
                'question_type' => 'mcq',
                'options' => [
                    ['id' => 'A', 'text' => 'Article 14'],
                    ['id' => 'B', 'text' => 'Article 19'],
                    ['id' => 'C', 'text' => 'Article 21'],
                    ['id' => 'D', 'text' => 'Article 32'],
                ],
                'correct_answer' => 'D',
                'explanation' => 'Article 32 provides the right to move the Supreme Court by appropriate proceedings for the enforcement of the Fundamental Rights.',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'difficulty' => 'easy',
                'year' => 2024,
                'created_by' => $adminId,
            ],
            [
                'question_code' => 'QS-CONST-002',
                'question_title' => 'In which landmark case was the "Basic Structure Doctrine" propounded by the Supreme Court of India?',
                'question_description' => 'Decided by the largest 13-Judge bench in Supreme Court history.',
                'question_type' => 'mcq',
                'options' => [
                    ['id' => 'A', 'text' => 'Golaknath v. State of Punjab'],
                    ['id' => 'B', 'text' => 'Kesavananda Bharati v. State of Kerala'],
                    ['id' => 'C', 'text' => 'Minerva Mills v. Union of India'],
                    ['id' => 'D', 'text' => 'Maneka Gandhi v. Union of India'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Kesavananda Bharati (1973) 4 SCC 225 established that Parliament cannot alter the basic structure of the Constitution under Article 368.',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'difficulty' => 'medium',
                'year' => 2024,
                'created_by' => $adminId,
            ],
            [
                'question_code' => 'QS-BNS-003',
                'question_title' => 'Under Bharatiya Nyaya Sanhita, 2023 (BNS), which section deals with the offense of Murder?',
                'question_description' => 'Replacing Section 302 of the Indian Penal Code, 1860.',
                'question_type' => 'mcq',
                'options' => [
                    ['id' => 'A', 'text' => 'Section 101'],
                    ['id' => 'B', 'text' => 'Section 103'],
                    ['id' => 'C', 'text' => 'Section 105'],
                    ['id' => 'D', 'text' => 'Section 109'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Section 103 of BNS 2023 prescribes punishment for murder, replacing Section 302 of the IPC.',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'difficulty' => 'hard',
                'year' => 2024,
                'created_by' => $adminId,
            ],
            [
                'question_code' => 'QS-BSA-004',
                'question_title' => 'Under Bharatiya Sakshya Adhiniyam, 2023 (BSA), electronic and digital records are treated as:',
                'question_description' => 'Provisions relating to digital evidence admissibility.',
                'question_type' => 'mcq',
                'options' => [
                    ['id' => 'A', 'text' => 'Strictly secondary evidence with no direct weight'],
                    ['id' => 'B', 'text' => 'Primary evidence having the same legal effect as paper documents'],
                    ['id' => 'C', 'text' => 'Inadmissible unless certified by foreign authority'],
                    ['id' => 'D', 'text' => 'Only corroborative evidence'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Section 61 of BSA 2023 elevates electronic/digital records to hold the same legal status and effect as original documentary records.',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'difficulty' => 'medium',
                'year' => 2024,
                'created_by' => $adminId,
            ],
            [
                'question_code' => 'QS-MAXIM-005',
                'question_title' => 'What is the literal translation of the maxim "Res ipsa loquitur"?',
                'question_description' => 'Evidentiary rule applied frequently in tort and negligence actions.',
                'question_type' => 'mcq',
                'options' => [
                    ['id' => 'A', 'text' => 'Hear the other side'],
                    ['id' => 'B', 'text' => 'The thing speaks for itself'],
                    ['id' => 'C', 'text' => 'Where there is right, there is remedy'],
                    ['id' => 'D', 'text' => 'Damage without injury'],
                ],
                'correct_answer' => 'B',
                'explanation' => 'Res ipsa loquitur is a doctrine under which a court may infer negligence from the very nature of an accident or injury in the absence of direct evidence.',
                'marks' => 2.00,
                'negative_marks' => 0.50,
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'difficulty' => 'easy',
                'year' => 2024,
                'created_by' => $adminId,
            ],
        ];

        $createdQuestions = [];
        foreach ($questions as $q) {
            $createdQuestions[] = Question::updateOrCreate(['question_code' => $q['question_code']], $q);
        }

        // 8. Examination Model & Question Linking
        $exam = Examination::updateOrCreate(
            ['exam_code' => 'EXAM-HJS-2026-MOCK1'],
            [
                'title' => 'Haryana Judicial Services Prelims Grand Mock 2026',
                'description' => 'Comprehensive full-length simulation of the Haryana Judicial Services (PCS-J) Preliminary Exam, testing Constitution, BNS, BNSS, BSA, and General Jurisprudence.',
                'category_id' => $examCatJudicial->id,
                'first_label_id' => $labelHjs->id,
                'second_label_id' => $labelLaw1->id,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(30),
                'duration_minutes' => 60,
                'total_marks' => 10.00,
                'passing_marks' => 5.00,
                'cutoff_percentage' => 50.00,
                'negative_marking_ratio' => 0.25,
                'flag_negative_marking' => true,
                'instructions' => "1. Read all questions carefully.\n2. Each question carries 2 marks. 0.50 marks will be deducted for every incorrect attempt.\n3. You may mark questions for review and return to them anytime before time expires.\n4. Click Submit Exam when finished.",
                'status' => 'published',
                'max_attempts' => 5,
                'shuffle_questions' => false,
                'show_result_immediately' => true,
                'created_by' => $adminId,
            ]
        );

        // Link questions to examination
        foreach ($createdQuestions as $idx => $qObj) {
            $exam->questions()->syncWithoutDetaching([
                $qObj->id => ['order' => $idx + 1, 'marks' => $qObj->marks],
            ]);
        }

        // 9. Previous Year Papers
        PreviousYearPaper::updateOrCreate(
            ['title' => 'Delhi Judicial Services (DJS) Prelims Solved Paper 2023'],
            [
                'exam_name' => 'Delhi Judicial Services (DJS)',
                'year' => 2023,
                'category_id' => $examCatJudicial->id,
                'subject' => 'Civil & Criminal Law',
                'explanation' => 'Complete answer key with statutory explanations for all 200 questions.',
                'is_free' => true,
                'created_by' => $adminId,
            ]
        );

        // 10. Blogging
        $blogCat = BlogCategory::updateOrCreate(
            ['slug' => 'constitutional-developments'],
            ['name' => 'Constitutional Developments', 'description' => 'In-depth analysis of emerging judicial doctrines and constitutional amendments.']
        );

        Blog::updateOrCreate(
            ['slug' => 'evolution-of-constitutional-law-in-india'],
            [
                'title' => 'The Evolution of Constitutional Law in India: A Historical Perspective',
                'category_id' => $blogCat->id,
                'keywords' => 'Constitution, Basic Structure, Kesavananda, Due Process, Fundamental Rights',
                'tags' => 'Indian Constitution, Constitutional Law, Supreme Court',
                'content' => "<p>The constitutional narrative of India since 1950 reflects an extraordinary journey of judicial resilience, dynamic interpretation, and the progressive democratization of rights. Through landmark decisions spanning <em>A.K. Gopalan</em>, <em>Golaknath</em>, <em>Kesavananda Bharati</em>, and <em>Maneka Gandhi</em>, the Supreme Court has transitioned from mechanical formalism to transformative constitutionalism.</p><p>Today, constitutional remedies encompass socio-economic guarantees, environmental jurisprudence under Article 21, and algorithmic transparency.</p>",
                'references' => "1. H.M. Seervai, Constitutional Law of India (4th Ed.)\n2. Granvil Austin, The Indian Constitution: Cornerstone of a Nation\n3. Kesavananda Bharati v. State of Kerala (1973) 4 SCC 225",
                'seo_title' => 'The Evolution of Constitutional Law in India - Law Universe',
                'meta_description' => 'Comprehensive historical and jurisprudential analysis of constitutional law developments in India from 1950 to 2026.',
                'publication_date' => now()->subDays(10),
                'status' => 'published',
                'views_count' => 1420,
                'author_id' => $adminId,
            ]
        );

        // 11. Finance Packages, Features, and Coupons
        $featBareActs = Feature::updateOrCreate(
            ['slug' => 'bare-acts-unlimited'],
            ['name' => 'Unlimited Bare Acts Access', 'module_scope' => 'manage_resources', 'feature_type' => 'boolean', 'default_value' => '1']
        );
        $featAi = Feature::updateOrCreate(
            ['slug' => 'ai-assistant-access'],
            ['name' => 'Legal AI Assistant Queries', 'module_scope' => 'manage_ai_chatbot', 'feature_type' => 'usage_limit', 'default_value' => '100']
        );
        $featExams = Feature::updateOrCreate(
            ['slug' => 'mock-tests-attempt'],
            ['name' => 'Judicial Mock Exams Access', 'module_scope' => 'manage_examinations', 'feature_type' => 'boolean', 'default_value' => '1']
        );
        $featDownloads = Feature::updateOrCreate(
            ['slug' => 'judgment-pdf-downloads'],
            ['name' => 'Judgment PDF Downloads', 'module_scope' => 'manage_resources', 'feature_type' => 'download_limit', 'default_value' => '50']
        );

        $packages = [
            [
                'plan_code' => 'PLAN-FREE-PUBLIC',
                'title' => 'Public Free Access',
                'subtitle' => 'Basic Citizen Legal Research',
                'tagline' => 'Free access to public bare acts and legal maxims',
                'pre_discount_price' => 0.00,
                'current_price' => 0.00,
                'duration_days' => 365,
                'description' => 'Designed for citizens and legal enthusiasts. Free access to public bare acts, Latin maxims, and notifications.',
                'status' => 'active',
                'display_order' => 1,
            ],
            [
                'plan_code' => 'PLAN-STUDENT-ASPIRANT',
                'title' => 'Student & Aspirant Pro Plan',
                'subtitle' => 'Judicial Exam Prep Suite',
                'tagline' => 'Full mock test engine and previous papers',
                'pre_discount_price' => 1499.00,
                'current_price' => 999.00,
                'duration_days' => 90,
                'description' => 'Unlimited mock exams, full question papers, solved previous year papers, and 100 AI legal tutor queries per month.',
                'status' => 'active',
                'display_order' => 2,
            ],
            [
                'plan_code' => 'PLAN-ADVOCATE-SUITE',
                'title' => 'Advocate Legal Workspace',
                'subtitle' => 'Complete Legal Practice Suite',
                'tagline' => 'AI Drafts, Precedents & Bare Acts',
                'pre_discount_price' => 4999.00,
                'current_price' => 2999.00,
                'duration_days' => 365,
                'description' => 'Comprehensive legal ecosystem for practicing advocates: advanced AI legal assistant, full precedent search, cause list notifications, and high-speed downloads.',
                'status' => 'active',
                'display_order' => 3,
            ],
            [
                'plan_code' => 'PLAN-JUDICIAL-CHAMBERS',
                'title' => 'Judicial Chambers Enterprise',
                'subtitle' => 'Judicial Research & Analysis',
                'tagline' => 'Unrestricted Access for Bench Officers',
                'pre_discount_price' => 9999.00,
                'current_price' => 5999.00,
                'duration_days' => 365,
                'description' => 'Institutional access for judicial officers and research clerks with high-priority GPT engine connectivity and legislative commentaries.',
                'status' => 'active',
                'display_order' => 4,
            ],
        ];

        foreach ($packages as $pkg) {
            $p = Package::updateOrCreate(['plan_code' => $pkg['plan_code']], $pkg);
            // Link features
            $p->features()->syncWithoutDetaching([
                $featBareActs->id => ['value' => '1'],
                $featAi->id => ['value' => '500'],
                $featExams->id => ['value' => '1'],
                $featDownloads->id => ['value' => '100'],
            ]);
        }

        Coupon::updateOrCreate(
            ['coupon_code' => 'WELCOME50'],
            [
                'coupon_name' => 'New User Welcome 50% Off',
                'description' => '50% discount on all student and advocate subscriptions.',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'start_date' => now()->subDays(30),
                'expiration_date' => now()->addDays(365),
                'usage_limit' => 1000,
                'per_user_limit' => 1,
                'status' => 'active',
            ]
        );

        Coupon::updateOrCreate(
            ['coupon_code' => 'DIWALI20'],
            [
                'coupon_name' => 'Festive 20% Off',
                'description' => 'Flat 20% discount on annual legal plans.',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'start_date' => now()->subDays(30),
                'expiration_date' => now()->addDays(365),
                'usage_limit' => 500,
                'per_user_limit' => 1,
                'status' => 'active',
            ]
        );

        // 12. AI Vendor
        AiVendor::updateOrCreate(
            ['vendor_name' => 'OpenAI GPT-4o'],
            [
                'api_base_url' => 'https://api.openai.com/v1',
                'api_key' => 'sk-dummy-test-key-for-local-development',
                'model_name' => 'gpt-4o',
                'api_version' => '2024-05-13',
                'email_address' => 'api.admin@lawuniverse.com',
                'validity_date' => now()->addYear(),
                'is_active' => true,
                'is_default' => true,
                'allowed_roles' => ['super_admin', 'admin', 'judge', 'advocate', 'student', 'public'],
            ]
        );

        AiVendor::updateOrCreate(
            ['vendor_name' => 'Google Gemini 1.5 Pro'],
            [
                'api_base_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'api_key' => 'ai-gemini-test-key',
                'model_name' => 'gemini-1.5-pro',
                'api_version' => 'v1beta',
                'email_address' => 'gemini@lawuniverse.com',
                'validity_date' => now()->addYear(),
                'is_active' => true,
                'is_default' => false,
                'allowed_roles' => ['super_admin', 'admin', 'judge', 'advocate'],
            ]
        );

        // 13. Notifications
        Notification::updateOrCreate(
            ['subject_code' => 'LU-NOTIF-001'],
            [
                'subject' => 'Launch of Bharatiya Nyaya Sanhita (BNS) 2023 Statutory Repository',
                'message' => 'The complete updated criminal laws (BNS, BNSS, and BSA) are now available in the Bare Acts and AI Research submodules.',
                'recipient_type' => 'all',
                'post_date' => now()->subDays(2),
                'status' => 'published',
                'created_by' => $adminId,
            ]
        );
    }
}
