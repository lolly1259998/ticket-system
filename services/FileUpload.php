<?php
class FileUpload {
    private $uploadDir = 'uploads/';
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
    private $maxSize = 5 * 1024 * 1024; // 5MB

    public function upload($file) {
        if($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Vérifier la taille
        if($file['size'] > $this->maxSize) {
            return null;
        }

        // Vérifier l'extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($fileExtension, $this->allowedTypes)) {
            return null;
        }

        // Créer le dossier s'il n'existe pas
        if(!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        // Générer un nom unique
        $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadFile = $this->uploadDir . $fileName;

        if(move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return $fileName;
        }

        return null;
    }
}
?>