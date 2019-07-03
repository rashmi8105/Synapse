<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-11607 Moving all soft deleted records from person_ebi_metadata
 * to temporary storage and deleting from person_ebi_metadata
 *
 * Also, updating incorrect risk values for students impacted by previously soft deleted profile metadata. 
 *
 * Not bothering with person_org_metadata as no students match criteria
 * needed to worry about recalculating risk
 */
class Version20160822140604 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER EVENT Survey_Risk_Event DISABLE;');
        
        $this->addSql('DROP TABLE IF EXISTS `person_ebi_metadata_soft_deletes`');
        $this->addSql('CREATE TABLE `person_ebi_metadata_soft_deletes` LIKE `person_ebi_metadata`;');
        $this->addSql('INSERT INTO `person_ebi_metadata_soft_deletes`
                        select * from `person_ebi_metadata` WHERE deleted_at IS NOT NULL;');
        $this->addSql('DELETE FROM `person_ebi_metadata` WHERE deleted_at IS NOT NULL;');
		$this->addSql("UPDATE org_calc_flags_risk ocfr 
						SET calculated_at = null, modified_at = NOW()
						WHERE person_id  IN (
						404715,895683,925129,1195534,1207986,1210113,4614904,4614905,4614942,4614945,4614954,4614955,4614967,4614975,4614976,4614988,4615010,4615040,4615055,4615058,4615074,4615086,4615112,4615120,4615123,4615128,4615135,4615139,4615140,4615147,4615148,4615160,4615182,4615193,4615206,4615209,4615255,4615262,4615277,4615284,4615298,4615319,4615328,4615331,4615342,4615344,4615355,4615369,4615371,4615426,4615430,4615509,4615518,4615528,4615570,4615572,4615579,4615610,4615616,4615621,4615625,4615632,4615637,4615652,4615665,4615666,4615677,4615680,4615682,4615685,4615730,4615736,4615741,4615747,4615765,4615772,4615798,4615850,4615855,4615860,4615913,4615936,4615997,4616041,4616042,4616047,4616056,4616073,4616075,4616081,4616107,4616114,4616119,4616145,4616158,4616178,4616179,4616192,4616207,4616233,4616245,4616346,4616372,4616377,4616384,4616388,4616393,4616395,4616396,4616397,4616409,4616415,4616476,4616485,4616489,4616512,4616515,4616531,4616554,4616557,4616570,4616589,4616593,4616605,4616652,4616668,4616696,4616699,4616713,4616729,4616738,4616743,4616751,4616773,4616776,4616799,4616830,4616847,4616924,4616926,4616927,4616948,4616959,4616961,4616962,4616980,4616982,4617040,4617064,4617085,4617098,4617100,4617101,4617144,4617148,4617163,4617166,4617170,4617171,4617181,4617184,4617199,4617262,4617268,4617275,4617285,4617288,4617292,4617297,4617305,4617318,4617328,4617330,4617336,4617338,4617424,4617455,4617466,4617473,4617477,4617484,4617489,4617541,4617550,4617551,4617601,4617619,4617627,4617628,4617719,4617722,4617724,4617735,4617739,4617760,4617787,4617805,4617816,4617818,4617835,4617851,4617853,4617855,4617871,4617876,4617888,4617903,4617908,4617912,4617932,4617940,4617941,4618007,4618032,4618035,4618051,4618059,4618087,4618110,4618183,4618215,4618221,4618225,4618251,4631482,4639299,4639300,4639303,4639305,4639319,4639324,4639341,4639342,4639350,4639351,4639371,4639372,4639383,4639388,4639394,4639397,4639402,4639411,4639418,4639434,4639446,4639461,4639480,4639484,4639487,4639493,4639502,4639508,4639517,4639531,4639535,4639542,4639553,4639563,4639567,4639578,4639585,4639591,4639595,4639596,4639603,4639606,4639622,4639629,4639634,4639637,4639641,4639646,4639649,4639653,4639662,4639663,4639676,4639677,4639679,4639696,4639707,4639712,4639736,4639742,4639755,4639770,4639774,4639776,4639777,4639778,4639783,4639800,4639802,4639826,4639830,4639837,4639842,4639844,4639845,4639855,4639860,4639873,4639875,4639880,4639889,4639900,4639904,4639911,4639917,4639922,4639943,4639962,4639965,4639973,4639981,4639982,4639985,4639986,4640004,4640008,4640011,4640015,4640025,4640026,4640039,4640042,4640046,4640047,4640051,4640061,4640063,4640068,4640076,4640081,4640088,4640094,4640101,4640104,4640108,4640124,4640126,4640128,4640129,4640143,4640144,4640150,4640155,4640157,4640161,4640162,4640176,4640181,4640187,4640195,4640196,4640197,4640207,4640209,4640215,4640219,4640248,4640252,4640259,4640263,4640268,4640276,4640279,4640281,4640292,4640295,4640301,4640303,4640304,4640305,4640313,4640317,4640337,4640346,4640360,4640363,4640368,4640369,4640376,4640377,4640385,4640392,4640395,4640402,4640419,4640421,4640424,4640425,4640427,4640443,4640450,4640451,4640469,4640484,4640487,4640493,4640499,4640504,4640515,4640516,4640523,4640531,4640532,4640550,4640554,4640557,4640562,4640575,4640578,4640590,4640601,4640614,4640632,4640634,4640636,4640643,4640685,4640692,4640693,4640696,4640697,4640702,4640714,4640715,4640718,4640720,4640721,4640723,4640726,4640735,4640737,4640738,4640745,4640757,4640761,4640767,4640782,4640789,4640791,4640801,4640802,4640803,4640807,4640811,4640814,4640855,4640856,4640866,4640869,4640872,4640877,4640884,4640903,4640904,4640913,4640926,4640928,4640937,4640942,4640943,4640953,4640962,4640973,4640979,4640991,4641004,4641012,4641024,4641028,4641033,4641037,4641051,4641056,4641059,4641083,4641109,4641117,4641136,4641153,4641154,4641157,4641166,4641168,4641169,4641186,4641194,4641196,4641222,4641223,4641229,4641231,4641238,4641242,4641243,4641246,4641254,4641258,4641261,4641262,4641266,4641270,4641277,4641278,4641283,4641296,4641304,4641309,4641312,4641322,4641336,4641338,4641345,4641350,4641360,4641369,4641370,4641371,4641374,4641375,4641377,4641383,4641388,4641389,4641395,4641397,4641404,4641406,4641415,4641431,4641441,4641450,4641451,4641465,4641467,4641478,4641483,4641494,4641495,4641500,4641504,4641511,4641514,4641535,4641539,4641541,4641549,4641561,4641574,4641575,4641592,4641610,4641614,4641617,4641618,4641619,4641628,4641638,4641640,4641641,4641642,4641645,4641646,4641650,4641651,4641669,4641677,4641689,4641699,4641707,4641709,4641728,4641737,4641740,4641742,4641754,4641775,4641784,4641785,4641787,4641789,4641796,4641800,4641803,4641821,4641827,4641831,4641842,4641843,4641845,4641850,4641857,4641861,4641864,4641870,4641875,4641884,4641896,4641897,4641902,4641907,4641908,4641914,4641935,4641937,4641942,4641964,4641965,4641969,4641985,4641989,4641994,4641998,4642003,4642005,4642006,4642010,4642018,4642020,4642021,4642026,4642030,4642036,4642039,4642050,4642054,4642055,4642067,4642082,4642090,4642093,4642104,4642107,4642120,4642121,4642143,4642146,4642176,4642186,4642188,4642194,4642206,4642211,4642215,4642226,4642235,4642243,4642263,4642269,4642274,4642275,4642277,4642278,4642296,4642312,4642314,4642319,4642320,4642342,4642344,4642348,4642350,4642351,4642353,4642364,4642369,4642385,4642395,4642403,4642445,4642451,4642460,4642463,4642467,4642484,4642487,4642489,4642495,4642500,4642509,4642510,4642516,4642536,4642542,4642543,4642551,4642564,4642566,4642575,4642589,4642590,4642591,4642595,4642596,4642604,4642607,4642616,4642618,4642619,4642625,4642628,4642629,4642646,4642659,4642662,4642664,4642679,4642686,4642687,4642691,4642701,4642712,4642713,4642716,4642720,4642728,4642737,4642744,4642746,4642754,4642770,4642777,4642795,4642821,4642828,4642833,4642838,4642866,4642890,4642895,4642901,4642902,4642906,4642913,4642916,4642918,4642920,4642921,4642925,4642955,4642977,4642984,4643000,4643007,4643016,4643028,4643038,4643043,4643048,4643055,4643069,4643076,4643084,4643111,4643112,4643115,4643117,4643119,4643120,4643128,4643140,4643141,4643155,4643164,4643167,4643174,4643194,4643198,4643200,4643206,4643210,4643213,4643214,4643215,4643225,4643227,4643235,4643250,4643265,4643267,4643278,4643282,4643286,4643315,4643322,4643325,4643328,4643342,4643351,4643355,4643378,4643381,4643388,4643393,4643395,4643410,4643413,4643426,4643427,4643430,4643432,4643446,4643448,4643449,4643453,4643455,4643458,4643461,4643465,4643481,4643491,4643503,4643504,4643505,4643507,4643513,4643523,4643541,4643548,4643551,4643561,4643569,4643585,4643589,4643590,4643592,4643593,4643597,4643608,4643610,4643611,4643617,4643633,4643640,4643650,4643660,4643673,4643678,4643679,4643683,4643689,4643696,4643719,4643723,4643727,4643729,4643730,4643755,4643756,4643778,4643792,4643808,4643812,4643818,4643831,4643833,4643847,4643850,4643860,4643863,4643879,4643888,4643894,4643902,4643907,4643911,4643912,4643914,4643921,4643922,4643928,4643931,4643945,4643955,4643962,4643972,4643974,4643976,4643985,4643987,4643989,4643990,4644003,4644004,4644006,4644013,4644019,4644022,4644033,4644038,4644042,4644049,4644052,4644063,4644066,4644094,4644122,4644127,4644132,4644134,4644136,4644144,4644146,4644149,4644151,4644153,4644155,4644169,4644181,4644183,4644185,4644186,4644189,4644196,4644206,4644209,4644213,4644228,4644231,4644239,4644248,4644251,4644263,4644286,4644307,4644311,4644314,4644320,4644321,4644324,4644333,4644338,4644360,4644361,4644369,4644378,4644382,4644389,4644392,4644398,4644401,4644423,4644424,4644426,4644431,4644435,4644438,4644449,4644450,4644454,4644459,4644480,4644482,4644491,4644506,4644508,4644510,4644516,4644533,4644539,4644543,4644552,4644572,4644574,4644579,4644582,4644583,4644584,4644589,4644597,4644598,4644604,4644619,4644622,4644625,4644626,4644642,4644652,4644658,4644665,4644671,4644684,4644688,4644706,4644718,4644720,4644723,4644733,4644741,4644764,4644765,4644771,4644780,4644789,4644804,4644808,4644811,4644816,4644820,4644822,4644833,4644853,4644858,4644860,4644870,4644874,4644880,4644914,4644919,4651864,4652144,4652153,4652156,4664275,4664276,4664279,4664293,4664300,4667174,4668802,4668806,4668818,4668819,4668831,4670041,4670042,4670043,4670052,4671322,4671364,4671370,4671395,4671401,4671422,4701773,4701775,4701777,4701806,4701807,4701810,4701818,4703289,4703290,4709901,4728590,4729592,4729628,4729631,4729655,4729678,4729685,4729686,4729691,4729694,4729695,4729696,4729697,4729698,4729699,4729703,4729706,4729708,4729713,4729716,4729719,4729720,4729721,4729724,4729726,4729728,4729729,4729731,4729738,4729741,4729742,4729744,4729748,4729757,4729760,4729761,4729764,4729766,4729767,4729768,4729769,4729773,4729775,4729783,4729792,4729796,4729797,4729806,4729808,4729810,4729811,4729814,4729815,4729817,4729818,4729821,4729822,4729830,4729831,4729834,4729835,4729837,4729839,4729840,4729841,4729842,4729843,4729845,4729846,4729847,4729849,4729850,4729855,4729856,4729857,4729860,4729861,4729863,4729865,4729868,4729870,4729877,4729878,4729879,4729880,4729882,4729884,4729885,4729886,4729887,4729888,4729889,4729890,4729891,4729892,4729895,4729898,4729899,4729900,4729905,4729908,4729909,4729910,4729911,4729912,4729914,4729916,4729919,4729920,4729922,4729923,4729924,4729925,4729926,4729927,4729928,4729931,4729932,4729934,4729935,4729937,4729938,4729939,4729940,4729941,4729943,4729946,4729948,4729949,4729950,4729951,4729952,4729953,4729955,4729956,4729957,4729958,4729959,4729960,4729962,4729963,4729964,4729965,4729967,4729968,4729969,4729970,4729972,4729975,4729977,4729980,4729982,4729983,4729984,4729985,4729987,4729988,4729990,4729991,4729996,4729997,4730000,4730002,4730003,4730006,4730008,4730010,4730011,4730012,4730014,4730017,4730019,4730021,4730023,4730024,4730025,4730027,4730028,4730030,4730032,4730035,4730036,4730039,4730040,4730043,4730045,4730046,4730049,4730053,4730054,4730055,4730056,4730057,4730058,4730059,4730060,4730061,4730063,4730064,4730066,4730069,4730071,4730074,4730078,4730079,4730080,4730082,4730083,4730085,4730086,4730092,4730095,4730096,4730097,4730098,4730101,4730105,4730106,4730108,4730109,4730110,4730111,4730112,4730114,4730115,4730116,4730117,4730119,4730120,4730121,4730122,4730124,4730125,4730127,4730128,4730129,4730130,4730131,4730133,4730135,4730138,4730139,4730140,4730142,4730144,4730146,4730148,4730149,4730151,4730152,4730153,4730154,4730155,4730156,4730157,4730159,4730160,4730162,4730163,4730164,4730165,4730166,4730167,4730171,4730174,4730175,4730177,4730178,4730180,4730181,4730182,4730184,4730185,4730186,4730187,4730188,4730189,4730190,4730191,4730192,4730193,4730194,4730195,4730196,4730197,4730199,4730201,4730204,4730205,4730207,4730208,4730209,4730212,4730215,4730216,4730217,4730218,4730221,4730223,4730224,4730226,4730227,4730230,4730231,4730232,4730233,4730236,4730237,4730239,4730241,4730242,4730243,4730245,4730246,4730247,4730248,4730251,4730253,4730255,4730256,4730259,4730261,4730262,4730263,4730265,4730268,4730271,4730273,4730274,4730275,4730277,4730278,4730279,4730280,4730281,4730282,4730283,4730284,4730285,4730286,4730287,4730288,4730289,4730290,4730292,4730293,4730294,4730295,4730296,4730298,4730299,4730301,4730302,4730303,4730305,4730306,4730307,4730310,4730311,4730312,4730314,4730315,4730316,4730318,4730319,4730321,4730322,4730323,4730324,4730325,4730326,4730327,4730329,4730330,4730333,4730334,4730335,4730336,4730338,4730339,4730344,4730345,4730346,4730347,4730350,4730351,4730353,4730355,4730359,4730361,4730364,4730367,4730368,4730371,4730372,4730373,4730375,4730376,4730377,4730378,4730379,4730380,4730382,4730384,4730388,4730389,4730391,4730393,4730394,4730397,4730398,4730402,4730405,4730406,4730407,4730408,4730409,4730410,4730413,4730415,4730416,4730417,4730418,4730419,4730421,4730424,4730425,4730426,4730430,4730434,4730437,4730438,4730439,4730440,4730442,4730444,4730447,4730448,4730449,4730450,4730454,4730455,4730456,4730457,4730458,4730459,4730460,4730461,4730465,4730467,4730468,4730469,4730470,4730471,4730472,4730473,4730475,4730476,4730477,4730478,4730479,4730480,4730481,4730484,4730485,4730486,4730487,4730489,4730490,4730492,4730493,4730494,4730495,4730496,4730497,4730498,4730500,4730502,4730503,4730504,4730508,4730509,4730510,4730514,4730515,4730516,4730518,4730519,4730521,4730523,4730525,4730526,4730527,4730528,4730529,4730534,4730535,4730536,4730539,4730540,4730541,4730542,4730543,4730547,4730549,4730551,4730552,4730553,4730555,4730556,4730558,4730563,4730564,4730565,4730568,4730569,4730570,4730572,4730578,4730580,4730582,4730584,4730585,4730586,4730587,4730590,4730591,4730592,4730593,4730594,4730595,4730596,4730597,4730598,4730599,4730601,4730604,4730605,4730606,4730607,4730610,4730612,4730615,4730617,4730618,4730620,4730621,4730622,4730623,4730625,4730626,4730627,4730628,4730630,4730631,4730632,4730633,4730634,4730642,4730643,4730645,4730646,4730648,4730649,4730651,4730653,4730654,4730657,4730659,4730661,4730663,4730664,4730665,4730666,4730669,4730670,4730672,4730674,4730675,4730676,4730682,4730683,4730684,4730685,4730687,4730690,4730693,4730694,4730697,4730698,4730699,4730702,4730703,4730704,4730705,4730706,4730707,4730710,4730712,4730714,4730716,4730717,4730718,4730720,4730722,4730723,4730725,4730728,4730729,4730730,4730731,4730732,4730733,4730735,4730736,4730738,4730739,4730742,4730743,4730745,4730747,4730748,4730750,4730751,4730752,4730753,4730756,4730758,4730759,4730760,4730762,4730763,4730764,4730765,4730766,4730768,4730769,4730770,4730772,4730773,4730775,4730776,4730781,4730784,4730785,4730786,4730787,4730789,4730790,4730794,4730796,4730799,4730800,4730801,4730802,4730803,4730808,4730809,4730811,4730813,4730814,4730822,4730823,4730824,4730826,4730829,4730830,4730831,4730834,4730836,4730841,4730843,4730847,4730848,4730849,4730853,4730855,4730856,4730859,4730862,4730863,4730868,4730870,4730872,4730873,4730876,4730877,4730880,4730881,4730883,4730884,4730891,4730892,4730895,4730897,4730901,4730902,4730908,4730913,4730922,4730928,4730930,4730931,4730932,4730935,4730936,4730938,4730942,4730944,4730946,4730949,4730951,4730952,4730954,4730955,4730956,4730957,4730960,4730961,4730963,4730964,4730974,4730979,4730980,4730983,4730985,4730986,4730994,4730997,4730998,4731000,4731001,4731004,4731008,4731010,4731011,4731012,4731014,4731017,4731018,4731019,4731020,4731022,4731024,4731028,4731029,4731030,4731035,4731037,4731038,4731039,4731040,4731042,4731043,4731044,4731046,4731055,4731059,4731060,4731063,4731064,4731069,4731070,4731072,4731075,4731082,4731086,4731089,4731091,4731092,4731094,4731096,4731100,4731101,4731102,4731103,4731105,4731107,4731109,4731111,4731112,4731113,4731114,4731115,4731116,4731117,4731119,4731122,4731124,4731128,4731133,4731136,4731139,4731141,4731143,4731148,4731149,4731152,4731153,4731154,4731156,4731159,4731160,4731162,4731163,4731165,4731167,4731169,4731171,4731172,4731174,4731175,4731176,4731179,4731180,4731181,4731187,4731188,4731189,4731190,4731197,4731198,4731200,4731201,4731202,4731203,4731204,4731205,4731206,4731207,4731209,4731212,4731213,4731215,4731216,4731217,4731218,4731219,4731220,4731221,4731222,4731223,4731224,4731225,4731226,4731227,4731228,4731230,4731231,4731232,4731233,4731234,4731237,4731238,4731239,4731240,4731241,4731243,4731245,4731247,4731248,4731249,4731252,4731254,4731256,4731258,4731259,4731260,4731261,4731262,4731263,4731266,4731267,4731269,4731270,4731272,4731273,4731275,4731276,4731277,4731278,4731279,4731282,4731283,4731284,4731286,4731287,4731289,4731291,4731293,4731295,4731296,4731297,4731298,4731300,4731301,4731303,4731304,4731305,4731306,4731307,4731308,4731313,4731314,4731315,4731316,4731319,4731321,4731323,4731324,4731325,4731328,4731330,4731331,4731332,4731336,4731339,4731340,4731341,4731342,4731343,4731344,4731345,4731346,4731347,4731353,4731354,4731357,4731358,4731359,4731360,4731362,4731363,4731364,4731366,4731367,4731369,4731370,4731371,4731372,4731373,4731374,4731376,4731378,4731379,4731381,4731384,4731387,4731389,4731392,4731394,4731395,4731400,4731401,4731403,4731405,4731406,4731408,4731413,4731414,4731415,4731416,4731417,4731420,4731421,4731422,4731423,4731425,4731428,4731429,4731430,4731432,4731433,4731436,4731437,4731439,4731440,4731443,4731445,4731446,4731447,4731448,4731450,4731451,4731452,4731454,4731456,4731457,4731459,4731460,4731462,4731466,4731468,4731469,4731471,4731474,4731475,4731476,4731483,4731484,4731485,4731486,4731488,4731489,4731490,4731493,4731494,4731496,4731497,4731502,4731503,4731504,4731505,4731506,4731512,4731515,4731517,4731518,4731520,4731521,4731523,4731524,4731525,4731528,4731530,4731531,4731534,4731537,4731538,4731539,4731541,4731542,4731543,4731545,4731546,4731549,4731550,4731551,4731553,4731554,4731555,4731556,4731557,4731558,4731560,4731562,4731563,4731565,4731566,4731569,4731571,4731576,4731578,4731579,4731582,4731583,4731585,4731586,4731589,4731591,4731592,4731593,4731595,4731596,4731597,4731598,4731599,4731601,4731602,4731606,4731607,4731609,4731610,4731611,4731612,4731614,4731617,4731619,4731620,4731621,4731622,4731624,4731625,4731633,4731634,4731635,4731637,4731638,4731639,4731644,4731645,4731646,4731648,4731650,4731652,4731656,4731664,4731665,4731671,4731674,4731678,4731680,4731681,4731685,4731686,4731690,4731691,4731693,4731696,4731698,4731699,4731703,4731709,4731724,4731758,4731792,4731797,4731798,4731799,4731801,4731802,4731803,4731804,4731806,4731809,4731810,4731812,4731817,4731820,4731822,4731823,4731824,4731829,4731833,4731835,4731837,4731839,4731840,4731844,4731845,4731851,4731852,4731870,4731879,4731880,4731883,4731884,4731885,4731886,4731887,4731889,4731891,4731892,4731895,4731897,4731900,4731902,4731903,4731904,4731905,4731906,4731907,4731908,4731909,4731910,4731911,4731912,4731913,4731915,4731916,4731918,4731919,4731920,4731922,4731923,4731924,4731926,4731927,4731928,4731929,4731930,4731931,4731932,4731933,4731934,4731935,4731936,4731937,4731938,4731939,4731942,4731943,4731946,4731947,4731948,4731949,4731950,4731955,4731956,4731958,4731959,4731962,4731963,4731964,4731965,4731966,4731967,4731968,4731971,4731972,4731973,4731974,4731976,4731978,4731979,4731980,4731981,4731982,4731984,4731988,4731989,4731990,4731991,4731992,4731993,4731995,4731996,4731998,4731999,4732000,4732001,4732002,4732003,4732006,4732007,4732009,4732010,4732012,4732013,4732014,4732015,4732016,4732017,4732018,4732019,4732020,4732021,4732022,4732025,4732026,4732027,4732028,4732029,4732030,4732032,4732033,4732034,4732035,4732037,4732038,4732039,4732040,4732041,4732042,4732043,4732044,4732045,4732047,4732048,4732051,4732052,4732053,4732054,4732055,4732058,4732059,4732060,4732061,4732065,4732070,4732071,4732072,4732073,4732074,4732075,4732076,4732078,4732079,4732081,4732082,4732083,4732087,4732088,4732090,4732091,4732092,4732093,4732096,4732097,4732098,4732099,4732100,4732101,4732103,4732104,4732105,4732106,4732110,4732111,4732113,4732115,4732116,4732118,4732119,4732120,4732121,4732122,4732123,4732124,4732125,4732126,4732127,4732129,4732130,4732131,4732132,4732133,4732139,4732142,4732143,4732144,4732145,4732147,4732148,4732149,4732150,4732151,4732153,4732154,4732157,4732158,4732161,4732162,4732163,4732164,4732165,4732166,4732169,4732170,4732172,4732174,4732177,4732180,4732181,4732183,4732184,4732185,4732187,4732188,4732189,4732190,4732191,4732192,4732193,4732194,4732195,4732198,4732199,4732201,4732202,4732203,4732204,4732205,4732207,4732208,4732209,4732210,4732211,4732212,4732213,4732215,4732217,4732221,4732223,4732225,4732226,4732228,4732230,4732232,4732233,4732234,4732240,4732243,4732245,4732247,4732251,4732252,4732255,4732257,4732259,4732260,4732261,4732263,4732264,4732266,4732267,4732268,4732270,4732271,4732272,4732273,4732278,4732279,4732281,4732282,4732287,4732288,4732291,4732292,4732294,4732295,4732296,4732297,4732298,4732299,4732301,4732302,4732317,4732318,4732336,4732342,4732359,4732374,4732379,4732412,4732414,4732465,4732477,4732500,4732521,4732542,4732580,4732583,4732596,4732603,4732605,4732652,4732710,4732749,4732824,4732831,4732847,4732878,4732892,4732931,4732962,4733072,4733103,4733165,4733234,4733270,4733271,4733504,4733658,4733892,4734024,4734101,4734247,4734357,4734375,4734484,4734567,4734573,4734615,4734705,4734836,4734837,4734858,4734862,4735011,4735086,4735339,4735700,4735944,4736305,4736522,4737602,4737607,4770753,4770760,4770766,4770778,4770824,4770829,4770836,4770853,4770863,4808436,4808465,4856635,4867334,4870219,4870229,4870231,4870369,4870423,4873775,4873778,4902645,4902646,4902647,4902648,4902650,4902651,4902656,4902663,4902665,4902678,4902683,4902685,4902686,4902688,4902689,4902690,4902692,4902695,4902696,4902697,4902699,4902700,4902701,4902704,4902706,4902708,4902710,4902711,4902713,4902716,4902719,4902721,4902728,4902729,4902730,4902731,4902734,4902735,4902736,4902738,4902740,4902741,4902745,4902747,4902749,4902752,4902755,4902756,4902758,4902759,4902760,4902764,4902765,4902766,4902767,4902769,4902772,4902773,4902776,4902777,4902778,4902780,4902781,4902782,4902783,4902784,4902788,4902789,4902790,4902792,4902793,4902794,4902795,4902796,4902799,4902800,4902802,4902805,4902806,4902807,4902808,4902809,4902810,4902811,4902812,4902813,4902814,4902816,4902817,4902818,4902820,4902821,4902822,4902823,4902824,4902827,4902829,4902830,4902831,4902832,4902833,4902834,4902835,4902837,4902838,4902839,4902844,4902845,4902848,4902849,4902856,4902857,4902860,4902861,4902862,4902863,4902864,4902865,4902867,4902868,4902870,4902871,4902873,4902876,4902877,4902878,4902879,4902881,4902882,4902883,4902884,4902885,4902886,4902888,4902892,4902893,4902894,4902896,4902897,4902898,4902900,4902901,4902902,4902903,4902904,4902906,4902908,4902909,4902911,4902912,4902913,4902915,4902916,4902917,4902918,4902919,4902922,4902925,4902926,4902931,4902932,4902933,4902935,4902937,4902938,4902940,4902941,4902942,4902943,4902944,4902947,4902949,4902950,4902952,4902953,4902954,4902956,4902957,4902958,4902959,4902960,4902961,4902962,4902963,4902964,4902966,4902969,4902970,4902971,4902972,4902974,4902978,4902979,4902980,4902981,4902982,4902983,4902984,4902985,4902986,4902987,4902988,4902989,4902990,4902992,4902993,4902994,4902995,4902996,4902997,4902998,4903000,4903001,4903003,4903004,4903005,4903007,4903008,4903009,4903011,4903013,4903016,4903017,4903018,4903020,4903021,4903023,4903024,4903025,4903028,4903029,4903031,4903033,4903034,4903035,4903036,4903037,4903038,4903039,4903041,4903042,4903043,4903044,4903045,4903046,4903047,4903050,4903054,4903055,4903057,4903059,4903060,4903061,4903062,4903067,4903068,4903069,4903070,4903071,4903072,4903073,4903074,4903075,4903076,4903077,4903078,4903079,4903080,4903082,4903083,4903085,4903086,4903087,4903088,4903089,4903090,4903092,4903093,4903094,4903095,4903096,4903097,4903098,4903100,4903102,4903103,4903104,4903105,4903106,4903108,4903109,4903110,4903111,4903114,4903115,4903116,4903118,4903119,4903121,4903123,4903124,4903125,4903126,4903129,4903130,4903132,4903133,4903134,4903137,4903139,4903141,4903142,4903143,4903144,4903145,4903146,4903147,4903148,4903149,4903152,4903153,4903154,4903155,4903156,4903159,4903161,4903162,4903163,4903164,4903165,4903168,4903169,4903170,4903171,4903172,4903173,4903175,4903178,4903180,4903182,4903184,4903185,4903186,4903188,4903189,4903190,4903192,4903195,4903196,4903197,4903198,4903200,4903201,4903202,4903203,4903204,4903205,4903206,4903207,4903208,4903210,4903211,4903212,4903213,4903214,4903215,4903216,4903218,4903219,4903220,4903222,4903223,4903224,4903226,4903227,4903228,4903231,4903233,4903234,4903235,4903236,4903238,4903239,4903240,4903242,4903243,4903245,4903247,4903248,4903250,4903252,4903254,4903255,4903256,4903259,4903260,4903261,4903262,4903263,4903265,4903267,4903269,4903270,4903271,4903272,4903273,4903274,4903275,4903276,4903278,4903279,4903281,4903282,4903283,4903286,4903287,4903288,4903289,4903290,4903292,4903293,4903295,4903296,4903297,4903298,4903299,4903300,4903301,4903302,4903303,4903304,4903305,4903306,4903307,4903308,4903310,4903311,4903313,4903314,4903315,4903317,4903318,4903319,4903320,4903323,4903324,4903329,4903330,4903331,4903332,4903334,4903335,4903336,4903337,4903338,4903339,4903343,4903347,4903348,4903349,4903350,4903351,4903354,4903355,4903356,4903357,4903359,4903361,4903362,4903363,4903365,4903366,4903369,4903370,4903371,4903373,4903374,4903375,4903376,4903377,4903378,4903379,4903380,4903381,4903382,4903383,4903384,4903385,4903386,4903387,4903390,4903391,4903392,4903393,4903394,4903396,4903397,4903398,4903399,4903400,4903401,4903402,4903403,4903405,4903406,4903407,4903408,4903411,4903412,4903413,4903414,4903415,4903416,4903417,4903418,4903419,4903420,4903421,4903422,4903427,4903428,4903429,4903430,4903431,4903432,4903433,4903434,4903435,4903436,4903437,4903439,4903440,4903442,4903443,4903444,4903445,4903446,4903448,4903449,4903450,4903452,4903453,4903454,4903460,4903461,4903462,4903463,4903464,4903467,4903468,4903469,4903470,4903471,4903472,4903473,4903474,4903475,4903478,4903479,4903481,4903482,4903483,4903484,4903485,4903486,4903489,4903490,4903491,4903492,4903494,4903495,4903496,4903497,4903498,4903499,4903500,4903502,4903503,4903505,4903507,4903508,4903509,4903510,4903511,4903512,4903513,4903515,4903516,4903517,4903518,4903519,4903520,4903521,4903522,4903527,4903528,4903529,4903530,4903531,4903534,4903535,4903536,4903537,4903539,4903541,4903543,4903544,4903545,4903548,4903552,4903553,4903555,4903556,4903557,4903558,4903560,4903561,4903562,4903563,4903564,4903565,4903566,4903567,4903568,4903570,4903572,4903574,4903575,4903576,4903578,4903581,4903582,4903584,4903585,4903586,4903588,4903589,4903590,4903591,4903592,4903593,4903597,4903598,4903599,4903600,4903601,4903603,4903605,4903606,4903608,4903609,4903610,4903611,4903612,4903614,4903617,4903618,4903619,4903620,4903621,4903622,4903623,4903624,4903626,4903628,4903630,4903631,4903632,4903634,4903636,4903637,4903638,4903640,4903641,4903643,4903644,4903646,4903648,4903650,4903653,4903654,4903657,4903658,4903659,4903660,4903661,4903662,4903664,4903665,4903666,4903667,4903668,4903670,4903671,4903672,4903673,4903674,4903675,4903676,4903677,4903678,4903680,4903681,4903682,4903684,4903687,4903689,4903692,4903693,4903695,4903704,4903705,4903706,4903707,4903709,4903710,4903714,4903717,4903718,4903719,4903720,4903721,4903722,4903723,4903724,4903725,4903727,4903728,4903729,4903730,4903732,4903733,4903736,4903739,4903740,4903741,4903742,4903743,4903744,4903745,4903747,4903750,4903752,4903753,4903754,4903755,4903756,4903757,4903758,4903759,4903760,4903761,4903762,4903763,4903764,4903765,4903766,4903767,4903768,4903769,4903770,4903771,4903772,4903773,4903775,4903776,4903779,4903780,4903784,4903785,4903787,4903788,4903789,4903790,4903791,4903792,4903793,4903797,4903798,4903801,4903802,4903809,4903811,4903812,4903813,4903814,4903819,4903821,4903822,4903823,4903824,4903827,4903830,4903831,4903832,4903833,4903834,4903835,4903836,4903838,4903839,4903840,4903841,4903842,4903843,4903844,4903845,4903846,4903847,4903851,4903852,4903854,4903855,4903856,4903859,4903860,4903861,4903863,4903865,4903866,4903867,4903868,4903869,4903870,4903871,4903874,4903875,4903880,4903882,4903883,4903884,4903885,4903888,4903889,4903891,4903892,4903893,4903894,4903896,4903897,4903901,4903902,4903906,4903907,4903908,4903909,4903910,4903911,4903912,4903913,4903915,4903917,4903918,4903919,4903920,4903921,4903922,4903923,4903925,4903928,4903930,4903931,4903934,4903936,4903937,4903940,4903941,4903946,4903947,4903949,4903950,4903951,4903953,4903955,4903956,4903957,4903958,4903959,4903960,4903961,4903962,4903964,4903965,4903968,4903969,4903970,4903971,4903972,4903973,4903974,4903975,4903976,4903977,4903978,4903979,4903981,4903982,4903984,4903985,4903986,4903987,4903988,4903989,4903990,4903991,4903992,4903994,4903998,4903999,4904000,4904001,4904003,4904004,4904005,4904006,4904009,4904011,4904016,4904017,4904018,4904019,4904021,4904023,4904024,4904025,4904026,4904027,4904028,4904029,4904031,4904032,4904034,4904035,4904036,4904037,4904039,4904045,4904047,4904049,4904050,4904053,4904058,4904060,4904061,4904065,4904067,4904070,4904073,4904077,4904079,4904080,4904081,4904082,4904088,4904091,4904093,4904096,4904098,4904099,4904102,4904104,4904105,4904108,4904110,4904111,4904114,4904115,4904117,4904118,4904122,4904123,4904124,4904126,4904129,4904131,4904134,4904138,4904139,4904140,4904141,4904145,4904149,4904154,4904155,4904156,4904157,4904162,4904167,4904168,4904169,4904170,4904171,4904172,4904174,4904178,4904181,4904182,4904183,4904184,4904189,4904190,4904191,4904194,4904195,4904196,4904201,4904203,4904205,4904207,4904210,4904215,4904216,4904219,4904220,4904221,4904223,4904224,4904226,4904228,4904231,4904234,4904235,4904236,4904237,4904238,4904239,4904240,4904241,4904245,4904247,4904255,4904270,4904282,4904283,4904296,4904313,4904317,4904331,4904334,4904335,4904337,4904338,4904339,4904341,4904342,4904377,4904378,4904381,4904382,4904383,4904384,4904385,4904386,4904387,4904388,4904389,4904390,4904391,4904392,4904393,4904394,4904395,4904396,4904399,4904400,4904402,4904403,4904404,4904405,4904406,4904407,4904408,4904409,4904410,4904411,4904412,4904414,4904415,4904416,4904417,4904418,4904419,4904420,4904421,4904422,4904423,4904425,4904426,4904428,4904429,4904430,4904431,4904433,4904434,4904437,4904438,4904439,4904441,4904442,4904444,4905203,4905207,4905214,4905215,4905217,4905218,4905221,4905222,4905224,4905225,4905226,4905230,4905231,4905234,4905235,4905237,4905239,4905242,4905243,4905244,4905245,4905247,4905251,4905252,4905256,4905257,4905259,4905260,4905261,4905263,4905268,4905270,4905271,4905273,4905275,4905276,4905278,4905279,4905283,4905284,4905285,4905287,4905289,4905292,4905293,4905294,4905297,4905298,4905299,4905300,4910039,4918102,4918103,4918104,4918105,4918106,4918107,4918108,4918109,4918110,4918111,4918112,4918113,4918114,4918115,4918116,4918117,4918118,4918119,4918120,4918121,4918122,4918123,4918125,4918126,4918127,4918128,4918129,4918130,4918131,4918132,4918133,4918134,4918201,4918202,4918203,4918204,4918205,4918206,4918207,4918209,4918210,4918211,4918212,4918213,4918214,4918217,4918218,4918219,4918220,4918221,4918223,4918224,4920642,4920644,4920645,4920646,4920647,4920648,4920650,4920651,4920652,4920653,4920654,4920655,4920656,4925168,4925171,4925188,4926740,4926746,4926749,4926752,4930978,4947054,4950120,4950121,4950126,4950127,4950128,4950130,4950131,4950132,4950133,4950134,4950135,4950137,4950138,4950139,4950595,4950596
						);");

        $this->addSql('ALTER EVENT Survey_Risk_Event ENABLE;');


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
