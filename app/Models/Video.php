<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes,Uuid, UploadFiles;

    public const RATING_LIST = [
      'L',
      '10',
      '12',
      '14',
      '16',
      '18'
    ];

    protected $table = 'videos';
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $casts = [
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public static $fileFields = ['movie', 'trailer'];


    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            /**
             * @var Video $obj
             */
            $obj =  static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            //Uploads
            $obj->uploadFiles($files);
            \DB::commit();
            return $obj;
        }catch (\Exception $e){
            if(isset($obj)){
                // Excluir arquivos de upload
            }
            \DB::rollBack();
            throw $e;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {

        try {
            \DB::beginTransaction();
            $saved =  parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if($saved){
                //Uploads
                // Upload dos novos arquivos e exclusão dos antigos
            }
            \DB::commit();
            return $saved;
        }catch (\Exception $e){
            // Excluir arquivos de upload
            \DB::rollBack();
            throw $e;
        }
    }

    public static function handleRelations(Video $video, array $attributes = [])
    {
        if(isset($attributes['categories_id'])){
            $video->categories()->sync($attributes['categories_id']);
        }
        if(isset($attributes['genres_id'])){
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }

}
