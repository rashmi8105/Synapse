<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151104200846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSQL('CREATE UNIQUE INDEX title_unique ON report_sections (title);');

        $this->addSQL('CREATE UNIQUE INDEX sq_unique ON report_section_elements (survey_question_id);');

        $this->addSQL('CREATE UNIQUE INDEX element_bucket_unique ON report_element_buckets (element_id, bucket_name);');

        $this->addSQL('insert ignore into report_sections (report_id, title, sequence)
                    select id, "Earning the Grades You Want", 1
                    from reports where name = "student-report";');

        $this->addSQL('insert ignore into report_sections (report_id, title, sequence)
                    select id, "Connecting with Others", 2
                    from reports where name = "student-report";');

        $this->addSQL('insert ignore into report_sections (report_id, title, sequence)
                    select id, "Paying for College", 3
                    from reports where name = "student-report";');


        $this->addSQL('insert into report_section_elements (section_id, factor_id, title, description, source_type, icon_file_name)
                    select rs.id, fl.factor_id, "Basic Academic Behaviors", "Basic Academic Behaviors", "F", "area-icon-academic-basic.svg"
                    from report_sections rs, factor_lang fl
                    where rs.title = "Earning the Grades You Want"
                    and fl.name = "Basic Academic Behaviors"
                    and (select count(*) from report_section_elements rse where rse.factor_id=fl.factor_id and survey_id is null) = 0;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "It is likely that your academic behaviors are not sufficient to achieve good grades. You need to increase your study time, take better notes, and get organized. If you continue on your current path, you will likely be disappointed with your final grades. Don\'t get discouraged! Contact your campus connections faculty/staff members (see the list at the end of this report); they can help you.", 1.0000, 2.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Basic Academic Behaviors")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "The academic behaviors you reported are likely insufficient to achieve good grades. What worked in high school will not be enough to succeed in college. You need to increase your study time, take better notes, and get organized. Contact your campus connections  faculty/staff members (see the list at the end of this report); they can help you.", 3.0000, 5.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Basic Academic Behaviors")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You have excellent academic behaviors! These skills typically lead to good grades; keep up the good work! It is important to sustain your current level of performance throughout the entire term. Tip: Talking with your instructors is a proven way to get the most from your classes! At any point you need help, contact your campus connections faulty/staff members (see the list at the end of this report).", 6.0000, 7.0000
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Basic Academic Behaviors")
                    and survey_id is null;');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Class Attendance", "Class Attendance", "Q", "area-icon-attendance.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 20 and sq.survey_id = 11;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Your poor class attendance is putting you at serious risk of getting poor grades. Many students think that class attendance is not important. They\'re wrong. Research indicates that students who miss class earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance. Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 2.0000, 4.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "While you try to attend class on a consistent basis, you do miss a few here and there. Many students think that attending most classes is just as good as attending all classes, but research indicates they\'re wrong! Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "Congratulations! You understand the importance of class attendance. Many students don\'t realize that attending every class is crucial to academic success in college. Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 11);');


        $this->addSQL('insert into report_section_elements (section_id, factor_id, title, description, source_type, icon_file_name)
                    select rs.id, fl.factor_id, "Time Management", "Time Management", "F", "area-icon-time.svg"
                    from report_sections rs, factor_lang fl
                    where rs.title = "Earning the Grades You Want"
                    and fl.name = "Self-Assessment: Time Management"
                    and (select count(*) from report_section_elements rse where rse.factor_id=fl.factor_id and survey_id is null) = 0;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Your time management skills are in serious need of improvement. Balancing demands on your time can be difficult. Did you know that good time management skills are linked to higher GPAs? Improving your skills will allow you to be more efficient with your time and more effective in achieving good grades.", 1.0000, 2.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Self-Assessment: Time Management")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You have fair time management skills, but to achieve good grades you will likely need to improve them. It is important to organize your time and to keep academics high on your priority list. Did you know that good time management skills are linked to higher GPAs? Improving your skills will allow you to be more efficient with your time and more effective in achieving good grades.", 3.0000, 5.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Self-Assessment: Time Management")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You have good time management skills. Keep up the good work! As you know, it\'s important to organize your time and to keep academics high on your priority list. Did you know that good time management skills are linked to higher GPAs? Improving your skills will allow you to be more efficient with your time and more effective in achieving good grades.", 6.0000, 7.0000
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Self-Assessment: Time Management")
                    and survey_id is null;');


        $this->addSQL('insert into report_section_elements (section_id, factor_id, title, description, source_type, icon_file_name)
                    select rs.id, fl.factor_id, "Academic Self-Confidence", "Academic Self-Confidence", "F", "area-icon-academic-confidence.svg"
                    from report_sections rs, factor_lang fl
                    where rs.title = "Earning the Grades You Want"
                    and fl.name = "Academic Self-Efficacy"
                    and (select count(*) from report_section_elements rse where rse.factor_id=fl.factor_id and survey_id is null) = 0;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "You have low confidence in your academic abilities. Academic confidence helps students persevere when they face challenges and also helps them speak up in classes- both of which are connected with academic success. Contact one of us for suggestions on ways to improve your confidence.", 1.0000, 2.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Academic Self-Efficacy")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You have moderate confidence in your academic abilities. Students with moderate confidence tend to do well until they face a challenge. They might give up on a task early when they feel stressed. If this happens, contact one of us immediately for help.", 3.0000, 5.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Academic Self-Efficacy")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You have high confidence in your academic abilities. Feeling confident will help you persevere on projects even when there are challenges. Confident students are also more likely to connect with their instructors and get the most out of each class. They also tend to earn higher grades.", 6.0000, 7.0000
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Academic Self-Efficacy")
                    and survey_id is null;');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Course Difficulties", "Course Difficulties", "Q", "area-icon-course-difficulties.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 11 and sq.survey_id = 11;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "You\'re struggling in at least two courses. Struggling in multiple courses dramatically increases your risk of earning a poor GPA. First, talk to those course instructors today. Don\'t wait. Second, contact one of us; we can help. To achieve the grades you expect you must take action now!", 2.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re struggling in a course. Most students do encounter difficulties from time to time. It is important to talk to that course instructor today. Don\'t wait. To achieve the grades you expect you must take action now!", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You reported that you aren\'t having any difficulties with your courses. That\'s great news! If you do need help, please don\'t hesitate to contact one of us immediately.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 11);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Campus Involvement", "Campus Involvement", "Q", "area-icon-campus-involvement.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 104 and sq.survey_id = 11;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Sometimes it\'s hard to justify participating in campus activities. But, they are an essential part of your college experience. You may feel that you don\'t have enough time or you may have other responsibilities, such as family or work obligations, that keep you from getting involved. However, even some involvement will create a more rewarding college experience. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re not very interested in getting involved on campus at this time. Please reconsider. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You want to be involved on our campus? That\'s great news! We love your enthusiasm. Getting involved is a great way to make the most of your college experience. The foundation for success extends beyond the classroom; the opportunities and experiences provided by campus activities are as important as your classroom experience when it comes to your future success.", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 11);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Sense of Belonging", "Sense of Belonging", "Q", "area-icon-belonging.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 213 and sq.survey_id = 11;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Adjusting to a new environment and social situation takes time. In the beginning you may feel like you don\'t fit in. This is a common feeling of most students. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! If you need suggestions, please reach out to one of us so we can help.", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "Everyone feels like an outsider at one time or another. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! Give yourself time to adjust to this environment. You\'re only just beginning to meet and connect with people who share your interests, values, or professional goals.", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 11);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "It\'s great news that you feel connected on campus! Remember that there are always new opportunities to make friends and build new relationships through student organizations, intramural sports, and community service projects!", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 11);');


        $this->addSQL('insert into report_section_elements (section_id, factor_id, title, description, source_type, icon_file_name)
                    select rs.id, fl.factor_id, "Financial Confidence", "Financial Confidence", "F", "area-icon-financial-01.svg"
                    from report_sections rs, factor_lang fl
                    where rs.title = "Paying for College"
                    and fl.name = "Financial Means"
                    and (select count(*) from report_section_elements rse where rse.factor_id=fl.factor_id and survey_id is null) = 0;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "To stay in college you have to pay for college, and if you\'re a little unsure of how you\'ll pay for next term\'s tuition and fees, you\'re not alone! Don\'t wait; contact the financial aid office for help right away. There are people here who can help you find ways to meet your financial needs. A list of campus resources is provided in this report to help you along.", 1.0000, 2.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Financial Means")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "To stay in college you have to pay for college, and if you\'re a little unsure of how you\'ll pay for next term\'s tuition and fees, you\'re not alone! Don\'t wait; contact the financial aid office for help right away. There are people here who can help you find ways to meet your financial needs. A list of campus resources is provided in this report to help you along.", 3.0000, 5.9999
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Financial Means")
                    and survey_id is null;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "To stay in college you have to pay for college, and if you\'re a little unsure of how you\'ll pay for next term\'s tuition and fees, you\'re not alone! Don\'t wait; contact the financial aid office for help right away. There are people here who can help you find ways to meet your financial needs. A list of campus resources is provided in this report to help you along.", 6.0000, 7.0000
                    from report_section_elements
                    where factor_id = (select factor_id from factor_lang where name = "Financial Means")
                    and survey_id is null;');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Class Attendance", "Class Attendance", "Q", "area-icon-attendance.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 20 and sq.survey_id = 12;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Your poor class attendance is putting you at serious risk of getting poor grades. Many students think that class attendance is not important. They\'re wrong. Research indicates that students who miss class earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance. Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 2.0000, 4.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "While you try to attend class on a consistent basis, you do miss a few here and there. Many students think that attending most classes is just as good as attending all classes, but research indicates they\'re wrong! Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "Congratulations! You understand the importance of class attendance. Many students don\'t realize that attending every class is crucial to academic success in college. Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 12);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Course Difficulties", "Course Difficulties", "Q", "area-icon-course-difficulties.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 11 and sq.survey_id = 12;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "You\'re struggling in at least two courses. Struggling in multiple courses dramatically increases your risk of earning a poor GPA. First, talk to those course instructors today. Don\'t wait. Second, contact one of us; we can help. To achieve the grades you expect you must take action now!", 2.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re struggling in a course. Most students do encounter difficulties from time to time. It is important to talk to that course instructor today. Don\'t wait. To achieve the grades you expect you must take action now!", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You reported that you aren\'t having any difficulties with your courses. That\'s great news! If you do need help, please don\'t hesitate to contact one of us immediately.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 12);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Campus Involvement", "Campus Involvement", "Q", "area-icon-campus-involvement.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 104 and sq.survey_id = 12;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Sometimes it\'s hard to justify participating in campus activities. But, they are an essential part of your college experience. You may feel that you don\'t have enough time or you may have other responsibilities, such as family or work obligations, that keep you from getting involved. However, even some involvement will create a more rewarding college experience. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re not very interested in getting involved on campus at this time. Please reconsider. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You want to be involved on our campus? That\'s great news! We love your enthusiasm. Getting involved is a great way to make the most of your college experience. The foundation for success extends beyond the classroom; the opportunities and experiences provided by campus activities are as important as your classroom experience when it comes to your future success.", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 12);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Sense of Belonging", "Sense of Belonging", "Q", "area-icon-belonging.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 213 and sq.survey_id = 12;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Adjusting to a new environment and social situation takes time. In the beginning you may feel like you don\'t fit in. This is a common feeling of most students. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! If you need suggestions, please reach out to one of us so we can help.", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "Everyone feels like an outsider at one time or another. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! Give yourself time to adjust to this environment. You\'re only just beginning to meet and connect with people who share your interests, values, or professional goals.", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 12);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "It\'s great news that you feel connected on campus! Remember that there are always new opportunities to make friends and build new relationships through student organizations, intramural sports, and community service projects!", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 12);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Class Attendance", "Class Attendance", "Q", "area-icon-attendance.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 20 and sq.survey_id = 13;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Your poor class attendance is putting you at serious risk of getting poor grades. Many students think that class attendance is not important. They\'re wrong. Research indicates that students who miss class earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance. Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 2.0000, 4.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "While you try to attend class on a consistent basis, you do miss a few here and there. Many students think that attending most classes is just as good as attending all classes, but research indicates they\'re wrong! Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "Congratulations! You understand the importance of class attendance. Many students don\'t realize that attending every class is crucial to academic success in college. Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 13);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Course Difficulties", "Course Difficulties", "Q", "area-icon-course-difficulties.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 11 and sq.survey_id = 13;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "You\'re struggling in at least two courses. Struggling in multiple courses dramatically increases your risk of earning a poor GPA. First, talk to those course instructors today. Don\'t wait. Second, contact one of us; we can help. To achieve the grades you expect you must take action now!", 2.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re struggling in a course. Most students do encounter difficulties from time to time. It is important to talk to that course instructor today. Don\'t wait. To achieve the grades you expect you must take action now!", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You reported that you aren\'t having any difficulties with your courses. That\'s great news! If you do need help, please don\'t hesitate to contact one of us immediately.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 13);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Campus Involvement", "Campus Involvement", "Q", "area-icon-campus-involvement.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 104 and sq.survey_id = 13;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Sometimes it\'s hard to justify participating in campus activities. But, they are an essential part of your college experience. You may feel that you don\'t have enough time or you may have other responsibilities, such as family or work obligations, that keep you from getting involved. However, even some involvement will create a more rewarding college experience. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re not very interested in getting involved on campus at this time. Please reconsider. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You want to be involved on our campus? That\'s great news! We love your enthusiasm. Getting involved is a great way to make the most of your college experience. The foundation for success extends beyond the classroom; the opportunities and experiences provided by campus activities are as important as your classroom experience when it comes to your future success.", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 13);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Sense of Belonging", "Sense of Belonging", "Q", "area-icon-belonging.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 213 and sq.survey_id = 13;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Adjusting to a new environment and social situation takes time. In the beginning you may feel like you don\'t fit in. This is a common feeling of most students. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! If you need suggestions, please reach out to one of us so we can help.", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "Everyone feels like an outsider at one time or another. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! Give yourself time to adjust to this environment. You\'re only just beginning to meet and connect with people who share your interests, values, or professional goals.", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 13);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "It\'s great news that you feel connected on campus! Remember that there are always new opportunities to make friends and build new relationships through student organizations, intramural sports, and community service projects!", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 13);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Class Attendance", "Class Attendance", "Q", "area-icon-attendance.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 20 and sq.survey_id = 14;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Your poor class attendance is putting you at serious risk of getting poor grades. Many students think that class attendance is not important. They\'re wrong. Research indicates that students who miss class earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance. Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 2.0000, 4.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "While you try to attend class on a consistent basis, you do miss a few here and there. Many students think that attending most classes is just as good as attending all classes, but research indicates they\'re wrong! Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "Congratulations! You understand the importance of class attendance. Many students don\'t realize that attending every class is crucial to academic success in college. Did you know that students who miss a class every once in a while earn an average GPA nearly a letter grade lower than students with perfect or near perfect class attendance? Furthermore, it is important to be active in class. Being mentally present in the class is just as important as being physically present.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 20 and survey_id = 14);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Course Difficulties", "Course Difficulties", "Q", "area-icon-course-difficulties.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Earning the Grades You Want"
                    and sq.qnbr = 11 and sq.survey_id = 14;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "You\'re struggling in at least two courses. Struggling in multiple courses dramatically increases your risk of earning a poor GPA. First, talk to those course instructors today. Don\'t wait. Second, contact one of us; we can help. To achieve the grades you expect you must take action now!", 2.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re struggling in a course. Most students do encounter difficulties from time to time. It is important to talk to that course instructor today. Don\'t wait. To achieve the grades you expect you must take action now!", 1.0000, 1.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You reported that you aren\'t having any difficulties with your courses. That\'s great news! If you do need help, please don\'t hesitate to contact one of us immediately.", 0.0000, 0.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 11 and survey_id = 14);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Campus Involvement", "Campus Involvement", "Q", "area-icon-campus-involvement.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 104 and sq.survey_id = 14;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Sometimes it\'s hard to justify participating in campus activities. But, they are an essential part of your college experience. You may feel that you don\'t have enough time or you may have other responsibilities, such as family or work obligations, that keep you from getting involved. However, even some involvement will create a more rewarding college experience. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "You\'re not very interested in getting involved on campus at this time. Please reconsider. We have student organizations, intramural sports, and interesting clubs to offer you. Looking back on your college years, you may not remember every class you took, but you will remember the clubs you were a part of and the events you shared with your friends. Get involved and you\'ll be glad you did!", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "You want to be involved on our campus? That\'s great news! We love your enthusiasm. Getting involved is a great way to make the most of your college experience. The foundation for success extends beyond the classroom; the opportunities and experiences provided by campus activities are as important as your classroom experience when it comes to your future success.", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 104 and survey_id = 14);');


        $this->addSQL('insert ignore into report_section_elements (section_id, survey_question_id, title, description, source_type, icon_file_name)
                    select rs.id, sq.id, "Sense of Belonging", "Sense of Belonging", "Q", "area-icon-belonging.svg"
                    from report_sections rs, survey_questions sq
                    where rs.title = "Connecting with Others"
                    and sq.qnbr = 213 and sq.survey_id = 14;');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Red", "Adjusting to a new environment and social situation takes time. In the beginning you may feel like you don\'t fit in. This is a common feeling of most students. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! If you need suggestions, please reach out to one of us so we can help.", 1.0000, 2.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Yellow", "Everyone feels like an outsider at one time or another. A great way to make friends and build new relationships is by joining a student organization, playing intramural sports, and/or volunteering for service projects. Don\'t be afraid to try new things! Give yourself time to adjust to this environment. You\'re only just beginning to meet and connect with people who share your interests, values, or professional goals.", 3.0000, 5.9999
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 14);');

        $this->addSQL('insert ignore into report_element_buckets (element_id, bucket_name, bucket_text, range_min, range_max)
                    select id, "Green", "It\'s great news that you feel connected on campus! Remember that there are always new opportunities to make friends and build new relationships through student organizations, intramural sports, and community service projects!", 6.0000, 7.0000
                    from report_section_elements
                    where survey_question_id = (select id from survey_questions where qnbr = 213 and survey_id = 14);');


        $this->addSQL('DROP INDEX title_unique ON report_sections;');

        $this->addSQL('DROP INDEX sq_unique ON report_section_elements;');

        $this->addSQL('DROP INDEX element_bucket_unique ON report_element_buckets;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
