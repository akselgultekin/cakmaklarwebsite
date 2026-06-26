<?php
class SliderModel extends Model
{
    protected string $table = 'sliders';

    public function activeSliders(): array
    {
        return Database::query(
            "SELECT * FROM sliders WHERE is_active=1 ORDER BY sort_order, id"
        );
    }
}
