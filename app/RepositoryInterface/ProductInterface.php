<?php

namespace App\RepositoryInterface;

interface ProductInterface
{
  public function index();
  public function getProductById($id);
  public function createProduct($data);
  public function updateProduct($id, $data);
  public function deleteProduct($id);
}
