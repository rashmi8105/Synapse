<?php

use Codeception\TestCase\Test;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionTrackingGroupRepository;


class OrgPersonStudentRetentionTrackingGroupRepositoryTest extends Test
{
    use Codeception\Specify;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var OrgPersonStudentRetentionTrackingGroupRepository
     */
    private $orgPersonStudentRetentionTrackingGroupRepository;


    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonStudentRetentionTrackingGroupRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRetentionTrackingGroupRepository::REPOSITORY_KEY);
    }

    public function testGetRetentionCompletionVariablesByOrganization()
    {
        $this->specify("Verify the functionality of the method getRetentionCompletionVariablesByOrganization", function ($organizationId, $yearId, $studentIds, $expectedCount, $expectedResults = []) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionCompletionVariablesByOrganization($organizationId, $yearId, $studentIds);

            $this->assertEquals($expectedCount, count($result));
            if (count($result) > 5) {
                $result = array_slice($result, 0, 5);
            }
            $this->assertEquals($expectedResults, $result);
        }, ["examples" =>
            [
                // these examples compares count of total results and result array (first five only)
                //example with student having retention completion variables
                [
                    59, //organization_id
                    201617, // year id
                    [4615031], // student ids
                    1, //total results,
                    [
                        [
                            'external_id' => '4615031',
                            'firstname' => 'Amari',
                            'lastname' => 'Allen',
                            'primary_email' => 'MapworksBetaUser04615031@mailinator.com',
                            'retention_tracking_year' => '201617',
                            'retention_tracking_year_name' => '2016-17 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => NULL,
                            'retained_to_midyear_year_3' => NULL,
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => NULL,
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                    ] //expected result array
                ],
                // example with student having no retention completion variables
                [
                    59,
                    201415,
                    [184907],
                    0,
                    []
                ],
                // example where retention completion variables exists for a given year
                [
                    59,
                    201516,
                    [],
                    1440,
                    [
                        [
                            'external_id' => '4614721',
                            'firstname' => 'Christopher',
                            'lastname' => 'Bullock',
                            'primary_email' => 'MapworksBetaUser04614721@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '1',
                            'completed_degree_in_3_years_or_less' => '1',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614728',
                            'firstname' => 'Ryan',
                            'lastname' => 'Kerr',
                            'primary_email' => 'MapworksBetaUser04614728@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614729',
                            'firstname' => 'Nathan',
                            'lastname' => 'Stout',
                            'primary_email' => 'MapworksBetaUser04614729@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '1',
                            'completed_degree_in_3_years_or_less' => '1',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614735',
                            'firstname' => 'Jonathan',
                            'lastname' => 'Pruitt',
                            'primary_email' => 'MapworksBetaUser04614735@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614736',
                            'firstname' => 'Levi',
                            'lastname' => 'Buck',
                            'primary_email' => 'MapworksBetaUser04614736@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                    ]
                ],
                // example where no retention completion variables exists for a given year
                [
                    59,
                    201314,
                    [],
                    0,
                    []
                ],
                // example with all students with no year provided
                [
                    59,
                    null,
                    [4614721, 4614728, 4614729, 4614735, 4614736, 4614748, 4614752, 4614756, 4614757, 4614761, 4614765, 4614823, 4614839, 4614858, 4614859, 4614860, 4614861, 4614863, 4614864, 4614865, 4614868, 4614869, 4614873, 4614879, 4614881, 4614882, 4614884, 4614885, 4614886, 4614887, 4614888, 4614889, 4614892, 4614893, 4614894, 4614896, 4614897, 4614899, 4614900, 4614902, 4614903, 4614904, 4614905, 4614907, 4614910, 4614913, 4614916, 4614920, 4614922, 4614926, 4614929, 4614930, 4614932, 4614933, 4614935, 4614936, 4614937, 4614938, 4614940, 4614941, 4614942, 4614944, 4614945, 4614950, 4614952, 4614954, 4614955, 4614958, 4614959, 4614961, 4614965, 4614967, 4614971, 4614972, 4614975, 4614976, 4614980, 4614984, 4614986, 4614988, 4614989, 4614990, 4614992, 4614994, 4614999, 4615003, 4615004, 4615010, 4615015, 4615016, 4615017, 4615019, 4615022, 4615026, 4615031, 4615034, 4615039, 4615040, 4615041, 4615045, 4615046, 4615051, 4615052, 4615055, 4615056, 4615058, 4615066, 4615071, 4615074, 4615076, 4615079, 4615083, 4615086, 4615087, 4615089, 4615091, 4615094, 4615097, 4615098, 4615099, 4615100, 4615109, 4615111, 4615112, 4615113, 4615114, 4615117, 4615118, 4615119, 4615120, 4615122, 4615123, 4615126, 4615127, 4615128, 4615129, 4615130, 4615131, 4615132, 4615135, 4615137, 4615138, 4615139, 4615140, 4615142, 4615144, 4615147, 4615148, 4615149, 4615151, 4615152, 4615156, 4615157, 4615160, 4615162, 4615164, 4615166, 4615168, 4615169, 4615170, 4615174, 4615175, 4615179, 4615180, 4615181, 4615182, 4615185, 4615186, 4615187, 4615188, 4615189, 4615193, 4615195, 4615202, 4615205, 4615206, 4615207, 4615209, 4615211, 4615213, 4615214, 4615220, 4615221, 4615223, 4615225, 4615229, 4615230, 4615233, 4615234, 4615238, 4615239, 4615240, 4615241, 4615243, 4615244, 4615245, 4615249, 4615250, 4615255, 4615259, 4615260, 4615261, 4615262, 4615264, 4615267, 4615268, 4615273, 4615275, 4615276, 4615277, 4615282, 4615283, 4615284, 4615288, 4615290, 4615291, 4615295, 4615298, 4615299, 4615304, 4615307, 4615309, 4615316, 4615317, 4615318, 4615319, 4615320, 4615322, 4615323, 4615324, 4615326, 4615328, 4615331, 4615333, 4615334, 4615335, 4615337, 4615338, 4615339, 4615341, 4615342, 4615344, 4615345, 4615346, 4615348, 4615349, 4615352, 4615353, 4615355, 4615356, 4615361, 4615368, 4615369, 4615371, 4615372, 4615376, 4615382, 4615388, 4615392, 4615395, 4615397, 4615401, 4615402, 4615409, 4615410, 4615415, 4615422, 4615425, 4615426, 4615428, 4615430, 4615431, 4615433, 4615438, 4615449, 4615454, 4615456, 4615457, 4615461, 4615462, 4615468, 4615469, 4615470, 4615474, 4615475, 4615476, 4615477, 4615484, 4615485, 4615488, 4615490, 4615492, 4615497, 4615499, 4615504, 4615505, 4615509, 4615510, 4615513, 4615514, 4615515, 4615517, 4615518, 4615528, 4615529, 4615530, 4615532, 4615533, 4615535, 4615536, 4615540, 4615545, 4615549, 4615553, 4615555, 4615558, 4615561, 4615563, 4615569, 4615570, 4615571, 4615572, 4615579, 4615598, 4615599, 4615600, 4615606, 4615608, 4615610, 4615613, 4615616, 4615617, 4615618, 4615620, 4615621, 4615622, 4615625, 4615626, 4615632, 4615634, 4615637, 4615638, 4615639, 4615646, 4615651, 4615652, 4615656, 4615657, 4615658, 4615659, 4615663, 4615665, 4615666, 4615667, 4615673, 4615674, 4615676, 4615677, 4615680, 4615681, 4615682, 4615683, 4615685, 4615689, 4615693, 4615696, 4615710, 4615711, 4615713, 4615714, 4615730, 4615736, 4615741, 4615747, 4615753, 4615759, 4615761, 4615765, 4615772, 4615781, 4615786, 4615789, 4615796, 4615798, 4615799, 4615800, 4615806, 4615807, 4615809, 4615810, 4615811, 4615814, 4615816, 4615819, 4615822, 4615826, 4615827, 4615830, 4615832, 4615834, 4615836, 4615839, 4615847, 4615850, 4615853, 4615854, 4615855, 4615856, 4615857, 4615859, 4615860, 4615861, 4615865, 4615867, 4615873, 4615875, 4615876, 4615877, 4615878, 4615880, 4615885, 4615887, 4615892, 4615894, 4615897, 4615900, 4615902, 4615904, 4615907, 4615912, 4615913, 4615918, 4615922, 4615923, 4615925, 4615926, 4615928, 4615930, 4615932, 4615935, 4615936, 4615937, 4615944, 4615945, 4615948, 4615958, 4615961, 4615964, 4615969, 4615971, 4615972, 4615974, 4615975, 4615983, 4615986, 4615988, 4615993, 4615997, 4615998, 4615999, 4616000, 4616009, 4616010, 4616011, 4616012, 4616013, 4616014, 4616016, 4616018, 4616019, 4616021, 4616025, 4616026, 4616027, 4616028, 4616029, 4616031, 4616032, 4616033, 4616034, 4616036, 4616038, 4616039, 4616041, 4616042, 4616043, 4616044, 4616046, 4616047, 4616048, 4616055, 4616056, 4616057, 4616058, 4616059, 4616062, 4616063, 4616064, 4616066, 4616068, 4616070, 4616071, 4616073, 4616075, 4616076, 4616081, 4616083, 4616087, 4616088, 4616091, 4616092, 4616096, 4616099, 4616100, 4616102, 4616103, 4616105, 4616106, 4616107, 4616111, 4616113, 4616114, 4616119, 4616121, 4616124, 4616125, 4616130, 4616132, 4616137, 4616138, 4616141, 4616144, 4616145, 4616150, 4616151, 4616153, 4616155, 4616158, 4616162, 4616163, 4616166, 4616178, 4616179, 4616183, 4616186, 4616187, 4616190, 4616192, 4616193, 4616195, 4616197, 4616198, 4616199, 4616201, 4616206, 4616207, 4616212, 4616214, 4616220, 4616227, 4616231, 4616233, 4616238, 4616244, 4616245, 4616251, 4616252, 4616262, 4616266, 4616268, 4616269, 4616286, 4616296, 4616298, 4616301, 4616303, 4616306, 4616309, 4616312, 4616317, 4616318, 4616319, 4616322, 4616323, 4616328, 4616329, 4616334, 4616335, 4616336, 4616337, 4616338, 4616340, 4616341, 4616343, 4616345, 4616346, 4616349, 4616351, 4616354, 4616355, 4616359, 4616360, 4616361, 4616363, 4616364, 4616366, 4616367, 4616369, 4616371, 4616372, 4616374, 4616377, 4616379, 4616380, 4616382, 4616383, 4616384, 4616385, 4616388, 4616389, 4616392, 4616393, 4616395, 4616396, 4616397, 4616399, 4616400, 4616402, 4616406, 4616407, 4616409, 4616410, 4616412, 4616415, 4616417, 4616419, 4616425, 4616426, 4616430, 4616441, 4616442, 4616443, 4616446, 4616448, 4616452, 4616455, 4616457, 4616458, 4616460, 4616463, 4616464, 4616465, 4616474, 4616476, 4616477, 4616479, 4616484, 4616485, 4616488, 4616489, 4616490, 4616492, 4616495, 4616497, 4616500, 4616510, 4616512, 4616514, 4616515, 4616518, 4616521, 4616524, 4616525, 4616531, 4616533, 4616534, 4616535, 4616536, 4616537, 4616543, 4616544, 4616546, 4616547, 4616548, 4616553, 4616554, 4616557, 4616560, 4616569, 4616570, 4616571, 4616574, 4616583, 4616588, 4616589, 4616593, 4616594, 4616598, 4616599, 4616600, 4616601, 4616605, 4616608, 4616617, 4616619, 4616620, 4616622, 4616625, 4616629, 4616634, 4616636, 4616644, 4616645, 4616650, 4616651, 4616652, 4616653, 4616654, 4616658, 4616660, 4616668, 4616673, 4616674, 4616676, 4616678, 4616683, 4616685, 4616686, 4616687, 4616688, 4616690, 4616691, 4616695, 4616696, 4616697, 4616699, 4616702, 4616705, 4616706, 4616707, 4616708, 4616709, 4616710, 4616712, 4616713, 4616714, 4616719, 4616720, 4616721, 4616722, 4616729, 4616730, 4616734, 4616738, 4616740, 4616741, 4616742, 4616743, 4616745, 4616747, 4616749, 4616751, 4616752, 4616755, 4616756, 4616761, 4616763, 4616767, 4616773, 4616776, 4616778, 4616784, 4616787, 4616788, 4616793, 4616796, 4616799, 4616804, 4616805, 4616807, 4616810, 4616820, 4616821, 4616823, 4616825, 4616827, 4616828, 4616829, 4616830, 4616831, 4616833, 4616835, 4616837, 4616839, 4616842, 4616844, 4616846, 4616847, 4616853, 4616857, 4616859, 4616865, 4616871, 4616875, 4616879, 4616880, 4616881, 4616888, 4616901, 4616904, 4616905, 4616910, 4616911, 4616913, 4616914, 4616916, 4616920, 4616921, 4616923, 4616924, 4616925, 4616926, 4616927, 4616929, 4616930, 4616932, 4616935, 4616936, 4616938, 4616941, 4616943, 4616946, 4616947, 4616948, 4616950, 4616957, 4616959, 4616960, 4616961, 4616962, 4616963, 4616965, 4616966, 4616974, 4616976, 4616977, 4616978, 4616979, 4616980, 4616981, 4616982, 4616985, 4616989, 4616993, 4616995, 4616998, 4617002, 4617009, 4617015, 4617016, 4617018, 4617019, 4617024, 4617034, 4617037, 4617040, 4617045, 4617052, 4617054, 4617058, 4617064, 4617065, 4617066, 4617067, 4617070, 4617073, 4617076, 4617078, 4617080, 4617081, 4617083, 4617084, 4617085, 4617089, 4617094, 4617098, 4617100, 4617101, 4617102, 4617105, 4617108, 4617111, 4617116, 4617117, 4617118, 4617121, 4617123, 4617124, 4617126, 4617130, 4617134, 4617140, 4617144, 4617148, 4617156, 4617160, 4617163, 4617166, 4617167, 4617168, 4617170, 4617171, 4617172, 4617174, 4617176, 4617180, 4617181, 4617182, 4617184, 4617185, 4617186, 4617188, 4617193, 4617194, 4617199, 4617204, 4617205, 4617206, 4617207, 4617208, 4617210, 4617218, 4617223, 4617224, 4617230, 4617232, 4617233, 4617241, 4617242, 4617245, 4617246, 4617249, 4617250, 4617251, 4617254, 4617255, 4617258, 4617259, 4617262, 4617267, 4617268, 4617269, 4617271, 4617273, 4617274, 4617275, 4617276, 4617277, 4617278, 4617279, 4617282, 4617285, 4617286, 4617287, 4617288, 4617289, 4617292, 4617293, 4617296, 4617297, 4617299, 4617301, 4617302, 4617305, 4617309, 4617310, 4617312, 4617317, 4617318, 4617319, 4617321, 4617325, 4617327, 4617328, 4617330, 4617332, 4617335, 4617336, 4617337, 4617338, 4617342, 4617345, 4617346, 4617349, 4617353, 4617354, 4617355, 4617356, 4617358, 4617359, 4617360, 4617363, 4617365, 4617373, 4617380, 4617383, 4617384, 4617386, 4617387, 4617389, 4617395, 4617398, 4617399, 4617403, 4617406, 4617407, 4617408, 4617410, 4617414, 4617415, 4617419, 4617422, 4617424, 4617432, 4617433, 4617436, 4617438, 4617449, 4617450, 4617455, 4617458, 4617462, 4617466, 4617467, 4617470, 4617471, 4617472, 4617473, 4617475, 4617477, 4617478, 4617479, 4617484, 4617487, 4617489, 4617490, 4617496, 4617499, 4617508, 4617509, 4617510, 4617512, 4617516, 4617522, 4617523, 4617525, 4617529, 4617535, 4617538, 4617541, 4617546, 4617550, 4617551, 4617553, 4617556, 4617562, 4617563, 4617564, 4617565, 4617569, 4617575, 4617578, 4617581, 4617582, 4617584, 4617585, 4617588, 4617589, 4617592, 4617594, 4617596, 4617599, 4617601, 4617603, 4617604, 4617607, 4617608, 4617609, 4617615, 4617617, 4617618, 4617619, 4617624, 4617625, 4617627, 4617628, 4617630, 4617631, 4617633, 4617634, 4617635, 4617640, 4617650, 4617651, 4617659, 4617663, 4617664, 4617666, 4617670, 4617671, 4617676, 4617683, 4617688, 4617692, 4617693, 4617695, 4617696, 4617698, 4617700, 4617701, 4617702, 4617712, 4617719, 4617721, 4617722, 4617724, 4617726, 4617729, 4617733, 4617735, 4617736, 4617738, 4617739, 4617741, 4617746, 4617752, 4617756, 4617760, 4617761, 4617764, 4617767, 4617770, 4617771, 4617773, 4617777, 4617778, 4617780, 4617782, 4617786, 4617787, 4617790, 4617795, 4617800, 4617805, 4617814, 4617815, 4617816, 4617817, 4617818, 4617820, 4617824, 4617827, 4617828, 4617835, 4617838, 4617840, 4617841, 4617842, 4617844, 4617846, 4617850, 4617851, 4617853, 4617855, 4617860, 4617861, 4617864, 4617865, 4617867, 4617868, 4617869, 4617871, 4617875, 4617876, 4617879, 4617881, 4617882, 4617885, 4617886, 4617888, 4617893, 4617898, 4617899, 4617902, 4617903, 4617905, 4617908, 4617909, 4617910, 4617912, 4617913, 4617919, 4617922, 4617932, 4617935, 4617936, 4617937, 4617938, 4617939, 4617940, 4617941, 4617943, 4617948, 4617949, 4617950, 4617955, 4617961, 4617962, 4617963, 4617968, 4617973, 4617976, 4617983, 4617984, 4617993, 4617996, 4617998, 4618001, 4618004, 4618007, 4618012, 4618013, 4618017, 4618019, 4618024, 4618027, 4618032, 4618035, 4618038, 4618042, 4618043, 4618044, 4618049, 4618051, 4618054, 4618055, 4618057, 4618058, 4618059, 4618060, 4618061, 4618064, 4618066, 4618067, 4618068, 4618069, 4618070, 4618071, 4618074, 4618079, 4618081, 4618083, 4618084, 4618085, 4618087, 4618090, 4618092, 4618093, 4618094, 4618097, 4618101, 4618105, 4618110, 4618113, 4618115, 4618119, 4618121, 4618123, 4618125, 4618128, 4618132, 4618135, 4618136, 4618139, 4618140, 4618142, 4618144, 4618145, 4618150, 4618151, 4618153, 4618157, 4618159, 4618161, 4618164, 4618166, 4618167, 4618169, 4618174, 4618176, 4618178, 4618182, 4618183, 4618186, 4618189, 4618191, 4618195, 4618197, 4618199, 4618203, 4618204, 4618205, 4618208, 4618209, 4618211, 4618212, 4618215, 4618220, 4618221, 4618222, 4618223, 4618225, 4618229, 4618230, 4618232, 4618233, 4618235, 4618236, 4618239, 4618242, 4618245, 4618249, 4618250, 4618251, 4618252, 4618254, 4631453, 4631463, 4631470, 4631479, 4631482, 4631516, 4631524, 4631565, 4636556, 4644914, 4644917, 4644918, 4644919, 4651864, 4651866, 4651879, 4652141, 4652144, 4652149, 4652153, 4652154, 4652155, 4652156, 4652158, 4664272, 4664273, 4664274, 4664275, 4664276, 4664277, 4664278, 4664279, 4664280, 4664281, 4664282, 4664293, 4664296, 4664300, 4664304, 4668798, 4668799, 4668800, 4668801, 4668802, 4668804, 4668806, 4668807, 4668809, 4668810, 4668811, 4668812, 4668813, 4668814, 4668815, 4668817, 4668818, 4668819, 4668823, 4668824, 4668826, 4668829, 4668830, 4668831, 4668837, 4670016, 4670019, 4670021, 4670023, 4670024, 4670025, 4670026, 4670029, 4670031, 4670036, 4670039, 4670041, 4670042, 4670043, 4670044, 4670046, 4670047, 4670051, 4670052, 4670053, 4711044, 4711047, 4711050, 4834646, 4870142, 4870153, 4870219, 4870221, 4870229, 4870231, 4870289, 4870291, 4870299, 4870300, 4870315, 4870322, 4870328, 4870369, 4870415, 4870423, 4873775, 4873777, 4873778],
                    1445,
                    [
                        [
                            'external_id' => '4614721',
                            'firstname' => 'Christopher',
                            'lastname' => 'Bullock',
                            'primary_email' => 'MapworksBetaUser04614721@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '1',
                            'completed_degree_in_3_years_or_less' => '1',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614728',
                            'firstname' => 'Ryan',
                            'lastname' => 'Kerr',
                            'primary_email' => 'MapworksBetaUser04614728@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614729',
                            'firstname' => 'Nathan',
                            'lastname' => 'Stout',
                            'primary_email' => 'MapworksBetaUser04614729@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '1',
                            'completed_degree_in_3_years_or_less' => '1',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614735',
                            'firstname' => 'Jonathan',
                            'lastname' => 'Pruitt',
                            'primary_email' => 'MapworksBetaUser04614735@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                        [
                            'external_id' => '4614736',
                            'firstname' => 'Levi',
                            'lastname' => 'Buck',
                            'primary_email' => 'MapworksBetaUser04614736@mailinator.com',
                            'retention_tracking_year' => '201516',
                            'retention_tracking_year_name' => '2015-16 Academic Year',
                            'retained_to_midyear_year_1' => '1',
                            'retained_to_start_of_year_2' => '1',
                            'retained_to_midyear_year_2' => '1',
                            'retained_to_start_of_year_3' => '1',
                            'retained_to_midyear_year_3' => '1',
                            'retained_to_start_of_year_4' => NULL,
                            'retained_to_midyear_year_4' => NULL,
                            'completed_degree_in_1_year_or_less' => '0',
                            'completed_degree_in_2_years_or_less' => '0',
                            'completed_degree_in_3_years_or_less' => '0',
                            'completed_degree_in_4_years_or_less' => NULL,
                            'completed_degree_in_5_years_or_less' => NULL,
                            'completed_degree_in_6_years_or_less' => NULL,
                        ],
                    ]
                ],
                // example with invalid student no retention variables data exists
                [
                    59,
                    201516,
                    [4457569],
                    0,
                    []
                ],
                // example with invalid year no retention variables data exists
                [
                    59,
                    198081,
                    [],
                    0,
                    []
                ],
                // example with invalid organization no retention variables data exists
                [
                    200,
                    201516,
                    [],
                    0,
                    []
                ]
            ]
        ]);
    }

    public function testGetRetentionTrackingOrgAcademicYearIdsForOrganization()
    {
        $this->specify("Verify the functionality of the method getRetentionTrackingOrgAcademicYearIdsForOrganization", function ($organizationId, $yearLimit, $expectedResults) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingOrgAcademicYearIdsForOrganization($organizationId, $yearLimit);
            verify($result)->equals($expectedResults);

        }, ["examples" =>
            [
                //  Would return empty array , as there are no retention tracking year for organization  before 201415 
                [59, 201314, []],
                //  Would return 94 (retention tracking year in org academic year id)
                [59, 201516, [94, 204]],
                //  Would return 194, 94 (retention tracking year in org academic year id)
                [59, 201617, [194, 94, 204]],
                // Would return 184, 194, 94, (retention tracking year in org academic year id)
                [59, 201718, [184, 194, 94, 204]],
            ]
        ]);
    }


    public function testGetRetentionTrackingGroupsForOrganization()
    {
        $this->specify("Verify the functionality of the method getRetentionTrackingGroupsForOrganization", function ($organizationId, $expectedResult) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupsForOrganization($organizationId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                [59,
                    [
                        [
                            'year_id' => 201415,
                            'year_name' => '2014-15 Academic Year'
                        ],
                        [
                            'year_id' => 201516,
                            'year_name' => '2015-16 Academic Year'
                        ],
                        [
                            'year_id' => 201617,
                            'year_name' => '2016-17 Academic Year'
                        ],
                        [
                            'year_id' => 201718,
                            'year_name' => '2017-18 Academic Year'
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testGetRetentionAndCompletionVariables()
    {
        $this->specify("Verify the functionality of the method getRetentionAndCompletionVariables", function ($organizationId, $retentionTrackingGroup, $expectedResult) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionAndCompletionVariables($organizationId, $retentionTrackingGroup);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                [59, "201516",
                    [
                        [
                            'year_id' => 201516,
                            'year_name' => '2015-16 Academic Year',
                            'retention_completion_name_text' => 'Retained to Midyear Year 1'
                        ],
                        [
                            'year_id' => 201516,
                            'year_name' => '2015-16 Academic Year',
                            'retention_completion_name_text' => 'Completed Degree in 1 Year or Less'
                        ],
                        [
                            'year_id' => 201617,
                            'year_name' => '2016-17 Academic Year',
                            'retention_completion_name_text' => 'Retained to Start of Year 2'
                        ],
                        [
                            'year_id' => 201617,
                            'year_name' => '2016-17 Academic Year',
                            'retention_completion_name_text' => 'Retained to Midyear Year 2'
                        ],
                        [
                            'year_id' => 201617,
                            'year_name' => '2016-17 Academic Year',
                            'retention_completion_name_text' => 'Completed Degree in 2 Years or Less'
                        ],

                        [
                            'year_id' => 201718,
                            'year_name' => '2017-18 Academic Year',
                            'retention_completion_name_text' => 'Retained to Start of Year 3'
                        ],
                        [
                            'year_id' => 201718,
                            'year_name' => '2017-18 Academic Year',
                            'retention_completion_name_text' => 'Retained to Midyear Year 3'
                        ],
                        [
                            'year_id' => 201718,
                            'year_name' => '2017-18 Academic Year',
                            'retention_completion_name_text' => 'Completed Degree in 3 Years or Less'
                        ]
                    ]

                ]
            ]
        ]);

    }


    public function testAreStudentsAssignedToThisRetentionTrackingYear()
    {
        $this->specify("Verify the functionality of the method getRetentionAndCompletionVariables", function ($organizationId, $orgAcademicYearId, $expectedResult) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->areStudentsAssignedToThisRetentionTrackingYear($organizationId, $orgAcademicYearId);
            verify($result)->equals($expectedResult);
        }, ["examples" =>
            [
                // students present for the retention tracking year 94, returns true
                [59, 94, true],
                // students present for the retention tracking year 194, returns true
                [59, 194, true],
                // students not present for the retention tracking year 194, returns false
                [20, 34, false],
            ]
        ]);
    }

    public function testGetRetentionTrackingGroupStudents()
    {

        $this->specify("Verify the functionality of the method getRetentionTrackingGroupStudents", function ($organizationId, $retentionTrackingGroup, $expectedCount, $expectedResultSubset) {
            $result = $this->orgPersonStudentRetentionTrackingGroupRepository->getRetentionTrackingGroupStudents($organizationId, $retentionTrackingGroup);
            verify(count($result))->equals($expectedCount);
            foreach ($expectedResultSubset as $person) {
                $IsExists = in_array($person, $result);
                verify($IsExists)->true();
            }
        }, ["examples" =>
            [
                [59, 194, 3, [4615031, 4615485, 4615528]],
                [59, 94, 1440, [
                    4670052,
                    4670053,
                    4711044,
                    4711047,
                    4711050,
                    4834646,
                    4870142,
                    4870153,
                    4870219,
                    4870221,
                    4870229,
                    4870231,
                    4870289,
                    4870291,
                    4870299,
                    4870300,
                    4870315,
                    4870322,
                    4870328,
                    4870369,
                    4870415,
                    4870423,
                    4873775,
                    4873777,
                    4873778]]
            ]]);
    }

}
