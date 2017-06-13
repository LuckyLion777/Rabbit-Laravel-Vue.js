<?php

namespace App\Console\Commands;

use App\Models\RabbitBreeder;
use App\Models\RabbitKit;
use App\Models\User;
use Illuminate\Console\Command;
use File;
use DB;
use Storage;
use Cloudder;

class UploadAssetsToCloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move all assets to cloudinary';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Command for uploading all images to Cloudinary. Do you wish to continue?')) {
            $breeders = RabbitBreeder::where('image', '!=', '')->whereNotNull('image')->get();
            $kits = RabbitKit::where('image', '!=', '')->whereNotNull('image')->get();
            $users = User::where('pedigree_logo', '!=', '')->whereNotNull('pedigree_logo')->get();

            $this->info('Uploading Breeders');
            $this->uploadImages($breeders);
            $this->info('Uploading Kits');
            $this->uploadImages($kits);

            $this->info('Uploading Pedigre Logos');
            $this->uploadLogos($users);
        }
    }

    protected function uploadImages($objects)
    {
        $counter = 0;
        foreach($objects as $object){
            $image = $object->image['name'];

            $fullPath = public_path('media/' . $object->getImagesFolder() . '/' . $image);
            if (Storage::disk('media')->exists($object->getImagesFolder() . '/' . $image)) {
                var_dump($fullPath);
                //$file = file_get_contents($fullPath);

                $options = [];
                $options['folder'] = $object->getImagesFolder();
                $options['crop'] = 'thumb';
                $options['overwrite'] = false;
                Cloudder::upload($fullPath, $image, $options);
                $publicId = Cloudder::getPublicId();
                if($publicId != $image){
                    $object->image = $publicId;
                    $object->save();
                }

                //Storage::disk('local')->put('backup/' . $image, $file);
                //Storage::disk('media')->delete($image);
                $counter++;
            }
        }
        $this->info($counter . ' objects');
    }

    protected function uploadLogos($objects)
    {
        $counter = 0;
        foreach($objects as $object){
            $image = $object->pedigree_logo['name'];

            $fullPath = public_path('media/' . $object->getImagesFolder() . '/' . $image);
            if (Storage::disk('media')->exists($object->getImagesFolder() . '/' . $image)) {
                var_dump($fullPath);
                //$file = file_get_contents($fullPath);

                $options = [];
                $options['folder'] = 'logo';
                $options['crop'] = 'thumb';
                $options['overwrite'] = false;
                Cloudder::upload($fullPath, $image, $options);
                $publicId = Cloudder::getPublicId();
                if($publicId != $image){
                    $object->pedigree_logo = $publicId;
                    $object->save();
                }

                $counter++;
            }
        }
        $this->info($counter . ' objects');
    }

}
