<?php
class VehicleModel extends Model
{
    protected string $table = 'vehicles';

    public function activeAll(): array
    {
        return Database::query(
            "SELECT * FROM vehicles WHERE is_active=1 ORDER BY sort_order, id DESC"
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return Database::queryOne(
            "SELECT * FROM vehicles WHERE slug=? AND is_active=1",
            [$slug]
        );
    }

    public function getImages(int $vehicleId): array
    {
        return Database::query(
            "SELECT * FROM vehicle_images WHERE vehicle_id=? ORDER BY sort_order, id",
            [$vehicleId]
        );
    }

    public function paginateActive(int $page, int $perPage = PER_PAGE): array
    {
        return $this->paginate($page, $perPage, 'is_active=1', [], 'sort_order, id DESC');
    }

    /** Admin: aktif/pasif tümünü listele */
    public function paginateAll(int $page, int $perPage = PER_PAGE): array
    {
        return $this->paginate($page, $perPage, '1=1', [], 'id DESC');
    }
}
