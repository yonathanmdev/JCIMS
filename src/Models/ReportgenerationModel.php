<?php
namespace App\Models;

use PDO;

class ReportgenerationModel 
{
    private $db;

    // የምንጠቀመው ዋና የዳታቤዝ ቴብል
    protected $table = 'job_seekers';

    public function __construct($db) 
    {
        $this->db = $db;
    }

    /**
     * ለሠ1 ሪፖርት የሚያስፈልጉትን አጠቃላይ የ SUM(CASE WHEN...) ዳታዎች
     * ከኮንትሮለር ይልቅ እዚሁ ሞዴል ላይ በንጽህና በ PDO አውጥቶ የሚመልስ ሜቶድ።
     */
    public function getSe1ReportData($year = null)
    {
        $query = "
            SELECT 
                -- 1. አጠቃላይ የተመዘገቡ ስራ ፈላጊዎች
                SUM(CASE WHEN residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_reg,
                SUM(CASE WHEN residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_reg,
                SUM(CASE WHEN residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_reg,
                SUM(CASE WHEN residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_reg,

                -- 2. በዕድሜ (15-29 ዓመት)
                SUM(CASE WHEN age >= 15 AND age <= 29 AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_age15_29,
                SUM(CASE WHEN age >= 15 AND age <= 29 AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_age15_29,
                SUM(CASE WHEN age >= 15 AND age <= 29 AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_age15_29,
                SUM(CASE WHEN age >= 15 AND age <= 29 AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_age15_29,

                -- 3. በዕድሜ (30-64 ዓመት)
                SUM(CASE WHEN age >= 30 AND age <= 64 AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_age30_64,
                SUM(CASE WHEN age >= 30 AND age <= 64 AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_age30_64,
                SUM(CASE WHEN age >= 30 AND age <= 64 AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_age30_64,
                SUM(CASE WHEN age >= 30 AND age <= 64 AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_age30_64,

                -- 4. የተመዘገቡ የዩኒቨርሲቲ ተመራቂዎች ብዛት
                SUM(CASE WHEN (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_uni,
                SUM(CASE WHEN (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_uni,
                SUM(CASE WHEN (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_uni,
                SUM(CASE WHEN (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_uni,

                -- 5. የተመዘገቡ የቴክኒክና ሙያ ተመራቂዎች ብዛት
                SUM(CASE WHEN (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_tvet,
                SUM(CASE WHEN (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_tvet,
                SUM(CASE WHEN (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_tvet,
                SUM(CASE WHEN (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_tvet,

                -- 6. የተመዘገቡ አካል ጉዳተኞች
                SUM(CASE WHEN physical_condition = '1' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_dis,
                SUM(CASE WHEN physical_condition = '1' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_dis,
                SUM(CASE WHEN physical_condition = '1' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_dis,
                SUM(CASE WHEN physical_condition = '1' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_dis,

                -- 7. የተመዘገቡ ከስደት ተመላሾች
                SUM(CASE WHEN srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_returnee,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_returnee,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_returnee,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_returnee,

                -- 8. የተመዘገቡ የሀገር ውስጥ ተፈናቃዮች
                SUM(CASE WHEN srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_idp,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_idp,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_idp,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_idp,

                -- 9. የተመዘገቡ መኖሪያቸው ጎዳና የሆኑ ዜጎች
                SUM(CASE WHEN meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_homeless,
                SUM(CASE WHEN meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_homeless,
                SUM(CASE WHEN meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_homeless,
                SUM(CASE WHEN meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_homeless,

                -- 10. የግንዛቤ ማሳደጊያ የተሰጣቸው ስራ ፈላጊዎች
                SUM(CASE WHEN  awareness = '1' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr,
                SUM(CASE WHEN  awareness = '1' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr,
                SUM(CASE WHEN  awareness = '1' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr,
                SUM(CASE WHEN  awareness = '1' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr,

                -- 11. የግንዛቤ ማሳደጊያ የተሰጣቸው ወጣቶች ስራ ፈላጊዎች
                SUM(CASE WHEN awareness = '1' AND age >= 15 AND age <= 29 AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_youth,
                SUM(CASE WHEN awareness = '1' AND age >= 15 AND age <= 29 AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_youth,
                SUM(CASE WHEN awareness = '1' AND age >= 15 AND age <= 29 AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_youth,
                SUM(CASE WHEN awareness = '1' AND age >= 15 AND age <= 29 AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_youth,

                -- 12. ለሌሎች ህብረተሰብ ክፍሎች
                SUM(CASE WHEN (meteleya_huneta NOT LIKE '%ስደት%' AND meteleya_huneta NOT LIKE '%ተፈናቃይ%' AND meteleya_huneta NOT LIKE '%ጎዳና%' AND physical_condition != 'አዎ' AND (educational_level NOT LIKE '%ዲግሪ%' AND educational_level NOT LIKE '%Degree%' AND educational_level NOT LIKE '%ሌቨል%' AND educational_level NOT LIKE '%Level%')) AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_others,
                SUM(CASE WHEN (meteleya_huneta NOT LIKE '%ስደት%' AND meteleya_huneta NOT LIKE '%ተፈናቃይ%' AND meteleya_huneta NOT LIKE '%ጎዳና%' AND physical_condition != 'አዎ' AND (educational_level NOT LIKE '%ዲግሪ%' AND educational_level NOT LIKE '%Degree%' AND educational_level NOT LIKE '%ሌቨል%' AND educational_level NOT LIKE '%Level%')) AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_others,
                SUM(CASE WHEN (meteleya_huneta NOT LIKE '%ስደት%' AND meteleya_huneta NOT LIKE '%ተፈናቃይ%' AND meteleya_huneta NOT LIKE '%ጎዳና%' AND physical_condition != 'አዎ' AND (educational_level NOT LIKE '%ዲግሪ%' AND educational_level NOT LIKE '%Degree%' AND educational_level NOT LIKE '%ሌቨል%' AND educational_level NOT LIKE '%Level%')) AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_others,
                SUM(CASE WHEN (meteleya_huneta NOT LIKE '%ስደት%' AND meteleya_huneta NOT LIKE '%ተፈናቃይ%' AND meteleya_huneta NOT LIKE '%ጎዳና%' AND physical_condition != 'አዎ' AND (educational_level NOT LIKE '%ዲግሪ%' AND educational_level NOT LIKE '%Degree%' AND educational_level NOT LIKE '%ሌቨል%' AND educational_level NOT LIKE '%Level%')) AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_others,

                -- 13. ስራ ፈላጊ ወላጆች
                SUM(CASE WHEN srafelagi_huneta LIKE '%ወላጅ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_parents,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ወላጅ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_parents,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ወላጅ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_parents,
                SUM(CASE WHEN srafelagi_huneta LIKE '%ወላጅ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_parents,

                -- 14. የግንዛቤ ማሳደጊያ የተሰጣቸው የዩኒቨርሲቲ ተመራቂዎች
                SUM(CASE WHEN awareness = '1' AND (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_uni,
                SUM(CASE WHEN awareness = '1' AND (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_uni,
                SUM(CASE WHEN awareness = '1' AND (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_uni,
                SUM(CASE WHEN awareness = '1' AND (educational_level LIKE '%የመጀመሪያ ዲግሪ%' OR educational_level LIKE '%ሁለተኛ ዲግሪ%') AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_uni,

                -- 15. የግንዛቤ ማሳደጊያ የተሰጣቸው የቴክኒክና ሙያ ተመራቂዎች
                SUM(CASE WHEN  awareness = '1' AND (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_tvet,
                SUM(CASE WHEN  awareness = '1' AND (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_tvet,
                SUM(CASE WHEN  awareness = '1' AND (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_tvet,
                SUM(CASE WHEN  awareness = '1' AND (educational_level LIKE '%ደረጃ 1%' OR educational_level LIKE '%ደረጃ 2%' OR educational_level LIKE '%ደረጃ 3%' OR educational_level LIKE '%ደረጃ 4%' OR educational_level LIKE '%ደረጃ 5%') AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_tvet,

                -- 16. የግንዛቤ ማሳደጊያ የተሰጣቸው አካል ጉዳተኞች
                SUM(CASE WHEN awareness = '1' AND physical_condition = '1' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_dis,
                SUM(CASE WHEN awareness = '1' AND physical_condition = '1' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_dis,
                SUM(CASE WHEN awareness = '1' AND physical_condition = '1' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_dis,
                SUM(CASE WHEN awareness = '1' AND physical_condition = '1' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_dis,

                -- 17. የግንዛቤ ማሳደጊያ የተሰጣቸው ከስደት ተመላሾች
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_ret,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_ret,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_ret,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ከስደት ተመላሽ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_ret,

                -- 18. የግንዛቤ ማሳደጊያ የተሰጣቸው የሀገር ውስጥ ተፈናቃዮች
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_idp,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_idp,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_idp,
                SUM(CASE WHEN awareness = '1' AND srafelagi_huneta LIKE '%ተፈናቃይ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_idp,

                -- 19. የግንዛቤ ማሳደጊያ የተሰጣቸው መኖሪያቸው ጎዳና የሆኑ ዜጎች
                SUM(CASE WHEN awareness = '1' AND meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_awr_homeless,
                SUM(CASE WHEN awareness = '1' AND meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_awr_homeless,
                SUM(CASE WHEN awareness = '1' AND meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_awr_homeless,
                SUM(CASE WHEN awareness = '1' AND meteleya_huneta LIKE '%ጎዳና ተዳዳሪ%' AND residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_awr_homeless,

                -- 20. ለስራ ፈላጊዎች ተገቢውን የምክርና የመረጃ አገልግሎት መስጠት
                SUM(CASE WHEN residence_status = 'ከተማ' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_advice,
                SUM(CASE WHEN residence_status = 'ከተማ' AND gender = 'ሴት' THEN 1 ELSE 0 END) as urban_f_advice,
                SUM(CASE WHEN residence_status = 'ገጠር' AND gender = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_advice,
                SUM(CASE WHEN residence_status = 'ገጠር' AND gender = 'ሴት' THEN 1 ELSE 0 END) as rural_f_advice
            FROM {$this->table}
            WHERE 1=1
        ";

        $params = [];
        if ($year) {
            $query .= " AND fiscal_year = ?";
            $params[] = $year;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $reportData = $stmt->fetch(PDO::FETCH_ASSOC);

        // ዳታ ከሌለ ሁሉንም 0 አድርጎ እንዲመልስ
        if (!$reportData || $reportData['urban_m_reg'] === null) {
            $fields = [
                'reg', 'age15_29', 'age30_64', 'uni', 'tvet', 'dis', 'returnee', 'idp', 
                'homeless', 'awr', 'awr_youth', 'others', 'parents', 'awr_uni', 'awr_tvet', 
                'awr_dis', 'awr_ret', 'awr_idp', 'awr_homeless', 'advice'
            ];
            $reportData = [];
            foreach ($fields as $field) {
                $reportData["urban_m_{$field}"] = 0;
                $reportData["urban_f_{$field}"] = 0;
                $reportData["rural_m_{$field}"] = 0;
                $reportData["rural_f_{$field}"] = 0;
            }
        }

        return $reportData;
    }



    /**
 * ለሌሎች ህብረተሰብ ክፍሎች (12) እና ለስራ ፈላጊ ወላጆች (13) ብቻ ከሌላው ቴብል ያመጣል
 */
public function getSe1OtherData($year = null, $branchId = null)
{
    $params = [];
    $whereClause = " WHERE 1=1";

    // የዓመት ማጣሪያ
    if (!empty($year)) {
        $whereClause .= " AND fiscal_year = :year";
        $params[':year'] = $year;
    }
    // የቅርንጫፍ ማጣሪያ
    if (!empty($branchId)) {
        $whereClause .= " AND branch_id = :branch_id";
        $params[':branch_id'] = $branchId;
    }

$sql = "SELECT 
        -- 12. ለሌሎች ህብረተሰብ ክፍሎች (LIKE ተስተካክሏል)
        SUM(CASE WHEN awareness_type LIKE '%ለሌሎች ህብረተሰብ ክፍሎች%' AND yemenoriya_akababi = 'ከተማ' AND sex = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_others,
        SUM(CASE WHEN awareness_type LIKE '%ለሌሎች ህብረተሰብ ክፍሎች%' AND yemenoriya_akababi = 'ከተማ' AND sex = 'ሴት' THEN 1 ELSE 0 END) as urban_f_others,
        SUM(CASE WHEN awareness_type LIKE '%ለሌሎች ህብረተሰብ ክፍሎች%' AND yemenoriya_akababi = 'ገጠር' AND sex = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_others,
        SUM(CASE WHEN awareness_type LIKE '%ለሌሎች ህብረተሰብ ክፍሎች%' AND yemenoriya_akababi = 'ገጠር' AND sex = 'ሴት' THEN 1 ELSE 0 END) as rural_f_others,

        -- 13. ስራ ፈላጊ ወላጆች (LIKE ተስተካክሏል)
        SUM(CASE WHEN awareness_type LIKE '%ለስራ ፈላጊ ወላጆች%' AND yemenoriya_akababi = 'ከተማ' AND sex = 'ወንድ' THEN 1 ELSE 0 END) as urban_m_parents,
        SUM(CASE WHEN awareness_type LIKE '%ለስራ ፈላጊ ወላጆች%' AND yemenoriya_akababi = 'ከተማ' AND sex = 'ሴት' THEN 1 ELSE 0 END) as urban_f_parents,
        SUM(CASE WHEN awareness_type LIKE '%ለስራ ፈላጊ ወላጆች%' AND yemenoriya_akababi = 'ገጠር' AND sex = 'ወንድ' THEN 1 ELSE 0 END) as rural_m_parents,
        SUM(CASE WHEN awareness_type LIKE '%ለስራ ፈላጊ ወላጆች%' AND yemenoriya_akababi = 'ገጠር' AND sex = 'ሴት' THEN 1 ELSE 0 END) as rural_f_parents
    FROM awareness_creation_other" . $whereClause;

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
}


public function getReport1ByHierarchy(string $myBranchId): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            -- 1. መጀመሪያ ቅርንጫፉንና ከሥሩ ያሉትን ንዑስ ቅርንጫፎች በፓዝ ይለያል
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredAwareness AS (
            -- 2. ኮምፖዚት ኢንዴክስ ቅደም-ተከተል ሙሉ በሙሉ ጠብቆ ያነባል (image_6c5b41.png)
            SELECT 
                aco.yemenoriya_akababi,
                aco.sex,
                aco.awareness_type
            FROM awareness_creation_other aco
            INNER JOIN SubBranches sb ON aco.branch_id = sb.internal_id
        )
        -- 3. ሁሉንም የኅብረተሰብ ክፍሎች የኢንዴክስህን ቅደም ተከተል ጠብቆ እዚህ ያሰላል
        SELECT
            -- ምድብ 1፡ ለስራ ፈላጊ ወላጆች
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ከተማ' AND aco.sex = 'ወንድ' AND aco.awareness_type = 'ለስራ ፈላጊ ወላጆች' THEN 1 END) AS urban_m_parents,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ከተማ' AND aco.sex = 'ሴት' AND aco.awareness_type = 'ለስራ ፈላጊ ወላጆች' THEN 1 END) AS urban_f_parents,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ገጠር' AND aco.sex = 'ወንድ' AND aco.awareness_type = 'ለስራ ፈላጊ ወላጆች' THEN 1 END) AS rural_m_parents,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ገጠር' AND aco.sex = 'ሴት' AND aco.awareness_type = 'ለስራ ፈላጊ ወላጆች' THEN 1 END) AS rural_f_parents,

            -- ምድብ 2፡ ለሌሎች ህብረተሰብ ክፍሎች
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ከተማ' AND aco.sex = 'ወንድ' AND aco.awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች' THEN 1 END) AS urban_m_others,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ከተማ' AND aco.sex = 'ሴት' AND aco.awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች' THEN 1 END) AS urban_f_others,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ገጠር' AND aco.sex = 'ወንድ' AND aco.awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች' THEN 1 END) AS rural_m_others,
            COUNT(CASE WHEN aco.yemenoriya_akababi = 'ገጠር' AND aco.sex = 'ሴት' AND aco.awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች' THEN 1 END) AS rural_f_others
        FROM FilteredAwareness AS aco
    ";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->normalizeReportRow($stmt->fetch(PDO::FETCH_ASSOC));
    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return $this->normalizeReportRow([]);
    }
}

private function normalizeReportRow(array|false $row): array
{
    // እያንዳንዱ አዲስ የተጨመረው ቁልፍ (Key) እዚህ ውስጥ ገብቷል ዳታ ባይኖር ራሱ 0 ተደርጎ ይወጣል
    $expectedKeys = [
        'urban_m_parents', 'urban_f_parents', 'rural_m_parents', 'rural_f_parents',
        'urban_m_others', 'urban_f_others', 'rural_m_others', 'rural_f_others',
    ];

    $normalized = [];
    foreach ($expectedKeys as $key) {
        $normalized[$key] = ($row && isset($row[$key])) ? (int)$row[$key] : 0;
    }

    return $normalized;
}
}