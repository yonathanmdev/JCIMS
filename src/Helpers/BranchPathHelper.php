<?php
namespace App\Helpers;
class BranchPathHelper
{
    /**
     * Returns the chain of branches strictly BELOW the requesting user's root branch,
     * down to (and including) the target branch — ordered root-to-leaf — each
     * annotated with the correct display label (region/zone/woreda vs.
     * ketema astedader/kifle ketema, depending on the chain).
     */
    public static function getRelativeBranchPath(\PDO $db, string $targetBranchPath, string $rootBranchPath): array
    {
        $sql = "
            SELECT internal_id, name, level, ketema_astedader, path
            FROM branches
            WHERE :leaf_path LIKE CONCAT(path, '%')
              AND LENGTH(path) > LENGTH(:root_path)
            ORDER BY LENGTH(path) ASC
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':leaf_path', $targetBranchPath);
        $stmt->bindValue(':root_path', $rootBranchPath);
        $stmt->execute();

        $branches = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $isKetemaChain = false;
        foreach ($branches as $b) {
            if ((int)$b['level'] === 2 && !empty($b['ketema_astedader'])) {
                $isKetemaChain = true;
                break;
            }
        }

        foreach ($branches as &$b) {
            $b['display_label'] = \App\Helpers\BranchNameHelper::labelForLevel((int)$b['level'], $isKetemaChain);
        }
        unset($b);

        return $branches;
    }

    /**
     * Convenience wrapper: builds the final "Name (label) / Name (label)" string directly.
     */
    public static function buildDisplayPath(\PDO $db, string $targetBranchPath, string $rootBranchPath): string
    {
        $ancestors = self::getRelativeBranchPath($db, $targetBranchPath, $rootBranchPath);
        return implode(' / ', array_map(
            fn($b) => "{$b['name']} {$b['display_label']}",
            $ancestors
        ));
    }

  
}