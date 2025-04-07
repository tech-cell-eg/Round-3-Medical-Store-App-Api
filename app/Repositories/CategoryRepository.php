<?php
namespace App\Repositories;
use App\RepositoryInterface\CategoryInterface;
use App\Models\Category;
class CategoryRepository implements CategoryInterface{
  public function index(){
    return Category::all();
  }
  public function getCategoryById($id){
    return Category::find($id);
  }
  public function createCategory($data){
    return Category::create($data);
  }
  public function updateCategory($id, $data){
    $category = Category::find($id);
    if($category){
      $category->update($data);
      return $category;
    }
    return null;
  }
  public function deleteCategory($id){
    $category = Category::find($id);
    if($category){
      $category->delete();
      return true;
    }
    return false;
  }
}
