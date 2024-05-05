<?php

namespace App\Rules;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Contracts\Validation\Rule;

class RelationBetweenVideoAndGenreRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $genresId = request()->input('genres_id');
        $categoriesId = request()->input('categories_id');

        if(empty($genresId) || empty($categoriesId)){
            return false;
        }

        return $this->verifyRelationBetweenVideoAndGenre($genresId, $categoriesId);
    }

    public function verifyRelationBetweenVideoAndGenre($genresId, $categoriesId){
        foreach ($genresId as $genreId){
            $genre = Genre::find($genreId);
            if(!$genre){
                return false;
            }
            // The pluck method retrieves all of the values for a given key:
            $relatedCategories = $genre->categories->pluck('id')->toArray();
            if(empty(array_intersect($categoriesId,$relatedCategories))){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.relation_between_video_and_genre_error');
    }
}
