<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // On donne un nouveau nom à l'image
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        // On récupère les infos de l'image
        $picture_infos = getimagesize($picture);

        if($picture_infos === false){
            throw new Exception('Format d\'image incorrect');
        }

        // On vérifie le format de l'image
        switch($picture_infos['mime']){
            case 'image/png':
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/webp':
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
                throw new Exception('Format d\'image incorrect');
        }

        // On recadre l'image
        // On récupère les dimensions
        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

      // On vérifie l'orientation de l'image
switch ($imageWidth <=> $imageHeight) {
    case -1: // portrait
        $src_x = 0;
        $src_y = ($imageHeight - $imageWidth) / 2; // Ajustement pour le portrait
        $squareSize = $imageWidth;
        break;
    case 0: // carré
        $src_x = 0;
        $src_y = 0;
        $squareSize = $imageWidth; // Vous pouvez utiliser $imageHeight ici aussi
        break;
    case 1: // paysage
        $src_x = ($imageWidth - $imageHeight) / 2; // Ajustement pour le paysage
        $src_y = 0;
        $squareSize = $imageHeight;
        break;
}

        // On crée une nouvelle image "vierge"
        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled($resized_picture, $picture_source, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

// Correction de la rotation de l'image
$exif = @exif_read_data($picture);
if (!empty($exif['Orientation'])) {
    switch ($exif['Orientation']) {
        case 3:
            $resized_picture = imagerotate($resized_picture, 180, 0);
            break;
        case 6:
            $resized_picture = imagerotate($resized_picture, -90, 0);
            break;
        case 8:
            $resized_picture = imagerotate($resized_picture, 90, 0);
            break;
    }
}


        $path = $this->params->get('images_directory') . $folder;

        // On crée le dossier de destination s'il n'existe pas
        if(!file_exists($path . '/mini/')){
            mkdir($path . '/mini/', 0755, true);
        }

        // On stocke l'image recadrée
        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(string $fichier, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        if($fichier !== 'default.webp'){
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if(file_exists($mini)){
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;

            if(file_exists($original)){
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }
}