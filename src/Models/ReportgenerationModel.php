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

    public function getTotalAwarenessCountByHierarchy($branchId)
{
    if (empty($branchId)) {
        return 0;
    }

    // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን የግንዛቤ ፈጠራ መቁጠሪያ ኩየሪ
$sql = "WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT COUNT(js.id) as total 
        FROM job_seekers js
        INNER JOIN SubBranches sb ON js.branch_id = sb.internal_id 
        WHERE js.awareness = 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['my_branch' => $branchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['total']) ? (int)$result['total'] : 0;
}

    public function getTotalJobSeekersCountByHierarchy($branchId)
    {
        if (empty($branchId)) {
            return 0;
        }

        // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን መቁጠሪያ ኩየሪ
        $sql = "WITH RECURSIVE SubBranches AS (
                    SELECT b.internal_id
                    FROM branches b
                    INNER JOIN branches root ON root.internal_id = :my_branch
                    WHERE b.path LIKE CONCAT(root.path, '%')
                )
                SELECT COUNT(js.id) as total 
                FROM job_seekers js
                INNER JOIN SubBranches sb ON js.branch_id = sb.internal_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['my_branch' => $branchId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($result['total']) ? (int)$result['total'] : 0;
    }


public function getDashboardChartsDataacall($branchId)
{
    // ቅርንጫፍ አይዲው ባዶ ከሆነ 0 ይመልስ
    if (empty($branchId)) {
        return [
            'gender' => ['ወንድ' => 0, 'ሴት' => 0],
            'sex' => ['ወንድ' => 0, 'ሴት' => 0]
        ];
    }

    // 1. መዋቅሩን በፓዝ መለየት
    $sqlBranches = "SELECT b.internal_id FROM branches b
                    INNER JOIN branches root ON root.internal_id = :my_branch
                    WHERE b.path LIKE CONCAT(root.path, '%')";
                    
    $stmtB = $this->db->prepare($sqlBranches);
    $stmtB->execute(['my_branch' => $branchId]);
    $branchIds = $stmtB->fetchAll(PDO::FETCH_COLUMN);

    // የንዑስ ቅርንጫፍ ፓዝ ባዶ ቢሆን የወቅቱን ቅርንጫፍ ብቻ መውሰድ
    if (empty($branchIds)) {
        $branchIds = [$branchId];
    }

    // 2. ፆታን በዳታቤዝ ደረጃ መቁጠር
    $placeholders = implode(',', array_fill(0, count($branchIds), '?'));
    
    $sqlGender = "SELECT gender, COUNT(*) as total 
                  FROM job_seekers 
                  WHERE branch_id IN ($placeholders) 
                    AND awareness = 1 
                  GROUP BY gender";

    $stmtG = $this->db->prepare($sqlGender);
    $stmtG->execute(array_map('intval', $branchIds));
    
    // እዚህ ጋር ስሙ በትክክል መፈጠሩን እናረጋግጣለን
    $rawResults = $stmtG->fetchAll(PDO::FETCH_ASSOC);

    // የመጨረሻውን ውጤት ማዘጋጀት
    $gender = ['ወንድ' => 0, 'ሴት' => 0];

    // ጥሬ መረጃው በትክክል መምጣቱን እና ድርድር (array) መሆኑን ማረጋገጥ
    if (is_array($rawResults)) {
        foreach ($rawResults as $row) {
            $g = isset($row['gender']) ? strtoupper(trim($row['gender'])) : '';
            $total = (int)$row['total'];

            if ($g === 'ወንድ') {
                $gender['ወንድ'] += $total;
            } elseif ($g === 'ሴት') {
                $gender['ሴት'] += $total;
            }
        }
    }
// --- ይህ አዲስ የሚጨመር ነው (የወላጆች ዳታ መግለጫ) ---
$sqlParentsGender = "SELECT sex, COUNT(*) as total 
                     FROM awareness_creation_other 
                     WHERE branch_id IN ($placeholders) 
                       AND awareness_type = 'ለስራ ፈላጊ ወላጆች' 
                     GROUP BY sex";

$stmtP = $this->db->prepare($sqlParentsGender);
$stmtP->execute(array_map('intval', $branchIds));
$rawParentsResults = $stmtP->fetchAll(PDO::FETCH_ASSOC);

$parentsGender = ['ወንድ' => 0, 'ሴት' => 0];

if (is_array($rawParentsResults)) {
    foreach ($rawParentsResults as $row) {
        $raw_g = isset($row['sex']) ? trim($row['sex']) : '';
        $g = strtoupper($raw_g); 
        $total = (int)$row['total'];

        if ($raw_g === 'ወንድ') {
            $parentsGender['ወንድ'] += $total;
        } elseif ($raw_g === 'ሴት') {
            $parentsGender['ሴት'] += $total;
        }
    }
}
// --- 3. ለሌሎች ህብረተሰብ ክፍሎች ዳታ መግለጫ (አዲስ የሚጨመር) ---
$sqlOthersGender = "SELECT sex, COUNT(*) as total 
                     FROM awareness_creation_other 
                     WHERE branch_id IN ($placeholders) 
                       AND awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች' 
                     GROUP BY sex";

$stmtO = $this->db->prepare($sqlOthersGender);
$stmtO->execute(array_map('intval', $branchIds));
$rawOthersResults = $stmtO->fetchAll(PDO::FETCH_ASSOC);

$othersGender = ['ወንድ' => 0, 'ሴት' => 0];

if (is_array($rawOthersResults)) {
    foreach ($rawOthersResults as $row) {
        $raw_g = isset($row['sex']) ? trim($row['sex']) : '';
        $g = strtoupper($raw_g); 
        $total = (int)$row['total'];

        if ($raw_g === 'ወንድ') {
            $othersGender['ወንድ'] += $total;
        } elseif ($raw_g === 'ሴት') {
            $othersGender['ሴት'] += $total;
        }
    }
}

// --- 4. ግንዛቤ ፈጠራ ያላገኙ በጾታ (አዲስ የሚጨመር) ---
$sqlNoAwareness = "SELECT gender, COUNT(*) as total 
                   FROM job_seekers 
                   WHERE branch_id IN ($placeholders) 
                     AND awareness = 0
                   GROUP BY gender";

$stmtNoA = $this->db->prepare($sqlNoAwareness);
$stmtNoA->execute(array_map('intval', $branchIds));
$rawNoAwarenessResults = $stmtNoA->fetchAll(PDO::FETCH_ASSOC);

$noAwarenessGender = ['ወንድ' => 0, 'ሴት' => 0];

if (is_array($rawNoAwarenessResults)) {
    foreach ($rawNoAwarenessResults as $row) {
        $raw_g = isset($row['gender']) ? trim($row['gender']) : '';
        $g = strtoupper($raw_g); 
        $total = (int)$row['total'];

        if ($raw_g === 'ወንድ' || $g === 'M' || $g === 'MALE') {
            $noAwarenessGender['ወንድ'] += $total;
        } elseif ($raw_g === 'ሴት' || $g === 'F' || $g === 'FEMALE') {
            $noAwarenessGender['ሴት'] += $total;
        }
    }
}
   return [
    'gender' => $gender,
    'parents_gender' => $parentsGender, // አዲስ የተጨመረ
    'others_gender' => $othersGender,
    'no_awareness_gender' => $noAwarenessGender
];
}



public function getDashboardChartsDataac($branchId)
{
    if (empty($branchId)) {
        return [
            'gender' => ['M' => 0, 'F' => 0],
            'residence' => ['urban' => 0, 'rural' => 0],
            'physical' => ['normal' => 0, 'disabled' => 0],
            'education' => [],
            'status' => []
        ];
    }

// 1. መዋቅሩን በፓዝ መለየት
$sqlBranches = "WITH RECURSIVE SubBranches AS (
                    SELECT b.internal_id FROM branches b
                    INNER JOIN branches root ON root.internal_id = :my_branch
                    WHERE b.path LIKE CONCAT(root.path, '%')
                ) SELECT internal_id FROM SubBranches";
                
$stmtB = $this->db->prepare($sqlBranches);
$stmtB->execute(['my_branch' => $branchId]);
$branchIds = $stmtB->fetchAll(PDO::FETCH_COLUMN);

// ማሻሻያ 1፦ የንዑስ ቅርንጫፍ ፓዝ ባዶ ቢሆን እንኳ የወቅቱን ቅርንጫፍ ብቻ ወስዶ ዳታ እንዲያመጣ ማድረግ
if (empty($branchIds)) {
    $branchIds = [$branchId];
}

// አደገኛ ሁኔታን ለመከላከል (የቅርንጫፍ አይዲው ጭምር ባዶ ከሆነ)
if (empty($branchIds) || $branchIds[0] === null) {
    return [
        'gender'    => ['ወንድ' => 0, 'ሴት' => 0],
        'residence' => ['ከተማ' => 0, 'ገጠር' => 0],
        'physical'  => ['0' => 0, '1' => 0],
        'education' => [],
        'status'    => []
    ];
}

$inClause = implode(',', array_map('intval', $branchIds));

// 2. ፆታ እና የመኖሪያ ቦታ ቆጠራ
$res = $this->db->query("SELECT gender, residence_status, physical_condition, education_level_category, srafelagi_huneta 
                         FROM job_seekers WHERE branch_id IN ($inClause) and awareness=1")->fetchAll(PDO::FETCH_ASSOC);

$gender = ['ወንድ' => 0, 'ሴት' => 0];
$residence = ['ከተማ' => 0, 'ገጠር' => 0];
$physical = ['0' => 0, '1' => 0];
$education = [];
$status = [];

foreach ($res as $row) {
    // ማሻሻያ 2፦ የፆታ ማጣሪያን አስተማማኝ ማድረግ (የባዶ ቦታ ማስወገጃ እና የትንሽ/ትልቅ ሆሄያት መቆጣጠሪያ)
    $g = isset($row['gender']) ? strtoupper(trim($row['gender'])) : '';
    if ($g === 'M' || $g === 'ወንድ' || $g === 'MALE') {
        $gender['ወንድ']++;
    } else if ($g === 'F' || $g === 'ሴት' || $g === 'FEMALE') {
        $gender['ሴት']++;
    }

    // ማሻሻያ 3፦ የመኖሪያ ሁኔታ ማጣሪያ (ዳታ መኖሩን ያረጋግጣል)
    $r = isset($row['residence_status']) ? trim($row['residence_status']) : '';
    if (!empty($r)) {
        if (stripos($r, 'urban') !== false || $r == 'ከተማ') {
            $residence['ከተማ']++;
        } else if (stripos($r, 'rural') !== false || $r == 'ገጠር') {
            $residence['ገጠር']++;
        }
    }

    // ማሻሻያ 4፦ የአካል ጉዳት ማጣሪያ
    $p = isset($row['physical_condition']) ? trim($row['physical_condition']) : '';
    if (!empty($p)) {
        if (stripos($p, '1') !== false){
            $physical['1']++;
        } else {
            $physical['0']++;
        }
    } else {
        $physical['0']++; // ባዶ ከሆነ እንደ መደበኛ ይቆጠራል
    }

    // ትምህርት
    $edu = !empty($row['education_level_category']) ? trim($row['education_level_category']) : 'ሌሎች';
    $education[$edu] = ($education[$edu] ?? 0) + 1;

    // ሁኔታ
    $st = !empty($row['srafelagi_huneta']) ? trim($row['srafelagi_huneta']) : 'ሌሎች';
    $status[$st] = ($status[$st] ?? 0) + 1;
}

return [
    'gender'    => $gender,
    'residence' => $residence,
    'physical'  => $physical,
    'education' => $education,
    'status'    => $status
];
}

    public function getDashboardChartsDatajs($branchId)
{
    if (empty($branchId)) {
        return [
            'gender' => ['M' => 0, 'F' => 0],
            'residence' => ['urban' => 0, 'rural' => 0],
            'physical' => ['normal' => 0, 'disabled' => 0],
            'education' => [],
            'status' => []
        ];
    }

// 1. መዋቅሩን በፓዝ መለየት
$sqlBranches = "WITH RECURSIVE SubBranches AS (
                    SELECT b.internal_id FROM branches b
                    INNER JOIN branches root ON root.internal_id = :my_branch
                    WHERE b.path LIKE CONCAT(root.path, '%')
                ) SELECT internal_id FROM SubBranches";
                
$stmtB = $this->db->prepare($sqlBranches);
$stmtB->execute(['my_branch' => $branchId]);
$branchIds = $stmtB->fetchAll(PDO::FETCH_COLUMN);

// ማሻሻያ 1፦ የንዑስ ቅርንጫፍ ፓዝ ባዶ ቢሆን እንኳ የወቅቱን ቅርንጫፍ ብቻ ወስዶ ዳታ እንዲያመጣ ማድረግ
if (empty($branchIds)) {
    $branchIds = [$branchId];
}

// አደገኛ ሁኔታን ለመከላከል (የቅርንጫፍ አይዲው ጭምር ባዶ ከሆነ)
if (empty($branchIds) || $branchIds[0] === null) {
    return [
        'gender'    => ['ወንድ' => 0, 'ሴት' => 0],
        'residence' => ['ከተማ' => 0, 'ገጠር' => 0],
        'physical'  => ['0' => 0, '1' => 0],
        'education' => [],
        'status'    => []
    ];
}

$inClause = implode(',', array_map('intval', $branchIds));

// 2. ፆታ እና የመኖሪያ ቦታ ቆጠራ
$res = $this->db->query("SELECT gender, residence_status, physical_condition, education_level_category, srafelagi_huneta 
                         FROM job_seekers WHERE branch_id IN ($inClause)")->fetchAll(PDO::FETCH_ASSOC);

$gender = ['ወንድ' => 0, 'ሴት' => 0];
$residence = ['ከተማ' => 0, 'ገጠር' => 0];
$physical = ['0' => 0, '1' => 0];
$education = [];
$status = [];

foreach ($res as $row) {
    // ማሻሻያ 2፦ የፆታ ማጣሪያን አስተማማኝ ማድረግ (የባዶ ቦታ ማስወገጃ እና የትንሽ/ትልቅ ሆሄያት መቆጣጠሪያ)
    $g = isset($row['gender']) ? strtoupper(trim($row['gender'])) : '';
    if ($g === 'M' || $g === 'ወንድ' || $g === 'MALE') {
        $gender['ወንድ']++;
    } else if ($g === 'F' || $g === 'ሴት' || $g === 'FEMALE') {
        $gender['ሴት']++;
    }

    // ማሻሻያ 3፦ የመኖሪያ ሁኔታ ማጣሪያ (ዳታ መኖሩን ያረጋግጣል)
    $r = isset($row['residence_status']) ? trim($row['residence_status']) : '';
    if (!empty($r)) {
        if (stripos($r, 'urban') !== false || $r == 'ከተማ') {
            $residence['ከተማ']++;
        } else if (stripos($r, 'rural') !== false || $r == 'ገጠር') {
            $residence['ገጠር']++;
        }
    }

    // ማሻሻያ 4፦ የአካል ጉዳት ማጣሪያ
    $p = isset($row['physical_condition']) ? trim($row['physical_condition']) : '';
    if (!empty($p)) {
        if (stripos($p, '1') !== false){
            $physical['1']++;
        } else {
            $physical['0']++;
        }
    } else {
        $physical['0']++; // ባዶ ከሆነ እንደ መደበኛ ይቆጠራል
    }

    // ትምህርት
    $edu = !empty($row['education_level_category']) ? trim($row['education_level_category']) : 'ሌሎች';
    $education[$edu] = ($education[$edu] ?? 0) + 1;

    // ሁኔታ
    $st = !empty($row['srafelagi_huneta']) ? trim($row['srafelagi_huneta']) : 'ሌሎች';
    $status[$st] = ($status[$st] ?? 0) + 1;
}

return [
    'gender'    => $gender,
    'residence' => $residence,
    'physical'  => $physical,
    'education' => $education,
    'status'    => $status
];
}
    /**
     * ለሠ1 ሪፖርት የሚያስፈልጉትን አጠቃላይ የ SUM(CASE WHEN...) ዳታዎች
     * ከኮንትሮለር ይልቅ እዚሁ ሞዴል ላይ በንጽህና በ PDO አውጥቶ የሚመልስ ሜቶድ።
     */
    
/**
 * ከተጠቃሚው ቅርንጫፍ ጀምሮ ያሉትን ንዑስ መዋቅሮች በስም ዝርዝር ያመጣል
 */
public function getAllowedBranches(string $myBranchId): array
{
    // ተጠቃሚው ያለበትን ቅርንጫፍ እና ከሱ በታች በ parent_id የተሳሰሩትን በሙሉ ያወጣል
    $sql = "
        WITH RECURSIVE AllowedBranches AS (
            SELECT b.internal_id, b.name, b.parent_id, b.level, b.path
            FROM branches b
            WHERE b.internal_id = :my_branch AND b.is_deleted = 0
            
            UNION ALL
            
            SELECT b.internal_id, b.name, b.parent_id, b.level, b.path
            FROM branches b
            INNER JOIN AllowedBranches ab ON b.parent_id = ab.internal_id
            WHERE b.is_deleted = 0
        )
        SELECT internal_id, name, level FROM AllowedBranches ORDER BY path ASC
    ";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return [];
    }
}

public function getReport1ByHierarchy(string $myBranchId, $startdate, $enddate): array
{
    // $myBranchId ባዶ ከሆነ ወይም NULL በካስቲንግ መጥቶ "" ከሆነ ቀጥታ ባዶ ሪፖርት ይመልሳል
    if (empty($myBranchId)) {
        return $this->normalizeReportRow([]);
    }

    $sql = "
        WITH RECURSIVE SubBranches AS (
            -- 1. መጀመሪያ ቅርንጫፉንና ከሥሩ ያሉትን ንዑስ ቅርንጫፎች በፓዝ ይለያል
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredAwareness AS (
            -- 2. የኮምፖዚት ኢንዴክስ ቅደም-ተከተል ጠብቆ ያነባል
            SELECT 
                aco.yemenoriya_akababi,
                aco.sex,
                aco.awareness_type
            FROM awareness_creation_other aco
            INNER JOIN SubBranches sb ON aco.branch_id = sb.internal_id 
            WHERE aco.reg_date BETWEEN :start_date AND :end_date
        )
        -- 3. ሁሉንም የኅብረተሰብ ክፍሎች እዚህ ያሰላል
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
        $stmt->bindValue(':start_date', $startdate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, PDO::PARAM_STR);
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

// ማሳሰቢያ፡ ይህ ኮድ በ Class ውስጥ መሆን አለበት!

/**
 * 2. ለስራ ፈላጊዎች የምክርና የመረጃ አገልግሎት እንዲሁም የዕድሜ ስብጥር ሪፖርት (ከ job_seekers ቴብል ብቻ)
 * ኢንዴክስ ቅደም-ተከተል፦ gender ➡️ residence_status ➡️ age
 */
public function getJobSeekersAdviceByHierarchy(string $myBranchId, $startdate, $enddate): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            -- 1. መጀመሪያ ቅርንጫፉንና ከሥሩ ያሉትን ንዑስ ቅርንጫፎች በፓዝ ይለያል
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredJobSeekers AS (
            -- 2. የኮምፖዚት ኢንዴክስ ቅደም-ተከተል ጠብቆ ያነባል (gender ➡️ residence_status ➡️ age)
            SELECT 
                js.gender,
                js.residence_status,
                js.age,
                js.education_level_category,
                js.physical_condition,
                js.srafelagi_huneta,
                js.meteleya_huneta,
                js.awareness
            FROM job_seekers js
            INNER JOIN SubBranches sb ON js.branch_id = sb.internal_id WHERE js.reg_date BETWEEN :start_date AND :end_date
        )
        SELECT
            -- ምድብ 1 እና መጨረሻው ፦ የምክርና መረጃ አገልግሎት
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' THEN 1 END) AS urban_m_advice,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' THEN 1 END) AS urban_f_advice,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' THEN 1 END) AS rural_m_advice,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' THEN 1 END) AS rural_f_advice,

            -- ምድብ 2፦ የዕድሜ ክልል ከ 15 እስከ 29 የሆኑ ስራ ፈላጊዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_m_age15_29,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_f_age15_29,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_m_age15_29,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_f_age15_29,

             -- ምድብ 3፦ የዕድሜ ክልል ከ 30 እስከ 64 የሆኑ ስራ ፈላጊዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.age >= 30 AND js.age <= 64 THEN 1 END) AS urban_m_age30_64,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.age >= 30 AND js.age <= 64 THEN 1 END) AS urban_f_age30_64,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.age >= 30 AND js.age <= 64 THEN 1 END) AS rural_m_age30_64,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.age >= 30 AND js.age <= 64 THEN 1 END) AS rural_f_age30_64,

             -- ምድብ 4፦ የተመዘገቡ የዩኒቨርሲቲ ተመራቂዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.education_level_category = 2 THEN 1 END) AS urban_m_uni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.education_level_category  = 2  THEN 1 END) AS urban_f_uni,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.education_level_category = 2  THEN 1 END) AS rural_m_uni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.education_level_category = 2  THEN 1 END) AS rural_f_uni,

            -- ምድብ 5፦ የተመዘገቡ የቴክኒክና ሙያ ተመራቂዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.education_level_category = 1 THEN 1 END) AS urban_m_tvt,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.education_level_category  = 1  THEN 1 END) AS urban_f_tvt,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.education_level_category = 1  THEN 1 END) AS rural_m_tvt,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.education_level_category = 1  THEN 1 END) AS rural_f_tvt,

            -- ምድብ 6፦ የተመዘገቡ አካል ጉዳተኞች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.physical_condition = 1 THEN 1 END) AS urban_m_phy,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.physical_condition  = 1  THEN 1 END) AS urban_f_phy,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.physical_condition = 1  THEN 1 END) AS rural_m_phy,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.physical_condition = 1  THEN 1 END) AS rural_f_phy,

             -- ምድብ 7፦ የተመዘገቡ ከስደት ተመላሾች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_m_immg,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_f_immg,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_m_immg,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_f_immg,

             -- ምድብ 8፦ የተመዘገቡ የሀገር ውስጥ ተፈናቃዮች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_m_teff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_f_teff,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_m_teff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_f_teff,

            -- ምድብ 9፦ የተመዘገቡ መኖሪያቸው ጎዳና የሆኑ ዜጎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_m_noh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_noh,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_m_noh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_noh,

            -- ምድብ 10፦ የግንዛቤ ማስጨባጫ የወሰዱ ስራ ፈላጊዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' THEN 1 END) AS urban_m_ajs,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' THEN 1 END) AS urban_f_ajs,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' THEN 1 END) AS rural_m_ajs,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' THEN 1 END) AS rural_f_ajs,

            -- ምድብ 11፦ የግንዛቤ ማስጨባጫ የወሰዱ ወጣቶች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_m_ajs15_29,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_f_ajs15_29,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_m_ajs15_29,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_f_ajs15_29,

            
            -- ምድብ 12፦ የግንዛቤ ማስጨባጫ የወሰዱ የዩኒቨርሲቲ ተመራቂዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.education_level_category = 2 THEN 1 END) AS urban_m_ajsuni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.education_level_category = 2 THEN 1 END) AS urban_f_ajsuni,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.education_level_category = 2 THEN 1 END) AS rural_m_ajsuni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.education_level_category = 2 THEN 1 END) AS rural_f_ajsuni,

            -- ምድብ 13፦ የግንዛቤ ማስጨባጫ የወሰዱ የቴክኒክና ሙያ ኮሌጅ ተመራቂዎች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.education_level_category = 1 THEN 1 END) AS urban_m_ajstvt,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.education_level_category = 1 THEN 1 END) AS urban_f_ajstvt,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.education_level_category = 1 THEN 1 END) AS rural_m_ajstvt,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.education_level_category = 1 THEN 1 END) AS rural_f_ajstvt,
        
            -- ምድብ 14፦ የግንዛቤ ማስጨባጫ የወሰዱ የአካል ጉዳተኞች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.physical_condition = 1 THEN 1 END) AS urban_m_ajsdis,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.physical_condition = 1 THEN 1 END) AS urban_f_ajsdis,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.physical_condition = 1 THEN 1 END) AS rural_m_ajsdis,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.physical_condition = 1 THEN 1 END) AS rural_f_ajsdis,
            
            -- ምድብ 15፦ የግንዛቤ ማስጨባጫ የወሰዱ ከስደት ተመላሾች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_m_ajsimmg,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_f_ajsimmg,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_m_ajsimmg,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_f_ajsimmg,

            -- ምድብ 16፦ የግንዛቤ ማስጨባጫ የወሰዱ የጎዳና ተዳዳሪ (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_ajsnoh

            FROM FilteredJobSeekers AS js
    ";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startdate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, PDO::PARAM_STR);
        $stmt->execute();

        return $this->normalizeAdviceRow($stmt->fetch(PDO::FETCH_ASSOC));
    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return $this->normalizeAdviceRow([]);
    }
}

/**
 * ለአዲሱ ቴብል Jobseekers የተዘጋጀ የዳታ ማስተካከያ (Normalization)
 */
private function normalizeAdviceRow(array|false $row): array
{
    $expectedKeys = [
        'urban_m_advice', 'urban_f_advice', 'rural_m_advice', 'rural_f_advice',
        // አዲሶቹ የዕድሜ ስብጥር ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_age15_29', 'urban_f_age15_29', 'rural_m_age15_29', 'rural_f_age15_29',
        'urban_m_age30_64', 'urban_f_age30_64', 'rural_m_age30_64', 'rural_f_age30_64',

        // የዩኒቨርሲቲ ተመራቂዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_uni', 'urban_f_uni', 'rural_m_uni', 'rural_f_uni',
        
        // የተመዘገቡ የቴክኒክና ሙያ ተመራቂዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_tvt', 'urban_f_tvt', 'rural_m_tvt', 'rural_f_tvt',

        // የተመዘገቡ አካል ጉዳተኞች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_phy', 'urban_f_phy', 'rural_m_phy', 'rural_f_phy',

        // የተመዘገቡ ከስደት ተመላሽ ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_immg', 'urban_f_immg', 'rural_m_immg', 'rural_f_immg',

        // የተመዘገቡ ተፈናቃይ ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_teff', 'urban_f_teff', 'rural_m_teff', 'rural_f_teff',

        // የተመዘገቡ መኖሪያቸው ጎዳና የሆኑ ዜጎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_noh', 'urban_f_noh', 'rural_m_noh', 'rural_f_noh',
        
        // ግንዛቤ ማስጨባጫ የወሰዱ ስራ ፈላጊዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajs', 'urban_f_ajs', 'rural_m_ajs', 'rural_f_ajs',
        
        // ግንዛቤ ማስጨባጫ የወሰዱ ወጣቶች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajs15_29', 'urban_f_ajs15_29', 'rural_m_ajs15_29', 'rural_f_ajs15_29',
        
        // ግንዛቤ ማስጨባጫ የወሰዱ የዩኒቨርሲቲ ተመራቂዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsuni', 'urban_f_ajsuni', 'rural_m_ajsuni', 'rural_f_ajsuni',

        // ግንዛቤ ማስጨባጫ የወሰዱ የቴክኒክና ሙያ ኮሌጅ ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajstvt', 'urban_f_ajstvt', 'rural_m_ajstvt', 'rural_f_ajstvt',
        
        // ግንዛቤ ማስጨባጫ የወሰዱ የአካል ጉዳተኞች  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsdis', 'urban_f_ajsdis', 'rural_m_ajsdis', 'rural_f_ajsdis',

        // ግንዛቤ ማስጨባጫ የወሰዱ ከስደት ተመላሾች  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsimmg', 'urban_f_ajsimmg', 'rural_m_ajsimmg', 'rural_f_ajsimmg',

        // ግንዛቤ ማስጨባጫ የወሰዱ ጎዳና ተዳዳሪ  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsnoh', 'urban_f_ajsnoh', 'rural_m_ajsnoh', 'rural_f_ajsnoh'
       

       
        ];

    $normalized = [];
    foreach ($expectedKeys as $key) {
        $normalized[$key] = ($row && isset($row[$key])) ? (int)$row[$key] : 0;
    }

    return $normalized;
}

/**
 * 3. ለሠ10 ሪፖርት ራሱን ችሎ የተሰራ ፈንክሽን
 */
public function getReportTenByHierarchy(string $myBranchId, string $startdate, string $enddate): array
{
    if (empty($myBranchId)) {
        return $this->normalizeReportTenRow([]);
    }

    $sql = "
        WITH RECURSIVE SubBranches AS (
            -- 1. ቅርንጫፉንና ከሥሩ ያሉትን ንዑስ ቅርንጫፎች በፓዝ ይለያል
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredJobSeekers AS (
            -- 2. ከተመረጡት ቅርንጫፎች እና ቀናት አንጻር ብቻ ያጣራል (ያለ ምንም የ gregby ገደብ)
            SELECT 
                js.gender,
                js.residence_status,
                js.age,
                js.education_level_category,
                js.physical_condition,
                js.srafelagi_huneta,
                js.meteleya_huneta
            FROM job_seekers js
            INNER JOIN SubBranches sb ON js.branch_id = sb.internal_id
            WHERE js.created_at BETWEEN :start_date AND :end_date
        )
        SELECT
            -- ምድብ 1፦ የሴቶች ስብጥር (ፆታ 'ሴት' የሆኑ ብቻ)
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' THEN 1 END) AS urban_f_reg,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' THEN 1 END) AS rural_f_reg,

            -- ምድብ 2፦ የወጣቶች ስብጥር (ዕድሜ 15-29)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_m_youth,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS urban_f_youth,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_m_youth,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.age >= 15 AND js.age <= 29 THEN 1 END) AS rural_f_youth,

            -- ምድብ 3፦ አካል ጉዳተኞች (physical_condition = 1)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.physical_condition = 1 THEN 1 END) AS urban_m_disabled,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.physical_condition = 1 THEN 1 END) AS urban_f_disabled,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.physical_condition = 1 THEN 1 END) AS rural_m_disabled,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.physical_condition = 1 THEN 1 END) AS rural_f_disabled,

            -- ምድብ 4፦ ከስደት ተመላሾች (srafelagi_huneta = 'ከስደት ተመላሽ')
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_m_returnee,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS urban_f_returnee,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_m_returnee,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 END) AS rural_f_returnee,

            -- ምድብ 5፦ የሀገር ውስጥ ተፈናቃዮች (srafelagi_huneta = 'ተፈናቃይ')
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_m_idp,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_f_idp,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_m_idp,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_f_idp,

            -- ምድብ 6፦ መኖሪያቸው ጎዳና የሆኑ ዜጎች (meteleya_huneta = 'ጎዳና ተዳዳሪ')
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_m_homeless,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_homeless,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_m_homeless,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_homeless,

            -- ምድብ 7፦ የዩኒቨርሲቲ ተመራቂዎች (education_level_category = 2)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.education_level_category = 2 THEN 1 END) AS urban_m_uni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.education_level_category = 2 THEN 1 END) AS urban_f_uni,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.education_level_category = 2 THEN 1 END) AS rural_m_uni,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.education_level_category = 2 THEN 1 END) AS rural_f_uni,

            -- ምድብ 8፦ የቴክኒክና ሙያ ተመራቂዎች (education_level_category = 1)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.education_level_category = 1 THEN 1 END) AS urban_m_tvet,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.education_level_category = 1 THEN 1 END) AS urban_f_tvet,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.education_level_category = 1 THEN 1 END) AS rural_m_tvet,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.education_level_category = 1 THEN 1 END) AS rural_f_tvet
        FROM FilteredJobSeekers AS js
    ";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId, PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startdate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, PDO::PARAM_STR);
        $stmt->execute();

        return $this->normalizeReportTenRow($stmt->fetch(PDO::FETCH_ASSOC));
    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return $this->normalizeReportTenRow([]);
    }
}
/**
 * እያንዳንዱን የዳታቤዝ ረድፍ የመጣለትን ቁልፍ ስም መነሻ በማድረግ
 * ባዶ የሆኑትን ወደ 0 የሚቀይር አጋዥ ዘዴ
 */
private function normalizeReportTenRow($row) {
    if (!$row) {
        return [];
    }
    
    // በፈርስት ሌቨል የሴቶች ሪፖርት ላይ ያሉ ቁልፎች
    $baseKeys = [
        'rural_f_reg', 'rural_f_awareness', 'rural_f_perm', 'rural_f_temp',
        'urban_f_reg', 'urban_f_awareness', 'urban_f_perm', 'urban_f_temp'
    ];
    
    // ለሌሎቹ 7 አመላካቾች (ወጣቶች፣ አካል ጉዳተኞች...) ያሉ ቁልፎች
    $indicators = ['youth', 'disabled', 'returnee', 'idp', 'homeless', 'uni', 'tvet'];
    foreach ($indicators as $ind) {
        $baseKeys[] = "rural_m_{$ind}_reg";
        $baseKeys[] = "rural_m_{$ind}_awareness";
        $baseKeys[] = "rural_m_{$ind}_perm";
        $baseKeys[] = "rural_m_{$ind}_temp";
        
        $baseKeys[] = "rural_f_{$ind}_reg";
        $baseKeys[] = "rural_f_{$ind}_awareness";
        $baseKeys[] = "rural_f_{$ind}_perm";
        $baseKeys[] = "rural_f_{$ind}_temp";
        
        $baseKeys[] = "urban_m_{$ind}_reg";
        $baseKeys[] = "urban_m_{$ind}_awareness";
        $baseKeys[] = "urban_m_{$ind}_perm";
        $baseKeys[] = "urban_m_{$ind}_temp";
        
        $baseKeys[] = "urban_f_{$ind}_reg";
        $baseKeys[] = "urban_f_{$ind}_awareness";
        $baseKeys[] = "urban_f_{$ind}_perm";
        $baseKeys[] = "urban_f_{$ind}_temp";
    }

    $normalized = [];
    foreach ($baseKeys as $key) {
        // ዳታው ካለና ባዶ ካልሆነ ወደ ቁጥር ይቀይረዋል፣ ከሌለ 0 ያደርገዋል
        $normalized[$key] = (isset($row->$key) && $row->$key !== '') ? (int)$row->$key : 
                            ((isset($row[$key]) && $row[$key] !== '') ? (int)$row[$key] : 0);
    }

    return $normalized;
}
}
