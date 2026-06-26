<?php
class NewsModel extends Model
{
    protected string $table = 'news';

    public function recent(int $limit = 3): array
    {
        return Database::query(
            "SELECT * FROM news WHERE is_active=1 ORDER BY published_at DESC, created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return Database::queryOne(
            "SELECT * FROM news WHERE slug=? AND is_active=1",
            [$slug]
        );
    }

    public function paginateActive(int $page, int $perPage = PER_PAGE): array
    {
        return $this->paginate($page, $perPage, 'is_active=1', [], 'published_at DESC, created_at DESC');
    }

    /** Admin: aktif/pasif tümünü listele */
    public function paginateAll(int $page, int $perPage = PER_PAGE): array
    {
        return $this->paginate($page, $perPage, '1=1', [], 'published_at DESC, created_at DESC');
    }
}
