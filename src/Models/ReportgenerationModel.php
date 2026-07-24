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



public function getTotalUserCountByHierarchy($branchId)
{
    if (empty($branchId)) {
        return 0;
    }

    // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን የስራ እድል የተፈጠረላቸውን መቁጠሪያ ኩየሪ
$sql = "WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT COUNT(us.id) as total 
        FROM users us
        INNER JOIN SubBranches sb ON us.branch_id = sb.internal_id 
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['my_branch' => $branchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['total']) ? (int)$result['total'] : 0;
}   




public function getTotalEnterpriseCountByHierarchy($branchId)
{
    if (empty($branchId)) {
        return 0;
    }

    // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን የስራ እድል የተፈጠረላቸውን መቁጠሪያ ኩየሪ
$sql = "WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT COUNT(ce.id) as total 
        FROM code003 ce
        INNER JOIN SubBranches sb ON ce.branch_id = sb.internal_id 
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['my_branch' => $branchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['total']) ? (int)$result['total'] : 0;
}   



public function getDashboardChartsDataot($branchId)
{
    // የቅርንጫፍ መታወቂያው ባዶ ከሆነ ነባሪ (Default) ባዶ ዳታ መመለስ
    if (empty($branchId)) {
        return [
            'yetederajubet_akababi' => ['ከተማ' => 0, 'ገጠር' => 0],
            'project_type'          => ['የቤተሰብ' => 0, 'የመንግስት' => 0, 'በራስ ፍላጎት' => 0, 'በልዩ ሁኔታ' => 0, 'NGO' => 0]
        ];
    }

    // 1. መዋቅሩን በፓዝ መለየት (ንዑስ ቅርንጫፎችን መፈለጊያ)
    $sqlBranches = "WITH RECURSIVE SubBranches AS (
                        SELECT b.internal_id FROM branches b
                        INNER JOIN branches root ON root.internal_id = :my_branch
                        WHERE b.path LIKE CONCAT(root.path, '%')
                    ) SELECT internal_id FROM SubBranches";
                    
    $stmtB = $this->db->prepare($sqlBranches);
    $stmtB->execute(['my_branch' => $branchId]);
    $branchIds = array_filter($stmtB->fetchAll(PDO::FETCH_COLUMN));

    if (empty($branchIds)) {
        $branchIds = [$branchId];
    }

    $inClause = implode(',', array_map('intval', $branchIds));

    // 2. የአደረጃጀት ዳታዎችን ከዳታቤዝ መሳብ
    $res = $this->db->query("SELECT yetederajubet_akababi, project_type
                             FROM group_table 
                             WHERE branch_id IN ($inClause)")->fetchAll(PDO::FETCH_ASSOC);

    // ነባሪ መዋቅር ማዘጋጀት
    $akababiCounts = ['ከተማ' => 0, 'ገጠር' => 0];
    $projectTypes  = [
        'የቤተሰብ' => 0, 'የመንግስት' => 0, 'በራስ ፍላጎት' => 0, 'በልዩ ሁኔታ' => 0, 'NGO' => 0
    ];

    foreach ($res as $row) {
        // 1. የተደራጁበት አካባቢ (1 = ከተማ, 2 = ገጠር)
        $valAkababi = isset($row['yetederajubet_akababi']) ? trim((string)$row['yetederajubet_akababi']) : '';
        if ($valAkababi === '1' || $valAkababi === 'ከተማ') {
            $akababiCounts['ከተማ']++;
        } else if ($valAkababi === '2' || $valAkababi === 'ገጠር') {
            $akababiCounts['ገጠር']++;
        }

        // 2. የአደረጃጀቱ አይነት (Project Type)
        $reason = isset($row['project_type']) ? trim((string)$row['project_type']) : '';
        if (!empty($reason)) {
            if (array_key_exists($reason, $projectTypes)) {
                $projectTypes[$reason]++;
            } else {
                $projectTypes[$reason] = 1;
            }
        }
    }

    return [
        'yetederajubet_akababi' => $akababiCounts,
        'project_type'          => $projectTypes
    ];
}




public function getDashboardChartsDatajc($branchId)
{
    // የቅርንጫፍ መታወቂያው ባዶ ከሆነ ነባሪ (Default) ባዶ ዳታ መመለስ
    if (empty($branchId)) {
        return [
            'employmentstatus'  => ['ቋሚ' => 0, 'ጊዜያዊ' => 0],
            'jobcreationreason' => [
                'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' => 0,
                'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' => 0,
                'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ' => 0,
                'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' => 0,
                'በህ/ስ/ማህበራት የተቀጠሩ' => 0,
                'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' => 0,
                'በመንግስት መ/ቤቶች የተቀጠሩ' => 0,
                'የውጭ አገር ሥራ ስምሪት' => 0
            ],
            'physical'  => ['መደበኛ' => 0, 'አካል ጉዳተኛ' => 0],
            'persector' => ['ግብርና' => 0, 'ኢንዱስትሪ' => 0, 'አገልግሎት' => 0],
            'gender'    => ['ወንድ' => 0, 'ሴት' => 0]
        ];
    }

    // 1. መዋቅሩን በፓዝ መለየት (ንዑስ ቅርንጫፎችን መፈለጊያ)
    $sqlBranches = "WITH RECURSIVE SubBranches AS (
                        SELECT b.internal_id FROM branches b
                        INNER JOIN branches root ON root.internal_id = :my_branch
                        WHERE b.path LIKE CONCAT(root.path, '%')
                    ) SELECT internal_id FROM SubBranches";
                    
    $stmtB = $this->db->prepare($sqlBranches);
    $stmtB->execute(['my_branch' => $branchId]);
    $branchIds = array_filter($stmtB->fetchAll(PDO::FETCH_COLUMN));

    if (empty($branchIds)) {
        $branchIds = [$branchId];
    }

    $inClause = implode(',', array_map('intval', $branchIds));

    // 2. የስራ እድል ፈጠራ ዳታዎችን ከዳታቤዝ መሳብ
    $res = $this->db->query("SELECT employment_type, job_creation_reason, sector 
                             FROM code003sraedl 
                             WHERE branchid IN ($inClause)")->fetchAll(PDO::FETCH_ASSOC);

    // ነባሪ መዋቅር ማዘጋጀት
    $employmentstatus = ['ቋሚ' => 0, 'ጊዜያዊ' => 0];
    $jobcreationreason = [
        'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' => 0,
        'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' => 0,
        'የግል ዘርፍ ኢንቭስትመንት/ድርጅቶች የተቀጠሩ' => 0,
        'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' => 0,
        'በህ/ስ/ማህበራት የተቀጠሩ' => 0,
        'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' => 0,
        'በመንግስት መ/ቤቶች የተቀጠሩ' => 0,
        'የውጭ አገር ሥራ ስምሪት' => 0
    ];
    $persector = ['ግብርና' => 0, 'ኢንዱስትሪ' => 0, 'አገልግሎት' => 0];

    foreach ($res as $row) {
        // 1. የቅጥር ሁኔታ (1 = ቋሚ, 2 = ጊዜያዊ)
        $empStatus = isset($row['employment_type']) ? trim((string)$row['employment_type']) : '';
        if ($empStatus === '1' || $empStatus === 'ቋሚ') {
            $employmentstatus['ቋሚ']++;
        } else if ($empStatus === '2' || $empStatus === 'ጊዜያዊ') {
            $employmentstatus['ጊዜያዊ']++;
        }

        // 2. የሥራ እድል መፍጠሪያ አማራጮች
        $reason = isset($row['job_creation_reason']) ? trim((string)$row['job_creation_reason']) : '';
        if (!empty($reason)) {
            if (array_key_exists($reason, $jobcreationreason)) {
                $jobcreationreason[$reason]++;
            } else {
                $jobcreationreason[$reason] = 1;
            }
        }

        // 3. በዋና ዋና ዘርፎች (1 = ኢንዱስትሪ, 2 = ግብርና, 3 = አገልግሎት)
        $sec = isset($row['sector']) ? trim((string)$row['sector']) : '';
        if ($sec === '2' || $sec === 'ግብርና') {
            $persector['ግብርና']++;
        } else if ($sec === '1' || $sec === 'ኢንዱስትሪ') {
            $persector['ኢንዱስትሪ']++;
        } else if ($sec === '3' || $sec === 'አገልግሎት') {
            $persector['አገልግሎት']++;
        }
    }

    return [
        'employmentstatus'  => $employmentstatus,
        'jobcreationreason' => $jobcreationreason,
        'persector'         => $persector
    ];
}
    
public function getTotalOrgteamCountByHierarchy($branchId)
{
    if (empty($branchId)) {
        return 0;
    }

    // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን የስራ እድል የተፈጠረላቸውን መቁጠሪያ ኩየሪ
$sql = "WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT COUNT(gt.id) as total 
        FROM group_table gt
        INNER JOIN SubBranches sb ON gt.branch_id = sb.internal_id 
        ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['my_branch' => $branchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['total']) ? (int)$result['total'] : 0;
}



public function getTotalCreationCountByHierarchy($branchId)
{
    if (empty($branchId)) {
        return 0;
    }

    // በፓዝ (path) ተዋረድ ላይ የተመሰረተ ፈጣን የስራ እድል የተፈጠረላቸውን መቁጠሪያ ኩየሪ
$sql = "WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT COUNT(js.id) as total 
        FROM job_seekers js
        INNER JOIN SubBranches sb ON js.branch_id = sb.internal_id 
        WHERE (js.employment_status = 1 or js.employment_status = 2)";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['my_branch' => $branchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return isset($result['total']) ? (int)$result['total'] : 0;
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

// --- 5. ጠቅላላ ግንዛቤ ፈጠራ በየምድቡ (ለዋናው ትልቅ ቻርት አዲስ የሚጨመር) ---

// ሀ. የስራ ፈላጊዎች ጠቅላላ ግንዛቤ ያገኙት ድምር
$sqlJobSeekersTotal = "SELECT COUNT(*) as total 
                       FROM job_seekers 
                       WHERE branch_id IN ($placeholders) 
                         AND awareness = 1";
$stmtJST = $this->db->prepare($sqlJobSeekersTotal);
$stmtJST->execute(array_map('intval', $branchIds));
$totalJobSeekers = (int)($stmtJST->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

// ለ. የወላጆች ጠቅላላ ግንዛቤ ያገኙት ድምር
$sqlParentsTotal = "SELECT COUNT(*) as total 
                    FROM awareness_creation_other 
                    WHERE branch_id IN ($placeholders) 
                      AND awareness_type = 'ለስራ ፈላጊ ወላጆች'";
$stmtPT = $this->db->prepare($sqlParentsTotal);
$stmtPT->execute(array_map('intval', $branchIds));
$totalParents = (int)($stmtPT->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

// ሐ. የሌሎች ማህበረሰብ ክፍሎች ጠቅላላ ድምር
$sqlOthersTotal = "SELECT COUNT(*) as total 
                   FROM awareness_creation_other 
                   WHERE branch_id IN ($placeholders) 
                     AND awareness_type = 'ለሌሎች ህብረተሰብ ክፍሎች'";
$stmtOT = $this->db->prepare($sqlOthersTotal);
$stmtOT->execute(array_map('intval', $branchIds));
$totalOthers = (int)($stmtOT->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

// የሦስቱን ድምር በአንድ ላይ ማደራጀት
$totalByGroup = [
    'ስራ ፈላጊዎች' => $totalJobSeekers,
    'ወላጆች' => $totalParents,
    'ሌሎች ክፍሎች' => $totalOthers
];

   return [
    'gender' => $gender,
    'parents_gender' => $parentsGender, // አዲስ የተጨመረ
    'others_gender' => $othersGender,
    'no_awareness_gender' => $noAwarenessGender,
    'total_by_group' => $totalByGroup
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
                js.awareness,
                js.employment_status
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

            -- ምድብ 15(1)፦ የግንዛቤ ማስጨባጫ የወሰዱ ሀገር ውስጥ ተፈናቃዮች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_m_ajsteff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_f_ajsteff,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_m_ajsteff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_f_ajsteff,           

            -- ምድብ 16፦ የግንዛቤ ማስጨባጫ የወሰዱ የጎዳና ተዳዳሪ (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_ajsnoh,

            -- ከዚህ በታች ያሉት የስራ እድል የተፈጠረላቸው ቋሚና ጊዚያዊ እንዲሆንና ድምሩን እንዲያሰላ ነው
            -- ምድብ 17፦ የስራ እድል የተፈጠረላቸው ሴቶች
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' THEN 1 ELSE 0 END) AS urban_f_ajcufp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' THEN 1 ELSE 0 END) AS rural_f_ajcrfp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' THEN 1 ELSE 0 END) AS urban_f_ajcuft,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' THEN 1 ELSE 0 END) AS rural_f_ajcrft,


             -- ምድብ 18፦ የስራ እድል የተፈጠረላቸው ወጣቶች
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS rural_m_ajc15_29p,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS rural_f_ajc15_29p,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS rural_m_ajc15_29t,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS rural_f_ajc15_29t,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS urban_m_ajc15_29p,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS urban_f_ajc15_29p,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS urban_m_ajc15_29t,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.age >= 15 AND js.age <= 29 THEN 1 ELSE 0 END) AS urban_f_ajc15_29t,

            -- ምድብ 19፦ የስራ እድል የተፈጠረላቸው የአካል ጉዳተኞች
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS rural_m_ajcphydisp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS rural_f_ajcphydisp,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS rural_m_ajcphydist,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS rural_f_ajcphydist,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS urban_m_ajcphydisp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS urban_f_ajcphydisp,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS urban_m_ajcphydist,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.physical_condition = 1 THEN 1 ELSE 0 END) AS urban_f_ajcphydist,

            -- ምድብ 19፦ የስራ እድል የተፈጠረላቸው ከስደት ተመላሾች
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS rural_m_ajcimmgp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS rural_f_ajcimmgp,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS rural_m_ajcimmgt,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS rural_f_ajcimmgt,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS urban_m_ajcimmgp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS urban_f_ajcimmgp,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS urban_m_ajcimmgt,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ከስደት ተመላሽ' THEN 1 ELSE 0 END) AS urban_f_ajcimmgt,

            -- ምድብ 20፦ የስራ እድል የተፈጠረላቸው ከሀገር ውስጥ ተፈናቃዮች
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS rural_m_ajcteffp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS rural_f_ajcteffp,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS rural_m_ajctefft,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS rural_f_ajctefft,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS urban_m_ajcteffp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS urban_f_ajcteffp,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS urban_m_ajctefft,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 ELSE 0 END) AS urban_f_ajctefft,

            -- ምድብ 21፦ የስራ እድል የተፈጠረላቸው መኖሪያቸው ጎዳና የሆኑ
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS rural_m_ajcnohp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS rural_f_ajcnohp,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS rural_m_ajcnoht,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS rural_f_ajcnoht,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS urban_m_ajcnohp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS urban_f_ajcnohp,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS urban_m_ajcnoht,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 ELSE 0 END) AS urban_f_ajcnoht,

            -- ምድብ 22፦ የስራ እድል የዩኒቨርሲቲ ተመራቂ የሆኑ
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS rural_m_ajcunip,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS rural_f_ajcunip,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS rural_m_ajcunit,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS rural_f_ajcunit,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS urban_m_ajcunip,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS urban_f_ajcunip,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS urban_m_ajcunit,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.education_level_category = 2 THEN 1 ELSE 0 END) AS urban_f_ajcunit,

            -- ምድብ 22፦ የስራ እድል የቴ/ሙ ተመራቂ የሆኑ
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS rural_m_ajctvtp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '1' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS rural_f_ajctvtp,
            
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS rural_m_ajctvtt,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.employment_status = '2' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS rural_f_ajctvtt,

            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS urban_m_ajctvtp,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '1' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS urban_f_ajctvtp,
            SUM(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS urban_m_ajctvtt,
            SUM(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.employment_status = '2' AND js.education_level_category = 1 THEN 1 ELSE 0 END) AS urban_f_ajctvtt


                        
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

        // ግንዛቤ ማስጨባጫ የወሰዱ ከስደት ተመላሾች  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsteff', 'urban_f_ajsteff', 'rural_m_ajsteff', 'rural_f_ajsteff',


        // ግንዛቤ ማስጨባጫ የወሰዱ ጎዳና ተዳዳሪ  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_m_ajsnoh', 'urban_f_ajsnoh', 'rural_m_ajsnoh', 'rural_f_ajsnoh',

         //የስራ እድል የተፈጠረላቸው ሴቶች  ኪዎች (Keys) እዚህ ተጨምረዋል
        'urban_f_ajcufp', 'urban_f_ajcuft', 'rural_f_ajcrfp', 'rural_f_ajcrft',
       
        // የስራ እድል የተፈጠረላቸው ወጣቶች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajc15_29p', 'rural_f_ajc15_29p', 'rural_m_ajc15_29t', 'rural_f_ajc15_29t','urban_m_ajc15_29p', 'urban_f_ajc15_29p', 'urban_m_ajc15_29t', 'urban_f_ajc15_29t',

        // የስራ እድል የተፈጠረላቸው የአካል ጉዳተኞች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajcphydisp', 'rural_f_ajcphydisp', 'rural_m_ajcphydist', 'rural_f_ajcphydist','urban_m_ajcphydisp', 'urban_f_ajcphydisp', 'urban_m_ajcphydist', 'urban_f_ajcphydist',
        // የስራ እድል የተፈጠረላቸው ከስደት ተመላሾች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajcimmgp', 'rural_f_ajcimmgp', 'rural_m_ajcimmgt', 'rural_f_ajcimmgt','urban_m_ajcimmgp', 'urban_f_ajcimmgp', 'urban_m_ajcimmgt', 'urban_f_ajcimmgt',
        // የስራ እድል የተፈጠረላቸው ከስደት ተመላሾች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajcteffp', 'rural_f_ajcteffp', 'rural_m_ajctefft', 'rural_f_ajctefft','urban_m_ajcteffp', 'urban_f_ajcteffp', 'urban_m_ajctefft', 'urban_f_ajctefft',
        // የስራ እድል የተፈጠረላቸው ጎዳና ላይ የሚኖሩ ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajcnohp', 'rural_f_ajcnohp', 'rural_m_ajcnoht', 'rural_f_ajcnoht','urban_m_ajcnohp', 'urban_f_ajcnohp', 'urban_m_ajcnoht', 'urban_f_ajcnoht',
        // የስራ እድል የተፈጠረላቸው ዩኒቨርሲቲ ተመራቂዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajcunip', 'rural_f_ajcunip', 'rural_m_ajcunit', 'rural_f_ajcunit','urban_m_ajcunip', 'urban_f_ajcunip', 'urban_m_ajcunit', 'urban_f_ajcunit',
        // የስራ እድል የተፈጠረላቸው ቴ/ሙ ተመራቂዎች ኪዎች (Keys) እዚህ ተጨምረዋል
        'rural_m_ajctvtp', 'rural_f_ajctvtp', 'rural_m_ajctvtt', 'rural_f_ajctvtt','urban_m_ajctvtp', 'urban_f_ajctvtp', 'urban_m_ajctvtt', 'urban_f_ajctvtt'

       
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
            -- ምድብ 15(1)፦ የግንዛቤ ማስጨባጫ የወሰዱ ሀገር ውስጥ ተፈናቃዮች (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_m_ajsteff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS urban_f_ajsteff,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_m_ajsteff,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.srafelagi_huneta = 'ተፈናቃይ' THEN 1 END) AS rural_f_ajsteff,


            -- ምድብ 16፦ የግንዛቤ ማስጨባጫ የወሰዱ የጎዳና ተዳዳሪ (ኢንዴክስ ኦርደር ጠብቆ የተሰራ)
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ወንድ' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_m_ajsnoh,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js.awareness = '1' AND js.meteleya_huneta = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_ajsnoh,
            
            --ከዚህ በታች ያሉት የስራ እድል የተፈጠረላቸው ቋሚና ጊዚያዊ እንዲሆንና ድምሩን እንዲያሰላ ነው
            -- ምድብ 17፦ የስራ እድል የተፈጠረላቸው ሴቶች
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ከተማ' AND js. = 'ጎዳና ተዳዳሪ' THEN 1 END) AS urban_f_ajcufp,
            COUNT(CASE WHEN js.gender = 'ሴት' AND js.residence_status = 'ገጠር' AND js. = 'ጎዳና ተዳዳሪ' THEN 1 END) AS rural_f_ajcrfp
            
        
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









public function getJobSeekers04ByHierarchy(string $myBranchId, $startdate, $enddate, $residenceStatus, string $sectorName): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredJobSeekers AS (
            SELECT
                c.subsector AS sub_sector_id,
                c.employment_type,
                js.gender
            FROM code003sraedl c
            INNER JOIN job_seekers js ON c.jobseeker_id = js.job_seeker_id
            INNER JOIN SubBranches sb ON CAST(c.branchid AS CHAR) = CAST(sb.internal_id AS CHAR) 
            WHERE js.residence_status = :residence_status
        )
        SELECT 
            sub.subsector AS sub_sector_name,
            COUNT(fjs.sub_sector_id) AS total_joined_seekers,
            SUM(CASE WHEN fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS m_perm,
            SUM(CASE WHEN fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS f_perm,
            SUM(CASE WHEN fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS m_temp,
            SUM(CASE WHEN fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS f_temp
        FROM sub_sector sub
        INNER JOIN sector_table sec ON sub.sectorid = sec.sectorid
        LEFT JOIN FilteredJobSeekers fjs ON sub.sub_sectorid = fjs.sub_sector_id 
        GROUP BY sub.sub_sectorid, sub.subsector
        ORDER BY sub.subsector ASC
    ";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId, \PDO::PARAM_STR);
        $stmt->bindValue(':residence_status', $residenceStatus, \PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        return [];
    }
}


public function getJobSeekers06ByHierarchy(string $myBranchId, string $startdate, string $enddate, ?string $residenceStatus, string $sectorName): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        ),
        FilteredJobSeekers AS (
            SELECT
                c.subsector AS sub_sector_id,
                TRIM(c.employment_type) AS employment_type,
                TRIM(c.job_creation_reason) AS job_creation_reason,
                TRIM(js.gender) AS gender
            FROM code003sraedl c
            INNER JOIN job_seekers js ON c.jobseeker_id = js.job_seeker_id
            INNER JOIN SubBranches sb ON CAST(c.branchid AS CHAR) = CAST(sb.internal_id AS CHAR) 
            INNER JOIN sub_sector sub_filter ON c.subsector = sub_filter.sub_sectorid
            INNER JOIN sector_table sec_filter ON sub_filter.sectorid = sec_filter.sectorid
            WHERE (:residence_status IS NULL OR js.residence_status = :residence_status_check)
              AND sec_filter.sector = :sector_name
              AND c.created_at BETWEEN :start_date AND :end_date
        )
        SELECT 
            sub.subsector AS sub_sector_name,
            
            -- 1. አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ
            SUM(CASE WHEN fjs.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c1,
            SUM(CASE WHEN fjs.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c2,
            SUM(CASE WHEN fjs.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c3,
            SUM(CASE WHEN fjs.job_creation_reason = 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c4,

            -- 2. ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ
            SUM(CASE WHEN fjs.job_creation_reason = 'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c5,
            SUM(CASE WHEN fjs.job_creation_reason = 'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c6,
            SUM(CASE WHEN fjs.job_creation_reason = 'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c7,
            SUM(CASE WHEN fjs.job_creation_reason = 'ነባር ኢንተርፕራይዞችን በማስፋፋት የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c8,

            -- 3. የግል ዘርፍ ኢንቭስትመንት/ድርጅቶች የተቀጠሩ
            SUM(CASE WHEN fjs.job_creation_reason = 'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c9,
            SUM(CASE WHEN fjs.job_creation_reason = 'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c10,
            SUM(CASE WHEN fjs.job_creation_reason = 'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c11,
            SUM(CASE WHEN fjs.job_creation_reason = 'የግል ዘርፍ ኢንቨስትመንት/ድርጅቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c12,

            -- 4. በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c13,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c14,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c15,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት ኢንተርፕራይዞች/ግዙፍ ፕሮጀክቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c16,

            -- 5. በህ/ስ/ማህበራት የተቀጠሩ
            SUM(CASE WHEN fjs.job_creation_reason = 'በህ/ስ/ማህበራት የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c17,
            SUM(CASE WHEN fjs.job_creation_reason = 'በህ/ስ/ማህበራት የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c18,
            SUM(CASE WHEN fjs.job_creation_reason = 'በህ/ስ/ማህበራት የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c19,
            SUM(CASE WHEN fjs.job_creation_reason = 'በህ/ስ/ማህበራት የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c20,

            -- 6. መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር
            SUM(CASE WHEN fjs.job_creation_reason = 'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c21,
            SUM(CASE WHEN fjs.job_creation_reason = 'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c22,
            SUM(CASE WHEN fjs.job_creation_reason = 'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c23,
            SUM(CASE WHEN fjs.job_creation_reason = 'መንግስታዊ ያልሆኑ ድርጅቶች ቅጥር' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c24,

            -- 7. በመንግስት መ/ቤቶች የተቀጠሩ
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት መ/ቤቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c25,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት መ/ቤቶች የተቀጠሩ' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c26,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት መ/ቤቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c27,
            SUM(CASE WHEN fjs.job_creation_reason = 'በመንግስት መ/ቤቶች የተቀጠሩ' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c28,

            -- 8. የውጭ አገር ሥራ ስምሪት
            SUM(CASE WHEN fjs.job_creation_reason = 'የውጭ አገር ሥራ ስምሪት' AND fjs.employment_type = '1' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c29,
            SUM(CASE WHEN fjs.job_creation_reason = 'የውጭ አገር ሥራ ስምሪት' AND fjs.employment_type = '1' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c30,
            SUM(CASE WHEN fjs.job_creation_reason = 'የውጭ አገር ሥራ ስምሪት' AND fjs.employment_type = '2' AND fjs.gender = 'ወንድ' THEN 1 ELSE 0 END) AS c31,
            SUM(CASE WHEN fjs.job_creation_reason = 'የውጭ አገር ሥራ ስምሪት' AND fjs.employment_type = '2' AND fjs.gender = 'ሴት' THEN 1 ELSE 0 END) AS c32

        FROM sub_sector sub
        INNER JOIN sector_table sec ON sub.sectorid = sec.sectorid
        LEFT JOIN FilteredJobSeekers fjs ON sub.sub_sectorid = fjs.sub_sector_id 
        WHERE sec.sector = :sector_name
        GROUP BY sub.sub_sectorid, sub.subsector
        ORDER BY sub.subsector ASC
    ";

    try {
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':my_branch', $myBranchId, \PDO::PARAM_STR);
        
        if ($residenceStatus === null) {
            $stmt->bindValue(':residence_status', null, \PDO::PARAM_NULL);
            $stmt->bindValue(':residence_status_check', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':residence_status', $residenceStatus, \PDO::PARAM_STR);
            $stmt->bindValue(':residence_status_check', $residenceStatus, \PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':sector_name', $sectorName, \PDO::PARAM_STR);
        $stmt->bindValue(':start_date', $startdate, \PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, \PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        return [];
    }
}



public function getJobSeekers08ByHierarchy(string $myBranchId, string $startdate, string $enddate, ?string $residenceStatus): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT 
            TRIM(c.job_creation_reason) AS job_reason,
            sec.sector AS sector_name,
            
            -- ቋሚ (Employment Type = 1)
            SUM(CASE WHEN TRIM(c.employment_type) = '1' AND TRIM(js.gender) = 'ወንድ' THEN 1 ELSE 0 END) AS perm_m,
            SUM(CASE WHEN TRIM(c.employment_type) = '1' AND TRIM(js.gender) = 'ሴት' THEN 1 ELSE 0 END) AS perm_f,
            
            -- ጊዜያዊ (Employment Type = 2)
            SUM(CASE WHEN TRIM(c.employment_type) = '2' AND TRIM(js.gender) = 'ወንድ' THEN 1 ELSE 0 END) AS temp_m,
            SUM(CASE WHEN TRIM(c.employment_type) = '2' AND TRIM(js.gender) = 'ሴት' THEN 1 ELSE 0 END) AS temp_f

        FROM code003sraedl c
        INNER JOIN job_seekers js ON c.jobseeker_id = js.job_seeker_id
        INNER JOIN SubBranches sb ON CAST(c.branchid AS CHAR) = CAST(sb.internal_id AS CHAR) 
        INNER JOIN sub_sector sub ON c.subsector = sub.sub_sectorid
        INNER JOIN sector_table sec ON sub.sectorid = sec.sectorid
        WHERE (:residence_status IS NULL OR js.residence_status = :residence_status_check)
          AND c.created_at BETWEEN :start_date AND :end_date
        GROUP BY TRIM(c.job_creation_reason), sec.sector
    ";

    try {
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':my_branch', $myBranchId, \PDO::PARAM_STR);
        
        if ($residenceStatus === null) {
            $stmt->bindValue(':residence_status', null, \PDO::PARAM_NULL);
            $stmt->bindValue(':residence_status_check', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':residence_status', $residenceStatus, \PDO::PARAM_STR);
            $stmt->bindValue(':residence_status_check', $residenceStatus, \PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':start_date', $startdate, \PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, \PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        return [];
    }
}

public function getJobSeekers02ByHierarchy(string $myBranchId, string $startdate, string $enddate, ?string $residenceStatus): array
{
    $sql = "
        WITH RECURSIVE SubBranches AS (
            SELECT b.internal_id
            FROM branches b
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')
        )
        SELECT 
            TRIM(sub.sub_sector_name) AS sub_sector_name,
            sec.sector AS sector_name,
            
            -- የኢንተርፕራይዝ ቁጥር (1 = በንግድ ማህበር, 2 = በግላበጥ)
            SUM(CASE WHEN TRIM(c.organization_type) = '1' THEN 1 ELSE 0 END) AS biz_mahber,
            SUM(CASE WHEN TRIM(c.organization_type) = '2' THEN 1 ELSE 0 END) AS biz_private,

            -- ቋሚ (Employment Type = 1)
            SUM(CASE WHEN TRIM(c.employment_type) = '1' AND TRIM(js.gender) = 'ወንድ' THEN 1 ELSE 0 END) AS perm_m,
            SUM(CASE WHEN TRIM(c.employment_type) = '1' AND TRIM(js.gender) = 'ሴት' THEN 1 ELSE 0 END) AS perm_f,
            
            -- ጊዜያዊ (Employment Type = 2)
            SUM(CASE WHEN TRIM(c.employment_type) = '2' AND TRIM(js.gender) = 'ወንድ' THEN 1 ELSE 0 END) AS temp_m,
            SUM(CASE WHEN TRIM(c.employment_type) = '2' AND TRIM(js.gender) = 'ሴት' THEN 1 ELSE 0 END) AS temp_f

        FROM code003sraedl c
        INNER JOIN job_seekers js ON c.jobseeker_id = js.job_seeker_id
        INNER JOIN SubBranches sb ON CAST(c.branchid AS CHAR) = CAST(sb.internal_id AS CHAR) 
        INNER JOIN sub_sector sub ON c.subsector = sub.sub_sectorid
        INNER JOIN sector_table sec ON sub.sectorid = sec.sectorid
        WHERE (:residence_status IS NULL OR js.residence_status = :residence_status_check)
          AND c.created_at BETWEEN :start_date AND :end_date
        GROUP BY TRIM(sub.sub_sector_name), sec.sector
    ";

    try {
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':my_branch', $myBranchId, \PDO::PARAM_STR);
        
        if ($residenceStatus === null) {
            $stmt->bindValue(':residence_status', null, \PDO::PARAM_NULL);
            $stmt->bindValue(':residence_status_check', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':residence_status', $residenceStatus, \PDO::PARAM_STR);
            $stmt->bindValue(':residence_status_check', $residenceStatus, \PDO::PARAM_STR);
        }
        
        $stmt->bindValue(':start_date', $startdate, \PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $enddate, \PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_CLASS);

    } catch (\PDOException $e) {
        return [];
    }
}
}