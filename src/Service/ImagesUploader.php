<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Image as Img;



class ImagesUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(/*UploadedFile $file, */ $request, $images)
    {

            // Get the array of uploaded images
            $uploaded = $request->files->get('post')["image"];

            if($uploaded !== null) {
            

                // Turn this array into a 0 based array (in order to loop through it along with another 0 based array: $images)
                $uploadedArray = array();
                foreach($uploaded as $element) {
                    array_push($uploadedArray, $element);
                }

                for($i=0; $i<=count($images)-1; $i++) {

                    // Get one uploaded image
                    $theImage = $uploadedArray[$i]["upload"];

                    if($theImage !== null) {

                        // Validate image
                        if(!array_search($theImage->guessExtension(), ['jpg', 'jpeg', 'png'])) {
                            throw new Exception('Not a jpeg or png');
                        }
                        $validator = Validation::createValidator();
                        $violations = $validator->validate($theImage, [
                            new Img([
                                'maxWidth' => '1500', 
                                'maxHeight' => '1000',
                                'detectCorrupted' => true,
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid JPG file'
                            ]),
                        ]);


                        // Get the filename without the extension
                        //$originalFilename = pathinfo($theImage->getClientOriginalName(), PATHINFO_FILENAME);

                        // generate unique ID + guess the extension
                        $newFilename = uniqid().'.'.$theImage->guessExtension();

                        // set this name into Image->filename
                        $images[$i]->setFilename($newFilename);

                        // Move file to 'public/img' directory
                        $imagesDirectory = $this->targetDirectory;
                        try {
                            $theImage->move(
                                $imagesDirectory,
                                $newFilename
                            );
                        } catch (FileException $e) {
                                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                        }         
                    }
                }

            }

    }
}




