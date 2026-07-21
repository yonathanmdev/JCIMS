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
        $sqlGroup = "INSERT INTO group_table (id, branch_id, yetederajubet_akababi, association_name, 
        sub_sector, yesra_mesk, project_type, user_level, teamleader_id, manager_phone,
        vice_teamleader_id, treasurer, procurement, registered_by, project_ID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtGroup = $this->db->prepare($sqlGroup);
        $stmtGroup->execute([
            $payload['team_id'],       
            $payload['branch_id'], 
            $payload['place'],   
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
    $sqlTeam = "SELECT g.id, g.table_id, g.branch_id, g.yetederajubet_akababi, g.association_name,
                       g.sub_sector, g.yesra_mesk, g.project_type, g.yesra_mesk,
                       g.teamleader_id, g.manager_phone, g.vice_teamleader_id,
                       g.treasurer, g.procurement, g.registered_by, g.project_ID,
                       g.created_at,
                       ss.subsector,
                       st.sector,
                       CONCAT_WS(' ', leader.first_name, leader.father_name, leader.last_name) AS teamleader_name,
                       CONCAT_WS(' ', vleader.first_name, vleader.father_name, vleader.last_name) AS vice_teamleader_name,
                       CONCAT_WS(' ', treas.first_name, treas.father_name, treas.last_name) AS treasurer_name,
                       CONCAT_WS(' ', proc.first_name, proc.father_name, proc.last_name) AS procurement_name, 
                       pn.pname AS project_name 
                FROM group_table g
                LEFT JOIN sub_sector ss ON ss.sub_sectorid = g.sub_sector
                LEFT JOIN sector_table st ON st.sectorid = ss.sectorid
                LEFT JOIN job_seekers leader  ON leader.job_seeker_id  = g.teamleader_id
                LEFT JOIN job_seekers vleader ON vleader.job_seeker_id = g.vice_teamleader_id
                LEFT JOIN job_seekers treas   ON treas.job_seeker_id   = g.treasurer
                LEFT JOIN job_seekers proc    ON proc.job_seeker_id    = g.procurement
                LEFT JOIN projectngos pn      ON pn.pid                = g.project_ID
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

public function getTeamForEdit(string $teamUuid): ?array
{
    // 1. Team row + sector/subsector (uuid + int) + leader/vice/treasurer/procurement names + NGO
    $sql = "SELECT 
                g.id             AS uuid,
                g.table_id,
                g.association_name,
                g.yetederajubet_akababi,
                g.manager_phone,
                g.yesra_mesk,
                g.project_type,

                -- sector: uuid for submit, int id for reference
                st.id            AS sector_uuid,
                st.sectorid      AS sector_int_id,
                st.sector        AS sector_name,

                -- subsector: uuid for submit, int id for reference
                ss.id            AS subsector_uuid,
                ss.sub_sectorid  AS subsector_int_id,
                ss.subsector     AS subsector_name,

                -- leader ids stored as job_seeker_id (int)
                g.teamleader_id,
                g.vice_teamleader_id,
                g.treasurer,
                g.procurement,

                -- NGO: only populated when project_type = 'NGO'
                g.project_ID     AS ngo_id,
                pn.pname         AS ngo_name,

                CONCAT_WS(' ', leader.first_name,  leader.father_name,  leader.last_name) AS teamleader_name,
                CONCAT_WS(' ', vleader.first_name, vleader.father_name, vleader.last_name) AS vice_teamleader_name,
                CONCAT_WS(' ', treas.first_name,   treas.father_name,   treas.last_name)   AS treasurer_name,
                CONCAT_WS(' ', proc.first_name,    proc.father_name,    proc.last_name)    AS procurement_name

            FROM group_table g
            LEFT JOIN sub_sector ss    ON ss.sub_sectorid = g.sub_sector
            LEFT JOIN sector_table st  ON st.sectorid     = ss.sectorid
            LEFT JOIN job_seekers leader  ON leader.job_seeker_id  = g.teamleader_id
            LEFT JOIN job_seekers vleader ON vleader.job_seeker_id = g.vice_teamleader_id
            LEFT JOIN job_seekers treas   ON treas.job_seeker_id   = g.treasurer
            LEFT JOIN job_seekers proc    ON proc.job_seeker_id    = g.procurement
            LEFT JOIN projectngos pn      ON pn.pid                = g.project_ID
            WHERE g.id = :uuid
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':uuid', $teamUuid);
    $stmt->execute();

    $team = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$team) {
        return null;
    }

    // 2. Members via organized_jobseekers -> job_seekers (uses internal int table_id)
    $internalTeamId = (int) $team['table_id'];

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

public function getTeamMembersWithProjectType(string $teamUuid): array
{
    try {
        $sql = "SELECT js.job_seeker_id, g.project_type
                FROM group_table g
                INNER JOIN organized_jobseekers oj ON oj.team_id = g.table_id
                INNER JOIN job_seekers js ON js.job_seeker_id = oj.jctbl_id
                WHERE g.id = :team_uuid";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':team_uuid', $teamUuid);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'member_ids'   => array_column($rows, 'job_seeker_id'),
            'project_type' => $rows[0]['project_type'] ?? null,
        ];

    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return ['member_ids' => [], 'project_type' => null];
    }
}

public function updateTeamFormation(string $teamUuid, array $payload): array
{
    try {
        $sql = "UPDATE group_table
                SET association_name      = :association_name,
                    yetederajubet_akababi = :yetederajubet_akababi,
                    manager_phone         = :manager_phone,
                    sub_sector            = :sub_sector,
                    yesra_mesk            = :yesra_mesk,
                    teamleader_id         = :teamleader_id,
                    vice_teamleader_id    = :vice_teamleader_id,
                    treasurer             = :treasurer,
                    procurement           = :procurement,
                    project_ID            = :project_ID
                WHERE branch_id = :branch_id AND id = :team_uuid
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':association_name', $payload['association_name']);
        $stmt->bindValue(':yetederajubet_akababi', $payload['yetederajubet_akababi']);
        $stmt->bindValue(':manager_phone', $payload['manager_phone']);
        $stmt->bindValue(':sub_sector', $payload['sub_sector_int_id'], PDO::PARAM_INT);
        $stmt->bindValue(':yesra_mesk', $payload['yesra_mesk']);
        $stmt->bindValue(':teamleader_id', $payload['teamleader_id'], PDO::PARAM_INT);
        $stmt->bindValue(':vice_teamleader_id', $payload['vice_teamleader_id'], PDO::PARAM_INT);
        $stmt->bindValue(':treasurer', $payload['treasurer'], PDO::PARAM_INT);
        $stmt->bindValue(':procurement', $payload['procurement'], PDO::PARAM_INT);
        $stmt->bindValue(':project_ID', $payload['ngo_id']);
         $stmt->bindValue(':branch_id', $payload['branch_id'], PDO::PARAM_INT);
        $stmt->bindValue(':team_uuid', $teamUuid);

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $check = $this->db->prepare("SELECT id FROM group_table WHERE id = :id LIMIT 1");
            $check->bindValue(':id', $teamUuid);
            $check->execute();
            if (!$check->fetch()) {
                return ['status' => 'error', 'message' => 'ቡድኑ አልተገኘም።'];
            }
        }

        return ['status' => 'success'];

    } catch (\PDOException $e) {
        error_log(__METHOD__ . ': ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'የመረጃ ጎዳ ችግር ተከስቷል።'];
    }
}
public function purge(int $branchId, string $userId, string $teamId, string $reason): array
{
    $this->db->beginTransaction();

    try {
        // 1. Resolve the UUID, scoped to the caller's branch
        $stmt = $this->db->prepare("
            SELECT id, table_id, project_type
            FROM group_table
            WHERE branch_id = :branchId AND id = :id 
            LIMIT 1
        ");
        $stmt->execute([':branchId' => $branchId, ':id' => $teamId]);
        $group = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$group) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ቡድኑ አልተገኘም።'];
        }

        $groupTableId = $group['table_id'];
        $projectType  = $group['project_type'];

        // 2. Archive group_table row
        $stmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_by, reason)
            SELECT
                :archiveId1, 'group_table', gt.id,
                JSON_OBJECT(
                    'id', gt.id, 'table_id', gt.table_id, 'branch_id', gt.branch_id,
                    'yetederajubet_akababi', gt.yetederajubet_akababi,
                    'association_name', gt.association_name, 'sub_sector', gt.sub_sector,
                    'yesra_mesk', gt.yesra_mesk, 'project_type', gt.project_type,
                    'user_level', gt.user_level, 'teamleader_id', gt.teamleader_id,
                    'manager_phone', gt.manager_phone, 'vice_teamleader_id', gt.vice_teamleader_id,
                    'treasurer', gt.treasurer, 'procurement', gt.procurement,
                    'org_type', gt.org_type, 'overseastatus', gt.overseastatus,
                    'registered_by', gt.registered_by, 'created_at', gt.created_at,
                    'updated_by', gt.updated_by, 'updated_at', gt.updated_at,
                    'project_ID', gt.project_ID
                ), :archived_by1, :reason1
            FROM group_table gt
            WHERE gt.id = :teamId
        ");
        $stmt->execute([
            ':archiveId1'   => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            ':archived_by1' => $userId,
            ':reason1'      => $reason,
            ':teamId'       => $teamId,
        ]);

        // 3. Archive organized_jobseekers rows (team_id is bigint, matches group_table.table_id)
        $stmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_by, reason)
            SELECT
                UUID(), 'organized_jobseekers', oj.id,
                JSON_OBJECT(
                    'id', oj.id, 'table_id', oj.table_id,
                    'jctbl_id', oj.jctbl_id, 'team_id', oj.team_id
                ), :archived_by, :reason
            FROM organized_jobseekers oj
            WHERE oj.team_id = :groupTableId
        ");
        $stmt->execute([
            ':archived_by'  => $userId,
            ':reason'       => $reason,
            ':groupTableId' => $groupTableId,
        ]);

        // 4. Update employment_status on linked job seekers (NGO teams only)
        //    TODO: confirm which oj column actually references job_seekers.id —
        //    if job_seekers.id is UUID, oj.table_id (bigint) is the wrong FK here.
        $jobSeekersCount = 0;
        if ($projectType === 'NGO') {
            $stmt = $this->db->prepare("
                UPDATE job_seekers js
                JOIN organized_jobseekers oj ON oj.jctbl_id = js.job_seeker_id
                SET js.employment_status = 0
                WHERE oj.team_id = :groupTableId
            ");
            $stmt->execute([':groupTableId' => $groupTableId]);
            $jobSeekersCount = $stmt->rowCount();
        }

        // 5. Delete organized_jobseekers rows (already archived)
        $stmt = $this->db->prepare("
            DELETE FROM organized_jobseekers
            WHERE team_id = :groupTableId
        ");
        $stmt->execute([':groupTableId' => $groupTableId]);

        // 6. Delete the group_table row itself (already archived)
        $stmt = $this->db->prepare("
            DELETE FROM group_table
            WHERE id = :teamId
        ");
        $stmt->execute([':teamId' => $teamId]);

        $this->db->commit();

        return [
            'status'    => 'success',
            'userCount' => $jobSeekersCount,
        ];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

public function purgeMember(int $branchId, string $userId, string $jobseekerId, string $reason): array
{
    $this->db->beginTransaction();

    try {
        // 1. Resolve the job seeker, scoped to the caller's branch
        $stmt = $this->db->prepare("
            SELECT id, job_seeker_id
            FROM job_seekers
            WHERE branch_id = :branchId AND id = :jobseekerId 
            LIMIT 1
        ");
        $stmt->execute([':branchId' => $branchId, ':jobseekerId' => $jobseekerId]);
        $member = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$member) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ስራ ፈላጊው አልተገኘም።'];
        }

        $jobSeekerId = $member['job_seeker_id'];

        // 2. Confirm the member belongs to a team, and block deletion if they
        //    currently hold a leadership role (must be reassigned first).
        $stmt = $this->db->prepare("
            SELECT gt.id, gt.teamleader_id, gt.vice_teamleader_id, gt.treasurer, gt.procurement
            FROM organized_jobseekers oj
            JOIN group_table gt ON gt.table_id = oj.team_id
            WHERE oj.jctbl_id = :jobSeekerId
            LIMIT 1
        ");
        $stmt->execute([':jobSeekerId' => $jobSeekerId]);
        $teamRow = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$teamRow) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ስራ ፈላጊው በቡድን ውስጥ አልተገኘም።'];
        }

        $isLeader = in_array($jobSeekerId, [
            $teamRow['teamleader_id'],
            $teamRow['vice_teamleader_id'],
            $teamRow['treasurer'],
            $teamRow['procurement'],
        ], true);

        if ($isLeader) {
            $this->db->rollBack();
            return [
                'status'  => 'error',
                'message' => 'ይህ አባል በቡድኑ ውስጥ የመሪነት ሚና ስላለው መጀመሪያ ሚናውን ይቀይሩ ከዚያ ያጥፉ።',
            ];
        }

        // 3. Archive organized_jobseekers row
        $stmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_by, reason)
            SELECT
                :archiveId1, 'organized_jobseekers', oj.id,
                JSON_OBJECT(
                    'id', oj.id, 'table_id', oj.table_id,
                    'jctbl_id', oj.jctbl_id, 'team_id', oj.team_id
                ), :archived_by, :reason
            FROM organized_jobseekers oj
            WHERE oj.jctbl_id = :jobSeekerId
        ");
        $stmt->execute([
            ':archiveId1'  => \Ramsey\Uuid\Uuid::uuid7()->toString(),
            ':archived_by' => $userId,
            ':reason'      => $reason,
            ':jobSeekerId' => $jobSeekerId,
        ]);

        // 4. Update employment_status on the linked job seeker (NGO teams only)
            $stmt = $this->db->prepare("
                UPDATE job_seekers js
                SET js.employment_status = 0
                WHERE js.branch_id = :branchId AND js.job_seeker_id = :jobSeekerId AND js.employment_status = 4
            ");
            $stmt->execute([':branchId' => $branchId, ':jobSeekerId' => $jobSeekerId]);
            $jobSeekersCount = $stmt->rowCount();
        

        // 5. Delete organized_jobseekers row (already archived)
        $stmt = $this->db->prepare("
            DELETE FROM organized_jobseekers
            WHERE jctbl_id = :jobSeekerId
        ");
        $stmt->execute([':jobSeekerId' => $jobSeekerId]);

        $this->db->commit();

        return [
            'status'    => 'success',
            'jobSeekersCount' => $jobSeekersCount,
        ];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}