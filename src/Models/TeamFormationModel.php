<?php
namespace App\Models;
use PDO;
class TeamFormationModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
 public function createTeamFormation(array $payload)
{
    try {
        // 1. Begin Transaction
        $this->db->beginTransaction();

        // 2. Insert into group_table
        $sqlGroup = "INSERT INTO group_table (id, branch_id, association_name, 
        sub_sector, yesra_mesk, project_type, user_level, teamleader_id, manager_phone,
        vice_teamleader_id, treasurer, procurement, registered_by, project_ID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtGroup = $this->db->prepare($sqlGroup);
        $stmtGroup->execute([
            $payload['team_id'],       
            $payload['branch_id'],    
            $payload['asso_name'],     
            $payload['sub_sector'],
            $payload['yesra_mesk'],    
            $payload['org_type'],      
            $payload['level'],    
            $payload['leader_id'],     
            $payload['manager_phone'], 
            $payload['co_id'],         
            $payload['finance_id'],    
            $payload['procurement_id'],
            $payload['registered_by'],  
            $payload['ngo_id'],      
        ]);

        $lastId = $this->db->lastInsertId();
        if($lastId){
            // 3. Insert into organized_jobseekers
            $sqlMember = "INSERT INTO organized_jobseekers (id, jctbl_id, team_id) VALUES (?, ?, ?)";
            $stmtMember = $this->db->prepare($sqlMember);

            // Prepare Update statement if condition is met
            $stmtUpdate = null;
            if ($payload['org_type'] === 'NGO') {
                $sqlUpdateStatus = "UPDATE job_seekers SET employment_status = 4 WHERE job_seeker_id = ?";
                $stmtUpdate = $this->db->prepare($sqlUpdateStatus);
            }

            foreach ($payload['member_ids'] as $jobSeekerId) {
                // Insert into organized_jobseekers
                $stmtMember->execute([
                    \Ramsey\Uuid\Uuid::uuid7()->toString(), 
                    $jobSeekerId,              
                    $lastId        
                ]);

                // Update job_seekers status ONLY if org_type is NGO
                if ($stmtUpdate !== null) {
                    $stmtUpdate->execute([$jobSeekerId]);
                }
            }
        }
        
        // 4. Commit Transaction
        $this->db->commit();

        return [
            'status' => 'success',
            'team_formation_id' => $payload['team_id']
        ];

    } catch (\Exception $e) {
        // Rollback on any error
        $this->db->rollBack();
        return [
            'status' => 'error',
            'message' => 'የውሂብ ማስገባት ስህተት: ' . $e->getMessage()
        ];
    }
}

public function getGroupsByBranch(int $branchId, int $page = 1, int $perPage = 10): array
{
    // 1. Calculate offset
    $offset = ($page - 1) * $perPage;

    // 2. Fetch total records for this specific branch
    $sqlCount = "SELECT COUNT(*) FROM group_table WHERE branch_id = :branch_id";
    $stmtCount = $this->db->prepare($sqlCount);
    $stmtCount->bindValue(':branch_id', $branchId, PDO::PARAM_INT);
    $stmtCount->execute();
    $totalCount = (int)$stmtCount->fetchColumn();

    // 3. Fetch the data for this specific branch
    $sqlData = "SELECT id, table_id, branch_id, association_name, sub_sector,project_type,  yesra_mesk FROM group_table 
                WHERE branch_id = :branch_id 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
    
    $stmt = $this->db->prepare($sqlData);
    
    $stmt->bindValue(':branch_id', $branchId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        'total' => $totalCount,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => ceil($totalCount / $perPage)
    ];
}
public function getTeamWithMembers(string $teamId): ?array
{
    // 1. Resolve the team row by its (UUID) id, pulling in:
    //    - sector/sub-sector names via sub_sector -> sector_table
    //    - team leader / vice leader / treasurer / procurement NAMES via
    //      job_seekers, since these columns store job_seeker_id values,
    //      not raw display data
    //    - table_id for the organized_jobseekers join below
    $sqlTeam = "SELECT g.id, g.table_id, g.branch_id, g.association_name,
                       g.sub_sector, g.yesra_mesk, g.project_type, g.yesra_mesk,
                       g.teamleader_id, g.manager_phone, g.vice_teamleader_id,
                       g.treasurer, g.procurement, g.registered_by, g.project_ID,
                       g.created_at,
                       ss.subsector,
                       st.sector,
                       CONCAT_WS(' ', leader.first_name, leader.father_name, leader.last_name) AS teamleader_name,
                       CONCAT_WS(' ', vleader.first_name, vleader.father_name, vleader.last_name) AS vice_teamleader_name,
                       CONCAT_WS(' ', treas.first_name, treas.father_name, treas.last_name) AS treasurer_name,
                       CONCAT_WS(' ', proc.first_name, proc.father_name, proc.last_name) AS procurement_name
                FROM group_table g
                LEFT JOIN sub_sector ss ON ss.sub_sectorid = g.sub_sector
                LEFT JOIN sector_table st ON st.sectorid = ss.sectorid
                LEFT JOIN job_seekers leader  ON leader.job_seeker_id  = g.teamleader_id
                LEFT JOIN job_seekers vleader ON vleader.job_seeker_id = g.vice_teamleader_id
                LEFT JOIN job_seekers treas   ON treas.job_seeker_id   = g.treasurer
                LEFT JOIN job_seekers proc    ON proc.job_seeker_id    = g.procurement
                WHERE g.id = :id
                LIMIT 1";

    $stmtTeam = $this->db->prepare($sqlTeam);
    $stmtTeam->bindValue(':id', $teamId);
    $stmtTeam->execute();

    $team = $stmtTeam->fetch(PDO::FETCH_ASSOC);
    if (!$team) {
        return null;
    }

    $internalTeamId = (int) $team['table_id'];

    // 2. Fetch members via organized_jobseekers -> job_seekers
    $sqlMembers = "SELECT js.id, js.job_seeker_id, js.first_name, js.father_name,
                          js.last_name, js.gender, js.phone_number
                   FROM organized_jobseekers oj
                   INNER JOIN job_seekers js ON js.job_seeker_id = oj.jctbl_id
                   WHERE oj.team_id = :team_id
                   ORDER BY js.first_name";

    $stmtMembers = $this->db->prepare($sqlMembers);
    $stmtMembers->bindValue(':team_id', $internalTeamId, PDO::PARAM_INT);
    $stmtMembers->execute();

    $team['members'] = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);

    return $team;
}

/**
 * Add a single job seeker to an existing team.
 *
 * @param int    $internalTeamId  The team's int table_id (organized_jobseekers.team_id target),
 *                                NOT the UUID g.id used in URLs.
 * @param string $jobSeekerId     job_seekers.job_seeker_id of the person to add.
 */
public function addMember(int $branchId, string $teamUUID, string $jobSeekerId): array
{
    try {
        // 1. Confirm the team exists and get its project_type, so we know
        //    whether to mirror the NGO employment_status update from
        //    createTeamFormation().
        $sqlTeam = "SELECT table_id, project_type FROM group_table WHERE branch_id = :branch_id AND id = :team_id LIMIT 1";
        $stmtTeam = $this->db->prepare($sqlTeam);
        $stmtTeam->bindValue(':branch_id', $branchId, PDO::PARAM_INT);
        $stmtTeam->bindValue(':team_id', $teamUUID, PDO::PARAM_STR);
        $stmtTeam->execute();
        $teamRow = $stmtTeam->fetch(PDO::FETCH_ASSOC);

        if (!$teamRow) {
            return ['status' => 'error', 'message' => 'ቡድኑ አልተገኘም'];
        }

        $isNgo = ($teamRow['project_type'] === 'NGO');
        $internalTeamId = (int) $teamRow['table_id'];

        // 2. Confirm the job seeker exists (also gives us data to return for the UI)
        $sqlJs = "SELECT id, job_seeker_id, first_name, father_name, last_name, gender, phone_number
                  FROM job_seekers WHERE id = :id LIMIT 1";
        $stmtJs = $this->db->prepare($sqlJs);
        $stmtJs->bindValue(':id', $jobSeekerId);
        $stmtJs->execute();
        $jobSeeker = $stmtJs->fetch(PDO::FETCH_ASSOC);


        if (!$jobSeeker) {
            return ['status' => 'error', 'message' => 'የስራ ፈላጊው አልተገኘም'];
        }

        // 3. Duplicate guard — don't add the same person to the same team twice
        $sqlDup = "SELECT COUNT(*) FROM organized_jobseekers WHERE team_id = :team_id AND jctbl_id = :jctbl_id";
        $stmtDup = $this->db->prepare($sqlDup);
        $stmtDup->bindValue(':team_id', $internalTeamId, PDO::PARAM_INT);
        $stmtDup->bindValue(':jctbl_id', $jobSeeker['job_seeker_id'], PDO::PARAM_INT);
        $stmtDup->execute();

        if ((int) $stmtDup->fetchColumn() > 0) {
            return ['status' => 'error', 'message' => 'ይህ የስራ ፈላጊ አስቀድሞ የቡድኑ አባል ነው'];
        }

        $this->db->beginTransaction();

        $sqlInsert = "INSERT INTO organized_jobseekers (id, jctbl_id, team_id) VALUES (?, ?, ?)";
        $stmtInsert = $this->db->prepare($sqlInsert);
        $stmtInsert->execute([
            \Ramsey\Uuid\Uuid::uuid7()->toString(),
            $jobSeeker['job_seeker_id'],
            $internalTeamId,
        ]);

        // Mirror the same NGO status update createTeamFormation() applies
        // to members added at creation time.
        if ($isNgo) {
            $sqlStatus = "UPDATE job_seekers SET employment_status = 4 WHERE job_seeker_id = ?";
            $stmtStatus = $this->db->prepare($sqlStatus);
            $stmtStatus->execute([$jobSeekerId]);
        }

        $this->db->commit();

        return [
            'status' => 'success',
            'member' => $jobSeeker,
        ];
    } catch (\Exception $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        return ['status' => 'error', 'message' => 'የውሂብ ማስገባት ስህተት: ' . $e->getMessage()];
    }
}
}