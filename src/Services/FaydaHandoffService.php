<?php
declare(strict_types=1);

namespace App\Services;

use PDO;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/**
 * FaydaHandoffService
 * ---------------------------------------------------------------------
 * WHAT THIS CLASS DOES
 * Reads and writes the `fayda_handoff` database table (see
 * database/001_create_fayda_handoff.sql). This table is how data
 * crosses from the bridge (nid.bols.gov.et) over to JCIMS
 * (jcv2.ltbdev.com) even though they are different domains and can't
 * share a PHP session.
 *
 *   store()   -- called by the BRIDGE, right after it fetches the
 *                Fayda profile. Writes one row, returns a random
 *                token to put in the redirect URL back to JCIMS.
 *
 *   consume() -- called by JCIMS, when the browser arrives back with
 *                that token in the URL. Reads the row AND deletes it
 *                in the same call, so the same token can never be
 *                used twice (protects against someone bookmarking or
 *                replaying the verify link).
 * ---------------------------------------------------------------------
 */
final class FaydaHandoffService
{
    private const LIFETIME_SECONDS = 300; // 5 minutes

    public function __construct(private readonly PDO $pdo)
    {
    }

    public function store(array $profile, ?string $jobSeekerId): string
    {
        $token = Uuid::uuid7()->toString();

        $stmt = $this->pdo->prepare(
            'INSERT INTO fayda_handoff (token, profile_json, job_seeker_id, expires_at)
             VALUES (:token, :profile_json, :job_seeker_id, :expires_at)'
        );

        $stmt->execute([
            ':token'         => $token,
            ':profile_json'  => json_encode($profile, JSON_UNESCAPED_UNICODE),
            ':job_seeker_id' => $jobSeekerId,
            ':expires_at'    => date('Y-m-d H:i:s', time() + self::LIFETIME_SECONDS),
        ]);

        return $token;
    }

    /**
     * Returns ['profile' => array, 'job_seeker_id' => ?string] or null
     * if the token doesn't exist / already expired / already used.
     */
    public function consume(string $token): ?array
    {
        $select = $this->pdo->prepare(
            'SELECT profile_json, job_seeker_id
             FROM fayda_handoff
             WHERE token = :token AND expires_at > NOW()'
        );
        $select->execute([':token' => $token]);
        $row = $select->fetch();

        if ($row === false) {
            return null;
        }

        // delete immediately -- one-time use, regardless of what the
        // caller does next
        $delete = $this->pdo->prepare('DELETE FROM fayda_handoff WHERE token = :token');
        $delete->execute([':token' => $token]);

        $profile = json_decode($row['profile_json'], true);
        if (!is_array($profile)) {
            throw new RuntimeException('Stored Fayda profile data is corrupted.');
        }

        return [
            'profile'       => $profile,
            'job_seeker_id' => $row['job_seeker_id'],
        ];
    }
}