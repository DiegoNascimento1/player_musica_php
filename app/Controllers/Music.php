<?php

namespace App\Controllers;

use CodeIgniter\Files\File;

class Music extends BaseController
{

    protected $helpers = ['form'];
    
    public function index()
    {
        $musicFolder = WRITEPATH . 'uploads/music/';
        $musicFiles = scandir($musicFolder);
        $i = 0;
        foreach ($musicFiles as $music) {
            if (is_file($musicFolder . $music)) {

                // Verificar se é um arquivo de música

                $getID3 = new \getID3;
                $musicFile = $musicFolder . $music;
                $ext = pathinfo($musicFile, PATHINFO_EXTENSION);
                $fileInfo = $getID3->analyze($musicFile);
                $musics[] = [
                    "id"        => $i,
                    "nome"      => isset($fileInfo["filename"]) ? str_replace("." . $ext, "", $fileInfo["filename"]) : 'Sem nome',
                    "titulo"    => isset($fileInfo['tags']['id3v2']['title'][0]) ? $fileInfo['tags']['id3v2']['title'][0] : 'Título não informado',
                    "artista"   => isset($fileInfo['tags']['id3v2']['artist'][0]) ? $fileInfo['tags']['id3v2']['artist'][0] : 'Artista não informado',
                    "album"     => isset($fileInfo['tags']['id3v2']['album'][0]) ? $fileInfo['tags']['id3v2']['album'][0] : 'Album não informado',
                    "duracao"   => gmdate("H:i:s", $fileInfo['playtime_seconds'])
                ];
                // $titulo = isset($fileInfo['tags']['id3v2']['title'][0]) ? $fileInfo['tags']['id3v2']['title'][0] : '';
                // $artista = isset($fileInfo['tags']['id3v2']['artist'][0]) ? $fileInfo['tags']['id3v2']['artist'][0] : '';
                // $album = isset($fileInfo['tags']['id3v2']['album'][0]) ? $fileInfo['tags']['id3v2']['album'][0] : '';
                // $duracao = gmdate("H:i:s", $fileInfo['playtime_seconds']);
                $i++;
            }
        }

        return view('music/index');
    }

    public function upload() {
        $validationRule = [
            'userfile' => [
                'label' => 'Image File',
                'rules' => [
                    'uploaded[userfile]',
                    'is_image[userfile]',
                    'mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                    'max_size[userfile,100]',
                    'max_dims[userfile,1024,768]',
                ],
            ],
        ];
        if (! $this->validate($validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];

            return view('upload_form', $data);
        }

        $img = $this->request->getFile('userfile');

        if (! $img->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $img->store();

            $data = ['uploaded_fileinfo' => new File($filepath)];

            return view('upload_success', $data);
        }

        $data = ['errors' => 'The file has already been moved.'];

        return view('upload_form', $data);
    }
}
