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
        sub_sector, project_type, user_level, teamleader_id, manager_phone,
        vice_teamleader_id, treasurer, procurement, registered_by, project_ID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtGroup = $this->db->prepare($sqlGroup);
        $stmtGroup->execute([
            $payload['team_id'],       
            $payload['branch_id'],    
            $payload['asso_name'],     
            $payload['sub_sector'],    
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
    $sqlData = "SELECT * FROM group_table 
                WHERE branch_id = :branch_id 
                ORDER BY association_name ASC 
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
}