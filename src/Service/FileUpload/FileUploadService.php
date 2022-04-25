<?php

namespace App\Service\FileUpload;

use App\Exception\CustomBadRequestException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class FileUploadService
{
    private ContainerBagInterface $params;

    /**
     * @param ContainerBagInterface $params
     */
    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function defaultImg()
    {
        return $this->params->get('default_img_dir').'/default/player.png';
    }


    public function uploadImgPlayer($file, $player)
    {
            list($type, $file) = explode(';', $file);
            list(, $file) = explode(',', $file);
            list($rest, $imageType) = explode('/', $type);
            if (strlen($file) < 200000){
            if($imageType === 'jpeg' || $imageType === 'jpg' || $imageType === 'png')
            {
            $data = base64_decode($file);
            $filePath = $this->params->get('player_img_dir').'/player'.$player->getId();
            file_put_contents($filePath, $data);
            return $filePath;
            }else{
                throw new CustomBadRequestException('Only jpeg, jpg, png extension are accepted');
            }
            }else{
                throw new CustomBadRequestException('The file has to be less than 200KB ');
            }



    }
}