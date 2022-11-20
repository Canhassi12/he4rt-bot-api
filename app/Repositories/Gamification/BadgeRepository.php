<?php

namespace App\Repositories\Gamification;

use App\Models\Gamefication\Badge;
use Illuminate\Http\UploadedFile;

class BadgeRepository
{
    /**
     * @var Badge
     */
    private $model;

    public function __construct()
    {
        $this->model = new Badge();
    }

    public function fetchBadges()
    {
        return $this->model->paginate(15);
    }

    public function newBadge(string $name, string $description, UploadedFile $image)
    {
        $fileDirectory = $image->store('badges');
        return $this->model->create([
            'name' => $name,
            'description' => $description,
            'image_url' => $fileDirectory
        ]);
    }

    public function findBadge(int $badgeId)
    {
        return $this->model->find($badgeId);
    }

    public function deleteBadge(int $badgeId)
    {
        $this->model->find($badgeId)->delete();
    }
}
