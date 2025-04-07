<?php
namespace App\RepositoryInterface;
interface CategoryInterface{
    public function index();
    public function getCategoryById($id);
    public function createCategory($data);
    public function updateCategory($id, $data);
    public function deleteCategory($id);
}