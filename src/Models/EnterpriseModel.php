<?php
namespace App\Models;
use App\Helpers\AmharicNormalizer;
use PDO;
class EnterpriseModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }



public function searchJobSeekersForIndividualEnterprise(int $branchId, string $term): array
{
    $normalized = AmharicNormalizer::normalize($term);
    $words = preg_split('/\s+/', trim($normalized), -1, PREG_SPLIT_NO_EMPTY);
    $booleanTerm = implode(' ', array_map(fn($w) => $w . '*', $words));

    $useMatch = mb_strlen($normalized) >= 3;

    $params = [
        ':branch_id'      => $branchId,
        ':term_like'      => '%' . $normalized . '%',
        ':job_seeker_id'  => $term . '%', // prefix match, not substring
    ];

    $matchClause = '';
    if ($useMatch) {
        $matchClause = "MATCH(js.full_name_normalized) AGAINST (:term_bool IN BOOLEAN MODE) OR ";
        $params[':term_bool'] = $booleanTerm;
    }

    $sql = "SELECT js.id, js.job_seeker_id,
                   CONCAT(js.first_name, ' ', js.father_name, ' ', js.last_name) AS label
            FROM job_seekers js
            WHERE js.branch_id = :branch_id
              AND ({$matchClause}CAST(js.job_seeker_id AS CHAR) LIKE :job_seeker_id
                   OR js.full_name_normalized LIKE :term_like)
              AND (js.employment_status IS NULL OR js.employment_status <> 1)
            LIMIT 15";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $val) {
        $type = ($key === ':branch_id') ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $val, $type);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function searchGroupsForAssociationEnterprise(int $branchId, string $term): array
{
    $normalized = AmharicNormalizer::normalize($term);

    $sql = "SELECT gt.id, gt.table_id, gt.project_type, gt.association_name AS label
            FROM group_table gt
            WHERE gt.branch_id = :branch_id AND gt.is_enterprise = 0
              AND (
                    gt.association_name LIKE :term_like
                    OR CAST(gt.table_id AS CHAR) LIKE :table_id_like
                  )
            LIMIT 15";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':branch_id', $branchId, PDO::PARAM_INT);
    $stmt->bindValue(':term_like', '%' . $normalized . '%', PDO::PARAM_STR);
    $stmt->bindValue(':table_id_like', '%' . $term . '%', PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Confirms the job seeker belongs to the caller's branch, and that they
 * don't already hold a permanent job (employment_status = 1).
 */
/**
 * Confirms the job seeker (looked up by UUID) belongs to the caller's branch,
 * isn't already permanently employed, and returns the resolved bigint job_seeker_id.
 */
private function validateJobSeekerForEnterprise(int $branchId, string $jobSeekerId): array
{
    $stmt = $this->db->prepare("
        SELECT job_seeker_id, concat(first_name, ' ', father_name, ' ', last_name) AS full_name, phone_number, employment_status
        FROM job_seekers
        WHERE branch_id = :branchId AND id = :jobSeekerId
        LIMIT 1
    ");
    $stmt->execute([
        ':branchId'    => $branchId,
        ':jobSeekerId' => $jobSeekerId,
    ]);
    $jobSeeker = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$jobSeeker) {
        return ['status' => 'error', 'message' => 'የመረጡት ስራ ፈላጊ ከቅርንጫፍዎ ጋር አይዛመድም።'];
    }

    if ((int) $jobSeeker['employment_status'] === 1) {
        return [
            'status'  => 'error',
            'message' => "ኢንተርፕራይዙ አልተመዘገበም ምክንያቱም {$jobSeeker['full_name']} የተባሉት ስራፈላጊ ከዚህ በፊት ቋሚ የስራ እድል ተፈጥሮላቸዋል።",
        ];
    }

    return [
        'status'        => 'success',
        'job_seeker_id' => $jobSeeker['job_seeker_id'],
        'full_name'     => $jobSeeker['full_name'],
        'phone_number'  => $jobSeeker['phone_number'],
    ];
}

public function createIndividualEnterprise(array $data) {
    // ---- Confirm linked_entity_id actually belongs to this branch ----
    $validation = $this->validateJobSeekerForEnterprise($data['branch_id'], $data['linked_entity_id']);

    if ($validation['status'] === 'error') {
        return $validation;
    }

    $jobseekerId = $validation['job_seeker_id'];
    $association_name   = $validation['full_name'];
  
$sectorModel = new SectorModel($this->db);

$sectorsList = $sectorModel->getSubsectorBigIntIds($data['sub_sector']);

if (!$sectorsList) {
    return [
        'status'  => 'error',
        'message' => 'ንዑስ ዘርፍ አልተገኘም።'
    ];
}

$sectorId    = $sectorsList['sectorid'] ?? null;
$subSectorId = $sectorsList['sub_sectorid'] ?? null;

if (!$sectorId || !$subSectorId) {
    return [
        'status'  => 'error',
        'message' => 'ንዑስ ዘርፍ ወይም ዘርፍ አልተገኘም።'
    ];
}

    $this->db->beginTransaction();

    try {


            // Junction row links whichever side is populated (individual XOR team)
        $stmtIndividual = $this->db->prepare("
            INSERT INTO individual_enterprise
                (id, job_seeker_id, yeaderejajet_ayinet,
                yeminorubet_acababi, sector, sub_sector, yesra_mesk)
            VALUES
                (:id, :job_seeker_id, :yeaderejajet_ayinet, :yeminorubet_acababi,
                 :sector, :sub_sector, :yesra_mesk)
        ");
        $stmtIndividual->execute([
            ':id' =>\Ramsey\Uuid\Uuid::uuid4()->toString(),
            ':job_seeker_id' => $jobseekerId,
            ':yeaderejajet_ayinet' => $data['yeaderejajet_ayinet'],
            ':yeminorubet_acababi' => $data['yeminorubet_acababi'],
            ':sector' => $sectorId,
            ':sub_sector' => $subSectorId,
            ':yesra_mesk' => $data['yesra_mesk'],

        ]);

        $junctionTableId = $this->db->lastInsertId();

        // Junction row links whichever side is populated (individual XOR team)
        $stmt = $this->db->prepare("
            INSERT INTO junction_table_individual_and_team
                (individual_enterprise_id)
            VALUES
                (:individual_enterprise_id)
        ");
        $stmt->execute([
            ':individual_enterprise_id' => $junctionTableId,
        ]);

        $junctionTableId = $this->db->lastInsertId();

        // code003 holds the shared enterprise financials/details, keyed to the junction row
        $stmt = $this->db->prepare("
            INSERT INTO code003
                (id, branch_id, junction_table_id, enterprisename, tine_number,
                 yeedget_dereja, initial_capital, starting_capital_in_kind, yehabtu_mnch,
                 wektawi_yehabt_meten, yemrt_ayinet, yemikerb_hager_weys_lewuch,
                 supported_by, supporter_NGO, supporter_other, supported_items, established_date, fiscal_year, cregby, enterprise_type)
            VALUES
                (:enterpriseId, :branchId, :junctionTableId, :enterprisename, :tinNumber,
                 :yeedgetDereja, :initialCapital, :startingCapitalInKind, :yehabtuMnch,
                 :wektawiYehabtMeten, :yemrtAyinet, :yemikerb,
                 :supportedBy, :supporter, :supporter_other, :supportedItems, :establishedDate, :fiscalYear, :cregby, :enterpriseType)
        ");
        $stmt->execute([
            ':enterpriseId'           => $data['enterpriseId'],
            ':branchId'               => $data['branch_id'],
            ':junctionTableId'        => $junctionTableId,
            'enterprisename'          => $data['enterprise_name'],
            ':tinNumber'              => $data['tin_number'],
            ':yeedgetDereja'          => $data['yeedget_dereja'],
            ':initialCapital'         => $data['initial_capital'],
            ':startingCapitalInKind'  => !empty($data['starting_capital_in_kind']) ? $data['starting_capital_in_kind'] : null,
            ':yehabtuMnch'            => $data['yehabtu_mnch'],
            ':wektawiYehabtMeten'     => $data['wektawi_yehabt_meten'],
            ':yemrtAyinet'            => $data['yemrt_ayinet'],
            ':yemikerb'               => $data['yemikerb_hager_weys_lewuch'],
            ':supportedBy'           => !empty($org_type_suport) ? $org_type_suport : null,
            ':supporter'            => !empty($project_ID) ? $project_ID : null,
            ':supporter_other'       => !empty($supporter) ? $supporter : null,
            ':supportedItems'         => !empty($data['supported_items']) ? $data['supported_items'] : null,
            ':establishedDate'        => $data['established_date'],
            ':fiscalYear'             => $data['fiscal_year'],
            ':cregby'                 => $data['user_id'],
            ':enterpriseType'         => 'የግል'
        ]);

     


         // NOTE: this only returns a real value if code003 has its own AUTO_INCREMENT
        // column separate from the UUID `id` column inserted above.
        $code003Id = $this->db->lastInsertId();
        $stmt = $this->db->prepare("
            INSERT INTO code003sraedl
                (uuid, branchid, code003_id, jobseeker_id, sector, subsector, 
                job_creation_reason, employment_type, employed_institution, 
                supporter, suportedby, ssuportedname, what_is_support, fiscal_year, registered_by, job_field)
            VALUES
                (:id, :branchId, :code003Id, :jobseekerId, :sector, :subsector, 
                :jobCreationReason, :employmentType, :employedInstitution, :supporter, :suportedby, 
                :ssuportedname, :whatIsSupport, :fiscalYear, :registeredBy, :jobField)
        ");

            $stmt->execute([
                ':id'            => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                ':branchId'            => $data['branch_id'],
                ':code003Id'           => $code003Id,
                ':jobseekerId'         => $jobseekerId,
                ':sector'              => $sectorId,
                ':subsector'           => $subSectorId,
                ':jobCreationReason'   => 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ',   // TODO: not in $data yet
                ':employmentType'      => '1',       // TODO: not in $data yet
                ':employedInstitution' => $association_name,  // TODO: not in $data yet
                ':supporter'            => !empty($org_type_suport) ? $org_type_suport : null,
                ':suportedby'           => !empty($supporter) ? $supporter : null,
                ':ssuportedname'        => !empty($project_ID) ? $project_ID : null,
                ':whatIsSupport'         => !empty($data['supported_items']) ? $data['supported_items'] : null,
                ':fiscalYear'          => $data['fiscal_year'],
                ':registeredBy'        => $data['user_id'],
                ':jobField'             => $data['yesra_mesk'],  // TODO: not in $data yet
            ]);
        // ---- Mark these job seekers as permanently employed (employment_status = 1) ----
        $updateStmt = $this->db->prepare("
            UPDATE job_seekers
            SET employment_status = 1
            WHERE branch_id = :branchId AND job_seeker_id = :jobSeekerTableId
        ");

            $updateStmt->execute([
                ':branchId'         => $data['branch_id'],
                ':jobSeekerTableId' => $jobseekerId,
            ]);
 

              if (!empty($validation['phone_number'])) {
                 $branchname = $_SESSION['user']['branch_name'];
                 $level       = $_SESSION['user']['level'] ?? null;

                 $full_name   =  $validation['full_name'];
    if ($level == 4) {
        $levelname = " ማእከል";
    } elseif ($level == 3) {
        $levelname = " ወረዳ";
    } elseif ($level == 2) {
        $levelname = " ዞን";
    } else {
        $levelname = " ቢሮ";
    }
        $phoneNumber = '251' . ltrim(trim($validation['phone_number']), '0');
        $message = "{$full_name} ቋሚ ስራ እድል እንደተፈጠረሎት በ{$branchname} {$levelname} ሪፖርት ተደርጎልናል። "
                 . "የውሸት/ሀሰት ከሆነ {$branchname} {$levelname} ያናግሩ ወይም በ 0904354716 ያሳውቁ። ";
        \App\Helpers\SmsHelper::send($phoneNumber, $message);
    }
        $this->db->commit();

        return [
            'status' => 'success',
            'id'     => $data['enterpriseId'],
        ];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
/**
 * Confirms the team belongs to the caller's branch, and that no member of the
 * team already has employment_status = 1 (a permanent job created for them).
 */
private function validateTeamForEnterprise(int $branchId, string $teamId): array
{
    $stmt = $this->db->prepare("
        SELECT table_id, association_name, project_type, sub_sector, yesra_mesk, project_ID
        FROM group_table
        WHERE branch_id = :branchId AND id = :teamId
        LIMIT 1
    ");
    $stmt->execute([
        ':branchId' => $branchId,
        ':teamId'   => $teamId,
    ]);
    $team = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$team) {
        return ['status' => 'error', 'message' => 'የተመረጠው ቡድን ከቅርንጫፍዎ ጋር አይዛመድም።'];
    }

    // Check every member of the team; collect anyone who already has a permanent job.
    $stmt = $this->db->prepare("
        SELECT js.job_seeker_id, CONCAT(js.first_name, ' ', js.father_name, ' ', js.last_name) AS full_name, js.employment_status, 
        js.phone_number FROM organized_jobseekers oj
        JOIN job_seekers js ON js.job_seeker_id = oj.jctbl_id
        WHERE oj.team_id = :teamIdForJunction
        ORDER BY js.employment_status DESC
    ");
    $stmt->execute([':teamIdForJunction' => $team['table_id']]);
    $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $employedMembers = array_filter($members, fn($m) => (int) $m['employment_status'] === 1);

    if (!empty($employedMembers)) {
        $names = array_map(fn($m) => $m['full_name'], $employedMembers);
        $namesList = implode('፣ ', $names);

        return [
            'status'  => 'error',
            'message' => "መጀመሪያ ሁሉም አባላት ቋሚ የስራ እድል ያልተፈጠረላቸው መሆናቸውን ያረጋግጡ። {$namesList} የተባሉት አባል(ዎች) ቀድሞውኑ ቋሚ የስራ እድል ተፈጥሮላቸዋል።",
        ];
    }
return [
    'status'            => 'success',
    'table_id'          => $team['table_id'],
    'association_name'  => $team['association_name'],
    'project_type'      => $team['project_type'],
    'sub_sector'        => $team['sub_sector'],
    'yesra_mesk'        => $team['yesra_mesk'],
    'project_ID'        => $team['project_ID'],
];
}
public function createAssocationEnterprise(array $data) {
    // ---- Confirm linked_entity_id actually belongs to this branch ----
    $validation = $this->validateTeamForEnterprise($data['branch_id'], $data['linked_entity_id']);

    if ($validation['status'] === 'error') {
        return $validation;
    }

    // Team/enterprise formation: linked_entity_id is the team
    $teamIdForJunction  = $validation['table_id'];
    $association_name   = $validation['association_name'];
    $sub_sector         = $validation['sub_sector'];
    $yesra_mesk         = $validation['yesra_mesk'];
    $project_ID         = null;
    $supporter         = null;
    $org_type_suport    = null;
    $project_type       = $validation['project_type'];

    // Safely assign org_type_suport from incoming data first if available, then apply conditional logic
    $incomingOrgSupport = $data['org_type_suport'] ?? '';

    if ($project_type === 'NGO') {
        $project_ID      = $validation['project_ID'];
        $org_type_suport = 'beproject';
    } else if ($incomingOrgSupport === 'beproject') {
        $project_ID      = $data['ngo_id'] ?? null;
        $org_type_suport = 'beproject';
    } else {
        $supporter      = $data['supported_by'] ?? null;
        $org_type_suport = $incomingOrgSupport;
    }

    $sectorModel = new SectorModel($this->db);
    // Fetch sectors based on sub_sector retrieved from team validation
    $sectorsList = $sectorModel->getSectorsBySubSector($sub_sector);

    // Extract sector_id safely
    $sectorId = !empty($sectorsList) ? ($sectorsList[0]['sectorid'] ?? null) : null;

    if (empty($sectorId)) {
        return [
            'status'  => 'error',
            'message' => 'ዘርፍ አልተገኘም።'
        ];
    }

    $this->db->beginTransaction();

    try {
        // Junction row links whichever side is populated (individual XOR team)
        $stmt = $this->db->prepare("
            INSERT INTO junction_table_individual_and_team
                (team_id)
            VALUES
                (:teamId)
        ");
        $stmt->execute([
            ':teamId' => $teamIdForJunction,
        ]);

        $junctionTableId = $this->db->lastInsertId();

        // code003 holds the shared enterprise financials/details, keyed to the junction row
        $stmt = $this->db->prepare("
            INSERT INTO code003
                (id, branch_id, junction_table_id, enterprisename, tine_number,
                 yeedget_dereja, initial_capital, starting_capital_in_kind, yehabtu_mnch,
                 wektawi_yehabt_meten, yemrt_ayinet, yemikerb_hager_weys_lewuch,
                 supported_by, supporter_NGO, supporter_other, supported_items, established_date, fiscal_year, cregby)
            VALUES
                (:enterpriseId, :branchId, :junctionTableId, :enterprisename, :tinNumber,
                 :yeedgetDereja, :initialCapital, :startingCapitalInKind, :yehabtuMnch,
                 :wektawiYehabtMeten, :yemrtAyinet, :yemikerb,
                 :supportedBy, :supporter, :supporter_other, :supportedItems, :establishedDate, :fiscalYear, :cregby)
        ");
        $stmt->execute([
            ':enterpriseId'           => $data['enterpriseId'],
            ':branchId'               => $data['branch_id'],
            ':junctionTableId'        => $junctionTableId,
            'enterprisename'          => $data['enterprise_name'],
            ':tinNumber'              => $data['tin_number'],
            ':yeedgetDereja'          => $data['yeedget_dereja'],
            ':initialCapital'         => $data['initial_capital'],
            ':startingCapitalInKind'  => !empty($data['starting_capital_in_kind']) ? $data['starting_capital_in_kind'] : null,
            ':yehabtuMnch'            => $data['yehabtu_mnch'],
            ':wektawiYehabtMeten'     => $data['wektawi_yehabt_meten'],
            ':yemrtAyinet'            => $data['yemrt_ayinet'],
            ':yemikerb'               => $data['yemikerb_hager_weys_lewuch'],
            ':supportedBy'           => !empty($org_type_suport) ? $org_type_suport : null,
            ':supporter'            => !empty($project_ID) ? $project_ID : null,
            ':supporter_other'       => !empty($supporter) ? $supporter : null,
            ':supportedItems'         => !empty($data['supported_items']) ? $data['supported_items'] : null,
            ':establishedDate'        => $data['established_date'],
            ':fiscalYear'             => $data['fiscal_year'],
            ':cregby'                 => $data['user_id'],
        ]);

     


         // NOTE: this only returns a real value if code003 has its own AUTO_INCREMENT
        // column separate from the UUID `id` column inserted above.
        $code003Id = $this->db->lastInsertId();

          $stmt = $this->db->prepare("
    SELECT oj.jctbl_id, js.first_name, js.father_name, js.phone_number AS phone
    FROM organized_jobseekers oj
    JOIN job_seekers js ON js.job_seeker_id = oj.jctbl_id
    WHERE branch_id = :branchId AND oj.team_id = :teamIdForJunction
");
$stmt->execute([':branchId' => $data['branch_id'], ':teamIdForJunction' => $teamIdForJunction]);
$jobSeekerRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = $this->db->prepare("
            INSERT INTO code003sraedl
                (uuid, branchid, code003_id, jobseeker_id, sector, subsector, 
                job_creation_reason, employment_type, employed_institution, 
                supporter, suportedby, ssuportedname, what_is_support, fiscal_year, registered_by, job_field, jcsource, member)
            VALUES
                (:id, :branchId, :code003Id, :jobseekerId, :sector, :subsector, 
                :jobCreationReason, :employmentType, :employedInstitution, :supporter, :suportedby, 
                :ssuportedname, :whatIsSupport, :fiscalYear, :registeredBy, :jobField, :jcsource, :member)
        ");

      foreach ($jobSeekerRows as $row) {
    $jobSeekerTableId = $row['jctbl_id'];
    $phone = $row['phone'];
    $first_name = $row['first_name'];
    $father_name = $row['father_name'];
    $full_name = $first_name . ' ' . $father_name;

            $stmt->execute([
                ':id'            => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                ':branchId'            => $data['branch_id'],
                ':code003Id'           => $code003Id,
                ':jobseekerId'         => $jobSeekerTableId,
                ':sector'              => $sectorId,
                ':subsector'           => $sub_sector,
                ':jobCreationReason'   => 'አዳዲስ ኢንተርፕራይዞች በማቋቋም የተፈጠረ ሥራ',   // TODO: not in $data yet
                ':employmentType'      => '1',       // TODO: not in $data yet
                ':employedInstitution' => $association_name,  // TODO: not in $data yet
                ':supporter'            => !empty($org_type_suport) ? $org_type_suport : null,
                ':suportedby'           => !empty($supporter) ? $supporter : null,
                ':ssuportedname'        => !empty($project_ID) ? $project_ID : null,
                ':whatIsSupport'         => !empty($data['supported_items']) ? $data['supported_items'] : null,
                ':fiscalYear'          => $data['fiscal_year'],
                ':registeredBy'        => $data['user_id'],
                ':jobField'             => $yesra_mesk,  // TODO: not in $data yet
                ':jcsource'             => 1,
                ':member'              => 1
            ]);
               if (!empty($phone)) {
                 $branchname = $_SESSION['user']['branch_name'];
                 $level       = $_SESSION['user']['level'] ?? null;

    if ($level == 4) {
        $levelname = " ማእከል";
    } elseif ($level == 3) {
        $levelname = " ወረዳ";
    } elseif ($level == 2) {
        $levelname = " ዞን";
    } else {
        $levelname = " ቢሮ";
    }
        $phoneNumber = '251' . ltrim(trim($phone), '0');
        $message = "{$full_name} ቋሚ ስራ እድል እንደተፈጠረሎት በ{$branchname} {$levelname} ሪፖርት ተደርጎልናል። "
                 . "የውሸት/ሀሰት ከሆነ {$branchname} {$levelname} ያናግሩ ወይም በ 0904354716 ያሳውቁ። ";
        \App\Helpers\SmsHelper::send($phoneNumber, $message);
    }
        }
        // ---- Mark these job seekers as permanently employed (employment_status = 1) ----
        $updateStmt = $this->db->prepare("
            UPDATE job_seekers
            SET employment_status = 1
            WHERE branch_id = :branchId AND job_seeker_id = :jobSeekerTableId
        ");
        foreach ($jobSeekerRows as $row) {
    $jobSeekerTableId = $row['jctbl_id'];
            $updateStmt->execute([
                ':branchId'         => $data['branch_id'],
                ':jobSeekerTableId' => $jobSeekerTableId,
            ]);
        }
 $updateGroupStmt = $this->db->prepare("
    UPDATE group_table
    SET is_enterprise = 1 
    WHERE branch_id = :branchId AND id = :teamId  
");
$updateGroupStmt->execute([
    ':branchId' => $data['branch_id'],
    ':teamId'   => $data['linked_entity_id'],
]);
        $this->db->commit();

        return [
            'status' => 'success',
            'id'     => $data['enterpriseId'],
        ];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}


/**
 * 1. Enterprises registered in code003 within the last 24 hours, for a branch.
 */
public function getEnterprisesRegisteredLast24Hours(int $branch_id): array
{
    $stmt = $this->db->prepare("
        SELECT
            id,
            branch_id,
            established_date,
            fiscal_year,
            tine_number,
            enterprise_type,
            enterprisename
        FROM code003
        WHERE branch_id = :branchId
          AND cregdate >= (NOW() - INTERVAL 24 HOUR)
        ORDER BY cregdate DESC
    ");

    $stmt->bindValue(':branchId', $branch_id, \PDO::PARAM_STR);
    $stmt->execute();

    $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return [
        'data'  => $data,
        'count' => count($data),
    ];
}
/**
 * 2. All enterprises for a branch, paginated.
 */
public function getEnterprisesByHierarchy(int $myBranchId, int $limit, int $offset): array
{
    $sql = "SELECT c.id, c.tine_number, c.enterprisename, c.enterprise_type,
                   c.established_date, c.fiscal_year, c.cregdate,
                   c.branch_id,
                   b.name AS branch_name, b.level AS branch_level,
                   anc.internal_id AS display_branch_id,
                   anc.name AS display_branch_name,
                   anc.level AS display_branch_level
            FROM code003 c
            INNER JOIN branches b ON c.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            LEFT JOIN branches anc
                   ON anc.level = root.level + 1
                  AND anc.path LIKE CONCAT(root.path, '%')
                  AND b.path LIKE CONCAT(anc.path, '%')
            WHERE b.path LIKE CONCAT(root.path, '%')
            ORDER BY c.cregdate DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("Get enterprises by hierarchy error: " . $e->getMessage());
        return [];
    }
}
public function getEnterprisesCountByHierarchy(int $myBranchId): int
{
    $sql = "SELECT COUNT(*) AS total
            FROM code003 c
            INNER JOIN branches b ON c.branch_id = b.internal_id
            INNER JOIN branches root ON root.internal_id = :my_branch
            WHERE b.path LIKE CONCAT(root.path, '%')";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':my_branch', $myBranchId);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    } catch (\PDOException $e) {
        error_log("Get enterprises count by hierarchy error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Archives an enterprise (individual or association) into data_archive,
 * resets employment_status for all linked job seekers, then deletes
 * the enterprise's live records.
 */
public function getBranchIdsInHierarchy(int $rootBranchId): array
{
    $stmt = $this->db->prepare("
        SELECT b.internal_id
        FROM branches b
        INNER JOIN branches root ON root.internal_id = :rootBranchId
        WHERE b.path LIKE CONCAT(root.path, '%')
    ");
    $stmt->execute([':rootBranchId' => $rootBranchId]);
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
}

public function getEnterpriseDetails(int $branchId, string $enterpriseId, array $branchIds): ?array
{
    if (empty($branchIds)) {
        return null;
    }

    // Named placeholders for the hierarchy IN(...) clause — avoids mixing
    // named + positional params in the same execute() call
    $inParams = [];
    $inNames  = [];
    foreach ($branchIds as $i => $id) {
        $name = ':b' . $i;
        $inNames[] = $name;
        $inParams[$name] = $id;
    }
    $inClause = implode(',', $inNames);

    $stmt = $this->db->prepare("
        SELECT c.id, c.code003_id, c.branch_id, c.junction_table_id,
               c.enterprisename, c.tine_number, c.yeedget_dereja, c.initial_capital,
               c.starting_capital_in_kind, c.yehabtu_mnch, c.wektawi_yehabt_meten,
               c.yemrt_ayinet, c.yemikerb_hager_weys_lewuch, c.supported_by,
               c.supporter_NGO, c.supporter_other, c.supported_items, c.established_date,
               jt.team_id, jt.individual_enterprise_id
        FROM code003 c
        INNER JOIN junction_table_individual_and_team jt ON jt.table_id = c.junction_table_id
        WHERE c.branch_id IN ($inClause) AND c.id = :enterpriseId
        LIMIT 1
    ");
    $stmt->execute($inParams + [':enterpriseId' => $enterpriseId]);
    $enterprise = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$enterprise) {
        return null;
    }

    // From here on, ALWAYS use $enterprise['branch_id'] (the enterprise's own
    // branch), never the original $branchId param — that's the viewer's branch,
    // which may be a parent of where this enterprise actually lives.
    $enterpriseBranchId = (int)$enterprise['branch_id'];

    $isAssociation = !empty($enterprise['team_id']);
    $enterprise['type'] = $isAssociation ? 'association' : 'individual';

    if ($isAssociation) {
        $stmt = $this->db->prepare("
            SELECT g.id , g.table_id, g.branch_id, g.yetederajubet_akababi, g.association_name,
                   g.sub_sector, g.yesra_mesk, g.project_type AS yeaderejajet_ayinet, g.user_level, g.teamleader_id,
                   g.manager_phone, g.vice_teamleader_id, g.treasurer, g.procurement,
                   g.org_type, g.overseastatus, g.registered_by,
                   s.sectorid as sector_id,
                   s.sector AS sector_name,
                   ss.subsector AS subsector_name,
                   ss.sub_sectorid
            FROM group_table g
            LEFT JOIN sub_sector ss ON ss.sub_sectorid = g.sub_sector
            LEFT JOIN sector_table s ON s.sectorid = ss.sectorid
            WHERE g.branch_id = :branchId AND g.table_id = :teamId
        ");
        $stmt->execute([
            ':branchId' => $enterpriseBranchId,
            ':teamId'   => $enterprise['team_id'],
        ]);
        $enterprise['linked_entity'] = $stmt->fetch(\PDO::FETCH_ASSOC);
    } else {
        $stmt = $this->db->prepare("
            SELECT ie.id, ie.individual_ent_id, ie.job_seeker_id, ie.yeaderejajet_ayinet,
                   ie.yeminorubet_acababi AS yetederajubet_akababi, ie.sector AS sector_id, ie.sub_sector AS sub_sectorid,
                   s.sector AS sector_name, ss.subsector AS subsector_name, ie.yesra_mesk
            FROM individual_enterprise ie
            LEFT JOIN sector_table s ON s.sectorid = ie.sector
            LEFT JOIN sub_sector ss ON ss.sub_sectorid = ie.sub_sector
            WHERE ie.individual_ent_id = :id
        ");
        $stmt->execute([':id' => $enterprise['individual_enterprise_id']]);
        $enterprise['linked_entity'] = $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Members — scoped to the enterprise's own branch, not the viewer's
    $stmt = $this->db->prepare("
        SELECT sr.member, js.branch_id, js.job_seeker_id, js.first_name, js.father_name, js.last_name, js.phone_number, js.gender
        FROM code003sraedl sr
        INNER JOIN job_seekers js ON js.job_seeker_id = sr.jobseeker_id
        WHERE sr.branchid = :branchId AND sr.code003_id = :code003Id
        ORDER BY sr.member DESC
    ");
    $stmt->execute([':branchId' => $enterpriseBranchId, ':code003Id' => $enterprise['code003_id']]);
    $enterprise['members'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return $enterprise;
}

public function getEnterpriseBranchPath(int $myBranchId, int $enterpriseBranchId): ?string
{
    $stmt = $this->db->prepare("
        SELECT b.path AS branch_path, root.path AS root_path
        FROM branches b
        INNER JOIN branches root ON root.internal_id = :myBranchId
        WHERE b.internal_id = :enterpriseBranchId
          AND b.path LIKE CONCAT(root.path, '%')
        LIMIT 1
    ");
    $stmt->execute([
        ':myBranchId'         => $myBranchId,
        ':enterpriseBranchId' => $enterpriseBranchId,
    ]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$row) {
        return null;
    }

    return \App\Helpers\BranchPathHelper::buildDisplayPath(
        $this->db,
        $row['branch_path'],
        $row['root_path']
    );
}

public function purge(int $branchId, int $userId, string $enterpriseId, string $reason): array
{
    // ---- Resolve the enterprise and its type via the junction row ----
    $stmt = $this->db->prepare("
        SELECT c.code003_id AS code003_internal_id, c.junction_table_id,
               jt.team_id, jt.individual_enterprise_id
        FROM code003 c
        INNER JOIN junction_table_individual_and_team jt ON jt.table_id = c.junction_table_id
        WHERE c.branch_id = :branchId AND c.id = :enterpriseId 
        LIMIT 1
    ");
    $stmt->execute([
        ':branchId'     => $branchId,
        ':enterpriseId' => $enterpriseId,
    ]);
    $enterprise = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$enterprise) {
        return ['status' => 'error', 'message' => 'ኢንተርፕራይዙ አልተገኘም ወይም አስቀድሞ ተሰርዟል።'];
    }

    $isAssociation = !empty($enterprise['team_id']);
    $code003InternalId = $enterprise['code003_internal_id'];

    // ---- Build the full snapshot before touching anything ----
    $snapshot = ['code003' => null, 'code003sraedl' => [], 'linked_entity' => null];

    $stmt = $this->db->prepare("SELECT * FROM code003 WHERE branch_id = :branchId AND id = :id");
    $stmt->execute([':branchId' => $branchId, ':id' => $enterpriseId]);
    $snapshot['code003'] = $stmt->fetch(\PDO::FETCH_ASSOC);

    $stmt = $this->db->prepare("SELECT * FROM code003sraedl WHERE branchid = :branchId AND code003_id = :code003Id");
    $stmt->execute([':branchId' => $branchId, ':code003Id' => $code003InternalId]);
    $sraedlRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    $snapshot['code003sraedl'] = $sraedlRows;

    $jobSeekerIds = array_column($sraedlRows, 'jobseeker_id');

    if ($isAssociation) {
        $stmt = $this->db->prepare("SELECT * FROM group_table WHERE table_id = :teamId");
        $stmt->execute([':teamId' => $enterprise['team_id']]);
        $snapshot['linked_entity'] = $stmt->fetch(\PDO::FETCH_ASSOC);
        $snapshot['type'] = 'association';
    } else {
        $stmt = $this->db->prepare("SELECT * FROM individual_enterprise WHERE individual_ent_id = :id");
        $stmt->execute([':id' => $enterprise['individual_enterprise_id']]);
        $snapshot['linked_entity'] = $stmt->fetch(\PDO::FETCH_ASSOC);
        $snapshot['type'] = 'individual';
    }

    $this->db->beginTransaction();

    try {
        // ---- 1. Archive ----
        $archiveStmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_by, reason)
            VALUES
                (:id, :entityType, :originalId, :snapshot, :archivedBy, :reason)
        ");
        $archiveStmt->execute([
            ':id'         => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            ':entityType' => 'code003',
            ':originalId' => $enterpriseId,
            ':snapshot'   => json_encode($snapshot, JSON_UNESCAPED_UNICODE),
            ':archivedBy' => $userId,
            ':reason'     => $reason,
        ]);

        // ---- 2. Reset employment_status for all linked job seekers ----
        if (!empty($jobSeekerIds)) {
            $placeholders = implode(',', array_fill(0, count($jobSeekerIds), '?'));
            $resetStmt = $this->db->prepare("
                UPDATE job_seekers
                SET employment_status = 0
                WHERE branch_id = ? AND job_seeker_id IN ($placeholders)
            ");
            $resetStmt->execute([$branchId, ...$jobSeekerIds]);
        }

        // ---- 3. Delete live records (children first) ----
        $this->db->prepare("DELETE FROM code003sraedl WHERE branchid = :branchId AND code003_id = :code003Id")
            ->execute([':branchId' => $branchId, ':code003Id' => $code003InternalId]);

        $this->db->prepare("DELETE FROM code003 WHERE branch_id = :branchId AND id = :id")
            ->execute([':branchId' => $branchId, ':id' => $enterpriseId]);

        $this->db->prepare("DELETE FROM junction_table_individual_and_team WHERE table_id = :id")
            ->execute([':id' => $enterprise['junction_table_id']]);

        if ($isAssociation) {
            $this->db->prepare("
                UPDATE group_table SET is_enterprise = 0
                WHERE branch_id = :branchId AND table_id = :teamId
            ")->execute([
                ':branchId' => $branchId,
                ':teamId'   => $enterprise['team_id'],
            ]);
        } else {
            $this->db->prepare("DELETE FROM individual_enterprise WHERE individual_ent_id = :id")
                ->execute([':id' => $enterprise['individual_enterprise_id']]);
        }


        $this->db->commit();

        return ['status' => 'success', 'id' => $enterpriseId];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}