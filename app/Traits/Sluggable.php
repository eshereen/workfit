<?php
// app/Models/Traits/Sluggable.php
namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the sluggable trait for the model.
     */
    protected static function bootSluggable()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') || $model->isDirty('title')) {
                $model->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the model.
     */
    public function generateSlug()
    {
        $slugField = $this->getSlugField();
        $sourceField = $this->getSlugSourceField();

        // Get the source value
        $sourceValue = $this->{$sourceField};

        if (!$sourceValue) {
            return;
        }

        // Generate base slug
        $slug = Str::slug($sourceValue);

        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $this->{$slugField} = $slug;
    }

    /**
     * Check if a slug already exists.
     */
    protected function slugExists($slug)
    {
        $query = static::where($this->getSlugField(), $slug);

        // Exclude current model if updating
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }

    /**
     * Get the slug field name.
     */
    protected function getSlugField()
    {
        return property_exists($this, 'slugField') ? $this->slugField : 'slug';
    }

    /**
     * Get the source field for slug generation.
     */
    protected function getSlugSourceField()
    {
        return property_exists($this, 'slugSourceField')
            ? $this->slugSourceField
            : (property_exists($this, 'title') ? 'title' : 'name');
    }
}
