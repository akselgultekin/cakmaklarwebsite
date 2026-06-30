<?php
class ListingModel extends Model
{
    protected string $table = 'listings';

    public function active(string $type = '', int $limit = 0): array
    {
        $where  = 'is_active=1';
        $params = [];
        if ($type) {
            $where .= ' AND type=?';
            $params[] = $type;
        }
        $limitClause = $limit ? "LIMIT {$limit}" : '';
        return Database::query(
            "SELECT * FROM listings WHERE {$where} ORDER BY sort_order, id DESC {$limitClause}",
            $params
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return Database::queryOne(
            "SELECT l.*, p.title AS project_title, p.slug AS project_slug
             FROM listings l
             LEFT JOIN projects p ON p.id=l.project_id
             WHERE l.slug=? AND l.is_active=1",
            [$slug]
        );
    }

    public function getImages(int $listingId): array
    {
        return Database::query(
            "SELECT * FROM listing_images WHERE listing_id=? ORDER BY sort_order, id",
            [$listingId]
        );
    }

    public function similar(int $listingId, string $type, int $limit = 3): array
    {
        return Database::query(
            "SELECT * FROM listings WHERE id!=? AND type=? AND is_active=1 ORDER BY RAND() LIMIT ?",
            [$listingId, $type, $limit]
        );
    }

    public function filter(array $filters, int $page = 1, int $perPage = PER_PAGE): array
    {
        $where  = ['is_active=1'];
        $params = [];

        if (!empty($filters['type'])) {
            $where[]  = 'type=?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['konum'])) {
            $where[]  = 'location LIKE ?';
            $params[] = '%' . $filters['konum'] . '%';
        }
        if (!empty($filters['oda'])) {
            $where[]  = 'room_count=?';
            $params[] = $filters['oda'];
        }
        if (!empty($filters['min_fiyat'])) {
            $where[]  = 'price>=?';
            $params[] = (float) str_replace('.', '', $filters['min_fiyat']);
        }
        if (!empty($filters['max_fiyat'])) {
            $where[]  = 'price<=?';
            $params[] = (float) str_replace('.', '', $filters['max_fiyat']);
        }
        if (!empty($filters['min_m2'])) {
            $where[]  = 'area_m2>=?';
            $params[] = (int) $filters['min_m2'];
        }
        if (!empty($filters['max_m2'])) {
            $where[]  = 'area_m2<=?';
            $params[] = (int) $filters['max_m2'];
        }

        $whereStr = implode(' AND ', $where);
        return $this->paginate($page, $perPage, $whereStr, $params, 'sort_order, id DESC');
    }
}
