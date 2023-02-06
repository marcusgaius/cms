<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\db;

use craft\base\Batchable;
use yii\db\Connection as YiiConnection;
use yii\db\Query as YiiQuery;
use yii\db\QueryInterface;

/**
 * QueryBatcher class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 4.4.0
 */
class QueryBatcher implements Batchable
{
    public function __construct(
        private QueryInterface $query,
        private ?YiiConnection $db = null,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        try {
            return $this->query->count(db: $this->db);
        } catch (QueryAbortedException) {
            return 0;
        }
    }

    /**
     * @inheritdoc
     */
    public function getSlice(int $offset, int $limit): iterable
    {
        /** @var YiiQuery $query */
        $query = $this->query;

        if (is_int($query->limit)) {
            // Don't go passed the query's limit
            if ($offset >= $query->limit) {
                return [];
            }
            $limit = min($limit, $query->limit - $offset);
        }

        $queryOffset = $query->offset;
        $queryLimit = $query->limit;

        try {
            $slice = $query
                ->offset((is_int($queryOffset) ? $queryOffset : 0) + $offset)
                ->limit($limit)
                ->all();
        } catch (QueryAbortedException) {
            $slice = [];
        }

        $query->offset($queryOffset);
        $query->limit($queryLimit);

        return $slice;
    }
}
