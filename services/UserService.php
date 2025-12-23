<?php
class UserService {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function getAllUsersForAssignment() {
        return $this->userModel->getAllUsers();
    }

    public function getUserInfo($user_id) {
        return $this->userModel->getUserById($user_id);
    }
}
?>