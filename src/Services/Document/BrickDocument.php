<?php
declare(strict_types=1);

namespace App\Services\Document;

/**
 * Class BrickDocument
 * @package App\Services\Document
 */
class BrickDocument
{

    /**
     * @param string $procedureId
     * @param string $userId
     * @return false|int[]|mixed
     */
    public function rules(string $procedureId, string $userId){
        $listRule = [];

        // Procedure id, User id
       /* $listRule['88cb928b-6cd6-440d-a2e5-d08fa9e16a79'] =
            [
                '52a0c1f7-37fd-472f-aaa4-4c25d10e67eb' => ['countBrickFiles' => 1]
            ];*/

        if (isset($listRule[$procedureId][$userId])) {
            return $listRule[$procedureId][$userId]['countBrickFiles'];
        }

        return false;
    }
}