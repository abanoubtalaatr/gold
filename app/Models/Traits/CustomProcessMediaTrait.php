<?php

namespace App\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Brackets\Media\HasMedia\MediaCollection;
use Brackets\Media\HasMedia\ProcessMediaTrait;

trait CustomProcessMediaTrait
{
    use ProcessMediaTrait;

       /**
     * Process single file metadata add/edit/delete to media library
     *
     * @param $inputMedium
     * @param $mediaCollection
     * @throws FileCannotBeAdded
     */
    public function processMedia(Collection $inputMedia): void
    {
        //        Don't we want to use maybe some class to represent the data structure?
        //        Maybe what we want is a MediumOperation class, which holds {collection name, operation (detach, attach, replace), metadata, filepath)} what do you think?

        //First validate input
        $this->getMediaCollections()->each(function ($mediaCollection) use ($inputMedia) {
            $this->validate(collect($inputMedia->get($mediaCollection->getName())), $mediaCollection);
        });

        //Then process each media
        $this->getMediaCollections()->each(function ($mediaCollection) use ($inputMedia) {
            collect($inputMedia->get($mediaCollection->getName()))->each(function ($inputMedium) use (
                $mediaCollection
            ) {
                if (is_array($inputMedium))
                    $this->processMedium($inputMedium, $mediaCollection);
            });
        });
    }

    /**
     * Validae input data for media
     *
     * @param Collection $inputMediaForMediaCollection
     * @param MediaCollection $mediaCollection
     * @throws FileCannotBeAdded
     */
    public function validate(Collection $inputMediaForMediaCollection, MediaCollection $mediaCollection): void
    {
        $this->validateCollectionMediaCount($inputMediaForMediaCollection, $mediaCollection);

        $inputMediaForMediaCollection->each(function ($inputMedium) use ($mediaCollection) {
            if (is_array($inputMedium) && $inputMedium['action'] === 'add') {
                $mediumFileFullPath = Storage::disk('uploads')->path($inputMedium['path']);
                $this->validateTypeOfFile($mediumFileFullPath, $mediaCollection);
                $this->validateSize($mediumFileFullPath, $mediaCollection);
            }
        });
    }

}
