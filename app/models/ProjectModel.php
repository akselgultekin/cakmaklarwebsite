<?php
class ProjectModel extends Model
{
    protected string $table = 'projects';

    public function featured(int $limit = 4): array
    {
        return Database::query(
            "SELECT * FROM projects WHERE is_active=1 AND is_featured=1 ORDER BY sort_order, id LIMIT ?",
            [$limit]
        );
    }

    public function activeAll(): array
    {
        return Database::query(
            "SELECT * FROM projects WHERE is_active=1 ORDER BY sort_order, id"
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return Database::queryOne(
            "SELECT * FROM projects WHERE slug=? AND is_active=1",
            [$slug]
        );
    }

    public function getImages(int $projectId): array
    {
        return Database::query(
            "SELECT * FROM project_images WHERE project_id=? ORDER BY sort_order, id",
            [$projectId]
        );
    }

    public function getFloorPlans(int $projectId): array
    {
        return Database::query(
            "SELECT * FROM project_floor_plans WHERE project_id=? ORDER BY sort_order, id",
            [$projectId]
        );
    }

    public function getListings(int $projectId): array
    {
        return Database::query(
            "SELECT * FROM listings WHERE project_id=? AND is_active=1 ORDER BY sort_order, id",
            [$projectId]
        );
    }

    public function paginateActive(int $page, int $perPage = PER_PAGE, string $status = ''): array
    {
        $where = 'is_active=1';
        $params = [];
        if ($status) {
            $where .= ' AND status=?';
            $params[] = $status;
        }
        return $this->paginate($page, $perPage, $where, $params, 'sort_order, id DESC');
    }
}
